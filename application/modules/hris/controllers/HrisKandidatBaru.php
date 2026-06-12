<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HrisKandidatBaru extends Public_Controller {

    private $pathView = 'hris/hris_kandidat_baru/';
    private $url;
    private $hakAkses;

    function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
        $this->load->library('telegram_lib');
    }

    public function index($segment=0)
    {

        if ( $this->hakAkses['a_view'] == 1 ) {

            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/toastr/js/toastr.js",
                "assets/toastr/js/toastr.min.js",
                "assets/hris/hris_kandidat_baru/js/hris_kandidat_baru.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/toastr/css/toastr.css",
                "assets/toastr/css/toastr.min.css",
                "assets/hris/hris_kandidat_baru/css/hris_kandidat_baru.css",
            ));

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Kadidat Baru';
            $content['status']          = $this->get_status_karyawan();

            $content['usulan']          = $this->get_data_usulan();

            // Load Indexx
            $data['title_menu']     = 'HRIS - Kadidat Baru';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

            // cetak_r($_SESSION, 1);

            // $message_telegram = '['.$_SESSION['id_user'].'] '. $_SESSION['detail_user']['nama_detuser'] . ' membuka halaman ' . $data['title_menu']; 
            // $this->telegram_lib->sendMessages($message_telegram);

        } else {
            showErrorAkses();
        }
    }

    public function get_data_usulan()
    {
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select hukb.*, k.nama from hris_usulan_karyawan_baru hukb 
						INNER JOIN (
                            SELECT * FROM karyawan
                                WHERE id IN (
                                    SELECT MAX(id)
                                    FROM karyawan
                                    GROUP BY nik
                                )
                            ) k ON hukb.nama_pengusul = k.nik
						where hukb.status = 3 ";
        $result_usulan     = $m_conf->hydrateRaw( $sql )->toArray();

        $sql_usulan_terpenuhi = " SELECT 
                                hukb.id,
                                hukb.jumlah,
                                COUNT(hdk.id) AS total_diterima
                            FROM hris_usulan_karyawan_baru hukb
                            JOIN hris_data_kandidat hdk 
                                ON hdk.usulan_id = hukb.id
                                AND hdk.status_kandidat = 2
                            GROUP BY hukb.id, hukb.jumlah
                            HAVING COUNT(hdk.id) >= hukb.jumlah ";

        $result_terpenuhi     = $m_conf->hydrateRaw( $sql_usulan_terpenuhi )->toArray();

        $id_terpenuhi = array_column($result_terpenuhi, 'id');

        $result = array_filter($result_usulan, function ($item) use ($id_terpenuhi) {
            return !in_array($item['id'], $id_terpenuhi);
        });

        $result = array_values($result);
        
        // cetak_r($result, 1);

        return $result;
    }


    public function save()
    {

        $params = $_POST;
        // cetak_r($params, 1);
        
        try {

            foreach ($params['detail'] as $v_det) {
                $m_db = new \Model\Storage\HrisDataKandidat_model();
                $m_db->nama             = $v_det['nama_karyawan'];
                $m_db->status_kandidat  = $v_det['status_karyawan'];
                $m_db->is_active        = 'ACTIVE';
                $m_db->usulan_id        = $v_det['usulan'];
                $m_db->save();
            }

            $id            = $m_db->id;
            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/save', $m_db, $deskripsi_log, null, $id, $m_db);

            $message_telegram = '['.$_SESSION['id_user'].'] '. $_SESSION['detail_user']['nama_detuser'] . ' Add Data Kode : ' . $v_det['nama_karyawan']; 
            $this->telegram_lib->sendMessages($message_telegram);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );

    }


    public function edit_data()
    {
        $params                     = $_POST;
        $data                       = $this->includes;
        $content['kategori']        = $this->getKategori($params['kode_kategori'])[0];

        echo $this->load->view($this->pathView . 'v_edit_data', $content, TRUE);
    }

    public function update()
    {
        $params = $_POST;

        // cetak_r($params, 1);


        try {
           
        
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di update.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }


    public function delete()
    {
        $params = $_POST;
        // cetak_r($params, 1);
        $id_form = $params['kode_kategori'];

        $m_form = new \Model\Storage\HrisKategori_model();

        try {

            $m_form->where('kode_kategori', $id_form)->delete();

    

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }


    public function load_form()
    {

        $content['list']    =  $this->get_data_form();

        if (!empty($_POST['kode'])) {
            $kode_get = urldecode($_POST['kode']);

            foreach ( $content['list'] as $key => $val) {
                if (trim($val['document']) == trim($kode_get)) {
                    $val['selected'] = 'selected';
                    $selected = $val;
                    unset($content['list'][$key]);
                    array_unshift($content['list'], $selected);
                    break;
                }
            }
        }
        // cetak_r($content, 1);

        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }


    public function get_data_form(){
        
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select hdk.id as id_data_karyawan, hdk.nama, hdk.status_kandidat, hdk.tgl_masuk,
                        hdk.keterangan_reject, hsk.nama_status , hsk.kategori, hdk.is_active, hdk.document, 
                        k.nama as nama_pengusul, k.jabatan as jabatan_pengusul, hukb.posisi, hukb.jumlah, hukb.unit,
                        w.induk as induk_wilayah
                        from hris_data_kandidat hdk
                        left join hris_status_kandidat hsk on hdk.status_kandidat = hsk.id 
                        inner join hris_usulan_karyawan_baru hukb on hdk.usulan_id = hukb.id
                        LEFT JOIN (
						    SELECT *
						    FROM wilayah w1
						    WHERE w1.id IN (
						        SELECT MAX(id)
						        FROM wilayah
						        GROUP BY kode
						    )
						) w ON hukb.unit = w.kode
                        INNER JOIN (
                            SELECT *
                            FROM karyawan
                            WHERE id IN (
                                SELECT MAX(id)
                                FROM karyawan
                                GROUP BY nik
                            )
                        ) k ON hukb.nama_pengusul = k.nik
                        order by hdk.id desc ";
        $result     = $m_conf->hydrateRaw( $sql )->toArray();

        return $result;
    }

    public function get_status_karyawan(){
        
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select * from hris_status_kandidat ";
        $result     = $m_conf->hydrateRaw( $sql )->toArray();

        return $result;
    }


    public function show_document_kandidat()
    {

        $this->add_external_js(array(
            "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
            "assets/select2/js/select2.min.js",
            "assets/toastr/js/toastr.js",
            "assets/toastr/js/toastr.min.js",
            "assets/hris/hris_kandidat_baru/js/hris_kandidat_baru.js",
        ));
        $this->add_external_css(array(
            "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
            "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
            "assets/select2/css/select2.min.css",
            "assets/toastr/css/toastr.css",
            "assets/toastr/css/toastr.min.css",
            "assets/hris/hris_kandidat_baru/css/hris_kandidat_baru.css",
        ));

        $data                       = $this->includes;
        $content['akses']           = $this->hakAkses;
        $content['title_panel']     = 'HRIS - Kadidat Baru';
        
        $content['biodata']         = $this->get_bio_data($_GET['id']);
        $content['data_kandidat']   = $this->get_upload_document($_GET['id'])[0];
        // cetak_r($content['biodata'], 1);

        // echo $this->load->view($this->pathView . 'v_detail', $content, TRUE);

        $data['title_menu']     = 'HRIS - Kadidat Baru / Detail Form Kandidat';

        $data['view'] = $this->load->view($this->pathView . 'v_detail', $content, TRUE);
        $this->load->view($this->template, $data);

        // return $content;

    }

    public function get_bio_data($id_kandidat)
    {
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select * from hris_data_kandidat_detail where id_data_karyawan in  ($id_kandidat)";

        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;
        
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }
        // cetak_r($data, 1);

        $result = [];

        foreach ($data as $row) {
            $id = $row['id_data_karyawan'];

            if (!isset($result[$id])) {
                $result[$id] = [
                    'standalone' => [],
                    'grouped' => []
                ];
            }

            if (!empty($row['parent_column'])) {
                $result[$id]['grouped'][$row['parent_column']][] = $row;
            } else {
                $result[$id]['standalone'][$row['label']] = $row;
            }
        }

        return $result;

    }

    public function get_upload_document($id_kandidat)
    {
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select * from hris_data_kandidat where id in  ($id_kandidat)";

        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;
        
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }
        // cetak_r($data, 1);

        return $data;

    }


    public function exec_keputusan_akhir()
    {

        $params = $_POST;

        



        try {

            $m_db = new \Model\Storage\HrisDataKandidat_model();

            $d_db = $m_db->where('id', $params['id_data'])->first();
            if (!$d_db) {
                throw new \Exception("Data form tidak ditemukan.");
            }

            $unit = [];
            $data_unit = $this->get_list_unit()->toArray();
            foreach($data_unit as $du){
                $unit[$du['id']] = $du;
            }

            // HRIS DATA KANDIDAT
            $m_db->where('id', $params['id_data'])->update([
                'status_kandidat'   => $params['keputusan'] == 1 ? 2 : 3,
                'tgl_masuk'         => $params['keputusan'] == 1 ? $params['tgl_masuk'] : null,
                'keterangan_reject' => $params['keputusan'] == 2 ? $params['keterangan_reject'] : null,
            ]);
            // END HRIS DATA KANDIDAT

            if ( $params['keputusan'] == 1 ){

                $m_karyawan = new \Model\Storage\Karyawan_model();
                $id_karyawan = $m_karyawan->getNextIdentity();
    
                $m_karyawan->id         = $id_karyawan;
                $m_karyawan->level      = $params['level'];
                $m_karyawan->nik        = $m_karyawan->getNextNomor('K');
                $m_karyawan->atasan     = $params['atasan'];
                $m_karyawan->atasan_nik = $params['atasan_nik'];
                $m_karyawan->nama       = $params['nama'];
                $m_karyawan->kordinator = $params['koordinator'];
                $m_karyawan->marketing  = $params['marketing'];
                $m_karyawan->jabatan    = $params['jabatan'];
                $m_karyawan->status     = 1;
                $m_karyawan->tgl_berlaku = $params['tgl_masuk'];
                // cetak_r($params, 1);
                $m_karyawan->save();

                // UPDATE NIK HRIS DATA KANDIDAT
                $m_db->where('id', $params['id_data'])->update([
                    'nik'   => $m_karyawan->nik,
                    
                ]);
                // END UPDATE NIK HRIS DATA KANDIDAT
    
                foreach ($params['unit'] as $k_val => $val) {
                    $m_unit_karyawan                = new \Model\Storage\UnitKaryawan_model();
    
                    $id_unit_karyawan               = $m_unit_karyawan->getNextIdentity();
                    $m_unit_karyawan->id            = $id_unit_karyawan;
                    $m_unit_karyawan->id_karyawan   = $id_karyawan;
                    $m_unit_karyawan->unit          = $val;
                    $m_unit_karyawan->save();
                }
    
                foreach ($params['wilayah'] as $k_val => $val) {
                    $m_wilayah_karyawan             = new \Model\Storage\WilayahKaryawan_model();
    
                    $id_wilayah_karyawan            = $m_wilayah_karyawan->getNextIdentity();
                    $m_wilayah_karyawan->id         = $id_wilayah_karyawan;
                    $m_wilayah_karyawan->id_karyawan = $id_karyawan;
                    $m_wilayah_karyawan->wilayah    = $val;
                    $m_wilayah_karyawan->save();
                }
    
                // KARYAWAN HISTORY 
                    $m_karyawan_history                 = new \Model\Storage\KaryawanHistory_model();
                    $m_karyawan_history->nik            = $m_karyawan->nik;
                    $m_karyawan_history->jabatan        = $params['jabatan'];
                    $m_karyawan_history->tgl_mulai      = $params['tgl_masuk'];
                    $m_karyawan_history->tgl_selesai    = null;
                    $m_karyawan_history->save();
                    $id_karyawan_history                = $m_karyawan_history->id;
                // END KARYAWAN HISTORY 
    
                
                // KARYAWAN HISTORY UNIT
                 foreach ($params['unit'] as $k_val => $val) {
                    $m_karyawan_history_unit                 = new \Model\Storage\KaryawanHistoryUnit_model();
                    $m_karyawan_history_unit->id             = $id_karyawan_history;
                    $m_karyawan_history_unit->kode_unit      = $val;
                    $m_karyawan_history_unit->save();
                 }
                // END KARYAWAN HISTORY UNIT

                // KARYAWAN HISTORY WILAYAH
                 foreach ($params['wilayah'] as $k_val => $val) {
                    $m_karyawan_history_unit                 = new \Model\Storage\KaryawanHistoryWilayah_model();
                    $m_karyawan_history_unit->id             = $id_karyawan_history;
                    $m_karyawan_history_unit->kode_wilayah   = $val;
                    $m_karyawan_history_unit->save();
                 }
                // END KARYAWAN HISTORY WILAYAH

                // STATUS KARYAWAN  
                    $m_skb              = new \Model\Storage\HrisStatusKaryawanBaru_model();
                    $m_skb->pengusul    = $_SESSION['detail_user']['nama_detuser'];
                    $m_skb->nik         = $m_karyawan->nik;
                    $m_skb->kategori    = 'HRIS/K/003'; //harcode training pertama 3 bulan dahulu
                    $m_skb->keterangan  = 'Training 3 Bulan';
                    $m_skb->status      = 1;
                    $m_skb->tgl_berlaku = $params['tgl_masuk'];
                    $m_skb->tgl_selesai = date('Y-m-d', strtotime('+3 months', strtotime($params['tgl_masuk'])));
                    // cetak_r($m_skb, 1);
                    $m_skb->save();
                // END STATUS KARYAWAN

                $d_karyawan = $m_karyawan->where('id', $id_karyawan)->with(['unit', 'dWilayah'])->first();
                $deskripsi_log_karyawan = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $d_karyawan, $deskripsi_log_karyawan );
            } else {
               
                $deskripsi_log_karyawan = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/update', $m_db,  $params['id_data'], $deskripsi_log_karyawan );
            }

            $status = '';
            if ($params['keputusan'] == 2) {
                $status = 'Approved';
            } elseif ($params['keputusan'] == 3) {
                $status = 'Rejected';
            }

            $message_telegram =
                "🔔 KANDIDAT BARU\n\n".
                "User : ".$_SESSION['detail_user']['nama_detuser']."\n".
                "Kode : ".$params['id_data']."\n".
                "Status : ".$status;

            $this->telegram_lib->sendMessages($message_telegram);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di update.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }


    
    public function generate_form_karyawan_baru(){
        
        $id_data = $_POST['id_data'];
        $list         = $this->get_data_form();

        $dt = [];
        foreach ( $list as $l ){
            $dt[$l['id_data_karyawan']] = $l;
        } 
        
        $content['list']         = $dt[$id_data];
        $content['jabatan_nama'] = $this->get_data_jabatan($dt[$id_data]['posisi']);
        $content['list_unit']    = $this->get_list_unit();
        $content['list_wilayah'] = $this->get_list_wilayah();
        $content['atasan']       = $this->get_atasan($dt[$id_data]['posisi']);

        // cetak_r($content['list_unit'], 1);

        echo $this->load->view($this->pathView . 'v_generate_form_karyawan_baru', $content, TRUE);
    }

    public function get_data_jabatan($kode)
    {
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select * from jabatan where kode = '".$kode."' ";
        $result     = $m_conf->hydrateRaw( $sql )->toArray();

        return $result[0];
    }

    public function get_atasan($jabatan)
	{
		// $jabatan = $this->input->post('jabatan');
		$level = getLevelJabatan($jabatan);
		$atasan = getAtasan($jabatan);

		$d_karyawan = null;
		if ( $level != 0 ) {
			$m_karyawan = new \Model\Storage\Karyawan_model();
			$d_karyawan = $m_karyawan->where('level', '<', $level)
									 ->whereIn('jabatan', $atasan)
									 ->where('status', 1)
									 ->orderBy('level', 'asc')
									 ->get();
		}

		// $this->result['status'] = 1;
		// $this->result['content'] = $d_karyawan;
        $result     = $d_karyawan->toArray();
        return $result;
	}

    public function get_list_unit()
	{
		$m_unit = new \Model\Storage\Wilayah_model();
		$d_unit = $m_unit->where('jenis', 'UN')->orderBy('nama')->get();

		return $d_unit;
	}

	public function get_list_wilayah()
	{
		$m_wilayah = new \Model\Storage\Wilayah_model();
		$d_wilayah = $m_wilayah->where('jenis', 'PW')->orderBy('nama')->get();

		return $d_wilayah;
	}

    public function upload_document()
    {

        try {

            $file               = $_FILES['file'];
            $id_data_karyawan   = $_POST['id_data_karyawan'];
            $label              = $_POST['label'];

            // cetak_r($_POST, 1);

            $path = FCPATH . 'uploads/recruitment/';

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $file_ori = $file['name'];
            $tmp_name = $file['tmp_name'];

            $ext = pathinfo($file_ori, PATHINFO_EXTENSION);

            $file_encrypt = md5(uniqid() . time() . rand()) . '.' . $ext;

            move_uploaded_file( $tmp_name, $path . $file_encrypt );

            $m_db = new \Model\Storage\HrisDataKandidatDetail_model();

            $m_db->where('id_data_karyawan', $id_data_karyawan)->where('label', $label)->update([
                'value' => $file_encrypt
            ]);

            $this->result['status'] = 1;
            $this->result['message'] = 'Document berhasil diupload.';

        } catch (\Exception $e) {

            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function delete_document()
    {
        try {

            $id_data_karyawan = $_POST['id_data_karyawan'];
            $label            = $_POST['label'];

            // cetak_r($_POST, 1);

            $m_db = new \Model\Storage\HrisDataKandidatDetail_model();

            $data = $m_db->where('id_data_karyawan', $id_data_karyawan)->where('label', $label)->first();

            if (empty($data)) {
                throw new \Exception('Data tidak ditemukan.');
            }

            $file_name = $data['value'];

            if (!empty($file_name) && $file_name != '-') {
                $path_file = FCPATH . 'uploads/recruitment/' . $file_name;
                if (file_exists($path_file)) {
                    unlink($path_file);
                }
            }

            $m_db->where('id_data_karyawan', $id_data_karyawan)->where('label', $label)->update([
                'value' => '-'
            ]);

            $this->result['status'] = 1;
            $this->result['message'] = 'Document berhasil dihapus.';

        } catch (\Exception $e) {

            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function edit_item()
    {
        $params = $_POST;

        // cetak_r($params, 1);

        try {

            $id_data_karyawan = $params['id'];
            $label            = $params['label'];
            $value            = $params['value'];
            $parent           = $params['parent'] ?? null;

            $m_db = new \Model\Storage\HrisDataKandidatDetail_model();

            $query = $m_db->where('id_data_karyawan', $id_data_karyawan)->where('label', $label);

            if (!empty($parent)) {
                $query->where('parent_column', $parent);
            }
            
            $query->update([
                'value' => $value
            ]);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil diupdate.';

        } catch (\Exception $e) {

            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }


    public function sinkron_document()
    {
        $nama_file = $_POST['document'];

        $source      = 'C:/xampp/htdocs/recruitment-gmp/uploads/recruitment/' . $nama_file;
        // cetak_r($source, 1);
        $destination = FCPATH . 'uploads/recruitment/' . $nama_file;

        if (empty($nama_file) || $nama_file == '-') {
            echo json_encode([
                'status' => false,
                'message' => 'Dokumen tidak tersedia'
            ]);
            return;
        } 

        if (!file_exists($source)) {
            echo json_encode([
                'status' => false,
                'message' => 'File sumber tidak ditemukan'
            ]);
            return;
        }

        if (copy($source, $destination)) {
            echo json_encode([
                'status' => true,
                'message' => 'File berhasil disalin'
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => 'Gagal menyalin file'
            ]);
        }
    }

    // public function sinkron_document()
    // {
    //     $nama_file = $_POST['document'];

    //     if (empty($nama_file) || $nama_file == '-') {
    //         echo json_encode([
    //             'status'  => false,
    //             'message' => 'Dokumen tidak tersedia'
    //         ]);
    //         return;
    //     }

    //     // URL file di aplikasi Recruitment
    //     $source = 'https://recruitment.domain.com/uploads/recruitment/' . $nama_file;

    //     // Lokasi penyimpanan di HRIS
    //     $destination = FCPATH . 'uploads/recruitment/' . $nama_file;

    //     $ch = curl_init($source);

    //     $fp = fopen($destination, 'wb');

    //     curl_setopt($ch, CURLOPT_FILE, $fp);
    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    //     curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    //     curl_exec($ch);

    //     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //     curl_close($ch);
    //     fclose($fp);

    //     if ($httpCode == 200) {
    //         echo json_encode([
    //             'status'  => true,
    //             'message' => 'File berhasil disalin'
    //         ]);
    //     } else {

    //         if (file_exists($destination)) {
    //             unlink($destination);
    //         }

    //         echo json_encode([
    //             'status'  => false,
    //             'message' => 'File sumber tidak ditemukan'
    //         ]);
    //     }
    // }

   

   

}
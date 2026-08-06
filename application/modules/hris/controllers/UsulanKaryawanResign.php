<?php defined('BASEPATH') OR exit('No direct script access allowed');

class UsulanKaryawanResign extends Public_Controller {

    private $pathView = 'hris/usulan_karyawan_resign/';
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
        $this->hakAkses = hakAkses($this->url);
        // cetak_r($_SESSION['id_user'], 1);
        if ( $this->hakAkses['a_view'] == 1 ) {

            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/toastr/js/toastr.js",
                "assets/toastr/js/toastr.min.js",
                "assets/hris/usulan_karyawan_resign/js/usulan_karyawan_resign.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/toastr/css/toastr.css",
                "assets/toastr/css/toastr.min.css",
                "assets/hris/usulan_karyawan_resign/css/usulan_karyawan_resign.css",
            ));

            $m_conf                     = new \Model\Storage\Conf();

            $data                       = $this->includes;

            $m_karyawan 			= new \Model\Storage\Karyawan_model();
            $d_karyawan 			= $m_karyawan->select(
                                        'karyawan.*',
                                        'jabatan.nama as nama_jabatan'
                                    )->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
                                    ->where('karyawan.status', 1)
                                    ->orderBy('karyawan.level', 'asc')
                                    ->get();
            $data_karyawan  		= $d_karyawan->toArray();
            $m_karyawan             = new \Model\Storage\Karyawan_model();

            $query = $m_karyawan->select(
                        'karyawan.*',
                        'jabatan.nama as nama_jabatan'
                    )
                    ->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan');

            $cek = (clone $query)->where('karyawan.status', 1)->count();
            if ($cek > 0) {
                $d_karyawan = $query->where('karyawan.status', 1)->orderBy('karyawan.level', 'asc')->get();
            } else {
                $d_karyawan = $query->where('karyawan.status', 0)->orderBy('karyawan.id', 'desc') ->limit(1)->get();
            }

            $data_karyawan          = $d_karyawan->toArray();
            $m_usulan               = new \Model\Storage\HrisUsulanResign_model();
            $outstanding            = $m_usulan->getAllData();

            $nik_outstanding        = [];
            foreach($outstanding as $o){
                if (in_array($o['status'], [1, 2, 3])) {
                    $nik_outstanding[] = $o['nik'];
                }
            }

            $content['karyawan']	    = $data_karyawan;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Usulan Karyawan Resign';
            $content['jabatan']         = $m_conf->hydrateRaw("select * from jabatan")->toArray();
            $content['nik_outstanding'] = $nik_outstanding;
            $content['nik_login']       = $this->cek_nik();

            // cetak_r($content, 1);

            // Load Indexx
            $data['title_menu']     = 'HRIS - Usulan Karyawan Resign';

            // $message_telegram = '['.$_SESSION['id_user'].'] '. $_SESSION['detail_user']['nama_detuser'] . ' membuka halaman ' . $data['title_menu']; 
            // $this->telegram_lib->sendMessages($message_telegram);

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }


    public function load_form()
    {
        $m_usulan               = new \Model\Storage\HrisUsulanResign_model();
        $nik_atasan             = $this->cek_nik();
        $content['list']        = $m_usulan->getAllData();

        // cetak_r($content, 1);
        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function generate_kode()
    {
  
        $tahun = date('y');
        $bulan = date('m');

        $m_conf     = new \Model\Storage\Conf();

        $sql = " SELECT MAX(CAST(RIGHT(document, 3) AS INT)) AS last_number
            FROM hris_usulan_resign
            WHERE document LIKE 'DOC/KR/{$tahun}/{$bulan}/%'
        ";

        $d_conf     = $m_conf->hydrateRaw( $sql );
        
        $data       = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }
        
        $last = $data[0]['last_number'] ?? 0;
        $new  = str_pad($last + 1, 3, '0', STR_PAD_LEFT);
        
        $kode = "DOC/KR/$tahun/$bulan/$new";
        return $kode;

    }

    public function save()
    {
        $params = $_POST;

        try {

            $m = new \Model\Storage\HrisUsulanResign_model();
            $m->document            = $this->generate_kode();
            $m->nik                 = $params['nik'];
            $m->tanggal_pengajuan   = $params['tanggal_pengajuan'];
            $m->tanggal_resign      = $params['tanggal_resign'];
            $m->alasan_resign       = $params['alasan_resign'];
            $m->jenis_resign        = $params['jenis'];
            $m->status              = 1;
            $m->save();

            if (!empty($_FILES['attachment'])) {

                $files = $_FILES['attachment'];
                $upload_path = 'uploads/hris_resign/' . $m->id . '/';

                if (!is_dir(FCPATH . $upload_path)) {
                    mkdir(FCPATH . $upload_path, 0755, true);
                }

                for ($i = 0; $i < count($files['name']); $i++) {

                    if (empty($files['name'][$i])) {
                        continue;
                    }

                    $original = $files['name'][$i];
                    $tmp = $files['tmp_name'][$i];
                    $ext = pathinfo($original, PATHINFO_EXTENSION);

                    $encName = md5(uniqid() . $original) . '.' . $ext;
                    $target = $upload_path . $encName;

                    if (move_uploaded_file($tmp, FCPATH . $target)) {


                        $m_att = new \Model\Storage\HrisAttachmentResign_model();
                        
                        $m_att->usulan_id       = $m->id; 
                        $m_att->file_attachment = $encName;
                        $m_att->nama_file       = $original;
                        $m_att->file_path       = $target;
                        $m_att->upload_by       = $this->userdata['detail_user']['nama_detuser'];
                        // cetak_r($m_att, 1);
                        // $m_att->created_at      = date('Y-m-d H:i:s');

                        $m_att->save();
                    }
                }
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/save', $m, $deskripsi_log, null, $m->id, $m);

            $message_telegram =
            "📢 *Pengajuan Resign Baru*\n\n" .
            " - Pengaju : {$params['nik']}\n" .
            " - Tanggal Pengajuan : {$params['tanggal_pengajuan']}\n" .
            " - Tanggal Resign : {$params['tanggal_resign']}\n" .
            " - Jenis Resign : {$params['jenis']}\n" .
            " - Alasan : {$params['alasan_resign']}\n\n" .
            " - Di input oleh : {$_SESSION['detail_user']['nama_detuser']} ({$_SESSION['id_user']})";
            $this->telegram_lib->sendMessages($message_telegram);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';

        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function edit_data()
    {
        $params         = $_POST;
        $m_usulan       = new \Model\Storage\HrisUsulanResign_model();
        $m_karyawan 			= new \Model\Storage\Karyawan_model();

        $d_karyawan 			= $m_karyawan->select(
                                    'karyawan.*',
                                    'jabatan.nama as nama_jabatan'
                                )->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
                                ->where('karyawan.status', 1)
                                ->orderBy('karyawan.level', 'asc')
                                ->get();

        $data_karyawan  		= $d_karyawan->toArray();

        $data['karyawan']	    = $data_karyawan;
        $data['header'] = $m_usulan->getAllData($params['id_data']);
        $data['detail'] = $m_usulan->getDataAttachment($data['header'][0]['id']);
        // $data['detail']

        // cetak_r($data, 1);

        echo $this->load->view($this->pathView . 'v_edit_data', $data, TRUE);

    }


    public function update()
    {
        $params = $_POST;


        try {

            $m_header = new \Model\Storage\HrisUsulanResign_model();
            $id = $params['id'];

            if (!$m_header) {
                throw new Exception('Data tidak ditemukan');
            }

            // update data header
            $m_header->where('id', $id)->update([
                'nik'                => $params['nik'],
                'tanggal_pengajuan'  => $params['tanggal_pengajuan'],
                'tanggal_resign'     => $params['tanggal_resign'],
                'alasan_resign'      => $params['alasan_resign'],
                'jenis_resign'       => $params['jenis'],
            ]);


            // hapus attachment lama
            $m_old_att = new \Model\Storage\HrisAttachmentResign_model();
            $old_files = $m_old_att->where('usulan_id', $id)->get();
            foreach ($old_files as $old) {
                // hapus file fisik
                if (!empty($old->file_path) && file_exists(FCPATH . $old->file_path)) {
                    unlink(FCPATH . $old->file_path);
                }
                // hapus record database
                $old->delete();
            }


            // upload attachment baru
            if (!empty($_FILES['attachment'])) {

                $files          = $_FILES['attachment'];
                $upload_path    = 'uploads/hris_resign/' . $id . '/';

                if (!is_dir(FCPATH . $upload_path)) {
                    mkdir(FCPATH . $upload_path, 0755, true);
                }

                for ($i = 0; $i < count($files['name']); $i++) {

                    if (empty($files['name'][$i])) {
                        continue;
                    }

                    $original = $files['name'][$i];
                    $tmp      = $files['tmp_name'][$i];

                    $ext = pathinfo($original, PATHINFO_EXTENSION);


                    $encName = md5(uniqid() . $original) . '.' . $ext;

                    $target = $upload_path . $encName;

                    if (move_uploaded_file($tmp, FCPATH . $target)) {
                        $m_att = new \Model\Storage\HrisAttachmentResign_model();
                        $m_att->usulan_id       = $id;
                        $m_att->file_attachment = $encName;
                        $m_att->nama_file       = $original;
                        $m_att->file_path       = $target;
                        $m_att->upload_by       = $this->userdata['detail_user']['nama_detuser'];
                        $m_att->save();
                    }

                }

            }


            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];

            Modules::run(
                'base/event/update',
                $m_header,
                $deskripsi_log,
                null,
                $id,
                $m_header
            );


            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di update.';


        } catch (Exception $e) {

            $this->result['message'] = $e->getMessage();

        }


        display_json($this->result);
    }


    public function delete_attachment()
    {
        try {

            $params = $_POST;

            $id_header     = $params['id_header'];
            $id_attachment = $params['id_attachment'];

            $m_att = new \Model\Storage\HrisAttachmentResign_model();


            $attachment = $m_att
                ->where('id', $id_attachment)
                ->where('usulan_id', $id_header)
                ->first();


            if(!$attachment){
                throw new Exception('Attachment tidak ditemukan.');
            }

            if(!empty($attachment->file_path)){
                $file = FCPATH . $attachment->file_path;
                if(file_exists($file)){
                    unlink($file);
                }
            }

            $attachment->delete();
            $this->result['status']  = 1;
            $this->result['message'] = 'Attachment berhasil dihapus.';


        } catch(Exception $e){

            $this->result['message'] = $e->getMessage();

        }


        display_json($this->result);
    }


    public function delete()
    {
        $params = $_POST;

        try {

            $id = $params['id_data'];

            $m_header = new \Model\Storage\HrisUsulanResign_model();
            $data_header = $m_header->where('id', $id)->first();

            if (!$data_header) {
                throw new Exception('Data tidak ditemukan');
            }

            $m_att = new \Model\Storage\HrisAttachmentResign_model();
            $attachments = $m_att->where('usulan_id', $id)->get();

            foreach ($attachments as $att) {
                if (!empty($att->file_path) && file_exists(FCPATH . $att->file_path)) {
                    unlink(FCPATH . $att->file_path);
                }

                $att->delete();
            }


            $data_header->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/delete', $m_header, $deskripsi_log, null, $id, $m_header);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil dihapus.';


        } catch (Exception $e) {

            $this->result['message'] = $e->getMessage();

        }

        display_json($this->result);
    }

    public function cek_nik()
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = "
			SELECT 
				du.id_user,
				k.nik
			FROM detail_user du
			INNER JOIN karyawan k
				ON du.nama_detuser = k.nama
				AND k.status = 1
			WHERE du.id_user = '" . $_SESSION['id_user'] . "'
			AND du.nonaktif_detuser IS NULL
		";

		$d_conf = $m_conf->hydrateRaw($sql);

		// cetak_r($sql);

		if ($d_conf->count() == 0) {
			return null;
		}

		$data = $d_conf->toArray();

		if ($_SESSION['detail_user']['data_group']['nama_group'] == 'HRD') {
			return null;
		}

		return $data[0]['nik'];
	}


    public function ReportUsulanKaryawan()
    {

        $akses= hakAkses($this->url . '/ReportUsulanKaryawan');

        $m = new \Model\Storage\HrisUsulanResign_model();

        $this->add_external_js(array(
            "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
            "assets/select2/js/select2.min.js",
            "assets/toastr/js/toastr.js",
            "assets/toastr/js/toastr.min.js",
            "assets/hris/usulan_karyawan_resign/js/usulan_karyawan_resign.js",
        ));
        $this->add_external_css(array(
            "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
            "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
            "assets/select2/css/select2.min.css",
            "assets/toastr/css/toastr.css",
            "assets/toastr/css/toastr.min.css",
            "assets/hris/usulan_karyawan_resign/css/usulan_karyawan_resign.css",
        ));


        $m_conf                 = new \Model\Storage\Conf();
        $data                   = $this->includes;
        $content['akses']       = $akses;
        $content['title_panel'] = 'HRIS - Usulan Karyawan Resign';
        $nik_atasan             = $this->cek_nik();
        $content['list_usulan'] = $m->getDataUsulanResign($nik_atasan);
        $content['config']      = $this->userdata['detail_user']['nama_detuser'];

        // cetak_r($content['akses'], 1);

        // Load Indexx
        $data['title_menu']     = 'HRIS - Usulan Karyawan Resign';


        $data['view'] = $this->load->view($this->pathView . 'v_report_resign', $content, TRUE);
        $this->load->view($this->template, $data);

    }

    public function updateKeputusan()
    {
        $params = $_POST;

        $this->result = [
            'status' => 0,
            'message' => 'Parameter tidak lengkap.'
        ];

        if (empty($params['id']) || empty($params['status'])) {
            display_json($this->result);
            return;
        }

        $id = $params['id'];
        $status = (int) $params['status'];
        $keterangan_reject = isset($params['keterangan_reject']) ? $params['keterangan_reject'] : null;

        $map = [
            1 => 'DRAFT',
            2 => 'ACKNOWLEDGE',
            3 => 'APPROVED',
            4 => 'REJECT ATASAN',
            5 => 'REJECT HRD'
        ];

        if (!isset($map[$status])) {
            $this->result['message'] = 'Status tidak valid.';
            display_json($this->result);
            return;
        }

        if (in_array($status, [4, 5]) && empty(trim($keterangan_reject))) {
            $this->result['message'] = 'Keterangan reject wajib diisi.';
            display_json($this->result);
            return;
        }

        $reject_note = null;
        if (in_array($status, [4, 5])) {
            $reject_note = trim($keterangan_reject);
        }

        try {

            $m_ur = new \Model\Storage\HrisUsulanResign_model();

            $d = $m_ur->where('id', $id)->first();

            if (!$d) {
                throw new \Exception('Data tidak ditemukan.');
            }

            $update = [
                'status' => $status,
                'keterangan_reject' => $reject_note
            ];

            if (in_array($status, [3, 5])) {
                $update['approved_by']          = $this->userdata['detail_user']['nama_detuser'];
                $update['approve_reject_date']  =  date('Y-m-d H:i:s');                
            } else {
                $update['ack_by']           = $this->userdata['detail_user']['nama_detuser'];
                $update['ack_date']         =  date('Y-m-d H:i:s');     
            }

            $m_ur->where('id', $id)->update($update);

            $deskripsi_log = 'Keputusan usulan resign di-set ke '.$map[$status].' oleh '.$this->userdata['detail_user']['nama_detuser'];

            if (!empty($reject_note)) {
                $deskripsi_log .= ' (catatan: '.substr($reject_note, 0, 200).')';
            }

            Modules::run('base/event/update', $m_ur, $deskripsi_log, null, $id, $m_ur);

            $this->result['status'] = 1;
            $this->result['message'] = 'Keputusan berhasil disimpan.';

        } catch (\Exception $e) {

            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();

        }

        display_json($this->result);
    }

    public function formClearanceResign()
    {
        $this->add_external_js(array(
            "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
            "assets/select2/js/select2.min.js",
            "assets/toastr/js/toastr.js",
            "assets/toastr/js/toastr.min.js",
            "assets/hris/usulan_karyawan_resign/js/usulan_karyawan_resign.js",
        ));
        $this->add_external_css(array(
            "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
            "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
            "assets/select2/css/select2.min.css",
            "assets/toastr/css/toastr.css",
            "assets/toastr/css/toastr.min.css",
            "assets/hris/usulan_karyawan_resign/css/usulan_karyawan_resign.css",
        ));

        $data                       = $this->includes;
        $m_model                    = new \Model\Storage\HrisUsulanResign_model();
        $m_clearance                = new \Model\Storage\HrisAttachmentClearance_model();

        $content['akses']           = $this->hakAkses;
        $content['title_panel']     = 'HRIS - Form Clearance';
        $content['data_clearance']  = $m_model->getAllData($_GET['kode'])[0] ?? [];
        $content['attachment']      = $m_model->getDataAttachment($_GET['kode']);
        $content['clearance']       = $m_clearance->getClearanceById($_GET['kode']);

        // cetak_r($content['data_clearance'], 1);

        // Load Indexx
        $data['title_menu']     = 'HRIS - Form Clearance';

        $data['view'] = $this->load->view($this->pathView . 'v_form_clearance', $content, TRUE);
        $this->load->view($this->template, $data);

    }

    // public function save_clearance()
    // {
    //     $params = $_POST;
    //     // cetak_r($_FILES, 1);

    //     try {

    //         $m_header = new \Model\Storage\HrisUsulanResign_model();
    //         $m_header->where('id', $params['id_data'])->update([
    //             'clearance_date'     => date('Y-m-d H:i:s'),
    //         ]);

    //         $old = new \Model\Storage\HrisAttachmentClearance_model();
    //         $old->where('usulan_id', $params['id_data'])->delete();


    //         foreach($params['data'] as $dt){

    //             $m = new \Model\Storage\HrisAttachmentClearance_model();

    //             $m->usulan_id            = $params['id_data'];
    //             $m->nama_fasilitas       = $dt['nama_fasilitas'];
    //             $m->kondisi_fasilitas    = $dt['kondisi_fasilitas'];
    //             $m->jumlah               = $dt['jumlah_fasilitas'];

    //             $m->save();
    //         }


    //         $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
    //         Modules::run('base/event/save', $m, $deskripsi_log, null, $m->id, $m);


    //         $this->result['status'] = 1;
    //         $this->result['message'] = 'Data berhasil di simpan.';

    //     } catch (Exception $e) {

    //         $this->result['message'] = $e->getMessage();

    //     }

    //     display_json($this->result);
    // }


    public function save_clearance()
    {
        $params = $_POST;

        try {

            $m_header = new \Model\Storage\HrisUsulanResign_model();

            $m_header
                ->where('id', $params['id_data'])
                ->update([
                    'clearance_date' => date('Y-m-d H:i:s'),
                ]);


            // hapus attachment lama
            $old = new \Model\Storage\HrisAttachmentClearance_model();

            $old_data = $old
                ->where('usulan_id', $params['id_data'])
                ->get()
                ->toArray();


            foreach($old_data as $row){

                if(!empty($row['path_file']) && file_exists($row['path_file'])){
                    unlink($row['path_file']);
                }

            }


            $delete = new \Model\Storage\HrisAttachmentClearance_model();

            $delete
                ->where('usulan_id', $params['id_data'])
                ->delete();



            $data_clearance = json_decode($params['data'], true);


            /*
            * INSERT FASILITAS BARU
            */
            $insert_ids = [];


            foreach($data_clearance as $dt){

                $m = new \Model\Storage\HrisAttachmentClearance_model();

                $m->usulan_id         = $params['id_data'];
                $m->nama_fasilitas    = $dt['nama_fasilitas'];
                $m->kondisi_fasilitas = $dt['kondisi_fasilitas'];
                $m->jumlah            = $dt['jumlah_fasilitas'];

                $m->save();


                /*
                * mapping:
                * id lama javascript => id baru database
                */
                $insert_ids[$dt['id']] = $m->id;

            }



            /*
            * UPLOAD FILE
            */
            if(isset($_FILES['attachment'])){


                $upload_path = "uploads/hris_resign/clearance/";


                if(!is_dir($upload_path)){
                    mkdir($upload_path,0777,true);
                }



                foreach($_FILES['attachment']['name'] as $key=>$file_name){


                    if(empty($file_name)){
                        continue;
                    }


                    /*
                    * key dari JS:
                    * attachment[id_fasilitas]
                    */
                    if(!isset($insert_ids[$key])){
                        continue;
                    }


                    // id clearance baru
                    $id_clearance = $insert_ids[$key];


                    $tmp_name = $_FILES['attachment']['tmp_name'][$key];


                    $ext = pathinfo($file_name, PATHINFO_EXTENSION);


                    $new_name = md5(uniqid($file_name,true)).'.'.$ext;


                    $path = $upload_path.$new_name;



                    if(move_uploaded_file($tmp_name,$path)){


                        $update = new \Model\Storage\HrisAttachmentClearance_model();


                        $update
                            ->where('id',$id_clearance)
                            ->update([
                                'nama_file'=>$file_name,
                                'path_file'=>$path
                            ]);

                    }

                }

            }



            $deskripsi_log = 'di-submit oleh '.$this->userdata['detail_user']['nama_detuser'];


            Modules::run(
                'base/event/save',
                $m_header,
                $deskripsi_log,
                null,
                $params['id_data'],
                $m_header
            );


            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil disimpan.';


        } catch(Exception $e){

            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();

        }


        display_json($this->result);
    }

    public function verifikasiClearance()
    {
        $this->add_external_js(array(
            "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
            "assets/select2/js/select2.min.js",
            "assets/toastr/js/toastr.js",
            "assets/toastr/js/toastr.min.js",
            "assets/hris/usulan_karyawan_resign/js/usulan_karyawan_resign.js",
        ));
        $this->add_external_css(array(
            "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
            "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
            "assets/select2/css/select2.min.css",
            "assets/toastr/css/toastr.css",
            "assets/toastr/css/toastr.min.css",
            "assets/hris/usulan_karyawan_resign/css/usulan_karyawan_resign.css",
        ));

        $data                       = $this->includes;
        // $m_model                    = new \Model\Storage\HrisUsulanResign_model();
        $m_clearance                = new \Model\Storage\HrisAttachmentClearance_model();
        $kode                       = $_GET['kode'] ?? null;

        $content['filter']          = $m_clearance->getFilterData();
        $content['akses']           = $this->hakAkses;
        $content['title_panel']     = 'HRIS - Verifikasi dan Serah Terima Clearance';
        $content['data_karyawan']   = $kode ? ($m_clearance->getDataClearance($kode)[0] ?? []) : [];
        $content['data_clearance']  = $kode ? ($m_clearance->getClearanceById($kode) ?? []) : [];
        // cetak_r($content, 1);

        // Load Indexx
        $data['title_menu']         = 'HRIS - Verifikasi dan Serah Terima Clearance';

        $data['view'] = $this->load->view($this->pathView . 'v_verifikasi_clearance', $content, TRUE);
        $this->load->view($this->template, $data);

    }

    public function saveVerifikasiClearance()
    {
        $params = $_POST;

        // cetak_r($_POST, 1);

        try {

            foreach($params['data'] as $dt){
                $m = new \Model\Storage\HrisAttachmentClearance_model();

                $m->where('id', $dt['id'])->update([
                    'status_clearance'     => $dt['status'],
                    'catatan_clearance'    => isset($dt['catatan']) ? $dt['catatan'] : '',
                    'verifikasi_by'        => $this->userdata['detail_user']['nama_detuser'],
                    'tanggal_verifikasi'   => date('Y-m-d H:i:s'),
                ]);

            }

            $m_header = new \Model\Storage\HrisUsulanResign_model();
            $m_header->where('id', $params['id_data'])->update([
                'verification_clearance_date'     => date('Y-m-d H:i:s'),
                'verification_by'                 => $this->userdata['detail_user']['nama_detuser'],
            ]);


            if ($params['nik']){

                // UPDATE MS USER
                // $id_user     = $m_header->getIdUser($params['nik']);
                // $ms_user     = new \Model\Storage\User_model();
                // $data_msuser = $ms_user->where('id_user', $id_user)->first();

                // if (!empty($data_msuser)) {
                //     $ms_user->where('id_user', $id_user)->update([
                //         'status_user' => 0,
                //     ]);
                // }

                // // UPDATE KARYAWAN
                // $m_karyawan     = new \Model\Storage\Karyawan_model();
                // $data_karyawan  = $m_karyawan->where('nik', $params['nik'])->where('status', 1)->first();

                // if (!empty($data_karyawan)) {
                //     $m_karyawan->where('nik', $params['nik'])
                //             ->where('status', 1)
                //             ->update([
                //                 'status' => 0,
                //             ]);
                // }

                // // UPDATE DATA KANDIDAT
                // $m_kandidat     = new \Model\Storage\HrisDataKandidat_model();
                // $data_kandidat  = $m_kandidat->where('nik', $params['nik'])->first();
                // if (!empty($data_kandidat)) {
                //     $m_kandidat->where('nik', $params['nik'])->update([
                //         'tgl_keluar' => date('Y-m-d H:i:s'),
                //     ]);
                // }


                // // UPDATE HISTORY KARYAWAN
                // $k_history      = new \Model\Storage\KaryawanHistory_model();
                // $data_history   = $k_history->where('nik', $params['nik'])->first();

                // if (!empty($data_history)) {
                //     $k_history->where('nik', $params['nik'])->update([
                //         'tgl_selesai' => date('Y-m-d H:i:s'),
                //     ]);
                // }
               
            }

            $deskripsi_log = 'Verifikasi dan serah terima clearance oleh ' . $this->userdata['detail_user']['nama_detuser'];

            Modules::run('base/event/update', $m_header, $deskripsi_log, null, $params['id_data'], $m_header);

            $this->result['status'] = 1;
            $this->result['message'] = 'Verifikasi clearance berhasil disimpan.';


        } catch(Exception $e){

            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();

        }

        display_json($this->result);
    }

    public function getAttachments($usulan_id = null)
    {
        if (empty($usulan_id)) return [];

        $m = new \Model\Storage\HrisAttachmentResign_model();
        $d = $m->where('usulan_id', $usulan_id)
            ->select(['id', 'usulan_id', 'file_attachment', 'nama_file', 'file_path'])
            ->get();

        return $d ? $d->toArray() : [];
    }


    public function showAttachment()
    {
        
        $data['attachment'] = $this->getAttachments($_POST['id']);
        // cetak_r($data, 1);
        echo $this->load->view($this->pathView . 'v_detail_attahcment', $data, TRUE);
    }


    public function revert_status()
    {
        $params = $_POST;

        try {
            $m_header = new \Model\Storage\HrisUsulanResign_model();

            if ($params['revert'] == 'DRAFT') {
                $m_header->where('id', $params['id_data'])->update([
                    'ack_by'   => null,
                    'ack_date' => null,
                    'status'   => 1,
                    'keterangan_reject'   => 1,
                ]);
            } elseif ($params['revert'] == 'ACK') {
                $m_header->where('id', $params['id_data'])->update([
                    'approved_by'         => null,
                    'approve_reject_date' => null,
                    'status'              => 2,
                    'keterangan_reject'   => 1,
                ]);
            } else {
                throw new \Exception('Status revert tidak valid.');
            }

            $this->result['status'] = 1;
            $this->result['message'] = 'Status berhasil dikembalikan.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    

}
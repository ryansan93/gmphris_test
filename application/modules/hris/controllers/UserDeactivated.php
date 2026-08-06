<?php defined('BASEPATH') OR exit('No direct script access allowed');

class UserDeactivated extends Public_Controller {

    private $pathView = 'hris/user_deactivated/';
    private $url;
    private $hakAkses;

    function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    public function index($segment=0)
    {

        if ( $this->hakAkses['a_view'] == 1 ) {
            $m_conf     = new \Model\Storage\Conf();
            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/hris/user_deactivated/js/user_deactivated.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/user_deactivated/css/user_deactivated.css",
            ));
            $m_conf                     = new \Model\Storage\Conf();

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Penonaktifan User';
            $content['jabatan']   = $m_conf->hydrateRaw("select * from jabatan")->toArray();

            // cetak_r($content['akses'], 1);

            // Load Indexx
            $data['title_menu']         = 'HRIS - Penonaktifan User';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }

    public function load_data()
    {

      
        $data['list_data'] = $this->get_data();

        // cetak_r($data, 1);
        echo $this->load->view($this->pathView.'v_list', $data, true);
    }

    public function filter_data()
    {
        $params = $_POST;

        $need = [
            'jenis' => 'FILTER',
            'data'  => $params,
        ];


        $data['list_data'] = $this->get_data($need);
        echo $this->load->view($this->pathView.'v_list', $data, true);
    }


    public function get_data($need = null)
    {

        $m_conf = new \Model\Storage\Conf();

        $sql = " SELECT 
                    hur.id, 
                    hur.document, 
                    hur.nik, 
                    hur.tanggal_resign, 
                    hur.nonactive_user_by, 
                    hur.nonactive_user_date, 
                    hur.clearance_date,
                    k.nama as nama_karyawan,
                    j.nama as nama_jabatan
                FROM hris_usulan_resign hur
                INNER JOIN karyawan k 
                    ON k.nik = hur.nik
                    AND k.id = (
                        SELECT MAX(k2.id)
                        FROM karyawan k2
                        WHERE k2.nik = hur.nik
                    )
                INNER JOIN jabatan j 
                    ON k.jabatan = j.kode
        ";

        $where = [];
        $where[] = "hur.clearance_date IS NOT NULL";

        $jenis    = $need['jenis'] ?? null;
        $dataNeed = $need['data'] ?? null;

        if ($jenis == 'FILTER' && is_array($dataNeed)) {

            $jabatan = $dataNeed['jabatan'] ?? null;
            $status  = $dataNeed['status'] ?? null;

            if ($status == 'active') {
                $where[] = "hur.nonactive_user_by IS NULL";
            } else if ($status == 'nonactive') {
                $where[] = "hur.nonactive_user_by IS NOT NULL";
            }

            if (!empty($jabatan)) {
                $where[] = "k.jabatan = '".addslashes($jabatan)."'";
            }
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY hur.id DESC";

        // cetak_r($sql, 1);




        $d_conf = $m_conf->hydrateRaw($sql);

        return $d_conf->count() > 0 ? $d_conf->toArray() : [];
    }

    

    public function save()
    {

        $params = $_POST;
        // cetak_r($params, 1);
        
        try {
    
           

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
            // $this->result['content'] = array('id' => $no_mm);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );

    }


    public function edit_data()
    {
        $params = $_POST;
        // cetak_r($params, 1);

        $m_conf                     = new \Model\Storage\Conf();

        $data                       = $this->includes;
        $content['jabatan']         = $this->getJabatan($params['kode_jabatan'])[0];
        $content['jabatan_atasan']  = $m_conf->hydrateRaw("select distinct(j.kode), j.* from jabatan_atasan ja inner join jabatan j on ja.kode_jabatan_atasan = j.kode ")->toArray();
        //  cetak_r($content, 1);

        echo $this->load->view($this->pathView . 'v_edit_data', $content, TRUE);
    }

    public function update()
    {
        $params = $_POST;
        // cetak_r($params, 1);

        try {
            $kode_jabatan = $params['kode_jabatan'];

            $m_jabatan = new \Model\Storage\MasterJabatan_model();

            $d_jabatan = $m_jabatan->where('kode', $kode_jabatan)->first();
            if (!$d_jabatan) {
                throw new \Exception("Data form tidak ditemukan.");
            }

            $m_jabatan->where('kode', $kode_jabatan)->update([
                'nama'  => $params['nama_jabatan'],
                'level' => $params['level'],
                'kode_dokumen' => $params['kode_dokumen'],
            ]);

            $m_jabatan_atasan = new \Model\Storage\MasterJabatanAtasan_model();

            $m_jabatan_atasan->where('kode_jabatan', $kode_jabatan)->update([
                'kode_jabatan_atasan' => $params['jabatan_atasan'],
            ]);

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/update', $m_jabatan, $deskripsi_log, null, $kode_jabatan, $m_jabatan);

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
        $kode_jabatan = $params['kode_jabatan'];
        $m_jabatan = new \Model\Storage\MasterJabatan_model();
        $m_jabatan_atasan = new \Model\Storage\MasterJabatanAtasan_model();

        try {

            $m_jabatan->where('kode', $kode_jabatan)->delete();
            $m_jabatan_atasan->where('kode_jabatan', $kode_jabatan)->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/delete', $m_jabatan, $deskripsi_log, null, $kode_jabatan, $m_jabatan);


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
        $content['akses']           = $this->hakAkses;
        $content['list']            =  $this->getJabatan();
        // cetak_r($content, 1);

        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function generate_kode(){
        $m_kategori = new \Model\Storage\HrisKategori_model();
        $last = $m_kategori->where('kode_kategori', 'like', 'HRIS/K/%')->orderBy('kode_kategori', 'desc')->first();

        $no = 1;

        if ($last) {
            // ambil angka terakhir (001, 002, dst)
            $last_kode = $last->kode_kategori;
            $explode = explode('/', $last_kode);
            $no = (int)$explode[2] + 1;
        }

        $no_format = str_pad($no, 3, '0', STR_PAD_LEFT);
        $kode_kategori = 'HRIS/K/' . $no_format;

        return $kode_kategori;
    }


    public function exec_nonaktifkanUser()
    {
        $params = $_POST;

        try {
            $nik = $params['nik'];
            $id_user = $this->cek_id_user($params);
            // cetak_r($id_user, 1);


           // UPDATE MS USER
            $ms_user     = new \Model\Storage\User_model();
            $data_msuser = $ms_user->where('id_user', $id_user)->first();

            $update_ms_user = false;

            if (!empty($data_msuser)) {
                $update_ms_user = $ms_user->where('id_user', $id_user)->update([
                    'status_user' => 0,
                ]);
            }

            if ($update_ms_user) {


                $m_header = new \Model\Storage\HrisUsulanResign_model();

                $m_header->where('id', $params['id_data'])
                    ->update([
                        'nonactive_user_date' => date('Y-m-d H:i:s'),
                        'nonactive_user_by' => $_SESSION['detail_user']['nama_detuser'],
                ]);


                // UPDATE KARYAWAN
                $m_karyawan = new \Model\Storage\Karyawan_model();
                $data_karyawan = $m_karyawan->where('nik', $params['nik'])->orderBy('id', 'desc')->first();

                if (!empty($data_karyawan)) {
                    $m_karyawan->where('id', $data_karyawan->id)
                        ->update([
                            'status' => 0,
                        ]);
                }

                // UPDATE DATA KANDIDAT
                $m_kandidat = new \Model\Storage\HrisDataKandidat_model();
                $data_kandidat = $m_kandidat->where('nik', $params['nik'])->first();

                if (!empty($data_kandidat)) {
                    $m_kandidat->where('nik', $params['nik'])->update([
                        'tgl_keluar' => date('Y-m-d H:i:s'),
                    ]);
                }

                // UPDATE HISTORY KARYAWAN
                $k_history = new \Model\Storage\KaryawanHistory_model();
                $data_history = $k_history->where('nik', $params['nik'])->first();

                if (!empty($data_history)) {
                    $k_history->where('nik', $params['nik'])->update([
                        'tgl_selesai' => date('Y-m-d H:i:s'),
                    ]);
                }

            } else {
                throw new \Exception('Gagal menonaktifkan user.');
            }

                

            $this->result['status'] = 1;
            $this->result['message'] = 'User berhasil dinonaktifkan.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function exec_aktifkanUser()
    {
        $params = $_POST;

        try {
            $nik = $params['nik'];
            $id_user = $this->cek_id_user($params);
            // cetak_r($id_user, 1);


           // UPDATE MS USER
            $ms_user     = new \Model\Storage\User_model();
            $data_msuser = $ms_user->where('id_user', $id_user)->first();

            $update_ms_user = false;

            if (!empty($data_msuser)) {
                $update_ms_user = $ms_user->where('id_user', $id_user)->update([
                    'status_user' => 1,
                ]);
            }

            if ($update_ms_user) {


                $m_header = new \Model\Storage\HrisUsulanResign_model();

                $m_header->where('id', $params['id_data'])
                    ->update([
                        'nonactive_user_date' => null,
                        'nonactive_user_by' => null,
                ]);


                // UPDATE KARYAWAN
                $m_karyawan = new \Model\Storage\Karyawan_model();
                $data_karyawan = $m_karyawan->where('nik', $params['nik'])->orderBy('id', 'desc')->first();

                if (!empty($data_karyawan)) {
                    $m_karyawan->where('id', $data_karyawan->id)
                        ->update([
                            'status' => 1,
                        ]);
                }

                // UPDATE DATA KANDIDAT
                $m_kandidat = new \Model\Storage\HrisDataKandidat_model();
                $data_kandidat = $m_kandidat->where('nik', $params['nik'])->first();

                if (!empty($data_kandidat)) {
                    $m_kandidat->where('nik', $params['nik'])->update([
                        'tgl_keluar' => null,
                    ]);
                }

                // UPDATE HISTORY KARYAWAN
                $k_history = new \Model\Storage\KaryawanHistory_model();
                $data_history = $k_history->where('nik', $params['nik'])->first();

                if (!empty($data_history)) {
                    $k_history->where('nik', $params['nik'])->update([
                        'tgl_selesai' => null,
                    ]);
                }

            } else {
                throw new \Exception('Gagal mengaktifkan user.');
            }

                

            $this->result['status'] = 1;
            $this->result['message'] = 'User berhasil diaktifkan.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function cek_id_user($data)
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = "
			SELECT 
				du.id_user,
				k.nik
			FROM detail_user du
			inner join karyawan k ON du.nama_detuser = k.nama
                                AND k.id = (
                                    SELECT MAX(k2.id)
                                    FROM karyawan k2
                                    WHERE k2.nik = '" . $data['nik'] . "'
                                )
			WHERE k.nik = '" . $data['nik'] . "'
			AND du.nonaktif_detuser IS NULL
		";

        

        // cetak_r($sql, 1);

        $d_conf = $m_conf->hydrateRaw($sql);

		if ($d_conf->count() == 0) {
			return null;
		}

		$data = $d_conf->toArray();

		return $data[0]['id_user'];
	}

}
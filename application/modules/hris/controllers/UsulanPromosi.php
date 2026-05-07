<?php defined('BASEPATH') OR exit('No direct script access allowed');

class UsulanPromosi extends Public_Controller {

    private $pathView = 'hris/usulan_promosi/';
    private $url;
    private $hakAkses;

    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    public function index($segment=0)
    {

        if ( $this->hakAkses['a_view'] == 1 ) {

            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/toastr/js/toastr.js",
                "assets/toastr/js/toastr.min.js",
                "assets/hris/usulan_promosi/js/usulan_promosi.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/toastr/css/toastr.css",
                "assets/toastr/css/toastr.min.css",
                "assets/hris/usulan_promosi/css/usulan_promosi.css",
            ));

            $m_conf                     = new \Model\Storage\Conf();

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Usulan Promosi';
            $content['karyawan']        = $this->get_list_karyawan();
            $content['jabatan']         =  $m_conf->hydrateRaw("select * from jabatan")->toArray();

            // cetak_r($content['jabatan'],1);

            $url                     = 'hris/UsulanPromosi';
		    $content['akses']        = $akses = hakAkses('/'.$url);
           

            // Load Indexx
            $data['title_menu']     = 'HRIS - Usulan Promosi';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }

    public function get_list_karyawan()
    {   
        $m_conf         = new \Model\Storage\Conf();
        $m_karyawan     = new \Model\Storage\Karyawan_model();
        $db_karyawan    = $m_karyawan->with('unit')->get()->toArray();
        $jabatan        = $m_conf->hydrateRaw("select * from jabatan")->toArray();

        $karyawan = [];
        foreach($db_karyawan as $dk){
            if ((int)$dk['status'] === 1){
                $karyawan[$dk['id']] = $dk;
            }
        }

        foreach ($karyawan as &$k) {
            foreach ($jabatan as $j) {
                if ($k['jabatan'] == $j['kode']) {
                    $k['detail_jabatan'] = $j;
                }
            }
        }

        usort($karyawan, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });
        // unset($k); 
        // cetak_r($karyawan, 1);

        return $karyawan;
    }

    public function save()
    {
        
        $params = $_POST;
       
        try {
            $m_db     = new \Model\Storage\HrisUsulanMutasi_model();

            $m_db->kode             = $this->generate_kode();
            $m_db->tanggal          = $params['tgl_usulan'];
            $m_db->pengusul         = $params['pengusul'];
            $m_db->karyawan         = $params['karyawan'];
            $m_db->jabatan_asal     = $params['jabatan_asal'];
            $m_db->jabatan_tujuan   = $params['jabatan_tujuan'];
            $m_db->jenis            = 'PROMOSI';
            $m_db->alasan           = $params['alasan'];

            // $m_db->tgl_berlaku      = $params['kategori'];
            $m_db->status           = 1;
            // $m_db->jenis            = $params['kategori'];
            // cetak_r($m_db, 1);
            
            $m_db->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/save', $m_db, $deskripsi_log, null, $m_db->kode , $m_db);


            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
            // $this->result['content'] = array('id' => $no_mm);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );

    }


    public function load_form(){

        $content['list'] =  $this->get_list_data();
        // cetak_r($content, 1);

        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function filter_data(){

        // cetak_r($_POST, 1);
         $need = [
            'jenis' => 'FILTER',
            'data'  => $_POST,
        ];

        $content['list'] =  $this->get_list_data($need);

        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function generate_kode()
    {
  
        $tahun = date('Y');
        $bulan = date('m');

        $m_conf     = new \Model\Storage\Conf();

        $sql = "SELECT MAX(CAST(RIGHT(kode, 3) AS INT)) AS last_number
		FROM hris_usulan_mutasi
		WHERE kode  LIKE 'DOC/SPJ/{$tahun}{$bulan}%'";

        $d_conf     = $m_conf->hydrateRaw( $sql );
        
        $data       = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }
        
        $last = $data[0]['last_number'] ?? 0;
        $new  = str_pad($last + 1, 3, '0', STR_PAD_LEFT);
        
        $kode = "DOC/SPJ/$tahun$bulan$new";
        return $kode;

    }

    public function get_list_data($need = null)
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = " SELECT 
                    karyawan.nama as nama_karyawan, 
                    pengusul.nama as nama_pengusul, 
                    asal.nama as nama_jabatan_asal, 
                    tujuan.nama as nama_jabatan_tujuan, 
                    hum.*  
                FROM hris_usulan_mutasi hum
                INNER JOIN karyawan ON hum.karyawan = karyawan.nik AND karyawan.status = 1
                INNER JOIN karyawan pengusul ON hum.pengusul = pengusul.nik AND pengusul.status = 1
                INNER JOIN jabatan asal ON hum.jabatan_asal = asal.kode 
                INNER JOIN jabatan tujuan ON hum.jabatan_tujuan = tujuan.kode ";

        $jenis     = $need['jenis'] ?? null;
        $dataNeed  = $need['data'] ?? null;

        $where = [];

        if (($jenis == 'DETAIL' || $jenis == 'EDIT') && !empty($dataNeed)) {
            $where[] = "hum.kode = '".addslashes($dataNeed)."'";
        }

        if ($jenis == 'FILTER' && is_array($dataNeed)) {
            $tgl_awal  = $dataNeed['tgl_awal'] ?? null;
            $tgl_akhir = $dataNeed['tgl_akhir'] ?? null;
            $jabatan   = $dataNeed['jabatan_usulan'] ?? null;

            if ($tgl_awal && $tgl_akhir) {
                $where[] = "hum.tanggal BETWEEN '".addslashes($tgl_awal)."' AND '".addslashes($tgl_akhir)."'";
            }

            if ($jabatan) {
                $where[] = "hum.jabatan_tujuan = '".addslashes($jabatan)."'";
            }
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY hum.kode DESC";

        // cetak_r($sql, 1);

        $d_conf = $m_conf->hydrateRaw($sql);

        return $d_conf->count() > 0 ? $d_conf->toArray() : [];
    }

    public function show_detail()
    {
        $need = [
            'jenis' => 'DETAIL',
            'data'  => $_POST['kode'],
        ];
        

        $data_detail = $this->get_list_data($need);
        $kode = $_POST['kode'] ?? null;

        $filtered = array_filter($data_detail, function ($row) use ($kode) {
            return $row['kode'] == $kode;
        });

        $result = array_values($filtered); 

        $content['data_detail'] = $result[0];
        $url                    = 'hris/UsulanPromosi';
		$content['akses']        = $akses = hakAkses('/'.$url);

        // cetak_r($content['akses'] , 1);

        
        echo $this->load->view($this->pathView . 'v_detail', $content, TRUE);

    }

    public function edit_data()
    {

        $this->add_external_js(array(
            "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
            "assets/select2/js/select2.min.js",
            "assets/toastr/js/toastr.js",
            "assets/toastr/js/toastr.min.js",
            "assets/hris/usulan_promosi/js/usulan_promosi.js",
        ));
        $this->add_external_css(array(
            "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
            "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
            "assets/select2/css/select2.min.css",
            "assets/toastr/css/toastr.css",
            "assets/toastr/css/toastr.min.css",
            "assets/hris/usulan_promosi/css/usulan_promosi.css",
        ));

        $m_conf                     = new \Model\Storage\Conf();

        $data                       = $this->includes;

        $need = [
            'jenis' => 'EDIT',
            'data'  => $_GET['kode'],
        ];
        
        $content['data_edit']       = $this->get_list_data($need)[0];
        $content['karyawan']        = $this->get_list_karyawan();
        $content['jabatan']         =  $m_conf->hydrateRaw("select * from jabatan")->toArray();

        // cetak_r($content['data_edit'], 1);


          // Load Indexx
        $data['title_menu']     = 'HRIS - Usulan Promosi';
        $data['view'] = $this->load->view($this->pathView . 'v_edit_data', $content, TRUE);
        $this->load->view($this->template, $data);
        

    }

    public function update()
    {
        // cetak_r($_POST, 1);

        $params = $_POST;

        try {
            $kode_usulan =  $params['kode'];

            $m_db     = new \Model\Storage\HrisUsulanMutasi_model();

            $d_db = $m_db->where('kode', $kode_usulan)->first();
            if (!$d_db) {
                throw new \Exception("Data form tidak ditemukan.");
            }

            $m_db->where('kode', $kode_usulan)->update([
                'tanggal'           => $params['tgl_usulan'],
                'pengusul'          => $params['pengusul'],
                'karyawan'          => $params['karyawan'],
                'jabatan_asal'      => $params['jabatan_asal'],
                'jabatan_tujuan'    => $params['jabatan_tujuan'],
                'alasan'            => $params['alasan'],
            ]);

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/update', $m_db, $deskripsi_log, null, $kode_usulan, $m_db);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di update.';

        } catch (Exception $e) {

            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();

        }

        display_json( $this->result );

    }


    public function delete()
    {
        $params = $_POST;
        // cetak_r($params, 1);
        $kode   = $params['kode'];

        $m_db     = new \Model\Storage\HrisUsulanMutasi_model();

        try {

            $m_db->where('kode', $kode)->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/delete', $m_db, $deskripsi_log, null, $kode, $m_db);


            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function keputusan()
    {
        $params = $_POST;

        // cetak_r($params, 1);

        try {
            $kode_usulan = $params['kode'] ?? null;

            if (!$kode_usulan) {
                throw new \Exception("Kode usulan tidak ditemukan.");
            }

            $m_db = new \Model\Storage\HrisUsulanMutasi_model();

            $d_db = $m_db->where('kode', $kode_usulan)->first();
            $data_mutasi = $d_db->toArray();

            // cetak_r($data_mutasi, 1);

            if (!$d_db) {
                throw new \Exception("Data form tidak ditemukan.");
            }

            $keterangan = null;

            if (in_array($params['keputusan'] ?? null, [4, 5])) {
                $keterangan = $params['keterangan'] ?? null;
            }

            $m_db->where('kode', $kode_usulan)->update([
                'status'        => $params['keputusan'],
                'alasan_reject' => $keterangan,
                'tgl_berlaku'   => !empty($params['tgl_berlaku']) ? $params['tgl_berlaku'] : null ,
            ]);

            
            
            if($params['keputusan'] == 3)
            {
                // KARYAWAN
                    $m_karyawan = new \Model\Storage\Karyawan_model();
                    $m_karyawan->where('nik', $data_mutasi['karyawan'])->update([
                        'jabatan'        => $data_mutasi['jabatan_tujuan'],
                    ]);
                // END KARYAWAN

                
                // UPDATE LAST KARYAWAN HISTORY 
                
                    $m_kh = new \Model\Storage\KaryawanHistory_model();

                    $last_history = $m_kh
                        ->where('nik', $data_mutasi['karyawan'])
                        ->orderBy('id', 'desc')
                        ->first();

                    if ($last_history) {
                        $last_data = $last_history->toArray();

                        $m_kh->where('id', $last_data['id'])->update([
                            'tgl_selesai' => date('Y-m-d'),
                        ]);
                    }

        
                // END UPDATE LAST KARYAWAN HISTORY 

                // KARYAWAN HISTORY 
                    $m_karyawan_history                 = new \Model\Storage\KaryawanHistory_model();
                    $m_karyawan_history->nik            = $data_mutasi['karyawan'];
                    $m_karyawan_history->jabatan        = $data_mutasi['jabatan_tujuan'];
                    $m_karyawan_history->tgl_mulai      = $params['tgl_berlaku'] ?? null;
                    $m_karyawan_history->tgl_selesai    = null;
                    $m_karyawan_history->save();
                // END KARYAWAN HISTORY 

            }
            
            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/update', $d_db, $deskripsi_log, null, $kode_usulan, $d_db);

            $this->result['status']  = 1;
            $this->result['message'] = 'Data berhasil di update.';

        } catch (Exception $e) {

            $this->result['status']  = 0;
            $this->result['message'] = $e->getMessage();

        }

        display_json($this->result);
    }

     public function print_preview() {        
  
        $key = "secretkey";

        if (!isset($_GET['kode']) || empty($_GET['kode'])) {
            show_error('ID tidak ditemukan', 400);
        }

        $kode = $_GET['kode'];
        $kode = str_replace(' ', '+', $kode);

        $decrypted = openssl_decrypt($kode, "AES-128-ECB", $key);
        // cetak_r($decrypted, 1);SSSSS

        $need = [
            'jenis' => 'DETAIL',
            'data'  => $decrypted,
        ];

        $content['data'] =  $this->get_list_data($need)[0];

        // cetak_r($akses,1);

        // $content['unit'] =  $this->get_unit();


        $res_view_html = $this->load->view($this->pathView.'v_export_pdf', $content, true);

        echo $res_view_html;
    }

    public function exportPdf()
    {
        $params = $this->input->post('params');

        try {
            $_no_km = $params['kode'];
            
            $kode = exDecrypt( $_no_km );

            $m_km = new \Model\Storage\Km_model();
            $d_km = $m_km->getKmCetak( $kode );

            $struktur = "";
            $text = "";
            foreach ($d_km as $k_km => $v_km) {
                $idx = 1;
                foreach ($v_km as $key => $value) {
                    $struktur .= '"'.$key.'"';
                    $text .= '"'.$value.'"';
                    if ( $idx < count($v_km) ) {
                        $struktur .= ',';
                        $text .= ',';
                    }

                    $idx++;
                }

                $text .= "\n";
            }

            $content = $struktur."\n".$text;
            $fp = fopen("cetak/ckmcet.TXT","wb");
            fwrite($fp,$content);
            fclose($fp);

            system("cmd /c C:/xampp_php7/htdocs/sistem_udlancar/copy_file.bat");

            $this->result['status'] = 1;
            // $this->result['content'] = array('url' => $path);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
   

}
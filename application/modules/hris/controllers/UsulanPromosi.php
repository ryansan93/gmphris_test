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
                "assets/hris/usulan_promosi/js/usulan_promosi.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/usulan_promosi/css/usulan_promosi.css",
            ));

            $m_conf                     = new \Model\Storage\Conf();

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Usulan Promosi';
            $content['karyawan']        = $this->get_list_karyawan();
            $content['jabatan']         =  $m_conf->hydrateRaw("select * from jabatan")->toArray();

            // cetak_r($content['jabatan'],1);
           

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

        $content['list'] =  $this->get_list_data($_POST['kategori']);
        // cetak_r($_POST, 1);

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

    public function get_list_data()
    {

        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select karyawan.nama as nama_karyawan, pengusul.nama as nama_pengusul, asal.nama as nama_jabatan_asal, tujuan.nama as nama_jabatan_tujuan, hum.*  
                        from hris_usulan_mutasi hum
                        INNER JOIN (
                            SELECT * FROM karyawan WHERE status = 1
                        ) karyawan ON hum.karyawan  = karyawan.nik
                        INNER JOIN (
                            SELECT * FROM karyawan WHERE status = 1
                        ) pengusul ON hum.pengusul  = pengusul.nik
                        inner join jabatan asal on hum.jabatan_asal  = asal.kode 
                        inner join jabatan tujuan on hum.jabatan_tujuan  = tujuan.kode 
                        order by hum.tanggal desc ";

        $d_conf     = $m_conf->hydrateRaw( $sql );

        $data       = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;

    }

    public function show_detail()
    {
        $data_detail = $this->get_list_data();
        $kode = $_POST['kode'] ?? null;

        $filtered = array_filter($data_detail, function ($row) use ($kode) {
            return $row['kode'] == $kode;
        });

        $result = array_values($filtered); 

        $content['data_detail'] = $result[0];

        // cetak_r($result, 1);
        echo $this->load->view($this->pathView . 'v_detail', $content, TRUE);

    }


   

}
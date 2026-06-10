<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Trackingrecruitment extends Public_Controller {

    private $pathView = 'hris/tracking_recruitment/';
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

        // if ( $this->hakAkses['a_view'] == 1 ) {

            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/hris/tracking_recruitment/js/tracking_recruitment.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/tracking_recruitment/css/tracking_recruitment.css",
            ));

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Tracking Recruitment';
            
            // Load Indexx
            $data['title_menu']     = 'HRIS - Tracking Recruitment';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        // } else {
        //     showErrorAkses();
        // }
    }

    public function load_form()
    {
        $data['list'] = $this->get_list_data();
        // cetak_r($data, 1);
        $this->load->view($this->pathView . 'v_list', $data);
    }

    public function get_list_data()
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = " select hukb.document, k.nama as nama_pengusul, j.nama as nama_jabatan, hukb.jumlah
                from hris_usulan_karyawan_baru hukb 
                inner join karyawan k on hukb.nama_pengusul = k.nik and k.status = 1
                inner join jabatan j on hukb.posisi = j.kode
                -- where tgl_ack is not null ";

        $d_conf = $m_conf->hydrateRaw($sql);

        return $d_conf->count() > 0 ? $d_conf->toArray() : [];
    }

    public function show_tracking()
    {
        $this->add_external_js(array(
            "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
            "assets/select2/js/select2.min.js",
            "assets/hris/tracking_recruitment/js/tracking_recruitment.js",
        ));
        $this->add_external_css(array(
            "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
            "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
            "assets/select2/css/select2.min.css",
            "assets/hris/tracking_recruitment/css/tracking_recruitment.css",
        ));

        $data                       = $this->includes;

        $content['title_panel']     = 'HRIS - Tracking Recruitment';
           
        $document = $this->input->get('document');

        $content['data_usulan']     = $this->get_data_usulan_karyawan($document);
        $content['data_kandidat']   = !empty($content['data_usulan']) ? $this->get_data_kandidat($content['data_usulan']['id_usulan']) : [];
        $content['data_karyawan']   = [];

        $data_kandidat_diterima = array_filter($content['data_kandidat'], function($kandidat) {
            return $kandidat['nik'] != null;
        });
      
        
        if ($data_kandidat_diterima){
            $nik = array_column($data_kandidat_diterima, 'nik');
            $nik_karyawan = "'" . implode("','", $nik) . "'";
            $content['data_karyawan'] = $this->get_data_karyawan($nik_karyawan);
        }

        // cetak_r($content['data_karyawan'], 1);
        

        $data['title_menu']     = 'HRIS - Tracking Recruitment';
        $data['view'] = $this->load->view($this->pathView . 'v_tracking_data', $content, TRUE);
        $this->load->view($this->template, $data);
    }   

    public function get_data_usulan_karyawan($document)
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = " select hukb.id as id_usulan, hukb.acknowledged_rejected_by, hukb.approved_rejected_by, 
                hukb.alasan, hukb.document, k.nama as nama_pengusul, j.nama as nama_jabatan, 
                hukb.status, hukb.tgl_pengusulan, hukb.jumlah as jumlah_kandidat,
                hukb.tgl_ack as tgl_acknowledge, hukb.tgl_approve, hukb.keterangan_hrd, hukb.keterangan_ceo ,
                hukb.tgl_reject
                from hris_usulan_karyawan_baru hukb 
                inner join karyawan k on hukb.nama_pengusul = k.nik and k.status = 1
                inner join jabatan j on hukb.posisi = j.kode  
                where hukb.document = '".$document."' ";

                // cetak_r($sql, 1);

        $d_conf = $m_conf->hydrateRaw($sql);

        return $d_conf->count() > 0 ? $d_conf->first()->toArray() : null;
    }

    public function get_data_kandidat($id_usulan)
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = " select * from hris_data_kandidat hdk where hdk.usulan_id = '".$id_usulan."' ";

        $d_conf = $m_conf->hydrateRaw($sql);

        return $d_conf->count() > 0 ? $d_conf->toArray() : [];
    }

    public function get_data_karyawan($nik_karyawan)
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = "SELECT 
                hk.duration,
                k.id AS id_karyawan,
                k.nama AS nama_karyawan,
                k.nik,
                hskb.*,
                j.nama AS nama_jabatan
            FROM hris_status_karyawan_baru hskb
            INNER JOIN (
                SELECT nik, MAX(id) AS id_terakhir
                FROM hris_status_karyawan_baru
                GROUP BY nik
            ) last_data 
                ON hskb.nik = last_data.nik 
                AND hskb.id = last_data.id_terakhir
            INNER JOIN karyawan k 
                ON hskb.nik = k.nik 
                AND k.status = 1
            INNER JOIN jabatan j 
                ON k.jabatan = j.kode
            INNER JOIN hris_kategori hk 
                ON hskb.kategori = hk.kode_kategori
            WHERE hskb.nik IN (" . $nik_karyawan . ")";

                // cetak_r($sql, 1);

        $d_conf = $m_conf->hydrateRaw($sql);

        return $d_conf->count() > 0 ? $d_conf->toArray() : [];
    
    }

    public function show_karyawan_detail()
    {
        $nik                            = $this->input->post('nik');
        $content['data_karyawan']       = $this->get_detail_karyawan($nik)[0];
        $content['karyawan_history']    = $this->get_history_karyawan($nik);
        $content['data_probation']      = $this->get_data_probation($nik);

        // cetak_r($content['data_probation'] , 1);

        echo $this->load->view($this->pathView . 'v_data_karyawan', $content);
    }

    public function get_detail_karyawan($nik)
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = "select k.id, k.nik, k.nama as nama_karyawan, atasan.nama as nama_atasan, jabatan_atasan.nama from karyawan k 
                inner join jabatan j on k.jabatan = j.kode 
                inner join karyawan atasan on k.atasan  = atasan.id
                inner join jabatan jabatan_atasan on atasan.jabatan = jabatan_atasan.kode 
                where k.nik = '" . $nik . "' and k.status = 1";

        $d_conf = $m_conf->hydrateRaw($sql);

        // cetak_r($sql, 1);

        return $d_conf->count() > 0 ? $d_conf->toArray() : [];
    }

    public function get_history_karyawan($nik)
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = "SELECT j.nama as nama_jabatan, kh.tgl_mulai, kh.tgl_selesai,
                STUFF((
                    SELECT DISTINCT ', ' + w1.nama
                    FROM karyawan_history_unit u1
                    INNER JOIN wilayah w1 
                        ON u1.kode_unit = w1.id
                    WHERE u1.id = kh.id
                    FOR XML PATH(''), TYPE
                ).value('.', 'NVARCHAR(MAX)'), 1, 2, '') AS unit,
                STUFF((
                    SELECT DISTINCT ', ' + w2.nama
                    FROM karyawan_history_wilayah w2h
                    INNER JOIN wilayah w2 
                        ON w2h.kode_wilayah = w2.id
                    WHERE w2h.id = kh.id
                    FOR XML PATH(''), TYPE
                ).value('.', 'NVARCHAR(MAX)'), 1, 2, '') AS wilayah
                FROM karyawan_history kh
                inner join jabatan j on kh.jabatan = j.kode
                WHERE kh.nik = '" . $nik . "' order by kh.tgl_mulai desc";

        $d_conf = $m_conf->hydrateRaw($sql);

        // cetak_r($sql, 1);

        return $d_conf->count() > 0 ? $d_conf->toArray() : [];
    }


    public function get_data_probation($nik)
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = " select hskb.*, hk.nama_kategori from hris_status_karyawan_baru hskb
                inner join hris_kategori hk on hskb.kategori = hk.kode_kategori  where nik = '" . $nik . "'";

        $d_conf = $m_conf->hydrateRaw($sql);

        // cetak_r($sql, 1);

        return $d_conf->count() > 0 ? $d_conf->toArray() : [];
    }
}
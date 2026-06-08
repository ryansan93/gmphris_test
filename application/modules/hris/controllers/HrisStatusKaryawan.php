<?php defined('BASEPATH') OR exit('No direct script access allowed');


class HrisStatusKaryawan extends Public_Controller {

    private $pathView = 'hris/hris_status_karyawan/';
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
        // cetak_r($_SESSION, 1);
        if ( $this->hakAkses['a_view'] == 1 ) {

            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/toastr/js/toastr.js",
                "assets/toastr/js/toastr.min.js",
                "assets/hris/hris_status_karyawan/js/hris_status_karyawan.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/toastr/css/toastr.css",
                "assets/toastr/css/toastr.min.css",
                "assets/hris/hris_status_karyawan/css/hris_status_karyawan.css",
            ));

            $m_conf                     = new \Model\Storage\Conf();

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Status Karyawan';
            $content['karyawan']        = $this->get_list_karyawan();
            $content['kategori']        = $this->get_data_kategori();
            $content['jabatan']         =  $m_conf->hydrateRaw("select * from jabatan")->toArray();

            // Load Indexx
            $data['title_menu']         = 'HRIS - Status Karyawan';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }

    public function get_list_karyawan()
    {
        $m_conf  = new \Model\Storage\Conf();

        $sql = " select * from hris_data_kandidat where nik is not NULL  ";
        
        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data; 
    }

    public function get_data_outstanding()
    {
        $m_conf  = new \Model\Storage\Conf();

        $sql = " select karyawan from hris_usulan_mutasi where status in (1, 2) ";
        
        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function load_form(){

        $data_list              = $this->get_list_data() ?? [];

        if (!empty($_POST['kode'])) {
            $kode_get = urldecode($_POST['kode']);

            foreach ($data_list as $key => $val) {
                if (trim($val['id']) == trim($kode_get)) {
                    $val['selected'] = 'selected';
                    $selected = $val;
                    unset($data_list[$key]);
                    array_unshift($data_list, $selected);
                    break;
                }
            }
        }

        // cetak_r($data_list, 1);
        $content['list']        = $data_list;

        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function filter_data(){

        // cetak_r($_POST, 1);
        $need = [
            'jenis' => 'FILTER',
            'data'  => $_POST,
        ];

        $data_list              = $this->get_list_data($need) ?? [];
        $content['list']        = $data_list;

        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function get_data_kategori()
    {
        $m_conf     = new \Model\Storage\Conf();
        $sql        = "select * from hris_kategori order by kode_kategori asc";

        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function get_list_data($need = null){

        $m_conf     = new \Model\Storage\Conf();

        $sql = " select hskb.*, hk.nama_kategori, k.nama  
        from hris_status_karyawan_baru hskb
        inner join hris_kategori hk on hk.kode_kategori = hskb.kategori 
        inner join karyawan k on hskb.nik = k.nik and k.status = 1 ";

        $where = [];

        if (isset($need['jenis']) && $need['jenis'] == 'FILTER') {

            if (!empty($need['data']['nik'])) {
                $where[] = " hskb.nik = '".$need['data']['nik']."' ";
            }

            if (!empty($need['data']['tanggal'])) {
                $where[] = " hskb.tgl_berlaku = '".$need['data']['tanggal']."' ";
            }
        }

        if (isset($need['jenis']) && $need['jenis'] == 'DETAIL') {
            $where[] = " hskb.id = '".$need['data']."' ";
        }

         if (isset($need['jenis']) && $need['jenis'] == 'CETAK') {
            $where[] = " hskb.id in (".$need['data'].") ";
        }

        if (!empty($where)) {
            $sql .= " WHERE ".implode(' AND ', $where);
        }

        $sql .= " order by hskb.id desc ";

        // cetak_r($sql, 1);

        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function update_status()
    {
        $params = $_POST;
        // cetak_r($params, 1);

        try {

            $m_db = new \Model\Storage\HrisStatusKaryawanBaru_model();
            $d_db = $m_db->where('id', $params['id_data'])->first();

            $m_db->where('id', $params['id_data'])->update([
                'status' => 0
            ]);

            if ($d_db) {

                $newData = $d_db->toArray();

                unset($newData['id']);

                $m_db_new = new \Model\Storage\HrisStatusKaryawanBaru_model();

                foreach ($newData as $key => $val) {
                    $m_db_new->$key = $val;
                }

                $tgl_selesai            = date('Y-m-d', strtotime($params['tgl_berlaku'].' +'.$params['duration'].' months'));
                $m_db_new->status       = 1;
                $m_db_new->kategori     = $params['kategori'];
                $m_db_new->tgl_berlaku  = date('Y-m-d', strtotime($params['tgl_berlaku']));
                $m_db_new->tgl_selesai  = $tgl_selesai;
                $m_db_new->keterangan   = $params['alasan'];
                // cetak_r($m_db_new, 1);
                // $m_db_new->duration     = $params['duration'];

                $m_db_new->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run('base/event/save', $m_db_new, $deskripsi_log, null, $params['id_data'] , $m_db_new);
            }

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil disimpan.';

        } catch (Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function print_preview()
    {
     
        $key = "secretkey";

        if (!isset($_GET['kode']) || empty($_GET['kode'])) {
            show_error('ID tidak ditemukan', 400);
        }

        $kode = $_GET['kode'];
        $kode = str_replace(' ', '+', $kode);

        $decrypted = openssl_decrypt($kode, "AES-128-ECB", $key);
        // cetak_r($decrypted, 1);

        $need = [
            'jenis' => 'DETAIL',
            'data'  => $decrypted,
        ];

        $content['data']     = $this->get_list_data($need)[0];
        $content['karyawan'] =  $this->get_data_detail($content['data']['nik'])[0];

        // cetak_r($content,1);
        

        $res_view_html = $this->load->view($this->pathView.'v_export_pdf', $content, true);

        echo $res_view_html;
    }

    public function get_data_detail($nik)
    {
        $m_conf  = new \Model\Storage\Conf();

        $sql = "select 
                    k.nik,
                    k.nama,
                    j.nama as jabatan,
                    ka.nama as atasan,

                    STUFF((
                        select ', ' + w_un.nama
                        from unit_karyawan uk2
                        inner join wilayah w_un on w_un.id = uk2.unit
                        where uk2.id_karyawan = k.id
                        FOR XML PATH(''), TYPE
                    ).value('.', 'NVARCHAR(MAX)'), 1, 2, '') as unit,

                    STUFF((
                        select distinct ', ' + w_pw.nama
                        from unit_karyawan uk2
                        inner join wilayah w_un on w_un.id = uk2.unit
                        inner join wilayah w_pw on w_pw.id = w_un.induk
                        where 
                            uk2.id_karyawan = k.id
                            and w_pw.jenis = 'PW'
                        FOR XML PATH(''), TYPE
                    ).value('.', 'NVARCHAR(MAX)'), 1, 2, '') as perwakilan

                from karyawan k
                inner join jabatan j on k.jabatan = j.kode
                left join karyawan ka on ka.id = k.atasan
                where 
                    k.nik = '".$nik."'
                    and k.status = 1";

        return $m_conf->hydrateRaw($sql)->toArray();
    }

    public function cetak_data_pdf()
    {
        $params = $this->input->post('id');

        $this->session->set_userdata('kode_pdf', $params);

        echo json_encode([
            'status' => true,
            'url' => base_url('hris/HrisStatusKaryawan/cetak_pdf')
        ]);
        exit;
    }

    public function cetak_pdf()
    {

        if (ob_get_length()) {
            ob_end_clean();
        }

        require_once(APPPATH . 'libraries/tcpdf_new/tcpdf.php');

        $params = $this->session->userdata('kode_pdf');

        $id = "'" . implode("','", $params) . "'";

        

        $need = [
            'jenis' => 'CETAK',
            'data'  => $id,
        ];

        $data_list       = $this->get_list_data($need);

        $content['data'] = $data_list;
        // cetak_r($content);

        $html = $this->load->view($this->pathView . 'v_report_status', $content, true);

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();
        $pdf->writeHTML($html);

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf->Output('report.pdf', 'I');
        exit;
    }


}
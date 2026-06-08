<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ReportHistoryKaryawan extends Public_Controller {

    private $pathView = 'hris/report_history_karyawan/';
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
        // cetak_r($_SESSION['id_user'], 1);
        if ( $this->hakAkses['a_view'] == 1 ) {

            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/toastr/js/toastr.js",
                "assets/toastr/js/toastr.min.js",
                "assets/hris/report_history_karyawan/js/report_history_karyawan.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/toastr/css/toastr.css",
                "assets/toastr/css/toastr.min.css",
                "assets/hris/report_history_karyawan/css/report_history_karyawan.css",
            ));

            $m_conf                     = new \Model\Storage\Conf();

            $data                   = $this->includes;
            $content['akses']       = $this->hakAkses;
            $content['title_panel'] = 'HRIS - Report History Karyawan';
            $content['karyawan']    =  $m_conf->hydrateRaw("select * from karyawan where status = 1")->toArray();

            // Load Indexx
            $data['title_menu']     = 'HRIS - Report History Karyawan';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }

    public function get_unit_wilayah_json(){

        $data = $this->get_unit_wilayah($_POST['id_karyawan']);
        echo json_encode($data);

    }

    public function get_unit_wilayah($id = null)
    {
        $id_karyawan = $id;

        $m_conf = new \Model\Storage\Conf();

        $sql_new_unit = "
            SELECT khu.kode_unit AS unit
            FROM karyawan_history kh
            INNER JOIN karyawan_history_unit khu ON kh.id = khu.id
            INNER JOIN karyawan k ON kh.nik = k.nik
            WHERE k.id = $id_karyawan and kh.tgl_selesai is null
        ";

        $karyawan_unit = $m_conf->hydrateRaw($sql_new_unit)->toArray();

        if (empty($karyawan_unit)) {
            $karyawan_unit = $m_conf->hydrateRaw("
                SELECT unit
                FROM unit_karyawan
                WHERE id_karyawan = $id_karyawan
            ")->toArray();
        }

        $unit = [];
        $wilayah = [];

        foreach ($karyawan_unit as $ku) {

            if ($ku['unit'] == 'all') {

                $unit = [
                    [
                        'id' => 'all',
                        'nama' => 'all'
                    ]
                ];

                $wilayah = [
                    [
                        'id' => 'all',
                        'nama' => 'all'
                    ]
                ];

                break;

            } else {

                $list_unit = $this->get_list_unit($ku['unit']);

                foreach ($list_unit as $u) {
                    $unit[] = $u;
                }

                $induk_wilayah  = array_column($list_unit, 'induk');
                $list_wilayah   = $this->get_list_wilayah($induk_wilayah);
                foreach ($list_wilayah as $w) {
                    $wilayah[] = $w;
                }
            }

            
        }

        // cetak_r($wilayah, 1);
        $unique_wilayah = [];
        $tmp = [];
        foreach ($wilayah as $w) {
            $key = serialize($w);

            if (!isset($tmp[$key])) {
                $tmp[$key] = $w;
            }
        }

        $unique_wilayah = array_values($tmp);

        return [
            'unit'    => $unit,
            'wilayah' => $unique_wilayah,
        ];
    }

    public function get_list_unit($id = null)
	{

		$m_unit = new \Model\Storage\Wilayah_model();
        $d_unit = $m_unit->where('jenis', 'UN');
        
        if (!empty($id)) {
            $id = explode(',', $id);
            $d_unit->whereIn('id', $id);
        }
        
        $d_unit = $d_unit->orderBy('nama')->get();
        // cetak_r($d_unit, 1);
        return $d_unit->toArray();

	}

	public function get_list_wilayah($id = null)
    {
       
        $m_wilayah = new \Model\Storage\Wilayah_model();

        $d_wilayah = $m_wilayah->where('jenis', 'PW');
        if (!empty($id)) {
            $d_wilayah->whereIn('id', [$id]);
        }
        
        $d_wilayah = $d_wilayah->orderBy('nama')->get();
        // cetak_r($d_wilayah, 1);

        return $d_wilayah->toArray();
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
        // cetak_r($params, 1);
       
        try {
            $m_db                       = new \Model\Storage\HrisUsulanMutasi_model();
            $m_db->kode                 = $this->generate_kode();
            $m_db->tanggal              = $params['tgl_usulan'];
            $m_db->pengusul             = $params['pengusul'];
            $m_db->karyawan             = $params['karyawan'];
            $m_db->jabatan_asal         = $params['jabatan_asal'];
            $m_db->jabatan_tujuan       = $params['jabatan_tujuan'];
            $m_db->jenis                = 'PROMOSI';
            $m_db->alasan               = $params['alasan'];
            $m_db->status               = 1;
            $m_db->perwakilan_tujuan    = implode(',', $_POST['perwakilan_tujuan']);
            $m_db->unit_tujuan          = implode(',', $_POST['unit_tujuan']);
            $m_db->perwakilan_asal      = implode(',', $_POST['perwakilan_asal']);
            $m_db->unit_asal            = implode(',', $_POST['unit_asal']);
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

        $data_list       = $this->get_list_data();
        $wilayah         = $this->get_list_wilayah();
        $unit            = $this->get_list_unit();

      
        $mapping_wilayah = [];
        foreach ($wilayah as $w) {
            $mapping_wilayah[$w['id']] = $w['nama'];
        }

        $mapping_unit = [];
        foreach ($unit as $u) {
            $mapping_unit[$u['id']] = $u['nama'];
        }

        foreach ($data_list as &$d) {
            $id_pw_asal = explode(',', $d['perwakilan_asal']);

            $nama_pw_asal = [];
            foreach ($id_pw_asal as $id) {
                $id = trim($id);

                if (isset($mapping_wilayah[$id])) {
                    $nama_pw_asal[] = $mapping_wilayah[$id];
                }
            }

            $id_pw_tujuan = explode(',', $d['perwakilan_tujuan']);

            $nama_pw_tujuan = [];
            foreach ($id_pw_tujuan as $id) {
                $id = trim($id);

                if (isset($mapping_wilayah[$id])) {
                    $nama_pw_tujuan[] = $mapping_wilayah[$id];
                }
            }

            $id_unit_asal = explode(',', $d['unit_asal']);

            $nama_unit_asal = [];
            foreach ($id_unit_asal as $id) {
                $id = trim($id);

                if (isset($mapping_unit[$id])) {
                    $nama_unit_asal[] = $mapping_unit[$id];
                }
            }

            $id_unit_tujuan = explode(',', $d['unit_tujuan']);

            $nama_unit_tujuan = [];
            foreach ($id_unit_tujuan as $id) {
                $id = trim($id);

                if (isset($mapping_unit[$id])) {
                    $nama_unit_tujuan[] = $mapping_unit[$id];
                }
            }

            $d['nama_perwakilan_asal']   = implode(', ', $nama_pw_asal);
            $d['nama_perwakilan_tujuan'] = implode(', ', $nama_pw_tujuan);

            $d['nama_unit_asal']         = implode(', ', $nama_unit_asal);
            $d['nama_unit_tujuan']       = implode(', ', $nama_unit_tujuan);
        }
        // cetak_r($data_list, 1);

        $content['list']            =  $data_list;

        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function filter_data(){

        // cetak_r($_POST, 1);
         $need = [
            'jenis' => 'FILTER',
            'data'  => $_POST,
        ];

        $data_list       = $this->get_list_data($need);

        //   cetak_r($data_list, 1);

        $wilayah         = $this->get_list_wilayah();
        $unit            = $this->get_list_unit();

        $mapping_wilayah = [];
        foreach ($wilayah as $w) {
            $mapping_wilayah[$w['id']] = $w['nama'];
        }

        $mapping_unit = [];
        foreach ($unit as $u) {
            $mapping_unit[$u['id']] = $u['nama'];
        }

        foreach ($data_list as &$d) {
            $id_pw_asal = explode(',', $d['perwakilan_asal']);

            $nama_pw_asal = [];
            foreach ($id_pw_asal as $id) {
                $id = trim($id);

                if (isset($mapping_wilayah[$id])) {
                    $nama_pw_asal[] = $mapping_wilayah[$id];
                }
            }

            $id_pw_tujuan = explode(',', $d['perwakilan_tujuan']);

            $nama_pw_tujuan = [];
            foreach ($id_pw_tujuan as $id) {
                $id = trim($id);

                if (isset($mapping_wilayah[$id])) {
                    $nama_pw_tujuan[] = $mapping_wilayah[$id];
                }
            }

            $id_unit_asal = explode(',', $d['unit_asal']);

            $nama_unit_asal = [];
            foreach ($id_unit_asal as $id) {
                $id = trim($id);

                if (isset($mapping_unit[$id])) {
                    $nama_unit_asal[] = $mapping_unit[$id];
                }
            }

            $id_unit_tujuan = explode(',', $d['unit_tujuan']);

            $nama_unit_tujuan = [];
            foreach ($id_unit_tujuan as $id) {
                $id = trim($id);

                if (isset($mapping_unit[$id])) {
                    $nama_unit_tujuan[] = $mapping_unit[$id];
                }
            }

            $d['nama_perwakilan_asal']   = implode(', ', $nama_pw_asal);
            $d['nama_perwakilan_tujuan'] = implode(', ', $nama_pw_tujuan);

            $d['nama_unit_asal']         = implode(', ', $nama_unit_asal);
            $d['nama_unit_tujuan']       = implode(', ', $nama_unit_tujuan);
        }

        $content['list']  =  $data_list;

        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }


    public function get_list_data($need = null)
    {

        $m_conf = new \Model\Storage\Conf();

        $sql = "SELECT hum.*, kh.tgl_mulai,kh.tgl_selesai,
            usul.nama AS nama_pengusul, kary.nama AS nama_karyawan, 
            jbt_asal.nama AS nama_jabatan_asal, jbt_tujuan.nama AS nama_jabatan_tujuan,
            jp.nama  as nama_jabatan_pengusul
            FROM hris_usulan_mutasi hum
            left JOIN karyawan_history kh 
                ON hum.karyawan = kh.nik 
                AND hum.jabatan_tujuan = kh.jabatan 
                AND hum.tgl_berlaku BETWEEN kh.tgl_mulai AND ISNULL(kh.tgl_selesai, '9999-12-31')
            LEFT JOIN karyawan usul 
                ON hum.pengusul = usul.nik 
                AND usul.status = 1 
            LEFT JOIN karyawan kary 
                ON hum.karyawan = kary.nik 
                AND kary.status = 1 
            LEFT JOIN jabatan jbt_asal 
                ON hum.jabatan_asal = jbt_asal.kode 
            LEFT JOIN jabatan jbt_tujuan 
                ON hum.jabatan_tujuan = jbt_tujuan.kode 
            LEFT JOIN jabatan jp ON usul.jabatan  = jp.kode 
            WHERE hum.status = 3 ";

            if (isset($need['jenis']) && $need['jenis'] == 'FILTER') {

                if (!empty($need['data']['karyawan'])) {
                    $sql .= " AND hum.karyawan = '".$need['data']['karyawan']."' ";
                }

                if (!empty($need['data']['pengusul'])) {
                    $sql .= " AND hum.pengusul = '".$need['data']['pengusul']."' ";
                }

                if (
                    !empty($need['data']['startdate']) && 
                    !empty($need['data']['enddate'])
                ) {
                    $sql .= " AND hum.tanggal 
                                BETWEEN '".$need['data']['startdate']."' 
                                AND '".$need['data']['enddate']."' ";
                }
            }

            if (isset($need['jenis']) && $need['jenis'] == 'DETAIL') {

                if (!empty($need['data'])) {
                    $sql .= " AND  hum.kode = '".$need['data']."' ";
                }

            }
            

            if (isset($need['jenis']) && $need['jenis'] == 'CETAK') {
                if (!empty($need['data'])) {;
                    $sql .= " AND hum.kode IN (" .$need['data']. ") ";
                }

            }

            $sql .= " ORDER BY hum.created_date DESC ";

            // cetak_r($sql, 1);

        $d_conf = $m_conf->hydrateRaw($sql);

        $data = $d_conf->count() > 0 ? $d_conf->toArray() : [];

        $grouped = [];
        foreach ($data as $row) {
            $grouped[$row['karyawan']][] = $row;
        }
        // cetak_r($grouped, 1);
        return $data;
    }

    public function show_detail()
    {
        $need = [
            'jenis' => 'DETAIL',
            'data'  => $_POST['kode'],
        ];
        

        $data_detail    = $this->get_list_data($need);
        $kode           = $_POST['kode'] ?? null;

        $filtered = array_filter($data_detail, function ($row) use ($kode) {
            return $row['kode'] == $kode;
        });
        
        $result = array_values($filtered); 

        $wilayah_pengusul    = $this->get_wilayah_pengusul($result[0]['id_pengusul']);
        $content['wil_pengusul'] = implode(', ', array_column($wilayah_pengusul, 'nama'));
        // cetak_r($content, 1);

        $content['data_detail'] = $result[0];
        $url                    = 'hris/UsulanPromosi';
		$content['akses']       = $akses = hakAkses('/'.$url);
        // $content['wilayah_asal'] = $this->get_unit_wilayah($result[0]['id_karyawan']);

        $data_unit = $this->get_list_unit();
        $data_wilayah = $this->get_list_wilayah();


        $id_unit_tujuan = explode(',', $content['data_detail']['unit_tujuan']);

        $nama_unit_tujuan = array_filter($data_unit, function ($v) use ($id_unit_tujuan) {
            return in_array($v['id'], $id_unit_tujuan);
        });

        $nama_unit_tujuan = array_column($nama_unit_tujuan, 'nama');
        $content['nama_unit_tujuan'] = $nama_unit_tujuan;

        $id_unit_asal = explode(',', $content['data_detail']['unit_asal']);

        $nama_unit_asal = array_filter($data_unit, function ($v) use ($id_unit_asal) {
            return in_array($v['id'], $id_unit_asal);
        });

        $nama_unit_asal = array_column($nama_unit_asal, 'nama');
        $content['nama_unit_asal'] = $nama_unit_asal;

        $id_wilayah_tujuan = explode(',', $content['data_detail']['perwakilan_tujuan']);

        $nama_wilayah_tujuan = array_filter($data_wilayah, function ($v) use ($id_wilayah_tujuan) {
            return in_array($v['id'], $id_wilayah_tujuan);
        });

        $nama_wilayah_tujuan = array_column($nama_wilayah_tujuan, 'nama');
        $content['nama_wilayah_tujuan'] = $nama_wilayah_tujuan;

        $id_wilayah_asal = explode(',', $content['data_detail']['perwakilan_asal']);

        $nama_wilayah_asal = array_filter($data_wilayah, function ($v) use ($id_wilayah_asal) {
            return in_array($v['id'], $id_wilayah_asal);
        });

        $nama_wilayah_asal = array_column($nama_wilayah_asal, 'nama');
        $content['nama_wilayah_asal'] = $nama_wilayah_asal;

        // cetak_r($content , 1);

        
        echo $this->load->view($this->pathView . 'v_detail', $content, TRUE);

    }

    

    public function print_preview() {        
  
        $key = "secretkey";

        if (!isset($_GET['kode']) || empty($_GET['kode'])) {
            show_error('ID tidak ditemukan', 400);
        }

        $kode = $_GET['kode'];
        $kode = str_replace(' ', '+', $kode);

        $decrypted = openssl_decrypt($kode, "AES-128-ECB", $key);
       

        $need = [
            'jenis' => 'DETAIL',
            'data'  => $decrypted,
        ];

        $content['data']     = $this->get_list_data($need)[0];
        // cetak_r($_SESSION, 1);
        $data_unit           = $this->get_list_unit();
        $id_unit             = explode(',', $content['data']['unit_tujuan']);

        $nama_unit = array_filter($data_unit, function($v) use ($id_unit) {
            return in_array($v['id'], $id_unit);
        });
        $nama_unit = array_column($nama_unit, 'nama');
        $content['nama_unit'] = implode(', ', $nama_unit);
        
        $id_wilayah           = explode(',', $content['data']['perwakilan_tujuan']);
        $data_wilayah         =  $this->get_list_wilayah();
        
        $nama_wilayah = array_filter($data_wilayah, function($v) use ($id_wilayah) {
            return in_array($v['id'], $id_wilayah);
        });
        $nama_wilayah = array_column($nama_wilayah, 'nama');
        $content['nama_wilayah'] = implode(', ', $nama_wilayah);
        
        // $content['wilayah_asal'] = $this->get_unit_wilayah($content['data']['id_karyawan']);

        $id_unit_asal = explode(',', $content['data']['unit_asal']);

        $nama_unit_asal = array_filter($data_unit, function ($v) use ($id_unit_asal) {
            return in_array($v['id'], $id_unit_asal);
        });

        $nama_unit_asal = array_column($nama_unit_asal, 'nama');
        $content['nama_unit_asal'] = implode(', ', $nama_unit_asal);

        $id_wilayah_tujuan = explode(',', $content['data']['perwakilan_tujuan']);

        $nama_wilayah_tujuan = array_filter($data_wilayah, function ($v) use ($id_wilayah_tujuan) {
            return in_array($v['id'], $id_wilayah_tujuan);
        });

        $nama_wilayah_tujuan = array_column($nama_wilayah_tujuan, 'nama');
        $content['nama_wilayah_tujuan'] = $nama_wilayah_tujuan;

        $id_wilayah_asal = explode(',', $content['data']['perwakilan_asal']);

        $nama_wilayah_asal = array_filter($data_wilayah, function ($v) use ($id_wilayah_asal) {
            return in_array($v['id'], $id_wilayah_asal);
        });

        $nama_wilayah_asal = array_column($nama_wilayah_asal, 'nama');
        $content['nama_wilayah_asal'] = implode(', ', $nama_wilayah_asal);
        // cetak_r($content,1);



        $res_view_html = $this->load->view($this->pathView.'v_export_pdf', $content, true);

        echo $res_view_html;
    }

    public function cetak_data_pdf()
    {
        $kode       = $this->input->post('kode');
        $startdate  = $this->input->post('tgl_awal');
        $enddate    = $this->input->post('tgl_akhir');

        $periode    = 'Periode : ' . tglIndonesia($startdate, "-", " ") .' s.d. '. tglIndonesia($enddate, "-", " ") ;

        $this->session->set_userdata('kode_pdf', $kode);
        $this->session->set_userdata('periode', $periode);

        echo json_encode([
            'status'    => true,
            'url'       => base_url('hris/ReportHistoryKaryawan/cetak_pdf')
        ]);
        exit;
    }

    public function cetak_pdf()
    {
        if (ob_get_length()) {
            ob_end_clean();
        }

        require_once(APPPATH . 'libraries/tcpdf_new/tcpdf.php');

        $kode = $this->session->userdata('kode_pdf');

        $kode = "'" . implode("','", $kode) . "'";

        $need = [
            'jenis' => 'CETAK',
            'data'  => $kode,
        ];

        $data_list  = $this->get_list_data($need);
        $wilayah    = $this->get_list_wilayah();
        $unit       = $this->get_list_unit();

        $mapping_wilayah = [];
        foreach ($wilayah as $w) {
            $mapping_wilayah[$w['id']]  = $w['nama'];
        }

        $mapping_unit = [];
        foreach ($unit as $u) {
            $mapping_unit[$u['id']]     = $u['nama'];
        }

        foreach ($data_list as &$d) {
            $id_pw_asal = explode(',', $d['perwakilan_asal']);

            $nama_pw_asal = [];
            foreach ($id_pw_asal as $id) {
                $id = trim($id);

                if (isset($mapping_wilayah[$id])) {
                    $nama_pw_asal[] = $mapping_wilayah[$id];
                }
            }

            $id_pw_tujuan = explode(',', $d['perwakilan_tujuan']);

            $nama_pw_tujuan = [];
            foreach ($id_pw_tujuan as $id) {
                $id = trim($id);

                if (isset($mapping_wilayah[$id])) {
                    $nama_pw_tujuan[] = $mapping_wilayah[$id];
                }
            }

            $id_unit_asal = explode(',', $d['unit_asal']);

            $nama_unit_asal = [];
            foreach ($id_unit_asal as $id) {
                $id = trim($id);

                if (isset($mapping_unit[$id])) {
                    $nama_unit_asal[] = $mapping_unit[$id];
                }
            }

            $id_unit_tujuan = explode(',', $d['unit_tujuan']);

            $nama_unit_tujuan = [];
            foreach ($id_unit_tujuan as $id) {
                $id = trim($id);

                if (isset($mapping_unit[$id])) {
                    $nama_unit_tujuan[] = $mapping_unit[$id];
                }
            }

            $d['nama_perwakilan_asal']   = implode(', ', $nama_pw_asal);
            $d['nama_perwakilan_tujuan'] = implode(', ', $nama_pw_tujuan);

            $d['nama_unit_asal']         = implode(', ', $nama_unit_asal);
            $d['nama_unit_tujuan']       = implode(', ', $nama_unit_tujuan);
        }

        $content['data']    = $data_list;
        $content['periode'] = $this->session->userdata('periode');
        //  cetak_r($content, 1);

        $html = $this->load->view($this->pathView . 'v_report_mutasi', $content, true);

        // $pdf = new TCPDF();
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
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


    public function get_unit()
    {
        $m_duser = new \Model\Storage\DetUser_model();
        $d_duser = $m_duser->where('id_user', $this->userid)->first();

        $m_karyawan = new \Model\Storage\Karyawan_model();
        $d_karyawan = $m_karyawan->where('nama', 'like', strtolower(trim($d_duser->nama_detuser)).'%')->orderBy('id', 'desc')->first();

        $data = null;

        // $kode_unit = array();
        // $kode_unit_all = null;
        $data = null;
        if ( $d_karyawan ) {
            $m_ukaryawan = new \Model\Storage\UnitKaryawan_model();
            $d_ukaryawan = $m_ukaryawan->where('id_karyawan', $d_karyawan->id)->get();

            if ( $d_ukaryawan->count() > 0 ) {
                $d_ukaryawan = $d_ukaryawan->toArray();

                foreach ($d_ukaryawan as $k_ukaryawan => $v_ukaryawan) {
                    if ( stristr($v_ukaryawan['unit'], 'all') === false ) {
                        $m_wil = new \Model\Storage\Wilayah_model();
                        $d_wil = $m_wil->where('id', $v_ukaryawan['unit'])->first();

                        $nama = str_replace('Kab ', '', str_replace('Kota ', '', $d_wil->nama));
                        $kode = $d_wil->kode;

                        $key = $kode;

                        $data[$key] = array(
                            'nama' => $nama,
                            'kode' => $kode
                        );
                    } else {
                        $m_wil = new \Model\Storage\Wilayah_model();
                        $d_wil = $m_wil->select('nama', 'kode')->where('jenis', 'UN')->get();

                        if ( $d_wil->count() > 0 ) {
                            $d_wil = $d_wil->toArray();
                            foreach ($d_wil as $k_wil => $v_wil) {
                                $nama = str_replace('Kab ', '', str_replace('Kota ', '', $v_wil['nama']));
                                $kode = $v_wil['kode'];

                                $key = $kode;
                                $data[$key] = array(
                                    'nama' => $nama,
                                    'kode' => $kode
                                );
                            }
                        }
                    }
                }
            } else {
                $m_wil = new \Model\Storage\Wilayah_model();
                $d_wil = $m_wil->select('nama', 'kode')->where('jenis', 'UN')->get();

                if ( $d_wil->count() > 0 ) {
                    $d_wil = $d_wil->toArray();
                    foreach ($d_wil as $k_wil => $v_wil) {
                        $nama = str_replace('Kab ', '', str_replace('Kota ', '', $v_wil['nama']));
                        $kode = $v_wil['kode'];

                        $key = $kode;
                        $data[$key] = array(
                            'nama' => $nama,
                            'kode' => $kode
                        );
                    }
                }
            }
        } else {
            $m_wil = new \Model\Storage\Wilayah_model();
            $d_wil = $m_wil->select('nama', 'kode')->where('jenis', 'UN')->get();

            if ( $d_wil->count() > 0 ) {
                $d_wil = $d_wil->toArray();
                foreach ($d_wil as $k_wil => $v_wil) {
                    $nama = str_replace('Kab ', '', str_replace('Kota ', '', $v_wil['nama']));
                    $kode = $v_wil['kode'];

                    $key = $kode;
                    $data[$key] = array(
                        'nama' => $nama,
                        'kode' => $kode
                    );
                }
            }
        }

        if ( !empty($data) ) {
            ksort($data);
        }

        return $data;
    }
   


}
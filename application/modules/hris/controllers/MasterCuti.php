<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MasterCuti extends Public_Controller {

    private $pathView = 'hris/master_cuti/';
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
                "assets/hris/master_cuti/js/master_cuti.js",
                "assets/xlsx/js/xlsx.full.min.js",
                "assets/html2pdf/html2canvas.min.js",
                "assets/html2pdf/jspdf.umd.min.js",
                
                
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/master_cuti/css/master_cuti.css",
            ));
            $m_conf                     = new \Model\Storage\Conf();

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Master Cuti';
            $content['jabatan']         = $m_conf->hydrateRaw("select * from jabatan")->toArray();
            $content['data_cuti']       = $this->get_data_cuti();

            $m_karyawan 			    = new \Model\Storage\Karyawan_model();
            $d_karyawan 			    = $m_karyawan->select(
                                            'karyawan.*',
                                            'jabatan.nama as nama_jabatan'
                                        )->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
                                        ->where('karyawan.status', 1)
                                        ->orderBy('karyawan.level', 'asc')
                                        ->get();

            $data_karyawan  		    = $d_karyawan->toArray();
            $content['karyawan']	    = $data_karyawan;

            // cetak_r($content['akses'], 1);
            $data['title_menu']         = 'HRIS - Master Cuti';

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

    public function SelectKaryawan()
    {
        $params = $_POST;

        $data['karyawan'] = $this->get_data_karyawan($params);

        // cetak_r($data, 1);
        echo $this->load->view($this->pathView.'v_list_karyawan', $data, true);
    }

    public function get_data_karyawan($params)
    {
        $m_conf     = new \Model\Storage\Conf();

        $sql = "SELECT 
                k.nik,
                k.nama
            FROM karyawan k
            WHERE k.status = 1
            AND NOT EXISTS (
                SELECT 1
                FROM hris_master_cuti mc
                WHERE mc.nik = k.nik
                AND mc.tahun = '". $params['tahun'] ."'
            ) ";

        return $m_conf->hydrateRaw($sql)->toArray();
    }

    public function get_data_cuti($need = null)
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = "SELECT 
                    mc.id,
                    mc.nik,
                    k.nama,
                    mc.tahun,
                    mc.hak_cuti,
                    mc.sisa_cuti,
                    mc.cuti_terpakai
                FROM hris_master_cuti mc
                INNER JOIN karyawan k
                    ON mc.nik = k.nik and k.status = 1 ";


        $jenis     = $need['jenis'] ?? null;
        $dataNeed  = $need['data'] ?? null;

        $where = [];

        if ($jenis == 'FILTER' && is_array($dataNeed)) {
            $tahun  = $dataNeed['tahun'] ?? null;
            $nik    = $dataNeed['karyawan'] ?? null;

            if ($tahun) {
                $where[] = "mc.tahun = '".addslashes($tahun)."'";
            }

            if ($nik) {
                $where[] = "mc.nik = '".addslashes($nik)."'";
            }
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY mc.tahun DESC, k.nama ASC";

        // cetak_r($sql, 1);

        $data = $m_conf->hydrateRaw($sql)->toArray();

        $result = [];

        foreach ($data as $row) {
            $result[$row['tahun']][] = $row;
        }

        return $result;
    }

    public function filter_list()
    {
        $need = [
            'jenis' => 'FILTER',
            'data'  => $_POST,
        ];

        $data['data_cuti'] = $this->get_data_cuti($need);

        // cetak_r($data, 1);


        echo $this->load->view($this->pathView.'v_filter_data', $data, true);
    }


    public function generate_cuti()
    {
        $params = $_POST;

        try {

            $generate  = $params['generate'];
            $tahun     = $params['tahun'];
            $hak_cuti  = $params['hak_cuti'];
            $karyawan  = $params['karyawan'] ?? [];

            $m_cuti    = new \Model\Storage\HrisMasterCuti_model();

            if ($generate == "SELECT") {

                if (empty($karyawan)) {
                    throw new \Exception("Karyawan belum dipilih");
                }

                foreach ($karyawan as $nik) {

                    // check data existing
                    $exist = $m_cuti
                        ->where('nik', $nik)
                        ->where('tahun', $tahun)
                        ->first();

                    if ($exist) {
                        continue; // skip jika sudah ada
                    }

                    $data = [
                        'nik'        => $nik,
                        'tahun'      => $tahun,
                        'hak_cuti'   => $hak_cuti,
                        'sisa_cuti'  => $hak_cuti,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $_SESSION['detail_user']['nama_detuser']
                    ];

                    $m_cuti->insert($data);
                }


            } elseif ($generate == "ALL") {

                $m_karyawan = new \Model\Storage\Karyawan_model();

                $karyawan = $m_karyawan
                    ->where('status', 1)
                    ->get()
                    ->toArray();


                foreach ($karyawan as $k) {

                    // check data existing
                    $exist = $m_cuti
                        ->where('nik', $k['nik'])
                        ->where('tahun', $tahun)
                        ->first();

                    if ($exist) {
                        continue; // skip jika sudah ada
                    }

                    $data = [
                        'nik'        => $k['nik'],
                        'tahun'      => $tahun,
                        'hak_cuti'   => $hak_cuti,
                        'sisa_cuti'  => $hak_cuti,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $_SESSION['detail_user']['nama_detuser']
                    ];

                    $m_cuti->insert($data);
                }
            }


            echo json_encode([
                'status'  => true,
                'message' => 'Generate berhasil'
            ]);

        } catch (\Exception $e) {

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete_cuti()
    {
        $params = $_POST;

        try {

            $m_cuti = new \Model\Storage\HrisMasterCuti_model();

            $data = $m_cuti
                ->where('id', $params['id'])
                ->first();


            if(!$data){
                throw new \Exception("Data cuti tidak ditemukan");
            }


            $data->delete();


            echo json_encode([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ]);


        } catch(\Exception $e){

            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }

    public function update_cuti()
    {
        $params = $_POST;

        try {

            $m_cuti = new \Model\Storage\HrisMasterCuti_model();

            $data = $m_cuti
                ->where('id', $params['id'])
                ->first();

            if (!$data) {
                throw new \Exception("Data cuti tidak ditemukan");
            }


            $hak_cuti = $params['hak_cuti'];
            $cuti_terpakai = $params['cuti_terpakai'];

            $data->hak_cuti      = $hak_cuti;
            $data->cuti_terpakai = $cuti_terpakai;
            $data->sisa_cuti     = $hak_cuti - $cuti_terpakai;

            $data->save();


            echo json_encode([
                'status' => true,
                'message' => 'Data berhasil diupdate'
            ]);


        } catch (\Exception $e) {

            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }
   

}
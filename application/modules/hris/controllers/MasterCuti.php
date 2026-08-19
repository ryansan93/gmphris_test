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
                "assets/toastr/js/toastr.js",
                "assets/toastr/js/toastr.min.js",
                "assets/xlsx/js/xlsx.full.min.js",
                "assets/html2pdf/html2canvas.min.js",
                "assets/html2pdf/jspdf.umd.min.js", 
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/toastr/css/toastr.css",
                "assets/toastr/css/toastr.min.css",
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

            // cetak_r($content['karyawan'], 1);
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
        // cetak_r($params, 1);

        $data['karyawan'] = $this->get_data_karyawan($params);

        echo $this->load->view($this->pathView.'v_list_karyawan', $data, true);
    }

    public function get_data_karyawan($params)
    {
        $m_conf     = new \Model\Storage\Conf();

        $sql = "SELECT 
                    k.nik,
                    k.nama,
                    j.nama as nama_jabatan,
                    u.nama_unit,
                    u.nama_wilayah
                FROM karyawan k
                INNER JOIN jabatan j on k.jabatan = j.kode
                OUTER APPLY
                (
                    SELECT
                        nama_unit = STUFF
                        (
                            (
                                SELECT ', ' +
                                    CASE 
                                        WHEN x.is_all = 1 THEN 'All'
                                        ELSE MAX(x.nama)
                                    END
                                FROM
                                (
                                    SELECT 
                                        uk2.unit,
                                        CASE 
                                            WHEN uk2.unit = 'all' THEN 1 
                                            ELSE 0 
                                        END AS is_all,
                                        w.kode,
                                        w.nama
                                    FROM unit_karyawan uk2
                                    LEFT JOIN wilayah w
                                        ON TRY_CAST(uk2.unit AS INT) = w.id
                                    WHERE uk2.id_karyawan = k.id
                                ) x
                                GROUP BY 
                                    x.is_all,
                                    x.kode
                                FOR XML PATH('')
                            ),
                            1,2,''
                        ),
                        nama_wilayah = STUFF
                        (
                            (
                                SELECT DISTINCT ', ' + 
                                    CASE 
                                        WHEN uk2.unit = 'all' THEN 'All'
                                        ELSE w_induk.nama 
                                    END
                                FROM unit_karyawan uk2
                                LEFT JOIN wilayah w
                                    ON TRY_CAST(uk2.unit AS INT) = w.id
                                LEFT JOIN wilayah w_induk 
                                    ON w.induk = w_induk.id 
                                WHERE uk2.id_karyawan = k.id
                                FOR XML PATH('')
                            ),
                            1,2,''
                        )
                ) u 
                WHERE k.status = 1
                AND NOT EXISTS (
                    SELECT 1
                    FROM hris_master_cuti mc
                    WHERE mc.nik = k.nik
                    AND mc.tahun = '". $params['tahun'] ."'
                ) ";

                // cetak_r($params, 1);

                if (isset($params['unit']) && $params['unit'] != null) {
                    $unit = "'" . implode("','", $params['unit']) . "'";
                    $sql .= " AND u.nama_unit IN ($unit)";
                }

                if (isset($params['perwakilan']) && $params['perwakilan'] != null) {
                    $perwakilan = "'" . implode("','", $params['perwakilan']) . "'";
                    $sql .= " AND u.nama_wilayah IN ($perwakilan)";
                }
                // cetak_r($sql, 1);

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
            $generate  = $params['generate'] ?? '';
            $tahun     = $params['tahun'] ?? date('Y');
            $hak_cuti_default = $params['hak_cuti'] ?? 12;
            $karyawan  = $params['karyawan'] ?? [];

            $m_cuti = new \Model\Storage\HrisMasterCuti_model();
            $created_by = $_SESSION['detail_user']['nama_detuser'] ?? 'SYSTEM';

            if ($generate === "SELECT") {
                if (empty($karyawan)) {
                    throw new \Exception("Karyawan belum dipilih");
                }

                foreach ($karyawan as $k) {
                    
                    $nik = $k['nik'];
                    $hak_cuti_item = $k['hak_cuti'] ?? $hak_cuti_default;

                    $exist = $m_cuti
                        ->where('nik', $nik)
                        ->where('tahun', $tahun)
                        ->first();

                    if ($exist) {
                        continue;
                    }

                    $data = [
                        'nik'        => $nik,
                        'tahun'      => $tahun,
                        'hak_cuti'   => $hak_cuti_item,
                        'sisa_cuti'  => $hak_cuti_item,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $created_by
                    ];

                    $m_cuti->insert($data);
                }

            } elseif ($generate === "ALL") {
                $m_karyawan = new \Model\Storage\Karyawan_model();

                $all_karyawan = $m_karyawan
                    ->where('status', 1)
                    ->get()
                    ->toArray();

                foreach ($all_karyawan as $k) {
                    
                    $exist = $m_cuti
                        ->where('nik', $k['nik'])
                        ->where('tahun', $tahun)
                        ->first();

                    if ($exist) {
                        continue; // Skip jika sudah ada
                    }

                    $data = [
                        'nik'        => $k['nik'],
                        'tahun'      => $tahun,
                        'hak_cuti'   => $hak_cuti_default,
                        'sisa_cuti'  => $hak_cuti_default,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $created_by
                    ];

                    $m_cuti->insert($data);
                }
            } else {
                throw new \Exception("Metode generate tidak dikenali");
            }

            echo json_encode([
                'status'  => true,
                'message' => 'Generate data cuti berhasil'
            ]);

        } catch (\Exception $e) {
            // Pastikan header JSON sudah benar sebelum output error
            http_response_code(400); 
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


    public function getDataUnitPerwakilan()
    {
        $m_conf = new \Model\Storage\Conf();

        $perwakilan = $m_conf->hydrateRaw(" SELECT * FROM wilayah WHERE jenis = 'PW' ")->toArray();
        $unit       = $m_conf->hydrateRaw("
                            SELECT *
                            FROM (
                                SELECT *,
                                    ROW_NUMBER() OVER (
                                        PARTITION BY kode
                                        ORDER BY id desc
                                    ) AS rn
                                FROM wilayah
                                WHERE jenis = 'UN'
                            ) x
                            WHERE rn = 1
                        ")->toArray();

        $data = [
            'perwakilan' => $perwakilan,
            'unit'       => $unit
        ];

        // cetak_r($data, 1);

        echo json_encode($data);
    }
   

}
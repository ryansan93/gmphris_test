<?php defined('BASEPATH') OR exit('No direct script access allowed');


class UsulanPromosi extends Public_Controller {

    private $pathView = 'hris/usulan_promosi/';
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
            $content['unit']            = $this->get_list_unit();
            $content['wilayah']         = $this->get_list_wilayah();
            // cetak_r($newId, 1);

            $content['jabatan']         =  $m_conf->hydrateRaw("select * from jabatan")->toArray();

            // Load Indexx
            $data['title_menu']     = 'HRIS - Usulan Promosi';

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

        $data_list  = $this->get_list_data();
        $unit       = $this->get_list_unit();
        $wilayah    = $this->get_list_wilayah();

        $unitMap    = array_column($unit, 'nama', 'id');
        $wilayahMap = array_column($wilayah, 'nama', 'id');

        foreach ($data_list as &$row) {

            $row['nama_perwakilan_tujuan']  = $wilayahMap[$row['perwakilan_tujuan']] ?? null;  
            $row['nama_perwakilan_asal']    = $wilayahMap[$row['perwakilan_asal']] ?? null;    
            $unitIdTujuan                   = explode(',', $row['unit_tujuan']);
            $unitIdAsal                     = explode(',', $row['unit_asal']);

            $namaUnitTujuan = [];
            foreach ($unitIdTujuan as $id) {
                $id = trim($id); 
                if (isset($unitMap[$id])) {
                    $namaUnitTujuan[] = $unitMap[$id];
                }
            }

            $namaUnitAsal = [];
            foreach ($unitIdAsal as $id) {
                $id = trim($id); 
                if (isset($unitMap[$id])) {
                    $namaUnitAsal[] = $unitMap[$id];
                }
            }

            $row['nama_unit_tujuan'] = implode(', ', $namaUnitTujuan);
            $row['nama_unit_asal'] = implode(', ', $namaUnitAsal);
        }

        // unset($row);
        // cetak_r($data_list, 1);

        $content['list']            =  $data_list;
        // $content['wilayah_asal']    = $this->get_unit_wilayah($data_list[0]['id_karyawan']);

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
                    karyawan.id as id_karyawan,
                    pengusul.id as id_pengusul,
                    karyawan.nama as nama_karyawan, 
                    pengusul.nama as nama_pengusul, 
                    jp.nama  as nama_jabatan_pengusul,
                    asal.nama as nama_jabatan_asal, 
                    tujuan.nama as nama_jabatan_tujuan, 
                    hum.*  
                FROM hris_usulan_mutasi hum
                INNER JOIN karyawan ON hum.karyawan = karyawan.nik AND karyawan.status = 1
                INNER JOIN karyawan pengusul ON hum.pengusul = pengusul.nik AND pengusul.status = 1
                INNER JOIN jabatan asal ON hum.jabatan_asal = asal.kode 
                INNER JOIN jabatan tujuan ON hum.jabatan_tujuan = tujuan.kode
                INNER JOIN jabatan jp ON pengusul.jabatan  = jp.kode ";

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
        

        $data_detail                    = $this->get_list_data($need);
        $kode = $_POST['kode'] ?? null;

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

    public function get_wilayah_pengusul($id_pengusul)
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = " SELECT w.nama, w.kode 
            FROM unit_karyawan uk
            INNER JOIN wilayah w ON w.id = uk.unit
            WHERE uk.id_karyawan = $id_pengusul ";

        $db = $m_conf->hydrateRaw($sql)->toArray();

        // cetak_r($db);

        return $db;

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
        $content['unit']            = $this->get_list_unit();
        $content['wilayah']         = $this->get_list_wilayah();

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

            // $m_db->perwakilan_tujuan    = implode(',', $_POST['perwakilan_tujuan']);
            // $m_db->unit_tujuan          = implode(',', $_POST['unit_tujuan']);

            $m_db->where('kode', $kode_usulan)->update([
                'tanggal'           => $params['tgl_usulan'],
                'pengusul'          => $params['pengusul'],
                'karyawan'          => $params['karyawan'],
                'jabatan_asal'      => $params['jabatan_asal'],
                'jabatan_tujuan'    => $params['jabatan_tujuan'],
                'alasan'            => $params['alasan'],
                'perwakilan_tujuan' => implode(',', $params['perwakilan_tujuan']),
                'unit_tujuan'       => implode(',', $params['unit_tujuan']),
                'perwakilan_asal'   => implode(',', $params['perwakilan_asal']),
                'unit_asal'         => implode(',', $params['unit_asal']),
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

            // cetak_r($params, 1);

            if (!$d_db) {
                throw new \Exception("Data form tidak ditemukan.");
            }

            $keterangan = null;

            if (in_array($params['keputusan'] ?? null, [4, 5])) {
                $keterangan = $params['keterangan'] ?? null;
            }

            $data_update = [
                'status'        => $params['keputusan'],
                'alasan_reject' => $keterangan,
                'tgl_berlaku'   => !empty($params['tgl_berlaku']) ? $params['tgl_berlaku'] : null,
            ];

            if ($params['keputusan'] == 2) {
                $data_update['tgl_ack']     = date("Y-m-d H:i:s");
            }
            
            if ($params['keputusan'] == 3) {
                $data_update['tgl_approve'] = date("Y-m-d H:i:s");
            }
            $m_db->where('kode', $kode_usulan)->update($data_update);
            
            if ( $params['keputusan'] == 3 ){
                $tgl_berlaku    = $params['tgl_berlaku'] ?? null;

                if (!$tgl_berlaku) {
                    throw new \Exception("Tanggal berlaku wajib diisi.");
                }

                // DUPLICATED KARYAWAN
                    // $m_karyawan = new \Model\Storage\Karyawan_model();
                    // $m_karyawan->where('nik', $data_mutasi['karyawan'])->where('status', 1)->update([
                    //     'jabatan' => $data_mutasi['jabatan_tujuan'],
                    //     // 'tgl_berlaku' => $tgl_berlaku,
                    // ]);

                    $m_karyawan = new \Model\Storage\Karyawan_model();

                    $d_karyawan = $m_karyawan
                        ->where('nik', $data_mutasi['karyawan'])
                        ->where('status', 1)
                        ->first();

                    $id_karyawan = '';

                    if ($d_karyawan) {

                        // update data lama jadi non aktif
                        $d_karyawan->status = 0;
                        $d_karyawan->save();

                        // duplicate data lama
                        $newData = $d_karyawan->toArray();

                        unset(
                            $newData['id'],
                            $newData['created_at'],
                            $newData['updated_at']
                        );

                        $m_karyawan_new = new \Model\Storage\Karyawan_model();

                        foreach ($newData as $key => $val) {
                            $m_karyawan_new->$key = $val;
                        }

                        // generate id baru
                        $newId = $m_karyawan->getNextIdentity();

                        // data baru
                        $m_karyawan_new->id = $newId;
                        $m_karyawan_new->status = 1;
                        $m_karyawan_new->jabatan = $data_mutasi['jabatan_tujuan'];
                        $m_karyawan_new->tgl_berlaku = $params['tgl_berlaku'] ?? null;

                        // insert baru
                        $m_karyawan_new->save();

                        // id data baru
                        $id_karyawan = $newId;
                    }
                // END UPDATE KARYAWAN

                // UPDATE KARYAWAN HISTORY
                    $m_kh = new \Model\Storage\KaryawanHistory_model();   
                    $last_history = $m_kh->where('nik', $data_mutasi['karyawan'])->orderBy('id', 'desc')->first();
                    $tgl_selesai_lama = date( 'Y-m-d', strtotime($tgl_berlaku . ' -1 day'));

                    if ($last_history) {
                        $last_data = $last_history->toArray();
                        $m_kh->where('id', $last_data['id'])->update([
                            'tgl_selesai' => $tgl_selesai_lama,
                        ]);
                    }
                // END UPDATE KARYAWAN HISTORY

                // INSERT KARYAWAN HISTORY
                    $m_karyawan_history                 = new \Model\Storage\KaryawanHistory_model();
                    $m_karyawan_history->nik            = $data_mutasi['karyawan'];
                    $m_karyawan_history->jabatan        = $data_mutasi['jabatan_tujuan'];
                    $m_karyawan_history->tgl_mulai      = $tgl_berlaku;
                    $m_karyawan_history->tgl_selesai    = null;
                    $m_karyawan_history->save();
                    $id_history                         = $m_karyawan_history->id;
                // END INSERT KARYAWAN HISTORY


                // INSERT KARYAWAN HISTORY UNIT
                    // $m_khu = new \Model\Storage\KaryawanHistoryUnit_model();
                    $unit_tujuan = explode(',', $data_mutasi['unit_tujuan']);
                    if (!empty($unit_tujuan)) {
                        foreach ($unit_tujuan as $kode_unit) {
                            $m_insert = new \Model\Storage\KaryawanHistoryUnit_model();
                            $m_insert->id         = $id_history;
                            $m_insert->kode_unit  = $kode_unit;
                            $m_insert->save();
                        }

                    }
                // END INSERT KARYAWAN HISTORY UNIT

                // INSERT KARYAWAN HISTORY WILAYAH
                    $wilayah_tujuan = explode(',', $data_mutasi['perwakilan_tujuan']);
                    if (!empty($wilayah_tujuan)) {
                        foreach ($wilayah_tujuan as $kode_wilayah) {
                            $m_insert = new \Model\Storage\KaryawanHistoryWilayah_model();
                            $m_insert->id             = $id_history;
                            $m_insert->kode_wilayah   = $kode_wilayah;
                            // cetak_r($m_insert, 1);
                            $m_insert->save();
                        }
                    }
                // END INSERT KARYAWAN HISTORY WILAYAH


                // INSERT UNIT KARYAWAN
                
                foreach($unit_tujuan as $ut){
                        $m_unit_karyawan        = new \Model\Storage\UnitKaryawan_model();
                        $id_unit_karyawan       = $m_unit_karyawan->getNextIdentity();

                        $m_unit_karyawan->id           = $id_unit_karyawan;
                        $m_unit_karyawan->id_karyawan  = $id_karyawan;
                        $m_unit_karyawan->unit         = $ut;
                        // cetak_r($m_unit_karyawan);
                        $m_unit_karyawan->save();

                    }
                //  END INSERT UNIT KARYAWAN


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

        $content['data']     = $this->get_list_data($need)[0];
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
        
        $content['wilayah_asal'] = $this->get_unit_wilayah($content['data']['id_karyawan']);
        // cetak_r($content['data'],1);



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
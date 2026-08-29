<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class KpiKaryawan extends Public_Controller
{
	private $url;
	private $pathView = 'hris/kpi_karyawan/';

	function __construct()
	{
		parent::__construct();
		$this->url = $this->current_base_uri;
		$this->load->library('telegram_lib');
	}

	public function index()
	{
		$akses = hakAkses($this->url);
		if ( $akses['a_view'] == 1 ) {

			$this->add_external_js(array(
				'assets/select2/js/select2.min.js',
				'assets/toastr/js/toastr.js',
                'assets/toastr/js/toastr.min.js',
				'assets/hris/kpi_karyawan/js/kpi_karyawan.js',
				'assets/xlsx/js/xlsx.full.min.js'
			));

			$this->add_external_css(array(
				'assets/select2/css/select2.min.css',
				"assets/toastr/css/toastr.css",
                "assets/toastr/css/toastr.min.css",
				'assets/hris/kpi_karyawan/css/kpi_karyawan.css'
			));
			
			$data 				= $this->includes;
			$content['charts']	= $this->getDataCharts();

			$hakAksesChild = [];

			foreach ($_SESSION['detail_user']['data_group']['detail_group'] as $item) {
				$path = $item['detail_fitur']['path_detfitur'] ?? '';
				if (strpos($path, 'hris/KpiKaryawan') === 0) {
					$hakAksesChild[$path] = [
						'a_view'    => $item['a_view'],
						'a_submit'  => $item['a_submit'],
						'a_edit'    => $item['a_edit'],
						'a_delete'  => $item['a_delete'],
						'a_ack'     => $item['a_ack'],
						'a_approve' => $item['a_approve'],
					];
				}
			}

			$content['jabatan'] 	= $this->getJabatanByNikAtasan() ?? $this->loadDataPenilaianKpi();
			// cetak_r($content['jabatan']	);
			$content['akses'] 		= $akses;
			$content['nik_login']   = $this->cek_nik();
			$content['akses_child'] = $hakAksesChild;
			$data['title_menu'] 	= 'KPI Karyawan';
			$data['view'] 			= $this->load->view('hris/kpi_karyawan/v_index', $content, true);

			$this->load->view($this->template, $data);
		} else {
			showErrorAkses();
		}
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

		// cetak_r($sql, 1)
// 
		$d_conf = $m_conf->hydrateRaw($sql);

		if ($d_conf->count() == 0) {
			return null;
		}

		$data = $d_conf->toArray();

		if ($_SESSION['detail_user']['data_group']['nama_group'] == 'HRD') {
			return null;
		}

		return $data[0]['nik'];
	}

	public function getJabatanByNikAtasan()
	{
		$m_conf = new \Model\Storage\Conf();

		$nik = $this->cek_nik();

		$sql = "
			SELECT DISTINCT
				j.kode AS jabatan_id,
				j.nama AS nama_jabatan
			FROM karyawan k
			INNER JOIN jabatan j
				ON k.jabatan = j.kode
			WHERE k.status = 1
		";

		if ($nik != null) {
			$sql .= " AND k.atasan_nik = '".$nik."'";
		}

		$sql .= " ORDER BY j.nama ASC";

		$d_conf = $m_conf->hydrateRaw($sql);

		if ($d_conf->count() > 0) {
			return $d_conf->toArray();
		}

		return [];
	}

	public function getDataCharts()
	{
		$m_conf = new \Model\Storage\Conf();

		$nik = $this->cek_nik();

		$sql = "
			SELECT
				hkp.nik,
				k.nama AS nama_karyawan,
				hkp.total_nilai,
				FORMAT(hkp.tanggal_mulai, 'MMMM yyyy', 'id-ID') AS periode_kpi
			FROM hris_kpi_penilaian hkp
			INNER JOIN karyawan k
				ON hkp.nik = k.nik
				AND k.status = 1
		";

		$where = [];
		$where[] = "hkp.status = 'APPROVED'";

		if ($nik != null) {
			$where[] = "k.atasan_nik = '".$nik."'";
		}

		$sql .= " WHERE ".implode(" AND ", $where);
		$sql .= " ORDER BY hkp.tanggal_mulai ASC";

		$d_conf = $m_conf->hydrateRaw($sql);

		$data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

		$data_charts = [];

		if(!empty($data)){

			foreach ($data as $d) {
				$key = $d['nik'] . ' - ' . ucwords(strtolower($d['nama_karyawan']));
	
				$data_charts[$key]['label'][] = $d['periode_kpi'];
				$data_charts[$key]['nilai'][] = $d['total_nilai'];
			}
	
			foreach ($data_charts as $key => $val) {
				$data_charts[$key] = [
					'label' =>  implode(',', array_map(function($v) {
									return "'" . $v . "'";
								}, $val['label'])),
					'nilai' =>  implode(',', $val['nilai']),
				];
			}
		}
				
		// cetak_r($data_charts, 1);

		return $data_charts;
	}

	public function penilaianKpi()
	{
		$this->add_external_js(array(
			"assets/select2/js/select2.min.js",
			"assets/toastr/js/toastr.js",
			"assets/toastr/js/toastr.min.js",
			"assets/hris/kpi_karyawan/js/kpi_karyawan.js",
			"assets/xlsx/js/xlsx.full.min.js"
		));
		$this->add_external_css(array(
			'assets/select2/css/select2.min.css',
			"assets/toastr/css/toastr.css",
			"assets/toastr/css/toastr.min.css",
			'assets/hris/kpi_karyawan/css/kpi_karyawan.css'
		));

		$m_karyawan 			= new \Model\Storage\Karyawan_model();
		$d_karyawan 			= $m_karyawan->select(
									'karyawan.*',
									'jabatan.nama as nama_jabatan'
								)->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
								->where('karyawan.status', 1)
								->orderBy('karyawan.level', 'asc')
								->get();

		$data_karyawan  		= $d_karyawan->toArray();

		$data 					= $this->includes;
		// $content['akses'] 		= $akses;
		$content['nik_login']   = $this->cek_nik();
		$content['karyawan']	= $data_karyawan;

		// cetak_r($content, 1);

		$data['title_menu'] = 'KPI Karyawan -  Penilaian';
		$data['view'] 		= $this->load->view('hris/kpi_karyawan/v_penilaian_kpi', $content, true);

		$this->load->view($this->template, $data);
	}

	public function loadPenilaianKpi()
	{
		
		$data['penilaian'] 	= $this->loadDataPenilaianKpi();
		// cetak_r($data, 1);

		$html = $this->load->view('hris/kpi_karyawan/v_riwayat_penilaian', $data, true);
		echo $html;
	}

	public function loadDataPenilaianKpi($data = null)
	{
		$m_conf = new \Model\Storage\Conf();

		$nik = $this->cek_nik();

		$sql = "
			SELECT 
				hkp.id,
				hkp.nik,
				k.nama,
				hkp.status,
				j.nama AS nama_jabatan,
				hkp.tanggal_mulai AS periode,
				hkp.total_nilai,
				hkp.penilai,
				hkp.jabatan AS jabatan_id
			FROM hris_kpi_penilaian hkp
			INNER JOIN karyawan k 
				ON hkp.nik = k.nik 
				AND k.status = 1
			INNER JOIN jabatan j 
				ON hkp.jabatan = j.kode
		";

		$where = [];

		// Filter berdasarkan atasan login
		if ($nik != null && isset($nik)) {
			$where[] = "k.atasan_nik = '" . $nik ."'";
		}

		// Filter detail penilaian
		if ($data != null && isset($data['id_penilaian'])) {
			$where[] = "hkp.id = " . $data['id_penilaian'];
		}

		if (count($where) > 0) {
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		$sql .= " ORDER BY hkp.id DESC";

		// cetak_r($sql, 1);

		$d_conf = $m_conf->hydrateRaw($sql);

		if ($d_conf->count() > 0) {
			return $d_conf->toArray();
		}

		return null;
	}


	public function loadDataBobot()
	{
		$m_conf = new \Model\Storage\Conf();

		$params 		= $_POST;
		$nik_atasan 	= $this->cek_nik();

		$check_atasan   = $m_conf->hydrateRaw("select * from karyawan where nik = '".$params['nik']."' and status = 1 ")->toArray()[0];
		// cetak_r($check_atasan, 1);
		$data['bulan'] 	= $params['bulan'];

		if (empty($nik_atasan) || $check_atasan['atasan_nik'] == $nik_atasan) {
			$data['bobot'] 	= $this->getDataBobot($params);
			$data['error'] = '';
		} else {
			$data['bobot']  = [];
			$data['error'] = 'Anda tidak berhak menilai karyawan ini.';
		}
		// cetak_r($data, 1);

		$html = $this->load->view('hris/kpi_karyawan/v_list_bobot', $data, true);
		echo $html;
	}

	public function getDataBobot($params)
	{
		$m_conf 	= new \Model\Storage\Conf();

		$jabatan_id = trim($params['jabatan']);
		$periode 	= trim($params['bulan']);

		// $sql = " SELECT hkmd.* FROM hris_kpi_master_header hkmh
		// inner join hris_kpi_master_detail hkmd on hkmh.id = hkmd.id_header  and hkmh.periode = '".$periode."'
		// WHERE hkmh.jabatan_id = '". $jabatan_id ."' 
		// AND hkmh.status = 'ACTIVE'
		// ORDER BY hkmh.id ASC ";

		$sql = " SELECT hkmd.*
				FROM hris_kpi_master_header hkmh
				INNER JOIN hris_kpi_master_detail hkmd
					ON hkmh.id = hkmd.id_header
				WHERE hkmh.jabatan_id =  '". $jabatan_id ."' 
					AND hkmh.status = 'ACTIVE'
					AND hkmh.periode = (
						SELECT TOP 1 periode
						FROM hris_kpi_master_header
						WHERE jabatan_id =  '". $jabatan_id ."' 
							AND status = 'ACTIVE'
							AND periode <= '".$periode."'
						ORDER BY periode DESC
					)
				ORDER BY hkmh.id ASC ";

		// cetak_r($sql, 1);

		$d_conf 	= $m_conf->hydrateRaw($sql);
		$data       = null;

        if ( $d_conf->count() > 0 ) {
            $data 	= $d_conf->toArray();
        }

		return $data;

	}

	public function configDataPenilaian()
	{
		$m_conf = new \Model\Storage\Conf();
		$params = $_POST;


		$sql = " SELECT nik
			FROM hris_kpi_penilaian
			WHERE tanggal_mulai = '".$params['startdate']."'
			AND tanggal_selesai = '".$params['enddate']."'
			and status != 'REJECTED'
		";

		// cetak_r($sql, 1);

		$d_conf = $m_conf->hydrateRaw($sql);

		$data_outstanding = [];

		if ($d_conf->count() > 0) {
			$data_outstanding = $d_conf->toArray();
		}

		$m_karyawan = new \Model\Storage\Karyawan_model();
		$d_karyawan = $m_karyawan->select(
							'karyawan.*',
							'jabatan.nama as nama_jabatan'
						)
						->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
						->where('karyawan.status', 1)
						->where('karyawan.atasan_nik', $params['penilai'])
						->orderBy('karyawan.level', 'asc')
						->get();

		$karyawan = $d_karyawan->toArray();

		$nik_terpakai = array_column($data_outstanding, 'nik');

		$result = [];

		foreach ($karyawan as $k) {
			if (!in_array($k['nik'], $nik_terpakai)) {
				$result[] = $k;
			}
		}

		$html = '
			<label>Nama Karyawan</label>
			<select class="select2 karyawan" id="karyawan" onchange="kpi.loadDataBobot(this, event)">
			<option disabled selected>Pilih Karyawan</option>
		';

		foreach ($result as $k) {
			$html .= '
				<option
					nama_jabatan="'.$k['nama_jabatan'].'"
					jabatan="'.$k['jabatan'].'"
					value="'.$k['nik'].'">
					'.ucwords(strtolower($k['nama'])).'
				</option>
			';
		}

		$html .= '
			</select>
		';

		echo $html;
	}

	public function get_unit_wilayah($nik)
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = "SELECT 
				k.nik,
				STUFF((
					SELECT DISTINCT ', ' + uk.unit
					FROM unit_karyawan uk
					WHERE uk.id_karyawan = k.id
					FOR XML PATH(''), TYPE
				).value('.', 'NVARCHAR(MAX)'), 1, 2, '') AS daftar_unit,
				STUFF((
					SELECT DISTINCT ', ' + wk.wilayah
					FROM wilayah_karyawan wk
					WHERE wk.id_karyawan = k.id
					FOR XML PATH(''), TYPE
				).value('.', 'NVARCHAR(MAX)'), 1, 2, '') AS daftar_wilayah

				FROM karyawan k where k.status = 1 and k.nik = '".$nik."'";

		$d_conf = $m_conf->hydrateRaw($sql);

		$data = [];

		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}

		return $data[0];
	}

	public function save()
    {
        
        $params = $_POST;

		$data_wilayah = $this->get_unit_wilayah($params['header']['nik']);
		// cetak_r($data_wilayah, 1);
        
        try {
            $m_header     			  	= new \Model\Storage\HrisKpiPenilaian_model();
            $m_header->nik            	= $params['header']['nik'];
            $m_header->tanggal_mulai    = $params['header']['tgl_mulai'];
            $m_header->tanggal_selesai  = $params['header']['tgl_selesai'];
            $m_header->total_nilai    	= $params['header']['total_score'];
			$m_header->jabatan    		= $params['header']['jabatan'];
			$m_header->penilai    		= $params['header']['penilai'];
			$m_header->wilayah 			= $data_wilayah['daftar_wilayah'] ?? null;
			$m_header->unit    			= $data_wilayah['daftar_unit'] ?? null;
            $m_header->status    	  	= 'DRAFT';

            $m_header->save();

            $id_header = $m_header->id;

            foreach ($params['detail'] as $v_det) {
                $m_detail 					= new \Model\Storage\HrisKpiPenilaianDetail_model();
                $m_detail->penilaian_id    	= $id_header;
                $m_detail->kode_index       = $v_det['kode_index'];
                $m_detail->nilai      		= $v_det['nilai'];
                $m_detail->skor  			= $v_det['score'];
                $m_detail->catatan	 		= $v_det['keterangan'] ?? null;
                $m_detail->save();
            }

            $id            = $m_header->id;
            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/save', $m_header, $deskripsi_log, null, $id, $m_header);

			$message_telegram =  '['.$_SESSION['id_user'].'] '.$_SESSION['detail_user']['nama_detuser']
				. ' submit Penilaian KPI'
				. "\n\n"
				. 'NIK : '.$params['header']['nik']
				. "\n"
				. 'Periode : '.$params['header']['tgl_mulai'] . ' sd '. $params['header']['tgl_selesai'] 
				. "\n"
				. 'Total Nilai : '.$params['header']['total_score'];

			$this->telegram_lib->sendMessages($message_telegram);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
            // $this->result['content'] = array('id' => $no_mm);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );

    }


	public function approvalKpi()
	{
		$this->add_external_js(array(
			'assets/select2/js/select2.min.js',
			"assets/toastr/js/toastr.js",
			"assets/toastr/js/toastr.min.js",
			'assets/hris/kpi_karyawan/js/kpi_karyawan.js'
		));
		$this->add_external_css(array(
			'assets/select2/css/select2.min.css',
			"assets/toastr/css/toastr.css",
			"assets/toastr/css/toastr.min.css",
			'assets/hris/kpi_karyawan/css/kpi_karyawan.css'
		));

		$data 						= $this->includes;
		// cetak_r($content, 1);

		$content = [];

		$data['title_menu'] = 'KPI Karyawan -  Approval';
		$data['view'] 		= $this->load->view('hris/kpi_karyawan/v_approval_kpi', $content, true);

		$this->load->view($this->template, $data);
	}

	public function loadDataApproval()
	{
		$data_list = $this->getDataKpiOutstanding();

		// cetak_r($data_list,1);


		if (!empty($_POST['kode'])) {
            $kode_get = urldecode($_POST['kode']);
            foreach ($data_list as $key => $val) {
                if (trim($val['nama_jabatan']) == trim($kode_get)) {
                    $val['selected']    = 'selected';
                    $selected           = $val;
                    unset($data_list[$key]);
                    array_unshift($data_list, $selected);
                    // break;
                }
            }
        }
		// cetak_r($data_list, 1);

		

		$content['kpi_outstanding']	= $data_list;

		echo $this->load->view('hris/kpi_karyawan/v_list_approval_kpi', $content, true);
	}

	public function getDataKpiOutstanding()
	{
		$m_conf = new \Model\Storage\Conf();
		$params = $_POST;

		$sql = " SELECT
					hkp.id,
					hkp.nik,
					hkp.total_nilai,
					hkp.tanggal_mulai,
					hkp.tanggal_selesai,
					hkp.status,
					CASE
						WHEN MONTH(hkp.tanggal_mulai) = MONTH(hkp.tanggal_selesai)
							AND YEAR(hkp.tanggal_mulai) = YEAR(hkp.tanggal_selesai)
						THEN DATENAME(MONTH, hkp.tanggal_mulai)
						ELSE DATENAME(MONTH, hkp.tanggal_mulai) + ' - ' +
							DATENAME(MONTH, hkp.tanggal_selesai)
					END AS periode,
					k.nama AS nama_karyawan,
					j.nama AS nama_jabatan
				FROM hris_kpi_penilaian hkp
				OUTER APPLY (
					SELECT TOP 1 kk.*
					FROM karyawan kk
					WHERE kk.nik = hkp.nik
						AND (
							kk.tgl_berlaku IS NULL
							OR kk.tgl_berlaku <= GETDATE()
						)
					ORDER BY
						ISNULL(kk.tgl_berlaku, '1900-01-01') DESC,
						kk.id DESC
				) k
				INNER JOIN jabatan j
					ON j.kode = k.jabatan
				WHERE hkp.status = 'DRAFT' ";

		$d_conf = $m_conf->hydrateRaw($sql);

		$data_outstanding = [];

		if ($d_conf->count() > 0) {
			$data_outstanding = $d_conf->toArray();
		}

		return $data_outstanding;
	}


	public function showPenilaian()
	{
		$params				= $_POST;
		$content['bobot']	= $this->getDataBobotKPI($params['id_data']);
		// cetak_r($content, 1);

		echo $this->load->view('hris/kpi_karyawan/v_detail_bobot_kpi', $content, true);
	}

	public function getDataBobotKPI($id)
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = " select hkpd.*, hkmd.nama_kpi, hkmd.bobot, hkpd.catatan from hris_kpi_penilaian_detail hkpd 
				inner join hris_kpi_master_detail hkmd on hkpd.kode_index = hkmd.kode_index 
				where hkpd.penilaian_id = $id ";

				// cetak_r($sql, 1);

		$d_conf = $m_conf->hydrateRaw($sql);

		$data_outstanding = [];

		if ($d_conf->count() > 0) {
			$data_outstanding = $d_conf->toArray();
		}

		return $data_outstanding;
	}


	public function settingKpi()
	{
		$this->add_external_js(array(
			'assets/select2/js/select2.min.js',
			"assets/toastr/js/toastr.js",
			"assets/toastr/js/toastr.min.js",			
			'assets/hris/kpi_karyawan/js/kpi_karyawan.js',
			"assets/xlsx/js/xlsx.full.min.js",
		));
		$this->add_external_css(array(
			'assets/select2/css/select2.min.css',
			"assets/toastr/css/toastr.css",
			"assets/toastr/css/toastr.min.css",
			'assets/hris/kpi_karyawan/css/kpi_karyawan.css'
		));

		$data 						= $this->includes;
		// cetak_r($content, 1);

		$m_conf = new \Model\Storage\Conf();
		$content['jabatan']	= $m_conf->hydrateRaw("select * from jabatan order by kode asc")->toArray();

		$data['title_menu'] = 'KPI Karyawan - Setting';
		$data['view'] 		= $this->load->view('hris/kpi_karyawan/v_setting_kpi', $content, true);

		$this->load->view($this->template, $data);
	}

	public function loadDataSetting()
	{

	
		$content['list_setting'] 	= $this->getDataSetting();

		echo $this->load->view('hris/kpi_karyawan/v_list_setting_kpi', $content, true);
	}
	

	public function getDataSetting($need = null)
	{

		$m_conf = new \Model\Storage\Conf();

		$sql 	= " select hkmh.*, j.nama as nama_jabatan from hris_kpi_master_header hkmh
				inner join jabatan j on hkmh.jabatan_id = j.kode ";

		$where = [];

		$jenis     = $need['jenis'] ?? null;
        $dataNeed  = $need['data'] ?? null;

		if (($jenis == 'DETAIL' || $jenis == 'EDIT') && !empty($dataNeed)) {
            $where[] = "hkmh.id = '".addslashes($dataNeed)."'";
        }

		if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

		$d_conf = $m_conf->hydrateRaw($sql);

		$data 	= [];
		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}

		return $data;
	}

	public function getKodeDocJabatan($kode)
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = " select kode_dokumen from jabatan where kode = '".$kode."' ";

				// cetak_r($sql, 1);

		$d_conf = $m_conf->hydrateRaw($sql);

		$data = [];

		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}

		return $data[0];
	}


	public function saveSetting()
	{
		$params = $_POST;

		$kd_doc_jabatan = $this->getKodeDocJabatan($params['header']['jabatan']);
		// cetak_r($kd_doc_jabatan, 1);

		$bulan 			= $params['header']['periode'];
		$tahun 			= date('y');
		$key_index 		= 1;

		try {

			$total_bobot = 0;
			foreach ($params['detail'] as $v_det) {
				$total_bobot += $v_det['bobot'];
			}
		
			$m_header     			  	= new \Model\Storage\HrisKpiMasterHeader_model();
			$m_header->nama_template    = $params['header']['nama'];
			$m_header->jabatan_id    	= $params['header']['jabatan'];
			$m_header->periode    		= $params['header']['periode'];
			$m_header->status  			= 'ACTIVE';
			$m_header->total_bobot  	= $total_bobot;
			$m_header->keterangan    	= $params['header']['keterangan'];
			$m_header->created_date    	= date("Y-m-d");
			$m_header->save();

			$id_header = $m_header->id;

			foreach ($params['detail'] as $v_det) {
				$m_detail 					= new \Model\Storage\HrisKpiMasterDetail_model();
				$m_detail->id_header    	= $id_header;
				$m_detail->kode_index    	= 'KPI/'. $kd_doc_jabatan['kode_dokumen'] .'/'. $bulan . '/'. $tahun . '/' . $key_index++;
				$m_detail->nama_kpi        	= $v_det['index_kpi'];
				$m_detail->bobot      		= $v_det['bobot'];
				$m_detail->keterangan	    = $v_det['keterangan'] ?? null;
				$m_detail->save();
			}

			$id            = $m_header->id;
			$deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
			Modules::run('base/event/save', $m_header, $deskripsi_log, null, $id, $m_header);

		    $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
            // $this->result['content'] = array('id' => $no_mm);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
	}

	public function settingEdit()
	{
		$params = $_POST;
		$m_conf = new \Model\Storage\Conf();

		$need = [
            'jenis' => 'DETAIL',
            'data'  => $params['id_data'],
        ];

		$content['header_data']	= $this->getDataSetting($need);
 		$content['detail_data'] = $this->getDataSettingDetail($params['id_data']);
		$content['jabatan']		= $m_conf->hydrateRaw("select * from jabatan order by kode asc")->toArray();

		// cetak_r($content, 1);

		echo $this->load->view('hris/kpi_karyawan/v_edit_setting_kpi', $content, true);
	}

	public function getDataSettingDetail($id)
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = " select * from hris_kpi_master_detail where id_header = $id ";

		$d_conf = $m_conf->hydrateRaw($sql);

		$data 	= [];
		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}

		return $data;

	}

	public function execEditSetting()
	{
		$params = $_POST;

		// cetak_r($params, 1);


		try {

			$total_bobot = 0;
			foreach ($params['detail'] as $v_det) {
				if (empty($v_det['index_kpi'])) {
					continue;
				}

				$total_bobot += (float) $v_det['bobot'];
			}

			$id_header = $params['header']['id_header'];

			$m_header = new \Model\Storage\HrisKpiMasterHeader_model();
			$d_header = $m_header->where('id', $id_header)->first();

			if (!$d_header) {
				throw new Exception('Data header tidak ditemukan.');
			}

			$d_header->nama_template = $params['header']['nama'];
			$d_header->jabatan_id    = $params['header']['jabatan'];
			$d_header->total_bobot   = $total_bobot;
			$d_header->keterangan    = $params['header']['keterangan'];
			$d_header->save();

			$m_detail = new \Model\Storage\HrisKpiMasterDetail_model();
			$m_detail->where('id_header', $id_header)->delete();

			foreach ($params['detail'] as $v_det) {
				$m_detail 					= new \Model\Storage\HrisKpiMasterDetail_model();
				$m_detail->id_header    	= $id_header;
				$m_detail->nama_kpi        	= $v_det['index_kpi'];
				$m_detail->bobot      		= $v_det['bobot'];
				$m_detail->kode_index    	= $v_det['kode_index'];
				$m_detail->keterangan	    = $v_det['keterangan'] ?? null;
				$m_detail->save();
			}

			$deskripsi_log = 'Update KPI oleh '.$this->userdata['detail_user']['nama_detuser'];
			Modules::run('base/event/update', $d_header, $deskripsi_log, null, $id_header, $d_header);

			$this->result['status'] = 1;
			$this->result['message'] = 'Data berhasil diupdate.';
		} catch (Exception $e) {
			$this->result['status'] = 0;
			$this->result['message'] = $e->getMessage();
		}

		display_json($this->result);
	}

	public function execDeleteSetting()
	{
		$params = $_POST;
		// cetak_r($params, 1);

		try{

			$m_detail = new \Model\Storage\HrisKpiMasterDetail_model();
			$m_detail->where('id_header', $params['id_data'])->delete();

			$m_header = new \Model\Storage\HrisKpiMasterHeader_model();
			$m_header->where('id', $params['id_data'])->delete();
			
			$this->result['status'] = 1;
			$this->result['message'] = 'Data berhasil dihapus.';
		} catch (Exception $e) {
			$this->result['status'] = 0;
			$this->result['message'] = $e->getMessage();
		}

		display_json($this->result);
	}

	public function getDataPeriode()
	{
		$params = $_POST;

		$content['data_karyawan'] 	= $this->getKaryawanKpi($params);
		$content['nilai_average']	= $this->getNilaiAverageKpi($params);
		// cetak_r($content, 1);

		echo $this->load->view('hris/kpi_karyawan/v_load_index', $content, true);
	}

	public function getKaryawanKpi($data)
	{
		$bulan = $data['periode']; 
		$tahun = date('Y');

		$tgl_awal  = date('Y-m-01', strtotime("$tahun-$bulan-01"));
		$tgl_akhir = date('Y-m-t', strtotime("$tahun-$bulan-01"));

		$m_conf = new \Model\Storage\Conf();

		$sql = " SELECT
					COUNT(DISTINCT CASE WHEN p.status = 'APPROVED' THEN k.nik END) AS sudah_dinilai,
					COUNT(DISTINCT CASE WHEN p.status = 'DRAFT' THEN k.nik END) AS menunggu_approval,
					COUNT(DISTINCT CASE WHEN p.nik IS NULL THEN k.nik END) AS belum_dinilai
				FROM karyawan k
				LEFT JOIN (
					SELECT nik, status
    				FROM hris_kpi_penilaian
					WHERE tanggal_mulai <= '".$tgl_awal . "'
					AND tanggal_selesai >= '".$tgl_akhir . "'
				) p ON p.nik = k.nik
				WHERE k.status = 1 ";



		$d_conf = $m_conf->hydrateRaw($sql);

		$data 	= [];
		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}

		return $data[0];
	}

	public function getNilaiAverageKpi($data)
	{
		$bulan 			= (int)$data['periode'];
		$tahun 			= date('Y');
		$tgl_awal 		= date('Y-m-01', strtotime("$tahun-$bulan-01"));
		$tgl_akhir 		= date('Y-m-t', strtotime("$tahun-$bulan-01"));
		$tgl_awal_lalu 	= date('Y-m-01', strtotime($tgl_awal . ' -1 month'));
		$tgl_akhir_lalu = date('Y-m-t', strtotime($tgl_awal . ' -1 month'));

		$m_conf = new \Model\Storage\Conf();

		$sql = "
			SELECT
				AVG(
					CASE
						WHEN tanggal_mulai <= '$tgl_akhir'
						AND tanggal_selesai >= '$tgl_awal'
						THEN total_nilai
					END
				) AS avg_sekarang,

				AVG(
					CASE
						WHEN tanggal_mulai <= '$tgl_akhir_lalu'
						AND tanggal_selesai >= '$tgl_awal_lalu'
						THEN total_nilai
					END
				) AS avg_lalu
			FROM hris_kpi_penilaian
			WHERE status = 'APPROVED'
		";

		$d_conf = $m_conf->hydrateRaw($sql);

		$result = [];

		if ($d_conf->count() > 0) {
			$result = $d_conf->toArray()[0];

			$avg_sekarang = (float) ($result['avg_sekarang'] ?? 0);
			$avg_lalu = (float) ($result['avg_lalu'] ?? 0);

			$persentase = 0;

			if ($avg_lalu > 0) {
				$persentase = (($avg_sekarang - $avg_lalu) / $avg_lalu) * 100;
			}

			$result['persentase'] = round($persentase, 2);
			$result['naik'] = $persentase >= 0;
		}

		return $result;
	}


	public function keputusanKpi()
	{
		$params = $_POST;

		// cetak_r($params, 1);

		try {

			$id_header 	= $params['id_data'];
			$m_header 	= new \Model\Storage\HrisKpiPenilaian_model();
			$d_header 	= $m_header->where('id', $id_header)->first();

			if (!$d_header) {
				throw new Exception('Data header tidak ditemukan.');
			}
			
			$d_header->status = $params['val'] == 1 ? 'APPROVED' : 'REJECTED';
			$d_header->approval_by = $_SESSION['detail_user']['nama_detuser'];
			$d_header->save();

			$this->result['status'] = 1;
			$this->result['message'] = 'Data berhasil diupdate.';
		} catch (Exception $e) {
			$this->result['message'] = $e->getMessage();

			
		}

		display_json($this->result);
	}


	public function laporanKpi()
	{
		$this->add_external_js(array(
			'assets/select2/js/select2.min.js',
			"assets/toastr/js/toastr.js",
			"assets/toastr/js/toastr.min.js",
			'assets/hris/kpi_karyawan/js/kpi_karyawan.js'
		));
		$this->add_external_css(array(
			'assets/select2/css/select2.min.css',
			"assets/toastr/css/toastr.css",
			"assets/toastr/css/toastr.min.css",
			'assets/hris/kpi_karyawan/css/kpi_karyawan.css'
		));

		$m_conf = new \Model\Storage\Conf();

		$data 					= $this->includes;

		$content['laporan']		= $this->getLaporanKpi();
		$content['unit']		= $this->get_list_unit();
		$content['jabatan']		= $m_conf->hydrateRaw("select * from jabatan order by kode asc")->toArray();
		// cetak_r($content, 1);

		$data['title_menu'] = 'KPI Karyawan -  Laporan';
		$data['view'] 		= $this->load->view('hris/kpi_karyawan/v_laporan_kpi', $content, true);

		$this->load->view($this->template, $data);
	}


	public function filterLaporanKpi()
	{

		$need = [
            'jenis' => 'FILTER',
            'data'  => $_POST,
        ];

		$content['unit']		= $this->get_list_unit();
		$content['laporan']		= $this->getLaporanKpi($need);
		
		// cetak_r($content, 1);

		echo $this->load->view('hris/kpi_karyawan/v_filter_laporan_kpi', $content, true);
	}

	// public function getLaporanKpi($need = null)
	// {
	// 	$m_conf = new \Model\Storage\Conf();

	// 	$jenis    = $need['jenis'] ?? null;
	// 	$dataNeed = $need['data'] ?? null; // bulan (1-12)

	// 	// cetak_r($dataNeed, 1);

	// 	$tahun 	= date('Y');

	// 	$sql 	= "  SELECT hkp.nik, hkp.tanggal_selesai, hkp.tanggal_mulai, 
	// 						hkp.total_nilai, k.nama, j.nama as nama_jabatan, 
	// 						FORMAT(hkp.tanggal_mulai, 'MMMM yyyy', 'id-ID') AS periode_kpi,
	// 						hdk.tgl_masuk, nama_wilayah, kode_wilayah
	// 					FROM hris_kpi_penilaian hkp
	// 					INNER JOIN karyawan k ON hkp.nik = k.nik AND k.status = 1
	// 					INNER JOIN  jabatan j on hkp.jabatan = j.kode  
	// 					LEFT JOIN hris_data_kandidat hdk on k.nik = hdk.nik 
	// 					LEFT JOIN 
	// 					(
	// 						SELECT 
	// 							uk1.id_karyawan,
	// 							STUFF(
	// 								(
	// 									SELECT ', ' + w.nama
	// 									FROM unit_karyawan uk2
	// 									INNER JOIN wilayah w
	// 										ON uk2.unit = w.id
	// 									WHERE uk2.id_karyawan = uk1.id_karyawan
	// 									FOR XML PATH('')
	// 								),
	// 								1,
	// 								2,
	// 								''
	// 							) AS nama_wilayah,
	// 							STUFF(
	// 								(
	// 									SELECT TOP 1 ', ' + w.kode
	// 									FROM unit_karyawan uk2
	// 									INNER JOIN wilayah w
	// 										ON uk2.unit = w.id
	// 									WHERE uk2.id_karyawan = uk1.id_karyawan
	// 									FOR XML PATH('')
	// 								),
	// 								1,
	// 								2,
	// 								''
	// 							) AS kode_wilayah
	// 						FROM unit_karyawan uk1
	// 						GROUP BY uk1.id_karyawan
	// 					) uk
	// 					ON k.id = uk.id_karyawan ";

	// 	$where = [];

	// 	$where[] = "hkp.status = 'APPROVED'";
		

	// 	if ($jenis == 'FILTER' && !empty($dataNeed)) {

	// 		$where[] = "
	// 			MONTH(hkp.tanggal_mulai) = ".$dataNeed['bulan']."
	// 			AND YEAR(hkp.tanggal_mulai) = ".$tahun."
	// 		";

	// 		if (!empty($dataNeed['unit']) && $dataNeed['unit'] != 'All') {
	// 			$where[] = " kode_wilayah = '".$dataNeed['unit']."'";
	// 		}

	// 		if (!empty($dataNeed['jabatan'])) {
	// 			$where[] = " k.jabatan = '".$dataNeed['jabatan']."'";
	// 		}
	// 	}

	// 	if ($jenis == 'CHECK' && !empty($dataNeed)) {
	// 		$where[] = "
	// 			hkp.nik = '".$dataNeed['nik']."'
	// 		";
	// 	}

	// 	if (!empty($where)) {
	// 		$sql .= " WHERE ".implode(' AND ', $where);
	// 	}

	// 	// cetak_r($sql, 1);


	// 	$d_conf = $m_conf->hydrateRaw($sql);

	// 	$data = [];
	// 	if ($d_conf->count() > 0) {
	// 		$data = $d_conf->toArray();
	// 	}

	// 	$report = [];

	// 	foreach($data as $d){
	// 		$report[$d['periode_kpi']][] = $d;
	// 	}

	// 	return $report;
	// }


	public function getLaporanKpi($need = null)
	{
		$m_conf = new \Model\Storage\Conf();

		$jenis    = $need['jenis'] ?? null;
		$dataNeed = $need['data'] ?? null;

		$tahun = date('Y');

		$sql = "
			SELECT 
				hkp.nik,
				hkp.tanggal_selesai,
				hkp.tanggal_mulai,
				hkp.total_nilai,

				k.nama,

				j.nama AS nama_jabatan,

				FORMAT(
					hkp.tanggal_mulai,
					'MMMM yyyy',
					'id-ID'
				) AS periode_kpi,

				hdk.tgl_masuk,

				hkp.wilayah AS nama_wilayah,
				hkp.unit AS kode_wilayah

			FROM hris_kpi_penilaian hkp

			INNER JOIN karyawan k 
				ON hkp.nik = k.nik 
				AND k.status = 1

			INNER JOIN jabatan j 
				ON hkp.jabatan = j.kode

			LEFT JOIN hris_data_kandidat hdk 
				ON k.nik = hdk.nik
		";

		$where = [];

		$where[] = "hkp.status = 'APPROVED'";


		/*
		|--------------------------------------------------------------------------
		| FILTER
		|--------------------------------------------------------------------------
		*/

		if ($jenis == 'FILTER' && !empty($dataNeed)) {

			$where[] = "
				MONTH(hkp.tanggal_mulai) = " . (int) $dataNeed['bulan'] . "
				AND YEAR(hkp.tanggal_mulai) = " . (int) $tahun . "
			";


			/*
			|--------------------------------------------------------------------------
			| FILTER WILAYAH
			|--------------------------------------------------------------------------
			*/

			if (!empty($dataNeed['unit']) && strtolower($dataNeed['unit']) != 'all') {

				$unit = addslashes($dataNeed['unit']);

				$where[] = "
					hkp.unit = '" . $unit . "'
				";
			}


			/*
			|--------------------------------------------------------------------------
			| FILTER JABATAN
			|--------------------------------------------------------------------------
			*/

			if (!empty($dataNeed['jabatan'])) {

				$jabatan = addslashes($dataNeed['jabatan']);

				$where[] = "
					hkp.jabatan = '" . $jabatan . "'
				";
			}
		}


		/*
		|--------------------------------------------------------------------------
		| CHECK
		|--------------------------------------------------------------------------
		*/

		if ($jenis == 'CHECK' && !empty($dataNeed)) {

			$nik = addslashes($dataNeed['nik']);

			$where[] = "
				hkp.nik = '" . $nik . "'
			";
		}


		/*
		|--------------------------------------------------------------------------
		| WHERE
		|--------------------------------------------------------------------------
		*/

		if (!empty($where)) {
			$sql .= "
				WHERE " . implode(' AND ', $where);
		}


		// cetak_r($sql, 1);


		/*
		|--------------------------------------------------------------------------
		| EXECUTE QUERY
		|--------------------------------------------------------------------------
		*/

		$d_conf = $m_conf->hydrateRaw($sql);

		$data = [];

		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}


		/*
		|--------------------------------------------------------------------------
		| GROUP BY PERIODE
		|--------------------------------------------------------------------------
		*/

		$report = [];

		foreach ($data as $d) {
			$report[$d['periode_kpi']][] = $d;
		}


		return $report;
	}

	public function getKpiPeriode()
	{
		$m_conf = new \Model\Storage\Conf();
		$header	= $m_conf->hydrateRaw("SELECT * from hris_kpi_master_header")->toArray();
		$detail	= $m_conf->hydrateRaw("SELECT * from hris_kpi_master_detail")->toArray(); 

		foreach ($header as $key => $val) {
			$header[$key]['detail'] = [];

			foreach ($detail as $v_detail) {
				if ($v_detail['id_header'] == $val['id']) {
					$header[$key]['detail'][] = $v_detail;
				}
			}
    	}

		// cetak_r($header, 1);

		echo json_encode($header);
	}

	public function loadChartsPeriode()
	{
		$content['data_periode'] = $this->chartsByIndex($_POST) ?? [];
		// cetak_r($content, 1);
		
		echo $this->load->view('hris/kpi_karyawan/v_load_chart_periode', $content, true);
	}

	public function chartsByIndex($data)
	{
		$m_conf = new \Model\Storage\Conf();

		$sql_index = " 
			select 
				hkmh.nama_template,
				hkmh.jabatan_id,
				hkmd.kode_index,
				hkmd.nama_kpi,
				hkmd.bobot
			from hris_kpi_master_header hkmh
			inner join hris_kpi_master_detail hkmd 
				on hkmh.id = hkmd.id_header
			where hkmh.periode = '".$data['bulan']."'
			and hkmh.jabatan_id = '".$data['jabatan']."'
			and hkmh.status = 'ACTIVE'
		";

		// cetak_r($sql_index, 1);

		$d_index = $m_conf->hydrateRaw($sql_index);
		$data_index = $d_index->count() > 0 ? $d_index->toArray() : [];

		$kode_indexs = [];
		foreach ($data_index as $row) {
			$kode_indexs[] = $row['kode_index'];
		}
		$kode_index = "'" . implode("','", $kode_indexs) . "'";

		$result = [];

		if (!empty($kode_index)){

			$sql_penilaian = " select k.nama, hkp.nik, hkp.jabatan, hkpd.kode_index, hkpd.nilai, hkpd.skor 
			from hris_kpi_penilaian hkp
			inner join hris_kpi_penilaian_detail hkpd on hkp.id = hkpd.penilaian_id 
			inner join karyawan k on hkp.nik = k.nik and k.status = 1
			where hkpd.kode_index in (" . $kode_index . ") and hkp.status = 'APPROVED' ";

			// cetak_r($sql_penilaian, 1);

	
			$d_penilaian = $m_conf->hydrateRaw($sql_penilaian);
			$data_penilaian = $d_penilaian->count() > 0 ? $d_penilaian->toArray() : [];
			
			$grouped_penilaian = [];
	
			foreach ($data_penilaian as $p) {
				$grouped_penilaian[$p['kode_index']][] = $p;
			}
	
			foreach ($data_index as $i) {
				$kode_index = $i['kode_index'];
	
				$result[$kode_index] = [
					'kode_index' => $kode_index,
					'nama_kpi' => $i['nama_kpi'],
					'bobot' => $i['bobot'],
					'data_penilaian' => $grouped_penilaian[$kode_index] ?? []
				];
			}
		}


		// cetak_r($result, 1);

		return $result;
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


	public function rankingKpi()
	{

		$m_conf = new \Model\Storage\Conf();

		$this->add_external_js(array(
			'assets/select2/js/select2.min.js',
			"assets/toastr/js/toastr.js",
			"assets/toastr/js/toastr.min.js",
			'assets/hris/kpi_karyawan/js/kpi_karyawan.js'
		));
		$this->add_external_css(array(
			'assets/select2/css/select2.min.css',
			"assets/toastr/css/toastr.css",
			"assets/toastr/css/toastr.min.css",
			'assets/hris/kpi_karyawan/css/kpi_karyawan.css'
		));


		$data 					= $this->includes;
		// $content['akses'] 		= $akses;
		$content['karyawan']	= [];
		$content['unit']		= $this->get_list_unit();
		$content['jabatan']		= $m_conf->hydrateRaw("select * from jabatan order by kode asc")->toArray();

		// cetak_r($content, 1);

		$data['title_menu'] = 'KPI Karyawan -  Ranking';
		$data['view'] 		= $this->load->view('hris/kpi_karyawan/v_ranking_kpi', $content, true);

		$this->load->view($this->template, $data);
	}

	public function ranking_by_periode()
	{
		$params = $_POST;
		
		$content['unit']		 = $this->get_list_unit();
		$content['data_ranking'] = $this->getRankingByPeriode($params);
		// cetak_r($content, 1);

		echo $this->load->view('hris/kpi_karyawan/v_load_ranking', $content, true);
	}


	// public function getRankingByPeriode($data)
	// {
	// 	$bulan = $data['periode']; 
	// 	$tahun = date('Y');

	// 	$m_conf = new \Model\Storage\Conf();

	// 	$sql = "SELECT *
	// 			FROM (
	// 				SELECT
	// 					k.nama,
	// 					k.nik,
	// 					hkp.total_nilai AS score,
	// 					hkp.approval_by,
	// 					MONTH(hkp.tanggal_mulai) AS periode,
	// 					j.nama AS nama_jabatan,
	// 					ROW_NUMBER() OVER (
	// 						PARTITION BY j.nama
	// 						ORDER BY hkp.total_nilai DESC
	// 					) AS urut,
	// 					hdk.tgl_masuk as tanggal_masuk,
	// 					nama_wilayah,
	// 					kode_wilayah
	// 				FROM hris_kpi_penilaian hkp
	// 				INNER JOIN karyawan k
	// 					ON hkp.nik = k.nik
	// 					AND k.status = 1
	// 				INNER JOIN jabatan j
	// 					ON hkp.jabatan = j.nama
	// 				LEFT JOIN hris_data_kandidat hdk on k.nik = hdk.nik
					
	// 				LEFT JOIN 
	// 				(
	// 					SELECT 
	// 						uk1.id_karyawan,
	// 						STUFF(
	// 							(
	// 								SELECT ', ' + w.nama
	// 								FROM unit_karyawan uk2
	// 								INNER JOIN wilayah w
	// 									ON uk2.unit = w.id
	// 								WHERE uk2.id_karyawan = uk1.id_karyawan
	// 								FOR XML PATH('')
	// 							),
	// 							1,
	// 							2,
	// 							''
	// 						) AS nama_wilayah,
	// 						STUFF(
	// 							(
	// 								SELECT TOP 1 ', ' + w.kode
	// 								FROM unit_karyawan uk2
	// 								INNER JOIN wilayah w
	// 									ON uk2.unit = w.id
	// 								WHERE uk2.id_karyawan = uk1.id_karyawan
	// 								FOR XML PATH('')
	// 							),
	// 							1,
	// 							2,
	// 							''
	// 						) AS kode_wilayah
	// 					FROM unit_karyawan uk1
	// 					GROUP BY uk1.id_karyawan
	// 				) uk
	// 				ON k.id = uk.id_karyawan 
	// 				WHERE MONTH(hkp.tanggal_mulai) = '".$data['periode']."' ";

	// 				if ($data['unit']){
	// 					$sql .= " AND kode_wilayah = '".$data['unit']."' ";
	// 				}
					
	// 				if ($data['jabatan']){
	// 					$sql .= " AND k.jabatan = '".$data['jabatan']."' ";
	// 				}
					
	// 				$sql .= " AND hkp.status = 'APPROVED'
	// 			) x
	// 			ORDER BY nama_jabatan, score DESC ";

	// 	// cetak_r($sql, 1);

	// 	$d_conf = $m_conf->hydrateRaw($sql);

	// 	$data 	= [];
	// 	if ($d_conf->count() > 0) {
	// 		$data = $d_conf->toArray();
	// 	}

	// 	$temp = [];
	// 	foreach ($data as $val) {
	// 		$temp[$val['nama_jabatan']][] = $val;
	// 	}

	// 	// cetak_r($temp, 1);

	// 	return $temp;
	// }

	public function getRankingByPeriode($data)
	{
		$bulan = $data['periode'];
		$tahun = date('Y');

		$m_conf = new \Model\Storage\Conf();

		$sql = "
			SELECT *
			FROM (
				SELECT
					k.nama,
					k.nik,

					hkp.total_nilai AS score,
					hkp.approval_by,

					MONTH(hkp.tanggal_mulai) AS periode,

					j.nama AS nama_jabatan,

					ROW_NUMBER() OVER (
						PARTITION BY j.nama
						ORDER BY hkp.total_nilai DESC
					) AS urut,

					hdk.tgl_masuk AS tanggal_masuk,

					/* SNAPSHOT WILAYAH & UNIT DARI KPI */
					hkp.wilayah AS nama_wilayah,
					hkp.unit AS kode_wilayah

				FROM hris_kpi_penilaian hkp

				INNER JOIN karyawan k
					ON hkp.nik = k.nik
					AND k.status = 1

				INNER JOIN jabatan j
					ON hkp.jabatan = j.kode

				LEFT JOIN hris_data_kandidat hdk
					ON k.nik = hdk.nik

				WHERE 
					MONTH(hkp.tanggal_mulai) = '" . (int) $bulan . "'
					AND YEAR(hkp.tanggal_mulai) = '" . (int) $tahun . "'
		";

		/*
		|--------------------------------------------------------------------------
		| FILTER WILAYAH
		|--------------------------------------------------------------------------
		*/

		if (!empty($data['unit']) && strtolower($data['unit']) != 'all') {

			$unit = addslashes($data['unit']);

			$sql .= "
				AND hkp.unit = '" . $unit . "'
			";
		}


		/*
		|--------------------------------------------------------------------------
		| FILTER JABATAN
		|--------------------------------------------------------------------------
		*/

		if (!empty($data['jabatan'])) {

			$jabatan = addslashes($data['jabatan']);

			$sql .= "
				AND k.jabatan = '" . $jabatan . "'
			";
		}


		/*
		|--------------------------------------------------------------------------
		| STATUS KPI
		|--------------------------------------------------------------------------
		*/

		$sql .= "
				AND hkp.status = 'APPROVED'
			) x

			ORDER BY nama_jabatan, score DESC
		";


		// cetak_r($sql, 1);


		/*
		|--------------------------------------------------------------------------
		| EXECUTE QUERY
		|--------------------------------------------------------------------------
		*/

		$d_conf = $m_conf->hydrateRaw($sql);

		$result = [];

		if ($d_conf->count() > 0) {
			$result = $d_conf->toArray();
		}


		/*
		|--------------------------------------------------------------------------
		| GROUP BY JABATAN
		|--------------------------------------------------------------------------
		*/

		$temp = [];

		foreach ($result as $val) {
			$temp[$val['nama_jabatan']][] = $val;
		}


		return $temp;
	}

	public function getRankingByPeriodeDetail()
	{
		$params = $_POST;

		$content['data_ranking'] 	= $this->getDataRankingDetail($params);
		$content['data_header']		= $params;
		// cetak_r($content, 1);

		echo $this->load->view('hris/kpi_karyawan/v_load_ranking_detail', $content, true);
	}

	public function getDataRankingDetail($data)
	{
		$bulan 	= $data['bulan']; 
		$nik 	= $data['nik']; 

		$m_conf = new \Model\Storage\Conf();

		$sql = " SELECT hkmd.nama_kpi, hkmd.bobot, hkpd.nilai, hkpd.skor, hkpd.catatan from hris_kpi_penilaian_detail hkpd 
			inner join hris_kpi_master_detail hkmd on hkpd.kode_index  = hkmd.kode_index 
			inner join hris_kpi_penilaian hkp on hkp.id = hkpd.penilaian_id 
			where hkp.nik = '".$nik."' and MONTH(hkp.tanggal_mulai) = '".$bulan."' ";

		$d_conf = $m_conf->hydrateRaw($sql);

		$data 	= [];
		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}

		return $data;
	}

	public function cetakLaporanPdf()
	{

		$bulan = $this->input->get('bulan', true);

		if (!empty($bulan) && $bulan !== 'null') {
			$need = [
				'jenis' => 'FILTER',
				'data'  => $this->input->get(),
			];

			$content['laporan'] = $this->getLaporanKpi($need);
		} else {
			$content['laporan'] = $this->getLaporanKpi();
		}
		// cetak_r($content, 1);

		$res_view_html = $this->load->view($this->pathView.'v_export_pdf', $content, true);

        echo $res_view_html;
		
	}

	public function loadViewExportPenilaian()
	{
		$content = [];
		echo $this->load->view($this->pathView.'v_import_xls_penilaian', $content, true);
	}

	public function exec_data_penilaian()
	{

		$m_conf = new \Model\Storage\Conf();
		$data 	= json_decode(file_get_contents('php://input'), true);	
		$kode 	= array_column($data, 'kode');

		$kode_index = "'" . implode("','", $kode) . "'";
		$nik 		= $data[0]['nik'];

		$check_nik 	= $m_conf->hydrateRaw("select * from karyawan where nik = '".$nik."' and status = 1 ")->toArray();
		$nik_atasan = $this->cek_nik();

		// cetak_r($nik_atasan);

		if ( $nik_atasan !== null && isset($check_nik[0]) && $check_nik[0]['atasan_nik'] != $nik_atasan){

			$html = ' <div style="border : 1px solid #a21e00; border-radius:5px; width:100%; background-color:#FFB2A1; text-align:center; color: #a21e00; padding:5px;">
				<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Anda tidak memiliki kewenangan untuk melakukan penilaian terhadap karyawan ini
			</div>  ';

		} else {

			if (count($check_nik) > 0) {
	
				$parts 		= explode('/', $data[0]['kode']);
				$periode 	= $parts[2];
				$startdate  = date('Y') . '-' . str_pad($periode, 2, '0', STR_PAD_LEFT) . '-01';
		
				$checkdata  	= $this->getDataPenilaianKpi($nik, $startdate);
				$template_kpi 	= $this->getTemplateKpi($kode_index, $check_nik[0]['jabatan']);
		
				
					foreach ($template_kpi as &$template) {
						foreach ($data as $item) {
							if ($template['kode_index'] == $item['kode']) {
								$template['nilai'] 		= $item['nilai'];
								$template['score'] 		= number_format(($template['bobot'] / 100) * $item['nilai'], 2, '.', '');
								$template['keterangan'] = $item['keterangan'] ?? null;
								break; 
							}
						}
					}
		
				$content['config']		 = empty($checkdata) ? 1 : 0;	
				$content['template_kpi'] = empty($checkdata) ? $template_kpi : []; 
				$content['karyawan']	 = $this->getDataKaryawan($nik);
				$content['periode']		 = $periode;
		
		
				$m_penilai = new \Model\Storage\Karyawan_model();
				$d_penilai = $m_penilai->select(
									'karyawan.*',
									'jabatan.nama as nama_jabatan'
								)
								->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
								->where('karyawan.status', 1)
								->orderBy('karyawan.level', 'asc')
								->get();
		
				$content['penilai'] = $d_penilai->toArray();
				// cetak_r($content['penilai'], 1);
		
				$html = $this->load->view('hris/kpi_karyawan/v_import_bobot', $content, true);
	
			} else {
				$html = ' <div style="border : 1px solid #2987E3; border-radius:5px; width:100%; background-color:#CCE6FF; text-align:center; color:#2987E3; padding:5px;">
					NIK tidak terdaftar
				</div>  ';
			}

		}


		echo $html;


	}

	public function getTemplateKpi($kode, $jabatan)
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = " SELECT hkmd.* from hris_kpi_master_detail hkmd
		inner join hris_kpi_master_header hkmh on hkmd.id_header = hkmh.id
		where hkmd.kode_index in (".$kode.") and hkmh.jabatan_id = '".$jabatan."' ";

		// cetak_r($sql, 1);

		$d_conf = $m_conf->hydrateRaw($sql);

		$data 	= [];
		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}

		return $data;
	}

	public function getDataPenilaianKpi($nik, $startdate) 
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = " SELECT nik, tanggal_mulai, total_nilai from hris_kpi_penilaian where nik = '".$nik."' and tanggal_mulai = '".$startdate."' and status != 'REJECTED' ";

		$d_conf = $m_conf->hydrateRaw($sql);
		
		$data 	= [];
		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}
		
		// cetak_r($sql, 1);
		return $data;
	}


	public function getDataKaryawan($nik) 
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = " SELECT k.nik, k.nama, k.jabatan as kode_jabatan, j.nama as nama_jabatan, k_atasan.nama as nama_atasan, k.atasan_nik as nik_atasan
		from karyawan k 
		inner join jabatan j on k.jabatan = j.kode
		inner join karyawan k_atasan on k.atasan_nik = k_atasan.nik and k_atasan.status = 1
		where k.nik = '".$nik."' and k.status = 1 ";

		
		$d_conf = $m_conf->hydrateRaw($sql);
		
		$data 	= [];
		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}
		
		// cetak_r($data, 1);
		return $data[0];
	}


	public function downloadTemplatePenilaian()
	{

		$nik 		= $this->cek_nik();
		$m_conf 	= new \Model\Storage\Conf();
		$m_karyawan = new \Model\Storage\Karyawan_model();

		$query = $m_karyawan->select(
					'karyawan.*',
					'jabatan.nama as nama_jabatan'
				)
				->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
				->where('karyawan.status', 1);

		if ($nik != null) {
			$query->where('karyawan.atasan_nik', $nik);
		}

		$d_karyawan = $query->orderBy('karyawan.level', 'asc')->get();
		$content['karyawan']  	= $d_karyawan->toArray();

		$sql = "";
		if ($nik != null) { 
			$sql = " select * from jabatan where level > (select level from karyawan where nik = '".$nik."' and status = 1) ";
		} else {
			$sql = " select * from jabatan order by kode asc ";
		}

		$content['jabatan']		= $m_conf->hydrateRaw($sql)->toArray();
		

		$html 					= $this->load->view('hris/kpi_karyawan/v_download_template_penilaian', $content, true);
		echo $html;
	}

	public function downloadTemplateSetting()
	{

		$m_conf = new \Model\Storage\Conf();

		$content['jabatan']		= $m_conf->hydrateRaw("select * from jabatan order by kode asc")->toArray();
		
		$html 					= $this->load->view('hris/kpi_karyawan/v_download_template_setting', $content, true);
		echo $html;
	}


	public function checkDataTemplatePenilaian()
	{
		$data = $this->getDataTemplateKpi($_GET);
		// cetak_r($data);
		if (empty($data)) {
			echo json_encode([
				'status'  => false,
				'message' => 'Data KPI tidak ditemukan.'
			]);
		} else {
			echo json_encode([
				'status'  => true,
				'message' => 'Data tersedia.'
			]);
		}
	}

	public function getDataTemplateKpi($data)
	{
		$m_conf = new \Model\Storage\Conf();

		$sql_header = "
			SELECT id
			FROM hris_kpi_master_header
			WHERE jabatan_id = '".$data['jabatan']."'
			AND periode = '".$data['periode']."'
		";

		// cetak_r($sql_header);

		$data_header = $m_conf->hydrateRaw($sql_header)->toArray();

		if (empty($data_header)) {
			return [];
		}

		$sql_detail = "
			SELECT *
			FROM hris_kpi_master_detail
			WHERE id_header = '".$data_header[0]['id']."'
			ORDER BY id ASC
		";

		return $m_conf->hydrateRaw($sql_detail)->toArray();
	}

	public function execDownloadTemplatePenilaian()
	{
		
		$data_x = $this->getDataTemplateKpi($_GET) ?? [];
		$nik_karyawan 	= isset($_GET['karyawan']) ? $_GET['karyawan'] : null;
		
		if (!empty($data_x)) {
			

			$m_conf 		= new \Model\Storage\Conf();
			$spreadsheet 	= new Spreadsheet();
			$sheet 			= $spreadsheet->getActiveSheet();
			$data_jabatan 	= $m_conf->hydrateRaw("select * from jabatan where kode = '".$_GET['jabatan']."' ")->toArray()[0];


			$sheet->setTitle('Template KPI');

			$sheet->mergeCells('A1:F1');
			$sheet->setCellValue('A1', 'TEMPLATE IMPORT KPI ' . $data_jabatan['kode_dokumen'] . ' PERIODE ' . $_GET['periode']);
			$sheet->setCellValue('A2', 'NIK');
			$sheet->setCellValue('B2', 'Kode Index');
			$sheet->setCellValue('C2', 'Nama KPI');
			$sheet->setCellValue('D2', 'Bobot (%)');
			$sheet->setCellValue('E2', 'Nilai');
			$sheet->setCellValue('F2', 'Keterangan');

			
			
			$data = array_map(function ($x) use ($nik_karyawan) {
				return [ $nik_karyawan , $x['kode_index'] , $x['nama_kpi'] , $x['bobot'] , null, null ];
			}, $data_x);


			$row = 3;

			// cetak_r($data, 1);

			foreach ($data as $item) {

				$sheet->setCellValueExplicit('A'.$row, $item[0], DataType::TYPE_STRING);
				$sheet->setCellValue('B'.$row, $item[1]);
				$sheet->setCellValue('C'.$row, $item[2]);
				$sheet->setCellValue('D'.$row, $item[3]);
				$sheet->setCellValue('E'.$row, $item[4]);
				$sheet->setCellValue('F'.$row, $item[5]);

				$row++;
			}

			$sheet->getStyle('A1:F1')->applyFromArray([
				'font' => [
					'bold' => true,
					'size' => 14
				],
				'alignment' => [
					'horizontal' => Alignment::HORIZONTAL_CENTER,
					'vertical'   => Alignment::VERTICAL_CENTER
				]
			]);

			$sheet->getStyle('A2:F2')->applyFromArray([
				'font' => [
					'bold' => true,
					'size' => 11
				],
				'alignment' => [
					'horizontal' => Alignment::HORIZONTAL_CENTER,
					'vertical'   => Alignment::VERTICAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => [
						'rgb' => 'D9D9D9'
					]
				],
				'borders' => [
					'allBorders' => [
						'borderStyle' => Border::BORDER_THIN
					]
				]
			]);

			$sheet->getStyle('A2:F'.($row-1))->applyFromArray([
				'borders' => [
					'allBorders' => [
						'borderStyle' => Border::BORDER_THIN
					]
				]
			]);

			$sheet->getStyle('A3:A'.($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle('D3:E'.($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

			$sheet->getColumnDimension('A')->setWidth(15);
			$sheet->getColumnDimension('B')->setWidth(25);
			$sheet->getColumnDimension('C')->setWidth(45);
			$sheet->getColumnDimension('D')->setWidth(15);
			$sheet->getColumnDimension('E')->setWidth(12);
			$sheet->getColumnDimension('F')->setWidth(30);

			$sheet->getRowDimension(1)->setRowHeight(30);
			$sheet->getRowDimension(2)->setRowHeight(25);

			$sheet->freezePane('A3');

			// KUNCI KOLOM
			$sheet->getStyle('A1:F'.($row - 1))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
			
			$sheet->getStyle('B3:B'.($row - 1))->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
			if (!empty($nik_karyawan)){
				$sheet->getStyle('A3:A'.($row - 1))->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
			}
			$sheet->getStyle('C3:C'.($row - 1))->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
			$sheet->getStyle('D3:D'.($row - 1))->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
			
			$sheet->getProtection()->setSheet(true);
			$sheet->getProtection()->setPassword('gmphris');
			// END KUNCI KOLOM

			$writer = new Xlsx($spreadsheet);

			while (ob_get_level()) {
				ob_end_clean();
			}

			if ($nik_karyawan){
				$filename = 'TEMPLATE_KPI_'. $nik_karyawan .'_'. $data_jabatan['kode_dokumen'] .'_PERIODE_'. $_GET['periode'] .'.xlsx';
			} else {
				$filename = 'TEMPLATE_KPI_'. $data_jabatan['kode_dokumen'] .'_PERIODE_'. $_GET['periode'] .'.xlsx';
			}

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Cache-Control: max-age=0');

			$writer->save('php://output');
			exit;

		} 
		
	}

	public function execDownloadTemplateSetting()
	{

		$m_conf 	= new \Model\Storage\Conf();
		$params 	= $_GET;
		$jabatan	= $m_conf->hydrateRaw("select * from jabatan where kode = '". $params['jabatan'] ."' ")->toArray()[0];

		$bulan = [
			'1'  => 'JANUARI',
			'2'  => 'FEBRUARI',
			'3'  => 'MARET',
			'4'  => 'APRIL',
			'5'  => 'MEI',
			'6'  => 'JUNI',
			'7'  => 'JULI',
			'8'  => 'AGUSTUS',
			'9'  => 'SEPTEMBER',
			'10' => 'OKTOBER',
			'11' => 'NOVEMBER',
			'12' => 'DESEMBER',
		];

		$periode = $bulan[$_GET['periode']];

		// cetak_r($jabatan);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$startRow = 11;
		$lastRow  = 30;

		$sheet->setTitle('Template Setting KPI');
		$sheet->getProtection()->setPassword('gmphris');

		$sheet->mergeCells('A1:C1');
		$sheet->setCellValue('A1', 'TEMPLATE IMPORT SETTING KPI');

		$sheet->setCellValue('A2', 'Petunjuk:');
		$sheet->setCellValue('A3', '1. Isi hanya sel yang berwarna kuning.');
		$sheet->setCellValue('A4', '2. Total bobot seluruh KPI harus 100%.');
		$sheet->setCellValue('A5', '3. Jangan mengubah struktur template.');

		$sheet->setCellValue('A7', 'Jabatan');
		$sheet->setCellValue('B7', 'Periode');
		$sheet->setCellValue('C7', 'Keterangan');

		$sheet->setCellValue('A8', $jabatan['nama']);
		$sheet->setCellValue('B8', $periode);
		$sheet->setCellValue('C8', 'KPI '.$jabatan['nama'].' bulan '.ucwords(strtolower($periode)));

		$sheet->setCellValue('A10', 'Nama KPI');
		$sheet->setCellValue('B10', 'Bobot (%)');
		$sheet->setCellValue('C10', 'Keterangan');

		for ($i = $startRow; $i <= $lastRow; $i++) {
			$sheet->setCellValue("A{$i}", '');
			$sheet->setCellValue("B{$i}", '');
			$sheet->setCellValue("C{$i}", '');
		}

		$sheet->getStyle('A1:C1')->applyFromArray([
			'font' => [
				'bold' => true,
				'size' => 18,
				'color' => ['rgb' => 'FFFFFF']
			],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical'   => Alignment::VERTICAL_CENTER
			],
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['rgb' => '1F4E78']
			]
		]);


		$sheet->getStyle('A7:C7')->applyFromArray([
			'font' => [
				'bold' => true,
				'color' => ['rgb' => 'FFFFFF']
			],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER
			],
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['rgb' => '4F81BD']
			]
		]);

		$sheet->getStyle('A10:C10')->applyFromArray([
			'font' => [
				'bold' => true,
				'color' => ['rgb' => 'FFFFFF']
			],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER
			],
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['rgb' => '70AD47']
			]
		]);

		$sheet->getStyle("A7:C{$lastRow}")->applyFromArray([
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			]
		]);


		$sheet->getStyle("B{$startRow}:B{$lastRow}")
			->getAlignment()
			->setHorizontal(Alignment::HORIZONTAL_CENTER);


		$sheet->getStyle('A8:B8')->applyFromArray([
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'E7E6E6'
				]
			]
		]);


		$sheet->getStyle("A{$startRow}:C{$lastRow}")->applyFromArray([
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'FFFEEB'
				]
			]
		]);


		$sheet->getColumnDimension('A')->setWidth(45);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(50);

		$sheet->getRowDimension(1)->setRowHeight(30);

		$sheet->freezePane('A11');
		$sheet->setAutoFilter("A10:C{$lastRow}");


		$sheet->getStyle('C8')
			->getProtection()
			->setLocked(Protection::PROTECTION_UNPROTECTED);


		$sheet->getStyle("A{$startRow}:C{$lastRow}")
			->getProtection()
			->setLocked(Protection::PROTECTION_UNPROTECTED);

		// Aktifkan proteksi
		$protection = $sheet->getProtection();
		$protection->setSheet(true);
		$protection->setSort(false);
		$protection->setInsertRows(false);
		$protection->setInsertColumns(false);
		$protection->setDeleteRows(false);
		$protection->setDeleteColumns(false);
		$protection->setFormatCells(false);
		$protection->setFormatColumns(false);
		$protection->setFormatRows(false);

		$writer = new Xlsx($spreadsheet);

		while (ob_get_level()) {
			ob_end_clean();
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header(
			'Content-Disposition: attachment; filename="Template_Setting_KPI_' .
			$jabatan['nama'] .
			'_Periode_' .
			ucwords(strtolower($periode)) .
			'.xlsx"'
		);
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		exit;
	}

	public function configDataPeriodeImport()
	{

		$need = [
			'jenis' => 'CHECK',
			'data'  => $_POST,
		];

		$data_check	= $this->getLaporanKpi($need);

		// cetak_r($data_check, 1);
		echo json_encode($data_check);
	}



	public function loadViewExportSettingKpi()
	{

		$m_conf = new \Model\Storage\Conf();

		$content['jabatan']	= $m_conf->hydrateRaw("select * from jabatan order by kode asc")->toArray();

		echo $this->load->view($this->pathView.'v_import_xls_setting', $content, true);
	}

	public function checkDataSetting()
	{
		$params = $_POST;

		$m_conf = new \Model\Storage\Conf();

		$sql = "
			SELECT * FROM hris_kpi_master_header hkmh
			INNER JOIN jabatan j ON hkmh.jabatan_id = j.kode
			WHERE j.nama = '".$params['jabatan']."'
			AND hkmh.periode = '".$params['periode']."'
		";

		$data = $m_conf->hydrateRaw($sql)->toArray();

		if (count($data) > 0) {

			$response = [
				'status'  => 1,
				'message' => 'Data setting KPI untuk jabatan '.$params['jabatan'].' periode '.$params['periode'].' sudah tersedia.'
			];

		} else {

			$response = [
				'status'  => 0,
				'message' => 'Data belum tersedia.'
			];

		}

		echo json_encode($response);
	}


	public function edit_penilaian()
	{
		$params = $_POST;

		$m_karyawan 			= new \Model\Storage\Karyawan_model();
		$d_karyawan 			= $m_karyawan->select(
									'karyawan.*',
									'jabatan.nama as nama_jabatan'
								)->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
								->where('karyawan.status', 1)
								->orderBy('karyawan.level', 'asc')
								->get();

		$content['karyawan']	= $d_karyawan->toArray();
		$content['header'] 		= $this->loadDataPenilaianKpi($params);
		$content['detail'] 		= $this->getDataBobotKPI($params['id_penilaian']);
	
		// cetak_r($content['header'], 1);

		echo $this->load->view($this->pathView.'v_edit_penilaian', $content, true);

	}


	public function configGetKaryawanByPenilai()
	{
		$m_conf = new \Model\Storage\Conf();
		$params = $_POST;

		// cetak_r($params, 1);


		$m_karyawan = new \Model\Storage\Karyawan_model();
		$d_karyawan = $m_karyawan->select(
							'karyawan.*',
							'jabatan.nama as nama_jabatan'
						)
						->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
						->where('karyawan.status', 1)
						->where('karyawan.atasan_nik', $params['penilai'])
						->where('karyawan.jabatan', $params['jabatan'])
						->orderBy('karyawan.level', 'asc')
						->get();

		$karyawan = $d_karyawan->toArray();

		$html = '
			<label>Nama Karyawan</label>
			<select class="select2 karyawan" id="karyawan" onchange="kpi.edit_checkKaryawanPeriode(this, event)">
			<option disabled selected>Pilih Karyawan</option>
		';

		foreach ($karyawan as $k) {
			$html .= '
				<option
					nama_jabatan="'.$k['nama_jabatan'].'"
					jabatan="'.$k['jabatan'].'"
					value="'.$k['nik'].'">
					'.ucwords(strtolower($k['nama'])).'
				</option>
			';
		}

		$html .= '
			</select>
		';

		echo $html;
	}


	public function exec_edit_penilaian()
	{
		$params = $_POST;


		// cetak_r($params, 1);

		try {

			$bulan = $params['header']['bulan'];
			$tahun = date('Y');

			$start_date = date('Y-m-01', strtotime("$tahun-$bulan-01"));
			$end_date   = date('Y-m-t', strtotime("$tahun-$bulan-01"));			

			$id_header = $params['header']['id_data'];

			$m_header = new \Model\Storage\HrisKpiPenilaian_model();
			$m_header = $m_header->where('id', $id_header)->first();

			if (!$m_header) {
				throw new Exception('Data penilaian tidak ditemukan.');
			}

			$m_header->tanggal_mulai   = $start_date;
			$m_header->tanggal_selesai = $end_date;
			$m_header->total_nilai     = $params['header']['total_score'];
			$m_header->jabatan         = $params['header']['jabatan'];
			$m_header->penilai         = $params['header']['penilai'];
			$m_header->nik         	   = $params['header']['nik'];
			$m_header->status          = 'DRAFT';

			// cetak_r($m_header, 1);

			$m_header->update();

			$m_detail = new \Model\Storage\HrisKpiPenilaianDetail_model();

			$detail_lama = $m_detail->where('penilaian_id', $id_header)->get();

			foreach ($detail_lama as $d) {
				$d->delete();
			}

			foreach ($params['detail'] as $v_det) {

				$m_detail = new \Model\Storage\HrisKpiPenilaianDetail_model();

				$m_detail->penilaian_id = $id_header;
				$m_detail->kode_index   = $v_det['kode_index'];
				$m_detail->nilai        = $v_det['nilai'];
				$m_detail->skor         = $v_det['score'];
				$m_detail->catatan      = $v_det['keterangan'] ?? null;

				$m_detail->save();
			}

			// $deskripsi_log = 'di-edit oleh ' . $this->userdata['detail_user']['nama_detuser'];

			// Modules::run(
			// 	'base/event/update',
			// 	$m_header,
			// 	$deskripsi_log,
			// 	null,
			// 	$id_header,
			// 	$m_header
			// );

			$message_telegram = '['.$_SESSION['id_user'].'] '
				.$_SESSION['detail_user']['nama_detuser']
				.' edit Penilaian KPI'
				. "\n\n"
				.'NIK : '.$params['header']['nik']
				."\n"
				.'Periode : '. $start_date
				.' sd '
				.$end_date
				."\n"
				.'Total Nilai : '.$params['header']['total_score'];


			$this->telegram_lib->sendMessages($message_telegram);


			$this->result['status'] = 1;
			$this->result['message'] = 'Data berhasil di edit.';


		} catch (Exception $e) {

			$this->result['message'] = $e->getMessage();

		}


		display_json($this->result);
	}


	public function delete_penilaian()
	{
		$params = $_POST;

		try {

			$id_header = $params['id_data'];

			$m_detail = new \Model\Storage\HrisKpiPenilaianDetail_model();

			$detail = $m_detail->where('penilaian_id', $id_header)->get();

			foreach ($detail as $d) {
				$d->delete();
			}

			$m_header = new \Model\Storage\HrisKpiPenilaian_model();

			$header = $m_header->where('id', $id_header)->first();

			if (!$header) {
				throw new Exception('Data tidak ditemukan.');
			}

			$header->delete();

			$deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];

			Modules::run(
				'base/event/delete',
				$m_header,
				$deskripsi_log,
				null,
				$id_header,
				$header
			);

			$this->result['status'] = 1;
			$this->result['message'] = 'Data berhasil dihapus.';

		} catch (Exception $e) {

			$this->result['status'] = 0;
			$this->result['message'] = $e->getMessage();

		}

		display_json($this->result);
	}



	public function edit_checkDetailKpiByJabatan()
	{
		$params 		= $_POST;
		$data['bobot'] 	= $this->getDataBobot($params);
		// cetak_r($data, 1);

		$html = $this->load->view('hris/kpi_karyawan/v_list_bobot', $data, true);
		echo $html;
	}

	public function edit_checkKaryawanPeriode()
	{
		$params = $_POST;
		// cetak_r($params, 1);

		$m_conf = new \Model\Storage\Conf();

		$bulan = (int) $params['bulan'];
		$startdate = date('Y-m-01', strtotime(date('Y').'-'.$bulan.'-01'));

		$check_kpi = $m_conf->hydrateRaw("
			SELECT id
			FROM hris_kpi_master_header
			WHERE jabatan_id = '".$params['jabatan']."'
			AND periode = '".$params['bulan']."'
		")->toArray();

		if (empty($check_kpi)) {

			$result = [
				'status' => 0,
				'message' => 'Template KPI untuk jabatan dan periode tersebut belum tersedia.'
			];

		} else {

			// $sql = " SELECT nik, tanggal_mulai
			// 	FROM hris_kpi_penilaian
			// 	WHERE nik = '".$params['nik_karyawan']."'
			// 	AND tanggal_mulai = '".$startdate."' ";

			// 	cetak_r($sql, 1);

			$check_data = $m_conf->hydrateRaw("
				SELECT nik, tanggal_mulai
				FROM hris_kpi_penilaian
				WHERE nik = '".$params['nik_karyawan']."'
				AND tanggal_mulai = '".$startdate."'
			")->toArray();


			if (!empty($check_data)) {

				$result = [
					'status' => 0,
					'message' => 'Data penilaian KPI untuk karyawan pada periode yang dipilih sudah tersedia.'
				];

			} else {

				$result = [
					'status' => 1,
					// 'message' => ''
				];

			}
		}

		echo json_encode($result);
	}

}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class KpiKaryawan extends Public_Controller
{
	private $url;

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

			
			
			$data 				= $this->includes;
			$content['charts']	= $this->getDataCharts();
			// cetak_r($data, 1);

			$content['akses'] 	= $akses;
			$data['title_menu'] = 'KPI Karyawan';
			$data['view'] 		= $this->load->view('hris/kpi_karyawan/v_index', $content, true);

			$this->load->view($this->template, $data);
		} else {
			showErrorAkses();
		}
	}

	public function getDataCharts()
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = " SELECT hkp.nik, k.nama as nama_karyawan, total_nilai,
				FORMAT(hkp.tanggal_mulai, 'MMMM yyyy', 'id-ID') AS periode_kpi
				from hris_kpi_penilaian hkp
				inner join karyawan k on hkp.nik = k.nik and k.status = 1
				where hkp.status = 'APPROVED'
				order by hkp.tanggal_mulai asc ";

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
		$content['karyawan']	= $data_karyawan;

		// cetak_r($content, 1);

		$data['title_menu'] = 'KPI Karyawan -  Penilaian';
		$data['view'] 		= $this->load->view('hris/kpi_karyawan/v_penilaian_kpi', $content, true);

		$this->load->view($this->template, $data);
	}


	public function loadDataBobot()
	{
		$params 		= $_POST;
		$data['bobot'] 	= $this->getDataBobot($params);
		// cetak_r($data, 1);

		$html = $this->load->view('hris/kpi_karyawan/v_list_bobot', $data, true);
		echo $html;
	}

	public function getDataBobot($params)
	{
		$m_conf = new \Model\Storage\Conf();

		$jabatan_id = trim($params['jabatan']);
		$periode = trim($params['bulan']);

		$sql = " SELECT hkmd.* FROM hris_kpi_master_header hkmh
		inner join hris_kpi_master_detail hkmd on hkmh.id = hkmd.id_header  and hkmh.periode = '".$periode."'
		WHERE hkmh.jabatan_id = '". $jabatan_id ."' 
		AND hkmh.status = 'ACTIVE'
		ORDER BY hkmh.id ASC ";

		// cetak_r($sql, 1);

		$d_conf = $m_conf->hydrateRaw($sql);

		$data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

		return $data;

	}

	public function configDataPenilaian()
	{
		$m_conf = new \Model\Storage\Conf();
		$params = $_POST;

		// cetak_r($params, 1);

		$sql = "
			SELECT nik
			FROM hris_kpi_penilaian
			WHERE tanggal_mulai = '".$params['startdate']."'
			AND tanggal_selesai = '".$params['enddate']."'
		";

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


	public function save()
    {
        
        $params = $_POST;

		// cetak_r($params, 1);
        
        try {
            $m_header     			  	= new \Model\Storage\HrisKpiPenilaian_model();
            $m_header->nik            	= $params['header']['nik'];
            $m_header->tanggal_mulai    = $params['header']['tgl_mulai'];
            $m_header->tanggal_selesai  = $params['header']['tgl_selesai'];
            $m_header->total_nilai    	= $params['header']['total_score'];
			$m_header->jabatan    		= $params['header']['jabatan'];
            $m_header->status    	  	= 'DRAFT';

            $m_header->save();

            $id_header = $m_header->id;

            foreach ($params['detail'] as $v_det) {
                $m_detail 					= new \Model\Storage\HrisKpiPenilaianDetail_model();
                $m_detail->penilaian_id    	= $id_header;
                $m_detail->kpi_id        	= $v_det['id_kpi'];
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


		if (!empty($_POST['kode'])) {
            $kode_get = urldecode($_POST['kode']);
            foreach ($data_list as $key => $val) {
                if (trim($val['id']) == trim($kode_get)) {
                    $val['selected']    = 'selected';
                    $selected           = $val;
                    unset($data_list[$key]);
                    array_unshift($data_list, $selected);
                    break;
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
		// cetak_r($params, 1);

		echo $this->load->view('hris/kpi_karyawan/v_detail_bobot_kpi', $content, true);
	}

	public function getDataBobotKPI($id)
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = " select hkpd.*, hkmd.nama_kpi, hkmd.bobot  from hris_kpi_penilaian_detail hkpd 
				inner join hris_kpi_master_detail hkmd on hkpd.kpi_id = hkmd.id 
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

		$m_conf = new \Model\Storage\Conf();
		$content['jabatan']	= $m_conf->hydrateRaw("select * from jabatan order by kode asc")->toArray();

		$data['title_menu'] = 'KPI Karyawan - Setting';
		$data['view'] 		= $this->load->view('hris/kpi_karyawan/v_setting_kpi', $content, true);

		$this->load->view($this->template, $data);
	}

	public function loadDataSetting()
	{

		$content['list_setting'] = $this->getDataSetting();

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

	public function saveSetting()
	{
		$params = $_POST;

		// cetak_r($params, 1);

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

		$data 					= $this->includes;

		$content['laporan']		= $this->getLaporanKpi();
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

		$content['laporan']		= $this->getLaporanKpi($need);
		// cetak_r($content, 1);

		echo $this->load->view('hris/kpi_karyawan/v_filter_laporan_kpi', $content, true);
	}

	public function getLaporanKpi($need = null)
	{
		$m_conf = new \Model\Storage\Conf();

		$jenis    = $need['jenis'] ?? null;
		$dataNeed = $need['data'] ?? null; // bulan (1-12)

		$tahun 	= date('Y');

		$sql 	= " SELECT hkp.nik, hkp.tanggal_selesai, hkp.tanggal_mulai, hkp.total_nilai, k.nama, j.nama as nama_jabatan, FORMAT(hkp.tanggal_mulai, 'MMMM yyyy', 'id-ID') AS periode_kpi
				FROM hris_kpi_penilaian hkp
				INNER JOIN karyawan k ON hkp.nik = k.nik AND k.status = 1
				inner join jabatan j on hkp.jabatan = j.kode ";

		$where = [];
		

		if ($jenis == 'FILTER' && !empty($dataNeed)) {
			$where[] = "
				MONTH(hkp.tanggal_mulai) = ".$dataNeed['bulan']."
				AND YEAR(hkp.tanggal_mulai) = ".$tahun."
			";
		}

		if (!empty($where)) {
			$sql .= " WHERE ".implode(' AND ', $where);
		}

		$d_conf = $m_conf->hydrateRaw($sql);

		$data = [];
		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}

		$report = [];

		foreach($data as $d){
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
				hkmd.id as kpi_id,
				hkmd.nama_kpi,
				hkmd.bobot
			from hris_kpi_master_header hkmh
			inner join hris_kpi_master_detail hkmd 
				on hkmh.id = hkmd.id_header
			where hkmh.periode = '".$data['bulan']."'
			and hkmh.jabatan_id = '".$data['jabatan']."'
		";

		// cetak_r($sql_index, 1);

		$d_index = $m_conf->hydrateRaw($sql_index);
		$data_index = $d_index->count() > 0 ? $d_index->toArray() : [];

		$kpi_ids = [];
		foreach ($data_index as $row) {
			$kpi_ids[] = $row['kpi_id'];
		}
		$kpi_id = implode(',', $kpi_ids);

		$result = [];

		if (!empty($kpi_id)){

			$sql_penilaian = " select k.nama, hkp.nik, hkp.jabatan, hkpd.kpi_id, hkpd.nilai, hkpd.skor 
			from hris_kpi_penilaian hkp
			inner join hris_kpi_penilaian_detail hkpd on hkp.id = hkpd.penilaian_id 
			inner join karyawan k on hkp.nik = k.nik and k.status = 1
			where hkpd.kpi_id in (" . $kpi_id . ")";
	
			$d_penilaian = $m_conf->hydrateRaw($sql_penilaian);
			$data_penilaian = $d_penilaian->count() > 0 ? $d_penilaian->toArray() : [];
			
			$grouped_penilaian = [];
	
			foreach ($data_penilaian as $p) {
				$grouped_penilaian[$p['kpi_id']][] = $p;
			}
	
			foreach ($data_index as $i) {
				$kpi_id = $i['kpi_id'];
	
				$result[$kpi_id] = [
					'kpi_id' => $kpi_id,
					'nama_kpi' => $i['nama_kpi'],
					'bobot' => $i['bobot'],
					'data_penilaian' => $grouped_penilaian[$kpi_id] ?? []
				];
			}
		}


		// cetak_r($result, 1);

		return $result;
	}
}
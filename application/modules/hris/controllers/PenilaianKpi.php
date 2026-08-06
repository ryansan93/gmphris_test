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

class PenilaianKpi extends Public_Controller
{
	private $url;
	private $pathView = 'hris/penilaian_kpi/';

	function __construct()
	{
		parent::__construct();
		$this->url = $this->current_base_uri;
		$this->load->library('telegram_lib');
	}

	public function index()
	{
		$akses = hakAkses($this->url);

                $this->add_external_js(array(
                    "assets/select2/js/select2.min.js",
                    "assets/toastr/js/toastr.js",
                    "assets/toastr/js/toastr.min.js",
                    "assets/hris/penilaian_kpi/js/penilaian_kpi.js",
                    "assets/xlsx/js/xlsx.full.min.js"
                ));
                $this->add_external_css(array(
                    'assets/select2/css/select2.min.css',
                    "assets/toastr/css/toastr.css",
                    "assets/toastr/css/toastr.min.css",
                    'assets/hris/penilaian_kpi/css/penilaian_kpi.css'
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

                $data['title_menu'] = 'KPI Karyawan -  Penilaian';
                $data['view'] 		= $this->load->view('hris/penilaian_kpi/v_index', $content, true);       

			$this->load->view($this->template, $data);
		// } else {
		// 	showErrorAkses();
		// }
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

    public function loadPenilaianKpi()
	{
		
		$data['penilaian'] 	= $this->loadDataPenilaianKpi();
		// cetak_r($data, 1);

		$html = $this->load->view('hris/penilaian_kpi/v_riwayat_penilaian', $data, true);
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
	
		// cetak_r($content, 1);

		echo $this->load->view($this->pathView.'v_edit_penilaian', $content, true);

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
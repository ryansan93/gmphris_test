<?php defined('BASEPATH') OR exit('No direct script access allowed');
// •
class Home extends Public_Controller
{
	// private $url;
	function __construct()
	{
		parent::__construct();
		// $this->url = $this->current_base_uri;
	}

	public function index()
	{
		$this->add_external_js(array(
            "assets/chart/chart.js",
            "assets/home/js/home.js",
        ));
        $this->add_external_css(array(
        	"assets/home/css/home.css",
        ));

		$data = $this->includes;

		$data['title_menu'] = 'Dashboard';

		$content['day_off'] 			= $this->dayOff();
		$content['karyawan_aktif']		= $this->getKaryawanAktif();
		$content['karyawan_nonaktif']	= $this->getKaryawanNonAktif();
		
		$content['formDashboardDirut'] = null;
		if ( hakAksesKhusus('dashboard_dirut') ) {
			$content['formDashboardDirut'] = $this->formDashboardDirut();
		}
		// } else {
			// $content['list_notif'] = $this->listNotif();
		// }

		$data['view'] = $this->load->view('home/dashboard', $content, true);

		$this->load->view($this->template, $data);
	}

	public function listNotif()
	{
		$notif = null;

		// ACK USULAN KARYAWAN
			$url_usulan_karyawan   = 'hris/FormAckUsulanKaryawan';
			$akses_usulan_karyawan = hakAkses('/'.$url_usulan_karyawan);


			if ( !empty($akses_usulan_karyawan['a_ack']) && $akses_usulan_karyawan['a_ack'] == 1 ) {

				$status = getStatus('submit');

				$m_usulan_karyawan = new \Model\Storage\HrisDataKandidat_model();
				$data_usulan_karyawan = $m_usulan_karyawan->notifAckDataUsulanKaryawanBaru();

				// cetak_r($data_usulan_karyawan, 1);

				if ( $data_usulan_karyawan ) {

					$display = array_map(function($val){
						return [
							'display'     => $val['document'] . ' - ' . $val['nama_pengusul'] . ' (' . $val['nama_jabatan'] . ')',
							'key'      => $val['document'],
						];
					}, $data_usulan_karyawan);

					$notif['ack_usulan_karyawan'] = $this->mappingNotif(
						$data_usulan_karyawan,
						$url_usulan_karyawan,
						'HRIS - Ack Usulan Karyawan',
						$display
					);

					$notif['ack_usulan_karyawan']['link'] = $url_usulan_karyawan;
					$notif['ack_usulan_karyawan']['jenis'] = 'window.open';
				}
				
			}
		// END ACK USULAN KARYAWAN


		// APPROVE USULAN KARYAWAN
			$url_approve = 'hris/FormAckUsulanKaryawan';
			$akses_approve = hakAkses('/'.$url_approve);

			if ( !empty($akses_approve['a_approve']) && $akses_approve['a_approve'] == 1 ) {

				$status = getStatus('submit');

				$m_dk = new \Model\Storage\HrisDataKandidat_model();
				$data = $m_dk->notifApprovekDataUsulanKaryawanBaru();
				

				if ( $data ) {

					$display = array_map(function($val){
						return [
							'display'     => $val['document'] . ' - ' . $val['nama_pengusul'] . ' (' . $val['nama_jabatan'] . ')',
							'key' 		=> $val['document'],
						];
					}, $data);

					$notif['approve_usulan_karyawan'] = $this->mappingNotif(
						$data,
						$url_approve,
						'HRIS - Approve Usulan Karyawan',
						$display
					);

					$notif['approve_usulan_karyawan']['link'] = $url_approve;
					$notif['approve_usulan_karyawan']['jenis'] = 'window.open';
				}
			}
		// END APPROVE USULAN KARYAWAN

		// ACK KANDIDAT BARU
		$url = 'hris/HrisKandidatBaru';
		$akses = hakAkses('/'.$url);

		if ( !empty($akses['a_ack']) && $akses['a_ack'] == 1 ) {

			$status = getStatus('submit');

			$m_dk = new \Model\Storage\HrisDataKandidat_model();
			$data = $m_dk->notifData();

			if ( $data ) {
				
				$key = 'ack_kandidat_baru';

				$display = array_map(function($val){
					return [
						'display' => $val['document'],
						'key' => $val['document'],
					];
				}, $data);

				$notif[$key] = $this->mappingNotif(
					$data,
					$url,
					'HRIS - Ack Kandidat Baru',
					$display
				);

				$notif['ack_kandidat_baru']['link'] = $url;
				$notif['ack_kandidat_baru']['jenis'] = 'window.open';
			}
		}
		// END ACK KANDIDAT BARU

		// APPROVE KANDIDAT BARU
		$url = 'hris/HrisKandidatBaru';
		$akses = hakAkses('/'.$url);

		if ( !empty($akses['a_approve']) && $akses['a_approve'] == 1 ) {

			$status = getStatus('submit');

			$m_dk = new \Model\Storage\HrisDataKandidat_model();
			$data = $m_dk->notifDataKandidatForm();

			// cetak_r($data, 1);

			if ( $data ) {
				
				$key = 'approve_kandidat_baru';

				$display = array_map(function($val){
					return [
						'display' => $val['document'] . ' - ' . $val['nama'] . ' (' . tglIndonesia($val['tgl_selesai_isi'], "-", " ") . ')',
						'key' => $val['document'],
					];
				}, $data);

				$notif[$key] = $this->mappingNotif(
					$data,
					$url,
					'HRIS - Approve Kandidat Baru',
					$display
				);

				$notif['approve_kandidat_baru']['link'] = $url;
				$notif['approve_kandidat_baru']['jenis'] = 'window.open';
			}
		}
		// END APPROVE KANDIDAT BARU

		// ACK USULAN PROMOSI
			$url_ack_promosi = 'hris/UsulanPromosi';
			$akses = hakAkses('/'.$url_ack_promosi);

			if ( !empty($akses['a_ack']) && $akses['a_ack'] == 1 ) {

				$status = getStatus('submit');

				$need = [
					'status' => 1,
					'jenis'  => 'PROMOSI'
				];

				$m_um = new \Model\Storage\HrisUsulanMutasi_model();
				$data = $m_um->notifUsulan($need);
				// cetak_r($data, 1);

				if ( $data ) {
					$key = 'usulan_promosi_ack';

					$display = array_map(function($val){
						return [
							'display' => $val['kode'] . ' - ' . $val['nama_karyawan'] . ' (' . $val['nama_jabatan'] . ')',
							'key' => $val['kode'],
						];
					}, $data);

					$notif[$key] = $this->mappingNotif(
						$data,
						$url_ack_promosi,
						'HRIS - Ack Usulan Promosi',
						$display
					);


					$notif[$key]['link']  = $url_ack_promosi;
					$notif[$key]['jenis'] = 'window.open';
				}
			}
		// END ACK USULAN PROMOSI

		// APPROVE USULAN PROMOSI
			$url_approve_promosi = 'hris/UsulanPromosi';
			$akses = hakAkses('/'.$url_approve_promosi);

			if ( !empty($akses['a_approve']) && $akses['a_approve'] == 1 ) {

				$status = getStatus('submit');

				$need = [
					'status' => 2,
					'jenis'  => 'PROMOSI'
				];

				$m_um = new \Model\Storage\HrisUsulanMutasi_model();
				$data = $m_um->notifUsulan($need);

				if ( $data ) {
					$key = 'usulan_promosi_approve';

					$display = array_map(function($val){
						return [
							'display' => $val['kode'] . ' - ' . $val['nama_karyawan'] . ' (' . $val['nama_jabatan'] . ')',
							'key' => $val['kode'],
						];
					}, $data);

					$notif[$key] = $this->mappingNotif(
						$data,
						$url_approve_promosi,
						'HRIS - Approve Usulan Promosi',
						$display
					);


					$notif[$key]['link']  = $url_approve_promosi;
					$notif[$key]['jenis'] = 'window.open';
				}
			}
		// END APPROVE USULAN PROMOSI

		// ACK USULAN DEMOSI
			$url_ack_demosi = 'hris/UsulanDemosi';
			$akses = hakAkses('/'.$url_ack_demosi);

			if ( !empty($akses['a_ack']) && $akses['a_ack'] == 1 ) {

				$status = getStatus('submit');

				$need = [
					'status' => 1,
					'jenis'  => 'DEMOSI'
				];

				$m_um = new \Model\Storage\HrisUsulanMutasi_model();
				$data = $m_um->notifUsulan($need);

				if ( $data ) {
					$key = 'usulan_demosi_ack';

					$display = array_map(function($val){
						return [
							'display' => $val['kode'] . ' - ' . $val['nama_karyawan'] . ' (' . $val['nama_jabatan'] . ')',
							'key' => $val['kode'],
						];
					}, $data);

					$notif[$key] = $this->mappingNotif(
						$data,
						$url_ack_demosi,
						'HRIS - Ack Usulan Demosi',
						$display
					);

					$notif[$key]['link']  = $url_ack_demosi;
					$notif[$key]['jenis'] = 'window.open';
				}
			}
		// END ACK USULAN DEMOSI

		// APPROVE USULAN DEMOSI
			$url_approve_demosi = 'hris/UsulanDemosi';
			$akses = hakAkses('/'.$url_approve_demosi);

			if ( !empty($akses['a_approve']) && $akses['a_approve'] == 1 ) {

				$status = getStatus('submit');

				$need = [
					'status' => 2,
					'jenis'  => 'DEMOSI'
				];

				$m_um = new \Model\Storage\HrisUsulanMutasi_model();
				$data = $m_um->notifUsulan($need);

				if ( $data ) {
					$key = 'usulan_demosi_approve';

					$display = array_map(function($val){
						return [
							'display' => $val['kode'] . ' - ' . $val['nama_karyawan'] . ' (' . $val['nama_jabatan'] . ')',
							'key' => $val['kode'],
						];
					}, $data);

					$notif[$key] = $this->mappingNotif(
						$data,
						$url_approve_demosi,
						'HRIS - Approve Usulan Demosi',
						$display
					);

					$notif[$key]['link']  = $url_approve_demosi;
					$notif[$key]['jenis'] = 'window.open';
				}
			}
		// END APPROVE USULAN DEMOSI

		// ACK USULAN MUTASI
			$url_ack_mutasi = 'hris/UsulanMutasi';
			$akses = hakAkses('/'.$url_ack_mutasi);

			if ( !empty($akses['a_ack']) && $akses['a_ack'] == 1 ) {

				$status = getStatus('submit');

				$need = [
					'status' => 1,
					'jenis'  => 'MUTASI'
				];

				$m_um = new \Model\Storage\HrisUsulanMutasi_model();
				$data = $m_um->notifUsulan($need);

				// cetak_r($data, 1);

				if ( $data ) {
					$key = 'usulan_mutasi_ack';

					$display = array_map(function($val){
						return [
							'display' => $val['kode'] . ' - ' . $val['nama_karyawan'] . ' (' . $val['nama_jabatan'] . ')',
							'key' => $val['kode'],
						];
					}, $data);

					$notif[$key] = $this->mappingNotif(
						$data,
						$url_ack_mutasi,
						'HRIS - Ack Usulan Mutasi',
						$display
					);

					$notif[$key]['link']  = $url_ack_mutasi;
					$notif[$key]['jenis'] = 'window.open';
				}
			}
		// END ACK USULAN MUTASI

		// APPROVE USULAN MUTASI
			$url_approve_mutasi = 'hris/UsulanMutasi';
			$akses = hakAkses('/'.$url_approve_mutasi);

			if ( !empty($akses['a_approve']) && $akses['a_approve'] == 1 ) {

				$status = getStatus('submit');

				$need = [
					'status' => 2,
					'jenis'  => 'MUTASI'
				];

				$m_um = new \Model\Storage\HrisUsulanMutasi_model();
				$data = $m_um->notifUsulan($need);

				if ( $data ) {
					$key = 'usulan_mutasi_approve';

					$display = array_map(function($val){
						return [
							'display' => $val['kode'] . ' - ' . $val['nama_karyawan'] . ' (' . $val['nama_jabatan'] . ')',
							'key' => $val['kode'],
						];
					}, $data);

					$notif[$key] = $this->mappingNotif(
						$data,
						$url_approve_mutasi,
						'HRIS - Approve Usulan Mutasi',
						$display
					);

					$notif[$key]['link']  = $url_approve_mutasi;
					$notif[$key]['jenis'] = 'window.open';
				}
			}
		// END APPROVE USULAN MUTASI

		// NOTIFIKASI STATUS KARYAWAN
			$url_status_karyawan = 'hris/HrisStatusKaryawan';
			$akses = hakAkses('/'. $url_status_karyawan);

			// cetak_r($akses, 1);

			if ( !empty($akses['a_edit']) && $akses['a_edit'] == 1 ) {

				// $status = getStatus('submit');

				// $need = [
				// 	'status' => 2,
				// 	'jenis'  => 'MUTASI'
				// ];

				$m_um = new \Model\Storage\HrisStatusKaryawanBaru_model();
				$data = $m_um->notifStatusKaryawan();

				if ( $data ) {
					$key = 'usulan_status_karyawan';

					$display = array_map(function($val){
						return [
							'display' => $val['nik'] . ' - ' .  $val['nama'] . ' ('. $val['keterangan'] .')',
							'key' => $val['id'],
						];
					}, $data);

					$notif[$key] = $this->mappingNotif(
						$data,
						$url_status_karyawan,
						'HRIS - Notifikasi Status Karyawan',
						$display
					);

					$notif[$key]['link']  = $url_status_karyawan;
					$notif[$key]['jenis'] = 'window.open';
				}
			}
		// END NOTIFIKASI STATUS KARYAWAN
		
		// cetak_r($notif, 1);

        return $notif;
	}

	public function mappingNotif($_data, $_url, $_nama_fitur, $_display = [])
	{
		$data = null;

		$data['data'] = $_data;
		$data['path'] = $_url;
		$data['nama_fitur'] = $_nama_fitur;
		$data['display'] = $_display;

		return $data;
	}

	public function formDashboardDirut()
	{
		$m_conf = new \Model\Storage\Conf();
		$d_conf = $m_conf->getDate();

		$today = $d_conf['tanggal'];

		$content['today'] = $today;
		// $content['data_summary'] = $this->_getDataSummaryPanenDanDoc();
		$content['data_summary'] = null;

		$html = $this->load->view('home/formDashboardDirut', $content, true);

		return $html;
	}

	public function getDataNotifikasi() {
		$data = $this->listNotif();

		$content['data'] = $data;
		$html = $this->load->view('home/listNotifikasi', $content, true);

		echo $html;
	}

	public function dayOff()
	{
		$url = 'https://libur.deno.dev/api';

		$ch = curl_init();

		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_SSL_VERIFYPEER => false,
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);

		curl_close($ch);

		if ($error) {
			echo json_encode([
				'status' => false,
				'message' => $error
			]);
			return;
		}

		if ($httpCode != 200) {
			echo json_encode([
				'status' => false,
				'message' => 'Gagal mengambil data'
			]);
			return;
		}

		$data = json_decode($response, true);

		$today = date('Y-m-d');
		$bulanIni = date('m');
		$tahunIni = date('Y');

		$nextHoliday = null;

		foreach ($data as $row) {
			$tanggal = $row['date'];

			if ( $row['is_national_holiday'] == true && date('Y', strtotime($tanggal)) == $tahunIni && date('m', strtotime($tanggal)) == $bulanIni && $tanggal > $today ) {
				$nextHoliday = $row;
				break;
			}
		}

		// cetak_r($nextHoliday, 1);
		return $nextHoliday ?? null;

	}

	public function getKaryawanAktif()
	{
		$m_conf     = new \Model\Storage\Conf();
        $sql        = " select distinct nik, nama, jabatan from karyawan where status = 1 ";
        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
	}

	public function getKaryawanNonAktif()
	{

		$m_conf     = new \Model\Storage\Conf();
        $sql        = " SELECT DISTINCT k1.nik, k1.nama, k1.jabatan
						FROM karyawan k1
						WHERE k1.status = 0
						AND NOT EXISTS (
							SELECT 1
							FROM karyawan k2
							WHERE k2.nik = k1.nik
							AND k2.status = 1
						) ";
        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
	}

	
	
}
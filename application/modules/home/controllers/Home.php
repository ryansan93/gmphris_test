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
            // "assets/chart/chart.js",
            "assets/home/js/home.js",
			"assets/select2/js/select2.min.js",
        ));
        $this->add_external_css(array(
        	"assets/home/css/home.css",
			"assets/select2/css/select2.min.css",
			'assets/hris/kpi_karyawan/css/kpi_karyawan.css'
        ));

		$data = $this->includes;

		$data['title_menu'] = 'Dashboard';

		// $content['day_off'] 			= $this->dayOff() ?? [];
		// cetak_r($content, 1);
		$content['karyawan_tetap']		= $this->getKaryawanTetap();
		$content['karyawan_kontrak']	= $this->getKaryawanKontrak() ?? 0;
		
		$content['formDashboardDirut'] = null;
		if ( hakAksesKhusus('dashboard_dirut') ) {
			$content['formDashboardDirut'] = $this->formDashboardDirut();
		}
		// } else {
			// $content['list_notif'] = $this->listNotif();
		// }

		// KPI
		$content['charts']		= $this->getDataCharts();
		// cetak_r($content['charts'], 1);
		$content['jabatan'] 	= $this->getJabatanByNikAtasan() ?? $this->loadDataPenilaianKpi();
		// END KPI


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
			$akses_ack_mutasi = hakAkses('/'.$url_ack_mutasi);
			// cetak_r($akses_ack_mutasi, 1);
			if ( !empty($akses_ack_mutasi['a_ack']) && $akses_ack_mutasi['a_ack'] == 1 ) {

				$status = getStatus('submit');

				$need = [
					'status' => 1,
					'jenis'  => 'MUTASI'
				];

				$m_um = new \Model\Storage\HrisUsulanMutasi_model();
				$data = $m_um->notifUsulan($need);

				

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


		// NOTIFIKASI KPI KARYAWAN
			$url_approve_kpi_karyawan = 'hris/KpiKaryawan/approvalKpi';
			$akses = hakAkses('/'. $url_approve_kpi_karyawan);

			if ( !empty($akses['a_approve']) && $akses['a_approve'] == 1 ) {

				$m_kpi = new \Model\Storage\HrisKpiPenilaian_model();
				$data  = $m_kpi->notifKpiKaryawan();

				if ($data) {

					$grouped = [];
					foreach ($data as $row) {
						$grouped[$row['nama_jabatan']][] = $row;
					}

					foreach ($grouped as $jabatan => $items) {

						$key = 'kpi_karyawan_' . md5($jabatan);

						$display = [
							[
								'display' => 'Total Karyawan : ' . count($items),
								'key' => null
							]
						];

						$notif[$key] = $this->mappingNotif(
							$items,
							$url_approve_kpi_karyawan,
							'HRIS - Approval KPI ' . $jabatan ,
							$display
						);

						$notif[$key]['link'] = $url_approve_kpi_karyawan;
						$notif[$key]['jenis'] = 'window.open';
					}
				}
			}
		// END NOTIFIKASI KPI KARYAWAN


		// NOTIFIKASI SETTING KPI
			$url_kpi_setting = 'hris/KpiKaryawan/settingKpi/';
			$akses_setting = hakAkses('/'. $url_kpi_setting);

			// if ( !empty($akses['a_edit']) && $akses['a_edit'] == 1 ) {

				$m_kpi = new \Model\Storage\HrisKpiMasterHeader_model();
				$data = []; // $m_kpi->notifSettingKpi();

				// cetak_r($data, 1);

				if ( $data ) {
					$key = 'kpi_karyawan_setting';

					$display = array_map(function($val){
						return [
							'display' => $val['nama_bulan'] . ' - ' .  $val['keterangan'] . ' Belum di Setting)',
							'key' => $val['id'],
						];
					}, $data);

					$notif[$key] = $this->mappingNotif(
						$data,
						$url_kpi_setting,
						'HRIS - Notifikasi KPI Karyawan',
						$display
					);

					$notif[$key]['link']  = $url_kpi_setting;
					$notif[$key]['jenis'] = 'window.open';
				}
			// }
		// END NOTIFIKASI SETTING KPI

		// NOTIFIKASI ACK PENGAJUAN CUTI
		$url_ack_cuti = 'hris/PengajuanCuti/ApprovalPengajuanCuti';
		$akses_ack_cuti = hakAkses('/'. $url_ack_cuti);

		// cetak_r($akses_ack_cuti, 1);
		if ( !empty($akses_ack_cuti['a_ack']) && $akses_ack_cuti['a_ack'] == 1 ) {

			$m_pc 		= new \Model\Storage\PengajuanCuti_model();
			$nik_atasan = $this->cek_nik();
			$data  		= $m_pc->notifAckPengajuanCuti($nik_atasan);

			

			if ( $data ) {
				$key = 'laporan_pengajuan_cuti';

				$display = array_map(function($val){
					return [
						'display' => ucwords(strtolower($val['nama_karyawan'])) . ' - ' .  $val['nik'] ,
						'key' => $val['id'],
					];
				}, $data);

				$notif[$key] = $this->mappingNotif(
					$data,
					$url_ack_cuti,
					'HRIS - Notifikasi Pengajuan Cuti',
					$display
				);

				$notif[$key]['link']  = $url_ack_cuti;
				$notif[$key]['jenis'] = 'window.open';
			}
		}
		// END NOTIFIKASI ACK PENGAJUAN CUTI

		// NOTIFIKASI APPROVE PENGAJUAN CUTI
		$url_ack_cuti = 'hris/PengajuanCuti/ApprovalPengajuanCuti';
		$akses_ack_cuti = hakAkses('/'. $url_ack_cuti);

		if ( !empty($akses_ack_cuti['a_approve']) && $akses_ack_cuti['a_approve'] == 1 ) {

			$m_pc 		= new \Model\Storage\PengajuanCuti_model();
			$nik_atasan = $this->cek_nik();
			$data  		= $m_pc->notifApprovePengajuanCuti();

			if ( $data ) {
				$key = 'laporan_pengajuan_cuti';

				$display = array_map(function($val){
					return [
						'display' => ucwords(strtolower($val['nama_karyawan'])) . ' - ' .  $val['nik'] ,
						'key' => $val['id'],
					];
				}, $data);

				$notif[$key] = $this->mappingNotif(
					$data,
					$url_ack_cuti,
					'HRIS - Notifikasi Pengajuan Cuti',
					$display
				);

				$notif[$key]['link']  = $url_ack_cuti;
				$notif[$key]['jenis'] = 'window.open';
			}
		}
		// END NOTIFIKASI APPROVE PENGAJUAN CUTI


		// NOTIFIKASI ACK USULAN RESIGN
		$url_ack_resign = 'hris/UsulanKaryawanResign/ApprovalUsulanKaryawan';
		$akses_ack_resign = hakAkses('/'. $url_ack_resign);

		// cetak_r($akses_ack_resign, 1);

		
		if ( !empty($akses_ack_resign['a_ack']) && $akses_ack_resign['a_ack'] == 1 ) {

			$m_ur 		= new \Model\Storage\HrisUsulanResign_model();
			$nik_atasan = $this->cek_nik();

			$need 		= [
						'nik' => $nik_atasan,
						'jenis'  => 'NOTIF_ACK'
					];


			$data  		= $m_ur->getNotifUsulan($need);

			// cetak_r($data, 1);

			if ( $data ) {
				$key = 'ack_usulan_resign';

				$display = array_map(function($val){
					return [
						'display' => ucwords(strtolower($val['nama_karyawan'])) . ' - ' .  $val['nik'] ,
						'key' => $val['id'],
					];
				}, $data);

				$notif[$key] = $this->mappingNotif(
					$data,
					$url_ack_resign,
					'HRIS - Acknowledge Usulan Resign',
					$display
				);

				$notif[$key]['link']  = $url_ack_resign;
				$notif[$key]['jenis'] = 'window.open';
			}
		}
		// END NOTIFIKASI ACK USULAN RESIGN

		// NOTIFIKASI APPROVE USULAN RESIGN
		$url_approve_resign = 'hris/UsulanKaryawanResign/ApprovalUsulanKaryawan';
		$akses_approve_resign = hakAkses('/'. $url_approve_resign);

		if ( !empty($akses_approve_resign['a_approve']) && $akses_approve_resign['a_approve'] == 1 ) {

			$m_ur 		= new \Model\Storage\HrisUsulanResign_model();
			$nik_atasan = $this->cek_nik();

			$need 		= [
						'nik' => $nik_atasan,
						'jenis'  => 'NOTIF_APPROVE'
					];


			$data  		= $m_ur->getNotifUsulan($need);


			if ( $data ) {
				$key = 'approve_usulan_resign';

				$display = array_map(function($val){
					return [
						'display' => ucwords(strtolower($val['nama_karyawan'])) . ' - ' .  $val['nik'] ,
						'key' => $val['id'],
					];
				}, $data);

				$notif[$key] = $this->mappingNotif(
					$data,
					$url_ack_resign,
					'HRIS - Approve Usulan Resign',
					$display
				);

				$notif[$key]['link']  = $url_ack_resign;
				$notif[$key]['jenis'] = 'window.open';
			}
		}
		// END NOTIFIKASI APPROVE USULAN RESIGN

		// NOTIFIKASI FORM CLEARANCE 
		$url_form_clearance = 'hris/UsulanKaryawanResign/formClearanceResign';
		$akses_form_clearance = hakAkses('/'. $url_form_clearance);
		$m_usulan   = new \Model\Storage\HrisUsulanResign_model();
		$nik_login  = $this->cek_nik();
		$need 		= [
						'nik' => $nik_login,
						'jenis'  => 'NOTIF_CLEARANCE'
					];
		$data  		= $m_usulan->getNotifUsulan($need);


		// if ( !empty($akses_form_clearance['a_submit']) && $akses_form_clearance['a_submit'] == 1 ) {

			if ( $data ) {
				$key = 'form_clearance';

				$display = array_map(function($val){
					return [
						'display' => ucwords(strtolower($val['nama_karyawan'])) . ' - ' .  $val['nik'] ,
						'key' => $val['id'],
					];
				}, $data);

				$notif[$key] = $this->mappingNotif(
					$data,
					$url_form_clearance,
					'HRIS - Notifikasi Form Clearance',
					$display
				);

				$notif[$key]['link']  = $url_form_clearance;
				$notif[$key]['jenis'] = 'window.open';
			}
		// }
		// END NOTIFIKASI FORM CLEARANCE


		// NOTIFIKASI VERIFIKASI & SERAH TERIMA CLEARANCE 
		$url_verification_clearance = 'hris/UsulanKaryawanResign/verifikasiClearance';
		$akses_verification_clearance = hakAkses('/'. $url_verification_clearance);
		$m_ver   	= new \Model\Storage\HrisAttachmentClearance_model();
		$data  		= $m_ver->getNotifVerificationClearance();

		// cetak_r($data, 1);
		if ( !empty($akses_verification_clearance['a_approve']) && $akses_verification_clearance['a_approve'] == 1 ) {

			if ($data) {

				$data = array_filter($data, function($val){
					return empty($val['verification_clearance_date']);
				});

				if ($data) {

					$key = 'verification_clearance';

					$display = array_map(function($val){
						return [
							'display' => ucwords(strtolower($val['nama_karyawan'])) . ' - ' . $val['nik'],
							'key' => $val['id'],
						];
					}, $data);

					$notif[$key] = $this->mappingNotif(
						$data,
						$url_verification_clearance,
						'HRIS - Verifikasi & Serah Terima Clearance',
						$display
					);

					$notif[$key]['link']  = $url_verification_clearance;
					$notif[$key]['jenis'] = 'window.open';
				}
			}
		}
		// END NOTIFIKASI VERIFIKASI & SERAH TERIMA CLEARANCE


		// NOTIFIKASI NONAKTIF USER
		$url_user_deactivated = 'hris/UserDeactivated';
		$akses_user_deactivated = hakAkses('/'. $url_user_deactivated);
		$m_data   	= new \Model\Storage\HrisUsulanResign_model();
		$data  		= $m_data->getNotifUserActivated();

		// cetak_r($data, 1);
		if ( !empty($akses_user_deactivated['a_view']) && $akses_user_deactivated['a_view'] == 1 ) {

			if ( $data ) {
				
				$key = 'user_deactivated';

				$display = array_map(function($val){
					return [
						'display' => ucwords(strtolower($val['nama_karyawan'])) . ' - ' .  $val['nik'] ,
						'key' => $val['id'],
					];
				}, $data);

				$notif[$key] = $this->mappingNotif(
					$data,
					$url_user_deactivated,
					'HRIS - Penonaktifan User',
					$display
				);

				$notif[$key]['link']  = $url_user_deactivated;
				$notif[$key]['jenis'] = 'window.open';
				

			}
		}
		// END NOTIFIKASI NONAKTIF USER
		

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

	// public function dayOff()
	// {
	// 	$url = 'https://libur.deno.dev/api';

	// 	$ch = curl_init();

	// 	curl_setopt_array($ch, [
	// 		CURLOPT_URL => $url,
	// 		CURLOPT_RETURNTRANSFER => true,
	// 		CURLOPT_TIMEOUT => 30,
	// 		CURLOPT_SSL_VERIFYPEER => false,
	// 	]);

	// 	$response = curl_exec($ch);
	// 	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	// 	$error = curl_error($ch);

	// 	curl_close($ch);

	// 	if ($error) {
	// 		echo json_encode([
	// 			'status' => false,
	// 			'message' => $error
	// 		]);
	// 		return;
	// 	}

	// 	if ($httpCode != 200) {
	// 		echo json_encode([
	// 			'status' => false,
	// 			'message' => 'Gagal mengambil data'
	// 		]);
	// 		return;
	// 	}

	// 	$data = json_decode($response, true);

	// 	$today = date('Y-m-d');
	// 	$bulanIni = date('m');
	// 	$tahunIni = date('Y');

	// 	$nextHoliday = null;

	// 	foreach ($data as $row) {
	// 		$tanggal = $row['date'];

	// 		if ( $row['is_national_holiday'] == true && date('Y', strtotime($tanggal)) == $tahunIni && date('m', strtotime($tanggal)) == $bulanIni && $tanggal > $today ) {
	// 			$nextHoliday = $row;
	// 			break;
	// 		}
	// 	}

	// 	// cetak_r($nextHoliday, 1);
	// 	return $nextHoliday ?? null;

	// }

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

	// public function getKaryawanNonAktif()
	// {

	// 	$m_conf     = new \Model\Storage\Conf();
    //     $sql        = " SELECT DISTINCT k1.nik, k1.nama, k1.jabatan
	// 					FROM karyawan k1
	// 					WHERE k1.status = 0
	// 					AND NOT EXISTS (
	// 						SELECT 1
	// 						FROM karyawan k2
	// 						WHERE k2.nik = k1.nik
	// 						AND k2.status = 1
	// 					) ";
    //     $d_conf     = $m_conf->hydrateRaw( $sql );
    //     $data       = null;

    //     if ( $d_conf->count() > 0 ) {
    //         $data = $d_conf->toArray();
    //     }

    //     return $data;
	// }

	public function getKaryawanKontrak()
	{

		$m_conf     = new \Model\Storage\Conf();

		$sql = " select * from hris_data_kandidat hdk 
				inner join hris_status_kandidat hsk on hdk.status_kandidat = hsk.id 
				where hdk.nik is not null and hdk.status_kandidat != 11 -- Karyawan tetap ";

		$d_conf     = $m_conf->hydrateRaw( $sql );

        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
	}


	public function getKaryawanTetap()
	{
		$m_conf     = new \Model\Storage\Conf();

		$sql = " SELECT 
				k.id,
				k.level,
				k.nik,
				k.nama,
				ISNULL(j.nama, j_temp.nama) AS nama_jabatan,
				k.marketing,
				k.kordinator,
				k.status,
				k.tgl_berlaku,
				k_now.status AS status_aktif,
				atasan.nama AS nama_atasan,

				ISNULL(

					STUFF((
						SELECT ', ' + 
							CASE 
								WHEN khu2.kode_unit = 'All' THEN 'All'
								ELSE w2.nama
							END
						FROM karyawan_history_unit khu2
						LEFT JOIN wilayah w2 
							ON CAST(w2.id AS VARCHAR) = khu2.kode_unit
						WHERE khu2.id = kh.id
						FOR XML PATH(''), TYPE
					).value('.', 'NVARCHAR(MAX)'), 1, 2, ''),

					STUFF((
						SELECT ', ' + 
							CASE 
								WHEN uk.unit = 'All' THEN 'All'
								ELSE w4.nama
							END
						FROM unit_karyawan uk
						LEFT JOIN wilayah w4
							ON CAST(w4.id AS VARCHAR) = uk.unit
						WHERE uk.id_karyawan = k.id
						FOR XML PATH(''), TYPE
					).value('.', 'NVARCHAR(MAX)'), 1, 2, '')

				) AS nama_unit,
				
				ISNULL(

					STUFF((
						SELECT ', ' + 
							CASE 
								WHEN khw2.kode_wilayah = 'All' THEN 'All'
								ELSE w3.nama
							END
						FROM karyawan_history_wilayah khw2
						LEFT JOIN wilayah w3 
							ON CAST(w3.id AS VARCHAR) = khw2.kode_wilayah
						WHERE khw2.id = kh.id
						FOR XML PATH(''), TYPE
					).value('.', 'NVARCHAR(MAX)'), 1, 2, ''),

					STUFF((
						SELECT ', ' + 
							CASE 
								WHEN wk.wilayah = 'All' THEN 'All'
								ELSE w5.nama
							END
						FROM wilayah_karyawan wk
						LEFT JOIN wilayah w5
							ON CAST(w5.id AS VARCHAR) = wk.wilayah
						WHERE wk.id_karyawan = k.id
						FOR XML PATH(''), TYPE
					).value('.', 'NVARCHAR(MAX)'), 1, 2, '')

				) AS nama_wilayah,

				kh.tgl_mulai,
				kh.tgl_selesai

			FROM (
				SELECT DISTINCT nik
				FROM karyawan
				WHERE status = 1
			) src

			OUTER APPLY (
			SELECT TOP 1 *
			FROM karyawan k1
			WHERE k1.nik = src.nik
			ORDER BY
				CASE
					WHEN k1.status = 1
						AND k1.tgl_berlaku IS NOT NULL
						AND k1.tgl_berlaku <= GETDATE()
					THEN 0

					WHEN k1.status = 1
						AND k1.tgl_berlaku IS NULL
					THEN 1

					WHEN k1.status = 0
						AND k1.tgl_berlaku IS NOT NULL
						AND k1.tgl_berlaku <= GETDATE()
					THEN 2

					ELSE 3
				END,

				k1.tgl_berlaku DESC,
				k1.id DESC
			) k

			OUTER APPLY (
				SELECT TOP 1
					kh2.*
				FROM karyawan_history kh2
				WHERE kh2.nik = k.nik
				ORDER BY
					CASE 
						WHEN kh2.tgl_mulai <= GETDATE() THEN 0
						WHEN kh2.tgl_selesai IS NOT NULL THEN 1
						ELSE 2
					END,

					CASE 
						WHEN kh2.tgl_mulai <= GETDATE()
						THEN kh2.tgl_mulai
					END DESC,

					CASE 
						WHEN kh2.tgl_selesai IS NOT NULL
						THEN kh2.tgl_selesai
					END DESC
			) kh

			LEFT JOIN jabatan j ON kh.jabatan = j.kode
			LEFT JOIN jabatan j_temp ON k.jabatan = j_temp.kode
			LEFT JOIN karyawan atasan ON k.atasan_nik = atasan.nik and atasan.status = 1
			LEFT JOIN karyawan k_now on k.nik = k_now.nik AND k_now.status = 1
			WHERE k.id IS NOT NULL
			AND k.nik NOT IN (
			    SELECT hdk.nik
			    FROM hris_data_kandidat hdk
			    WHERE hdk.nik IS NOT NULL
			      AND hdk.status_kandidat <> 11
			)
						
			ORDER BY k.level ASC, ISNULL(j.nama, j_temp.nama) ASC  ";

		$d_conf     = $m_conf->hydrateRaw( $sql );

        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;

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

		// cetak_r($sql);

		if ($d_conf->count() == 0) {
			return null;
		}

		$data = $d_conf->toArray();

		if ($_SESSION['detail_user']['data_group']['nama_group'] == 'HRD') {
			return null;
		}

		return $data[0]['nik'];
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
				
		// cetak_r($sql, 1);

		return $data_charts;
	}

	public function loadChartsPeriode()
	{
		$content['data_periode'] = $this->chartsByIndex($_POST) ?? [];
		// cetak_r($content, 1);
		
		echo $this->load->view('home/v_load_chart_periode', $content, true);
	}

	// public function chartsByIndex($data)
	// {
	// 	$m_conf = new \Model\Storage\Conf();

	// 	$sql_index = " 
	// 		select 
	// 			hkmh.nama_template,
	// 			hkmh.jabatan_id,
	// 			hkmd.kode_index,
	// 			hkmd.nama_kpi,
	// 			hkmd.bobot
	// 		from hris_kpi_master_header hkmh
	// 		inner join hris_kpi_master_detail hkmd 
	// 			on hkmh.id = hkmd.id_header
	// 		where hkmh.periode = '".$data['bulan']."'
	// 		and hkmh.jabatan_id = '".$data['jabatan']."'
	// 		and hkmh.status = 'ACTIVE'
	// 	";

	// 	// cetak_r($sql_index, 1);

	// 	$d_index = $m_conf->hydrateRaw($sql_index);
	// 	$data_index = $d_index->count() > 0 ? $d_index->toArray() : [];

	// 	$kode_indexs = [];
	// 	foreach ($data_index as $row) {
	// 		$kode_indexs[] = $row['kode_index'];
	// 	}
	// 	$kode_index = "'" . implode("','", $kode_indexs) . "'";

	// 	$result = [];

	// 	if (!empty($kode_index)){

	// 		$sql_penilaian = " select k.nama, hkp.nik, hkp.jabatan, hkpd.kode_index, hkpd.nilai, hkpd.skor 
	// 		from hris_kpi_penilaian hkp
	// 		inner join hris_kpi_penilaian_detail hkpd on hkp.id = hkpd.penilaian_id 
	// 		inner join karyawan k on hkp.nik = k.nik and k.status = 1
	// 		where hkpd.kode_index in (" . $kode_index . ") and hkp.status = 'APPROVED' ";

	// 		// cetak_r($sql_penilaian, 1);

	
	// 		$d_penilaian = $m_conf->hydrateRaw($sql_penilaian);
	// 		$data_penilaian = $d_penilaian->count() > 0 ? $d_penilaian->toArray() : [];
			
	// 		$grouped_penilaian = [];
	
	// 		foreach ($data_penilaian as $p) {
	// 			$grouped_penilaian[$p['kode_index']][] = $p;
	// 		}
	
	// 		foreach ($data_index as $i) {
	// 			$kode_index = $i['kode_index'];
	
	// 			$result[$kode_index] = [
	// 				'kode_index' => $kode_index,
	// 				'nama_kpi' => $i['nama_kpi'],
	// 				'bobot' => $i['bobot'],
	// 				'data_penilaian' => $grouped_penilaian[$kode_index] ?? []
	// 			];
	// 		}
	// 	}


	// 	// cetak_r($result, 1);

	// 	return $result;
	// }

	public function chartsByIndex($data)
	{
		$m_conf = new \Model\Storage\Conf();

		// Cari periode master terakhir yang berlaku
		$sql_periode = "
			SELECT TOP 1 periode
			FROM hris_kpi_master_header
			WHERE jabatan_id = '".$data['jabatan']."'
			AND status = 'ACTIVE'
			AND TRY_CONVERT(INT, periode) <= ".(int)$data['bulan']."
			ORDER BY TRY_CONVERT(INT, periode) DESC
		";

		$d_periode = $m_conf->hydrateRaw($sql_periode);

		$periode_master = $d_periode->count() > 0
			? $d_periode->toArray()[0]['periode']
			: null;

		if (empty($periode_master)) {
			return [];
		}

		// Ambil master KPI berdasarkan periode master
		$sql_index = "
			SELECT
				hkmh.nama_template,
				hkmh.jabatan_id,
				hkmd.kode_index,
				hkmd.nama_kpi,
				hkmd.bobot
			FROM hris_kpi_master_header hkmh
			INNER JOIN hris_kpi_master_detail hkmd
				ON hkmh.id = hkmd.id_header
			WHERE hkmh.periode = '".$periode_master."'
			AND hkmh.jabatan_id = '".$data['jabatan']."'
			AND hkmh.status = 'ACTIVE'
			ORDER BY hkmd.kode_index
		";

		$d_index = $m_conf->hydrateRaw($sql_index);

		$data_index = $d_index->count() > 0 ? $d_index->toArray() : [];

		if (empty($data_index)) {
			return [];
		}

		// Ambil seluruh kode index dari master KPI
		$kode_indexs = array_column($data_index, 'kode_index');

		$kode_index = "'" . implode("','", $kode_indexs) . "'";

		// Ambil penilaian berdasarkan periode yang dipilih
		$sql_penilaian = "
			SELECT
				k.nama,
				hkp.nik,
				hkp.jabatan,
				hkpd.kode_index,
				hkpd.nilai,
				hkpd.skor
			FROM hris_kpi_penilaian hkp
			INNER JOIN hris_kpi_penilaian_detail hkpd
				ON hkp.id = hkpd.penilaian_id
			INNER JOIN karyawan k
				ON hkp.nik = k.nik
				AND k.status = 1
			WHERE hkpd.kode_index IN ($kode_index)
			AND hkp.status = 'APPROVED'
			AND hkp.jabatan = '".$data['jabatan']."'
			AND MONTH(hkp.tanggal_mulai) = ".(int)$data['bulan']."
		";

		$d_penilaian = $m_conf->hydrateRaw($sql_penilaian);

		$data_penilaian = $d_penilaian->count() > 0 ? $d_penilaian->toArray() : [];

		// Group penilaian berdasarkan kode index
		$grouped_penilaian = [];

		foreach ($data_penilaian as $p) {
			$grouped_penilaian[$p['kode_index']][] = $p;
		}

		// Gabungkan master KPI dengan data penilaian
		$result = [];

		foreach ($data_index as $i) {

			$kode = $i['kode_index'];

			$result[$kode] = [
				'kode_index'      => $kode,
				'nama_kpi'        => $i['nama_kpi'],
				'bobot'           => $i['bobot'],
				'periode_master'  => $periode_master,
				'periode_nilai'   => $data['bulan'],
				'data_penilaian'  => $grouped_penilaian[$kode] ?? []
			];
		}

		return $result;
	}

	
	
}
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

		// $content['list_notif'] = $this->list_notif();
		// $content['jml_notif'] = count($this->list_notif());
		
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
							'display'     => $val['document'],
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
							'display'     	=> $val['document'],
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
							'display' => $val['kode'],
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
							'display' => $val['kode'],
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
							'display' => $val['kode'],
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
							'display' => $val['kode'],
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

				if ( $data ) {
					$key = 'usulan_mutasi_ack';

					$display = array_map(function($val){
						return [
							'display' => $val['kode'],
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
							'display' => $val['kode'],
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

	public function _getDataSummaryPanenDanDoc()
	{
		$data = null;

		$m_conf = new \Model\Storage\Conf();
		$d_conf = $m_conf->getDate();

		$today = $d_conf['tanggal'];

		$first_date = date('Y-m-01', strtotime($today)).' 00:00:00';
		$end_date = date('Y-m-t', strtotime($today)).' 23:59:59';

		$m_td = new \Model\Storage\TerimaDoc_model();
		$sql = "
			select sum(jml_ekor) as jml_ekor, sum(total) as total, count(noreg) as jml_kdg from 
			(
				select sum(td.jml_ekor) as jml_ekor, sum(td.total) as total, od.noreg from terima_doc td
				right join
					(select max(id) as id, no_order, no_sj, nopol from terima_doc where datang BETWEEN '".$first_date."' and '".$end_date."' group by no_order, no_sj, nopol) td1
					on 
						td1.id = td.id 
				left join
					(
						select od1.* from order_doc od1
						right join
							(select max(id) as id, no_order from order_doc group by no_order) od2
							on
								od1.id = od2.id
					) od
					on
						td.no_order = od.no_order 
				where
					td.datang BETWEEN '".$first_date."' and '".$end_date."' and
					od.no_order is not null
				group by
					od.noreg
			) td
		";
		$d_td = $m_td->hydrateRaw( $sql );

		if ( $d_td->count() > 0 ) {
			$d_td = $d_td->toArray();

			$data['docin']['jml_ekor'] = $d_td[0]['jml_ekor'];
			$data['docin']['jml_kdg'] = $d_td[0]['jml_kdg'];
			$data['docin']['rata_harga_doc'] = ($d_td[0]['total'] <> 0 && $d_td[0]['jml_ekor'] <> 0) ? $d_td[0]['total'] / $d_td[0]['jml_ekor'] : 0;
			// $data['docin']['rata_harga_pakan'] = 0;
		}

		// $m_tp = new \Model\Storage\TerimaPakan_model();
		// $sql = "
		// 	select sum(jumlah) as jumlah, sum(cast(total as bigint)) as total, round((sum(cast(total as bigint)) / sum(jumlah)), 2) as rata_harga from 
		// 	(
		// 		select dtp.jumlah, (dtp.jumlah * opd.harga) as total from det_terima_pakan dtp 
		// 		left join
		// 			(select max(id) as id, id_kirim_pakan, tgl_terima from terima_pakan tp group by id_kirim_pakan, tgl_terima ) tp 
		// 			on
		// 				dtp.id_header = tp.id
		// 		left join
		// 			kirim_pakan kp 
		// 			on
		// 				kp.id = tp.id_kirim_pakan 
		// 		left join
		// 			order_pakan op 
		// 			on
		// 				op.no_order = kp.no_order 
		// 		left join
		// 			order_pakan_detail opd 
		// 			on
		// 				opd.id_header = op.id and
		// 				dtp.item = opd.barang 
		// 		where
		// 			tp.tgl_terima BETWEEN '".$first_date."' and '".$end_date."'
		// 	) dtp
		// ";
		// $d_tp = $m_tp->hydrateRaw( $sql );

		// if ( $d_tp->count() > 0 ) {
		// 	$d_tp = $d_tp->toArray();

		// 	$data['docin']['rata_harga_pakan'] = $d_tp[0]['rata_harga'];
		// }

		$m_rs = new \Model\Storage\RealSJ_model();
		$sql = "
			select sum(tonase) as tonase, sum(ekor) as ekor, round(sum(lama_panen)/count(*), 2) as rata_lama_panen, round(sum(grand_total)/sum(tonase), 2) as rata_harga from 
			(
				select
					min(panen.tgl_panen) as awal, 
					max(panen.tgl_panen) as akhir, 
					(datediff(day, min(panen.tgl_panen), max(panen.tgl_panen)) + 1) as lama_panen,
					sum(panen.tonase_detail) as tonase_detail,
					sum(panen.tonase) as tonase,
					sum(panen.ekor_detail) as ekor_detail,
					sum(panen.ekor) as ekor,
					sum(panen.total) as grand_total
				from 
				(
					select 
						rs.noreg,
						rs.tgl_panen,
						sum(drs.tonase) as tonase_detail,
						rs.netto_kg as tonase,
						sum(drs.ekor) as ekor_detail,
						rs.netto_ekor as ekor,
						sum(drs.tonase * drs.harga) as total
					from det_real_sj drs 
					left join
						real_sj rs
						on
							drs.id_header = rs.id
					where
						drs.harga > 0 and
						rs.noreg is not null
					group by
						drs.id_header,
						rs.noreg,
						rs.tgl_panen,
						rs.netto_kg,
						rs.netto_ekor
				) as panen
				where
					panen.tgl_panen between '".$first_date."' and '".$end_date."'
				group by
					panen.noreg
			) panen
		";
		$d_rs = $m_rs->hydrateRaw( $sql );

		if ( $d_rs->count() > 0 ) {
			$d_rs = $d_rs->toArray();

			$data['panen']['tonase'] = $d_rs[0]['tonase'];
			$data['panen']['ekor'] = $d_rs[0]['ekor'];
			$data['panen']['rata_lama_panen'] = $d_rs[0]['rata_lama_panen'];
			$data['panen']['rata_harga'] = $d_rs[0]['rata_harga'];
		}

		return $data;
	}

	public function getDataSummaryPanenDanDoc()
	{
		try {
			$this->result['status'] = 1;
			$this->result['content'] = $this->_getDataSummaryPanenDanDoc();
		} catch (Exception $e) {
			$this->result['message'] = $e->getMessage();
		}

		display_json( $this->result );
	}

	public function getDataPanjualanDanHarga()
	{
		try {
			$data = null;

			$m_conf = new \Model\Storage\Conf();
			$d_conf = $m_conf->getDate();

			$today = $d_conf['tanggal'];

			$first_date = prev_date($today, 10).' 00:00:00';
			$end_date = $today.' 23:59:59';

			$m_rs = new \Model\Storage\RealSJ_model();
			$sql = "
				select
					panen.tgl_panen, 
					round(sum(panen.tonase_detail), 2) as tonase_detail,
					round(sum(panen.tonase), 2) as tonase,
					sum(panen.ekor_detail) as ekor_detail,
					sum(panen.ekor) as ekor,
					sum(panen.total) as grand_total,
					round((sum(panen.total)/sum(panen.tonase)), 2) as harga
				from 
				(
					select 
						rs.noreg,
						rs.tgl_panen,
						round(sum(drs.tonase), 2) as tonase_detail,
						rs.netto_kg as tonase,
						sum(drs.ekor) as ekor_detail,
						rs.netto_ekor as ekor,
						sum(drs.tonase * drs.harga) as total
					from det_real_sj drs 
					left join
						real_sj rs
						on
							drs.id_header = rs.id
					where
						drs.harga > 0 and
						rs.noreg is not null
					group by
						drs.id_header,
						rs.noreg,
						rs.tgl_panen,
						rs.netto_kg,
						rs.netto_ekor
				) as panen
				where
					panen.tgl_panen between '".$first_date."' and '".$end_date."'
				group by
					panen.tgl_panen
				order by
					panen.tgl_panen asc
			";
			$d_rs = $m_rs->hydrateRaw( $sql );

			if ( $d_rs->count() > 0 ) {
				$d_rs = $d_rs->toArray();

				foreach ($d_rs as $k_rs => $v_rs) {
					$data['tgl_panen'][] = (int)substr($v_rs['tgl_panen'], -2);
					$data['tonase'][] = $v_rs['tonase'];
					$data['harga'][] = $v_rs['harga'];
					$data['tgl_panen_real'][] = tglIndonesia($v_rs['tgl_panen'], '-', ' ');
				}
			}

			$this->result['content'] = $data;
		} catch (Exception $e) {
			$this->result['message'] = $e->getMessage();
		}

		display_json( $this->result );
	}

	public function getDataPlasmaMerah()
	{
		try {
			$data = null;

			$m_conf = new \Model\Storage\Conf();
			$d_conf = $m_conf->getDate();

			$today = $d_conf['tanggal'];

			$first_date = prev_date($today, 10).' 00:00:00';
			$end_date = $today.' 23:59:59';

			$m_rs = new \Model\Storage\RealSJ_model();
			$sql = "
				select r.*, w.kode, m.nama, k.kandang from rhpp r 
				right join
					tutup_siklus ts 
					on
						r.id_ts = ts.id
				right join
					rdim_submit rs 
					on
						rs.noreg = r.noreg
				right join
					(select max(mitra) as mitra, nim from mitra_mapping group by nim) mm
					on
						mm.nim = rs.nim
				right join
					mitra m
					on
						m.id= mm.mitra

				right join
					kandang k
					on
						rs.kandang = k.id
				right join
					wilayah w 
					on
						w.id = k.unit 
				where
					r.jenis = 'rhpp_plasma' and
					r.pdpt_peternak_sudah_pajak < 0 and
					ts.tgl_tutup between '".$first_date."' and '".$end_date."'
			";
			$d_rs = $m_rs->hydrateRaw( $sql );

			$_data = null;
			if ( $d_rs->count() > 0 ) {
				$d_rs = $d_rs->toArray();

				foreach ($d_rs as $k_rs => $v_rs) {
					$_data[ $v_rs['kode'] ]['kode_unit'] = $v_rs['kode'];
					$_data[ $v_rs['kode'] ]['mitra'][] = $v_rs['nama'].' (KDG : '.$v_rs['kandang'].')';
					if ( !isset($_data[ $v_rs['kode'] ]['jumlah']) ) {
						$_data[ $v_rs['kode'] ]['jumlah'] = 1;
					} else {
						$_data[ $v_rs['kode'] ]['jumlah'] += 1;
					}
				}
			}

			if ( !empty($_data) ) {
				foreach ($_data as $key => $value) {
					$data['kode_unit'][] = $value['kode_unit'];
					$data['jumlah'][] = $value['jumlah'];
					$data['mitra'][] = $value['mitra'];
				}
			}

			$this->result['content'] = $data;
		} catch (Exception $e) {
			$this->result['message'] = $e->getMessage();
		}

		display_json( $this->result );
	}
}
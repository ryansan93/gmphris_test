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

class LaporanKpiLapangan extends Public_Controller
{
	private $url;
	private $pathView = 'hris/laporan_kpi_lapangan/';

	function __construct()
	{
		parent::__construct();
		$this->url = $this->current_base_uri;
		$this->load->library('telegram_lib');
	}

	public function index()
	{
		$akses = hakAkses($this->url);
		// if ( $akses['a_view'] == 1 ) {

			$this->add_external_js(array(
				'assets/select2/js/select2.min.js',
				'assets/toastr/js/toastr.js',
                'assets/toastr/js/toastr.min.js',
				'assets/hris/laporan_kpi_lapangan/js/laporan_kpi_lapangan.js',
				'assets/xlsx/js/xlsx.full.min.js'
			));

			$this->add_external_css(array(
				'assets/select2/css/select2.min.css',
				"assets/toastr/css/toastr.css",
                "assets/toastr/css/toastr.min.css",
				'assets/hris/laporan_kpi_lapangan/css/laporan_kpi_lapangan.css'
			));
			
			$data 				= $this->includes;


			$data['title_menu'] = 'Laporan KPI Lapangan';

			$content 			= [];
			$data['view'] 		= $this->load->view('hris/laporan_kpi_lapangan/v_index', $content, true);

			$this->load->view($this->template, $data);
		// } else {
		// 	showErrorAkses();
		// }
	}


	public function filter_data()
	{
		$params = $_POST;

		$data['list_data_ppl'] 			= $this->get_list_data_ppl($params);
		$data['list_data_penimbang'] 	= $this->get_list_data_penimbang($params);
		$data['params']					= $params;
		
		// cetak_r($params, 1);

		echo $this->load->view('hris/laporan_kpi_lapangan/v_list', $data, true);
	}


	// public function get_list_data_ppl($params)
	// {
	// 	$m_conf = new \Model\Storage\Conf();

	// 	// 1. AMANKAN INPUT & FILTER UNIT
	// 	$tahun = isset($params['tahun']) ? (int)$params['tahun'] : 0;
	// 	$bulan = isset($params['bulan']) ? (int)$params['bulan'] : 0;
	// 	$id_wilayah = isset($params['id_wilayah']) ? (int)$params['id_wilayah'] : 0;

	// 	$sql = "WITH Data_PPL AS (
	// 				SELECT 
	// 					w.nama AS nama_wilayah, -- <--- TAMBAHKAN INI
	// 					ppl.nama AS nama_ppl,
	// 					SUM(r.populasi) AS total_populasi,
	// 					COUNT(DISTINCT r.noreg) AS jumlah_peternak,
	// 					AVG(r.fcr) AS rata_fcr,
	// 					AVG(r.ip) AS rata_ip,
	// 					AVG(r.deplesi) AS rata_deplesi,
	// 					AVG(r.bb) AS rata_bb,
	// 					AVG(r.rata_umur) AS rata_umur_panen
	// 				FROM rhpp r
	// 				INNER JOIN rdim_submit rs ON r.noreg = rs.noreg 
	// 				INNER JOIN kandang k ON rs.kandang = k.id
	// 				INNER JOIN wilayah w ON k.unit = w.id
	// 				INNER JOIN karyawan ppl ON rs.sampling = ppl.nik AND ppl.status = 1
	// 				WHERE r.id_ts NOT IN (SELECT id_header FROM rhpp_group)
	// 				AND r.jenis = 'rhpp_plasma' ";
					
	// 				if ($tahun > 0) {
	// 					$sql .= " AND YEAR(r.tgl_docin) = {$tahun} ";
	// 				}
	// 				if ($bulan > 0) {
	// 					$sql .= " AND MONTH(r.tgl_docin) = {$bulan} ";
	// 				}
	// 				if ($id_wilayah > 0) {
	// 					$sql .= " AND w.id = {$id_wilayah} ";
	// 				}

	// 	$sql .= " GROUP BY w.nama, ppl.nama
	// 			),
	// 			Total_Unit AS (
	// 				SELECT SUM(r.populasi) AS grand_total_populasi
	// 				FROM rhpp r
	// 				INNER JOIN rdim_submit rs ON r.noreg = rs.noreg 
	// 				INNER JOIN kandang k ON rs.kandang = k.id
	// 				INNER JOIN wilayah w ON k.unit = w.id
	// 				WHERE r.id_ts NOT IN (SELECT id_header FROM rhpp_group)
	// 				AND r.jenis = 'rhpp_plasma' ";
					
	// 				if ($tahun > 0) {
	// 					$sql .= " AND YEAR(r.tgl_docin) = {$tahun} ";
	// 				}
	// 				if ($bulan > 0) {
	// 					$sql .= " AND MONTH(r.tgl_docin) = {$bulan} ";
	// 				}
	// 				if ($id_wilayah > 0) {
	// 					$sql .= " AND w.id = {$id_wilayah} ";
	// 				}
					
	// 	$sql .= ")
	// 			SELECT 
	// 				p.nama_wilayah, -- <--- TAMPILKAN DI SINI
	// 				p.nama_ppl,
	// 				p.total_populasi,
	// 				p.jumlah_peternak,
	// 				ROUND(p.rata_fcr, 3) AS rata_fcr,
	// 				ROUND(p.rata_ip, 2) AS rata_ip,
	// 				ROUND(p.rata_deplesi, 2) AS rata_deplesi,
	// 				ROUND(p.rata_bb, 3) AS rata_bb,
	// 				ROUND(p.rata_umur_panen, 1) AS rata_umur_panen,
	// 				ROUND((p.total_populasi * 100.0 / NULLIF(t.grand_total_populasi, 0)), 2) AS persen_kontribusi_populasi
	// 			FROM Data_PPL p
	// 			CROSS JOIN Total_Unit t
	// 			ORDER BY p.nama_wilayah, p.total_populasi DESC ";

	// 	$d_conf = $m_conf->hydrateRaw($sql);
	// 	$data   = null;

	// 	if ($d_conf->count() > 0) {
	// 		$data = $d_conf->toArray();
	// 	}

	// 	return $data;
	// }

	public function get_list_data_ppl($params)
	{
		$m_conf = new \Model\Storage\Conf();

		$tahun = isset($params['tahun']) ? (int)$params['tahun'] : 0;
		$bulan = isset($params['bulan']) ? (int)$params['bulan'] : 0;
		$id_wilayah = isset($params['id_wilayah']) ? (int)$params['id_wilayah'] : 0;

		$filter_tanggal_individual = "";
		$filter_tanggal_group = "";
		
		if ($tahun > 0) {
			if ($bulan > 0) {
				$tgl_awal = sprintf("%04d-%02d-01", $tahun, $bulan);
				$tgl_depan = date("Y-m-01", strtotime($tgl_awal . " +1 month"));
				$filter_tanggal_individual = " AND ts.tgl_tutup >= '{$tgl_awal}' AND ts.tgl_tutup < '{$tgl_depan}' ";
				$filter_tanggal_group = " AND rgh.tgl_submit >= '{$tgl_awal}' AND rgh.tgl_submit < '{$tgl_depan}' ";
			} else {
				$tgl_awal = "{$tahun}-01-01";
				$tahun_depan = $tahun + 1;
				$filter_tanggal_individual = " AND ts.tgl_tutup >= '{$tgl_awal}' AND ts.tgl_tutup < '{$tahun_depan}-01-01' ";
				$filter_tanggal_group = " AND rgh.tgl_submit >= '{$tgl_awal}' AND rgh.tgl_submit < '{$tahun_depan}-01-01' ";
			}
		}

		$filter_wilayah = "";
		if ($id_wilayah > 0) {
			$filter_wilayah = " AND w.id = {$id_wilayah} ";
		}

		// 4. SUSUN QUERY SQL (FINAL FIX: GROUPING PPL BENAR-BENAR MENYATU)
		$sql = "WITH 
				-- 1. DATA INDIVIDUAL
				Data_Individual AS (
					SELECT 
						COALESCE(w.alias, CAST(w.id AS VARCHAR(50))) AS kode_wilayah,
						ppl.nama AS nama_ppl_raw,
						r.noreg,
						r.populasi,
						r.fcr,
						r.ip,
						r.deplesi,
						r.bb,
						r.rata_umur
					FROM rhpp r
					INNER JOIN rdim_submit rs ON r.noreg = rs.noreg 
					INNER JOIN kandang k ON rs.kandang = k.id
					INNER JOIN wilayah w ON k.unit = w.id
					INNER JOIN karyawan ppl ON rs.sampling = ppl.nik AND ppl.status = 1
					INNER JOIN tutup_siklus ts ON r.id_ts = ts.id
					WHERE r.jenis = 'rhpp_plasma'
					AND r.noreg NOT IN (SELECT noreg FROM rhpp_group_noreg)
					{$filter_tanggal_individual}
					{$filter_wilayah}
				),
				
				-- 2. DATA GROUP
				Data_Group AS (
					SELECT 
						COALESCE(w.alias, CAST(w.id AS VARCHAR(50))) AS kode_wilayah,
						ppl.nama AS nama_ppl_raw,
						rgn.noreg,
						rgn.populasi,
						rg.fcr,
						rg.ip,
						rg.deplesi,
						rg.bb,
						rg.rata_umur
					FROM rhpp_group rg
					INNER JOIN rhpp_group_header rgh ON rg.id_header = rgh.id
					INNER JOIN rhpp_group_noreg rgn ON rg.id = rgn.id_header
					INNER JOIN rdim_submit rs ON rgn.noreg = rs.noreg
					INNER JOIN karyawan ppl ON rs.sampling = ppl.nik AND ppl.status = 1
					INNER JOIN kandang k ON rs.kandang = k.id
					INNER JOIN wilayah w ON k.unit = w.id
					WHERE rg.jenis = 'rhpp_plasma'
					{$filter_tanggal_group}
					{$filter_wilayah}
				),
				
				-- 3. GABUNGKAN KEDUA DATA
				Gabungan_Data AS (
					SELECT * FROM Data_Individual
					UNION ALL
					SELECT * FROM Data_Group
				),
				
				-- 4. TOTAL PER KODE WILAYAH
				Total_Unit AS (
					SELECT 
						kode_wilayah,
						SUM(populasi) AS grand_total_populasi
					FROM Gabungan_Data
					GROUP BY kode_wilayah
				),
				
				-- 5. AGREGASI PER PPL PER WILAYAH (KUNCI: GROUP BY kode_wilayah + nama_ppl)
				Agregasi_PPL AS (
					SELECT 
						g.kode_wilayah,
						LTRIM(RTRIM(g.nama_ppl_raw)) AS nama_ppl, -- ✅ Normalisasi spasi
						SUM(g.populasi) AS total_populasi,
						COUNT(g.noreg) AS jumlah_peternak,
						AVG(g.fcr) AS rata_fcr,
						AVG(g.ip) AS rata_ip,
						AVG(g.deplesi) AS rata_deplesi,
						AVG(g.bb) AS rata_bb,
						AVG(g.rata_umur) AS rata_umur_panen
					FROM Gabungan_Data g
					GROUP BY g.kode_wilayah, LTRIM(RTRIM(g.nama_ppl_raw))
				)
				
				-- 6. HASIL AKHIR
				SELECT 
					(SELECT TOP 1 w2.nama FROM wilayah w2 WHERE COALESCE(w2.alias, CAST(w2.id AS VARCHAR(50))) = a.kode_wilayah) AS nama_wilayah,
					a.kode_wilayah,
					a.nama_ppl,
					a.total_populasi,
					a.jumlah_peternak,
					ROUND(a.rata_fcr, 3) AS rata_fcr,
					ROUND(a.rata_ip, 2) AS rata_ip,
					ROUND(a.rata_deplesi, 2) AS rata_deplesi,
					ROUND(a.rata_bb, 3) AS rata_bb,
					ROUND(a.rata_umur_panen, 1) AS rata_umur_panen,
					ROUND((a.total_populasi * 100.0 / NULLIF(t.grand_total_populasi, 0)), 2) AS persen_kontribusi_populasi
				FROM Agregasi_PPL a
				INNER JOIN Total_Unit t ON a.kode_wilayah = t.kode_wilayah
				ORDER BY a.kode_wilayah, a.total_populasi DESC";

		$d_conf = $m_conf->hydrateRaw($sql);
		$data = [];

		if ($d_conf && $d_conf->count() > 0) {
			$raw_data = $d_conf->toArray();
			
			$grouped_data = [];
			foreach ($raw_data as $row) {
				$wilayah_key = $row['kode_wilayah'];
				
				if (!isset($grouped_data[$wilayah_key])) {
					$grouped_data[$wilayah_key] = [
						'nama_wilayah' => $row['nama_wilayah'],
						'ppl_list' => [],
						'total_populasi_wilayah' => 0,
						'total_peternak_wilayah' => 0
					];
				}
				
				$grouped_data[$wilayah_key]['ppl_list'][] = $row;
				$grouped_data[$wilayah_key]['total_populasi_wilayah'] += $row['total_populasi'];
				$grouped_data[$wilayah_key]['total_peternak_wilayah'] += $row['jumlah_peternak'];
			}
			
			$data = array_values($grouped_data);
		}

		return $data;
	}

	// public function get_list_data_penimbang($params)
	// {
	// 	$m_conf = new \Model\Storage\Conf();

	// 	// 1. AMANKAN INPUT
	// 	$tahun = isset($params['tahun']) ? (int)$params['tahun'] : 0;
	// 	$bulan = isset($params['bulan']) ? (int)$params['bulan'] : 0;
	// 	$id_wilayah = isset($params['id_wilayah']) ? (int)$params['id_wilayah'] : 0;

	// 	// 2. LOGIKA DATE RANGE
	// 	$filter_tanggal = "";
	// 	if ($tahun > 0) {
	// 		if ($bulan > 0) {
	// 			$tgl_awal = sprintf("%04d-%02d-01", $tahun, $bulan);
	// 			$tgl_depan = date("Y-m-01", strtotime($tgl_awal . " +1 month"));
	// 			$filter_tanggal = " AND r.tgl_docin >= '{$tgl_awal}' AND r.tgl_docin < '{$tgl_depan}' ";
	// 		} else {
	// 			$tgl_awal = "{$tahun}-01-01";
	// 			$tahun_depan = $tahun + 1;
	// 			$filter_tanggal = " AND r.tgl_docin >= '{$tgl_awal}' AND r.tgl_docin < '{$tahun_depan}-01-01' ";
	// 		}
	// 	}

	// 	// 3. LOGIKA FILTER WILAYAH
	// 	$filter_wilayah = "";
	// 	if ($id_wilayah > 0) {
	// 		$filter_wilayah = " AND w.id = {$id_wilayah} ";
	// 	}

	// 	// 4. SUSUN QUERY SQL
	// 	$sql = "WITH Data_Penimbang AS (
	// 				SELECT 
	// 					w.id AS id_wilayah,
	// 					w.nama AS nama_wilayah,
	// 					penimbang.nama AS nama_penimbang,
	// 					SUM(r.populasi) AS total_populasi,
	// 					SUM(r.jml_panen_ekor) AS total_panen_ekor,
	// 					SUM(r.jml_panen_kg) AS total_panen_kg,
	// 					COUNT(DISTINCT r.noreg) AS jumlah_peternak,
	// 					AVG(r.fcr) AS rata_fcr,
	// 					AVG(r.ip) AS rata_ip,
	// 					AVG(r.deplesi) AS rata_deplesi,
	// 					AVG(r.bb) AS rata_bb,
	// 					AVG(r.rata_umur) AS rata_umur_panen
	// 				FROM rhpp r
	// 				INNER JOIN rdim_submit rs ON r.noreg = rs.noreg 
	// 				INNER JOIN kandang k ON rs.kandang = k.id
	// 				INNER JOIN wilayah w ON k.unit = w.id
	// 				INNER JOIN karyawan penimbang ON rs.tim_panen = penimbang.nik AND penimbang.status = 1
	// 				WHERE r.id_ts NOT IN (SELECT id_header FROM rhpp_group)
	// 				AND r.jenis = 'rhpp_plasma'
	// 				{$filter_tanggal}
	// 				{$filter_wilayah}
	// 				GROUP BY w.id, w.nama, penimbang.nama
	// 			),
	// 			Total_Unit AS (
	// 				SELECT 
	// 					w.id AS id_wilayah,
	// 					SUM(r.populasi) AS grand_total_populasi
	// 				FROM rhpp r
	// 				INNER JOIN rdim_submit rs ON r.noreg = rs.noreg 
	// 				INNER JOIN kandang k ON rs.kandang = k.id
	// 				INNER JOIN wilayah w ON k.unit = w.id
	// 				WHERE r.id_ts NOT IN (SELECT id_header FROM rhpp_group)
	// 				AND r.jenis = 'rhpp_plasma'
	// 				{$filter_tanggal}
	// 				{$filter_wilayah}
	// 				GROUP BY w.id
	// 			)
	// 			SELECT 
	// 				p.nama_wilayah,
	// 				p.nama_penimbang,
	// 				p.total_populasi,
	// 				p.total_panen_ekor,
	// 				p.total_panen_kg,
	// 				p.jumlah_peternak,
	// 				ROUND(p.rata_fcr, 3) AS rata_fcr,
	// 				ROUND(p.rata_ip, 2) AS rata_ip,
	// 				ROUND(p.rata_deplesi, 2) AS rata_deplesi,
	// 				ROUND(p.rata_bb, 3) AS rata_bb,
	// 				ROUND(p.rata_umur_panen, 1) AS rata_umur_panen,
	// 				ROUND((p.total_populasi * 100.0 / NULLIF(t.grand_total_populasi, 0)), 2) AS persen_kontribusi_populasi
	// 			FROM Data_Penimbang p
	// 			INNER JOIN Total_Unit t ON p.id_wilayah = t.id_wilayah
	// 			ORDER BY p.nama_wilayah, p.total_panen_ekor DESC";

	// 	// 5. EKSEKUSI QUERY
	// 	$d_conf = $m_conf->hydrateRaw($sql);
		
	// 	if ($d_conf && $d_conf->count() > 0) {
	// 		return $d_conf->toArray();
	// 	}

	// 	return [];
	// }

	public function get_list_data_penimbang($params)
	{
		$m_conf = new \Model\Storage\Conf();

		// 1. AMANKAN INPUT
		$tahun = isset($params['tahun']) ? (int)$params['tahun'] : 0;
		$bulan = isset($params['bulan']) ? (int)$params['bulan'] : 0;
		$id_wilayah = isset($params['id_wilayah']) ? (int)$params['id_wilayah'] : 0;

		// 2. LOGIKA DATE RANGE
		$filter_tanggal_individual = "";
		$filter_tanggal_group = "";
		
		if ($tahun > 0) {
			if ($bulan > 0) {
				$tgl_awal = sprintf("%04d-%02d-01", $tahun, $bulan);
				$tgl_depan = date("Y-m-01", strtotime($tgl_awal . " +1 month"));
				$filter_tanggal_individual = " AND ts.tgl_tutup >= '{$tgl_awal}' AND ts.tgl_tutup < '{$tgl_depan}' ";
				$filter_tanggal_group = " AND rgh.tgl_submit >= '{$tgl_awal}' AND rgh.tgl_submit < '{$tgl_depan}' ";
			} else {
				$tgl_awal = "{$tahun}-01-01";
				$tahun_depan = $tahun + 1;
				$filter_tanggal_individual = " AND ts.tgl_tutup >= '{$tgl_awal}' AND ts.tgl_tutup < '{$tahun_depan}-01-01' ";
				$filter_tanggal_group = " AND rgh.tgl_submit >= '{$tgl_awal}' AND rgh.tgl_submit < '{$tahun_depan}-01-01' ";
			}
		}

		// 3. LOGIKA FILTER WILAYAH
		$filter_wilayah = "";
		if ($id_wilayah > 0) {
			$filter_wilayah = " AND w.id = {$id_wilayah} ";
		}

		// 4. SUSUN QUERY SQL (FINAL FIX: PENGGABUNGAN WILAYAH & PENIMBANG)
		$sql = "WITH 
				-- 1. DATA INDIVIDUAL
				Data_Individual AS (
					SELECT 
						COALESCE(w.alias, CAST(w.id AS VARCHAR(50))) AS kode_wilayah,
						LTRIM(RTRIM(tim_penimbang.nama_penimbang)) AS nama_penimbang_raw,
						SUM(r.populasi) AS total_populasi,
						SUM(r.jml_panen_ekor) AS total_panen_ekor,
						SUM(r.jml_panen_kg) AS total_panen_kg,
						COUNT(DISTINCT r.noreg) AS jumlah_peternak
					FROM rhpp r
					INNER JOIN rdim_submit rs ON r.noreg = rs.noreg 
					INNER JOIN kandang k ON rs.kandang = k.id
					INNER JOIN wilayah w ON k.unit = w.id
					INNER JOIN tutup_siklus ts ON r.id_ts = ts.id
					INNER JOIN (
						SELECT rsj.noreg, MAX(du.nama_detuser) AS nama_penimbang
						FROM real_sj rsj
						INNER JOIN log_tables lt ON rsj.id = lt.tbl_id AND lt.tbl_name = 'real_sj' AND lt._action = 'insert'
						INNER JOIN detail_user du ON du.id_user = lt.user_id
						GROUP BY rsj.noreg
					) AS tim_penimbang ON r.noreg = tim_penimbang.noreg
					WHERE r.jenis = 'rhpp_plasma'
					AND r.noreg NOT IN (SELECT noreg FROM rhpp_group_noreg)
					{$filter_tanggal_individual}
					{$filter_wilayah}
					GROUP BY 
						COALESCE(w.alias, CAST(w.id AS VARCHAR(50))), 
						LTRIM(RTRIM(tim_penimbang.nama_penimbang))
				),
				
				-- 2. DATA GROUP
				Data_Group AS (
					SELECT 
						COALESCE(w.alias, CAST(w.id AS VARCHAR(50))) AS kode_wilayah,
						LTRIM(RTRIM(tim_penimbang.nama_penimbang)) AS nama_penimbang_raw,
						SUM(rgn.populasi) AS total_populasi,
						SUM(rg.jml_panen_ekor) AS total_panen_ekor,
						SUM(rg.jml_panen_kg) AS total_panen_kg,
						COUNT(DISTINCT rgn.noreg) AS jumlah_peternak
					FROM rhpp_group rg
					INNER JOIN rhpp_group_header rgh ON rg.id_header = rgh.id
					INNER JOIN rhpp_group_noreg rgn ON rg.id = rgn.id_header
					INNER JOIN rdim_submit rs ON rgn.noreg = rs.noreg
					INNER JOIN kandang k ON rs.kandang = k.id
					INNER JOIN wilayah w ON k.unit = w.id
					INNER JOIN (
						SELECT rsj.noreg, MAX(du.nama_detuser) AS nama_penimbang
						FROM real_sj rsj
						INNER JOIN log_tables lt ON rsj.id = lt.tbl_id AND lt.tbl_name = 'real_sj' AND lt._action = 'insert'
						INNER JOIN detail_user du ON du.id_user = lt.user_id
						GROUP BY rsj.noreg
					) AS tim_penimbang ON rgn.noreg = tim_penimbang.noreg
					WHERE rg.jenis = 'rhpp_plasma'
					{$filter_tanggal_group}
					{$filter_wilayah}
					GROUP BY 
						COALESCE(w.alias, CAST(w.id AS VARCHAR(50))), 
						LTRIM(RTRIM(tim_penimbang.nama_penimbang))
				),
				
				-- 3. GABUNGKAN KEDUA DATA
				Gabungan_Data AS (
					SELECT * FROM Data_Individual
					UNION ALL
					SELECT * FROM Data_Group
				),
				
				-- 4. TOTAL PER KODE WILAYAH
				Total_Unit AS (
					SELECT 
						kode_wilayah,
						SUM(total_panen_ekor) AS grand_total_panen_ekor
					FROM Gabungan_Data
					GROUP BY kode_wilayah
				),
				
				-- 5. AGREGASI PER PENIMBANG PER WILAYAH (KUNCI: GROUP BY kode_wilayah + nama_penimbang)
				Agregasi_Penimbang AS (
					SELECT 
						g.kode_wilayah,
						g.nama_penimbang_raw AS nama_penimbang,
						SUM(g.total_populasi) AS total_populasi,
						SUM(g.total_panen_ekor) AS total_panen_ekor,
						SUM(g.total_panen_kg) AS total_panen_kg,
						SUM(g.jumlah_peternak) AS jumlah_peternak
					FROM Gabungan_Data g
					GROUP BY g.kode_wilayah, g.nama_penimbang_raw
				)
				
				-- 6. HASIL AKHIR
				SELECT 
					(SELECT TOP 1 w2.nama FROM wilayah w2 WHERE COALESCE(w2.alias, CAST(w2.id AS VARCHAR(50))) = a.kode_wilayah) AS nama_wilayah,
					a.kode_wilayah,
					a.nama_penimbang,
					a.total_populasi,
					a.total_panen_ekor,
					a.total_panen_kg,
					a.jumlah_peternak,
					-- ✅ RUMUS WEIGHTED AVERAGE BW YANG AKURAT
					ROUND((a.total_panen_kg * 1.0 / NULLIF(a.total_panen_ekor, 0)), 3) AS rata_bb_panen,
					ROUND((a.total_panen_ekor * 100.0 / NULLIF(t.grand_total_panen_ekor, 0)), 2) AS persen_kontribusi_panen
				FROM Agregasi_Penimbang a
				INNER JOIN Total_Unit t ON a.kode_wilayah = t.kode_wilayah
				ORDER BY a.kode_wilayah, a.total_panen_ekor DESC";

		// 5. EKSEKUSI QUERY & GROUPING PHP
		$d_conf = $m_conf->hydrateRaw($sql);
		$grouped_data = [];

		if ($d_conf && $d_conf->count() > 0) {
			$raw_data = $d_conf->toArray();
			
			foreach ($raw_data as $row) {
				// ✅ GUNAKAN kode_wilayah SEBAGAI KUNCI AGAR KOTA & KAB MALANG MENYATU
				$wilayah_key = $row['kode_wilayah'];
				
				if (!isset($grouped_data[$wilayah_key])) {
					$grouped_data[$wilayah_key] = [
						'nama_wilayah' => $row['nama_wilayah'],
						'penimbang_list' => [],
						'total_panen_ekor_wilayah' => 0,
						'total_panen_kg_wilayah' => 0,
						'total_peternak_wilayah' => 0
					];
				}
				
				$grouped_data[$wilayah_key]['penimbang_list'][] = $row;
				$grouped_data[$wilayah_key]['total_panen_ekor_wilayah'] += $row['total_panen_ekor'];
				$grouped_data[$wilayah_key]['total_panen_kg_wilayah'] += $row['total_panen_kg'];
				$grouped_data[$wilayah_key]['total_peternak_wilayah'] += $row['jumlah_peternak'];
			}
			
			// Reset key array agar berurutan (0, 1, 2...)
			$grouped_data = array_values($grouped_data);
		}

		return $grouped_data;
	}


	public function export_excel()
	{
		// 1. Bersihkan semua output buffer
		if (ob_get_level()) {
			ob_end_clean();
		}
		
		// 2. Matikan error output (agar error tidak masuk ke file Excel)
		error_reporting(0);
		ini_set('display_errors', 0);

		$params = $_GET;
		$jabatan = isset($params['jabatan']) ? strtolower($params['jabatan']) : 'ppl';

		// 3. Validasi parameter
		if (!in_array($jabatan, ['ppl', 'penimbang'])) {
			die("Parameter jabatan tidak valid");
		}

		// 4. Ambil data
		try {
			if ($jabatan == 'ppl') {
				$data = $this->get_list_data_ppl($params);
				$sheetName = 'Laporan PPL';
				$headers = ['Wilayah / Nama PPL', 'Total Populasi', 'Jml Peternak', 'Rata2 FCR', 'Rata2 IP', 'Rata2 Deplesi', 'Rata2 BB', 'Rata2 Umur Panen', 'Kontribusi'];
				$lastCol = 'I';
				$listKey = 'ppl_list';
				$nameKey = 'nama_ppl';
			} else {
				$data = $this->get_list_data_penimbang($params);
				$sheetName = 'Laporan Penimbang';
				$headers = ['Wilayah / Nama Penimbang', 'Total Populasi', 'Total Panen (Ekor)', 'Total Panen (Kg)', 'Jml Peternak', 'Rata2 BW Panen', 'Kontribusi'];
				$lastCol = 'G';
				$listKey = 'penimbang_list';
				$nameKey = 'nama_penimbang';
			}
		} catch (Exception $e) {
			die("Error mengambil data: " . $e->getMessage());
		}

		// 5. Buat Spreadsheet
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle($sheetName);

		// Style definitions
		$styleHeader = [
			'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => '2C3E50']],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
			'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
		];
		
		$styleRegion = [
			'font' => ['bold' => true, 'color' => ['argb' => '2C3E50']],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'E9ECEF']],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
			'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
		];

		// Tulis Header
		$sheet->fromArray($headers, null, 'A1');
		$sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($styleHeader);
		$sheet->getRowDimension(1)->setRowHeight(25);

		// Tulis Data
		$rowNum = 2;
		if (!empty($data)) {
			// ✅ PERBAIKAN PENTING: 
			// Karena get_list_data_* sekarang mengembalikan array numerik (array_values),
			// kita loop langsung ke $wilayah_data, bukan $wilayah_name => $wilayah_data
			foreach ($data as $wilayah_data) {
				$wilayah_name = $wilayah_data['nama_wilayah'];

				// Baris Wilayah (Header Group)
				if ($jabatan == 'ppl') {
					$regionText = "📍 " . $wilayah_name . " | Total Populasi: " . number_format($wilayah_data['total_populasi_wilayah'], 0, ',', '.');
				} else {
					$regionText = "📍 " . $wilayah_name . " | Total Panen: " . number_format($wilayah_data['total_panen_ekor_wilayah'], 0, ',', '.') . " Ekor";
				}
				
				$sheet->setCellValue('A' . $rowNum, $regionText);
				$sheet->mergeCells('A' . $rowNum . ':' . $lastCol . $rowNum);
				$sheet->getStyle('A' . $rowNum . ':' . $lastCol . $rowNum)->applyFromArray($styleRegion);
				$sheet->getRowDimension($rowNum)->setRowHeight(20);
				$rowNum++;

				// Baris Data Per PPL/Penimbang
				foreach ($wilayah_data[$listKey] as $item) {
					$sheet->setCellValue('A' . $rowNum, ucwords(strtolower($item[$nameKey])));
					
					if ($jabatan == 'ppl') {
						$sheet->setCellValue('B' . $rowNum, $item['total_populasi']);
						$sheet->setCellValue('C' . $rowNum, $item['jumlah_peternak']);
						$sheet->setCellValue('D' . $rowNum, $item['rata_fcr']);
						$sheet->setCellValue('E' . $rowNum, $item['rata_ip']);
						$sheet->setCellValue('F' . $rowNum, $item['rata_deplesi']);
						$sheet->setCellValue('G' . $rowNum, $item['rata_bb']);
						$sheet->setCellValue('H' . $rowNum, $item['rata_umur_panen']);
						// Bagi 100 agar format persentase Excel bekerja (karena SQL mengembalikan 15.50, bukan 0.1550)
						$sheet->setCellValue('I' . $rowNum, $item['persen_kontribusi_populasi'] / 100);
					} else {
						$sheet->setCellValue('B' . $rowNum, $item['total_populasi']);
						$sheet->setCellValue('C' . $rowNum, $item['total_panen_ekor']);
						$sheet->setCellValue('D' . $rowNum, $item['total_panen_kg']);
						$sheet->setCellValue('E' . $rowNum, $item['jumlah_peternak']);
						$sheet->setCellValue('F' . $rowNum, $item['rata_bb_panen']);
						$sheet->setCellValue('G' . $rowNum, $item['persen_kontribusi_panen'] / 100);
					}

					// Apply border
					$range = 'A' . $rowNum . ':' . $lastCol . $rowNum;
					$sheet->getStyle($range)->applyFromArray([
						'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
					]);
					
					// Alignment & Number Format
					if ($jabatan == 'ppl') {
						$sheet->getStyle('B' . $rowNum . ':H' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
						$sheet->getStyle('I' . $rowNum)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
					} else {
						$sheet->getStyle('B' . $rowNum . ':F' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
						$sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
					}
					
					$rowNum++;
				}
			}
			
			// Auto-size columns
			foreach (range('A', $lastCol) as $col) {
				$sheet->getColumnDimension($col)->setAutoSize(true);
			}
		}

		// 6. Headers untuk download
		$filename = 'Laporan_KPI_' . ucfirst($jabatan) . '_' . date('Ymd_His') . '.xlsx';

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Pragma: public');

		// 7. Save dan output
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}


	public function show_detail_rhpp()
	{
		$params = $_POST;
		// cetak_r($params, 1);


		// $rhpp = [];
		if ($params['jabatan'] == 'ppl') {
			$rhpp = $this->get_detail_data_rhpp_ppl($params);
			$data['detail_data'] = $rhpp;
			$data['params']      = $params;
			echo $this->load->view('hris/laporan_kpi_lapangan/v_detail_rhpp_ppl', $data, true);
		} else if ($params['jabatan'] == 'penimbang'){
			$rhpp = $this->get_detail_data_rhpp_penimbang($params);
			// cetak_r($rhpp, 1);
			$data['detail_data'] = $rhpp;
			$data['params']      = $params;
			echo $this->load->view('hris/laporan_kpi_lapangan/v_detail_rhpp_penimbang', $data, true);
		}

		// cetak_r($rhpp, 1);

	}

	public function get_detail_data_rhpp_ppl($params)
	{
		$m_conf = new \Model\Storage\Conf();

		$tahun = isset($params['tahun']) ? (int)$params['tahun'] : 0;
		$bulan = isset($params['bulan']) ? (int)$params['bulan'] : 0;

		// 1. LOGIKA DATE RANGE (TERPISAH UNTUK INDIVIDUAL & GROUP)
		$filter_tanggal_individual = "";
		$filter_tanggal_group = "";
		
		if ($tahun > 0) {
			if ($bulan > 0) {
				$tgl_awal = sprintf("%04d-%02d-01", $tahun, $bulan);
				$tgl_depan = date("Y-m-01", strtotime($tgl_awal . " +1 month"));
				$filter_tanggal_individual = " AND ts.tgl_tutup >= '{$tgl_awal}' AND ts.tgl_tutup < '{$tgl_depan}' ";
				$filter_tanggal_group = " AND rgh.tgl_submit >= '{$tgl_awal}' AND rgh.tgl_submit < '{$tgl_depan}' ";
			} else {
				$tgl_awal = "{$tahun}-01-01";
				$tahun_depan = $tahun + 1;
				$filter_tanggal_individual = " AND ts.tgl_tutup >= '{$tgl_awal}' AND ts.tgl_tutup < '{$tahun_depan}-01-01' ";
				$filter_tanggal_group = " AND rgh.tgl_submit >= '{$tgl_awal}' AND rgh.tgl_submit < '{$tahun_depan}-01-01' ";
			}
		}

		// 2. AMANKAN INPUT NAMA (Escape untuk mencegah SQL injection)
		$nama_ppl = isset($params['nama']) ? addslashes($params['nama']) : '';

		$sql = "SELECT 
					'RHPP_INDIVIDUAL' AS sumber_data,
					r.noreg,
					r.populasi,
					r.jml_panen_ekor,
					r.jml_panen_kg,
					r.fcr,
					r.ip,
					r.deplesi,
					r.bb,
					r.rata_umur,
					ts.tgl_tutup AS tanggal_tutup,
					w.nama AS nama_wilayah,
					ppl.nama AS nama_ppl,
					NULL AS id_group,
					NULL AS nomor_group
				FROM rhpp r
				INNER JOIN rdim_submit rs ON r.noreg = rs.noreg 
				INNER JOIN kandang k ON rs.kandang = k.id
				INNER JOIN wilayah w ON k.unit = w.id
				INNER JOIN tutup_siklus ts ON r.id_ts = ts.id
				INNER JOIN karyawan ppl ON rs.sampling = ppl.nik AND ppl.status = 1
				WHERE r.jenis = 'rhpp_plasma'
				AND r.noreg NOT IN (SELECT noreg FROM rhpp_group_noreg) 
				AND ppl.nama = '{$nama_ppl}'
				{$filter_tanggal_individual}

				UNION ALL

				SELECT 
					'RHPP_GROUP' AS sumber_data,
					rgn.noreg,
					rgn.populasi,
					rg.jml_panen_ekor,
					rg.jml_panen_kg,
					rg.fcr,
					rg.ip,
					rg.deplesi,
					rg.bb,
					rg.rata_umur,
					rgh.tgl_submit AS tanggal_tutup,
					w.nama AS nama_wilayah,
					ppl.nama AS nama_ppl,
					rg.id AS id_group,
					rgh.nomor AS nomor_group
				FROM rhpp_group rg
				INNER JOIN rhpp_group_header rgh ON rg.id_header = rgh.id
				INNER JOIN rhpp_group_noreg rgn ON rg.id = rgn.id_header
				INNER JOIN rdim_submit rs ON rgn.noreg = rs.noreg
				INNER JOIN kandang k ON rs.kandang = k.id
				INNER JOIN wilayah w ON k.unit = w.id
				INNER JOIN karyawan ppl ON rs.sampling = ppl.nik AND ppl.status = 1
				WHERE rg.jenis = 'rhpp_plasma'
				AND ppl.nama = '{$nama_ppl}'
				{$filter_tanggal_group}

				ORDER BY sumber_data DESC, nama_wilayah, noreg ASC";

			// cetak_r($sql, 1);


		$d_conf = $m_conf->hydrateRaw($sql);
		$data = [];

		if ($d_conf && $d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}

		return $data;
	}

	public function get_detail_data_rhpp_penimbang($params)
	{
		$m_conf = new \Model\Storage\Conf();

		$tahun = isset($params['tahun']) ? (int)$params['tahun'] : 0;
		$bulan = isset($params['bulan']) ? (int)$params['bulan'] : 0;

		$filter_tanggal_individual = "";
		$filter_tanggal_group = "";
		
		if ($tahun > 0) {
			if ($bulan > 0) {
				$tgl_awal = sprintf("%04d-%02d-01", $tahun, $bulan);
				$tgl_depan = date("Y-m-01", strtotime($tgl_awal . " +1 month"));
				$filter_tanggal_individual = " AND ts.tgl_tutup >= '{$tgl_awal}' AND ts.tgl_tutup < '{$tgl_depan}' ";
				$filter_tanggal_group = " AND rgh.tgl_submit >= '{$tgl_awal}' AND rgh.tgl_submit < '{$tgl_depan}' ";
			} else {
				$tgl_awal = "{$tahun}-01-01";
				$tahun_depan = $tahun + 1;
				$filter_tanggal_individual = " AND ts.tgl_tutup >= '{$tgl_awal}' AND ts.tgl_tutup < '{$tahun_depan}-01-01' ";
				$filter_tanggal_group = " AND rgh.tgl_submit >= '{$tgl_awal}' AND rgh.tgl_submit < '{$tahun_depan}-01-01' ";
			}
		}

		// Pastikan key-nya 'nama' atau 'nama_penimbang' sesuai yang dikirim controller
		$nama_penimbang = isset($params['nama']) ? addslashes($params['nama']) : ''; 
		// Jika di controller kirimnya 'nama_penimbang', ubah jadi: isset($params['nama_penimbang'])

		$sql = "SELECT 
					'RHPP_INDIVIDUAL' AS sumber_data,
					r.noreg,
					r.populasi,
					r.jml_panen_ekor,
					r.jml_panen_kg,
					r.bb AS bw_panen,             -- ✅ TAMBAHKAN INI (Ambil dari tabel rhpp)
					r.rata_umur,
					ts.tgl_tutup AS tanggal_tutup,
					w.nama AS nama_wilayah,
					tim_penimbang.nama_penimbang,
					NULL AS id_group,
					NULL AS nomor_group
				FROM rhpp r
				INNER JOIN rdim_submit rs ON r.noreg = rs.noreg 
				INNER JOIN kandang k ON rs.kandang = k.id
				INNER JOIN wilayah w ON k.unit = w.id
				INNER JOIN tutup_siklus ts ON r.id_ts = ts.id
				INNER JOIN (
					SELECT rsj.noreg, MAX(du.nama_detuser) AS nama_penimbang
					FROM real_sj rsj
					INNER JOIN log_tables lt ON rsj.id = lt.tbl_id AND lt.tbl_name = 'real_sj' AND lt._action = 'insert'
					INNER JOIN detail_user du ON du.id_user = lt.user_id
					GROUP BY rsj.noreg
				) AS tim_penimbang ON r.noreg = tim_penimbang.noreg
				WHERE r.jenis = 'rhpp_plasma'
				AND r.noreg NOT IN (SELECT noreg FROM rhpp_group_noreg) 
				AND tim_penimbang.nama_penimbang = '{$nama_penimbang}'
				{$filter_tanggal_individual}

				UNION ALL

				SELECT 
					'RHPP_GROUP' AS sumber_data,
					rgn.noreg,
					rgn.populasi,
					rg.jml_panen_ekor,
					rg.jml_panen_kg,
					rg.bb AS bw_panen,            -- ✅ TAMBAHKAN INI (Ambil dari tabel rhpp_group)
					rg.rata_umur,
					rgh.tgl_submit AS tanggal_tutup,
					w.nama AS nama_wilayah,
					tim_penimbang.nama_penimbang,
					rg.id AS id_group,
					rgh.nomor AS nomor_group
				FROM rhpp_group rg
				INNER JOIN rhpp_group_header rgh ON rg.id_header = rgh.id
				INNER JOIN rhpp_group_noreg rgn ON rg.id = rgn.id_header
				INNER JOIN rdim_submit rs ON rgn.noreg = rs.noreg
				INNER JOIN kandang k ON rs.kandang = k.id
				INNER JOIN wilayah w ON k.unit = w.id
				INNER JOIN (
					SELECT rsj.noreg, MAX(du.nama_detuser) AS nama_penimbang
					FROM real_sj rsj
					INNER JOIN log_tables lt ON rsj.id = lt.tbl_id AND lt.tbl_name = 'real_sj' AND lt._action = 'insert'
					INNER JOIN detail_user du ON du.id_user = lt.user_id
					GROUP BY rsj.noreg
				) AS tim_penimbang ON rgn.noreg = tim_penimbang.noreg
				WHERE rg.jenis = 'rhpp_plasma'
				AND tim_penimbang.nama_penimbang = '{$nama_penimbang}'
				{$filter_tanggal_group}

				ORDER BY sumber_data DESC, nama_wilayah, noreg ASC";

		$d_conf = $m_conf->hydrateRaw($sql);
		$data = [];

		if ($d_conf && $d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}

		return $data;
	}
}




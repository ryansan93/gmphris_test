<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Karyawan extends Public_Controller
{
	private $url;

	function __construct()
	{
		parent::__construct();
		$this->url = $this->current_base_uri;
	}

	public function index()
	{
		$akses = hakAkses($this->url);
		// if ( $akses['a_view'] == 1 ) {
			$this->add_external_js(array(
				'assets/select2/js/select2.min.js',
				'assets/parameter/karyawan/js/karyawan.js'
			));
			$this->add_external_css(array(
				'assets/select2/css/select2.min.css',
				'assets/parameter/karyawan/css/karyawan.css'
			));

			$data 				= $this->includes;
			$content['akses'] 	= $akses;

			$text = '';

			if (isset($_GET['getdata'])) {
				if ($_GET['getdata'] == 1) {
					$text = ' - Tetap';
				} elseif ($_GET['getdata'] == 2) {
					$text = ' - Kontrak';
				}
			}

			$data['title_menu'] = 'Master Karyawan'. $text ;
			$data['view'] 		= $this->load->view('parameter/karyawan/index', $content, true);

			$this->load->view($this->template, $data);
		// } else {
		// 	showErrorAkses();
		// }
	}

	public function get_list()
	{
		
		// cetak_r(, 1);

		$params = $_GET['key'] ?? null;

		$data_karyawan = $this->getDataKaryawan($params);;

		if ($params == 1) {
			$nik_kandidat = $this->getKaryawanKontrak();
			$nik_kandidat = array_column($nik_kandidat, 'nik');

			$data_karyawan = array_filter($data_karyawan, function ($row) use ($nik_kandidat) {
				return !in_array($row['nik'], $nik_kandidat);
			});
			$data_karyawan = array_values($data_karyawan);
		} else if($params == 2) {
			$nik_kandidat = $this->getKaryawanKontrak();
			$nik_kandidat = array_column($nik_kandidat, 'nik');

			$data_karyawan = array_filter($data_karyawan, function ($row) use ($nik_kandidat) {
				return in_array($row['nik'], $nik_kandidat);
			});
			$data_karyawan = array_values($data_karyawan);
		}
		
		$content['data'] = $data_karyawan;

		$html = $this->load->view('parameter/karyawan/list', $content);

		echo $html;
	}

	public function getKaryawanKontrak()
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = "SELECT hdk.nik
				FROM hris_data_kandidat hdk
				WHERE hdk.nik IS NOT NULL
				AND hdk.status_kandidat <> 11 ";

		$d_conf     = $m_conf->hydrateRaw( $sql );
        
        $data       = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

		return $data;
		
	}

	public function getDataKaryawan($params = null)
	{
		$m_conf = new \Model\Storage\Conf();

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
			ORDER BY k.level ASC, ISNULL(j.nama, j_temp.nama) ASC  ";

		// $cetak_r($params, 1);

 		$d_conf     = $m_conf->hydrateRaw( $sql );
        
        $data       = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

		return $data;

	}

	public function edit_form()
	{
		$id_karyawan 			    = $this->input->get('id');
		$m_karyawan 				= new \Model\Storage\Karyawan_model();
		$d_karyawan 				= $m_karyawan->where('id', $id_karyawan)->with(['unit', 'dWilayah'])->first()->toArray();

		// cetak_r($d_karyawan, 1);
        $content['data'] 			= $d_karyawan;
        $content['list_unit'] 		= $this->get_list_unit();
        $content['list_wilayah'] 	= $this->get_list_wilayah();
        $this->load->view('parameter/karyawan/edit_form', $content);
	}

	public function get_list_unit()
	{
		$m_unit = new \Model\Storage\Wilayah_model();
		$d_unit = $m_unit->where('jenis', 'UN')->orderBy('nama')->get();

		return $d_unit;
	}

	public function get_list_wilayah()
	{
		$m_wilayah = new \Model\Storage\Wilayah_model();
		$d_wilayah = $m_wilayah->where('jenis', 'PW')->orderBy('nama')->get();

		return $d_wilayah;
	}

	public function get_atasan()
	{
		$jabatan = $this->input->post('jabatan');
		$level = getLevelJabatan($jabatan);
		$atasan = getAtasan($jabatan);

		$d_karyawan = null;
		if ( $level != 0 ) {
			$m_karyawan = new \Model\Storage\Karyawan_model();
			$d_karyawan = $m_karyawan->where('level', '<', $level)
									 ->whereIn('jabatan', $atasan)
									 ->where('status', 1)
									 ->orderBy('level', 'asc')
									 ->get();
		}

		$this->result['status'] = 1;
		$this->result['content'] = $d_karyawan;

        display_json($this->result);
	}

	public function edit()
	{
		$params = $this->input->post('params');

		try {
			$m_karyawan = new \Model\Storage\Karyawan_model();

			$m_karyawan->where('id', $params['id'])->update(
				array(
						'status' => 0
					)
			);

			$id_karyawan = $m_karyawan->getNextIdentity();

			$m_karyawan->id = $id_karyawan;
			$m_karyawan->level = $params['level'];
			$m_karyawan->nik = $params['nik'];
			$m_karyawan->atasan = $params['atasan'];
			$m_karyawan->nama = $params['nama'];
			$m_karyawan->kordinator = $params['koordinator'];
			$m_karyawan->marketing = $params['marketing'];
			$m_karyawan->jabatan = $params['jabatan'];
			$m_karyawan->status = 1;
			$m_karyawan->save();

            foreach ($params['unit'] as $k_val => $val) {
	            $m_unit_karyawan = new \Model\Storage\UnitKaryawan_model();

	            $id_unit_karyawan = $m_unit_karyawan->getNextIdentity();

				$m_unit_karyawan->id = $id_unit_karyawan;
				$m_unit_karyawan->id_karyawan = $id_karyawan;
				$m_unit_karyawan->unit = $val;
				$m_unit_karyawan->save();
            }

            foreach ($params['wilayah'] as $k_val => $val) {
	            $m_wilayah_karyawan = new \Model\Storage\WilayahKaryawan_model();

	            $id_wilayah_karyawan = $m_wilayah_karyawan->getNextIdentity();

				$m_wilayah_karyawan->id = $id_wilayah_karyawan;
				$m_wilayah_karyawan->id_karyawan = $id_karyawan;
				$m_wilayah_karyawan->wilayah = $val;
				$m_wilayah_karyawan->save();
            }

			$d_karyawan = $m_karyawan->where('id', $id_karyawan)->with(['unit'])->first();

			$deskripsi_log_karyawan = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_karyawan, $deskripsi_log_karyawan );

			$this->result['status'] = 1;
            $this->result['message'] = 'Data karyawan berhasil di update';
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = "Gagal : " . $e->getMessage();
        }

        display_json($this->result);
	}


	
}
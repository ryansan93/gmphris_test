<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisKpiMasterHeader_model extends Conf{
	public $table = 'hris_kpi_master_header';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function notifSettingKpi()
	{
		$sql = " WITH daftar_bulan AS (
					SELECT number + 1 AS periode
					FROM master..spt_values
					WHERE type = 'P'
					AND number < MONTH(GETDATE())
				),
				daftar_jabatan AS (
					SELECT
						kode AS jabatan_id,
						nama AS nama_jabatan
					FROM jabatan
					WHERE kode IN ('ppl', 'penimbang')
				)

				SELECT
					b.periode,
					CASE b.periode
						WHEN 1 THEN 'Januari'
						WHEN 2 THEN 'Februari'
						WHEN 3 THEN 'Maret'
						WHEN 4 THEN 'April'
						WHEN 5 THEN 'Mei'
						WHEN 6 THEN 'Juni'
						WHEN 7 THEN 'Juli'
						WHEN 8 THEN 'Agustus'
						WHEN 9 THEN 'September'
						WHEN 10 THEN 'Oktober'
						WHEN 11 THEN 'November'
						WHEN 12 THEN 'Desember'
					END + ' ' + CAST(YEAR(GETDATE()) AS VARCHAR(4)) AS nama_bulan,
					j.jabatan_id,
					j.nama_jabatan
				FROM daftar_bulan b
				CROSS JOIN daftar_jabatan j
				LEFT JOIN hris_kpi_master_header h
					ON h.periode = b.periode
					AND h.jabatan_id = j.jabatan_id
					AND h.status = 'ACTIVE'
					AND YEAR(h.created_date) = YEAR(GETDATE())
				WHERE h.id IS NULL
				ORDER BY b.periode, j.nama_jabatan; ";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

		$result = [];

		foreach ($data as $row) {
			$key = $row['periode'];

			if (!isset($result[$key])) {
				$result[$key] = [
					'periode'     => $row['periode'],
					'nama_bulan'  => $row['nama_bulan'],
					'keterangan'  => []
				];
			}

			$result[$key]['keterangan'][] = $row['nama_jabatan'];
		}

		foreach ($result as &$row) {
			$row['keterangan'] = implode(', ', $row['keterangan']);
		}

		$result = array_values($result);


        return $result;
	}

}


<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisKpiPenilaian_model extends Conf{
	public $table = 'hris_kpi_penilaian';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function notifKpiKaryawan()
	{
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
				WHERE hkp.status = 'DRAFT'; ";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data;
		
	}

}


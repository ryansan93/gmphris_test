<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisStatusKaryawanBaru_model extends Conf {
	protected $table = 'hris_status_karyawan_baru';
	protected $primaryKey = 'kode';

	public function notifStatusKaryawan()
	{
		$date_config = date("Y-m-d");
		// $date_config = date("Y-m-d", strtotime('2026-08-27'));

		$sql = " SELECT hskb.*, k.nama FROM hris_status_karyawan_baru hskb
				inner join karyawan k on hskb.nik = k.nik and k.status = 1 
				WHERE CAST('".$date_config."' AS DATE) BETWEEN
				DATEADD(DAY, -7, CAST(tgl_selesai AS DATE))
				AND DATEADD(DAY, -1, CAST(tgl_selesai AS DATE)) and hskb.status = 1 ";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

		// cetak_r($sql, 1);

        return $data;
	}

}
<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisUsulanMutasi_model extends Conf {
	protected $table = 'hris_usulan_mutasi';
	protected $primaryKey = 'kode';

	public function notifUsulan($need)
	{

		$sql = " select * from hris_usulan_mutasi where status = ". $need['status'] ." and jenis = '". $need['jenis'] ."' ";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

		// cetak_r($data, 1);

        return $data;
	}


	public function getHistoryKaryawan($kode)
	{
		$sql = "
			SELECT 
				hum.kode,
				hum.jenis,
				hum.karyawan,
				hum.jabatan_asal,
				kha.tgl_selesai AS tgl_selesai_jabatan_asal,
				hum.jabatan_tujuan,
				kht.tgl_mulai AS tgl_mulai_jabatan_tujuan
			FROM hris_usulan_mutasi hum
			LEFT JOIN karyawan_history kha
				ON kha.nik = hum.karyawan
				AND kha.jabatan = hum.jabatan_asal
			LEFT JOIN karyawan_history kht
				ON kht.nik = hum.karyawan
				AND kht.jabatan = hum.jabatan_tujuan
			WHERE hum.kode = '" . $kode . "'
		";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data[0];
	}
}
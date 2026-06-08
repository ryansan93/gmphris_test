<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisUsulanMutasi_model extends Conf {
	protected $table = 'hris_usulan_mutasi';
	protected $primaryKey = 'kode';

	public function notifUsulan($need)
	{

		$sql = " select hum.*, k.nama as nama_karyawan, j.nama as nama_jabatan from hris_usulan_mutasi hum
		inner join karyawan k on k.nik = hum.karyawan and k.status = 1
		inner join jabatan j on j.kode = hum.jabatan_asal
		where hum.status = ". $need['status'] ." and hum.jenis = '". $need['jenis'] ."' ";

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
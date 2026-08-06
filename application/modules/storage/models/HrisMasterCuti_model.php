<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisMasterCuti_model extends Conf{
    public $table = 'hris_master_cuti';
    protected $primaryKey = 'id';
    public $timestamps = false;


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

		$d_conf = $this->hydrateRaw($sql);

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

}



<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisDataKandidat_model extends Conf{
	
	public $table = 'hris_data_kandidat';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function notifData()
	{
		$sql = "select * from hris_data_kandidat where status_kandidat = 1";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data;
	}

	public function notifDataUsulanKaryawanBaru()
	{
		$sql = " select * from hris_usulan_karyawan_baru where status in (1,2) ";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data;
	}
}

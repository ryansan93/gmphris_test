<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisUsulanMutasi_model extends Conf {
	protected $table = 'hris_usulan_mutasi';
	protected $primaryKey = 'kode';

	public function notifData()
	{
		$sql = " select * from hris_usulan_mutasi where status in (1,2) ";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data;
	}
}
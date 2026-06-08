<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisDataKandidat_model extends Conf{
	
	public $table = 'hris_data_kandidat';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function notifData()
	{
		$sql = "select * from hris_data_kandidat where status_kandidat = 1 and tgl_selesai_isi is null ";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data;
	}

	public function notifAckDataUsulanKaryawanBaru()
	{
		$sql = " select hukb.*, k.nama as nama_pengusul, j.nama as nama_jabatan from hris_usulan_karyawan_baru hukb
		left join karyawan k on k.nik = hukb.nama_pengusul and k.status = 1
		left join jabatan j on k.jabatan = j.kode
		where hukb.status in (1)  ";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data;
	}


	public function notifDataKandidatForm()
	{
		$sql 	= " select * from hris_data_kandidat hdk WHERE  hdk.status_kandidat = 1 and hdk.document is not null ";
		
		$d_dpd 	= $this->hydrateRaw($sql);

        $data 	= null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data;
	}

	public function notifApprovekDataUsulanKaryawanBaru()
	{
		$sql = " select hukb.*, k.nama as nama_pengusul, j.nama as nama_jabatan from hris_usulan_karyawan_baru hukb
		left join karyawan k on k.nik = hukb.nama_pengusul and k.status = 1
		left join jabatan j on k.jabatan = j.kode
		where hukb.status in (2) ";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data;
	}
}

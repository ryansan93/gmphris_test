<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisAttachmentClearance_model extends Conf{
    public $table = 'hris_document_clearance';
    protected $primaryKey = 'id';
    public $timestamps = false;  

    public function getClearanceById($usulan_id)
    {
        $sql = "
            select *
            from hris_document_clearance
            where usulan_id = '".$usulan_id."'
            order by id asc
        ";

        // cetak_r($sql, 1);

        $db = $this->hydrateRaw($sql);

        $data = null;
        if ($db->count() > 0) {
            $data = $db->toArray();
        }

        return $data;
    }

    public function getNotifVerificationClearance()
    {
        $sql = " select hur.id, hur.document, hur.nik, k.nama as nama_karyawan, j.nama as nama_jabatan, hur.verification_clearance_date
            from hris_usulan_resign hur
            inner join karyawan k on hur.nik = k.nik and k.status = 1
            inner join jabatan j on k.jabatan = j.kode
            where hur.clearance_date is not null -- and verification_clearance_date is null
        ";

        // cetak_r($sql, 1);

        $db = $this->hydrateRaw($sql);

        $data = null;
        if ($db->count() > 0) {
            $data = $db->toArray();
        }

        return $data;
    }

    public function getDataClearance($data = null)
    {
        $sql = " select hur.id, hur.document, hur.nik, k.nama as nama_karyawan, j.nama as nama_jabatan,
            hur.verification_clearance_date
            from hris_usulan_resign hur
            inner join karyawan k 
            ON k.nik = hur.nik
            AND k.id = (
                SELECT MAX(k2.id)
                FROM karyawan k2
                WHERE k2.nik = hur.nik
            )
            inner join jabatan j on k.jabatan = j.kode
            where hur.clearance_date is not null and hur.id = '".$data."' ";



        // cetak_r($data, 1);

        $db = $this->hydrateRaw($sql);

        $data = null;
        if ($db->count() > 0) {
            $data = $db->toArray();
        }

        return $data;
    }



    public function getFilterData()
    {
        $sql = " select hur.id, hur.document, hur.nik,
        k.nama as nama_karyawan,
        j.nama as nama_jabatan
        from hris_usulan_resign  hur
        inner join karyawan k 
            ON k.nik = hur.nik
            AND k.id = (
                SELECT MAX(k2.id)
                FROM karyawan k2
                WHERE k2.nik = hur.nik
            )
        inner join jabatan j on k.jabatan = j.kode
        where hur.clearance_date is not null";

        // cetak_r($sql, 1);
        $db = $this->hydrateRaw($sql);

        $data = null;
        if ($db->count() > 0) {
            $data = $db->toArray();
        }

        return $data;
    }

    


}



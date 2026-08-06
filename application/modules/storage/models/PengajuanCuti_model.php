<?php
    namespace Model\Storage;
    use \Model\Storage\Conf as Conf;

    class PengajuanCuti_model extends Conf{
        public $table = 'hris_pengajuan_cuti';
        protected $primaryKey = 'id';
        public $timestamps = false;

        public function notifAckPengajuanCuti($nik_atasan)
        {
            $sql    = "select hpc.id, hpc.nik, k.nama as nama_karyawan, j.nama as nama_jabatan from hris_pengajuan_cuti hpc
            inner join karyawan k on hpc.nik = k.nik and k.status = 1
            inner join jabatan j on k.jabatan = j.kode
            where k.atasan_nik = '".$nik_atasan."' and hpc.status_pengajuan =1 " ;

            // cetak_r($sql, 1);
    
            $db     = $this->hydrateRaw($sql)->toArray();
            return $db;
    
        }


        public function notifApprovePengajuanCuti()
        {
            $sql    = "select hpc.id, hpc.nik, k.nama as nama_karyawan, j.nama as nama_jabatan from hris_pengajuan_cuti hpc
            inner join karyawan k on hpc.nik = k.nik and k.status = 1
            inner join jabatan j on k.jabatan = j.kode
            where hpc.status_pengajuan =2 " ;

            // cetak_r($sql, 1);
    
            $db     = $this->hydrateRaw($sql)->toArray();
            return $db;
    
        }
    }



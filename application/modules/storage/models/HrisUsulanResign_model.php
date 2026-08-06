<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisUsulanResign_model extends Conf{
    public $table = 'hris_usulan_resign';
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


    public function getAllData($id = null)
    {
        $nik_login = $this->cek_nik();

        $sql = " 
            select 
                hur.id, 
                hur.document, 
                hur.jenis_resign, 
                hur.nik, 
                hur.alasan_resign, 
                hur.tanggal_pengajuan, 
                hur.tanggal_resign, 
                hur.status,
                hur.keterangan_reject,
                hur.clearance_date,
                hur.verification_clearance_date,
                k.nama as nama_karyawan, 
                j.nama as nama_jabatan
            from hris_usulan_resign hur
            inner join karyawan k 
                on hur.nik = k.nik 
                and k.status = 1
            inner join jabatan j 
                on k.jabatan = j.kode
        ";

        $where = [];

        if ($nik_login) {
            $where[] = "(hur.nik = '".$nik_login."' OR k.atasan_nik = '".$nik_login."')";
        }

        if ($id) {
            $where[] = "hur.id = '".$id."'";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY hur.id DESC ";

        // cetak_r($sql, 1);

        $d_dpd = $this->hydrateRaw($sql);

        if ($d_dpd->count() > 0) {
            return $d_dpd->toArray();
        }

        return [];
    }

    public function getDataAttachment($id)
    {
        $sql = " select id, usulan_id, file_attachment, nama_file, file_path from hris_attachment_resign ";

        if ($id){
            $sql .= " where usulan_id = '".$id."' ";
        }

		// cetak_r($sql, 1);


        $d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

		// cetak_r($data, 1);

        return $data;
    }


    public function getDataUsulanResign( $nik = null)
    {
        $sql = " SELECT 
            hur.id, 
            hur.document, 
            hur.jenis_resign, 
            hur.nik, 
            hur.alasan_resign, 
            hur.tanggal_pengajuan, 
            hur.tanggal_resign, 
            hur.status,
            hur.keterangan_reject,
            hur.ack_by,
            hur.approved_by,
            k.nama AS nama_karyawan,
            j.nama AS nama_jabatan
        FROM hris_usulan_resign hur
        INNER JOIN karyawan k 
            ON k.nik = hur.nik
            AND k.id = (
                SELECT MAX(k2.id)
                FROM karyawan k2
                WHERE k2.nik = hur.nik
            )
        INNER JOIN jabatan j 
            ON k.jabatan = j.kode ";

        if ($nik){
            $sql .= " where k.atasan_nik = '". $nik ."' ";
        }

        $db = $this->hydrateRaw($sql);

        $data = null;
        if ($db->count() > 0) {
            $data = $db->toArray();
        }

		// cetak_r($data, 1);

        return $data;
    }

    public function getNotifUsulan($need = null)
    {
        $sql = " select hur.id, hur.document, hur.jenis_resign, hur.nik, hur.alasan_resign, hur.tanggal_pengajuan, hur.tanggal_resign, hur.status,
                k.nama as nama_karyawan, j.nama as nama_jabatan, hur.jenis_resign
                from hris_usulan_resign hur
                inner join karyawan k on hur.nik = k.nik and k.status = 1 
                inner join jabatan j on k.jabatan = j.kode ";

        if (!empty($need) && isset($need['jenis']) && $need['jenis'] == 'NOTIF_ACK') {
            $sql .= " where hur.status = '1' AND k.atasan_nik = '".$need['nik']."'";
        }

        if (!empty($need) && isset($need['jenis']) && $need['jenis'] == 'NOTIF_APPROVE') {
            $sql .= " where hur.status = '2' ";
        }

        if (!empty($need) && isset($need['jenis']) && $need['jenis'] == 'NOTIF_CLEARANCE') {
            $sql .= " where hur.status = '3' AND hur.nik = '".$need['nik']."'";
        }

        
		// cetak_r($sql, 1);


        $db = $this->hydrateRaw($sql);

        $data = null;
        if ($db->count() > 0) {
            $data = $db->toArray();
        }


        return $data;
    }


    public function getIdUser($nik)
    {
        $sql = " SELECT 
                du.id_user,
                k.nik
            FROM detail_user du
            INNER JOIN karyawan k
                ON du.nama_detuser = k.nama
                AND k.status = 1
            WHERE  du.nonaktif_detuser IS NULL and k.nik = '" . $nik . "' ";

        $db = $this->hydrateRaw($sql);

        $data = null;
        if ($db->count() > 0) {
            $data = $db->toArray();
        }

        return $data[0]['id_user'];
    }

    public function get_report_resign()
    {
        $sql = "SELECT * FROM hris_usulan_karyawan_baru hukb 
        INNER JOIN (
            SELECT *
            FROM karyawan
            WHERE id IN (
                SELECT MAX(id)
                FROM karyawan
                GROUP BY nik
            )
        ) k ON hukb.nama_pengusul = k.nik
        WHERE hukb.status = 1 ";

        $d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data;
    }


    public function getNotifUserActivated(){

        $sql = "SELECT
            hur.id,
            hur.document,
            hur.nik,
            hur.tanggal_resign,
            k.nama AS nama_karyawan,
            j.nama AS nama_jabatan
        FROM hris_usulan_resign hur
        INNER JOIN karyawan k
            ON k.nik = hur.nik
            AND k.id = (
                SELECT MAX(k2.id)
                FROM karyawan k2
                WHERE k2.nik = hur.nik
            )
        INNER JOIN jabatan j
            ON k.jabatan = j.kode
        WHERE hur.clearance_date IS NOT NULL
          AND hur.nonactive_user_date IS NULL
          and hur.verification_clearance_date IS NOT NULL
           -- AND CAST(GETDATE() AS DATE) >= DATEADD(DAY, -2, CAST(hur.tanggal_resign AS DATE))
           ";

        $db = $this->hydrateRaw($sql);

        $data = null;
        if ($db->count() > 0) {
            $data = $db->toArray();
        }

        return $data;
    }
}



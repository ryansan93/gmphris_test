<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class StrukturOrganisasi_model extends Conf {
	// public $incrementing = false;
	public $timestamps = false;

    public function data_struktur_organisasi($data = null)
    {
        $sql = " SELECT
                    j.level,
                    k.nik,
                    k.nama,
                    k.atasan_nik,
                    j.nama AS nama_jabatan,
                    u.nama_unit,
                    u.nama_wilayah
                FROM karyawan k
                INNER JOIN jabatan j
                    ON k.jabatan = j.kode
                OUTER APPLY
                (
                    SELECT
                        nama_unit = STUFF
                        (
                            (
                                SELECT ', ' +
                                    CASE 
                                        WHEN x.is_all = 1 THEN 'All'
                                        ELSE MAX(x.nama)
                                    END
                                FROM
                                (
                                    SELECT 
                                        uk2.unit,
                                        CASE 
                                            WHEN uk2.unit = 'all' THEN 1 
                                            ELSE 0 
                                        END AS is_all,
                                        w.kode,
                                        w.nama
                                    FROM unit_karyawan uk2
                                    LEFT JOIN wilayah w
                                        ON TRY_CAST(uk2.unit AS INT) = w.id
                                    WHERE uk2.id_karyawan = k.id
                                ) x
                                GROUP BY 
                                    x.is_all,
                                    x.kode
                                FOR XML PATH('')
                            ),
                            1,2,''
                        ),
                        nama_wilayah = STUFF
                        (
                            (
                                SELECT ', ' +
                                    CASE
                                        WHEN wk2.wilayah = 'all' THEN 'All'
                                        ELSE w.nama
                                    END
                                FROM wilayah_karyawan wk2
                                LEFT JOIN wilayah w
                                    ON TRY_CAST(wk2.wilayah AS INT) = w.id
                                WHERE wk2.id_karyawan = k.id
                                FOR XML PATH('')
                            ),
                            1,2,''
                        )
                ) u
                where k.status = 1";
            
                if(isset($data['level_max']) && $data['level_max']){
                    $sql .= " and j.level <= '".$data['level_max']."' ";
                } else {
                    $sql .= " and j.level <=7 ";
                }

                if(isset($data['wilayah']) && $data['wilayah']){
                    $wilayah        = $data['wilayah'];
                    $arrWilayah     = array_map('trim', explode(',', $wilayah));
                    $filterWilayah  = [];

                    foreach($arrWilayah as $wil){
                        $filterWilayah[] = "u.nama_wilayah LIKE '%" . $wil . "%'";
                    }

                    $sql .= " AND (" . implode(" OR ", $filterWilayah) . ")";
                }

                if(isset($data['unit']) && $data['unit']){
                    $sql .= " AND nama_unit like '%" . $data['unit'] . "%' ";
                }

                $sql .= " order by k.level asc";

                // cetak_r($sql, 1);

        $db = $this->hydrateRaw($sql)->toArray();

        return $db;
    }

    public function get_unit()
    {
        $sql = "SELECT 
                    w1.kode,
                    MAX(w1.nama) AS nama_unit
                FROM wilayah w1
                WHERE w1.kode IS NOT NULL
                GROUP BY w1.kode
                ORDER BY w1.kode;";

        $db = $this->hydrateRaw($sql)->toArray();

        return $db;
    }

    public function get_perwakilan()
    {
        $sql = "select nama as nama_wilayah from wilayah where jenis = 'PW' order by nama asc";

        $db = $this->hydrateRaw($sql)->toArray();

        return $db;
        
    }

}
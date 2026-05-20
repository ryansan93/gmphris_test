<style>
    *{
        font-size:9px;
    }

    .table tr th,td {
        border:1px solid black;
        height:25px; 
    }
</style>

<h3>REPORT HISTORY KARYAWAN</h3><br>
<span>PT. Girya Mitra Poultry</span><br>
<span><?php echo $periode; ?></span>
<br><br><br>


<!-- < ?php cetak_r($data, 1); ?> -->

<table class="table">
    <thead>
        <tr>
            <th style="font-size:10px; font-weight:bold; line-height:20px; height:30px; text-align:center; width:100px;">No. Document</th>
            <th style="font-size:10px; font-weight:bold; line-height:20px; height:30px; text-align:center; width:60px;">Tgl Usulan</th>
            <th style="font-size:10px; font-weight:bold; line-height:20px; height:30px; text-align:center; width:40px;">Jenis</th>
            <th style="font-size:10px; font-weight:bold; line-height:20px; height:30px; text-align:center; width:90px;">Pengusul</th>
            <th style="font-size:10px; font-weight:bold; line-height:20px; height:30px; text-align:center; width:90px;">Karyawan</th>
            <th style="font-size:10px; font-weight:bold; line-height:20px; height:30px; text-align:center; width:140px;">Jabatan Asal</th>
            <th style="font-size:10px; font-weight:bold; line-height:20px; height:30px; text-align:center; width:140px;">Jabatan Tujuan</th>
            <th style="font-size:10px; font-weight:bold; line-height:20px; height:30px; text-align:center; width:130px;">Periode</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($data)) { ?>
           
            <?php foreach($data as $l){?>

                <tr class="data-row">
                    <td style="text-align:center; width:100px;"><?php echo $l['kode'] ?></td>
                    <td style="text-align:center; width:60px;"><?php echo tglIndonesia($l['tanggal'], "-" , " ") ?></td>
                    <td style="text-align:center; width:40px;"><?php echo ucwords(strtolower($l['jenis'])) ?></td>
                    <td style="width:90px;"><?php echo ucwords(strtolower($l['nama_pengusul'])) ?></td>
                    <td style="width:90px;"><?php echo ucwords(strtolower($l['nama_karyawan'])) ?></td>
                    <td style="width:140px;">
                        <?php echo $l['nama_jabatan_asal']?><br>
                        Perwakilan : <?php echo $l['nama_perwakilan_asal']?> <br>
                        Unit : <?php echo $l['nama_unit_asal']?> 
                    </td>
                    <td style="width:140px;">
                        <?php echo $l['nama_jabatan_tujuan']?><br>
                        Perwakilan : <?php echo $l['nama_perwakilan_tujuan']?> <br>
                        Unit : <?php echo $l['nama_unit_tujuan']?> 
                    </td>
                    <td style="width:130px; text-align:left;">
                        Tgl Mulai : <?php echo tglIndonesia($l['tgl_mulai'], "-" , " ") ?><br>
                        Tgl Selesai : <?php echo !empty($l['tgl_selesai']) ? tglIndonesia($l['tgl_selesai'], "-", " ") : '-' ?>
                    </td>
                    
                </tr>
            <?php } ?>
      
        <?php } else { ?>

            <tr>
                <td colspan="9" style="text-align:center;">Tidak ada data</td>
            </tr>
        <?php } ?>

    </tbody>
</table>
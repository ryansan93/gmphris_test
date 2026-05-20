<style>
    *{
        font-size:12px;
    }

    .table tr th,td {
        border:1px solid black;
        font-size:10px;
    }
</style>

<h3>REPORT PROBATION KARYAWAN</h3>
<span>PT. Griya Mitra Poultry</span>
<br><br><br>


<table class="table">
    <thead>
        <tr>
            <th style="height:30px; line-height:20px; text-align:center; width:150px;">Karyawan</th>
            <th style="height:30px; line-height:20px; text-align:center; width:120px;">Status</th>
            <th style="height:30px; line-height:20px; text-align:center;">Keterangan</th>
            <th style="height:30px; line-height:20px; text-align:center; width: 80px;">Tgl. Awal</th>
            <th style="height:30px; line-height:20px; text-align:center; width: 80px;">Tgl. Selesai</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($data)) { ?>
           
            <?php foreach($data as $l){?>

                <tr class="data-row">
                    <td style="text-align:left; width:150px;"><?php echo $l['nama'] ?></td>
                    <td style="text-align:center; width:120px;"><?php echo $l['nama_kategori'] ?></td>
                    <td style="text-align:center;"><?php echo $l['keterangan'] ?></td>
                    <td style="text-align:center; width: 80px;"><?php echo tglIndonesia($l['tgl_berlaku'], "-" , " ") ?></td>
                    <td style="text-align:center; width: 80px;"><?php echo tglIndonesia($l['tgl_selesai'], "-" , " ") ?></td>
                </tr>
            <?php } ?>
      
        <?php } else { ?>

            <tr>
                <td colspan="5" style="text-align:center;">Tidak ada data</td>
            </tr>
        <?php } ?>

    </tbody>
</table>
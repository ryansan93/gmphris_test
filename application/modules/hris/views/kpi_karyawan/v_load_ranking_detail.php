<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Data Karyawan</b>
        </div>
    </legend>

    Nama Karyawan : <?php echo $data_header['nama_karyawan']?>
</fieldset>

<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center">Nama Index</th>
            <th class="text-center">Bobot</th>
            <th class="text-center">Nilai</th>
            <th class="text-center">Score</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($data_ranking)) { ?>
            <?php $total = 0; ?>
            <?php foreach ($data_ranking as $row) { ?>

                <?php $total += $row['skor']; ?>

                <tr>
                    <td class="text-left"><?php echo $row['nama_kpi'] ?></td>
                    <td class="text-right"><?= rtrim(rtrim(number_format($row['bobot'], 2), '0'), '.'); ?>%</td>
                    <td class="text-right"><?php echo $row['nilai'] ?></td>
                    <td class="text-right"><?php echo $row['skor'] ?></td>
                </tr>
                
            <?php } ?>
            <tr>
                <td colspan="3" class="text-center"> Total Score </td>
                <td class="text-right"> <b><?php echo number_format($total, 2); ?></b> </td>
            </tr>
        <?php } else { ?>
            <tr>
                <td colspan="3" class="text-center">
                    Tidak ada data
                </td>
            </tr>
        <?php } ?>
</table>
<?php if (!empty($data_ranking)) { ?>
    <?php foreach ($data_ranking as $jabatan => $rows) { ?>
        <tr>
            <td colspan="3" style="font-weight:bold;background:#f2f2f2;">
                <?php echo $jabatan; ?>
            </td>
        </tr>
        <?php foreach ($rows as $i => $row) { ?>
            <tr>
                <td class="text-center"><?php echo $row['nik']; ?></td>
                <td><?php echo ucwords(strtolower($row['nama'])); ?></td>
                <td class="text-center"><a href="#" nik="<?php echo $row['nik']; ?>" total_score="<?php echo number_format($row['score'], 2); ?>" onclick="kpi.view_detail_nilai(this, event)"><?php echo number_format($row['score'], 2); ?></a></td>
            </tr>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <tr>
        <td colspan="3" class="text-center">
            Tidak ada data
        </td>
    </tr>
<?php } ?>
<?php if (!empty($laporan)) { ?>
    <?php foreach ($laporan as $periode => $items) { ?>
        <tr>
            <td colspan="4" style="font-weight:bold;background:#f5f5f5;">
                <?php echo $periode; ?>
            </td>
        </tr>

        <?php foreach ($items as $l) { ?>
            <tr>
                <td><?php echo $l['nik']; ?></td>
                <td><?php echo ucwords(strtolower($l['nama'])); ?></td>
                <td><?php echo $l['nama_jabatan']; ?></td>
                <td class="text-right"><?php echo $l['total_nilai']; ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <tr>
        <td colspan="4" class="text-center">
            <i>Tidak ada data</i>
        </td>
    </tr>
<?php } ?>
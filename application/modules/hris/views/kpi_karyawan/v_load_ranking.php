<?php if (!empty($data_ranking)) : ?>

    <?php foreach ($data_ranking as $jabatan => $rows) : ?>

        <tr>
            <td colspan="3"
                style="font-weight:bold;background:#f2f2f2;">
                <?= $jabatan ?>
            </td>
        </tr>

        <?php foreach ($rows as $i => $row) : ?>

            <tr>
                <td><?=  ucwords(strtolower($row['nama'])); ?></td>
                <td class="text-center">
                    <?= ucwords(strtolower($row['nama_jabatan'])); ?>
                </td>
                <td class="text-center">
                    <?= number_format($row['score'], 2); ?>
                </td>
            </tr>

        <?php endforeach; ?>

    <?php endforeach; ?>

<?php else : ?>

<tr>
    <td colspan="3" class="text-center">
        Tidak ada data
    </td>
</tr>

<?php endif; ?>
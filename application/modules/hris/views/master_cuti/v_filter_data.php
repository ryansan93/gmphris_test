
<?php foreach($data_cuti as $tahun => $cuti) { ?>

    <tr>
        <td colspan="4">
            <strong>Tahun : <?= $tahun ?></strong>
        </td>
    </tr>

    <?php foreach($cuti as $d) { ?>
        <tr>
            <td><?= $d['nik'] ?></td>
            <td><?= $d['nama'] ?></td>
            <td style="text-align:center;"><?= (int)$d['hak_cuti'] ?> Hari</td>
            <td style="text-align:center;"><?= (int)$d['cuti_terpakai'] ?> Hari</td>
            <td style="text-align:center;"><?= (int)$d['sisa_cuti'] ?> Hari</td>
            <td style="text-align:center;">
                <button type="button" 
                    class="btn btn-warning btn-sm"
                    onclick="mc.edit_cuti(
                        '<?= $d['id'] ?>',
                        '<?= $d['nik'] ?>',
                        '<?= $d['nama'] ?>',
                        '<?= $d['hak_cuti'] ?>',
                        '<?= $d['cuti_terpakai'] ?>',
                        '<?= $d['sisa_cuti'] ?>'
                    )">
                    <i class="fa fa-edit"></i>
                </button>

                <button type="button" 
                    class="btn btn-danger btn-sm"
                    onclick="mc.delete_cuti(<?= $d['id'] ?>)">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    <?php } ?>

<?php } ?>

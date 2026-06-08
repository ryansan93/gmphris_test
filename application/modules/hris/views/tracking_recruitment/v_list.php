<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center">Document</th>
            <th class="text-center">Pengusul</th>
            <th class="text-center">Posisi</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>

        <?php foreach($list as $l){?>

        <tr class="data-row">
            <td class="text-center"><?php echo $l['document'] ?></td>
            <td><?php echo $l['nama_pengusul'] ?></td>
            <td><?php echo $l['nama_jabatan'] ?></td>
            <td class="text-center">
                <button document="<?php echo $l['document'] ?>" class="btn btn-primary"
                    onclick="tr.show_tracking(this, event)"><i style="margin-right:5px;" class="fa fa-file"
                        aria-hidden="true"></i> Lihat Tracking</button>
            </td>
        </tr>
        <?php } ?>

        <?php } else { ?>

        <tr>
            <td colspan="6" style="text-align:center;">Tidak ada data</td>
        </tr>

        <?php } ?>
    </tbody>
</table>
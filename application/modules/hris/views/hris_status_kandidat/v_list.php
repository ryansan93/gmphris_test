<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center">Nama Status</th>
            <th class="text-center">Nama Kategori</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
            <?php foreach($list as $l){?>
                <tr>
                    <td class="text-center"><?php echo $l['nama_status'] ?></td>
                    <td class="text-left"><?php echo $l['nama_kategori'] ?></td>
                    <td class="text-center">

                        <?php if ($akses['a_edit'] == 1) { ?>
                            <button id_data="<?php echo $l['id_data'] ?>" class="btn btn-warning" onclick="hf.edit(this, event)"><i style="margin-right:5px;" class="fa fa-edit" aria-hidden="true"></i> Edit</button>
                        <?php } else if($akses['a_delete'] == 1) { ?>
                            <button id_data="<?php echo $l['id_data'] ?>" class="btn btn-danger" onclick="hf.delete(this, event)"><i style="margin-right:5px;" class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                        <?php } else { ?>
                        -
                        <?php } ?>

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
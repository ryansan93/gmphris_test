<div style="overflow-y:scroll; height:350px;">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width:20%;" class="text-center">Kode Jabatan</th>
                <th style="width:40%;" class="text-center">Nama Jabatan</th>
                <th style="width:15%;" class="text-center">Level</th>
                <th style="width:25%;" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($list)) { ?>
                <?php foreach($list as $l){?>
                    <tr>
                        <td class="text-left kode_jabatan"><?php echo $l['kode'] ?></td>
                        <td class="text-left nama_jabatan"><?php echo $l['nama'] ?></td>
                        <td class="text-center level"><?php echo $l['level'] ?></td>
                        <td class="text-center">
                            
                            <?php if ($akses['a_edit'] == 1) { ?>
                                <button kode_jabatan="<?php echo $l['kode'] ?>" class="btn btn-warning" onclick="mj.edit(this, event)">
                                    <i style="margin-right:5px;" class="fa fa-edit" aria-hidden="true"></i>
                                    Edit
                                </button>
                            <?php } ?>
    
                            <?php if ($akses['a_delete'] == 1) { ?>
                                <button kode_jabatan="<?php echo $l['kode'] ?>" class="btn btn-danger" onclick="mj.delete(this, event)">
                                    <i style="margin-right:5px;" class="fa fa-trash" aria-hidden="true"></i>
                                    Delete
                                </button>
                            <?php } ?>
    
                            <?php if ($akses['a_edit'] != 1 && $akses['a_delete'] != 1) { ?>
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
</div>
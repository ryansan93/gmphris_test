<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center">Nama KPI</th>
            <th class="text-center">Keterangan</th>
            <th class="text-center">Bobot</th>
            <th class="text-center">Jabatan</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($list_setting as $ls) { ?>
        <tr>
            <td><?php echo $ls['nama_template']?></td>
            <td><?php echo $ls['keterangan']?></td>
            <td class="text-right"><?php echo $ls['total_bobot']?>%</td>
            <td><?php echo $ls['nama_jabatan']?></td>
            <td class="text-center">
                <button id_data="<?php echo $ls['id']?>" onclick="kpi.setting_edit(this, event)" class="btn btn-secondary"><i class="fa fa-edit"></i></button>
                <button id_data="<?php echo $ls['id']?>" onclick="kpi.setting_delete(this, event)" class="btn btn-danger"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
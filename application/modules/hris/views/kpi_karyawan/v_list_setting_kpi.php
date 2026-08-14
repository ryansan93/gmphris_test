<style>
    .gmp-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
    }

    .gmp-table thead th {
        background: #f0f4f8;
        color: #5b6b7c;
        text-transform: uppercase;
        font-size: .72rem;
        letter-spacing: .5px;
        font-weight: 700;
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .gmp-table tbody td {
        padding: 16px;
        border-bottom: 1px solid #eef2f6;
        font-size: .9rem;
        color: #334155;
        vertical-align: middle;
    }

    .gmp-table tbody tr { transition: background .15s; }
    .gmp-table tbody tr:hover td { background: #fafcfe; }
    .gmp-table tbody tr:last-child td { border-bottom: none; }

    .gmp-table .text-center, .gmp-table th.text-center { text-align: center; }
    .gmp-table .text-right,  .gmp-table td.text-right  { text-align: right; }

    .gmp-table-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        overflow-x: auto;
    }

    .gmp-table thead th + th { border-left: 1px solid #e2e8f0; }
    .gmp-table tbody td + td { border-left: 1px solid #eef2f6; }

</style>

<table class="gmp-table">
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
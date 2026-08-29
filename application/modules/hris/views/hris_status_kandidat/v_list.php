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
        padding: 10px;
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

    /* Garis pemisah antar kolom */
    .gmp-table thead th + th { border-left: 1px solid #e2e8f0; }
    .gmp-table tbody td + td { border-left: 1px solid #eef2f6; }
</style>

<table class="gmp-table">
    <thead>
        <tr>
            <th class="text-left">Nama Status</th>
            <th class="text-left">Nama Kategori</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>

            <?php foreach($list as $l){?>
                <tr>
                    <td class="text-left"><?php echo $l['nama_status'] ?></td>
                    <td class="text-left"><?php echo $l['nama_kategori'] ?></td>
                    <td class="text-center">

                        <?php if ($akses['a_edit'] == 1) { ?>
                            <button id_data="<?php echo $l['id_data'] ?>" 
                                    class="btn btn-warning" 
                                    onclick="hf.edit(this, event)">
                                <i style="margin-right:5px;" class="fa fa-edit"></i> 
                                Edit
                            </button>
                        <?php } ?>

                        <?php if ($akses['a_delete'] == 1) { ?>
                            <button id_data="<?php echo $l['id_data'] ?>" 
                                    class="btn btn-danger" 
                                    onclick="hf.delete(this, event)">
                                <i style="margin-right:5px;" class="fa fa-trash"></i> 
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
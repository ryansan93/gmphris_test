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
            <th class="text-center">Urutan</th>
            <th class="text-center">Kategori</th>
            <th class="text-center">Title</th>
            <th class="text-center">Keterangan</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
            <?php foreach($list as $l){?>
                <tr class="data-row">
                    <td class="text-center"><?php echo $l['urutan'] ?></td>
                    <td><?php echo $l['nama_kategori'] ?></td>
                    <td><?php echo $l['title'] ?></td>
                    <td><?php echo $l['keterangan'] ?></td>
                    <td class="text-center">
                        <button id="<?php echo $l['id'] ?>" class="btn btn-primary" onclick="hf.show_detail(this, event)"><i style="margin-right:5px;" class="fa fa-file" aria-hidden="true"></i> Show Detail</button>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>

        <tr>
            <td colspan="6" style="text-align:center;">Tidak ada data</td>
        </tr>
        <?php } ?>


        <tr class="no-data" style="display:none;">
            <td colspan="6" style="text-align:center;">Tidak ada data</td>
        </tr>
        


    </tbody>
</table>
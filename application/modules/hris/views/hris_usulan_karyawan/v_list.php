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

    /* Garis pemisah antar kolom */
    .gmp-table thead th + th { border-left: 1px solid #e2e8f0; }
    .gmp-table tbody td + td { border-left: 1px solid #eef2f6; }


</style>


<table class="gmp-table">
    <thead>
        <tr>
            <th class="text-center">Yang Mengajukan</th>
            <th class="text-center">Jabatan</th>
            <th class="text-center">Posisi</th>
            <th class="text-center">Jumlah Butuh</th>
            <th class="text-center">Unit</th>
            <th class="text-center">Status Usulan</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
            <?php foreach($list as $l){?>
                <?php 
                    $status = '';
                    $color = '';
                    if( $l['status'] == 1 ){
                        $status = 'Draft';
                        $color = 'border: 2px solid #f7f578;';
                    } else if ( $l['status'] == 2 ){
                        $status = 'Acknowledge';
                        $color = 'border: 2px solid #AAF799;';
                    } else if ( $l['status'] == 3 ){
                        $status = 'Approved';
                        $color = 'border: 2px solid #2283D6;';
                    } else if ( $l['status'] == 6 ){
                        $status = 'Done';
                        $color = 'border: 2px solid #C7C7C7;';
                    } else if ( $l['status'] == 4 || $l['status'] == 5 ){ 
                        $status = $l['status'] == 5 ? 'Reject CEO' :'Reject HRD';
                        $color = 'border: 2px solid #F76363;';
                    } else {
                        $status = ' - ';
                        $color = ' ';
                    }
                ?>

                <tr class="data-row">
                    <td class="text-left"><?php echo ucwords(strtolower($l['nama'])) ?></td>
                    <td class="text-center"><?php echo ucwords(strtolower($l['jabatan'])) ?></td>
                    <td class="text-center"><?php echo $l['nama_posisi'] ?></td>
                    <td class="text-center"><?php echo $l['jumlah'] ?> Orang</td>
                    <td class="text-center"><?php echo $unit[$l['unit']]['nama'] ?></td>
                    <td class="text-center">
                        <div style="font-weight:600; padding:2px; border-radius:10px; text-align:center; <?php echo $color; ?>"><?php echo $status; ?> </div>
                    </td>
                    <td class="text-center">
                        <!-- <button class="btn btn-secondary" onclick="hf.show_detail(this, event);"><i class="fa fa-file"></i>  Show Detail</button> -->

                        <?php if( $l['status'] == 1 ){ ?>
                            <?php if ($akses['a_edit'] == 1) { ?>
                                <button id="<?php echo $l['id'] ?>" class="btn btn-warning" onclick="hf.edit(this, event)"><i style="margin-right:5px;" class="fa fa-edit" aria-hidden="true"></i> Edit</button>
                            <?php } ?>

                            <?php if ($akses['a_delete'] == 1) { ?>
                                <button id_data="<?php echo $l['id'] ?>" class="btn btn-danger" onclick="hf.delete(this, event)"><i style="margin-right:5px;" class="fa fa-trash" aria-hidden="true"></i> Hapus</button>
                            <?php } ?>

                        <?php } else {?>
                            <span>-</span>
                        <?php } ?>

                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>

            <tr>
                <td colspan="7" style="text-align:center;">Tidak ada data</td>
            </tr>

        <?php } ?>

        <tr class="no-data" style="display:none;">
            <td colspan="7" style="text-align:center;">Tidak ada data</td>
        </tr>

    </tbody>
</table>
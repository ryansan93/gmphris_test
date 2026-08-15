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
<table class="table-list gmp-table">
    <thead>
        <tr>
            <th class="text-center">Karyawan</th>
            <th class="text-center">Status</th>
            <th class="text-center">Keterangan</th>
            <th class="text-center">Tgl. Awal</th>
            <th class="text-center">Tgl. Selesai</th>
            <th class="text-center" colspan="2">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
           
            <?php foreach($list as $l){?>
                <tr class="data-row" id_data="<?php echo $l['id'] ?>" style="<?php echo !empty($l['selected']) ? 'background-color: #FFF9D6;' : '' ?>">
                    <td class="text-left"><?php echo ucwords(strtolower($l['nama'])) ?></td>
                    <td class="text-center"><?php echo $l['nama_kategori'] ?></td>
                    <td class="text-center"><?php echo $l['keterangan'] ?></td>
                    <td class="text-center"><?php echo tglIndonesia($l['tgl_berlaku'], "-" , " ") ?></td>
                    <td class="text-center"><?php echo tglIndonesia($l['tgl_selesai'], "-" , " ") ?></td>
                    <td class="text-center" style="min-width:150px;">
                        <?php 
                            $h7 = date('Y-m-d', strtotime($l['tgl_selesai'].' -7 days'));
                            $today = date('Y-m-d');
                            // $today = date('Y-m-d', strtotime($l['tgl_selesai'].' -2 days'));

                            if ( $l['status'] == 1 ){
                                echo ($today >= $h7 && $today < $l['tgl_selesai']) ? '<button class="btn btn-success"  last_kategori="'. $l['kategori'] .'" tgl_selesai="'. $l['tgl_selesai'] .'" id_data="'. $l['id'] .'" onclick="up.update_status(this, event)">Update Status</button>' : ' - ';
                            } else {
                                echo 'Status sudah diperbarui';
                            }
                        ?>

                        <!-- <button class="btn btn-success" last_kategori="<?php echo $l['kategori'] ?>" tgl_selesai="<?php echo $l['tgl_selesai'] ?>" id_data="<?php echo $l['id'] ?>" onclick="up.update_status(this, event)">Update Status</button> -->
                    </td>
                    <td class="text-center">
                        <?php 
                            $key = "secretkey";
                            $plaintext = $l['id'];
                            $encrypted = urlencode( openssl_encrypt($plaintext, 'AES-128-ECB', $key) );
                        ?>
                        <button  onclick="window.open('hris/HrisStatusKaryawan/print_preview?kode=<?php echo $encrypted ?>','_blank')" class="btn btn-secondary" onclick=""> <i class="fa fa-print"></i></button>
                    </td>
                </tr>
            <?php } ?>

        <?php } else { ?>

            <tr>
                <td colspan="9" style="text-align:center;">Tidak ada data</td>
            </tr>

        <?php } ?>

    </tbody>
</table>
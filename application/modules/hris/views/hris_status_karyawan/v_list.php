<table class="table-list table table-bordered">
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
                    <td class="text-left"><?php echo $l['nama'] ?></td>
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
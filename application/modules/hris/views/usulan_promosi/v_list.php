<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center">Tanggal</th>
            <th class="text-center">Pengusul</th>
            <th class="text-center">Karyawan</th>
            <th class="text-center">Jabatan Asal</th>
            <th class="text-center">Jabatan Tujuan</th>
            <th class="text-center">Status</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
           
            <?php foreach($list as $l){?>
                <tr class="data-row">
                    <td class="text-center"><?php echo tglIndonesia($l['tanggal'], "-" , " ") ?></td>
                    <td class="text-center"><?php echo ucwords(strtolower($l['nama_pengusul'])) ?></td>
                    <td class="text-center"><?php echo ucwords(strtolower($l['nama_karyawan'])) ?></td>
                    <td class="text-center"><?php echo $l['nama_jabatan_asal'] ?></td>
                    <td class="text-center"><?php echo $l['nama_jabatan_tujuan'] ?></td>

                    <?php
                        $status_map = [
                            1 => 'Draft',
                            2 => 'Acknowledge',
                            3 => 'Approved',
                            4 => 'Reject HRD',
                            5 => 'Reject CEO'
                        ];
                    ?>


                    <td class="text-center"><?php echo $status_map[$l['status']] ?? '-' ?></td>
                    <td class="text-center">
                        <?php 
                            $key = "secretkey";
                            $plaintext = $l['kode'];
                            $encrypted = urlencode( openssl_encrypt($plaintext, 'AES-128-ECB', $key) );
                        ?>

                        <button class="btn btn-secondary" status="<?= $l['status'] ?>" id_data="<?= $l['kode'] ?>" onclick="up.show_detail(this, event)"> <i class="fa fa-file"></i></button>

                        <!-- < ?php if ($l['status'] == 2 || $l['status'] == 3 ){?> -->
                            <button class="btn btn-info" onclick="window.open('hris/UsulanPromosi/print_preview?kode=<?= $encrypted ?>','_blank')"><i class="fa fa-print"></i></button>
                        <!-- < ?php } ?> -->
                    </td>
                </tr>
            <?php } ?>

      
        <?php } else { ?>

            <tr>
                <td colspan="7" style="text-align:center;">Tidak ada data</td>
            </tr>
        <?php } ?>

    </tbody>
</table>
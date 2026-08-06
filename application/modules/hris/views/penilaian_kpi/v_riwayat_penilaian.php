

<?php if(!empty($penilaian)){ ?>
    <?php foreach($penilaian as $p) { ?>
        <tr class="tr_loop">
            <td><?php echo $p['nik']?></td>
            <td><?php echo ucwords(strtolower($p['nama']))?></td>
            
            <?php 
                $bulan = [
                    1 => 'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember'
                ];
            ?>
            <td style="text-align:center"><?php echo $bulan[(int)date('n', strtotime($p['periode']))]; ?></td>
            <td style="text-align:center"><?php echo $p['nama_jabatan']?></td>
            <td style="text-align:right"><?php echo $p['total_nilai']?></td>
            <td style="text-align:center">
                <?php
                    switch ($p['status']) {
                        case 'APPROVED':
                            $statusClass = 'approved';
                            $icon = '🟢';
                            break;

                        case 'REJECTED':
                            $statusClass = 'rejected';
                            $icon = '🔴';
                            break;

                        default:
                            $statusClass = 'draft';
                            $icon = '🟡';
                            break;
                    }
                ?>

                <span class="status-badge <?= $statusClass ?>">
                    <?php echo $icon . ' ' . $p['status']; ?>
                </span>
            </td>
            <td style="text-align:center">
                <?php if ($p['status'] == 'DRAFT' ) { ?>
                    <button nik="<?php echo $p['nik']?>" id_penilaian="<?php echo $p['id']?>" onclick="penilaian.edit_penilaian(this, event)" class="btn btn-warning"><i class="fa fa-edit"></i></button>
                    <button nik="<?php echo $p['nik']?>" id_penilaian="<?php echo $p['id']?>" onclick="penilaian.delete_penilaian(this, event)" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                <?php } else { ?>
                    -
                <?php } ?>

            </td>
        </tr>
    <?php } ?>
<?php } else{ ?>
    <tr>
        <td colspan="7" style="text-align:center;"><i>Tidak ada data</i></td>
    </tr>
<?php } ?>

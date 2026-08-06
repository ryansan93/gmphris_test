

<?php if(!empty($list)): ?>
    <?php foreach($list as $k => $v): ?>

        <?php 
            $jenis_cuti = [
                'cuti' => ['label' => 'Cuti', 'class' => ''],
                'cuti_sakit'   => ['label' => 'Cuti Sakit',   'class' => 'jenis-sakit'],
                'cuti_force_majeure'    => ['label' => 'Cuti Force Majeure', 'class' => 'jenis-lain'],
            ];
            $jenis_info = $jenis_cuti[$v['jenis_cuti']] ?? ['label' => '-', 'class' => ''];

            // Status class mapping
            $status_lower = strtolower($v['status_pengajuan']);
            $status_class = 'status-draft';
            if (strpos($status_lower, 'pending') !== false || strpos($status_lower, 'ack') !== false) {
                $status_class = 'status-pending';
            } elseif (strpos($status_lower, 'approve') !== false) {
                $status_class = 'status-approved';
            } elseif (strpos($status_lower, 'reject') !== false) {
                $status_class = 'status-reject';
            }

            // Status icon
            $status_icon = '🟡';
            if ($status_class === 'status-pending')   $status_icon = '🔵';
            if ($status_class === 'status-approved')  $status_icon = '🟢';
            if ($status_class === 'status-reject')    $status_icon = '🔴';
        ?>

        <tr>
            <td class="text-center">
                <span class="cuti-nik"><?php echo $v['nik']; ?></span>
            </td>
            <td>
                <span class="cuti-name"><?php echo ucwords(strtolower($v['nama_karyawan'])); ?></span>
            </td>
            <td class="text-center">
                <span class="cuti-date"><?php echo tglIndonesia($v['tanggal_mulai'], '-', ' '); ?></span>
            </td>
            <td class="text-center">
                <span class="cuti-date"><?php echo tglIndonesia($v['tanggal_selesai'], '-', ' '); ?></span>
            </td>
            <td class="text-center">
                <span class="cuti-date">
                    <?php
                        $jumlah_hari = '-';
                        if (isset($v['jumlah_hari']) && $v['jumlah_hari'] !== '') {
                            $jumlah_hari = (int) $v['jumlah_hari'];
                        }
                        echo $jumlah_hari;
                    ?>
                </span>
            </td>
            <td class="text-center">
                <span class="cuti-jenis <?php echo $jenis_info['class']; ?>">
                    <?php echo $jenis_info['label']; ?>
                </span>
            </td>
            <td class="text-center">
                <span class="cuti-status <?php echo $status_class; ?>">
                    <span><?php echo $status_icon; ?></span>
                    <?php 
                        $map = [
                            1 => 'DRAFT',
                            2 => 'ACKNOWLEDGE',
                            3 => 'APPROVED',
                            4 => 'REJECT ATASAN',
                            5 => 'REJECT HRD'
                        ];
                    ?>
                    <span><?php echo $map[$v['status_pengajuan']] ?></span>
                </span>
            </td>
            <td>
                <div class="cuti-alasan" title="<?php echo htmlspecialchars($v['alasan']); ?>">
                    <?php echo $v['alasan']; ?>
                </div>
            </td>
            <td class="text-center">
                <?php if ($v['status_pengajuan'] == 1): ?>
                    <div class="cuti-actions">
                        <button class="btn btn-primary" onclick="pc.editPengajuan('<?php echo $v['id']; ?>')" title="Edit">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="pc.deletePengajuan('<?php echo $v['id']; ?>')" title="Hapus">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                <?php else: ?>
                    <span style="color:#adb5bd;">—</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="9">
            <div class="cuti-empty">
                <i class="fa fa-calendar-times-o"></i>
                <span>Belum ada data pengajuan cuti</span>
            </div>
        </td>
    </tr>
<?php endif; ?>
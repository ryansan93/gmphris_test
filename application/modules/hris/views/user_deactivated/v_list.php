
<table class="gmp-table">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>ID</th>
            <th>Document</th>
            <th>NIK</th>
            <th>Nama Karyawan</th>
            <th>Nama Jabatan</th>
            <th>Status</th>
            <th style="text-align: center;">Tanggal Resign</th>
            <th style="text-align: center;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list_data)): ?>
            <?php foreach ($list_data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['document']); ?></td>
                    <td><?= htmlspecialchars($row['nik']); ?></td>
                    <td><?= htmlspecialchars($row['nama_karyawan']); ?></td>
                    <td><?= htmlspecialchars($row['nama_jabatan']); ?></td>
                    <td>
                        <?php if (!empty($row['nonactive_user_by'])): ?>
                            <span class="badge badge-danger">Nonaktif</span>
                        <?php else: ?>
                            <span class="badge badge-success">Aktif</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;"><?= tglIndonesia($row['tanggal_resign'], '-', ' '); ?></td>
                    <td style="text-align: center;">
                        <?php if (!empty($row['nonactive_user_by'])): ?>
                            <button class="btn btn-primary" id_data="<?= htmlspecialchars($row['id']); ?>" nik=<?= htmlspecialchars($row['nik']); ?> onclick="ud.exec_aktifkanUser(this)"> Aktifkan</button>
                        <?php else: ?>
                            <!-- < ?php if (date('Y-m-d') > $row['tanggal_resign']) { ?> -->
                                <button class="btn btn-secondary" tgl_berjalan="<?php echo date('Y-m-d'); ?>" tglIndonesia="<?= tglIndonesia($row['tanggal_resign'], '-', ' '); ?>" tgl_resign="<?= htmlspecialchars($row['tanggal_resign']); ?>" id_data="<?= htmlspecialchars($row['id']); ?>" nik=<?= htmlspecialchars($row['nik']); ?> onclick="ud.exec_nonaktifkanUser(this)"> Nonaktifkan</button>
                            <!-- < ?php } else { ?>
                                -
                            < ?php } ?> -->
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align: center;">Data tidak ditemukan</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php
    $grouped = [];

    foreach ($list as $row) {
        $grouped[$row['karyawan']][] = $row;
    }
?>

<table class="table-list table table-bordered">
    <thead>
        <tr>
            <th class="text-center">No. Document</th>
            <th class="text-center">Tgl Usulan</th>
            <th class="text-center">Jenis</th>
            <th class="text-center">Pengusul</th>
            <!-- <th class="text-center">Karyawan</th> -->
            <th class="text-center">Jabatan Asal</th>
            <th class="text-center">Jabatan Tujuan</th>
            <th class="text-center">Tgl Mulai</th>
            <th class="text-center">Tgl Selesai</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($grouped)) { ?>

            <?php foreach ($grouped as $nik => $rows) { ?>

                <tr style="background:#f5f5f5;">
                    <td colspan="10">
                        <b><?php echo strtoupper($rows[0]['nama_karyawan']); ?></b>
                        (<?php echo $nik; ?>)
                    </td>
                </tr>

                <?php foreach ($rows as $l) { ?>
                    <tr class="data-row">
                        <td class="text-center"><?php echo $l['kode'] ?></td>
                        <td class="text-center" style="white-space:nowrap;"><?php echo tglIndonesia($l['tanggal'], "-", " ") ?></td>
                        <td class="text-center"><?php echo ucwords(strtolower($l['jenis'])) ?></td>
                        <td class="text-center"><?php echo ucwords(strtolower($l['nama_pengusul'])) ?></td>
                        <!-- <td class="text-center">< ?php echo ucwords(strtolower($l['nama_karyawan'])) ?></td> -->

                        <td class="text-left" style="white-space:nowrap;">
                            <?php echo $l['nama_jabatan_asal'] ?>
                            <br>
                            Perwakilan : <?php echo $l['nama_perwakilan_asal'] ?>
                            <br>
                            Unit : <?php echo $l['nama_unit_asal'] ?>
                        </td>

                        <td class="text-left" style="white-space:nowrap;">
                            <?php echo $l['nama_jabatan_tujuan'] ?>
                            <br>
                            Perwakilan : <?php echo $l['nama_perwakilan_tujuan'] ?>
                            <br>
                            Unit : <?php echo $l['nama_unit_tujuan'] ?>
                        </td>

                        <td class="text-center" style="white-space:nowrap;">
                            <?php echo tglIndonesia($l['tgl_mulai'], "-", " ") ?>
                        </td>

                        <td class="text-center" style="white-space:nowrap;">
                            <?php echo !empty($l['tgl_selesai']) ? tglIndonesia($l['tgl_selesai'], "-", " ") : '-' ?>
                        </td>

                        <td>
                            <?php
                            $key = "secretkey";
                            $plaintext = $l['kode'];
                            $encrypted = urlencode(openssl_encrypt($plaintext, 'AES-128-ECB', $key));
                            ?>

                            <?php if ($l['status'] == 2 || $l['status'] == 3) { ?>
                                <button class="btn btn-info"
                                    onclick="window.open('hris/ReportHistoryKaryawan/print_preview?kode=<?= $encrypted ?>','_blank')">
                                    <i class="fa fa-print"></i>
                                </button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>

            <?php } ?>

        <?php } else { ?>
            <tr>
                <td colspan="10" class="text-center">Tidak ada data</td>
            </tr>
        <?php } ?>
    </tbody>
</table>
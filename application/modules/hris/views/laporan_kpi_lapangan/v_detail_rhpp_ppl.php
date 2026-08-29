<?php 
    // 1. Inisialisasi Variabel
    $total_individual = 0;
    $total_group = 0;
    $total_populasi = 0;
    $total_fcr = 0;
    $total_ip = 0;
    $total_deplesi = 0;
    $total_bb = 0;
    $total_umur = 0;
    $jumlah_data = 0;

    // 2. Satu kali looping untuk menghitung semua (Lebih efisien daripada looping 3x)
    if (!empty($detail_data)) {
        foreach ($detail_data as $d) {
            $jumlah_data++;
            $total_populasi += $d['populasi'];
            $total_fcr += $d['fcr'];
            $total_ip += $d['ip'];
            $total_deplesi += $d['deplesi'];
            $total_bb += $d['bb'];
            $total_umur += $d['rata_umur'];

            if ($d['sumber_data'] == 'RHPP_INDIVIDUAL') {
                $total_individual++;
            } else {
                $total_group++;
            }
        }
    }

    // 3. Hitung rata-rata (dengan pengamanan division by zero)
    $avg_fcr = $jumlah_data > 0 ? $total_fcr / $jumlah_data : 0;
    $avg_ip = $jumlah_data > 0 ? $total_ip / $jumlah_data : 0;
    $avg_deplesi = $jumlah_data > 0 ? $total_deplesi / $jumlah_data : 0;
    $avg_bb = $jumlah_data > 0 ? $total_bb / $jumlah_data : 0;
    $avg_umur = $jumlah_data > 0 ? $total_umur / $jumlah_data : 0;
?>

<?php if ($jumlah_data == 0): ?>
    <!-- TAMPILAN JIKA DATA KOSONG -->
    <fieldset>
        <legend>DETAIL DATA RHPP</legend>
        <div style="text-align: center; padding: 40px; color: #777; background-color: #f9f9f9; border-radius: 5px;">
            <i class="fa fa-inbox" style="font-size: 40px; margin-bottom: 10px; display: block;"></i>
            <h4>Tidak ada data untuk periode atau PPL yang dipilih.</h4>
            <p>Silakan ubah filter pencarian Anda.</p>
        </div>
    </fieldset>

<?php else: ?>
    <!-- TAMPILAN JIKA ADA DATA -->

    <!-- 1. FIELDSET RHPP INDIVIDUAL -->
    <fieldset>
        <legend>RHPP INDIVIDUAL</legend>
        <div style="height:200px; overflow-y:scroll">
            <table class="gmp-table">
                <thead>
                    <tr>
                        <th class="text-center">Noreg</th>
                        <th class="text-center">Populasi</th>
                        <th class="text-center">FCR</th>
                        <th class="text-center">IP</th>
                        <th class="text-center">Deplesi</th>
                        <th class="text-center">BB</th>
                        <th class="text-center">Umur</th>
                        <th class="text-center">Tgl Tutup</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($detail_data as $d): ?>
                        <?php if ($d['sumber_data'] == 'RHPP_INDIVIDUAL'): ?>
                            <tr>
                                <td class="text-center"><?php echo $d['noreg'] ?></td>
                                <td class="text-right"><?php echo number_format($d['populasi'], 0, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['fcr'], 3, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['ip'], 3, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['deplesi'], 3, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['bb'], 3, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['rata_umur'], 1, ',', '.') ?></td>
                                <td class="text-right"><?php echo tglIndonesia($d['tanggal_tutup'], '-', ' ') ?></td>                 
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pull-right" style="margin-top: 10px;">
            <span style="font-size:12px; font-weight: bold;">Total Data : <?php echo $total_individual ?></span>
        </div>
    </fieldset>

    <br>

    <!-- 2. FIELDSET RHPP GROUP -->
    <fieldset>
        <legend>RHPP GROUP</legend>
        <div style="min-height:40px; height:200px; overflow-y:scroll">
            <table class="gmp-table">
                <thead>
                    <tr>
                        <th class="text-center">Noreg</th>
                        <th class="text-center">Populasi</th>
                        <th class="text-center">FCR</th>
                        <th class="text-center">IP</th>
                        <th class="text-center">Deplesi</th>
                        <th class="text-center">BB</th>
                        <th class="text-center">Umur</th>
                        <th class="text-center">Tgl Tutup</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($detail_data as $d): ?>
                        <?php if ($d['sumber_data'] == 'RHPP_GROUP'): ?>
                            <tr>
                                <td class="text-center"><?php echo $d['noreg'] ?></td>
                                <td class="text-right"><?php echo number_format($d['populasi'], 0, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['fcr'], 3, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['ip'], 3, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['deplesi'], 3, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['bb'], 3, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['rata_umur'], 1, ',', '.') ?></td>
                                <td class="text-right"><?php echo tglIndonesia($d['tanggal_tutup'], '-', ' ') ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pull-right" style="margin-top: 10px;">
            <span style="font-size:12px; font-weight: bold;">Total Data : <?php echo $total_group ?></span>
        </div>
    </fieldset>

    <br>

    <!-- 3. FIELDSET REPORT (TOTAL & RATA-RATA) -->
    <fieldset>
        <legend>REPORT SUMMARY</legend>
        <table class="gmp-table">
            <thead>
                <tr>
                    <th class="text-center">Jml <br> Plasma</th>
                    <th class="text-center">Total <br> Populasi</th>
                    <th class="text-center">Rata² FCR</th>
                    <th class="text-center">Rata² IP</th>
                    <th class="text-center">Rata² Deplesi</th>
                    <th class="text-center">Rata² BB</th>
                    <th class="text-center">Rata² Umur</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td class="text-center"><?php echo $jumlah_data ?></td>
                    <td class="text-right"><?php echo number_format($total_populasi, 0, ',', '.') ?></td>
                    <td class="text-right"><?php echo number_format($avg_fcr, 3, ',', '.') ?></td>
                    <td class="text-right"><?php echo number_format($avg_ip, 3, ',', '.') ?></td>
                    <td class="text-right"><?php echo number_format($avg_deplesi, 3, ',', '.') ?></td>
                    <td class="text-right"><?php echo number_format($avg_bb, 3, ',', '.') ?></td>
                    <td class="text-right"><?php echo number_format($avg_umur, 1, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>

<?php endif; ?>
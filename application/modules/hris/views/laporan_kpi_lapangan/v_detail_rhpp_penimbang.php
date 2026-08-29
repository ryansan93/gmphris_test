<?php 
    // 1. Inisialisasi Variabel Khusus Penimbang
    $total_individual = 0;
    $total_group = 0;
    $total_populasi = 0;
    $total_panen_ekor = 0;
    $total_panen_kg = 0;
    $jumlah_data = 0;

    // 2. Satu kali looping untuk menghitung semua
    if (!empty($detail_data)) {
        foreach ($detail_data as $d) {
            $jumlah_data++;
            $total_populasi += $d['populasi'];
            $total_panen_ekor += $d['jml_panen_ekor'];
            $total_panen_kg += $d['jml_panen_kg'];

            if ($d['sumber_data'] == 'RHPP_INDIVIDUAL') {
                $total_individual++;
            } else {
                $total_group++;
            }
        }
    }

    // 3. Hitung Rata-rata BW Panen (Total Kg / Total Ekor) -> Ini metrik paling penting untuk Penimbang!
    $avg_bw_panen = $total_panen_ekor > 0 ? ($total_panen_kg / $total_panen_ekor) : 0;
?>

<?php if ($jumlah_data == 0): ?>
    <!-- TAMPILAN JIKA DATA KOSONG -->
    <fieldset>
        <legend>DETAIL DATA PENIMBANG</legend>
        <div style="text-align: center; padding: 40px; color: #777; background-color: #f9f9f9; border-radius: 5px;">
            <i class="fa fa-inbox" style="font-size: 40px; margin-bottom: 10px; display: block;"></i>
            <h4>Tidak ada data untuk periode atau Penimbang yang dipilih.</h4>
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
                        <th class="text-center">Total Panen<br>(Ekor)</th>
                        <th class="text-center">Total Panen<br>(Kg)</th>
                        <th class="text-center">Rata2 BW<br>(Kg)</th>
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
                                <td class="text-right"><?php echo number_format($d['jml_panen_ekor'], 0, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['jml_panen_kg'], 3, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['bw_panen'], 3, ',', '.') ?></td>
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
                        <th class="text-center">Total Panen<br>(Ekor)</th>
                        <th class="text-center">Total Panen<br>(Kg)</th>
                        <th class="text-center">Rata2 BW<br>(Kg)</th>
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
                                <td class="text-right"><?php echo number_format($d['jml_panen_ekor'], 0, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['jml_panen_kg'], 3, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($d['bw_panen'], 3, ',', '.') ?></td>
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

    <!-- 3. FIELDSET REPORT SUMMARY (Disesuaikan untuk Metrik Penimbang) -->
    <fieldset>
        <legend>REPORT SUMMARY</legend>
        <table class="gmp-table">
            <thead>
                <tr>
                    <th class="text-center">Jml <br> Plasma</th>
                    <th class="text-center">Total <br> Populasi</th>
                    <th class="text-center">Total Panen <br> (Ekor)</th>
                    <th class="text-center">Total Panen <br> (Kg)</th>
                    <th class="text-center">Rata2 BW <br> Panen (Kg)</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td class="text-center"><?php echo $jumlah_data ?></td>
                    <td class="text-right"><?php echo number_format($total_populasi, 0, ',', '.') ?></td>
                    <td class="text-right"><?php echo number_format($total_panen_ekor, 0, ',', '.') ?></td>
                    <td class="text-right"><?php echo number_format($total_panen_kg, 3, ',', '.') ?></td>
                    <td class="text-right" style="color: #d35400;"><?php echo number_format($avg_bw_panen, 3, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>

<?php endif; ?>
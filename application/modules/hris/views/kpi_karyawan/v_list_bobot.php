<?php 
    function formatPersen($nilai)
    {
        return rtrim(rtrim($nilai, '0'), '.') . '%';
    }

    $bulan_text = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
?>


<?php if (!empty($error)) { ?>
    <tr>
        <td colspan="6">
            <div class="alert alert-danger mb-0">
                <?= $error; ?>
            </div>
        </td>
    </tr>

<?php } elseif (!empty($bobot)) { ?>

    <?php 
        $bobot_total = 0; 
        $no = 1;
    ?>

    <?php 
        $kode = $bobot[0]['kode_index'];
        $parts = explode('/', $kode);
        $periode_bulan = $parts[2];
        $periode_tahun = $parts[3];
        // cetak_r($parts, 1);
    ?>

    <?php if( $periode_bulan != $bulan ) { ?>
        <tr>
            <td colspan="6" style="background-color:#B0ECFF;">
                <i class="fa fa-exclamation-triangle" style="margin-right:10px;" aria-hidden="true"></i>
                Index KPI periode terpilih tidak ditemukan, anda sedang menggunakan Index KPI periode <b><?php echo $bulan_text[$periode_bulan] ?></b>
            </td>
        </tr>
    <?php } ?>

    <?php foreach ($bobot as $b) { ?>
        <?php $bobot_total += $b['bobot']; ?>

        <tr class="tr_loop" kode_index="<?= $b['kode_index']; ?>">
            <td class="text-center" style="width:100px;"><?= $no++; ?></td>
            <td><?= $b['nama_kpi']; ?></td>
            <td class="text-center"><?php echo formatPersen($b['bobot']); ?></td>

            <td style="width:100px;">
                <input type="number" min="0" class="form-control nilai text-right" max="100" oninput="if(this.value > 100) this.value = 100;  if(this.value < 0) this.value = 0;" onchange="kpi.hitungScore(this)">
            </td>

            <td style="width:100px;">
                <input type="number" class="form-control text-right" disabled>
            </td>

            <td>
                <textarea class="form-control" style="height:34px;"></textarea>
            </td>
        </tr>
    <?php } ?>

    <tr>
        <td colspan="2"><b>Total Bobot</b></td>
        <td class="text-center" style="width:100px;"> <?php echo $bobot_total .'%' ?></td>
        <td></td>
        <td><input type="int" disabled class="form form-control total_score" value="" style="width:100px;"></td>
        <td></td>
    </tr>

<?php } else { ?>
    <tr>
        <td colspan="6" class="text-center">
            Data bobot KPI tidak ditemukan
        </td>
    </tr>
<?php } ?>
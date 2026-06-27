<?php 
function formatPersen($nilai)
{
    return rtrim(rtrim($nilai, '0'), '.') . '%';
}
?>

<?php if (!empty($bobot)) { ?>
    <?php 
        $bobot_total = 0; 
        $no = 1;
    ?>

    <?php foreach ($bobot as $b) { ?>
        <?php $bobot_total += $b['bobot']; ?>

        <tr class="tr_loop" id_kpi="<?= $b['id']; ?>">
            <td class="text-center" style="width:100px;"><?= $no++; ?></td>
            <td><?= $b['nama_kpi']; ?></td>
            <td class="text-center"><?php echo formatPersen($b['bobot']); ?></td>

            <td style="width:100px;">
                <input type="number" class="form-control nilai text-right" max="100" oninput="if(this.value > 100) this.value = 100;" onchange="kpi.hitungScore(this)">
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
<?php $bulanSekarang = date('n'); ?>

<label>Periode Bulan</label>
<select class="select2 periode_kpi" id="periode_kpi" name="periode_kpi" onchange="kpi.get_data_periode(this, event)">
    <option value="1" <?= $bulanSekarang == 1 ? 'selected' : '' ?>>Januari</option>
    <option value="2" <?= $bulanSekarang == 2 ? 'selected' : '' ?>>Februari</option>
    <option value="3" <?= $bulanSekarang == 3 ? 'selected' : '' ?>>Maret</option>
    <option value="4" <?= $bulanSekarang == 4 ? 'selected' : '' ?>>April</option>
    <option value="5" <?= $bulanSekarang == 5 ? 'selected' : '' ?>>Mei</option>
    <option value="6" <?= $bulanSekarang == 6 ? 'selected' : '' ?>>Juni</option>
    <option value="7" <?= $bulanSekarang == 7 ? 'selected' : '' ?>>Juli</option>
    <option value="8" <?= $bulanSekarang == 8 ? 'selected' : '' ?>>Agustus</option>
    <option value="9" <?= $bulanSekarang == 9 ? 'selected' : '' ?>>September</option>
    <option value="10" <?= $bulanSekarang == 10 ? 'selected' : '' ?>>Oktober</option>
    <option value="11" <?= $bulanSekarang == 11 ? 'selected' : '' ?>>November</option>
    <option value="12" <?= $bulanSekarang == 12 ? 'selected' : '' ?>>Desember</option>
</select>

<hr>

<div class="index_content">

</div>


<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Filter</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding notifContain">

        <div style="display:flex; flex-direction:column;">
            <label for="">Periode</label>
            <select name="bulan" class="select2 bulan" onchange="kpi.ranking_by_periode(this, event);">
                <option value="">-- Pilih Bulan --</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 1 ? 'selected' : '' ); ?> value="1">Januari</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 2 ? 'selected' : '' ); ?> value="2">Februari</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 3 ? 'selected' : '' ); ?> value="3">Maret</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 4 ? 'selected' : '' ); ?> value="4">April</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 5 ? 'selected' : '' ); ?> value="5">Mei</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 6 ? 'selected' : '' ); ?> value="6">Juni</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 7 ? 'selected' : '' ); ?> value="7">Juli</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 8 ? 'selected' : '' ); ?> value="8">Agustus</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 9 ? 'selected' : '' ); ?> value="9">September</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 10 ? 'selected' : '' ); ?> value="10">Oktober</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 11 ? 'selected' : '' ); ?> value="11">November</option>
                <option <?php echo (isset($_GET['periode']) && $_GET['periode'] == 12 ? 'selected' : '' ); ?> value="12">Desember</option>
            </select>
            <input style="display:none;" type="date" class="tgl_mulai">
            <input style="display:none;" type="date" class="tgl_selesai">
        </div>

    </div>
</fieldset>

<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Bobot KPI</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding notifContain">

        <table class="table table-bordered list_ranking_kpi" style="width:100%;">
            <thead>
                <tr>
                    <th class="text-center">NIK</th>
                    <th class="text-center">Nama Karyawan</th>
                    <th class="text-center">Score</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3" class="text-center">Tidak ada data</td>
                </tr>
            </tbody>

        </table>

    </div>
</fieldset>

<div style="display:flex; flex-direction; justify-content:right; align-items:center; gap:10px;">
    <button class="btn btn-secondary" onclick="window.location.href='hris/KpiKaryawan'">Kembali</button>
</div>
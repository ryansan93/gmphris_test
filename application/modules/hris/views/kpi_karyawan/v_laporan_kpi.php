<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Filter Laporan</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding notifContain">

        <div style="display:flex; flex-direction:column;">
            <label for="">Periode</label>
            <select name="bulan" class="select2 bulan" onchange="kpi.filter_report_by_periode(this, event);">
                <option value="0" disabled selected>-- Pilih Bulan --</option>
                <option value="1">Januari</option>
                <option value="2">Februari</option>
                <option value="3">Maret</option>
                <option value="4">April</option>
                <option value="5">Mei</option>
                <option value="6">Juni</option>
                <option value="7">Juli</option>
                <option value="8">Agustus</option>
                <option value="9">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
            </select>
            <input style="display:none;" type="date" class="tgl_mulai">
            <input style="display:none;" type="date" class="tgl_selesai">
        </div>
    </div>
</fieldset>

<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Laporan KPI / Periode</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding notifContain">

        <table class="table table-bordered list_bobot">
            <thead>
                <tr>
                    <th class="text-center">NIK</th>
                    <th class="text-center">Nama Karyawan</th>
                    <th class="text-center">Jabatan</th>
                    <th class="text-center">Total Nilai</th>
                </tr>
            </thead>
            <tbody class="tbl-laporan-kpi">
                <?php if (!empty($laporan)) { ?>
                    <?php foreach ($laporan as $periode => $items) { ?>
                        <tr>
                            <td colspan="4" style="font-weight:bold;background:#f5f5f5;">
                                <?php echo $periode; ?>
                            </td>
                        </tr>

                        <?php foreach ($items as $l) { ?>
                            <tr>
                                <td><?php echo $l['nik']; ?></td>
                                <td><?php echo ucwords(strtolower($l['nama'])); ?></td>
                                <td><?php echo $l['nama_jabatan']; ?></td>
                                <td class="text-right"><?php echo $l['total_nilai']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">
                            <i>Tidak ada data</i>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>

        </table>

    </div>
</fieldset>

<div style="display:flex; flex-direction; justify-content:right; align-items:center; gap:10px;">
    <button class="btn btn-secondary" onclick="window.location.href='hris/KpiKaryawan'">Kembali</button>
</div>
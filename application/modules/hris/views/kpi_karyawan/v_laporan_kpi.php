<div id="laporan_kpi">

    <fieldset style="margin-bottom: 15px;">
        <legend>
            <div class="col-xs-12 no-padding">
                <b>Filter Laporan</b>
            </div>
        </legend>
        <div class="col-xs-12 no-padding notifContain">
    
            <div style="display:flex; flex-direction:row; gap:10px;">
                <div style="display:flex; flex-direction:column; width:50%;">
                    <label for="">Periode</label>
                    <?php $bulanSekarang = date('n'); ?>
                    <select class="select2 bulan" onchange="kpi.filter_report_by_periode(this, event);">
                        <option value="" disabled selected>-- Pilih Bulan --</option>
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
                    <input style="display:none;" type="date" class="tgl_mulai">
                    <input style="display:none;" type="date" class="tgl_selesai">
                </div>

                <div style="display:flex; flex-direction:column; width:50%">
                    <label for="">Unit</label>
                    
                    <select name="bulan" class="select2 unit" onchange="kpi.filter_report_by_periode(this, event);">
                        <option value="">-- Pilih Unit --</option>
                        <?php foreach($unit as $u) { ?>
                        <option value="<?php echo $u['kode'] ?>"><?php echo $u['nama'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div style="display:flex; flex-direction:column; width:50%">
                    <label for="">Jabatan</label>
                    
                    <select class="select2 jabatan" onchange="kpi.filter_report_by_periode(this, event);">
                        <option value="">-- Pilih Jabatan --</option>
                        <?php foreach($jabatan as $j) { ?>
                        <option value="<?php echo $j['kode'] ?>"><?php echo $j['nama'] ?></option>
                        <?php } ?>
                    </select>
                </div>
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
            
            <button class="btn btn-secondary" onclick="kpi.cetakLaporanPdf(this, event)">
                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Cetak Pdf
            </button>
            
            <table class="table table-bordered list_bobot" style="margin-top:10px;">
                <thead>
                    <tr>
                        <th class="text-center">NIK</th>
                        <th class="text-center">Nama Karyawan</th>
                        <th class="text-center">Masa Kerja</th>
                        <th class="text-center">Jabatan</th>
                        <th class="text-center">Unit</th>
                        <th class="text-center">Total Nilai</th>
                    </tr>
                </thead>
                <tbody class="tbl-laporan-kpi">
                    <?php if (!empty($laporan)) { ?>
                        <?php foreach ($laporan as $periode => $items) { ?>
                            <?php
                                    usort($items, function($a, $b) {
                                    return strcasecmp($a['nama_jabatan'], $b['nama_jabatan']);
                                });
                            ?>
                            <tr>
                                <td colspan="6" style="font-weight:bold;background:#f5f5f5;">
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
                            <td colspan="6" class="text-center">
                                <i>Tidak ada data</i>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
    
            </table> 
    
        </div>
    </fieldset>
<!--     
    <div style="display:flex; flex-direction; justify-content:right; align-items:center; gap:10px;">
        <button class="btn btn-secondary" onclick="window.location.href='hris/KpiKaryawan'">Kembali</button>
    </div> -->
</div>

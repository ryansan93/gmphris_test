

<script src="assets/chart/chart.js"></script>


<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Dashboard KPI</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding notifContain">

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

    </div>
</fieldset>

<div style="display:flex; flex-direction:row; justify-content:space-between; gap:10px; flex-wrap:wrap;">

    <div style="display:flex; flex-wrap:wrap; gap:10px;">

        <!-- Penilaian KPI -->
        <div style="flex:1 1 calc(20% - 8px); min-width:200px; height:150px; border:1px solid #D6E8FC; background:white; padding:12px; border-radius:12px;">
            <div style="display:flex; gap:10px; height:100%;">
                <div>
                    <div style="width:50px; display:flex; padding:10px; border-radius:50%; background:#3B82F6; justify-content:center; align-items:center;">
                        <i class="fa fa-pencil-square-o" style="font-size:30px; color:white;"></i>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; flex:1;">
                    <span style="font-size:12px; color:#3B82F6; font-weight:bold;">Penilaian KPI</span>
                    <span style="font-size:12px; color:#1F2937;">Input dan kelola penilaian KPI Karyawan</span>

                    <button onclick="window.location.href='hris/KpiKaryawan/penilaianKpi'" style="margin-top:auto; width:100%; border:1px solid #D1D5DB; border-radius:6px; padding:6px 12px; background:white;">
                        Buka →
                    </button>
                </div>
            </div>
        </div>

        <!-- Approval KPI -->
        <div style="flex:1 1 calc(20% - 8px); min-width:200px; height:150px; border:1px solid #D6E8FC; background:white; padding:12px; border-radius:12px;">
            <div style="display:flex; gap:10px; height:100%;">
                <div>
                    <div style="width:50px; display:flex; padding:10px; border-radius:50%; background:#FFB859; justify-content:center; align-items:center;">
                        <i class="fa fa-check-circle-o" style="font-size:30px; color:white;"></i>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; flex:1;">
                    <span style="font-size:12px; color:#FFB859; font-weight:bold;">Approval KPI</span>
                    <span style="font-size:12px; color:#1F2937;">Review dan approval penilaian KPI</span>

                    <button onclick="window.location.href='hris/KpiKaryawan/approvalKpi'" style="margin-top:auto; width:100%; border:1px solid #D1D5DB; border-radius:6px; padding:6px 12px; background:white;">
                        Buka →
                    </button>
                </div>
            </div>
        </div>

        <!-- Ranking KPI -->
        <div style="flex:1 1 calc(20% - 8px); min-width:200px; height:150px; border:1px solid #D6E8FC; background:white; padding:12px; border-radius:12px;">
            <div style="display:flex; gap:10px; height:100%;">
                <div>
                    <div style="width:50px; display:flex; padding:10px; border-radius:50%; background: #63E04C; justify-content:center; align-items:center;">
                        <i class="fa fa-signal" style="font-size:30px; color:white;"></i>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; flex:1;">
                    <span style="font-size:12px; color: #63E04C; font-weight:bold;">Ranking KPI</span>
                    <span style="font-size:12px; color: #1F2937;">Lihat ranking KPI karyawan berdasarkan periode</span>

                    <button style="margin-top:auto; width:100%; border:1px solid #D1D5DB; border-radius:6px; padding:6px 12px; background:white;">
                        Buka →
                    </button>
                </div>
            </div>
        </div>

        <!-- Target KPI -->
        <div style="flex:1 1 calc(20% - 8px); min-width:200px; height:150px; border:1px solid #D6E8FC; background:white; padding:12px; border-radius:12px;">
            <div style="display:flex; gap:10px; height:100%;">
                <div>
                    <div style="position:relative; width:50px; height:50px; border-radius:50%; background: #808080; display:flex; justify-content:center; align-items:center;">
                        <i class="fa fa-circle-o" style="position:absolute; font-size:40px; color:white;"></i>
                        <i class="fa fa-cog" style="position:absolute; font-size:20px; color:white;"></i>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; flex:1;">
                    <span style="font-size:12px; color: #a5a5a5; font-weight:bold;">Setting KPI</span>
                    <span style="font-size:12px; color: #1F2937;">Kelola target KPI per jabatan dan posisi</span>

                    <button onclick="window.location.href='hris/KpiKaryawan/settingKpi'" style="margin-top:auto; width:100%; border:1px solid #D1D5DB; border-radius:6px; padding:6px 12px; background:white;">
                        Buka →
                    </button>
                </div>
            </div>
        </div>

        <!-- Laporan KPI -->
        <div style="flex:1 1 calc(20% - 8px); min-width:200px; height:150px; border:1px solid #D6E8FC; background:white; padding:12px; border-radius:12px;">
            <div style="display:flex; gap:10px; height:100%;">
                <div>
                    <div style="width:50px; display:flex; padding:10px; border-radius:50%; background: #8B5CF6; justify-content:center; align-items:center;">
                        <i class="fa fa-file" style="font-size:30px; color:white;"></i>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; flex:1;">
                    <span style="font-size:12px; color: #8B5CF6; font-weight:bold;">Laporan KPI</span>
                    <span style="font-size:12px; color: #1F2937;">Laporan dan analisis hasil KPI karyawan</span>

                    <button onclick="window.location.href='hris/KpiKaryawan/laporanKpi'" style="margin-top:auto; width:100%; border:1px solid #D1D5DB; border-radius:6px; padding:6px 12px; background:white;">
                        Buka →
                    </button>
                </div>
            </div>
        </div>

    </div>
   
</div>


<br>


<fieldset style="margin-bottom: 15px; width:50%;">
    <legend style="width:30%;">
        <div class="col-xs-12 no-padding" onchange="kpi.charts_performance(this, event)">
            <b>Perfomance Karyawan</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding notifContain">

        <select class="select2" onchange="kpi.loadCharts(this, event)">
            <option disabled selected>Pilih Karyawan</option>
            <?php foreach($charts as $c_index => $c){ ?>
                <option value="<?php echo $c_index ?>" label="<?php echo $c["label"] ?>" nilai="<?php echo $c["nilai"] ?>" >
                    <?php echo $c_index ?>
                </option>
            <?php } ?>
        </select>

        <canvas id="kpiChart" width="600" height="300"></canvas>

    </div>
</fieldset>
















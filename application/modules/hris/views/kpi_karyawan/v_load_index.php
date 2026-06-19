<div style="display:flex; flex-direction:row; justify-content:space-between; gap:10px; flex-wrap:wrap;">

    <div style="flex: 1 1 calc(20% - 8px); min-width:180px; border:1px solid #D6E8FC; background-color: #EAF3FE; padding:10px; border-radius:5px;">
        <div style="display:flex; flex-direction:row; gap:10px; justify-content:space-between;">

            <?php
                $nilai                      = (float)$nilai_average['avg_sekarang'];
                $nilai_periode_sebelumnya   = (float)$nilai_average['avg_lalu'];

                if ($nilai < 60) {
                    $kategori = 'Kurang';
                    $bgcolor = '#fbd7ce';
                    $textcolor = '#f6643b';
                } elseif ($nilai < 70) {
                    $kategori = 'Cukup';
                    $bgcolor = '#fbf3ce';
                    $textcolor = '#f6b83b';
                } elseif ($nilai < 85) {
                    $kategori = 'Sangat Baik';
                    $bgcolor = '#cee1fb';
                    $textcolor = '#3B82F6';
                } else {
                    $kategori = 'Istimewa';
                    $bgcolor = '#cefbd5';
                    $textcolor = '#3bf670';
                }
            ?>
            
            <div style="display:flex; flex-direction:column; ">
                <span style="font-size:12px; color: #3B82F6; font-weight:bold;">Rata rata KPI Perusahaan</span>

                 <div style="display:flex; flex-direction:row; gap:10px; align-items:center;">
                    <span style="font-size:20px; color: #1F2937; font-weight:bold;"> <?php echo number_format($nilai, 2, ',', '.') ?></span>
                    <div style="display:flex; padding:3px; border-radius:15px; background-color: #cee1fb; height:20px; justify-content:center; align-items:center;">
                        <span style="color: #3B82F6; font-size:12px;">Sangat Baik</span>
                    </div>
                </div>

                <span style="font-size:10px">
                    <?php if ($nilai_average['persentase'] !== null) { ?>
                        <b style="color: <?= $nilai_average['naik'] ? '#22C55E' : '#EF4444' ?>;">
                            <i class="fa <?= $nilai_average['naik'] ? 'fa-long-arrow-up' : 'fa-long-arrow-down' ?>" aria-hidden="true"></i>
                            <?= number_format(abs($nilai_average['persentase']), 2) ?>%
                        </b>
                        dari periode lalu
                    <?php } else { ?>
                        <span style="color:#6B7280;">
                            Belum ada data periode lalu
                        </span>
                    <?php } ?>
                </span>

            </div>

            <div style="display:flex; flex-direction:column; gap:10px; justify-content:center; align-items:center;">
                <div style="display:flex; padding:10px; border-radius:50%; background-color: #DCEBFF; justify-content:center; align-items:center;">
                    <i class="fa fa-line-chart" style="font-size:30px; color:#3B82F6" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <div style="flex: 1 1 calc(20% - 8px); min-width:180px; border:1px solid #dafec2; background-color: #eefde4; padding:10px; border-radius:5px;">
        <div style="display:flex; flex-direction:row; gap:10px; justify-content:space-between;">
            <div style="display:flex; flex-direction:column; ">
                <span style="font-size:12px; color: #5daa2a; font-weight:bold;">Karyawan Dinilai</span>

                 <div style="display:flex; flex-direction:row; gap:10px; align-items:center;">
                    <span style="font-size:20px; color: #31531a; font-weight:bold;"><?php echo $data_karyawan['sudah_dinilai']?></span>
                </div>

                <span style="font-size:10px">dari <?php echo $data_karyawan['sudah_dinilai'] + $data_karyawan['menunggu_approval'] + $data_karyawan['belum_dinilai'] ?> karyawan</span>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px; justify-content:center; align-items:center;">
                <div style="position:relative; display:flex; height:50px; width:50px; padding:10px; border-radius:50%; background-color: #dafec2; justify-content:center; align-items:center;">
                    <i class="fa fa-user" style="position:absolute; z-index:1; margin-left:-10px; font-size:30px; color: #22C55E" aria-hidden="true"></i>
                    <i class="fa fa-thumbs-up" style="position:absolute; margin-left:18px; margin-top:-15px; color: #36dc73" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <div style="flex: 1 1 calc(20% - 8px); min-width:180px; border:1px solid #f7f1ae; background-color: #FCF9D7; padding:10px; border-radius:5px;">
        <div style="display:flex; flex-direction:row; gap:10px; justify-content:space-between;">
            <div style="display:flex; flex-direction:column; ">
                <span style="font-size:12px; color: #d5c405; font-weight:bold;">Menunggu Approval</span>

                 <div style="display:flex; flex-direction:row; gap:10px; align-items:center;">
                    <span style="font-size:20px; color: #1F2937; font-weight:bold;"><?php echo $data_karyawan['menunggu_approval'] ?></span>
                </div>

                <span style="font-size:10px">Penilaian KPI</span>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px; justify-content:center; align-items:center;">
                 <div style=" display:flex; height:50px; width:50px; padding:10px; border-radius:50%; background-color: #f7f1ae; justify-content:center; align-items:center;">
                    <i class="fa fa-clock-o" style="position:absolute; font-size:30px; color: #d5c405" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <div style="flex: 1 1 calc(20% - 8px); min-width:180px; border:1px solid #D6E8FC; background-color: #EAF3FE; padding:10px; border-radius:5px;">
        <div style="display:flex; flex-direction:row; gap:10px; justify-content:space-between;">
            <div style="display:flex; flex-direction:column; ">
                <span style="font-size:12px; color: #3B82F6; font-weight:bold;">Belum Dinilai</span>

                 <div style="display:flex; flex-direction:row; gap:10px; align-items:center;">
                    <span style="font-size:20px; color: #1F2937; font-weight:bold;"><?php echo $data_karyawan['belum_dinilai']?></span>
                </div>

                <span style="font-size:10px">Karyawan</span>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px; justify-content:center; align-items:center; width:30%">
                <div style="display:flex; position:relative; height:50px; width:50px; padding:10px; border-radius:50%; background-color: #DCEBFF; justify-content:center; align-items:center;">
                    <i class="fa fa-user" style="position:absolute; font-size:30px; color:#3B82F6" aria-hidden="true"></i>
                    <i class="fa fa-question" style="position:absolute; margin-top:-17px; margin-left:17px; color:#3B82F6" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

</div>

<br>

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
                    <div style="width:50px; display:flex; padding:10px; border-radius:50%; background:#3B82F6; justify-content:center; align-items:center;">
                        <i class="fa fa-signal" style="font-size:30px; color:white;"></i>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; flex:1;">
                    <span style="font-size:12px; color:#3B82F6; font-weight:bold;">Ranking KPI</span>
                    <span style="font-size:12px; color:#1F2937;">Lihat ranking KPI karyawan berdasarkan periode</span>

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
                    <div style="position:relative; width:50px; height:50px; border-radius:50%; background:#3B82F6; display:flex; justify-content:center; align-items:center;">
                        <i class="fa fa-circle-o" style="position:absolute; font-size:40px; color:white;"></i>
                        <i class="fa fa-circle-o" style="position:absolute; font-size:20px; color:white;"></i>
                        <i class="fa fa-circle-o" style="position:absolute; font-size:5px; color:white;"></i>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; flex:1;">
                    <span style="font-size:12px; color:#3B82F6; font-weight:bold;">Setting KPI</span>
                    <span style="font-size:12px; color:#1F2937;">Kelola target KPI per jabatan dan posisi</span>

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
                    <div style="width:50px; display:flex; padding:10px; border-radius:50%; background:#3B82F6; justify-content:center; align-items:center;">
                        <i class="fa fa-file" style="font-size:30px; color:white;"></i>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; flex:1;">
                    <span style="font-size:12px; color:#3B82F6; font-weight:bold;">Laporan KPI</span>
                    <span style="font-size:12px; color:#1F2937;">Laporan dan analisis hasil KPI karyawan</span>

                    <button onclick="window.location.href='hris/KpiKaryawan/laporanKpi'" style="margin-top:auto; width:100%; border:1px solid #D1D5DB; border-radius:6px; padding:6px 12px; background:white;">
                        Buka →
                    </button>
                </div>
            </div>
        </div>

    </div>
   
</div>

<br>
<br>

<style>
    .status-badge {
        display: inline-block;
        min-width: 100px;
        padding: 5px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
    }

    .status-badge.approved {
        border: 1px solid #28a745;
        color: #198754;
        background-color: #eaf8ee;
    }

    .status-badge.draft {
        border: 1px solid #e6dc4e;
        color: #8a7d00;
        background-color: #fffde7;
    }

    .status-badge.rejected {
        border: 1px solid #dc3545;
        color: #dc3545;
        background-color: #fdeaea;
    }

    .btn-xls {
        width:100%; 
        flex:1; color: #03477e; 
        border:1px solid #03477e; 
        border-radius:6px; 
        padding:6px 12px; 
        background-color:#9FD3FC;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    .btn-xls:hover {
        background-color: #416d8e;
        color : #ffffff;
    }
</style>

<div id="penilaian_id">

    <div class="panel-heading no-padding">
        <ul class="nav nav-tabs nav-justified">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#list_data" data-tab="list_data">LIST DATA</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#action" data-tab="action">ADD DATA</a>
            </li>
        </ul>
    </div>
    <br>
    <div class="tab-content">

        <div id="list_data" class="tab-pane fade in active">

            <fieldset>
                <legend>Import</legend>

                <div style="display:flex; gap:4px;">
                    
                    <button onclick="kpi.downloadTemplatePenilaian(this, event)" class="btn-xls">
                        <i class="fa fa-download" aria-hidden="true"></i> Download Template
                    </button>
                    <button onclick="kpi.importXlsPenilaian(this, event)" class="btn-xls">
                        <i class="fa fa-upload" aria-hidden="true"></i> Import Xls
                    </button>
                </div>
            </fieldset>
            <br>

            <fieldset>
                <legend>Riwayat Penilaian</legend>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align:center;">NIK</th>
                            <th style="text-align:center;">Nama</th>
                            <th style="text-align:center;">Periode</th>
                            <th style="text-align:center;">Jabatan</th>
                            <th style="text-align:center;">Total Nilai</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="list_data_penilaian_kpi">
                    
                    </tbody>
                </table>
            </fieldset>
    
        </div>

        <div id="action" class="tab-pane fade">

            <fieldset style="margin-bottom: 15px;">
                <legend>
                    <div class="col-xs-12 no-padding">
                        <b>Data Karyawan</b>
                    </div>
                </legend>
                <div class="col-xs-12 no-padding notifContain">

                    <div style="display:flex; flex-direction:column;">
                        <label for="">Periode</label>
                        <select name="bulan" class="select2 bulan" onchange="kpi.getPeriode();">
                            <option value="">-- Pilih Bulan --</option>
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

                    <div style="display:flex; flex-direction:column;" class="select-penilai">
                        <label for="">Penilai</label>
                        <select class="select2 penilai" id="penilai" onchange="kpi.getPeriode(this, event)">
                            <option disabled selected>Pilih Penilai</option>
                            <?php foreach($karyawan as $k){ ?>
                                <option <?php echo $nik_login == $k['nik'] ? 'selected' : ''  ?> value="<?php echo $k['nik']?>"><?php echo ucwords(strtolower($k['nama'])) . ' - ' . $k['nama_jabatan'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div style="display:flex; flex-direction:column;" class="select-karyawan">
                        <label for="">Nama Karyawan</label>
                        <select class="select2 karyawan" id="karyawan" onchange="kpi.loadDataBobot(this, event)" disabled>
                            <option>Pilih Karyawan</option>
                        </select>
                    </div>

                    <div style="display:flex; flex-direction:column;">
                        <label for="">Jabatan</label>
                        <input type="text" disabled class="form form-control nama-jabatan">
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

                    <table class="table table-bordered list_bobot">
                        <thead>
                            <tr>
                                <th class="text-center">Kode Bobot</th>
                                <th class="text-center">Nama Penilaian</th>
                                <th class="text-center">Bobot</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center">Score</th>
                                <th class="text-center">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        </tbody>

                    </table>

                </div>
            </fieldset>

            <div style="display:flex; flex-direction; justify-content:right; align-items:center; gap:10px;">
                <!-- <button class="btn btn-secondary" onclick="window.location.href='hris/KpiKaryawan'">Kembali</button> -->
                <button class="btn btn-primary" onclick="kpi.save(this, event)">Simpan</button>
            </div>
        </div>



    </div>
</div>

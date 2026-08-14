<style>
    /* ===== STATUS BADGE (dengan dot, seperti referensi) ===== */
    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-width: 100px;
        padding: 5px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
    }
    .sb-dot {
        width: 9px; height: 9px;
        border-radius: 50%;
        box-shadow: inset 0 -1px 2px rgba(0,0,0,.25);
        flex-shrink: 0;
    }
    .status-badge.approved          { border: 1px solid #28a745; color: #198754; background-color: #eaf8ee; }
    .status-badge.approved .sb-dot  { background: #28a745; }
    .status-badge.draft             { border: 1px solid #e6dc4e; color: #8a7d00; background-color: #fffde7; }
    .status-badge.draft .sb-dot     { background: #d9cf3a; }
    .status-badge.rejected          { border: 1px solid #dc3545; color: #dc3545; background-color: #fdeaea; }
    .status-badge.rejected .sb-dot  { background: #dc3545; }

    /* ===== TOMBOL IMPORT ===== */
    .btn-xls {
        flex: 1;
        color: #03477e;
        border: 1px solid #03477e;
        border-radius: 6px;
        padding: 6px 12px;
        background-color: #9FD3FC;
        transition: background-color .3s ease, color .3s ease, border-color .3s ease;
    }
    .btn-xls:hover { background-color: #416d8e; color: #ffffff; }

    /* ===== CLEAN TABLE (hilangkan border kotak Bootstrap) ===== */
    #penilaian_id table.table-bordered { background: #fff; }
    #penilaian_id table.table-bordered > thead > tr > th,
    #penilaian_id table.table-bordered > tbody > tr > td { border: none; }

    #penilaian_id table.table-bordered > thead > tr > th {
        background: #f0f4f8;
        color: #5b6b7c;
        text-transform: uppercase;
        font-size: .72rem;
        letter-spacing: .5px;
        font-weight: 700;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0 !important;
        white-space: nowrap;
    }
    #penilaian_id table.table-bordered > tbody > tr > td {
        padding: 16px;
        border-bottom: 1px solid #eef2f6 !important;
        font-size: .9rem;
        color: #334155;
        vertical-align: middle;
    }
    #penilaian_id .table tbody tr:hover td { background: #fafcfe; }

    /* ===== ELEMEN BARIS (dipakai view AJAX) ===== */
    .ct-nik { color: #1f75fe; font-weight: 600; }
    .ct-actions { display: flex; gap: 6px; justify-content: center; }
    .ct-act {
        width: 30px; height: 30px;
        border-radius: 8px;
        border: 1px solid;
        cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px;
        transition: all .15s;
    }
    .ct-act.edit { background: #fffde7; border-color: #e6dc4e; color: #8a7d00; }
    .ct-act.edit:hover { background: #e6dc4e; color: #fff; }
    .ct-act.del  { background: #fdeaea; border-color: #dc3545; color: #dc3545; }
    .ct-act.del:hover  { background: #dc3545; color: #fff; }
    .ct-dash { color: #cbd5e1; font-weight: 700; }

    /* ===== FORM ADD DATA — grid rapi ===== */
    #penilaian_id #action .notifContain {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 14px;
        align-items: end;
    }
    #penilaian_id #action .notifContain > div { display: flex; flex-direction: column; }
    #penilaian_id #action label {
        font-size: .75rem;
        font-weight: 700;
        color: #5b6b7c;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 6px;
    }

    /* ===== SAVE BAR (ganti inline style yang typo) ===== */
    .kpi-savebar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
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

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
                <option>Pilih Karyawan</option>
                <?php foreach($karyawan as $k){ ?>
                    <option disabled selected>Pilih Penilai</option>
                    <option value="<?php echo $k['nik']?>"><?php echo $k['nama']?></option>
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
    <button class="btn btn-secondary" onclick="window.location.href='hris/KpiKaryawan'">Kembali</button>
    <button class="btn btn-primary" onclick="kpi.save(this, event)">Simpan</button>
</div>
<style>
    .gmp-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
    }

    .gmp-table thead th {
        background: #f0f4f8;
        color: #5b6b7c;
        text-transform: uppercase;
        font-size: .72rem;
        letter-spacing: .5px;
        font-weight: 700;
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .gmp-table tbody td {
        padding: 10px;
        border-bottom: 1px solid #eef2f6;
        font-size: .9rem;
        color: #334155;
        vertical-align: middle;
    }

    .gmp-table tbody tr { transition: background .15s; }
    .gmp-table tbody tr:hover td { background: #fafcfe; }
    .gmp-table tbody tr:last-child td { border-bottom: none; }

    .gmp-table .text-center, .gmp-table th.text-center { text-align: center; }
    .gmp-table .text-right,  .gmp-table td.text-right  { text-align: right; }

    .gmp-table-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        overflow-x: auto;
    }

    /* Garis pemisah antar kolom */
    .gmp-table thead th + th { border-left: 1px solid #e2e8f0; }
    .gmp-table tbody td + td { border-left: 1px solid #eef2f6; }
</style>

<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Filter</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding notifContain">

        <div style="display:flex; flex-direction:row; gap:10px;">
            <div style="display:flex; flex-direction:column; width:50%">
                <label for="">Periode</label>
                <?php $bulanSekarang = date('n'); ?>
                <select class="select2 bulan" onchange="kpi.ranking_by_periode(this, event);">
                    <option disabled selected>-- Pilih Bulan --</option>
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
                
                <select class="select2 unit" onchange="kpi.ranking_by_periode(this, event);">
                    <option value="">-- Pilih Unit --</option>
                    <?php foreach($unit as $u) { ?>
                    <option value="<?php echo $u['kode'] ?>"><?php echo $u['nama'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; width:50%">
                <label for="">Jabatan</label>
                
                <select class="select2 jabatan" onchange="kpi.ranking_by_periode(this, event);">
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
            <b>Bobot KPI</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding notifContain">

        <table class="gmp-table list_ranking_kpi" style="width:100%;">
            <thead>
                <tr>
                    <th class="text-center">No.</th>
                    <th class="text-center">NIK</th>
                    <th class="text-center">Masa Kerja</th>
                    <th class="text-center">Nama Karyawan</th>
                    <th class="text-center">Unit</th>
                    <th class="text-center">Score</th>
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

<!-- <div style="display:flex; flex-direction; justify-content:right; align-items:center; gap:10px;">
    <button class="btn btn-secondary" onclick="window.location.href='hris/KpiKaryawan'">Kembali</button>
</div> -->
<style>
    .filter-container {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }

    .filter-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1 1 250px;
    }

    .filter-item label {
        font-weight: 600;
    }

    .filter-item select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e3e8f0;
        border-radius: 8px;
        background: #fff;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .filter-container {
            flex-direction: column;
        }
    }
</style>

<fieldset>
    <legend>Filter Report</legend>

    <div class="filter-container">

        <div class="filter-item">
            <label>Jabatan</label>
            <select class="select2 jabatan" onchange="report.filter_list(this, event)">
                <option value="">Pilih Jabatan</option>
                <?php foreach ($jabatan as $j) { ?>
                    <option value="<?= $j['kode']; ?>"><?= $j['nama']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="filter-item">
            <label>Jenis Usulan</label>
            <select class="select2 jenis" onchange="report.filter_list(this, event)">
                <option value="">Pilih Jenis</option>
                <option value="RESIGN">Resign</option>
                <option value="DO">Drop Out (DO)</option>
            </select>
        </div>

        <div class="filter-item">
            <label>Status Pengajuan</label>
            <select class="select2 status" onchange="report.filter_list(this, event)">
                <option value="">Pilih Jenis</option>
                <option value="1">DRAFT</option>
                <option value="2">ACKNOWLEDGE</option>
                <option value="3">APPROVED</option>
                <option value="4">REJECT ATASAN</option>
                <option value="5">REJECT HRD</option>
            </select>
        </div>

    </div>
</fieldset>

<br>
<div class="list-area">

</div>
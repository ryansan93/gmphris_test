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

<fieldset class="mb-3">
    <legend>Filter</legend>

    <div class="filter-container">

        <div class="filter-item">
            <label>Jabatan</label>
            <select class="select2 jabatan" onchange="ud.filter_list(this, event)">
                <option value="">Pilih Jabatan</option>
                <?php foreach ($jabatan as $j) { ?>
                    <option value="<?= $j['kode']; ?>"><?= $j['nama']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="filter-item">
            <label>Jenis Status</label>
            <select class="select2 status" onchange="ud.filter_list(this, event)">
                <option value="">Pilih Status</option>
                <option value="active">AKTIF</option>
                <option value="nonactive">NON-AKTIF</option>
            </select>
        </div>

    </div> 
</fieldset>


<fieldset class="mb-3">
    <legend>Notifikasi Penonaktifan Akses</legend>

    <!-- Notification List -->
    <div id="notification-list">
        
    </div>   
</fieldset>
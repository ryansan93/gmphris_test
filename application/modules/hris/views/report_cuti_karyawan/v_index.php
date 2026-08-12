<style>
    .filter-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        width: 100%;
    }

    .filter-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    .filter-item label {
        font-weight: 600;
        margin-bottom: 0;
    }

    .filter-item select,
    .filter-item .select2 {
        width: 100% !important;
        border-radius: 8px;
        background: #fff;
        font-size: 14px;
        box-sizing: border-box;
    }

    /* Tablet */
    @media (max-width: 992px) {
        .filter-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Mobile */
    @media (max-width: 576px) {
        .filter-container {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }
</style>


<fieldset>
    <legend>Filter Report</legend>

    <div class="filter-container">

        <div class="filter-item">
            <label>Karyawan</label>
            <select class="select2 karyawan" onchange="report.filter_list(this, event)">
                <option value="">Pilih Karyawan</option>
                <?php foreach ($karyawan as $k) { ?>
                    <option value="<?= $k['nik']; ?>">
                        <?= ucwords(strtolower($k['nama'])); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="filter-item">
            <label>Jabatan</label>
            <select class="select2 jabatan" onchange="report.filter_list(this, event)">
                <option value="">Pilih Jabatan</option>
                <?php foreach ($jabatan as $j) { ?>
                    <option value="<?= $j['kode']; ?>">
                        <?= $j['nama']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="filter-item">
            <label>Jenis Usulan</label>
            <select class="select2 jenis" onchange="report.filter_list(this, event)">
                <option value="">Pilih Jenis</option>
                <option value="cuti">Cuti</option>
                <option value="cuti_sakit">Cuti Sakit</option>
                <option value="cuti_force_majeure">Cuti Force Majeure</option>
                <option value="cuti_jatah_liburan">Cuti Jatah Liburan</option>
            </select>
        </div>

        <div class="filter-item">
            <label>Status</label>
            <select class="select2 status" onchange="report.filter_list(this, event)">
                <option value="">Pilih Status</option>
                <option value="1">DRAFT</option>
                <option value="2">ACKNOWLEDGE</option>
                <option value="3">APPROVED</option>
                <option value="4">REJECT ATASAN</option>
                <option value="5">REJECT HRD</option>
            </select>
        </div>

        <div class="filter-item">
            <label>Bulan</label>
            <select class="select2 bulan" onchange="report.filter_list(this, event)">
                <option value="all">Semua Bulan</option>

                <?php
                $months = [
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ];

                foreach ($months as $num => $name) {
                    $selected = ($num == date('m')) ? ' selected' : '';
                    echo "<option value=\"{$num}\"{$selected}>{$name}</option>";
                }
                ?>
            </select>
        </div>

        <div class="filter-item">
            <label>Tahun</label>
            <select class="select2 tahun" onchange="report.filter_list(this, event)">
                <?php
                $current_year = date('Y');

                for ($year = $current_year - 2; $year <= $current_year + 2; $year++) {
                    $selected = ($year == $current_year) ? ' selected' : '';
                    echo "<option value=\"{$year}\"{$selected}>{$year}</option>";
                }
                ?>
            </select>
        </div>

    </div>
</fieldset>

<br>

<div class="list-area"></div>
<style>



    /* ===== Select ===== */
    .karyawan-select-wrapper .select2-container {
        width: 100% !important;
    }

    /* ===== Buttons ===== */
    .karyawan-actions {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    .karyawan-actions .btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-add {
        background: #ecfdf5;
        color: #059669;
    }

    .btn-add:hover {
        background: #059669;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(5, 150, 105, 0.3);
    }

    .btn-remove {
        background: #fef2f2;
        color: #dc2626;
    }

    .btn-remove:hover {
        background: #dc2626;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
    }

    /* ===== Empty State ===== */
    .empty-state {
        text-align: center;
        padding: 30px;
        color: #94a3b8;
        font-style: italic;
    }

    /* ===== Select2 Custom ===== */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 8px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        color: #334155;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    .select2-dropdown {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #6366f1;
    }
</style>

<fieldset>
    <legend>Daftar Karyawan </legend>

    <table class="gmp-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th>Hak Cuti</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="list-karyawan">
            <?php $no = 1; ?>
            <?php foreach ($karyawan as $k) { ?>
                <tr class="row-karyawan">
                    <td class="row-number"><?php echo $no++ ?></td>
                    <td>
                        <input type="text" nik="<?php echo $k['nik']; ?>" class="form form-control" value="<?php echo ucwords(strtolower($k['nama'])); ?>">
                    </td>
                    <td>
                        <input type="text" class="form form-control" value="<?php echo $k['nama_jabatan']; ?>">
                    </td>
                    <td>
                        <input type="number" class="form form-control" value="12">
                    </td>
                    <td>
                        <div class="karyawan-actions">
                            <!-- <button type="button" class="btn btn-add" onclick="mc.addrow(this)" title="Tambah Baris">
                                <i class="fa fa-plus"></i>
                            </button> -->
                            <button type="button" class="btn btn-remove" onclick="mc.removerow(this)" title="Hapus Baris">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</fieldset>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Pilih Karyawan',
            allowClear: true
        });
    });
</script>
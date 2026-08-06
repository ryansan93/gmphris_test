<div id="list-karyawan">
    <div class="row-karyawan" style="display:flex; flex-direction:row; gap:10px; margin-top:10px;">
        <select class="karyawan select2" >
            <option value="">Pilih Karyawan</option>
            <?php foreach ($karyawan as $k) { ?>
                <option value="<?= $k['nik']; ?>"><?= ucwords(strtolower($k['nama'])); ?></option>
            <?php } ?>
        </select>

        <button type="button" class="btn btn-secondary" onclick="mc.addrow(this)">
            <i class="fa fa-plus"></i>
        </button>

        <button type="button" class="btn btn-danger" onclick="mc.removerow(this)">
            <i class="fa fa-trash"></i>
        </button>
    </div>
</div>
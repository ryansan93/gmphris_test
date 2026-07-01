<div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">
    <div style="display:flex; flex-direction:row; gap:15px; align-items:center;">
        
        <div style="display:flex; flex-direction:column;">
            <label>Kode</label>
            <input type="text" class="form form-control kode_jabatan" disabled value="<?php echo $jabatan['kode'] ?>">
        </div>

        <div style="display:flex; flex-direction:column;">
            <label>Nama Jabatan</label>
            <input type="text" class="form form-control nama_jabatan" value="<?php echo $jabatan['nama'] ?>">
        </div>

        <div style="display:flex; flex-direction:column;">
            <label>Level</label>
            <input type="number" class="form form-control level" value="<?php echo $jabatan['level'] ?>">
        </div>

        <div style="display:flex; flex-direction:column; width:20%;">
            <label>Kode Document</label>
            <input type="text" class="form form-control kode_dokumen" style="text-transform: uppercase;" value="<?php echo $jabatan['kode_dokumen'] ?>">
        </div>

        <div style="display:flex; flex-direction:column;">
            <label>Jabatan Atasan</label>
            <select class="select2 jabatan_atasan">
                <option disabled selected>Pilih Jabatan Atasan</option>
                <?php foreach($jabatan_atasan as $ja) { ?>
                    <option value="<?php echo $ja['kode'] ?>" <?php echo ($ja['kode'] == $jabatan['kode_jabatan_atasan']) ? 'selected' : '' ?>><?php echo $ja['nama'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
<div id="setting_edit">

    <div>
        <label for="">Nama Template</label>
        <input type="text" class="form form-control nama" value="<?php echo $header_data[0]['nama_template'] ?>">
    </div>
    
    <div>
        <label for="">Jabatan</label>
        <select class="select2 jabatan" id="">
            <option disabled selected> Pilih Jabatan</option>
            <?php foreach($jabatan as $j){ ?>
                <option <?php echo $header_data[0]['jabatan_id'] == $j['kode'] ? 'selected' : '' ?> value="<?php echo $j['kode'] ?>"><?php echo $j['nama'] ?></option>
            <?php } ?>
        </select>
    </div>
    
    <div>
        <label for="">Keterangan</label>
        <textarea type="text" class="form form-control keterangan" rowspan="2"><?php echo $header_data[0]['keterangan']?></textarea>
    </div>
    
    <br>
    <fieldset>
        <legend>
            <b>Input Data Bobot</b>
        </legend>
    
    
        <div class="detail-input" style="display:flex; flex-direction:column; gap:10px;">
    
            <?php foreach($detail_data as $detail){ ?>
                <div class="row-input" style="display:flex; flex-direction:row; gap:10px;">
                    <input class="form form-control nama_kpi" type="text" placeholder="Masukan nama KPI" style="width:40%" value="<?php echo $detail['nama_kpi'] ?>">
                    <input class="form form-control keterangan_detail" type="text" placeholder="Masukan keterangan" value="<?php echo $detail['keterangan'] ?>">
                    <input class="form form-control bobot" oninput="kpi.config_bobot(this, event)" value="<?php echo intval($detail['bobot']) ?>" type="number" style="width:15%" placeholder="Masukan bobot">
                    <button class="btn btn-primary add-row" onclick="kpi.add_row_setting(this, event)"><i class="fa fa-plus"></i></button>
                    <button class="btn btn-danger" onclick="kpi.delete_row_setting(this, event)"><i class="fa fa-close"></i></button>
                </div>
            <?php } ?>
        </div>
    </fieldset>
    
    <div id_data="<?php echo $header_data[0]['id'] ?>" id="id_edit_setting"></div>
</div>

<!-- <button class=" pull-right btn btn-primary save-setting" onclick="kpi.save_setting(this, event)">Simpan</button> -->
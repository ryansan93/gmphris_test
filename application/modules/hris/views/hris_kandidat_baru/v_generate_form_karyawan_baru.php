<br>

<fieldset>
    <legend>Data Karyawan</legend>

    <div class="body">

        <div class="form-group d-flex align-items-center">
            <div class="col-lg-2">
                <span>NIK</span>
            </div>
            <div class="col-lg-6">
                <input type="text" placeholder="NIK Pegawai" class="form-control nik_pegawai" data-required="1">
            </div>
        </div>
        <div class="form-group d-flex align-items-center">
            <div class="col-lg-2">
                <span>Nama Pegawai</span>
            </div>
            <div class="col-lg-6">
                <input type="text" placeholder="Nama Pegawai" class="form-control nama_pegawai" value="<?php echo $list['nama'] ?>" data-required="1">
            </div>
        </div>
        <div class="form-group d-flex align-items-center">
            <div class="col-lg-2">
                <span>Jabatan</span>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form form-control jabatan" value_fix="<?php echo $jabatan_nama['kode'] ?>" value="<?php echo $jabatan_nama['nama'] ?>" disabled>
            </div>
        </div>
        <div class="form-group d-flex align-items-center">
            <div class="col-lg-2">
                <span>Atasan</span>
            </div>
            <div class="col-lg-4">
                <select class="select2 form-control atasan">
                    <option value="">-- Pilih Atasan --</option>
                    <?php foreach($atasan as $a){?>
                        <option value="<?php echo $a['id'] ?>"><?php echo $a['nama'] ?></option>
                    <?php } ?>
                </select>
                <!-- <input type="text" class="form-control jabatan" data-required="1"> -->
            </div>
        </div>
        <div class="form-group d-flex align-items-center">
            <div class="col-lg-2">
                <span>Marketing</span>
            </div>
            <div class="col-lg-3">
                <select class="form-control marketing" data-required="1">
                    <option value="">-- Pilih Marketing --</option>
                    <option value="all">All</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
                <!-- <input type="text" placeholder="Marketing" class="form-control marketing" data-required="1" data-tipe="integer" maxlength="2" disabled> -->
            </div>
        </div>
        <div class="form-group d-flex align-items-center">
            <div class="col-lg-2">
                <span>Koordinator</span>
            </div>
            <div class="col-lg-3">
                <select class="form-control koordinator" data-required="1">
                    <option value="">-- Pilih Koordinator --</option>
                    <option value="all">All</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
                <!-- <input type="text" placeholder="Koordinator" class="form-control koordinator" data-required="1" data-tipe="integer" maxlength="2" disabled> -->
            </div>
        </div>
        <div class="form-group d-flex align-items-center">
            <div class="col-lg-2">
                <span>Wilayah</span>
            </div>
            <div class="col-lg-6">
                <select class="wilayah" name="wilayah[]" multiple="multiple" width="100%" placeholder="Pilih Wilayah" data-required="1">
                    <option value="all" > All </option>
                    <?php foreach ($list_wilayah as $key => $v_wilayah): ?>
                        <?php $selected = $list['induk_wilayah'] == $v_wilayah['id'] ? 'selected' : ''?>
                        <option <?php echo $selected ?> value="<?php echo $v_wilayah['id']; ?>" > <?php echo $v_wilayah['nama']; ?> </option>
                    <?php endforeach ?>
                </select>
                <!-- <input type="text" placeholder="Unit" class="form-control unit" data-required="1" disabled> -->
            </div>
        </div>
        <div class="form-group d-flex align-items-center">
            <div class="col-lg-2">
                <span>Unit</span>
            </div>
            <div class="col-lg-6">
                <select class="unit" name="unit[]" multiple="multiple" width="100%" placeholder="Pilih Unit" data-required="1">
                    <option value="all" > All </option>
                    <?php foreach ($list_unit as $key => $v_unit): ?>
                        <?php $selected = $list['unit'] == $v_unit['kode'] ? 'selected' : ''?>
                        <option <?php echo $selected ?> value="<?php echo $v_unit['id']; ?>" > <?php echo $v_unit['nama']; ?> </option>
                    <?php endforeach ?>
                </select>
                <!-- <input type="text" placeholder="Unit" class="form-control unit" data-required="1" disabled> -->
            </div>
        </div>
        <div class="form-group d-flex align-items-center">
            <div class="col-lg-2">
                <span>Level</span>
            </div>
            <div class="col-lg-3">
                <select class="form-control level" data-required="1">
                    <option value="">-- Pilih Level --</option>
                    <!-- <option value="0">0</option> -->
                    <option <?php echo $jabatan_nama['level'] == 1 ? 'selected' : '' ?> value="1">1</option>
                    <option <?php echo $jabatan_nama['level'] == 2 ? 'selected' : '' ?> value="2">2</option>
                    <option <?php echo $jabatan_nama['level'] == 3 ? 'selected' : '' ?> value="3">3</option>
                    <option <?php echo $jabatan_nama['level'] == 4 ? 'selected' : '' ?> value="4">4</option>
                    <option <?php echo $jabatan_nama['level'] == 5 ? 'selected' : '' ?> value="5">5</option>
                    <option <?php echo $jabatan_nama['level'] == 6 ? 'selected' : '' ?> value="6">6</option>
                    <option <?php echo $jabatan_nama['level'] == 7 ? 'selected' : '' ?> value="7">7</option>
                </select>
                <!-- <input type="text" placeholder="Wilayah" class="form-control wilayah" data-required="1" disabled> -->
            </div>
        </div>

    </div>    

</fieldset>



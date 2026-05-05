<div class="panel-heading no-padding">
    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#riwayat" data-tab="riwayat">RIWAYAT USULAN</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#action" data-tab="action">ADD DATA</a>
        </li>
    </ul>
</div>


<div class="tab-content"> 
<div id="riwayat" class="tab-pane fade show active" role="tabpanel" style="padding-top: 10px;">


    <fieldset style="margin-bottom: 15px;">
        <legend>
            <div class="col-xs-12 no-padding">
                <b>FILTER</b>
            </div>
        </legend>

        <div style="display:flex; flex-direction:column; gap:15px;">

            <!-- Tanggal -->
            <div class="filter-row">
                <div class="filter-item">
                    <label>Tanggal Awal</label>
                    <div class="input-group date datetimepicker" id="tgl_awal">
                        <input type="text" name="tgl_awal" class="datepicker form-control text-center" placeholder="Tanggal Awal" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>

                <div class="filter-item">
                    <label>Tanggal Akhir</label>
                    <div class="input-group date datetimepicker" id="tgl_akhir">
                        <input type="text" name="tgl_akhir" class="datepicker form-control text-center" placeholder="Tanggal Akhir" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Jabatan -->
            <div class="filter-full">
                <label>Jabatan Yang Diusulkan</label>
                <select class="select2 form-control"></select>
            </div>

            <!-- Button -->
            <div class="filter-actions">
                <button class="btn btn-primary" onclick="hf.filter_data(this, event)">
                    <i class="fa fa-search" style="margin-right: 10px;"></i> Filter
                </button>

                <button class="btn btn-primary" onclick="hf.changeTabActive()">
                    <i class="fa fa-plus" style="margin-right: 10px;"></i> Add Data
                </button>
            </div>

        </div>
    </fieldset>

    <fieldset style="margin-bottom: 15px;">
        <legend>
            <div class="col-xs-12 no-padding">
                <b>LIST DATA</b>
            </div>
        </legend>
        <div class="col-xs-12 no-padding list_data">
            <div class="spinner-load"></div>
        </div>
    </fieldset>

</div>

<div id="action" class="tab-pane fade tab-detail" role="tabpanel" style="padding-top: 10px;">

    <div class="panel panel-default">
        <div class="panel-heading"><span style="font-size:17px;">Tambah Data</span></div>
        <div class="panel-body">

            <div style="display:flex; flex-direction:column; gap:10px;">
                <!-- <div style="display:flex; flex-direction:row; ">
                    <span style="width:200px;">Kode Usulan</span>
                    <span style="width:50px;">:</span>
                    <input type="text" class="form form-control kode_usulan">
                </div> -->
                
                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">Tgl Usulan</span>
                    <span style="width:50px;">:</span>
                    <div class="input-group date datetimepicker" id="tgl_usulan">
                        <input type="text" name="tgl_usulan" class="datepicker form-control text-center" placeholder="Tanggal Usulan" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>

                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">Pengusul</span>
                    <span style="width:50px;">:</span>
                    <select name="" id="" class="select2 pengusul" onchange="up.set_jabatan(this, event, 'pengusul')">
                        <option disabled selected> -- Pilih Karyawan -- </option>
                        <?php foreach ( $karyawan as $k ) {?>
                            <option jabatan_val="<?php echo $k['jabatan']; ?>" jabatan_text="<?php echo $k['detail_jabatan']['nama']; ?>" value="<?php echo $k['nik']; ?>" ><?php echo ucwords(strtolower($k['nama'])) ?></option>
                        <?php }?>
                    </select>
                </div>

                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">Jabatan Pengusul</span>
                    <span style="width:50px;">:</span>
                    <input type="text" class="form form-control jabatan_pengusul">
                </div>

                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">Karyawan Yang di Usulkan</span>
                    <span style="width:50px;">:</span>
                    <select name="" id="" class="select2 karyawan" onchange="up.set_jabatan(this, event, 'karyawan')">
                        <option disabled selected> -- Pilih Karyawan -- </option>
                        <?php foreach ( $karyawan as $k ) {?>
                            <option jabatan_val="<?php echo $k['jabatan']; ?>" jabatan_text="<?php echo $k['detail_jabatan']['nama']; ?>" value="<?php echo $k['nik']; ?>" ><?php echo ucwords(strtolower($k['nama'])) ?></option>
                        <?php }?>
                    </select>
                </div>

                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">Jabatan Asal</span>
                    <span style="width:50px;">:</span>
                    <input type="text" class="form form-control jabatan_asal">
                </div>

                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">Jabatan Tujuan</span>
                    <span style="width:50px;">:</span>
                    <select name="" id="" class="select2 jabatan_tujuan">
                        <option disabled selected> -- Pilih Jabatan -- </option>
                        <?php foreach ( $jabatan as $j ) {?>
                            <option value="<?php echo $j['kode']; ?>" ><?php echo $j['nama'] ?></option>
                        <?php }?>
                    </select>
                </div>

                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">Alasan</span>
                    <span style="width:50px;">:</span>
                    <textarea type="text" class="form form-control alasan"></textarea>
                </div>
                
            </div>
            <br>
            <div class="pull-right">
                <button class="btn btn-secondary " onclick="window.location.href='hris/UsulanPromosi' "> <i class="fa fa-angle-left" style="margin-right:10px;" aria-hidden="true"></i>  Back</button>
                <button class="btn btn-primary " onclick="up.save(this, event)"> <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>  Save Data</button>
            </div>

        </div>

        </div>

</div>
</div>


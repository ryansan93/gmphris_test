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
                    <b>LIST DATA</b>
                </div>
            </legend>
            <div class="col-xs-12 no-padding list_data" style="overflow-x:scroll">
                <div class="spinner-load"></div>
            </div>
        </fieldset>

    </div>

    <div id="action" class="tab-pane fade tab-detail" role="tabpanel" style="padding-top: 10px;">
        <!-- < ?php if ($akses['a_submit'] == 1) { ?> -->
       
            <div class="panel panel-default">

                <div class="panel-heading">
                    <span style="font-size:17px;">Tambah Data</span>
                </div>

                <div class="panel-body">

                    <div class="row">

                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Karyawan</label>
                                <select name="nik" class="form-control select2 nik" required>
                                    <option value="">-- Pilih Karyawan --</option>

                                    <?php foreach($karyawan as $row){ ?>
                                    
                                        <?php if (in_array($row['nik'], $nik_outstanding)) continue; ?>

                                        <option <?php echo $nik_login == $row['nik'] ? 'selected' : '' ?> value="<?php echo $row['nik']; ?>">
                                            <?php echo $row['nik'] .' - '. ucwords(strtolower($row['nama'])); ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Jenis Pengunduran</label>
                                <select class="select2 jenis">
                                    <option value="DO">Drop Out (DO)</option>
                                    <option value="RESIGN">Resign</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Tanggal Pengajuan Resign</label>
                                <div class="input-group date datetimepicker tgl_pengajuan" id="tgl_pengajuan">
                                    <input type="text"
                                        name="tgl_usulan"
                                        value=""
                                        class="datepicker form-control text-center"
                                        placeholder="Tanggal Usulan" />

                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Tanggal Efektif Resign</label>
                                <div class="input-group date datetimepicker tgl_resign" id="tgl_resign">
                                    <input type="text"
                                        name="tgl_resign"
                                        value=""
                                        class="datepicker form-control text-center"
                                        placeholder="Tanggal Resign" />

                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        

                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <label>Alasan Resign</label>

                                <textarea name="alasan_resign"
                                        class="alasan_resign form-control"
                                        rows="4"
                                        placeholder="Masukkan alasan resign"
                                        required></textarea>
                            </div>
                        </div>

                    </div>

             
                    <fieldset>
                        <legend>Upload Attachment</legend>
                        <button class="btn btn-sm btn-primary" onclick="ukr.add_attachment(this, event)"> <i class="fa fa-plus"></i> Tambah Upload</button>
                        
                        <div class="upload-area">

                            <div class="upload-card" data-index="0">
                                <span style="color:red" onclick="ukr.remove_attachment(this,event)"><i class="fa fa-times"></i> </span>
                                <div class="thumbnail">
                                    <i class="fa fa-upload" onclick="ukr.upload_attachment(this,event)" style="font-size:50px; cursor:pointer"></i>
                                </div>
                                <div class="nama-file">
                                    Surat Resign
                                </div>
                                <!-- <button type="button" class="btn btn-sm btn-danger mt-1" onclick="ukr.remove_attachment(this,event)"> <i class="fa fa-times"></i> </button> -->
                            </div>

                        </div>
                    </fieldset>

                    <br>
                    <div class="pull-right">

                        <button class="btn btn-primary" onclick="ukr.save(this, event)">
                            <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>
                            Save Data
                        </button>

                    </div>
                </div>
            </div>

        <!-- < ?php } else { ?> -->


        <!-- < ?php }  ?> -->

    </div>
</div>
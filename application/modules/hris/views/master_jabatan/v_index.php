<div class="panel-heading no-padding">
    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#riwayat" data-tab="riwayat">RIWAYAT JABATAN</a>
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
                <b>FILTER DATA</b>
            </div>
        </legend>
        <div class="col-xs-12 no-padding">
           <label for="">Cari Data</label>
           <input type="text" class="form form-control" placeholder="Cari Data" onkeyup="mj.filterall(this, event)">
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

    <?php if ($akses['a_submit'] == 1) { ?>
        <div class="panel panel-default">
            <div class="panel-heading"><span style="font-size:17px;">Tambah Data</span></div>
                <div class="panel-body">
            
                    <div class="detail_area" style="display:flex; flex-direction:column; gap:10px; ">

                        <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

                            <div style="display:flex; flex-direction:row; gap:15px; align-items:center;">
                            
                                <div style="display:flex; flex-direction:column; width:20%;">
                                    <label>Kode</label>
                                    <input type="text" class="form form-control kode_jabatan">
                                </div>

                                <div style="display:flex; flex-direction:column; width:20%;">
                                    <label>Nama Jabatan</label>
                                    <input type="text" class="form form-control nama_jabatan">
                                </div>

                                <div style="display:flex; flex-direction:column; width:20%;">
                                    <label>Level</label>
                                    <input type="number" class="form form-control level">
                                </div>

                                <div style="display:flex; flex-direction:column; width:20%;">
                                    <label>Kode Document</label>
                                    <input type="text" class="form form-control kode_dokumen" style="text-transform: uppercase;">
                                </div>

                                <div style="display:flex; flex-direction:column; width:30%;">
                                    <label>Jabatan Atasan</label>
                                    <select class="select2 jabatan_atasan">
                                        <option disabled selected>Pilih Jabatan Atasan</option>
                                        <?php foreach($jabatan_atasan as $ja) { ?>
                                            <option value="<?php echo $ja['kode'] ?>"><?php echo $ja['nama'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div style="width:40%; text-align:right">
                                    <button class="btn btn-warning" onclick="mj.add_row(this, event);"><span class="fa fa-plus"></span></button>
                                    <button class="btn btn-danger" onclick="mj.delete_row(this, event);"><span class="fa fa-close"></span></button>   
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <br>

                    <div class="pull-right">
                        <button class="btn btn-secondary " onclick="window.location.href='hris/MasterJabatan'"> <i class="fa fa-angle-left" style="margin-right:10px;" aria-hidden="true"></i>  Back</button>
                        <button class="btn btn-primary " onclick="mj.save(this, event)"> <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>  Save Data</button>
                    </div>

                </div>
            </div>

        </div>
    <?php } ?>
</div>


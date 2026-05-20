


<!-- <div class="panel-heading no-padding">
    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#riwayat" data-tab="riwayat">RIWAYAT USULAN</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#action" data-tab="action">ADD DATA</a>
        </li>
    </ul>
</div> -->


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
                    <label>Tanggal</label>
                    <div class="input-group date datetimepicker" id="tgl_awal">
                        <input type="text" name="tgl_awal" class="datepicker form-control text-center" placeholder="Tanggal" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>

                <!-- <div class="filter-item">
                    <label>Tanggal Akhir</label>
                    <div class="input-group date datetimepicker" id="tgl_akhir">
                        <input type="text" name="tgl_akhir" class="datepicker form-control text-center" placeholder="Tanggal Akhir" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div> -->


                <div class="filter-full">
                    <label>Karyawan</label>
                    <select class="select2 form-control nik">
                        <option selected value="">-- Pilih Karyawan --</option>
                        <?php foreach($karyawan as $k){ ?>
                            <option value="<?php echo $k['nik'] ?>"><?php echo ucwords(strtolower($k['nama'])) ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- Button -->
            <div class="filter-actions">
                <button class="btn btn-primary" onclick="up.filter_data(this, event)">
                    <i class="fa fa-search" style="margin-right: 10px;"></i> Filter
                </button>

                <button class="btn btn-primary" onclick="up.cetak_data_pdf(this, event)">
                    <i class="fa fa-print" style="margin-right: 10px;"></i> Cetak Data
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
        <div class="col-xs-12 no-padding list_data" style="overflow-x:scroll">
            <div class="spinner-load"></div>
        </div>
    </fieldset>

</div>

<div id="action" class="tab-pane fade tab-detail" role="tabpanel" style="padding-top: 10px;">
    <!-- < ?php if ($akses['a_submit'] == 1) { ?> -->
        <div class="panel panel-default">
            <div class="panel-heading"><span style="font-size:17px;">Tambah Data</span></div>
            <div class="panel-body">

                <div style="display:flex; flex-direction:column; gap:10px;">

                    <div style="display:flex; flex-direction:row;">
                        <span style="width:200px;">Nama Pengusul</span>
                        <span style="width:50px;">:</span>
                        <input type="text" class="form form-control pengusul" id_user="<?php echo $_SESSION['id_user'] ?>" value="<?php echo $_SESSION['detail_user']['nama_detuser']?>" disabled>
                    </div>

                    <div style="display:flex; flex-direction:row;">
                        <span style="width:200px;">Karyawan Yang di Usulkan</span>
                        <span style="width:50px;">:</span>
                        <!-- < ?php $outstanding_nik = is_array($outstanding) ? array_column($outstanding, 'karyawan') : []; ?> -->
                        <select class="select2 karyawan">
                            <option disabled selected> -- Pilih Karyawan -- </option>
                            <?php foreach ( $karyawan as $k ) {?>
                                <!-- < ?php $disabled = in_array($k['nik'], $outstanding_nik) ? 'disabled' : ''; ?> -->
                                <option <?php echo $disabled; ?>  atasan="<?php echo $k['atasan']; ?>" id_karyawan="<?php echo $k['id']; ?>" jabatan_val="<?php echo $k['jabatan']; ?>" jabatan_text="<?php echo $k['detail_jabatan']['nama']; ?>" level="<?php echo $k['detail_jabatan']['level']; ?>" value="<?php echo $k['nik']; ?>" ><?php echo ucwords(strtolower($k['nama'])) ?></option>
                            <?php }?>
                        </select>
                    </div>

                    <div style="display:flex; flex-direction:row;">
                        <span style="width:200px;">Kategori</span>
                        <span style="width:50px;">:</span>
                        <select class="select2 kategori">
                            <?php foreach ( $kategori as $k ) {?>
                                <option duration="<?php echo $k['duration']; ?>" value="<?php echo $k['kode_kategori']; ?>" ><?php echo ucwords(strtolower($k['nama_kategori'])) ?></option>
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
                    <button class="btn btn-secondary " onclick="window.location.href='hris/HrisStatusKaryawan' "> <i class="fa fa-angle-left" style="margin-right:10px;" aria-hidden="true"></i>  Back</button>
                    <button class="btn btn-primary " onclick="up.save(this, event)"> <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>  Save Data</button>
                </div>

            </div>

        </div>

    <!-- < ?php } else { ?> -->


    <!-- < ?php }  ?> -->

</div>
</div>



    <div class="panel panel-default edit_page">
        <div class="panel-heading"><span style="font-size:17px;">Edit Data</span></div>
        <div class="panel-body">

            <div style="display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; flex-direction:row; ">
                    <span style="width:200px;">Kode Usulan</span>
                    <span style="width:50px;">:</span>
                    <input type="text" class="form form-control kode_usulan" value="<?php echo $data_edit['kode'] ?>" readonly>
                </div>
                
                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">Tgl Usulan</span>
                    <span style="width:50px;">:</span>
                    <div class="input-group date datetimepicker" id="tgl_usulan">
                        <input type="text" name="tgl_usulan" value="<?php echo $data_edit['tanggal'] ?>" class="datepicker form-control text-center" placeholder="Tanggal Usulan" />
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
                            <option id_atasan="<?php echo $k['id']; ?>" jabatan_val="<?php echo $k['jabatan']; ?>" jabatan_text="<?php echo $k['detail_jabatan']['nama']; ?>" <?php echo $data_edit['pengusul'] == $k['nik'] ? 'selected' : '' ?> value="<?php echo $k['nik']; ?>" ><?php echo ucwords(strtolower($k['nama'])) ?></option>
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
                            <option atasan="<?php echo $k['atasan']; ?>" id_karyawan="<?php echo $k['id']; ?>" jabatan_val="<?php echo $k['jabatan']; ?>" jabatan_text="<?php echo $k['detail_jabatan']['nama']; ?>" level="<?php echo $k['detail_jabatan']['level']; ?>" <?php echo $data_edit['karyawan'] == $k['nik'] ? 'selected' : '' ?> value="<?php echo $k['nik']; ?>" ><?php echo ucwords(strtolower($k['nama'])) ?></option>
                        <?php }?>
                    </select>
                </div>

                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">Jabatan Asal</span>
                    <span style="width:50px;">:</span>
                    <input type="text" class="form form-control jabatan_asal">
                </div>

                 <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">&nbsp;</span>
                
                    <div style="display:flex; flex-direction:row; gap:10px; margin-left:6px;">
                        <div style="display:flex; flex-direction:column;">
                            <span>Perwakilan</span>
                                <select disabled class="select2 perwakilan_asal select_multiple" name="perwakilan_asal[]" multiple="multiple" >
                
                            </select>
                        </div>
                        <div style="display:flex; flex-direction:column;">
                            <span>Unit</span>
                                <select disabled class="select2 unit_asal select_multiple" name="unit_asal[]" multiple="multiple" >
                                
                            </select>
                        </div>
                    </div>
                    
                </div>


                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">Jabatan Tujuan </span>
                    <span style="width:50px;">:</span>
                    <select name="" id="" class="select2 jabatan_tujuan">
                        <option value=""> -- Pilih Jabatan -- </option>
                        <?php foreach ( $jabatan as $j ) {?>
                            <option level="<?php echo $j['level'] ?>" <?php echo $data_edit['jabatan_tujuan'] == $j['kode'] ? 'selected' : '' ?> value="<?php echo $j['kode']; ?>" ><?php echo $j['nama'] ?></option>
                        <?php }?>
                    </select>
                </div>

                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">&nbsp;</span>
                
                    <div style="display:flex; flex-direction:row; gap:10px; margin-left:6px;">
                       <?php
                            $selected_perwakilan_tujuan = explode(',', $data_edit['perwakilan_tujuan']);
                            $selected_unit_tujuan = explode(',', $data_edit['unit_tujuan']);
                        ?>

                        <div style="display:flex; flex-direction:column;">
                            <span>Perwakilan</span>

                            <?php
                                $isAllSelected = in_array('all', $selected_perwakilan_tujuan);
                            ?>

                            <select class="select2 perwakilan_tujuan select_multiple" name="perwakilan_tujuan[]" multiple="multiple" onchange="up.set_unit_by_wilayah(this, event)" >
                                <option value="all" <?php echo $isAllSelected ? 'selected' : '' ?>>  All </option>
                                <?php foreach($wilayah as $w){ ?>

                                    <option induk_wil="<?php echo $w['induk']?>" value="<?php echo $w['id']?>"
                                        <?php 
                                            if (in_array($w['id'], $selected_perwakilan_tujuan)) {
                                                echo 'selected';
                                            }
                                        ?> >

                                        <?php echo $w['nama']?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div style="display:flex; flex-direction:column;">
                            <span>Unit</span>

                            <select class="select2 unit_tujuan" name="unit_tujuan[]" multiple="multiple">
                                <option value="all"> All </option>
                                <?php foreach($unit as $u){ ?>

                                    <option induk="<?php echo $u['induk']?>" value="<?php echo $u['id']?>"
                                        <?php if (in_array($u['id'], $selected_unit_tujuan)) { 
                                            echo 'selected';
                                            } ?> >
                                        <?php echo $u['nama']?>

                                    </option>

                                <?php } ?>

                            </select>
                        </div>

                    </div>
                    
                </div>

                <div style="display:flex; flex-direction:row;">
                    <span style="width:200px;">Alasan</span>
                    <span style="width:50px;">:</span>
                    <textarea type="text" class="form form-control alasan"><?php echo $data_edit['alasan'] ?></textarea>
                </div>
                
            </div>
            <br>
            <div class="pull-right">
                <button class="btn btn-secondary " onclick="window.location.href='hris/UsulanPromosi' "> <i class="fa fa-angle-left" style="margin-right:10px;" aria-hidden="true"></i>  Back</button>
                <button class="btn btn-primary " onclick="up.update(this, event)"> <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>  Save Data</button>
            </div>

        </div>

    </div>

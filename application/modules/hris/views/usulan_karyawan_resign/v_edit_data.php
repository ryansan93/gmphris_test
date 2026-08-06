<input type="text" style="display:none" class="id_data" value="<?php echo $header[0]['id']?>">
<div class="row">


    <div class="col-md-6 col-sm-12">
        <div class="form-group">
            <label>Karyawan</label>
            <select name="nik" class="form-control select2 nik" required>
                <option value="">-- Pilih Karyawan --</option>

                <?php foreach($karyawan as $row){ ?>
                    <option <?php echo $header[0]['nik'] == $row['nik'] ? 'selected' : '' ?> value="<?php echo $row['nik']; ?>">
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
                <option  <?php echo $header[0]['jenis_resign'] == 'DO' ? 'selected' : '' ?> value="DO">Drop Out (DO)</option>
                <option  <?php echo $header[0]['jenis_resign'] == 'RESIGN' ? 'selected' : '' ?> value="RESIGN">Resign</option>
            </select>
        </div>
    </div>

    <div class="col-md-6 col-sm-12">
        <div class="form-group">
            <label>Tanggal Pengajuan Resign</label>
            <div class="input-group date datetimepicker tgl_pengajuan" id="tgl_pengajuan">
                <input type="text"
                    name="tgl_usulan"
                    value="<?php echo date('d M Y', strtotime($header[0]['tanggal_pengajuan'])); ?>"
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
                    value="<?php echo date('d M Y', strtotime($header[0]['tanggal_resign'])); ?>"
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

            <textarea name="alasan_resign" class="alasan_resign form-control" rows="4" placeholder="Masukkan alasan resign"required><?php echo $header[0]['alasan_resign'] ?></textarea>
        </div>
    </div>

</div>


<fieldset>
    <legend>Upload Attachment</legend>
    <button class="btn btn-sm btn-primary" onclick="ukr.add_attachment(this, event)"> <i class="fa fa-plus"></i> Tambah Upload</button>
    
    <div class="upload-area" data-attachment='<?= json_encode($detail); ?>' base_url="<?php echo base_url(); ?>">

    <?php if(!empty($detail)) { ?>
        <?php foreach($detail as $i => $d) { ?>

            <div class="upload-card"  data-index="<?php echo $i; ?>">

                <span style="color:red" id_attachment="<?php echo $d['id'] ?>" onclick="ukr.delete_attachment(this,event)">
                    <i class="fa fa-times"></i>
                </span>

                <div class="thumbnail">

                    <?php  $ext = pathinfo($d['file_attachment'], PATHINFO_EXTENSION) ?>

                    <?php if(in_array(strtolower($ext), ['jpg','jpeg','png'])) {?>

                        <img style="cursor:pointer;" onclick="ukr.show_attachment(this, event)" src="<?php echo base_url($d['file_path']); ?>">

                    <?php } else { ?>

                        <i class="fa fa-file-pdf-o" style="font-size:50px"></i>

                    <?php } ?>

                </div>

                <div class="nama-file">
                    <?php echo $d['nama_file']; ?>
                </div>

            </div>

        <?php } ?>
    <?php } else { ?>

        <div class="text-muted">
            Belum ada attachment
        </div>

    <?php } ?>
            

    </div>

</fieldset>


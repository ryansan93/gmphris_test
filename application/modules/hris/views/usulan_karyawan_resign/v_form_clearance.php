
<style>
    .form-row-flex {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }

    .form-row-flex .form-group {
        flex: 1;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .card{
        border :1px solid #ccc;
        border-radius:5px;
        padding:5px;
        display:flex;
        flex-direction: column;
        justify-content:center;
        align-items:center;
        cursor: pointer;
    }

    fieldset{
        margin-top:10px;
    }

    .card img {
        width: 150px;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
    }




    .card-facility{
        display:flex;
        align-items:center;
        gap:10px;
        margin-bottom:10px;
    }

    .nama{
        flex:2;
    }

    .row-kondisi{
        display:flex;
        gap:10px;
        flex:2;
    }

    .row-kondisi .kondisi_fasilitas{
        flex:1;
    }

    .row-kondisi .jumlah_fasilitas{
        width:100px;
    }

    .row-action{
        display:flex;
        align-items:center;
        gap:10px;
    }

    .upload-box{
        display:flex;
        align-items:center;
        justify-content:space-between;
        border:1px solid #ccc;
        border-radius:5px;
        padding:6px 10px;
        height:38px;
        width:170px;
    }

    .upload-icon{
        width:26px;
        height:26px;
        display:flex;
        align-items:center;
        justify-content:center;
        border:1px solid #ccc;
        border-radius:4px;
    }


    /* MOBILE */
    @media(max-width:768px){

        .card-facility{
            flex-direction:column;
            align-items:stretch;
            border-bottom : 1px solid black;
            padding:10px;
        }

        .nama{
            width:100%;
        }

        .row-kondisi{
            width:100%;
        }

        .row-kondisi .kondisi_fasilitas{
            flex:1;
        }

        .row-kondisi .jumlah_fasilitas{
            width:100px;
        }

        .row-action{
            width:100%;
        }

        .upload-box{
            flex:1;
        }

        .row-action button{
            width:40px;
        }
    }



    /* Mobile */
    @media (max-width: 576px) {
        .form-row-flex {
            flex-direction: column;
        }
    }
</style>

<fieldset>
    <legend style="width:50%;">Data Karyawan</legend>
    
    <?php if ( !empty($data_clearance) ) {?>
        <div class="form-group">
            <label>No. Document</label>
            <input type="text" class="form-control" value="<?php echo $data_clearance['document'] ?>" disabled>
        </div>

        <div class="form-row-flex">
            <div class="form-group">
                <label>Nama Karyawan</label>
                <input type="text" class="form-control" value="<?php echo $data_clearance['nama_karyawan'] ?>" disabled>
            </div>

            <div class="form-group">
                <label>NIK</label>
                <input type="text" class="form-control" value="<?php echo $data_clearance['nik'] ?>" disabled>
            </div>
        </div>

        <div class="form-group">
            <label>Jabatan</label>
            <input type="text" class="form-control" value="<?php echo $data_clearance['nama_jabatan'] ?>" disabled>
        </div>
    <?php } ?>
</fieldset>

<fieldset>
    <legend style="width:50%;">Attchment</legend>

    <?php if ( !empty($data_clearance) ) {?>
        <?php if (!empty($attachment)) { ?>
            <div class="form-row-flex">
                <?php foreach( $attachment as $a ){?>
                    <div class="card" onclick="window.open('<?php echo base_url() . $a['file_path']; ?>', '_blank')">
                        <img src="<?php echo base_url() . $a['file_path']?>" alt="" srcset="">
                        <label for=""><?php echo $a['nama_file'] ?></label>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

    <?php } ?>

</fieldset>

<fieldset>
    <legend style="width:50%;">Fasilitas Karyawan</legend>

    <div class="form-group">
        <label>Daftar Fasilitas yang didapat</label>
        <?php if (!empty($clearance)) { ?>
            <?php foreach ($clearance as $val) { ?>
                <div class="edit_clearance"></div>
                <div class="card-facility" data-id="<?php echo isset($val['id']) ? $val['id'] : ''; ?>" data-file-path="<?php echo base_url($val['path_file']); ?>" data-file-name="<?php echo $val['nama_file']; ?>">

                    <div class="nama">
                        <input type="text" class="nama_fasilitas form-control" value="<?php echo $val['nama_fasilitas']; ?>">
                    </div>

                    <div class="row-kondisi">
                        <input type="text" class="kondisi_fasilitas form-control" value="<?php echo $val['kondisi_fasilitas']; ?>">
                        <input type="number" onchange="if (this.value < 0) this.value = 0;" class="jumlah_fasilitas form-control" value="<?php echo $val['jumlah']; ?>">
                    </div>

                    <div class="row-action">
                        <div class="upload-box">
                            <span class="nama-file">
                               <?php if (!empty($val['nama_file'] ?? null)) : ?>
                                    <a href="javascript:void(0)"
                                    src="<?= base_url($val['path_file'] ?? '') ?>"
                                    onclick="ukr.show_attachment(this, event)">
                                        <?= htmlspecialchars($val['nama_file'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                <?php else : ?>
                                    Upload Attachment
                                <?php endif; ?>
                            </span>
                            <div class="upload-icon ml-auto" onclick="ukr.upload_attachment_clearance(this,event)">
                                <i class="fa fa-upload"></i>
                            </div>
                            <div class="data_attachment"></div>
                        </div>

                        <?php if (empty($data_clearance['verification_clearance_date'])): ?>
                            <button class="btn btn-primary" onclick="ukr.addFasilitas_edit(this,event)">
                                <i class="fa fa-plus"></i>
                            </button>
                            <button class="btn btn-danger" onclick="ukr.removeFasilitas_edit(this,event)">
                                <i class="fa fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="card-facility" data-id="0">
                <div class="nama">
                    <input placeholder="Nama Fasilitas" type="text" class="nama_fasilitas form-control" >
                </div>
                <div class="row-kondisi">
                    <input placeholder="Kondisi Fasilitas" type="text" class="kondisi_fasilitas form-control" >
                    <input placeholder="Jumlah" onchange="if (this.value < 0) this.value = 0;" type="number" class="jumlah_fasilitas form-control">
                </div>
                <div class="row-action">
                    <div class="upload-box">
                        <span class="nama-file">Upload Attachment</span>
                        <div class="upload-icon ml-auto" onclick="ukr.upload_attachment_clearance(this, event)">
                            <i class="fa fa-upload"></i>
                        </div>
                        <div class="data_attachment"></div>
                    </div>

                     <?php if ( !empty($data_clearance) ) {?>
                        <button class="btn btn-primary" onclick="ukr.addFasilitas_insert(this, event)"> <i class="fa fa-plus"></i> </button>
                        <button class="btn btn-danger" onclick="ukr.removeFasilitas(this, event)"> <i class="fa fa-trash"></i></button>
                    <?php } ?>

                </div>
            </div>

        <?php } ?>

    </div>

    <?php if (empty($data_clearance['verification_clearance_date'])): ?>
        <button class="btn btn-primary"
            id_data="<?php echo isset($data_clearance['id']) ? $data_clearance['id'] : ''; ?>"
            onclick="ukr.save_clearance(this, event)">
            Save Clearance
        </button>
    <?php endif; ?>
</fieldset>

    

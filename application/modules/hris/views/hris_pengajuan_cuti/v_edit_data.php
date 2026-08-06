<input type="hidden" class="check_tanggal" tanggal="<?php echo $data['tanggal_mulai'] ?>">

<form id="form-pengajuan-cuti" class="form-pengajuan-cuti">
    <input type="hidden" name="id" value="<?php echo isset($data['id']) ? $data['id'] : ''; ?>">

    <div class="form-group">
        <label>Karyawan</label>
        <select id="select_nik" name="nik" <?php echo !empty($nik_login) ? 'disabled' : ''; ?> class="select2 nik form-control" onchange="pc.setDataKaryawan(this, event)" <?php echo isset($data['tanggal_mulai']) ? '': 'onchange="pc.checkTanggalPengajuan(this, event);"'; ?>>
            <option value="">Pilih Karyawan</option>
            <?php foreach($karyawan as $k){?>
                <option  jabatan="<?php echo $k['nama_jabatan'] ?>" <?php echo (isset($data['nik']) && $data['nik'] == $k['nik']) ? 'selected' : ''; ?>  value="<?php echo $k['nik']; ?>"> <?php echo ucwords(strtolower($k['nama'])); ?></option>
            <?php } ?>
        </select>
        <!-- <input type="text" name="nik" class="form-control" value="<?php echo isset($data['nik']) ? $data['nik'] : (isset($nik_login) ? $nik_login : ''); ?>" required> -->
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>NIK</label>
            <input type="text" name="nama" class="nik_karyawan form-control" disabled required value="<?php echo isset($data['nik']) ? $data['nik'] : ''; ?>">
        </div>
        <div class="form-group col-md-6">
            <label>Jabatan</label>
            <input type="text" name="jabatan" class="jabatan form-control" disabled required value="<?php echo isset($data['nama_jabatan']) ? $data['nama_jabatan'] : ''; ?>">
        </div>
        <!-- <div class="form-group col-md-1">
            <label>Sisa Cuti</label>
            <input type="text" name="sisa_cuti" class="sisa_cuti form-control" disabled required value="<?php echo isset($data['sisa_cuti']) ? (int)$data['sisa_cuti'] : ''; ?>">
        </div>
        <div class="form-group col-md-1">
            <label>Cuti Terpakai</label>
            <input type="text" name="cuti_terpakai" class="cuti_terpakai form-control" disabled required value="<?php echo isset($data['cuti_terpakai']) ? (int)$data['cuti_terpakai'] : ''; ?>">
        </div> -->

    </div>

    <div class="form-group">
        <label>Jenis Cuti</label>
        <select name="jenis_cuti" class="select2 form-control" onchange="pc.checkJumlahLibur(); pc.checkTanggalPengajuanEdit();">
            <option value="cuti" <?php echo (isset($data['jenis_cuti']) && $data['jenis_cuti']=='cuti')? 'selected':''; ?>>Cuti</option>
            <option value="cuti_sakit" <?php echo (isset($data['jenis_cuti']) && $data['jenis_cuti']=='cuti_sakit')? 'selected':''; ?>>Cuti Sakit</option>
            <option value="cuti_force_majeure" <?php echo (isset($data['jenis_cuti']) && $data['jenis_cuti']=='cuti_force_majeure')? 'selected':''; ?>>Cuti Force Majeure</option>
            <option value="cuti_jatah_liburan" <?php echo (isset($data['jenis_cuti']) && $data['jenis_cuti']=='cuti_jatah_liburan')? 'selected':''; ?>>Cuti Jatah Liburan</option>
        </select>
    </div>

    <div class="form-row">
        <div class="form-group col-md-5">
            <label>Tanggal Mulai</label>
            <input type="text" autocomplete="off"
                id="tanggal_mulai_edit"
                name="tanggal_mulai"
                class="form-control date"
                value="<?php echo !empty($data['tanggal_mulai']) ? date('d M Y', strtotime($data['tanggal_mulai'])) : ''; ?>"
                required>
        </div>
        <div class="form-group col-md-5">
            <label>Tanggal Selesai</label>
            <input type="text" autocomplete="off"
                id="tanggal_selesai_edit"
                name="tanggal_selesai"
                class="form-control date"
                value="<?php echo !empty($data['tanggal_selesai']) ? date('d M Y', strtotime($data['tanggal_selesai'])) : ''; ?>"
                required>
        </div>

        <div class="form-group col-md-2">
            <label>Jumlah Hari</label>
            <input type="int" disabled id="jumlah_hari" name="jumlah_hari" class="form-control" value="<?php echo isset($data['jumlah_hari']) && $data['jumlah_hari'] != ''  ? $data['jumlah_hari']  : ''; ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label>Alasan</label>
        <textarea name="alasan" style="height: 70px;" class="form-control"><?php echo isset($data['alasan']) ? $data['alasan'] : ''; ?></textarea>
    </div>

    <div class="form-group">
        <label>Attachment</label>
        <input type="file" name="attachment[]" multiple class="form-control">
        <div id="attachment-thumbs" class="mt-2 d-flex flex-row flex-nowrap overflow-auto">
            <div id="attachment-existing" class="d-flex flex-row flex-nowrap">
                <?php if (!empty($data['id']) && !empty($attachments)): ?>
                    <?php foreach($attachments as $att): ?>
                        <div class="m-1 p-1 border rounded text-center existing-attachment" data-id="<?php echo $att['id']; ?>" style="width:120px;flex:0 0 auto;">
                            <div style="height:70px;overflow:hidden;margin-bottom:6px">
                                <?php $ext = pathinfo($att['nama_file'], PATHINFO_EXTENSION); ?>
                                <?php if (in_array(strtolower($ext), ['jpg','jpeg','png','gif'])): ?>
                                    <img src="<?php echo base_url($att['file_path']); ?>" style="max-width:100%;max-height:70px;display:block;margin:0 auto;"/>
                                <?php elseif (strtolower($ext) === 'pdf'): ?>
                                    <i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="fa fa-file-o fa-2x" aria-hidden="true"></i>
                                <?php endif; ?>
                            </div>
                            <!-- <div class="small text-truncate" title="< php echo $att['nama_file']; ?>">< ?php echo $att['nama_file']; ?></div> -->
                            <!-- <div class="small text-muted">< ?php echo round(filesize(FCPATH . $att['file_path'])/1024); ?> KB</div> -->
                            <div>
                                <a href="<?php echo base_url($att['file_path']); ?>" class="btn btn-sm btn-primary" target="_blank">Buka</a>
                                <button type="button" class="btn btn-sm btn-danger text-danger btn-delete-existing" data-id="<?php echo $att['id']; ?>"><i style="color:white;" class="fa fa-trash"></i></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div  class="attachment-new d-flex flex-row flex-nowrap"></div>
        </div>
    </div>

    <div class="form-group">
        <button type="button" class="btn-save btn btn-success" onclick="pc.savePengajuan()">Simpan</button>
    </div>
    <input type="hidden" id="base_controller" value="<?php echo site_url('hris/PengajuanCuti'); ?>">
</form>

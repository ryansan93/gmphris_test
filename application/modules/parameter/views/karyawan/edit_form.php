<style>
	/* ===== Form Rapi - Label & Input Sejajar ===== */
.form-rapi .form-group {
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.form-rapi .label-rapi {
    text-align: right;
    padding-right: 15px;
    font-weight: 500;
    color: #495057;
    line-height: 34px; /* sejajar dengan tinggi input */
}

.form-rapi .input-rapi {
    padding-left: 0;
}

.form-rapi .form-control {
    border-radius: 4px;
    border: 1px solid #ced4da;
    transition: all 0.2s ease;
}

.form-rapi .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
}

/* ===== Select2 Styling (agar sejajar juga) ===== */
.form-rapi .select2-container {
    width: 100% !important;
}

.form-rapi .select2-selection--multiple {
    min-height: 34px !important;
    border-radius: 4px !important;
}

/* ===== Tombol Update ===== */
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    padding: 8px 20px;
    border-radius: 4px;
    font-weight: 500;
}

.btn-primary:hover {
    background-color: #0069d9;
    border-color: #0062cc;
}

.btn-primary i {
    margin-right: 5px;
}

/* ===== Container Tag Unit ===== */
.unit-tag-container {
    border: 1px solid #d1d5db;
    padding: 8px 10px;
    border-radius: 5px;
    background: #f9fafb;
    min-height: 42px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
    transition: all 0.2s ease;
}


/* ===== Tag / Badge Unit ===== */
.unit-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: #f9fafb;
    color: #101010;
    border-radius:5px;
    font-size: 12px;
    font-weight: 500;
    border : 1px solid grey;
    transition: all 0.2s ease;
}

.unit-tag:hover {
    transform: translateY(-1px);
    cursor: pointer;
}

.unit-tag i {
    font-size: 12px;
    opacity: 0.9;
}

/* ===== Responsive ===== */
@media (max-width: 991px) {
    .form-rapi .label-rapi {
        text-align: left !important;
        padding-bottom: 5px;
    }
    .form-rapi .form-group {
        flex-direction: column;
        align-items: flex-start;
    }
    .form-rapi .input-rapi {
        width: 100%;
    }

	.unit-tag-container {
        padding: 6px 8px;
    }
    .unit-tag {
        font-size: 12px;
        padding: 4px 10px;
    }
}
</style>

<div class="row">
    <div class="col-lg-12" style="padding-bottom: 0px;">
        <form role="form" class="form-horizontal form-rapi body">
            
            <!-- Nama Pegawai -->
            <div class="form-group d-flex align-items-center">
                <div class="col-lg-2 text-left label-rapi">
                    <span>Nama Pegawai</span>
                </div>
                <div class="col-lg-10 input-rapi">
                    <input type="text" placeholder="Nama Pegawai" class="form-control nama_pegawai" data-required="1" value="<?php echo html_escape($data['nama']); ?>">
                </div>
            </div>

            <!-- Jabatan -->
            <div class="form-group d-flex align-items-center">
                <div class="col-lg-2 text-left label-rapi">
                    <span>Jabatan</span>
                </div>
                <div class="col-lg-10 input-rapi">
                    <select class="form-control jabatan" disabled onchange="pegawai.set_disable_by_jabatan(this)" data-required="1">
                        <option value="">-- Pilih Jabatan --</option>
                        <?php
                            $CI = & get_instance();
                            $jabatan = $CI->config->item('jabatan');
                        ?>
                        <?php foreach ($jabatan as $key => $val): ?>
                            <?php
                                $selected = '';
                                if ( $key == $data['jabatan'] ) {
                                    $selected = 'selected';
                                }
                            ?>
                            <option value="<?php echo html_escape($key); ?>" <?php echo $selected; ?> ><?php echo html_escape($val); ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <!-- Atasan -->
            <div class="form-group d-flex align-items-center">
                <div class="col-lg-2 text-left label-rapi">
                    <span>Atasan</span>
                </div>
                <div class="col-lg-10 input-rapi">
                    <!-- <input type="text" class="form form-control atasan" value="< ?php echo $list_karyawan[$data['atasan']]['nama']; ?>"> -->

                    <select class="select2 atasan" id="" disabled>
                        <?php foreach($list_karyawan as $index => $k ) { ?>
                           <option <?php echo $k['nik'] == $data['atasan_nik'] ? 'selected' : ''?> value="<?php echo $index?>"><?php echo $k['nama']?></option>         
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- Marketing -->
            <div class="form-group d-flex align-items-center">
                <div class="col-lg-2 text-left label-rapi">
                    <span>Marketing</span>
                </div>
                <div class="col-lg-10 input-rapi">
                    <select class="form-control marketing" data-required="1">
                        <option value="">-- Pilih Marketing --</option>
                        <option value="all" <?php echo ($data['marketing'] == "all") ? 'selected' : ''; ?> >All</option>
                        <option value="1" <?php echo ($data['marketing'] == "1") ? 'selected' : ''; ?> >1</option>
                        <option value="2" <?php echo ($data['marketing'] == "2") ? 'selected' : ''; ?> >2</option>
                        <option value="3" <?php echo ($data['marketing'] == "3") ? 'selected' : ''; ?> >3</option>
                        <option value="4" <?php echo ($data['marketing'] == "4") ? 'selected' : ''; ?> >4</option>
                    </select>
                </div>
            </div>

            <!-- Koordinator -->
            <div class="form-group d-flex align-items-center">
                <div class="col-lg-2 text-left label-rapi">
                    <span>Koordinator</span>
                </div>
                <div class="col-lg-10 input-rapi">
                    <select class="form-control koordinator" data-required="1">
                        <option value="">-- Pilih Koordinator --</option>
                        <option value="all" <?php echo ($data['kordinator'] == "all") ? 'selected' : ''; ?> >All</option>
                        <option value="1" <?php echo ($data['kordinator'] == "1") ? 'selected' : ''; ?> >1</option>
                        <option value="2" <?php echo ($data['kordinator'] == "2") ? 'selected' : ''; ?> >2</option>
                        <option value="3" <?php echo ($data['kordinator'] == "3") ? 'selected' : ''; ?> >3</option>
                        <option value="4" <?php echo ($data['kordinator'] == "4") ? 'selected' : ''; ?> >4</option>
                    </select>
                </div>
            </div>

            <!-- Wilayah -->
            <div class="form-group d-flex align-items-center">
				<div class="col-lg-2 text-left label-rapi">
					<span>Wilayah</span>
				</div>
				<div class="col-lg-10 input-rapi">
					<div class="unit-tag-container">
						<?php if (!empty($data['d_wilayah'])): ?>
							<?php foreach ($data['d_wilayah'] as $w): ?>
								<?php 
									$id_wilayah = $w['wilayah'] ?? ($w['id'] ?? null);
									$nama_wilayah = isset($list_wilayah[$id_wilayah]) ? $list_wilayah[$id_wilayah]['nama'] : 'All';
								?>
								<div class="unit-tag">
									<span><?php echo html_escape($nama_wilayah); ?></span>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<span class="text-muted"><i>Belum ada wilayah dipilih</i></span>
						<?php endif; ?>
					</div>
				</div>
			</div>

            <!-- Unit -->
            <div class="form-group d-flex align-items-center">
				<div class="col-lg-2 text-left label-rapi">
					<span>Unit</span>
				</div>
				<div class="col-lg-10 input-rapi">
					<div class="unit-tag-container">
						<?php if (!empty($data['unit'])): ?>
							<?php foreach ($data['unit'] as $u): ?>
								<?php 
									$nama_unit = isset($list_unit[$u['unit']]) ? $list_unit[$u['unit']]['nama'] : (isset($list_unit[$u['id']]) ? $list_unit[$u['id']]['nama'] : 'All');
								?>
								<div class="unit-tag">
									<span><?php echo html_escape($nama_unit); ?></span>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<span class="text-muted"><i>Belum ada unit dipilih</i></span>
						<?php endif; ?>
					</div>
				</div>
			</div>

            <!-- Level -->
            <div class="form-group d-flex align-items-center">
                <div class="col-lg-2 text-left label-rapi">
                    <span>Level</span>
                </div>
                <div class="col-lg-10 input-rapi">
                    <select class="form-control level" data-required="1" disabled>
                        <option value="">-- Pilih Level --</option>
                        <option value="1" <?php echo ($data['level'] == '1') ? 'selected' : ''; ?> >1</option>
                        <option value="2" <?php echo ($data['level'] == '2') ? 'selected' : ''; ?> >2</option>
                        <option value="3" <?php echo ($data['level'] == '3') ? 'selected' : ''; ?> >3</option>
                        <option value="4" <?php echo ($data['level'] == '4') ? 'selected' : ''; ?> >4</option>
                        <option value="5" <?php echo ($data['level'] == '5') ? 'selected' : ''; ?> >5</option>
                        <option value="6" <?php echo ($data['level'] == '6') ? 'selected' : ''; ?> >6</option>
                        <option value="7" <?php echo ($data['level'] == '7') ? 'selected' : ''; ?> >7</option>
                    </select>
                </div>
            </div>

        </form>
    </div>

    <div class="col-md-12 no-padding"><hr></div>

    <div class="col-lg-12 no-padding">
        <div class="col-lg-12 text-right">
			<button type="button" class="btn btn-default" onclick="$('.bootbox').modal('hide');">
				<i class="fa fa-times"></i> Tutup
			</button>
            <button type="button" class="btn btn-primary cursor-p" onclick="karyawan.edit(this)" data-id="<?php echo html_escape($data['id']); ?>" data-nik="<?php echo html_escape($data['nik']); ?>">
                <i class="fa fa-edit"></i> Update
            </button>
        </div>
    </div>
</div>
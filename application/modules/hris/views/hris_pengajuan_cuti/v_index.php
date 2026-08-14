<!-- < ?php cetak_r($nik_login, 1);  ?> -->
<style>
	/* Attachment thumbnail styles */
	.attachment-card { background: #fff; border: 1px solid #ececec; padding: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); }
	.attachment-thumb img { display:block; max-width:100%; max-height:100%; }
	.existing-attachment .remove-btn { position: absolute; right:6px; top:6px; z-index:5; }
	.existing-attachment { position:relative; width:140px; padding:6px; background:#fff; border:1px solid #eee; margin-right:6px; box-shadow:0 2px 6px rgba(0,0,0,0.03); }
	.existing-attachment .small { display:block; text-align:left; max-width:120px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
	.cuti-table-container {
		background: #ffffff;
		box-shadow: 0 4px 14px rgba(15,23,42,0.05);
		overflow: hidden;
		border: 1px solid #f1f3f5;
	}

	
	.cuti-nik {
		font-family: 'SF Mono', 'Fira Code', monospace;
		background: #f1f3f5;
		padding: 4px 10px;
		border-radius: 6px;
		font-size: 12px;
		font-weight: 600;
		color: #495057;
		display: inline-block;
	}

	.cuti-name {
		font-weight: 600;
		color: #1a2332;
		font-size: 14px;
	}

	.cuti-date {
		font-size: 13px;
		color: #495057;
		white-space: nowrap;
	}

	.cuti-jenis {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 5px 12px;
		border-radius: 20px;
		font-size: 12px;
		font-weight: 600;
		background: #f0f4ff;
		color: #3b5bdb;
		border: 1px solid #d0dbf5;
	}

	.cuti-jenis.jenis-sakit {
		background: #fff4f0;
		color: #c23b3b;
		border-color: #ffd6d6;
	}

	.cuti-jenis.jenis-lain {
		background: #f0fff4;
		color: #2b8a3e;
		border-color: #b3e6c7;
	}

	.cuti-status {
		display: inline-flex;
		align-items: center;
		gap: 5px;
		padding: 5px 12px;
		border-radius: 20px;
		font-size: 11px;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: 0.3px;
	}

	.cuti-status.status-draft {
		background: #fff7e6;
		color: #a66b00;
		border: 1px solid #ffe4b3;
	}

	.cuti-status.status-pending {
		background: #e8f1ff;
		color: #0b539f;
		border: 1px solid #b3d4ff;
	}

	.cuti-status.status-approved {
		background: #e6f7ec;
		color: #0b6b2f;
		border: 1px solid #b3e6c7;
	}

	.cuti-status.status-reject {
		background: #fff0f0;
		color: #c23b3b;
		border: 1px solid #ffcccc;
	}

	.cuti-alasan {
		max-width: 180px;
		font-size: 12px;
		color: #6b7a85;
		line-height: 1.4;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.cuti-actions {
		display: flex;
		gap: 6px;
		justify-content: center;
	}

	.cuti-actions .btn {
		padding: 5px 12px;
		font-size: 12px;
		border-radius: 6px;
		font-weight: 500;
		transition: all 0.2s ease;
	}

	.cuti-actions .btn:hover {
		transform: translateY(-1px);
		box-shadow: 0 3px 8px rgba(0,0,0,0.12);
	}

	.cuti-actions .btn-primary {
		background: #1f75fe;
		border-color: #1f75fe;
	}

	.cuti-actions .btn-danger {
		background: #e03131;
		border-color: #e03131;
	}

	.cuti-empty {
		text-align: center;
		padding: 48px 20px;
		color: #8a96a3;
	}

	.cuti-empty i {
		font-size: 42px;
		opacity: 0.25;
		margin-bottom: 10px;
		display: block;
	}

	.cuti-empty span {
		font-size: 14px;
	}

	@media (max-width: 768px) {
		.cuti-table-container {
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
		}

		.cuti-alasan {
			max-width: 120px;
		}
	}
</style>

<div >
	<ul class="nav nav-tabs" id="tabPengajuanCuti" role="tablist">
		<li class="nav-item" style="width:50%; text-align:center;">
			<a class="nav-link active" id="riwayat-tab" data-toggle="tab" href="#riwayat" role="tab">Riwayat Data</a>
		</li>
		<?php if ($akses['a_submit'] == 1){?>
			<li class="nav-item" style="width:50%; text-align:center;">
				<a class="nav-link" id="tambah-tab" data-toggle="tab" href="#tambah" role="tab">Tambah Data</a>
			</li>
		<?php } ?>
	</ul>

	<div class="tab-content mt-3">
		<div class="tab-pane fade show active" id="riwayat" role="tabpanel">
			<div id="list-pengajuan">

 				<fieldset>
					<legend>Filter</legend>
					<div class="form-row">
						<div class="form-group col-md-4">
							<label>Status Pengajuan</label>
							<select id="filter_status" class="form-control">
								<option value="">-- Semua Status --</option>
								<option value="DRAFT">DRAFT</option>
								<option value="ACKNOWLEDGE">ACKNOWLEDGE</option>
								<option value="APPROVE">APPROVED</option>
								<option value="REJECT">REJECTED</option>
							</select>
						</div>
						<div class="form-group col-md-2" style="padding-top: 25px;">
							<button type="button" class="btn btn-primary btn-block" onclick="pc.applyFilter(this, event)">Filter</button>
						</div>
						<div class="form-group col-md-2" style="padding-top: 25px;">
							<button type="button" class="btn btn-secondary btn-block" onclick="pc.resetFilter()">Reset</button>
						</div>
					</div>
				</fieldset>
				<br>
				<fieldset>
					<legend>Data Pengajuan</legend>

					<div class="cuti-table-container">
						<table class="gmp-table" id="table-pengajuan">
							<thead>
								<tr>
									<th>NIK</th>
									<th>Karyawan</th>
									<th>Tgl Mulai</th>
									<th>Tgl Selesai</th>
									<th>Jumlah Hari</th>
									<th>Jenis</th>
									<th>Status</th>
									<th>Alasan</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php if(!empty($list)): ?>
									<?php foreach($list as $k => $v): ?>

										<?php 
											$jenis_cuti = [
												'cuti' => ['label' => 'Cuti', 'class' => ''],
												'cuti_sakit'   => ['label' => 'Cuti Sakit',   'class' => 'jenis-sakit'],
												'cuti_force_majeure'    => ['label' => 'Cuti Force Majeure', 'class' => 'jenis-lain'],
												'cuti_jatah_liburan'    => ['label' => 'Cuti Jatah Liburan', 'class' => 'jenis-lain'],
											];
											$jenis_info = $jenis_cuti[$v['jenis_cuti']] ?? ['label' => '-', 'class' => ''];

											/// Status class mapping
											$status = $v['status_pengajuan'];

											$status_class = 'status-draft';
											$status_icon  = '🟡';

											switch ($status) {
												case 1:
													$status_class = 'status-draft';
													$status_icon  = '🟡';
													break;

												case 2:
													$status_class = 'status-pending';
													$status_icon  = '🔵';
													break;

												case 3:
													$status_class = 'status-approved';
													$status_icon  = '🟢';
													break;

												case 4:
												case 5:
													$status_class = 'status-reject';
													$status_icon  = '🔴';
													break;
											}
										?>

										<tr>
											<td class="text-center">
												<span class="cuti-nik"><?php echo $v['nik']; ?></span>
											</td>
											<td style="white-space:nowrap;">
												<span class="cuti-name"><?php echo ucwords(strtolower($v['nama_karyawan'])); ?></span>
											</td>
											<td class="text-center">
												<span class="cuti-date"><?php echo tglIndonesia($v['tanggal_mulai'], '-', ' '); ?></span>
											</td>
											<td class="text-center">
												<span class="cuti-date"><?php echo tglIndonesia($v['tanggal_selesai'], '-', ' '); ?></span>
											</td>
											<td class="text-center">
												<span class="cuti-date">
													<?php
														$jumlah_hari = '-';
														if (isset($v['jumlah_hari']) && $v['jumlah_hari'] !== '') {
															$jumlah_hari = (int) $v['jumlah_hari'];
														}
														echo $jumlah_hari;
													?>
												</span>
											</td>
											<td class="text-center">
												<span class="cuti-jenis <?php echo $jenis_info['class']; ?>">
													<?php echo $jenis_info['label']; ?>
												</span>
											</td>
											<td class="text-center">
												<span class="cuti-status <?php echo $status_class; ?>">
													<span><?php echo $status_icon; ?></span>
													<?php 
														$map = [
															1 => 'DRAFT',
															2 => 'ACKNOWLEDGE',
															3 => 'APPROVED',
															4 => 'REJECT ATASAN',
															5 => 'REJECT HRD'
														];
													?>
													<span><?php echo $map[$v['status_pengajuan']] ?></span>
												</span>
											</td>
											<td>
												<div class="cuti-alasan" title="<?php echo htmlspecialchars($v['alasan']); ?>">
													<?php echo $v['alasan']; ?>
												</div>
											</td>
											<td class="text-center">
												<?php if ($v['status_pengajuan'] == 1): ?>
													<div class="cuti-actions">
														<button class="btn btn-primary" onclick="pc.editPengajuan('<?php echo $v['id']; ?>')" title="Edit">
															<i class="fa fa-edit"></i>
														</button>
														<button class="btn btn-danger" onclick="pc.deletePengajuan('<?php echo $v['id']; ?>','<?php echo tglIndonesia($v['tanggal_mulai'], '-', ' '); ?>')" title="Hapus">
															<i class="fa fa-trash"></i>
														</button>
													</div>
												<?php else: ?>
													<span style="color:#adb5bd;">—</span>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="9">
											<div class="cuti-empty">
												<i class="fa fa-calendar-times-o"></i>
												<span>Belum ada data pengajuan cuti</span>
											</div>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</fieldset>

			</div>
		</div>
		<div class="tab-pane fade" id="tambah" role="tabpanel">
			<div id="form-pengajuan">

				<form id="form-pengajuan-cuti" class="form-pengajuan-cuti">
					<input type="hidden" name="id" value="<?php echo isset($data['id']) ? $data['id'] : ''; ?>">
					<input type="hidden" name="nik_login" value="<?php echo isset($nik_login) ? $nik_login : ''; ?>">

					<!-- <div class="alert-notif">

					</div> -->

					<div class="form-group">
						<label>Karyawan</label>
						<select id="select_nik" name="nik" <?php echo !empty($nik_login) ? 'disabled' : ''; ?> class="select2 nik form-control" onchange="pc.setDataKaryawan(this, event)" <?php echo isset($data['tanggal_mulai']) ? '': 'onchange="pc.checkTanggalPengajuan(this, event);"'; ?>>
							<option value="">Pilih Karyawan</option>
							<?php foreach($karyawan as $k){?>
								<option <?php echo $nik_login == $k['nik'] ? 'selected' : ''; ?> cuti_terpakai="<?php echo $k['cuti_terpakai'] ?>" sisa_cuti="<?php echo $k['sisa_cuti'] ?>" jabatan="<?php echo $k['nama_jabatan'] ?>" <?php echo (isset($data['nik']) && $data['nik'] == $k['nik']) ? 'selected' : ''; ?>  value="<?php echo $k['nik']; ?>"> <?php echo ucwords(strtolower($k['nama'])); ?></option>
							<?php } ?>
						</select>
						<?php if (!empty($nik_login)): ?>
							<input type="hidden" name="nik" value="<?php echo $nik_login; ?>">
						<?php endif; ?>
						<!-- <input type="text" name="nik" class="form-control" value="<?php echo isset($data['nik']) ? $data['nik'] : (isset($nik_login) ? $nik_login : ''); ?>" required> -->
					</div>

					<div class="cuti-area">

						<div class="form-row">
							<div class="form-group col-md-6">
								<label>NIK</label>
								<input type="text" name="nama" class="nik_karyawan form-control" disabled required value="<?php echo isset($data['nik']) ? $data['nik'] : (isset($nik_login) ? $nik_login : ''); ?>">
							</div>
							<div class="form-group col-md-6">
								<label>Jabatan</label>
								<?php
								$init_jabatan = '';
								$init_nik = isset($data['nik']) ? $data['nik'] : (isset($nik_login) ? $nik_login : '');
								if (!empty($init_nik) && !empty($karyawan)){
									foreach($karyawan as $k){
										if ($k['nik'] == $init_nik){ $init_jabatan = $k['nama_jabatan']; break; }
									}
								}
								?>
								<input type="text" name="jabatan" class="jabatan form-control" disabled required value="<?php echo htmlspecialchars($init_jabatan); ?>">
							</div>
							<!-- <div class="form-group col-md-1">
								<label>Sisa Cuti</label>
								<input type="text" name="sisa_cuti" class="sisa_cuti form-control" disabled required value="<?php echo isset($data['sisa_cuti']) ? $data['sisa_cuti'] : ''; ?>">
							</div>
							<div class="form-group col-md-1">
								<label>Cuti Terpakai</label>
								<input type="text" name="cuti_terpakai" class="cuti_terpakai form-control" disabled required value="<?php echo isset($data['cuti_terpakai']) ? $data['cuti_terpakai'] : ''; ?>">
							</div> -->
						</div>
	
	
						<div class="form-group">
							<label>Jenis Cuti</label>
							<select name="jenis_cuti" class="select2 form-control">
								<option value="cuti" <?php echo (isset($data['jenis_cuti']) && $data['jenis_cuti']=='cuti')? 'selected':''; ?>>Cuti</option>
								<option value="cuti_sakit" <?php echo (isset($data['jenis_cuti']) && $data['jenis_cuti']=='cuti_sakit')? 'selected':''; ?>>Cuti Sakit</option>
								<option value="cuti_force_majeure" <?php echo (isset($data['jenis_cuti']) && $data['jenis_cuti']=='cuti_force_majeure')? 'selected':''; ?>>Cuti Force Majeure</option>
								<option value="cuti_jatah_liburan" <?php echo (isset($data['jenis_cuti']) && $data['jenis_cuti']=='cuti_jatah_liburan')? 'selected':''; ?>>Cuti Jatah Liburan</option>
							</select>
						</div>
	
						<div class="form-row">
							<div class="form-group col-md-5">
								<label>Tanggal Mulai</label>
								<input type="text" autocomplete="off" id="tanggal_mulai" <?php echo isset($data['tanggal_mulai']) ? '': 'onchange="pc.checkTanggalPengajuan(this, event);"'; ?> name="tanggal_mulai" class="form-control date" value="<?php echo isset($data['tanggal_mulai']) && $data['tanggal_mulai'] != ''  ? date('d M Y', strtotime($data['tanggal_mulai']))  : ''; ?>" required>						
							</div>
							<div class="form-group col-md-5">
								<label>Tanggal Selesai</label>
								<input type="text" autocomplete="off" id="tanggal_selesai" onchange="pc.checkJumlahLibur(this, event);" name="tanggal_selesai" class="form-control date" value="<?php echo isset($data['tanggal_selesai']) && $data['tanggal_selesai'] != ''  ? date('d M Y', strtotime($data['tanggal_selesai']))  : ''; ?>" required>
							</div>
							<div class="form-group col-md-2">
								<label>Jumlah Hari</label>
								<input type="int" disabled id="jumlah_hari" name="jumlah_hari" class="form-control date" value="<?php echo isset($data['tanggal_selesai']) && $data['tanggal_selesai'] != ''  ? date('d M Y', strtotime($data['tanggal_selesai']))  : ''; ?>" required>
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
								<div class="attachment-new d-flex flex-row flex-nowrap"></div>
							</div>
						</div>
	
						<div class="form-group">
							<button type="button" class="btn-save btn btn-success" onclick="pc.savePengajuan()">Simpan</button>
							<?php if (empty($data['id'])): ?>
								<button type="reset" class="btn btn-secondary">Reset</button>
							<?php endif; ?>
						</div>

						
					</div>

				</form>

			</div>
		</div>
	</div>
</div>
	
<script>
	var base_controller = '<?php echo site_url("hris/PengajuanCuti"); ?>';
</script>

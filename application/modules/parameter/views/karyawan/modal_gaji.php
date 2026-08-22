<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">History Jabatan</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding">
			
		</div>
		<div class="col-sm-12 no-padding">
			<small>
				<table class="table table-bordered" style="margin-bottom: 0px;">
					<thead>
						<tr>
							<th>Jabatan</th>
							<th>Unit</th>
							<th>Wilayah</th>
							<th>Mulai</th>
							<th>Selesai</th>
							<th>Gaji</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($data_history)) { ?>
							<?php foreach($data_history as $dh){ ?>
							<tr>
								<td><?php echo $dh['jabatan'] ?></td>
								<td><?php echo $dh['nama_unit'] ?></td>
								<td><?php echo $dh['nama_wilayah'] ?></td>
								<td style="white-space:nowrap;"><?php echo tglIndonesia($dh['tgl_mulai'], '-', ' ') ?></td>
								
								<!-- PERUBAHAN LOGIKA TANGGAL SELESAI -->
								<td style="white-space:nowrap;">
									<?php 
										$tgl_selesai = $dh['tgl_selesai'];
										// Cek jika kosong, 0000-00-00, atau tanggal selesai > hari ini (belum lewat)
										if (empty($tgl_selesai) || $tgl_selesai == '0000-00-00' || $tgl_selesai > date('Y-m-d')) {
											echo '-';
										} else {
											echo tglIndonesia($tgl_selesai, '-', ' ');
										}
									?>
								</td>
								<!-- ----------------------------------- -->

								<td>-</td>
							</tr>
							<?php } ?>
						<?php } else { ?>
							<tr>
								<td colspan="6" class="text-center">Tidak ada data</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</small>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
		</div>
	</div>
</div>
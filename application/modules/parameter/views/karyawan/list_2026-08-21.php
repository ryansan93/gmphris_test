<?php if ( count($data) > 0 ) { ?>
	<!-- <tr>
		<td colspan="12"><?php echo count($data) ?></td>
	</tr> -->
	<?php foreach ($data as $k_data => $v_data){ ?>
		<tr class="cursor-p search" title="Klik 2x untuk edit data" ondblclick="karyawan.edit_form(this)" data-id="<?php echo $v_data['id']; ?>">
			<td style="width:20px;"><?php echo $v_data['level']; ?></td>
			<td><?php echo $v_data['nik']; ?></td>
			<td><?php echo ucwords(strtolower($v_data['nama'])); ?></td>
			<td><?php echo !empty($v_data['nama_jabatan']) ? $v_data['nama_jabatan'] : '-'; ?></td>
			<td><?php echo !empty($v_data['nama_atasan']) ? ucwords(strtolower($v_data['nama_atasan'] )): '-'; ?></td>
			<td><?php echo ucfirst($v_data['marketing']); ?></td>
			<td><?php echo ucfirst($v_data['kordinator']); ?></td>
			<td><?php echo !empty($v_data['nama_wilayah']) ? $v_data['nama_wilayah'] : '-'; ?></td>
			<td><?php echo !empty($v_data['nama_unit']) ? $v_data['nama_unit'] : '-'; ?></td>
			<td><?php echo ( $v_data['status_aktif'] == 1 ) ? 'AKTIF' : 'NON AKTIF'; ?></td>
			<td><?php echo !empty($v_data['tgl_berlaku']) ? tglIndonesia($v_data['tgl_berlaku'], "-", " ") : '-'; ?></td>
			<td><button type="button" class="col-xs-12 btn btn-primary" onclick="pegawai.modalGaji(this)" data-nik="<?php echo $v_data['nik']; ?>"><i class="fa fa-usd"></i></button></td>
		</tr>
	<?php } ?>
<?php } else { ?>
	<tr>
		<td colspan="12">Data tidak ditemukan.</td>
	</tr>
<?php } ?>

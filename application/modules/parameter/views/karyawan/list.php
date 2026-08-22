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
			<!-- <td>< ?php echo ucfirst($v_data['marketing']); ?></td>
			<td>< ?php echo ucfirst($v_data['kordinator']); ?></td> -->
			<td>
				<?php 
				if (!empty($v_data['nama_wilayah'])) {
					$items = explode(', ', ucwords(strtolower($v_data['nama_wilayah'])));
					foreach ($items as $item) {
						echo '• ' . trim($item) . '<br>';
					}
				} else {
					echo '-';
				}
				?>
			</td>

			<td>
				<?php 
				if (!empty($v_data['nama_unit'])) {
					$items = explode(', ', ucwords(strtolower($v_data['nama_unit'])));
					foreach ($items as $item) {
						echo '• ' . trim($item) . '<br>';
					}
				} else {
					echo '-';
				}
				?>
			</td>
			
			<!-- ⭐ STATUS DENGAN DETEKSI MUTASI -->
			<td>
				<?php 
				// Mapping status
				$status_map = [
					1 => ['text' => 'AKTIF',     'class' => 'label-success', 'icon' => 'fa-check-circle'],
					2 => ['text' => 'MUTASI',    'class' => 'label-warning', 'icon' => 'fa-exchange'],
					0 => ['text' => 'NON AKTIF', 'class' => 'label-danger',  'icon' => 'fa-times-circle']
				];
				
				$status_code = isset($v_data['status_display']) ? $v_data['status_display'] : 0;
				$info = isset($status_map[$status_code]) ? $status_map[$status_code] : $status_map[0];
				?>
				
				<span class="label <?= $info['class'] ?>" 
					  title="<?= $info['text'] ?><?= !empty($v_data['jenis_mutasi']) ? ' - ' . $v_data['jenis_mutasi'] : '' ?>">
					<i class="fa <?= $info['icon'] ?>"></i> 
					<?= $info['text'] ?>
				</span>
				
				<?php if ($status_code == 2 && !empty($v_data['jenis_mutasi'])): ?>
					<br>
					<small class="text-muted">
						<i class="fa fa-arrow-right"></i> 
						<?= $v_data['jenis_mutasi'] ?> 
						<?php if (!empty($v_data['tgl_mutasi_berlaku'])): ?>
							<br>(<?= $v_data['tgl_mutasi_berlaku'] ?>)
						<?php endif; ?>
					</small>
				<?php endif; ?>
			</td>
			
			<td><?php echo !empty($v_data['tgl_berlaku']) ? tglIndonesia($v_data['tgl_berlaku'], "-", " ") : '-'; ?></td>
			<td><button type="button" class="col-xs-12 btn btn-primary" onclick="karyawan.modalGaji(this)" data-nik="<?php echo $v_data['nik']; ?>"><i class="fa fa-usd"></i></button></td>
		</tr>
	<?php } ?>
<?php } else { ?>
	<tr>
		<td colspan="12">Data tidak ditemukan.</td>
	</tr>
<?php } ?>
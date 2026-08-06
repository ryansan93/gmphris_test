<style>
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
</style>

<fieldset class="mb-3">
    <legend>Laporan Cuti Karyawan</legend>
    <!-- <div class="filter-bar mb-2 d-flex align-items-center justify-content-between"> -->
        <!-- <input id="filter-search" type="search" style="width:360px" class="form-control" placeholder="Cari Karyawan / NIK / Jabatan / Jenis Cuti"> -->
    <!-- </div> -->
    <button class="btn btn-secondary" onclick="report.export_excel();">Export Excel</button>

    <?php
        $rows = isset($list['list']) ? $list['list'] : $list;
    ?>

    <?php if (!empty($rows)) { ?>
        <div class="resign-table-scroll">
            <div class="pull-right" style="padding:10px; color:grey;">
                Total Data : <?php echo count($rows) ?>
            </div>
            <table class="resign-table">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Jenis Cuti</th>
                        <th style="text-align: center">Tanggal Mulai</th>
                        <th style="text-align: center">Tanggal Selesai</th>
                        <th style="text-align: center">Hari</th>
                        <th style="text-align: center">Status</th>
                    </tr>
                </thead>
                <tbody id="cuti-tbody">
                <?php foreach($rows as $i => $o){
                  
                    $map = [
                        1 => 'DRAFT',
                        2 => 'ACKNOWLEDGE',
                        3 => 'APPROVED',
                        4 => 'REJECT ATASAN',
                        5 => 'REJECT HRD'
                    ];
													
                    $nama = isset($o['nama_karyawan']) ? $o['nama_karyawan'] : '-';
                    $nik = isset($o['nik']) ? $o['nik'] : '-';
                    $jab = isset($o['nama_jabatan']) ? $o['nama_jabatan'] : '-';
                    $jenis = isset($o['jenis_cuti']) ? $o['jenis_cuti'] : '-';
                    $status = isset($o['status_pengajuan']) ? $map[$o['status_pengajuan']] : '-';
                    $mulai = !empty($o['tanggal_mulai']) ? tglIndonesia($o['tanggal_mulai'],'-',' ') : '-';
                    $selesai = !empty($o['tanggal_selesai']) ? tglIndonesia($o['tanggal_selesai'],'-',' ') : '-';
                    $hari = isset($o['jumlah_hari']) ? $o['jumlah_hari'] : '-';;
                    
                    $alasan = !empty($o['alasan']) ? htmlspecialchars($o['alasan']) : '<span class="text-muted">-</span>';
                ?>
                    <tr class="cuti-row"
                        data-name="<?= strtolower($nama); ?>"
                        data-nik="<?= strtolower($nik); ?>"
                        data-jabatan="<?= strtolower($jab); ?>"
                        data-jenis="<?= strtolower($jenis); ?>"
                        data-start-month="<?= !empty($o['tanggal_mulai']) ? date('m', strtotime($o['tanggal_mulai'])) : ''; ?>"
                        data-end-month="<?= !empty($o['tanggal_selesai']) ? date('m', strtotime($o['tanggal_selesai'])) : ''; ?>">
                        <td><a style="font-weight:600" href="javascript:void(0)" id_data="<?= $o['id']; ?>" onclick="report.show_detail_data(this, event)"><?= $nik; ?></a></td>
                        <td><?= ucwords(strtolower($nama)); ?></td>
                        <td><?= $jab; ?></td>
                        <td><?= ucwords(str_replace('_',' ',$jenis)); ?> - <?= $alasan; ?> </td>
                        <td style="text-align: center"><?= $mulai; ?></td>
                        <td style="text-align: center"><?= $selesai; ?></td>
                        <td style="text-align: center"><?= $hari; ?></td>

                        <?php 
                            
                            $status_class = 'status-draft';
                            $status_icon  = '🟡';

                            switch ($o['status_pengajuan']) {
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

                        <td style="text-align: center">                    
                            <span class="cuti-status <?php echo $status_class; ?>">
                                <span><?php echo $status_icon; ?></span>
                                <span><?php echo $status ?></span>
                            </span>
                        </td>

                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

    <?php } else { ?>

        <div class="empty-state text-center">
            <i class="fa fa-inbox fa-2x"></i>
            <div class="mt-2">Tidak ada data</div>
        </div>

    <?php } ?>

    <script>
    (function(){
        var search = document.getElementById('filter-search');
        var month = document.getElementById('filter-month');
        function apply(){
            var q = (search.value || '').toLowerCase();
            var m = (month ? month.value : 'all');
            document.querySelectorAll('.cuti-row').forEach(function(row){
                var text = (row.dataset.name||'') + ' ' + (row.dataset.nik||'') + ' ' + (row.dataset.jabatan||'') + ' ' + (row.dataset.jenis||'');
                var okSearch = text.indexOf(q) > -1;
                var okMonth = true;
                if (m !== 'all') {
                    okMonth = row.dataset.startMonth === m || row.dataset.endMonth === m;
                }
                row.style.display = (okSearch && okMonth) ? '' : 'none';
            });
        }
        if (search) search.addEventListener('keyup', apply);
        if (month) month.addEventListener('change', apply);
    })();
    </script>

</fieldset>
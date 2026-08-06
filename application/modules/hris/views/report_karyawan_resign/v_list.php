<fieldset class="mb-3">
    <legend>Laporan Resign</legend>
    <div class="filter-bar">
        <input id="filter-search" type="search" style="width:500px" class="form-control pull-right" placeholder="Cari Karyawan / NIK / Jabatan / Unit / Wilayah">
    </div>

    <button class="btn btn-secondary" onclick="report.export_excel()">Export Excel</button>
    
    <?php
    // $list = isset($list_usulan['list']) ? $list_usulan['list'] : $list_usulan;

    $statusList = [
        1 => 'DRAFT',
        2 => 'ACKNOWLEDGE',
        3 => 'APPROVED',
        4 => 'REJECT ATASAN',
        5 => 'REJECT HRD'
    ];
    ?>

    <div class="pull-right" style="padding:10px; color:grey;">
        Total Data : <?php echo count($list) ?>
    </div>

    <?php if (!empty($list)) { ?>
        
        <div style="margin-top:10px;" class="resign-table-wrapper">
            <div class="resign-table-scroll">
                <table class="resign-table">
                    <thead>
                        <tr>
                            <th>Dokumen</th>
                            <th>Karyawan</th>
                            <th>Penempatan</th>
                            <th>Jenis</th>
                            <th>Tgl Pengajuan</th>
                            <th>Tgl Resign</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Clearance</th>
                            <th>Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody id="resign-tbody">

                    <?php foreach($list as $idx => $o){ ?>

                        <?php
                            $nama  = trim($o['nama_karyawan']);
                            $kata  = explode(' ', $nama);
                            $inisial = strtoupper(
                                substr($kata[0],0,1).
                                (isset($kata[1]) ? substr($kata[1],0,1) : '')
                            );

                            $status = isset($statusList[$o['status']]) ? $statusList[$o['status']] : '-';

                            $statusClass = 'status-draft';
                            switch($o['status']){
                                case 2: $statusClass = 'status-ack'; break;
                                case 3: $statusClass = 'status-approved'; break;
                                case 4:
                                case 5: $statusClass = 'status-reject'; break;
                            }

                            $hasClearance = !empty($o['clearance_date']);
                            $clearanceLabel = $hasClearance
                                ? tglIndonesia($o['clearance_date'],'-',' ')
                                : '-';

                            $hasVerification = !empty($o['verification_clearance_date']);
                            $verificationLabel = $hasVerification
                                ? tglIndonesia($o['verification_clearance_date'],'-',' ')
                                : '-';
                        ?>

                        <!-- BARIS UTAMA -->
                        <tr class="resign-row"
                            data-idx="<?= $idx; ?>"
                            data-name="<?= strtolower($o['nama_karyawan']); ?>"
                            data-nik="<?= strtolower($o['nik']); ?>"
                            data-jabatan="<?= strtolower($o['nama_jabatan']); ?>"
                            data-unit="<?= strtolower(!empty($o['nama_unit']) ? $o['nama_unit'] : ''); ?>"
                            data-wilayah="<?= strtolower(!empty($o['nama_wilayah']) ? $o['nama_wilayah'] : ''); ?>"
                            data-status="<?= strtolower($status); ?>"
                            data-alasan="<?= strtolower(!empty($o['alasan_resign']) ? $o['alasan_resign'] : ''); ?>"
                            onclick="toggleDetail(<?= $idx; ?>)">

                         

                             <!-- Dokumen -->
                            <td style="white-space:nowrap;">
                                <a href="javascript:void(0)"
                                   class="doc-link"
                                   id_data="<?= $o['id'] ?? ''; ?>"
                                   onclick="event.stopPropagation(); report.show_detail_data(this, event);">
                                    <i class="fa fa-paperclip"></i>
                                    <?= !empty($o['document']) ? $o['document'] : '-'; ?>
                                </a>
                            </td>

                            <!-- Karyawan -->
                            <td>
                                <div class="emp-cell">
                                    
                                    <div class="emp-info">
                                        <div class="emp-name" title="<?= htmlspecialchars($nama); ?>">
                                            <?= ucwords(strtolower($nama)); ?>
                                        </div>
                                        <!-- <div class="emp-sub">
                                            < ?= $o['nik']; ?> &bull; < ?= $o['nama_jabatan']; ?>
                                        </div>
                                        <div class="emp-loc">
                                            <i class="fa fa-map-marker"></i>
                                            < ?= !empty($o['nama_unit']) ? $o['nama_unit'] : '-'; ?>
                                            &bull;
                                            < ?= !empty($o['nama_wilayah']) ? $o['nama_wilayah'] : '-'; ?>
                                        </div> -->
                                    </div>
                                </div>
                            </td>

                            <!-- Penempatan -->
                            <td style="white-space:nowrap;" class="date-cell">
                                <?= (!empty($o['nama_unit']) ? $o['nama_unit'] : '-') . ' - ' . (!empty($o['nama_wilayah']) ? $o['nama_wilayah'] : '-'); ?>
                            </td>

                            <td style="white-space:nowrap;" class="date-cell">
                                <?= (!empty($o['jenis_resign']) ? $o['jenis_resign'] : '-') ; ?>
                            </td>

                            <!-- Tgl Pengajuan -->
                            <td style="white-space:nowrap;" class="date-cell">
                                <?= !empty($o['tanggal_pengajuan']) ? tglIndonesia($o['tanggal_pengajuan'],'-',' ') : '-'; ?>
                            </td>

                            <!-- Tgl Resign -->
                            <td style="white-space:nowrap;" class="date-cell">
                                <?= !empty($o['tanggal_resign']) ? tglIndonesia($o['tanggal_resign'],'-',' ') : '-'; ?>
                            </td>

                            <!-- Alasan -->
                            <td>
                                <div class="reason-cell" title="<?= htmlspecialchars(!empty($o['alasan_resign']) ? $o['alasan_resign'] : ''); ?>">
                                    <?= !empty($o['alasan_resign']) ? $o['alasan_resign'] : '<span style="color:#cbd5e1;font-style:italic;">-</span>'; ?>
                                </div>
                            </td>

                            <!-- Status -->
                            <td style="white-space:nowrap;">
                                <span class="status-pill <?= $statusClass; ?>">
                                    <?= $status; ?> 
                                </span>
                            </td>

                            <!-- Clearance -->
                            <td style="white-space:nowrap;">
                                <?php if($o['status'] == 3 && $hasClearance){ ?>
                                    <span class="clearance-badge clearance-done">
                                        <i class="fa fa-check-circle"></i>
                                        <?= $clearanceLabel; ?>
                                    </span>
                                <?php } elseif($o['status'] == 3){ ?>
                                    <span class="clearance-badge clearance-pending">
                                        <i class="fa fa-clock-o"></i> Pending
                                    </span>
                                <?php } else { ?>
                                    <span class="clearance-na">-</span>
                                <?php } ?>
                            </td>

                            <!-- Verifikasi -->
                            <td style="white-space:nowrap;">
                                <?php if($hasVerification){ ?>
                                    <span class="clearance-badge clearance-done">
                                        <i class="fa fa-check-circle"></i>
                                        <?= $verificationLabel; ?>
                                    </span>
                                <?php } elseif($o['status'] == 3){ ?>
                                    <span class="clearance-badge clearance-pending">
                                        <i class="fa fa-clock-o"></i> Pending
                                    </span>
                                <?php } else { ?>
                                    <span class="clearance-na">-</span>
                                <?php } ?>
                            </td>

                          
                        </tr>

                        <!-- DETAIL ROW - CLASS NAME SUDAH DIUBAH
                        <tr class="resign-detail" id="resign-detail-<?= $idx; ?>">
                            <td colspan="10">
                                <div class="resign-detail-content">

                                    <div class="resign-detail-item">
                                        <label>Acknowledge Oleh</label>
                                        <span><?= !empty($o['ack_by']) ? $o['ack_by'] : '<span class="text-muted">Belum</span>'; ?></span>
                                    </div>

                                    <div class="resign-detail-item">
                                        <label>Tanggal Acknowledge</label>
                                        <span><?= !empty($o['ack_date']) ? tglIndonesia($o['ack_date'],'-',' ') : '<span class="text-muted">Belum</span>'; ?></span>
                                    </div>

                                    <div class="resign-detail-item">
                                        <label>Approved / Reject Oleh</label>
                                        <span><?= !empty($o['approved_by']) ? $o['approved_by'] : '<span class="text-muted">Belum</span>'; ?></span>
                                    </div>

                                    <div class="resign-detail-item">
                                        <label>Tanggal Approve / Reject</label>
                                        <span><?= !empty($o['approve_reject_date']) ? tglIndonesia($o['approve_reject_date'],'-',' ') : '<span class="text-muted">Belum</span>'; ?></span>
                                    </div>

                                    <div class="resign-detail-item">
                                        <label>Tanggal Clearance</label>
                                        <span><?= !empty($o['clearance_date']) ? tglIndonesia($o['clearance_date'],'-',' ') : '<span class="text-muted">Belum</span>'; ?></span>
                                    </div>

                                    <div class="resign-detail-item">
                                        <label>Verifikasi Clearance Oleh</label>
                                        <span><?= !empty($o['verification_by']) ? $o['verification_by'] : '<span class="text-muted">Belum</span>'; ?></span>
                                    </div>

                                    <div class="resign-detail-item">
                                        <label>Tanggal Verifikasi Clearance</label>
                                        <span><?= !empty($o['verification_clearance_date']) ? tglIndonesia($o['verification_clearance_date'],'-',' ') : '<span class="text-muted">Belum</span>'; ?></span>
                                    </div>

                                    <div class="resign-detail-item">
                                        <label>Alasan Resign</label>
                                        <span><?= !empty($o['alasan_resign']) ? $o['alasan_resign'] : '<span class="text-muted">Tidak ada keterangan</span>'; ?></span>
                                    </div>

                                </div>
                            </td>
                        </tr> -->

                    <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>

    <?php } else { ?>

        <div class="resign-table-wrapper">
            <div class="empty-state">
                <i class="fa fa-inbox"></i>
                <div class="mt-2">Tidak ada data</div>
            </div>
        </div>

    <?php } ?>

    <script>


    // Filter
    (function(){
        let search = document.getElementById('filter-search');
        let status = document.getElementById('filter-status');

        function apply(){
            let q  = search.value.toLowerCase();
            let st = status.value.toLowerCase();

            document.querySelectorAll('.resign-row').forEach(function(row){
                let text =
                    row.dataset.name + ' ' +
                    row.dataset.nik + ' ' +
                    row.dataset.jabatan + ' ' +
                    row.dataset.unit + ' ' +
                    row.dataset.wilayah + ' ' +
                    row.dataset.alasan;

                let okSearch = text.indexOf(q) > -1;
                let okStatus = (st === 'all') ? true : row.dataset.status.indexOf(st) > -1;
                let show     = okSearch ;
                // && okStatus;

                row.style.display = show ? '' : 'none';

                // Sembunyikan detail row juga
                let idx = row.dataset.idx;
                let detailRow = document.getElementById('resign-detail-' + idx);
                if (!show && detailRow) {
                    detailRow.classList.remove('show');
                    document.getElementById('toggle-icon-' + idx).classList.remove('open');
                }
            });
        }

        search.addEventListener('keyup', apply);
        status.addEventListener('change', apply);
    })();
    </script>

</fieldset>
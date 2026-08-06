<style>
/* =========================================
   Laporan Cuti — Filter & Kartu Pengajuan
   ========================================= */

/* ---------- Filter bar ---------- */
.filter-bar{
    display:flex;
    gap:10px;
    align-items:center;
    margin:12px 0 8px;
}
.filter-search{
    flex:1;
    min-width:0;
    padding:9px 12px;
    border:1px solid #d8dde3;
    border-radius:6px;
    font-size:13px;
    background:#fff;
    outline:none;
}
.filter-status{
    padding:9px 12px;
    border:1px solid #d8dde3;
    border-radius:6px;
    font-size:13px;
    background:#fff;
    outline:none;
}
.filter-search:focus,
.filter-status:focus{
    border-color:#7aa5f8;
    box-shadow:0 0 0 3px rgba(59,130,246,.12);
}
.filter-count{
    margin:0 0 10px;
    font-size:12px;
    color:#8a919c;
}

/* ---------- Kartu ---------- */
.leave-card{
    display:grid;
    grid-template-columns:1.6fr 1.3fr 1fr 1.5fr 1.1fr minmax(150px,auto) auto;
    gap:16px;
    align-items:center;
    margin-bottom:10px;
    padding:14px 16px;
    border:1px solid #e4e7ec;
    border-radius:8px;
    background:#fff;
    transition:border-color .15s ease, box-shadow .15s ease;
}
.leave-card:hover{
    border-color:#cdd5df;
    box-shadow:0 2px 10px rgba(16,24,40,.06);
}

/* Label kolom */
.leave-card .col-label{
    display:block;
    margin:0 0 4px;
    font-size:11px;
    font-weight:600;
    letter-spacing:.4px;
    text-transform:uppercase;
    color:#8a919c;
}

/* Karyawan */
.leave-meta{display:flex;flex-direction:column;min-width:0;}
.leave-name{
    font-size:14px;
    font-weight:600;
    line-height:1.4;
    color:#1f2937;
    word-break:break-word;
}
.leave-sub{
    margin-top:4px;
    font-size:12px;
    line-height:1.5;
    color:#667085;
    word-break:break-word;
}
.leave-sub a{color:#2563eb;font-weight:600;text-decoration:none;}
.leave-sub a:hover{text-decoration:underline;}

/* Tanggal / jenis / keterangan */
.leave-date,
.leave-type,
.leave-note{display:flex;flex-direction:column;min-width:0;}
.leave-date strong,
.leave-type strong{
    font-size:13px;
    font-weight:600;
    line-height:1.45;
    color:#1f2937;
    word-break:break-word;
}
.leave-duration{margin-top:3px;font-size:12px;color:#667085;}
.leave-reason{
    font-size:13px;
    line-height:1.5;
    color:#344054;
    word-break:break-word;
    display:-webkit-box;
    -webkit-line-clamp:3;
    -webkit-box-orient:vertical;
    overflow:hidden;
}

/* Status */
.leave-status{
    display:flex;
    flex-direction:column;
    align-items:flex-start;
    gap:6px;
    min-width:0;
}
.status-row{display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
.status-pill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:5px 12px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    letter-spacing:.3px;
    white-space:nowrap;
}
.status-pill.is-button{cursor:pointer;transition:filter .15s ease;}
.status-pill.is-button:hover{filter:brightness(.93);}
.status-approved{background:#e6f7ec;color:#0b6b2f;}
.status-ack{background:#e8f1ff;color:#0b539f;}
.status-draft{background:#fff7e6;color:#a66b00;}
.status-reject{background:#fff0f0;color:#c23b3b;}
.reject-note{font-size:11px;line-height:1.5;color:#8a919c;word-break:break-word;}

/* Tombol aksi */
.leave-actions{display:flex;flex-direction:column;gap:6px;justify-self:end;}
.leave-actions .btn{white-space:nowrap;}

/* Empty state */
.leave-empty{
    padding:36px 16px;
    border:1px dashed #d0d5dd;
    border-radius:8px;
    background:#fafbfc;
    text-align:center;
    font-size:13px;
    color:#8a919c;
}

.show-edit-note{
    color:blue;
}

/* ---------- Tablet : 3 kolom ---------- */
@media(max-width:1200px){
    .leave-card{
        grid-template-columns:1.5fr 1.2fr 1fr;
        gap:14px;
    }
    .leave-note{grid-column:span 2;}
    .leave-status{
        grid-column:3;
        align-items:flex-end;
        text-align:right;
    }
    .status-row{justify-content:flex-end;}
    .leave-actions{
        grid-column:1 / -1;
        flex-direction:row;
        margin-top:2px;
        padding-top:12px;
        border-top:1px dashed #e7eaee;
    }
}

/* ---------- Mobile : 2 kolom ---------- */
@media(max-width:768px){
    .filter-bar{flex-direction:column;align-items:stretch;}
    .filter-search,
    .filter-status{width:100%;}

    .leave-card{
        grid-template-columns:1fr 1fr;
        gap:12px;
        padding:14px;
    }
    .leave-meta,
    .leave-note,
    .leave-status,
    .leave-actions{grid-column:1 / -1;}

    .leave-status{
        align-items:flex-start;
        text-align:left;
    }
    .status-row{justify-content:flex-start;}

    .leave-actions{
        flex-direction:row;
        justify-self:stretch;
    }
    .leave-actions .btn{flex:1 1 0;}
}

/* ---------- Mobile kecil : 1 kolom ---------- */
@media(max-width:480px){
    .leave-card{grid-template-columns:1fr;}
    .leave-actions{flex-direction:column;}
    .leave-actions .btn{flex:none;width:100%;}
}
</style>

<fieldset class="mb-3">
    <legend style="width:50%;">Laporan Cuti</legend>

    <div class="filter-bar">
        <input id="filter-search" class="filter-search" type="search"
               placeholder="Cari nama, NIK, jabatan, jenis cuti, alasan, tanggal...">
        <select id="filter-status" class="filter-status">
            <option value="all">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="acknowledge">Acknowledge</option>
            <option value="approved">Approved</option>
            <option value="reject atasan">Reject Atasan</option>
            <option value="reject hrd">Reject HRD</option>
        </select>
    </div>

    <div class="filter-count" id="filter-count"></div>

    <?php if (!empty($pengajuan)) { ?>
        <?php foreach ($pengajuan as $o) { ?>
            <?php
                $status_raw = isset($o['status_pengajuan']) ? $o['status_pengajuan'] : '';
                $status_map = [1 => 'DRAFT', 2 => 'ACKNOWLEDGE', 3 => 'APPROVED', 4 => 'REJECT ATASAN', 5 => 'REJECT HRD'];
                $status = is_numeric($status_raw)
                    ? (isset($status_map[(int)$status_raw]) ? $status_map[(int)$status_raw] : (string)$status_raw)
                    : (string)$status_raw;

                $statusClass  = 'status-draft';
                $status_lower = strtolower($status);
                if (strpos($status_lower, 'acknowledge') !== false || strpos($status_lower, 'ack') !== false) $statusClass = 'status-ack';
                if (strpos($status_lower, 'approved') !== false) $statusClass = 'status-approved';
                if (strpos($status_lower, 'reject')   !== false) $statusClass = 'status-reject';

                $jenis_cuti = [
                    'cuti'               => 'Cuti',
                    'cuti_sakit'         => 'Cuti Sakit',
                    'cuti_force_majeure' => 'Cuti Force Majeure',
                    'cuti_jatah_liburan' => 'Cuti Jatah Liburan',
                ];
                $jenisText = isset($jenis_cuti[$o['jenis_cuti']]) ? $jenis_cuti[$o['jenis_cuti']] : '-';
                $alasan    = (isset($o['alasan']) && trim($o['alasan']) !== '') ? $o['alasan'] : '-';

                $tanggal_selesai      = isset($o['tanggal_selesai']) ? $o['tanggal_selesai'] : '';
                $tanggal_selesai_text = !empty($tanggal_selesai) ? tglIndonesia($tanggal_selesai, '-', ' ') : '-';

                $jumlah_hari = '-';
                if (isset($o['jumlah_hari']) && $o['jumlah_hari'] !== '') {
                    $jumlah_hari = (int)$o['jumlah_hari'] . ' hari';
                }

                // Hak aksi & revert
                $show_ack     = ($o['status_pengajuan'] == 1) && isset($akses['a_ack'])     && $akses['a_ack'] == 1;
                $show_approve = ($o['status_pengajuan'] == 2) && isset($akses['a_approve']) && $akses['a_approve'] == 1;
                $can_revert_draft = in_array($o['status_pengajuan'], [2, 4]) && isset($o['ack_by'])     && $o['ack_by'] == $config;
                $can_revert_ack   = in_array($o['status_pengajuan'], [3, 5]) && isset($o['approve_by']) && $o['approve_by'] == $config;
            ?>

            <div class="leave-card"
                 data-name="<?php echo htmlspecialchars(strtolower($o['nama_karyawan']), ENT_QUOTES); ?>"
                 data-nik="<?php echo htmlspecialchars(strtolower($o['nik']), ENT_QUOTES); ?>"
                 data-jabatan="<?php echo htmlspecialchars(strtolower($o['nama_jabatan']), ENT_QUOTES); ?>"
                 data-jenis="<?php echo htmlspecialchars(strtolower($jenisText), ENT_QUOTES); ?>"
                 data-status="<?php echo htmlspecialchars(strtolower($status), ENT_QUOTES); ?>"
                 data-alasan="<?php echo htmlspecialchars(strtolower($alasan), ENT_QUOTES); ?>"
                 data-selesai="<?php echo htmlspecialchars(strtolower($tanggal_selesai_text), ENT_QUOTES); ?>"
                 data-durasi="<?php echo htmlspecialchars(strtolower($jumlah_hari), ENT_QUOTES); ?>">

                <!-- Karyawan -->
                <div class="leave-meta">
                    <div class="leave-name"><?php echo htmlspecialchars(ucwords(strtolower($o['nama_karyawan']))); ?></div>
                    <div class="leave-sub">
                        <?php echo htmlspecialchars($o['nik']); ?> •
                        <?php echo htmlspecialchars($o['nama_jabatan']); ?> •
                        <a href="javascript:void(0)" id_data="<?php echo $o['id']; ?>" onclick="pc.showAttachment(this, event)">Attachment</a>
                    </div>
                    <?php if (!empty($o['edit_note'])) { ?>
                        <div class="leave-sub">
                            <i class="fa fa-edit"></i>
                            <?php 
                                $edit_note = htmlspecialchars($o['edit_note']); 
                                $words = preg_split('/\s+/', trim($edit_note));                                
                            ?>
                            <?php if (count($words) > 2) { ?>
                                <a href="#" class="show-edit-note" data-note="<?= htmlspecialchars($o['edit_note']) ?>"> <?php echo implode(' ', array_slice($words, 0, 2)); ?> ...</a>
                            <?php } else { ?>
                                    <?php echo $edit_note; ?>
                            <?php }?>
                        
                            <?php echo ' - ' . tglIndonesia($o['updated_at'], '-', ' ') ?>
                        </div>
                    <?php } ?>
                </div>

                <!-- Tanggal -->
                <div class="leave-date">
                    <small class="col-label">Tanggal</small>
                    <strong><?php echo tglIndonesia($o['tanggal_mulai'], '-', ' '); ?> - <?php echo $tanggal_selesai_text; ?></strong>
                    <span class="leave-duration">Durasi: <?php echo $jumlah_hari; ?></span>
                </div>

                <!-- Jenis cuti -->
                <div class="leave-type">
                    <small class="col-label">Jenis Cuti</small>
                    <strong><?php echo $jenisText; ?></strong>
                </div>

                <!-- Keterangan -->
                <div class="leave-note">
                    <small class="col-label">Keterangan</small>
                    <div class="leave-reason" title="<?php echo htmlspecialchars($alasan, ENT_QUOTES); ?>"><?php echo htmlspecialchars($alasan); ?></div>
                </div>

                <!-- Status -->
                <div class="leave-status">
                    <div class="status-row">
                        <span class="status-pill <?php echo $statusClass; ?>"><?php echo ucwords(strtolower(str_replace(['_', '-'], ' ', $status))); ?></span>

                        <?php if ($can_revert_draft) { ?>
                            <span class="status-pill <?php echo $statusClass; ?> is-button"
                                  revert="DRAFT" id_data="<?php echo $o['id']; ?>"
                                  revert_note="<?php echo isset($o['revert_note']) ? $o['revert_note'] : ''; ?>"
                                  onclick="pc.revert_status(this, event)"
                                  data-toggle="tooltip" data-placement="top" title="Kembalikan ke Draft">
                                <i class="fa fa-undo" aria-hidden="true"></i>
                            </span>
                        <?php } elseif ($can_revert_ack) { ?>
                            <span class="status-pill <?php echo $statusClass; ?> is-button"
                                  revert="ACK" id_data="<?php echo $o['id']; ?>"
                                  revert_note="<?php echo isset($o['revert_note']) ? $o['revert_note'] : ''; ?>"
                                  onclick="pc.revert_status(this, event)"
                                  data-toggle="tooltip" data-placement="top" title="Kembalikan ke Acknowledge">
                                <i class="fa fa-undo" aria-hidden="true"></i>
                            </span>
                        <?php } ?>
                    </div>

                    <?php if ($o['status_pengajuan'] == 4 || $o['status_pengajuan'] == 5) { ?>
                        <small class="reject-note">Keterangan Reject: <?php echo htmlspecialchars($o['keterangan_reject']); ?></small>
                    <?php } ?>
                </div>

                <!-- Aksi (hanya dirender jika ada tombol) -->
                <?php if ($show_ack || $show_approve) { ?>
                    <div class="leave-actions">
                        <?php if ($show_ack) { ?>
                            <button type="button" id_data="<?php echo $o['id']; ?>" class="btn btn-success" value="2" onclick="pc.keputusanPengajuan(this, event)"><i class="fa fa-check"></i> Acknowledge</button>
                            <button type="button" id_data="<?php echo $o['id']; ?>" class="btn btn-danger"  value="4" onclick="pc.keputusanPengajuan(this, event)"><i class="fa fa-times"></i> Reject</button>
                        <?php } else { ?>
                            <button type="button" id_data="<?php echo $o['id']; ?>" class="btn btn-success" value="3" onclick="pc.keputusanPengajuan(this, event)"><i class="fa fa-check"></i> Approved</button>
                            <button type="button" id_data="<?php echo $o['id']; ?>" class="btn btn-danger"  value="5" onclick="pc.keputusanPengajuan(this, event)"><i class="fa fa-times"></i> Reject</button>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

        <?php } ?>

        <div class="leave-empty" id="leave-no-result" style="display:none;">
            Tidak ada pengajuan yang cocok dengan filter.
        </div>
    <?php } else { ?>
        <div class="leave-empty">Belum ada data pengajuan cuti.</div>
    <?php } ?>
</fieldset>

<script>
(function(){
    var searchEl = document.getElementById('filter-search');
    var statusEl = document.getElementById('filter-status');
    var countEl  = document.getElementById('filter-count');
    var noResult = document.getElementById('leave-no-result');

    if (!searchEl || !statusEl) return;

    function normalize(s){ return (s || '').toString().toLowerCase(); }

    function applyFilter(){
        var q  = normalize(searchEl.value.trim());
        var st = normalize(statusEl.value.trim());
        var cards = document.querySelectorAll('.leave-card');

        if (!cards.length) {
            if (countEl) countEl.textContent = '';
            return;
        }

        var visible = 0;

        cards.forEach(function(card){
            var hay = [
                card.getAttribute('data-name'),
                card.getAttribute('data-nik'),
                card.getAttribute('data-jabatan'),
                card.getAttribute('data-jenis'),
                card.getAttribute('data-alasan'),
                card.getAttribute('data-selesai'),
                card.getAttribute('data-durasi')
            ].join(' ');

            var status = card.getAttribute('data-status') || '';

            var matchesQuery  = !q || hay.indexOf(q) !== -1;
            var matchesStatus = (st === 'all' || st === '') || status.indexOf(st) !== -1;
            var show = matchesQuery && matchesStatus;

            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (countEl) countEl.textContent = 'Menampilkan ' + visible + ' dari ' + cards.length + ' pengajuan';
        if (noResult) noResult.style.display = (visible === 0) ? '' : 'none';
    }

    searchEl.addEventListener('input', applyFilter);
    statusEl.addEventListener('change', applyFilter);
    applyFilter();
})();
</script>
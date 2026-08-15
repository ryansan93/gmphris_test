<style>
    .hlt-wrap {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* ===== LEGEND / KETERANGAN ===== */
    .hlt-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
        background: #fff;
        border: 1px solid #e2e8f0;
        /* border-radius: 12px; */
        padding: 10px 18px;
        margin-bottom: 14px;
        box-shadow: 0 2px 6px rgba(0,0,0,.03);
    }
    .hlt-legend-label {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .8px;
        font-weight: 700;
        color: #64748b;
        padding-right: 12px;
        border-right: 1px solid #e2e8f0;
    }
    .hlt-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: .82rem;
        color: #334155;
        font-weight: 500;
    }
    .hlt-dot {
        width: 14px; height: 14px;
        border-radius: 50%;
        border: 1px solid rgba(0,0,0,.15);
        flex-shrink: 0;
        box-shadow: inset 0 -2px 3px rgba(0,0,0,.08);
    }
    .hlt-dot-selected { background: #FFF9D6; }
    .hlt-dot-pending  { background: #FFD252; }
    .hlt-dot-done     { background: #C9FF9C; }

    /* ===== TABLE CARD ===== */
    .hlt-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        /* border-radius: 14px; */
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0,0,0,.04);
    }
    .hlt-scroll { overflow-x: auto; }
    .hlt-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1000px;
    }

    .hlt-table thead th {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        text-transform: uppercase;
        font-size: .7rem;
        letter-spacing: .6px;
        color: #475569;
        font-weight: 700;
        padding: 13px 16px;
        text-align: center;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
        position: sticky;
        top: 0;
    }
    .hlt-table thead th:first-child { text-align: center; }
    .hlt-table thead th.hlt-th-left { text-align: left; }

    .hlt-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: .88rem;
        color: #1e293b;
        vertical-align: middle;
        text-align: center;
        background: #fff;
        transition: background .15s;
    }
    .hlt-table tbody tr { transition: all .15s; position: relative; }
    .hlt-table tbody tr:hover td { background: #f8fafc; }
    .hlt-table tbody tr.hlt-row-selected td {
        background: #FFF9D6 !important;
    }
    .hlt-table tbody tr:last-child td { border-bottom: none; }

    /* Status indicator column */
    .hlt-status-cell {
        position: relative;
        padding: 0 !important;
        width: 6px !important;
        min-width: 6px;
    }
    .hlt-status-bar {
        position: absolute;
        top: 8px; bottom: 8px; left: 50%;
        transform: translateX(-50%);
        width: 6px;
        border-radius: 3px;
    }
    .hlt-status-bar.done    { background: #C9FF9C; box-shadow: 0 0 6px rgba(147,197,59,.5); }
    .hlt-status-bar.pending { background: #FFD252; box-shadow: 0 0 6px rgba(234,179,8,.5); }

    /* Kolom nama + avatar inisial */
    .hlt-user {
        display: flex;
        align-items: center;
        gap: 10px;
        text-align: left;
    }
    .hlt-avatar {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        color: #4f46e5;
        font-weight: 700;
        font-size: .85rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .hlt-name { font-weight: 600; color: #0f172a; }

    /* Document link */
    .hlt-doc-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #4f46e5;
        text-decoration: none;
        font-weight: 500;
        font-size: .82rem;
        padding: 4px 10px;
        border-radius: 6px;
        background: #eef2ff;
        transition: all .2s;
        white-space: nowrap;
    }
    .hlt-doc-link:hover {
        background: #6366f1;
        color: #fff;
        text-decoration: none;
    }
    .hlt-doc-empty { color: #94a3b8; font-style: italic; font-size: .85rem; }

    /* Status badge */
    .hlt-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .hlt-badge-active  { background: #dbeafe; color: #1e40af; }
    .hlt-badge-reject  { background: #fee2e2; color: #991b1b; }
    .hlt-badge-process { background: #fef3c7; color: #92400e; }

    /* Pengusul */
    .hlt-pengusul { text-align: left; font-size: .85rem; }
    .hlt-pengusul-name { font-weight: 600; color: #0f172a; }
    .hlt-pengusul-role { font-size: .75rem; color: #64748b; margin-top: 2px; }

    /* Link Form button */
    .hlt-link-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: .82rem;
        font-weight: 600;
        text-decoration: none;
        transition: all .2s;
        cursor: pointer;
        white-space: nowrap;
        border: 1px solid #c7d2fe;
        background: #eef2ff;
        color: #4f46e5;
    }
    .hlt-link-btn:hover {
        background: #6366f1;
        color: #fff;
        border-color: #6366f1;
        transform: translateY(-1px);
        text-decoration: none;
    }
    .hlt-link-btn.disabled {
        background: #f1f5f9;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Keputusan */
    .hlt-keputusan { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; }
    .hlt-btn-approve, .hlt-btn-reject {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: .78rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex; align-items: center; gap: 5px;
        border: 1px solid;
        transition: all .2s;
        white-space: nowrap;
    }
    .hlt-btn-approve {
        background: #d1fae5; color: #065f46; border-color: #a7f3d0;
    }
    .hlt-btn-approve:hover {
        background: #10b981; color: #fff; border-color: #10b981;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(16,185,129,.3);
    }
    .hlt-btn-reject {
        background: #fee2e2; color: #991b1b; border-color: #fecaca;
    }
    .hlt-btn-reject:hover {
        background: #ef4444; color: #fff; border-color: #ef4444;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(239,68,68,.3);
    }

    .hlt-reject-badge {
        cursor: pointer;
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px;
        background: #f7a7a7;
        color: #771818;
        border-radius: 10px;
        font-weight: 700;
        font-size: .78rem;
        border: 1px solid #ef9a9a;
        transition: all .2s;
    }
    .hlt-reject-badge:hover { background: #ef5350; color: #fff; border-color: #ef5350; }

    .hlt-date-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: .82rem;
        color: #065f46;
        background: #d1fae5;
        padding: 5px 12px;
        border-radius: 10px;
        font-weight: 500;
        border: 1px solid #a7f3d0;
        white-space: nowrap;
    }

    .hlt-dash { color: #cbd5e1; font-weight: 700; font-size: 1.2em; }

    /* Empty state */
    .hlt-empty {
        padding: 60px 20px;
        text-align: center;
        color: #94a3b8;
    }
    .hlt-empty i {
        font-size: 44px;
        color: #cbd5e1;
        display: block;
        margin-bottom: 12px;
    }
    .hlt-empty h4 { margin: 0 0 4px; color: #475569; font-weight: 600; }
    .hlt-empty p  { margin: 0; font-size: .88rem; }
</style>

<div class="hlt-wrap">

    <!-- ===== LEGEND ===== -->
    <div class="hlt-legend">
        <span class="hlt-legend-label">Keterangan</span>
        <span class="hlt-legend-item">
            <span class="hlt-dot hlt-dot-selected"></span> Data Terpilih
        </span>
        <span class="hlt-legend-item">
            <span class="hlt-dot hlt-dot-pending"></span> Belum isi form
        </span>
        <span class="hlt-legend-item">
            <span class="hlt-dot hlt-dot-done"></span> Sudah isi form
        </span>
    </div>

    <!-- ===== TABLE ===== -->
    <div class="hlt-card">
        <div class="hlt-scroll">
            <table class="hlt-table">
                <thead>
                    <tr>
                        <th style="width:6px;">#</th>
                        <th>Document</th>
                        <th class="hlt-th-left">Nama Kandidat</th>
                        <th>Status Kandidat</th>
                        <th class="hlt-th-left">Pengusul</th>
                        <th>Link Form</th>
                        <th>Keputusan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($list)) { ?>
                        <?php foreach ($list as $l) { 
                            $isSelected  = !empty($l['selected']);
                            $isActive    = ($l['is_active'] == 'NONACTIVE');
                            $statusBadge = ($l['status_kandidat'] == 3) ? 'hlt-badge-reject' : 'hlt-badge-active';
                            $statusText  = ($l['status_kandidat'] == 3) ? 'Ditolak' : $l['nama_status'];
                        ?>
                            <tr class="<?= $isSelected ? 'hlt-row-selected' : '' ?>">
                                <!-- Status indicator bar -->
                                <td class="hlt-status-cell">
                                    <span class="hlt-status-bar <?= $isActive ? 'done' : 'pending' ?>"></span>
                                </td>

                                <!-- Document -->
                                <td>
                                    <?php if (!empty($l['document'])) { ?>
                                        <a href="<?= base_url('hris/HrisKandidatBaru/show_document_kandidat?id=' . $l['id_data_karyawan']) ?>" 
                                           target="_blank" 
                                           class="hlt-doc-link">
                                            <i class="fa fa-file-text-o"></i> <?= htmlspecialchars($l['document']) ?>
                                        </a>
                                    <?php } else { ?>
                                        <span class="hlt-doc-empty">—</span>
                                    <?php } ?>
                                </td>

                                <!-- Nama Kandidat -->
                                <td>
                                    <div class="hlt-user">
                                        <div class="hlt-avatar"><?= strtoupper(substr($l['nama'] ?? '?', 0, 1)) ?></div>
                                        <div class="hlt-name"><?= htmlspecialchars($l['nama']) ?></div>
                                    </div>
                                </td>

                                <!-- Status Kandidat -->
                                <td>
                                    <span class="hlt-badge <?= $statusBadge ?>"><?= htmlspecialchars($statusText) ?></span>
                                </td>

                                <!-- Pengusul -->
                                <td>
                                    <div class="hlt-pengusul">
                                        <div class="hlt-pengusul-name"><?= ucwords(strtolower($l['nama_pengusul'])) ?></div>
                                        <div class="hlt-pengusul-role"><?= ucwords(strtolower($l['jabatan_pengusul'])) ?></div>
                                    </div>
                                </td>

                                <!-- Link Form -->
                                <td>
                                    <?php 
                                        $key        = "secretkey";
                                        $plaintext  = $l['kategori'] . '-' . $l['id_data_karyawan'];
                                        $encrypted  = openssl_encrypt($plaintext, "AES-128-ECB", $key);
                                        $url        = "http://localhost/recruitment-gmp/Form?kode=" . urlencode($encrypted);
                                        $isNonActive = ($l['is_active'] == 'NONACTIVE');
                                    ?>
                                    <a class="hlt-link-btn <?= $isNonActive ? 'disabled' : '' ?>"
                                    <?php if (!$isNonActive) echo 'url="' . $url . '" onclick="hf.copy_link(this, event)"'; ?>>
                                        <i class="fa fa-link"></i> Generate Link
                                    </a>
                                </td>

                                <!-- Keputusan -->
                                <td>
                                    <div class="hlt-keputusan">
                                        <?php if ($isActive) { ?>

                                            <?php if (!empty($l['keterangan_reject'])) { ?>
                                                <span class="hlt-reject-badge" 
                                                      title="Lihat Keterangan" 
                                                      keterangan="<?= htmlspecialchars($l['keterangan_reject']) ?>" 
                                                      onclick="hf.show_keterangan(this, event)">
                                                    <i class="fa fa-times-circle"></i> Reject
                                                </span>

                                            <?php } elseif (!empty($l['tgl_masuk'])) { ?>
                                                <span class="hlt-date-badge">
                                                    <i class="fa fa-calendar-check-o"></i> 
                                                    <?= tglIndonesia($l['tgl_masuk'], '-', ' ') ?>
                                                </span>

                                            <?php } else { ?>
                                                <button type="button" 
                                                        class="hlt-btn-approve" 
                                                        id_data="<?= $l['id_data_karyawan'] ?>" 
                                                        onclick="hf.keputusan_akhir(this, event, 1)">
                                                    <i class="fa fa-check"></i> Approve
                                                </button>
                                                <button type="button" 
                                                        class="hlt-btn-reject" 
                                                        id_data="<?= $l['id_data_karyawan'] ?>" 
                                                        onclick="hf.keputusan_akhir(this, event, 2)">
                                                    <i class="fa fa-times"></i> Reject
                                                </button>
                                            <?php } ?>

                                        <?php } else { ?>
                                            <span class="hlt-dash">—</span>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7">
                                <div class="hlt-empty">
                                    <i class="fa fa-inbox"></i>
                                    <h4>Tidak ada data</h4>
                                    <p>Belum ada kandidat yang tersedia.</p>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
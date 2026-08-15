<style>
    :root {
        --us-primary: #6366f1;
        --us-primary-light: #eef2ff;
        --us-draft: #f59e0b;
        --us-ack: #8b5cf6;
        --us-approve: #3b82f6;
        --us-reject: #ef4444;
        --us-done: #64748b;
        --us-border: #e2e8f0;
        --us-text: #1e293b;
        --us-muted: #64748b;
        --us-radius: 12px;
    }

    /* ===== FILTER PILLS ===== */
    .us-filter-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 16px 20px;
        background: #fff;
        border: 1px solid var(--us-border);
        border-radius: var(--us-radius);
        margin-bottom: 16px;
    }
    .us-filter-btn {
        flex: 1 1 120px;
        min-width: 110px;
        padding: 10px 16px;
        border-radius: 10px;
        border: 2px solid;
        font-weight: 700;
        font-size: .82rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all .2s;
        background: #fff;
        color: #334155;
        font-family: inherit;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .us-filter-btn:hover { transform: translateY(-1px); }

    .us-filter-btn.draft    { border-color: var(--us-draft);   color: var(--us-draft); }
    .us-filter-btn.ack      { border-color: var(--us-ack);     color: var(--us-ack); }
    .us-filter-btn.approve  { border-color: var(--us-approve); color: var(--us-approve); }
    .us-filter-btn.reject   { border-color: var(--us-reject);  color: var(--us-reject); }
    .us-filter-btn.done     { border-color: var(--us-done);    color: var(--us-done); }

    .us-filter-btn.active {
        color: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,.08);
    }
    .us-filter-btn.draft.active    { background: var(--us-draft); }
    .us-filter-btn.ack.active      { background: var(--us-ack); }
    .us-filter-btn.approve.active  { background: var(--us-approve); }
    .us-filter-btn.reject.active   { background: var(--us-reject); }
    .us-filter-btn.done.active     { background: var(--us-done); }

    .us-filter-count {
        background: rgba(255,255,255,.25);
        padding: 2px 8px;
        border-radius: 10px;
        font-size: .7rem;
        font-weight: 700;
    }
    .us-filter-btn:not(.active) .us-filter-count {
        background: rgba(0,0,0,.08);
    }

    /* ===== TABLE CARD ===== */
    .us-card {
        background: #fff;
        border: 1px solid var(--us-border);
        /* border-radius: var(--us-radius); */
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0,0,0,.04);
    }
    .us-scroll { overflow-x: auto; }
    .us-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1100px;
    }
    .us-table thead th {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        text-transform: uppercase;
        font-size: .7rem;
        letter-spacing: .6px;
        color: #475569;
        font-weight: 700;
        padding: 14px 16px;
        text-align: left;
        border-bottom: 2px solid var(--us-border);
        white-space: nowrap;
    }
    .us-table thead th.us-center { text-align: center; }

    .us-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: .88rem;
        color: var(--us-text);
        vertical-align: middle;
        background: #fff;
        transition: background .15s;
    }
    .us-table tbody tr { transition: all .15s; }
    .us-table tbody tr:hover td { background: #f8fafc; }
    .us-table tbody tr.us-row-selected td {
        background: #FFF9D6 !important;
    }
    .us-table tbody tr:last-child td { border-bottom: none; }

    /* Document column */
    .us-doc {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background: #fef3c7;
        color: #92400e;
        border-radius: 8px;
        font-weight: 600;
        font-size: .82rem;
        white-space: nowrap;
    }
    .us-doc i { color: #b45309; }

    /* Pengusul user cell */
    .us-user { display: flex; align-items: center; gap: 10px; }
    .us-avatar {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        color: #4f46e5;
        font-weight: 700;
        font-size: .85rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .us-user-name { font-weight: 600; white-space: nowrap; }

    /* Centered numeric/text */
    .us-center-cell { text-align: center; white-space: nowrap; }
    .us-unit {
        background: #f1f5f9;
        padding: 4px 12px;
        border-radius: 6px;
        font-weight: 500;
        font-size: .82rem;
        display: inline-block;
    }
    .us-jumlah {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--us-primary-light);
        color: var(--us-primary);
        font-weight: 700;
    }

    /* Status badge */
    .us-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        white-space: nowrap;
    }
    .us-status-badge.draft    { background: #fef3c7; color: #92400e; }
    .us-status-badge.ack      { background: #ede9fe; color: #5b21b6; }
    .us-status-badge.approve  { background: #dbeafe; color: #1e40af; }
    .us-status-badge.reject   { background: #fee2e2; color: #991b1b; }
    .us-status-badge.done     { background: #f1f5f9; color: #475569; }

    /* Action buttons */
    .us-action-wrap { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; }
    .us-action-btn {
        padding: 7px 14px;
        border-radius: 8px;
        border: 1px solid #c7d2fe;
        background: var(--us-primary-light);
        color: var(--us-primary);
        font-size: .8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .2s;
        white-space: nowrap;
        font-family: inherit;
    }
    .us-action-btn:hover {
        background: var(--us-primary);
        color: #fff;
        border-color: var(--us-primary);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(99,102,241,.3);
    }

    /* Empty state */
    .us-empty {
        padding: 60px 20px;
        text-align: center;
        color: var(--us-muted);
    }
    .us-empty i { font-size: 44px; color: #cbd5e1; display: block; margin-bottom: 12px; }
    .us-empty h4 { margin: 0 0 4px; color: #475569; font-weight: 600; }
    .us-empty p  { margin: 0; font-size: .88rem; }

    @media (max-width: 576px) {
        .us-filter-btn { flex: 1 1 calc(50% - 4px); }
    }
</style>

<div>
    <!-- ===== FILTER PILLS ===== -->
    <div class="us-filter-wrap">
        <button class="us-filter-btn draft active" onclick="fr.filter(this, event, 1)">
            <i class="fa fa-pencil"></i> Draft
        </button>
        <button class="us-filter-btn ack" onclick="fr.filter(this, event, 2)">
            <i class="fa fa-check-circle"></i> Acknowledge
        </button>
        <button class="us-filter-btn approve" onclick="fr.filter(this, event, 3)">
            <i class="fa fa-thumbs-up"></i> Approved
        </button>
        <button class="us-filter-btn reject" onclick="fr.filter(this, event, 4)">
            <i class="fa fa-times-circle"></i> Reject
        </button>
        <button class="us-filter-btn done" onclick="fr.filter(this, event, 6)">
            <i class="fa fa-check"></i> Done
        </button>
    </div>

    <!-- ===== TABLE ===== -->
    <div class="us-card">
        <div class="us-scroll">
            <table class="us-table">
                <thead>
                    <tr>
                        <th>Document</th>
                        <th>Nama Pengusul</th>
                        <th>Tgl. Pengusulan</th>
                        <th>Posisi</th>
                        <th class="us-center">Jumlah</th>
                        <th>Unit</th>
                        <th class="us-center">Status</th>
                        <th class="us-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($list)) { ?>
                        <?php foreach ($list as $l) {
                            $statusKey = (int) $l['status'];
                            $statusMap = [
                                1 => ['text' => 'Draft',        'class' => 'draft'],
                                2 => ['text' => 'Acknowledge',  'class' => 'ack'],
                                3 => ['text' => 'Approved',     'class' => 'approve'],
                                4 => ['text' => 'Reject HRD',   'class' => 'reject'],
                                5 => ['text' => 'Reject CEO',   'class' => 'reject'],
                                6 => ['text' => 'Done',         'class' => 'done'],
                            ];
                            $statusInfo = $statusMap[$statusKey] ?? ['text' => '-', 'class' => ''];
                            $statusText = $statusInfo['text'];
                            $statusClass = $statusInfo['class'];

                            // Encrypted key (untuk handler JS lama)
                            $key       = "secretkey";
                            $encrypted = openssl_encrypt($l['id'], "AES-128-ECB", $key);

                            $unitName = $unit[$l['unit']]['nama'] ?? '-';
                        ?>
                            <tr class="<?= !empty($l['selected']) ? 'us-row-selected' : '' ?>"
                                data-id="<?= htmlspecialchars($l['id']) ?>"
                                data-status-key="<?= $statusKey ?>"
                                data-status="<?= htmlspecialchars($statusText) ?>"
                                data-encrypted="<?= htmlspecialchars($encrypted) ?>"
                                data-document="<?= htmlspecialchars($l['document']) ?>"
                                data-unit="<?= htmlspecialchars($unitName) ?>"
                                data-jumlah="<?= htmlspecialchars($l['jumlah']) ?>"
                                data-alasan="<?= htmlspecialchars($l['alasan']) ?>"
                                data-posisi="<?= htmlspecialchars($l['posisi']) ?>"
                                data-nama-pengusul="<?= htmlspecialchars($l['nama']) ?>"
                                data-tgl-pengusul="<?= htmlspecialchars(tglIndonesia($l['tgl_pengusulan'], '-', ' ')) ?>"
                                data-keterangan-ceo="<?= htmlspecialchars($l['keterangan_ceo'] ?? '') ?>"
                                data-keterangan-hrd="<?= htmlspecialchars($l['keterangan_hrd'] ?? '') ?>"
                                id_data="<?= htmlspecialchars($l['id']) ?>">

                                <!-- Document -->
                                <td>
                                    <span class="us-doc">
                                        <i class="fa fa-file-text-o"></i>
                                        <?= htmlspecialchars($l['document']) ?>
                                    </span>
                                </td>

                                <!-- Pengusul -->
                                <td>
                                    <div class="us-user">
                                        <div class="us-avatar"><?= strtoupper(substr($l['nama'] ?? '?', 0, 1)) ?></div>
                                        <div class="us-user-name"><?= htmlspecialchars($l['nama']) ?></div>
                                    </div>
                                </td>

                                <!-- Tanggal -->
                                <td class="us-center-cell">
                                    <?= htmlspecialchars(tglIndonesia($l['tgl_pengusulan'], '-', ' ')) ?>
                                </td>

                                <!-- Posisi -->
                                <td><?= htmlspecialchars($l['nama_posisi'] ?? $l['posisi']) ?></td>

                                <!-- Jumlah -->
                                <td class="us-center-cell">
                                    <span class="us-jumlah"><?= (int) $l['jumlah'] ?></span>
                                </td>

                                <!-- Unit -->
                                <td><span class="us-unit"><?= htmlspecialchars($unitName) ?></span></td>

                                <!-- Status -->
                                <td class="us-center-cell">
                                    <span class="us-status-badge <?= $statusClass ?>">
                                        <?= htmlspecialchars($statusText) ?>
                                    </span>
                                </td>

                                <!-- Action -->
                                <td class="us-center-cell">
                                    <div class="us-action-wrap">
                                        <button class="us-action-btn" onclick="fr.show_usulan(this, event)">
                                            <i class="fa fa-file"></i> Detail Usulan
                                        </button>
                                        <?php if ($statusKey == 1) { ?>
                                            <button class="us-action-btn" id_data="<?= htmlspecialchars($l['id']) ?>" 
                                                    onclick="fr.keputusan(this, event, 2)"
                                                    style="background:#d1fae5; color:#065f46; border-color:#a7f3d0;">
                                                <i class="fa fa-check"></i> Acknowledge
                                            </button>
                                            <button class="us-action-btn" id_data="<?= htmlspecialchars($l['id']) ?>" 
                                                    onclick="fr.keputusan(this, event, 4)"
                                                    style="background:#fee2e2; color:#991b1b; border-color:#fca5a5;">
                                                <i class="fa fa-times"></i> Reject
                                            </button>
                                        <?php } elseif ($statusKey == 2) { ?>
                                            <button class="us-action-btn" id_data="<?= htmlspecialchars($l['id']) ?>" 
                                                    onclick="fr.keputusan(this, event, 3)"
                                                    style="background:#dbeafe; color:#1e40af; border-color:#93c5fd;">
                                                <i class="fa fa-arrow-right"></i> Done
                                            </button>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8">
                                <div class="us-empty">
                                    <i class="fa fa-inbox"></i>
                                    <h4>Tidak ada data</h4>
                                    <p>Belum ada usulan karyawan yang tersedia.</p>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
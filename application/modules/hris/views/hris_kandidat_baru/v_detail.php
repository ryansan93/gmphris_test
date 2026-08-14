<?php 
    $id_karyawan = $_GET['id'] ?? null;
    $data        = $biodata[$id_karyawan] ?? [];

    $standalone = $data['standalone'] ?? [];
    $grouped    = $data['grouped'] ?? [];

    $standaloneMap = [];
    foreach ($standalone as $item) {
        $standaloneMap[$item['label']] = $item['value'];
    }

    $byTitle = [];
    foreach ($standalone as $item) {
        $title = $item['title'] ?? 'Lainnya';
        $byTitle[$title]['standalone'][] = $item;
    }
    foreach ($grouped as $group => $items) {
        $title = $items[0]['title'] ?? 'Lainnya';
        $byTitle[$title]['grouped'][$group] = $items;
    }

    // Deteksi item dokumen: berdasarkan title ATAU ekstensi file
    $isDocItem = function ($item) {
        if (($item['title'] ?? '') === 'Data Document Kandidat') return true;
        return (bool) preg_match('/\.(png|jpe?g|gif|webp|pdf|docx?|xlsx?)$/i', $item['value'] ?? '');
    };

    $sectionIcon = function ($title) {
        if (strpos($title, 'Document')   !== false) return 'fa-folder-open';
        if (strpos($title, 'Pribadi')    !== false) return 'fa-user';
        if (strpos($title, 'Kontak')     !== false) return 'fa-phone';
        if (strpos($title, 'Pendidikan') !== false) return 'fa-graduation-cap';
        return 'fa-list-alt';
    };

    $hasDocuments = !empty($byTitle['Data Document Kandidat']);
?>

<style>
    :root {
        --bf-primary: #6366f1;
        --bf-primary-dark: #4f46e5;
        --bf-primary-light: #eef2ff;
        --bf-success: #10b981;
        --bf-warning: #f59e0b;
        --bf-warning-light: #fffbeb;
        --bf-danger: #ef4444;
        --bf-text: #1e293b;
        --bf-muted: #64748b;
        --bf-border: #e2e8f0;
        --bf-bg: #f8fafc;
        --bf-radius: 12px;
    }
    * { box-sizing: border-box; }

    
    .bf-card {
        max-width: 1100px;
        margin: 0 auto;
        background: #fff;
        /* box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); */
        overflow: hidden;
    }

    /* ===== TOP BAR ===== */
    .bf-topbar {
        padding: 24px 40px;
        color: #3b3b3b;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .bf-topbar h2 { margin: 0; font-size: 1.4rem; font-weight: 700; }
    .bf-topbar p  { margin: 4px 0 0; opacity: .9; font-size: .9rem; }
    .bf-topbar-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .bf-topbar-actions .bf-btn-ghost {
        background: rgb(72, 72, 72);
        border: 1px solid rgba(255,255,255,.35);
        color: #fff;
        backdrop-filter: blur(8px);
    }
    .bf-topbar-actions .bf-btn-ghost:hover { 
        background: rgb(50, 49, 49);
    }

    /* Pill penanda perubahan */
    .bf-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--bf-warning);
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: .78rem;
        font-weight: 600;
        animation: bf-pulse-pill 1.6s infinite;
    }
    @keyframes bf-pulse-pill {
        0%,100% { box-shadow: 0 0 0 0 rgba(245,158,11,.45); }
        50%     { box-shadow: 0 0 0 8px rgba(245,158,11,0); }
    }

    /* ===== FORM BODY ===== */
    .bf-body { padding: 32px 40px; }

    .bf-section { margin-bottom: 36px; }
    .bf-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--bf-text);
        margin: 0 0 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--bf-primary-light);
    }
    .bf-section-title i {
        width: 34px; height: 34px;
        background: var(--bf-primary-light);
        color: var(--bf-primary);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
    }

    .bf-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px 20px;
    }

    .bf-field {
        background: var(--bf-bg);
        border: 1px solid var(--bf-border);
        border-radius: var(--bf-radius);
        padding: 5px 14px;
        transition: all .2s;
    }
    .bf-field:focus-within {
        border-color: var(--bf-primary);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(99,102,241,.15);
    }
    .bf-field.bf-changed {
        border-color: var(--bf-warning);
        background: var(--bf-warning-light);
    }
    .bf-field.bf-changed .bf-label::after {
        content: '•';
        color: var(--bf-warning);
        margin-left: 6px;
        font-size: 1.1em;
        line-height: 0;
    }
    .bf-label {
        display: block;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .6px;
        font-weight: 600;
        color: var(--bf-muted);
        margin-bottom: 6px;
    }
    .bf-input-row { display: flex; align-items: center; gap: 8px; }
    .bf-input {
        flex: 1;
        min-width: 0;
        border: none;
        background: transparent;
        font-size: .95rem;
        font-weight: 500;
        color: var(--bf-text);
        outline: none;
        font-family: inherit;
    }
    .bf-input::placeholder { color: #b6c2d2; font-weight: 400; }

    /* Tombol pensil = hf.edit_item (SAVE per field) */
    .bf-edit-btn {
        width: 30px; height: 30px;
        flex-shrink: 0;
        border-radius: 8px;
        border: 1px solid var(--bf-border);
        background: #fff;
        color: var(--bf-muted);
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px;
        transition: all .2s;
    }
    .bf-edit-btn:hover {
        background: var(--bf-primary);
        border-color: var(--bf-primary);
        color: #fff;
    }
    .bf-field.bf-changed .bf-edit-btn {
        border-color: var(--bf-warning);
        color: #b45309;
        background: #fff;
        animation: bf-pulse-btn 1.5s infinite;
    }
    @keyframes bf-pulse-btn {
        0%,100% { box-shadow: 0 0 0 0 rgba(245,158,11,.4); }
        50%     { box-shadow: 0 0 0 6px rgba(245,158,11,0); }
    }

    /* ===== DOCUMENT FIELD ===== */
    .bf-doc { display: flex; align-items: center; gap: 8px; }
    .bf-doc-file {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px dashed var(--bf-border);
        border-radius: 8px;
        padding: 7px 10px;
        font-size: .82rem;
    }
    .bf-doc.has-file .bf-doc-file {
        border-style: solid;
        border-color: #a7f3d0;
        background: #f0fdf4;
    }
    .bf-doc-file i { color: var(--bf-muted); flex-shrink: 0; }
    .bf-doc.has-file .bf-doc-file i { color: var(--bf-success); }
    .bf-doc-file a, .bf-doc-file span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--bf-text);
        text-decoration: none;
    }
    .bf-doc-file a:hover { color: var(--bf-primary); text-decoration: underline; }
    .bf-doc-file span.empty { color: #b6c2d2; font-style: italic; }

    .bf-doc-actions { display: flex; gap: 6px; flex-shrink: 0; }
    .bf-icon-btn {
        width: 32px; height: 32px;
        border-radius: 8px;
        border: 1px solid;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px;
        transition: all .2s;
    }
    .bf-icon-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,.12); }
    .bf-btn-upload { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
    .bf-btn-delete { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
    .bf-btn-sync   { background: #cffafe; color: #155e75; border-color: #67e8f9; }

    /* ===== GROUPED TOGGLE (ACCORDION) ===== */
    .bf-subsection {
        margin-top: 16px;
        border: 1px solid var(--bf-border);
        border-left: 4px solid var(--bf-primary);
        border-radius: var(--bf-radius);
        background: #fff;
        overflow: hidden;
    }
    .bf-subsection-head {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        background: #fff;
        border: none;
        cursor: pointer;
        font-family: inherit;
        font-weight: 700;
        font-size: .92rem;
        color: var(--bf-primary-dark);
        text-align: left;
        transition: background .2s;
    }
    .bf-subsection-head:hover { background: var(--bf-primary-light); }
    .bf-subsection-head > i:first-child { color: var(--bf-primary); }

    .bf-sub-summary {
        font-weight: 500;
        color: var(--bf-muted);
        font-size: .83rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .bf-sub-title::after {
        content: '';
        color: var(--bf-warning);
        margin-left: 6px;
        font-size: 1.2em;
        line-height: 0;
    }
    .bf-subsection.has-changed .bf-sub-title::after { content: '•'; }

    .bf-sub-count {
        margin-left: auto;
        background: var(--bf-primary-light);
        color: var(--bf-primary);
        font-size: .7rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        flex-shrink: 0;
    }
    .bf-chevron { transition: transform .3s; color: var(--bf-muted); flex-shrink: 0; }
    .bf-subsection.open .bf-chevron { transform: rotate(180deg); }

    .bf-subsection-body {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows .35s ease;
    }
    .bf-subsection.open .bf-subsection-body { grid-template-rows: 1fr; }
    .bf-sub-clip { overflow: hidden; min-height: 0; }
    .bf-sub-grid { padding: 4px 18px 18px; }

    /* Hilangkan border/outline hitam saat tombol diklik (focus mouse) */
    .bf-subsection-head:focus,
    .bf-edit-btn:focus,
    .bf-icon-btn:focus,
    .bf-btn:focus {
        outline: none;
        box-shadow: none;
        -webkit-tap-highlight-color: transparent;
    }

    /* Tetap aksesibel: ring hanya muncul saat navigasi via keyboard (Tab) */
    .bf-subsection-head:focus-visible,
    .bf-edit-btn:focus-visible,
    .bf-icon-btn:focus-visible,
    .bf-btn:focus-visible {
        outline: 2px solid var(--bf-primary);
        outline-offset: 2px;
    }

    /* ===== GENERIC BUTTON ===== */
    .bf-btn {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: .88rem;
        cursor: pointer;
        border: 1px solid;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all .2s;
        text-decoration: none;
        font-family: inherit;
    }

    /* ===== EMPTY ===== */
    .bf-empty { text-align: center; padding: 60px 20px; color: var(--bf-muted); }
    .bf-empty i { font-size: 42px; color: #cbd5e1; margin-bottom: 14px; display: block; }

    @media (max-width: 640px) {
        .bf-topbar, .bf-body { padding-left: 20px; padding-right: 20px; }
        .bf-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="bf-wrapper">
    <div class="bf-card">

        <!-- TOP BAR -->
        <div class="bf-topbar">
            <div>
                <h2><i class="fa fa-edit"></i> Form Biodata Kandidat</h2>
                <p><?= htmlspecialchars($data_kandidat['nama'] ?? '-') ?> · ID: <?= htmlspecialchars($data_kandidat['id'] ?? '-') ?></p>
            </div>
            <div class="bf-topbar-actions">
                <span class="bf-pill" id="bf-changed-badge" style="display:none;">
                    <i class="fa fa-exclamation-circle"></i>
                    <b id="bf-changed-count">0</b> belum disimpan
                </span>
                <?php if (!$hasDocuments): ?>
                    <?php 
                        $key       = "secretkey";
                        $plaintext = "HRIS/K/004-" . ($data_kandidat['id'] ?? '');
                        $encrypted = openssl_encrypt($plaintext, "AES-128-ECB", $key);
                        $shareUrl  = "http://localhost/recruitment-gmp/FormUpload?kode=" . urlencode($encrypted);
                    ?>
                    <button class="bf-btn bf-btn-ghost" url="<?= htmlspecialchars($shareUrl) ?>" onclick="hf.copy_link(this, event)">
                        <i class="fa fa-link"></i> Generate Link
                    </button>
                <?php endif; ?>
                <button class="bf-btn bf-btn-ghost" onclick="window.location.href='<?= base_url(); ?>hris/HrisKandidatBaru/'">
                    <i class="fa fa-arrow-left"></i> Kembali
                </button>
            </div>
        </div>

        <!-- FORM -->
        <form id="biodata-form" class="bf-body" autocomplete="off" onsubmit="return false;">

            <?php if (empty($byTitle)): ?>
                <div class="bf-empty">
                    <i class="fa fa-inbox"></i>
                    Data biodata tidak tersedia
                </div>
            <?php endif; ?>

            <?php foreach ($byTitle as $title => $section): ?>
                <section class="bf-section">
                    <h3 class="bf-section-title">
                        <i class="fa <?= $sectionIcon($title) ?>"></i>
                        <?= htmlspecialchars($title) ?>
                    </h3>

                    <div class="bf-grid">
                        <?php foreach ($section['standalone'] ?? [] as $item): ?>
                            <?php if (isset($grouped[$item['label']])) continue; 
                                  $value = $item['value'] ?? '';
                                  $label = $item['label'];
                            ?>

                            <?php if ($isDocItem($item)): ?>
                                <!-- ===== FIELD DOKUMEN ===== -->
                                <?php $hasFile = !empty($value) && $value !== '-'; ?>
                                <div class="bf-field">
                                    <label class="bf-label"><?= htmlspecialchars($label) ?></label>
                                    <div class="bf-doc <?= $hasFile ? 'has-file' : '' ?>">
                                        <div class="bf-doc-file">
                                            <i class="fa <?= $hasFile ? 'fa-file-text' : 'fa-file-o' ?>"></i>
                                            <?php if ($hasFile): ?>
                                                <a href="http://localhost/gmphris_test/uploads/recruitment/<?= htmlspecialchars($value) ?>" target="_blank" title="<?= htmlspecialchars($value) ?>">
                                                    <?= htmlspecialchars($value) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="empty">Belum ada file</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="bf-doc-actions">
                                            <?php if ($hasFile): ?>
                                                <button type="button" class="bf-icon-btn bf-btn-sync" title="Sinkron"
                                                        id_data="<?= $item['id_data_karyawan'] ?>"
                                                        doc="<?= htmlspecialchars($value) ?>"
                                                        label="<?= htmlspecialchars($label) ?>"
                                                        onclick="hf.sinkron_document(this, event)">
                                                    <i class="fa fa-refresh"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="bf-icon-btn bf-btn-upload" title="Upload"
                                                    id_data="<?= $item['id_data_karyawan'] ?>"
                                                    label="<?= htmlspecialchars($label) ?>"
                                                    onclick="hf.upload_document(this, event)">
                                                <i class="fa fa-upload"></i>
                                            </button>
                                            <?php if ($hasFile): ?>
                                                <button type="button" class="bf-icon-btn bf-btn-delete" title="Hapus"
                                                        id_data="<?= $item['id_data_karyawan'] ?>"
                                                        label="<?= htmlspecialchars($label) ?>"
                                                        onclick="hf.delete_document(this, event)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                            <?php else: ?>
                                <!-- ===== FIELD TEKS + hf.edit_item ===== -->
                                <div class="bf-field">
                                    <label class="bf-label"><?= htmlspecialchars($label) ?></label>
                                    <div class="bf-input-row">
                                        <input type="text" class="bf-input"
                                               data-label="<?= htmlspecialchars($label) ?>"
                                               data-original="<?= htmlspecialchars($value) ?>"
                                               value="<?= htmlspecialchars($value) ?>"
                                               placeholder="Isi <?= htmlspecialchars(strtolower($label)) ?>..."
                                               oninput="bf.mark(this)">
                                        <button type="button" class="bf-edit-btn" title="Simpan perubahan"
                                                onclick="hf.edit_item(this, event)"
                                                label="<?= htmlspecialchars($label) ?>"
                                                value="<?= htmlspecialchars($value) ?>">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </div>

                    <!-- ===== GROUPED (TOGGLE) ===== -->
                    <?php foreach ($section['grouped'] ?? [] as $group => $items): 
                        $parentValue = $standaloneMap[$group] ?? '';
                    ?>
                    <?php if ($parentValue) {?>
                        <div class="bf-subsection">
                            <button type="button" class="bf-subsection-head" 
                                    onclick="bf.toggleSub(this)" aria-expanded="false">
                                <i class="fa fa-layer-group"></i>
                                <span class="bf-sub-title"><?= htmlspecialchars($group) ?></span>
                                <span class="bf-sub-summary">· <?= htmlspecialchars($parentValue ?: 'Belum diisi') ?></span>
                                <span class="bf-sub-count"><?= (count($items) + 1) ?> field</span>
                                <i class="fa fa-chevron-down bf-chevron"></i>
                            </button>

                            <div class="bf-subsection-body">
                                <div class="bf-sub-clip">
                                    <div class="bf-grid bf-sub-grid">

                                        <!-- Parent -->
                                        <div class="bf-field">
                                            <label class="bf-label"><?= htmlspecialchars($group) ?></label>
                                            <div class="bf-input-row">
                                                <input type="text" class="bf-input bf-parent-input"
                                                       data-label="<?= htmlspecialchars($group) ?>"
                                                       data-original="<?= htmlspecialchars($parentValue) ?>"
                                                       value="<?= htmlspecialchars($parentValue) ?>"
                                                       placeholder="Belum diisi..."
                                                       oninput="bf.mark(this)">
                                                <button type="button" class="bf-edit-btn" title="Simpan perubahan"
                                                        onclick="hf.edit_item(this, event)"
                                                        label="<?= htmlspecialchars($group) ?>"
                                                        value="<?= htmlspecialchars($parentValue) ?>">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Children -->
                                        <?php foreach ($items as $item): $gVal = $item['value'] ?? ''; ?>
                                            <div class="bf-field">
                                                <label class="bf-label"><?= htmlspecialchars($item['label']) ?></label>
                                                <div class="bf-input-row">
                                                    <input type="text" class="bf-input"
                                                           data-label="<?= htmlspecialchars($item['label']) ?>"
                                                           data-original="<?= htmlspecialchars($gVal) ?>"
                                                           value="<?= htmlspecialchars($gVal) ?>"
                                                           placeholder="Belum diisi..."
                                                           oninput="bf.mark(this)">
                                                    <button type="button" class="bf-edit-btn" title="Simpan perubahan"
                                                            onclick="hf.edit_item(this, event)"
                                                            parent="<?= htmlspecialchars($group) ?>"
                                                            label="<?= htmlspecialchars($item['label']) ?>"
                                                            value="<?= htmlspecialchars($gVal) ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php endforeach; ?>

                </section>
            <?php endforeach; ?>
        </form>
    </div>
</div>

<script>
    const bf = {
        form: () => document.getElementById('biodata-form'),

        /* Toggle buka/tutup subsection grouped */
        toggleSub(btn) {
            const sub = btn.closest('.bf-subsection');
            sub.classList.toggle('open');
            btn.setAttribute('aria-expanded', sub.classList.contains('open'));
        },

        /* Tandai perubahan + SINKRONKAN atribut value tombol edit */
        mark(input) {
            const field = input.closest('.bf-field');
            const changed = input.value !== (input.dataset.original ?? '');
            field.classList.toggle('bf-changed', changed);

            // ⭐ Kunci integrasi: hf.edit_item selalu menerima nilai terbaru
            const btn = field.querySelector('.bf-edit-btn');
            if (btn) btn.setAttribute('value', input.value);

            const sub = input.closest('.bf-subsection');
            if (sub) {
                sub.classList.toggle('has-changed', sub.querySelectorAll('.bf-changed').length > 0);
                if (input.classList.contains('bf-parent-input')) {
                    sub.querySelector('.bf-sub-summary').textContent = '· ' + (input.value || 'Belum diisi');
                }
            }
            this.updatePill();
        },

        updatePill() {
            const count = this.form().querySelectorAll('.bf-changed').length;
            document.getElementById('bf-changed-count').textContent = count;
            document.getElementById('bf-changed-badge').style.display = count > 0 ? 'inline-flex' : 'none';
        },

        /* Kembalikan semua field ke nilai awal */
        reset() {
            this.form().querySelectorAll('input.bf-input').forEach(i => {
                i.value = i.dataset.original ?? '';
                this.mark(i);
            });
        }
    };
</script>
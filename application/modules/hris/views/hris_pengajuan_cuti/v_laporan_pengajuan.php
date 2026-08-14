<style>


/* =====================================================
   LAPORAN CUTI — Identik dengan Laporan Resign
   (prefix "lc" agar tidak konflik dengan "rr")
   ===================================================== */

.lc-report{
    --lc-ink:#12203a;
    --lc-muted:#67788f;
    --lc-line:#e6ebf3;
    --lc-blue:#1f75fe;
    --lc-display:'Space Grotesk','Segoe UI',sans-serif;
    --lc-body:'Public Sans','Segoe UI',system-ui,sans-serif;
    --lc-cols:minmax(0,1.15fr) minmax(0,.8fr) minmax(0,.95fr) minmax(0,1.25fr) minmax(0,1.15fr) auto;
    font-family:var(--lc-body);
    color:var(--lc-ink);
    border:0; padding:0; margin:0; min-width:0;
}

.lc-sub{ margin:0 0 2px; font-size:13.5px; color:var(--lc-muted); }

/* ===== Chip statistik ===== */
.lc-stats{ display:flex; flex-wrap:wrap; gap:9px; margin:14px 0 16px; }
.lc-chip{
    display:inline-flex; align-items:center; gap:8px;
    padding:8px 14px; border-radius:999px;
    border:1px solid #e2e8f2; background:#fff;
    font-family:var(--lc-body); font-size:12.5px; font-weight:600; color:#4a5b73;
    cursor:pointer; transition:all .2s ease;
    box-shadow:0 1px 2px rgba(18,32,58,.04);
}
.lc-chip b{
    font-family:var(--lc-display); font-size:12px; font-weight:700;
    background:#eef2f8; color:#33465f;
    padding:1px 8px; border-radius:999px;
    font-variant-numeric:tabular-nums; transition:inherit;
}
.lc-chip:hover{ transform:translateY(-2px); border-color:#c9d6ea; box-shadow:0 6px 14px rgba(18,32,58,.08); }
.lc-chip.active{ background:var(--lc-ink); border-color:var(--lc-ink); color:#fff; box-shadow:0 8px 18px rgba(18,32,58,.25); }
.lc-chip.active b{ background:rgba(255,255,255,.16); color:#fff; }
.lc-dot{ width:8px; height:8px; border-radius:50%; flex:0 0 auto; }
.lc-dot-all{ background:linear-gradient(135deg,#1f75fe,#18a058); }
.lc-dot-draft{ background:#f0a020; }
.lc-dot-ack{ background:#1f75fe; }
.lc-dot-ok{ background:#18a058; }
.lc-dot-no{ background:#e5484d; }

/* ===== Panel ===== */
.lc-panel{
    position:relative;
    container-type:inline-size;
    container-name:lcpanel;
    background:
        radial-gradient(720px 260px at 90% -20%, rgba(31,117,254,.09), transparent 60%),
        radial-gradient(560px 240px at -10% 120%, rgba(24,154,82,.07), transparent 60%),
        #f6f8fc;
    border:1px solid var(--lc-line);
    border-radius:18px;
    padding:18px;
    min-width:0;
}
.lc-panel::before{
    content:'';
    position:absolute; inset:0; border-radius:inherit;
    background-image:radial-gradient(rgba(18,32,58,.05) 1px, transparent 1.4px);
    background-size:20px 20px;
    -webkit-mask-image:linear-gradient(180deg,#000 0%,transparent 55%);
    mask-image:linear-gradient(180deg,#000 0%,transparent 55%);
    pointer-events:none;
}
.lc-panel > *{ position:relative; }

/* ===== Filter bar ===== */
.lc-filter{ display:flex; gap:12px; margin:0 0 16px; align-items:center; flex-wrap:wrap; }
.lc-search{ position:relative; min-width:0; }
.lc-search i{
    position:absolute; left:14px; top:50%; transform:translateY(-50%);
    color:#93a1b5; font-size:13px; pointer-events:none;
}
.lc-filter .form-control{
    border-radius:10px; border:1px solid #dfe4ee;
    padding:9px 13px; font-size:13.5px; height:auto;
    background:#fff; color:var(--lc-ink);
    transition:border-color .2s, box-shadow .2s;
}
.lc-search .form-control{ padding-left:38px; width:100%; }
.lc-filter .form-control:focus{
    border-color:var(--lc-blue);
    box-shadow:0 0 0 3px rgba(31,117,254,.14);
    outline:0;
}
.lc-select{
    width:240px; max-width:100%; flex:0 1 auto; cursor:pointer;
    -webkit-appearance:none; appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b7a8f' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 13px center;
    padding-right:36px;
}
.lc-count{
    margin-left:auto; font-size:12.5px; color:#7b8aa0;
    font-variant-numeric:tabular-nums; white-space:nowrap;
}

/* ===== Header kolom ===== */
.lc-head{
    display:grid; grid-template-columns:var(--lc-cols); gap:16px;
    padding:4px 18px 12px 22px;
    font-family:var(--lc-display);
    font-size:10.5px; font-weight:600;
    letter-spacing:.14em; text-transform:uppercase; color:#8593a8;
    min-width:0;
}
.lc-head span{ display:flex; align-items:center; gap:6px; min-width:0; }
.lc-head span::before{ content:''; width:5px; height:5px; border-radius:2px; background:#c3cddb; flex:0 0 auto; }

/* ===== Kartu ===== */
.lc-card{
    position:relative;
    background:#fff;
    border:1px solid var(--lc-line);
    border-radius:14px;
    padding:16px 18px 16px 22px;
    margin-bottom:12px;
    display:grid;
    grid-template-columns:var(--lc-cols);
    gap:16px;
    align-items:start;
    overflow:hidden;
    min-width:0;
    box-shadow:0 2px 6px rgba(18,32,58,.04);
    transition:box-shadow .22s ease, transform .22s ease, border-color .22s ease;
    animation:lc-in .5s cubic-bezier(.22,.7,.3,1) backwards;
}
.lc-card:last-of-type{ margin-bottom:0; }
.lc-card::before{
    content:'';
    position:absolute; left:0; top:0; bottom:0; width:4px;
    background:#a8b6ca;
    transition:width .2s ease;
}
.lc-card[data-status="draft"]::before{ background:#f0a020; }
.lc-card[data-status="acknowledge"]::before{ background:#1f75fe; }
.lc-card[data-status="approved"]::before{ background:#18a058; }
.lc-card[data-status^="reject"]::before{ background:#e5484d; }
.lc-card:hover{
    box-shadow:0 10px 26px rgba(18,32,58,.10);
    transform:translateY(-2px);
    border-color:#dbe4f2;
}
.lc-card:hover::before{ width:6px; }

.lc-meta,.lc-date,.lc-jenis,.lc-note,.lc-status{ min-width:0; }
.lc-name,.lc-sub,.lc-alasan,.lc-date strong,.reject-note,.lc-type{ overflow-wrap:anywhere; }

/* --- Meta (karyawan) --- */
.lc-meta{ display:flex; flex-direction:column; align-items:flex-start; }
.lc-name{
    font-family:var(--lc-display);
    font-size:15px; font-weight:700; letter-spacing:-.01em;
    color:#16233b; line-height:1.25;
}
.lc-sub{
    font-size:12.5px; color:#67788f; margin-top:4px;
    display:flex; align-items:center; gap:7px; flex-wrap:wrap;
}
.lc-nik{
    font-variant-numeric:tabular-nums;
    background:#f1f4f9; padding:1px 7px; border-radius:6px;
    font-size:11.5px; font-weight:600; color:#44546a;
}
.lc-doc{
    display:inline-flex; align-items:center; gap:6px;
    margin-top:8px; padding:4px 10px; border-radius:999px;
    background:#eaf2ff; color:var(--lc-blue);
    font-size:11.5px; font-weight:600; text-decoration:none;
    max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
    transition:all .18s ease;
}
.lc-doc:hover{
    background:var(--lc-blue); color:#fff; text-decoration:none;
    transform:translateY(-1px);
    box-shadow:0 5px 12px rgba(31,117,254,.3);
}

/* edit note */
.lc-editnote{
    margin-top:6px; font-size:11.5px; color:#67788f;
    display:flex; align-items:center; gap:5px; flex-wrap:wrap;
}
.lc-editnote a{
    color:var(--lc-blue); font-weight:600;
    text-decoration:underline dotted;
    cursor:pointer;
}

/* --- Tanggal & Jenis (desktop: susun vertikal) --- */
.lc-date,
.lc-jenis{ display:flex; flex-direction:column; gap:11px; }
.lc-date small,
.lc-jenis small{
    display:flex; align-items:center; gap:6px;
    color:#8a96a8; font-family:var(--lc-display);
    font-size:10.5px; font-weight:600;
    text-transform:uppercase; letter-spacing:.08em;
    margin-bottom:5px;
}
.lc-date small i,
.lc-jenis small i{ font-size:10px; color:#aeb9ca; }
.lc-date strong{
    font-family:var(--lc-display);
    font-size:13.5px; font-weight:600; color:#22334c;
    font-variant-numeric:tabular-nums;
    overflow-wrap:anywhere;
}
.lc-date-end strong{
    display:inline-block;
    background:#eaf2ff; color:#135fce;
    padding:2px 8px; border-radius:6px;
}
.lc-type{
    display:inline-block;
    border:1px solid #dfe6f0; background:#f8fafd;
    padding:4px 10px; border-radius:7px;
    font-weight:700; font-size:12px; letter-spacing:.02em; color:#33465f;
}
.lc-durasi{
    display:inline-block;
    background:#fff6e3; color:#9a6200;
    padding:2px 8px; border-radius:6px;
    font-size:11px; font-weight:700;
    letter-spacing:.02em;
}

/* --- Keterangan --- */
.lc-alasan{
    margin:4px 0 0;
    font-size:13px; color:#44546a; line-height:1.5;
    display:-webkit-box;
    -webkit-line-clamp:3;
    -webkit-box-orient:vertical;
    overflow:hidden;
    overflow-wrap:anywhere;
}

/* --- Status --- */
.lc-status{ display:flex; flex-direction:column; align-items:stretch; gap:9px; min-width:0; }
.lc-status-row{ display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.status-pill{
    display:inline-flex; align-items:center; gap:6px;
    padding:5px 11px; border-radius:999px;
    font-family:var(--lc-display);
    font-size:10px; font-weight:700; letter-spacing:.06em;
    text-transform:uppercase;
    border:1px solid transparent;
    white-space:nowrap;
}
.status-pill::before{ content:''; width:7px; height:7px; border-radius:50%; background:currentColor; flex:0 0 auto; }
.status-draft{ background:#fff6e3; color:#9a6200; border-color:#ffe3ae; }
.status-ack{ background:#e9f1ff; color:#135fce; border-color:#c9ddff; }
.status-approved{ background:#e6f8ee; color:#0d7a3f; border-color:#bfe9d0; }
.status-reject{ background:#fdeeee; color:#c23b3b; border-color:#f6caca; }
.status-draft::before,
.status-ack::before{ animation:lc-pulse 1.8s ease-in-out infinite; }

.lc-revert{
    width:27px; height:27px; border-radius:8px;
    display:inline-flex; align-items:center; justify-content:center;
    background:#f2f5fa; border:1px solid #e0e7f1; color:#5a6b83;
    font-size:12px; cursor:pointer; transition:all .2s ease;
    flex:0 0 auto;
}
.lc-revert:hover{ background:var(--lc-ink); border-color:var(--lc-ink); color:#fff; transform:rotate(-45deg); }

/* Varian kecil di dalam "Keterangan Reject" */
.lc-revert.lc-revert-sm{
    width:22px; height:22px; border-radius:6px; font-size:10px;
    margin-left:auto;
    background:#fff;
    border-color:#f6caca;
    color:#c23b3b;             
}
.lc-revert.lc-revert-sm:hover{
    background:#c23b3b;
    border-color:#c23b3b;
    color:#fff;                 
    transform:rotate(-45deg);
}

.reject-note{
    width:100%;
    background:#fff7f7; border:1px solid #f7d7d7; border-left:3px solid #e5484d;
    border-radius:8px; padding:8px 11px;
    font-size:12px; color:#7c3d3d; line-height:1.5;
    overflow-wrap:anywhere;
}
.reject-note small{
    display:flex; align-items:center; gap:5px;
    font-weight:700; color:#c23b3b;
    margin-bottom:3px; font-size:10px;
    text-transform:uppercase; letter-spacing:.06em;
    white-space:nowrap;
}
.reject-note small i{ flex:0 0 auto; }

/* --- Aksi --- */
.lc-actions{ display:flex; flex-direction:column; gap:8px; min-width:150px; }
.lc-actions .btn{
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    width:100%;
    font-family:var(--lc-body); font-size:13px; font-weight:600;
    padding:8px 16px; border-radius:9px; border:0; cursor:pointer;
    transition:transform .18s ease, box-shadow .18s ease, filter .18s ease;
}
.lc-actions .btn:active{ transform:translateY(1px) scale(.99); }
.lc-actions .btn-success{
    color:#fff;
    background:linear-gradient(180deg,#27bb6e,#189a52);
    box-shadow:0 6px 14px rgba(24,154,82,.28);
}
.lc-actions .btn-success:hover{ transform:translateY(-2px); box-shadow:0 10px 20px rgba(24,154,82,.34); filter:brightness(1.04); }
.lc-actions .btn-danger{
    color:#fff;
    background:linear-gradient(180deg,#f2666a,#d84a4f);
    box-shadow:0 6px 14px rgba(216,74,79,.25);
}
.lc-actions .btn-danger:hover{ transform:translateY(-2px); box-shadow:0 10px 20px rgba(216,74,79,.30); filter:brightness(1.04); }
.lc-noaction{
    display:inline-flex; align-items:center; justify-content:center; gap:7px;
    width:100%; padding:8px 12px; border-radius:9px;
    font-size:12px; color:#93a1b5;
    background:#f6f8fb; border:1px dashed #dfe6f0;
}
.lc-noaction.lc-done{ color:#0d7a3f; background:#effaf3; border-color:#cdeeda; border-style:solid; }
.lc-noaction.lc-rejected{ color:#c23b3b; background:#fff7f7; border-color:#f6caca; border-style:solid; }

/* ===== Empty state ===== */
.lc-empty{
    text-align:center; padding:46px 20px;
    background:#fff; border:1.5px dashed #dde5f0; border-radius:14px;
}
.lc-empty-icon{
    width:64px; height:64px; margin:0 auto 14px;
    display:flex; align-items:center; justify-content:center;
    border-radius:20px; font-size:26px;
    background:linear-gradient(180deg,#f2f6fd,#e8eefa); color:#9fb0c8;
    box-shadow:inset 0 0 0 1px #e3eaf5;
    animation:lc-float 3.2s ease-in-out infinite;
}
.lc-empty-title{ font-family:var(--lc-display); font-weight:600; font-size:15.5px; color:#33465f; }
.lc-empty-sub{ font-size:13px; color:#8a97ab; margin-top:3px; }
.lc-empty-mini{ padding:30px 16px; margin-top:12px; }

/* ===== Animasi ===== */
@keyframes lc-in{
    from{ opacity:0; transform:translateY(14px) scale(.99); }
    to{ opacity:1; transform:none; }
}
@keyframes lc-pulse{ 0%,100%{ opacity:1; } 50%{ opacity:.3; } }
@keyframes lc-float{ 0%,100%{ transform:translateY(0); } 50%{ transform:translateY(-6px); } }

.lc-chip:focus-visible,
.lc-revert:focus-visible,
.lc-doc:focus-visible,
.lc-actions .btn:focus-visible{ outline:2px solid var(--lc-blue); outline-offset:2px; }

/* =====================================================
   RESPONSIF — berbasis lebar PANEL (container query)
   ===================================================== */
@container lcpanel (max-width: 940px){
    .lc-head{ display:none; }
    .lc-card{
        grid-template-columns:1fr 1fr;
        grid-template-areas:
            "meta    meta"
            "date    jenis"
            "note    note"
            "status  status"
            "actions actions";
        gap:14px;
        padding:16px 16px 16px 20px;
    }
    .lc-meta{ grid-area:meta; }
    .lc-date{ grid-area:date; }
    .lc-jenis{ grid-area:jenis; }
    .lc-note{ grid-area:note; border-top:1px dashed var(--lc-line); padding-top:12px; }
    .lc-status{
        grid-area:status;
        border-top:1px dashed var(--lc-line);
        padding-top:12px;
    }
    .lc-actions{
        grid-area:actions;
        flex-direction:row;
        min-width:0;
        border-top:1px dashed var(--lc-line);
        padding-top:12px;
    }
    .lc-actions .btn{ width:auto; flex:0 1 auto; min-width:150px; }
    .lc-noaction{ width:auto; }

    /* MOBILE/TABLET: Mulai-Selesai & Jenis-Durasi jadi 1 baris */
    .lc-date,
    .lc-jenis{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:12px;
        align-content:start;
    }
}

@container lcpanel (max-width: 560px){
    .lc-card{
        grid-template-columns:1fr;
        grid-template-areas:
            "meta"
            "date"
            "jenis"
            "note"
            "status"
            "actions";
    }
    .lc-actions{ flex-direction:column; }
    .lc-actions .btn{ width:100%; }
}

/* Fallback viewport (browser lama tanpa container query) */
@media (max-width:940px) and (not (container-type: inline-size)){
    .lc-head{ display:none; }
    .lc-card{
        grid-template-columns:1fr 1fr;
        grid-template-areas:
            "meta    meta"
            "date    jenis"
            "note    note"
            "status  status"
            "actions actions";
        gap:14px;
    }
    .lc-meta{ grid-area:meta; }
    .lc-date{ grid-area:date; }
    .lc-jenis{ grid-area:jenis; }
    .lc-note{ grid-area:note; border-top:1px dashed var(--lc-line); padding-top:12px; }
    .lc-status{ grid-area:status; border-top:1px dashed var(--lc-line); padding-top:12px; }
    .lc-actions{ grid-area:actions; flex-direction:row; min-width:0; border-top:1px dashed var(--lc-line); padding-top:12px; }
    .lc-actions .btn{ width:auto; flex:0 1 auto; min-width:150px; }
    .lc-noaction{ width:auto; }

    .lc-date,
    .lc-jenis{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:12px;
        align-content:start;
    }
}

@media (max-width:768px){
    .lc-filter{ flex-direction:column; align-items:stretch; }
    .lc-select{ width:100%; }
    .lc-count{ margin:0; text-align:center; }
    .lc-panel{ padding:13px; }
}

@media (max-width:560px) and (not (container-type: inline-size)){
    .lc-card{
        grid-template-columns:1fr;
        grid-template-areas: "meta" "date" "jenis" "note" "status" "actions";
    }
    .lc-actions{ flex-direction:column; }
    .lc-actions .btn{ width:100%; }
}

@media (prefers-reduced-motion: reduce){
    .lc-card,
    .status-pill::before,
    .lc-empty-icon{ animation:none !important; }
    *{ transition:none !important; }
}
</style>

<fieldset class="mb-3 lc-report">
    <legend style="width:50%;">Laporan Cuti</legend>

    <?php if (!empty($pengajuan)) { ?>

    <?php
    $statusList = [
        1 => 'DRAFT',
        2 => 'ACKNOWLEDGE',
        3 => 'APPROVED',
        4 => 'REJECT ATASAN',
        5 => 'REJECT HRD'
    ];

    // hitung per status untuk chip
    $cnt = ['all' => count($pengajuan), 'draft' => 0, 'acknowledge' => 0, 'approved' => 0, 'reject' => 0];
    foreach($pengajuan as $c){
        if($c['status_pengajuan'] == 1) $cnt['draft']++;
        elseif($c['status_pengajuan'] == 2) $cnt['acknowledge']++;
        elseif($c['status_pengajuan'] == 3) $cnt['approved']++;
        elseif($c['status_pengajuan'] == 4 || $c['status_pengajuan'] == 5) $cnt['reject']++;
    }
    ?>

    <p class="lc-sub">Ringkasan pengajuan cuti karyawan — klik status di bawah untuk memfilter cepat.</p>

    <div class="lc-stats">
        <button type="button" class="lc-chip active" data-status="all"><span class="lc-dot lc-dot-all"></span>Semua <b><?php echo $cnt['all'];?></b></button>
        <button type="button" class="lc-chip" data-status="draft"><span class="lc-dot lc-dot-draft"></span>Draft <b><?php echo $cnt['draft'];?></b></button>
        <button type="button" class="lc-chip" data-status="acknowledge"><span class="lc-dot lc-dot-ack"></span>Acknowledge <b><?php echo $cnt['acknowledge'];?></b></button>
        <button type="button" class="lc-chip" data-status="approved"><span class="lc-dot lc-dot-ok"></span>Approved <b><?php echo $cnt['approved'];?></b></button>
        <button type="button" class="lc-chip" data-status="reject"><span class="lc-dot lc-dot-no"></span>Reject <b><?php echo $cnt['reject'];?></b></button>
    </div>

    <div class="lc-panel">

        <div class="lc-filter">
            <div class="lc-search">
                <i class="fa fa-search" aria-hidden="true"></i>
                <input id="filter-search"
                       type="search"
                       class="form-control"
                       placeholder="Cari karyawan, NIK, jabatan, jenis cuti, atau alasan...">
            </div>

            <select id="filter-status" class="form-control lc-select">
                <option value="all">Semua Status</option>
                <option value="draft">DRAFT</option>
                <option value="acknowledge">ACKNOWLEDGE</option>
                <option value="approved">APPROVED</option>
                <option value="reject">REJECT (Atasan + HRD)</option>
                <option value="reject atasan">REJECT ATASAN</option>
                <option value="reject hrd">REJECT HRD</option>
            </select>

            <span id="filter-count" class="lc-count"></span>
        </div>

        <div class="lc-head" aria-hidden="true">
            <span>Karyawan</span>
            <span>Tanggal</span>
            <span>Jenis Cuti</span>
            <span>Keterangan</span>
            <span>Status</span>
            <span>Aksi</span>
        </div>

        <?php
        $jenis_cuti = [
            'cuti'               => 'Cuti',
            'cuti_sakit'         => 'Cuti Sakit',
            'cuti_force_majeure' => 'Cuti Force Majeure',
            'cuti_jatah_liburan' => 'Cuti Jatah Liburan',
        ];
        ?>

        <?php $i = 0; foreach ($pengajuan as $o) { ?>
            <?php
                $status_raw = isset($o['status_pengajuan']) ? $o['status_pengajuan'] : '';
                $status = is_numeric($status_raw)
                    ? (isset($statusList[(int)$status_raw]) ? $statusList[(int)$status_raw] : (string)$status_raw)
                    : (string)$status_raw;

                $statusClass  = 'status-draft';
                $status_lower = strtolower($status);
                if (strpos($status_lower, 'acknowledge') !== false || strpos($status_lower, 'ack') !== false) $statusClass = 'status-ack';
                if (strpos($status_lower, 'approved') !== false) $statusClass = 'status-approved';
                if (strpos($status_lower, 'reject')   !== false) $statusClass = 'status-reject';

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

                $is_reject = ($o['status_pengajuan'] == 4 || $o['status_pengajuan'] == 5);

                $delay = min($i * 45, 360);
            ?>

            <div class="lc-card"
                style="animation-delay:<?php echo $delay;?>ms"
                data-name="<?php echo htmlspecialchars(strtolower($o['nama_karyawan']), ENT_QUOTES); ?>"
                data-nik="<?php echo htmlspecialchars(strtolower($o['nik']), ENT_QUOTES); ?>"
                data-jabatan="<?php echo htmlspecialchars(strtolower($o['nama_jabatan']), ENT_QUOTES); ?>"
                data-jenis="<?php echo htmlspecialchars(strtolower($jenisText), ENT_QUOTES); ?>"
                data-status="<?php echo htmlspecialchars(strtolower($status), ENT_QUOTES); ?>"
                data-alasan="<?php echo htmlspecialchars(strtolower($alasan), ENT_QUOTES); ?>"
                data-selesai="<?php echo htmlspecialchars(strtolower($tanggal_selesai_text), ENT_QUOTES); ?>"
                data-durasi="<?php echo htmlspecialchars(strtolower($jumlah_hari), ENT_QUOTES); ?>">

                <div class="lc-meta">
                    <div class="lc-name">
                        <?php echo htmlspecialchars(ucwords(strtolower($o['nama_karyawan'])), ENT_QUOTES); ?>
                    </div>
                    <div class="lc-sub">
                        <span class="lc-nik"><?php echo htmlspecialchars($o['nik'], ENT_QUOTES); ?></span>
                        <?php echo htmlspecialchars($o['nama_jabatan'], ENT_QUOTES); ?>
                    </div>
                    <a href="javascript:void(0)" class="lc-doc" id_data="<?php echo $o['id']; ?>" onclick="pc.showAttachment(this, event)">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        Attachment
                    </a>
                    <?php if (!empty($o['edit_note'])) { ?>
                        <?php
                            $edit_note = htmlspecialchars($o['edit_note'], ENT_QUOTES);
                            $words = preg_split('/\s+/', trim($edit_note));
                        ?>
                        <div class="lc-editnote">
                            <i class="fa fa-edit"></i>
                            <?php if (count($words) > 2) { ?>
                                <a href="#" class="show-edit-note" data-note="<?= $edit_note ?>">
                                    <?= implode(' ', array_slice($words, 0, 2)); ?> ...
                                </a>
                            <?php } else { ?>
                                <span><?= $edit_note ?></span>
                            <?php } ?>
                            <span>— <?= tglIndonesia($o['updated_at'], '-', ' ') ?></span>
                        </div>
                    <?php } ?>
                </div>

                <div class="lc-date">
                    <div>
                        <small><i class="fa fa-calendar-o" aria-hidden="true"></i> Mulai</small>
                        <strong><?php echo tglIndonesia($o['tanggal_mulai'], '-', ' '); ?></strong>
                    </div>
                    <div class="lc-date-end">
                        <small><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Selesai</small>
                        <strong><?php echo $tanggal_selesai_text; ?></strong>
                    </div>
                </div>

                <div class="lc-jenis">
                    <div>
                        <small>Jenis Cuti</small>
                        <span class="lc-type"><?php echo htmlspecialchars($jenisText, ENT_QUOTES); ?></span>
                    </div>
                    <?php if ($jumlah_hari !== '-'): ?>
                        <div>
                            <small>Durasi</small>
                            <span class="lc-durasi"><?php echo htmlspecialchars($jumlah_hari, ENT_QUOTES); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="lc-note">
                    <small>Keterangan</small>
                    <p class="lc-alasan" title="<?php echo htmlspecialchars($alasan, ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($alasan, ENT_QUOTES); ?>
                    </p>
                </div>

                <div class="lc-status">
                    <div class="lc-status-row">
                        <span class="status-pill <?php echo $statusClass; ?>">
                            <?php echo ucwords(strtolower(str_replace(['_', '-'], ' ', $status))); ?>
                        </span>

                        <?php if (!$is_reject) { ?>
                            <?php if ($can_revert_draft) { ?>
                                <span class="lc-revert"
                                      revert="DRAFT"
                                      id_data="<?php echo $o['id']; ?>"
                                      revert_note="<?php echo isset($o['revert_note']) ? htmlspecialchars($o['revert_note'], ENT_QUOTES) : ''; ?>"
                                      onclick="pc.revert_status(this, event)"
                                      data-toggle="tooltip"
                                      data-placement="top"
                                      title="Kembalikan ke Draft">
                                    <i class="fa fa-undo" aria-hidden="true"></i>
                                </span>
                            <?php } elseif ($can_revert_ack) { ?>
                                <span class="lc-revert"
                                      revert="ACK"
                                      id_data="<?php echo $o['id']; ?>"
                                      revert_note="<?php echo isset($o['revert_note']) ? htmlspecialchars($o['revert_note'], ENT_QUOTES) : ''; ?>"
                                      onclick="pc.revert_status(this, event)"
                                      data-toggle="tooltip"
                                      data-placement="top"
                                      title="Kembalikan ke Acknowledge">
                                    <i class="fa fa-undo" aria-hidden="true"></i>
                                </span>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <?php if ($is_reject) { ?>
                        <div class="reject-note">
                            <small>
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                Keterangan Reject
                                <?php if ($can_revert_draft) { ?>
                                    <span class="lc-revert lc-revert-sm"
                                          revert="DRAFT"
                                          id_data="<?php echo $o['id']; ?>"
                                          revert_note="<?php echo isset($o['revert_note']) ? htmlspecialchars($o['revert_note'], ENT_QUOTES) : ''; ?>"
                                          onclick="pc.revert_status(this, event)"
                                          data-toggle="tooltip"
                                          data-placement="top"
                                          title="Kembalikan ke Draft">
                                        <i class="fa fa-undo" aria-hidden="true"></i>
                                    </span>
                                <?php } elseif ($can_revert_ack) { ?>
                                    <span class="lc-revert lc-revert-sm"
                                          revert="ACK"
                                          id_data="<?php echo $o['id']; ?>"
                                          revert_note="<?php echo isset($o['revert_note']) ? htmlspecialchars($o['revert_note'], ENT_QUOTES) : ''; ?>"
                                          onclick="pc.revert_status(this, event)"
                                          data-toggle="tooltip"
                                          data-placement="top"
                                          title="Kembalikan ke Acknowledge">
                                        <i class="fa fa-undo" aria-hidden="true"></i>
                                    </span>
                                <?php } ?>
                            </small>
                            <?php echo htmlspecialchars($o['keterangan_reject'], ENT_QUOTES); ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="lc-actions">
                    <?php if ($show_ack) { ?>
                        <button type="button" id_data="<?php echo $o['id']; ?>" class="btn btn-success" value="2" onclick="pc.keputusanPengajuan(this, event)">
                            <i class="fa fa-check"></i> Acknowledge
                        </button>
                        <button type="button" id_data="<?php echo $o['id']; ?>" class="btn btn-danger" value="4" onclick="pc.keputusanPengajuan(this, event)">
                            <i class="fa fa-times"></i> Reject
                        </button>
                    <?php } elseif ($show_approve) { ?>
                        <button type="button" id_data="<?php echo $o['id']; ?>" class="btn btn-success" value="3" onclick="pc.keputusanPengajuan(this, event)">
                            <i class="fa fa-check"></i> Approve
                        </button>
                        <button type="button" id_data="<?php echo $o['id']; ?>" class="btn btn-danger" value="5" onclick="pc.keputusanPengajuan(this, event)">
                            <i class="fa fa-times"></i> Reject
                        </button>
                    <?php } elseif ($o['status_pengajuan'] == 3) { ?>
                        <span class="lc-noaction lc-done"><i class="fa fa-check-circle" aria-hidden="true"></i> Selesai diproses</span>
                    <?php } elseif ($is_reject) { ?>
                        <span class="lc-noaction lc-rejected"><i class="fa fa-times-circle" aria-hidden="true"></i> Ditolak</span>
                    <?php } else { ?>
                        <span class="lc-noaction"><i class="fa fa-hourglass-half" aria-hidden="true"></i> Menunggu tindakan</span>
                    <?php } ?>
                </div>

            </div>

        <?php $i++; } ?>

        <div class="lc-empty lc-empty-mini" id="lc-noresult" hidden>
            <div class="lc-empty-icon"><i class="fa fa-search" aria-hidden="true"></i></div>
            <div class="lc-empty-title">Tidak ada hasil yang cocok</div>
            <div class="lc-empty-sub">Coba ubah kata kunci pencarian atau filter status.</div>
        </div>

    </div>

    <?php } else { ?>

    <div class="lc-empty">
        <div class="lc-empty-icon"><i class="fa fa-inbox" aria-hidden="true"></i></div>
        <div class="lc-empty-title">Belum ada pengajuan cuti</div>
        <div class="lc-empty-sub">Pengajuan cuti dari karyawan akan tampil di sini.</div>
    </div>

    <?php } ?>

</fieldset>

<script>
(function(){

    var search    = document.getElementById('filter-search');
    var selStatus = document.getElementById('filter-status');
    var countEl   = document.getElementById('filter-count');
    var noResult  = document.getElementById('lc-noresult');

    if(!search || !selStatus){ return; }

    var cards = Array.prototype.slice.call(document.querySelectorAll('.lc-card'));
    var chips = Array.prototype.slice.call(document.querySelectorAll('.lc-chip'));

    function apply(){

        var q  = search.value.trim().toLowerCase();
        var st = selStatus.value.toLowerCase();
        var visible = 0;

        cards.forEach(function(card){

            var text = [
                card.dataset.name,
                card.dataset.nik,
                card.dataset.jabatan,
                card.dataset.jenis,
                card.dataset.alasan,
                card.dataset.selesai,
                card.dataset.durasi
            ].join(' ');

            var okSearch = !q || text.indexOf(q) > -1;
            var okStatus = (st === 'all') || card.dataset.status.indexOf(st) > -1;
            var show     = okSearch && okStatus;

            card.style.display = show ? '' : 'none';
            if(show) visible++;

        });

        if(countEl) countEl.textContent = 'Menampilkan ' + visible + ' dari ' + cards.length + ' pengajuan';
        if(noResult) noResult.hidden = (visible > 0);

        chips.forEach(function(c){
            c.classList.toggle('active', c.dataset.status === st);
        });

    }

    chips.forEach(function(c){
        c.addEventListener('click', function(){
            selStatus.value = c.dataset.status;
            apply();
        });
    });

    search.addEventListener('input', apply);
    selStatus.addEventListener('change', apply);

    apply();

})();
</script>
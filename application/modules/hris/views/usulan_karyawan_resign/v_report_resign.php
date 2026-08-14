<fieldset class="mb-3 resign-report">
    <legend>Approval Resign</legend>

    <style>

        .resign-report{
            --rr-ink:#12203a;
            --rr-muted:#67788f;
            --rr-line:#e6ebf3;
            --rr-blue:#1f75fe;
            --rr-display:'Space Grotesk','Segoe UI',sans-serif;
            --rr-body:'Public Sans','Segoe UI',system-ui,sans-serif;
            /* CAIR: tanpa minimum pixel besar, kolom bebas mengecil */
            --rr-cols:minmax(0,1.15fr) minmax(0,.8fr) minmax(0,1.25fr) minmax(0,1.15fr) auto;
            font-family:var(--rr-body);
            color:var(--rr-ink);
            border:0;
            padding:0;
            margin:0;
            min-width:0;
        }

        .rr-sub{ margin:0 0 2px; font-size:13.5px; color:var(--rr-muted); }

        /* ===== Chip statistik ===== */
        .rr-stats{ display:flex; flex-wrap:wrap; gap:9px; margin:14px 0 16px; }
        .rr-chip{
            display:inline-flex; align-items:center; gap:8px;
            padding:8px 14px; border-radius:999px;
            border:1px solid #e2e8f2; background:#fff;
            font-family:var(--rr-body); font-size:12.5px; font-weight:600; color:#4a5b73;
            cursor:pointer; transition:all .2s ease;
            box-shadow:0 1px 2px rgba(18,32,58,.04);
        }
        .rr-chip b{
            font-family:var(--rr-display); font-size:12px; font-weight:700;
            background:#eef2f8; color:#33465f;
            padding:1px 8px; border-radius:999px;
            font-variant-numeric:tabular-nums; transition:inherit;
        }
        .rr-chip:hover{ transform:translateY(-2px); border-color:#c9d6ea; box-shadow:0 6px 14px rgba(18,32,58,.08); }
        .rr-chip.active{ background:var(--rr-ink); border-color:var(--rr-ink); color:#fff; box-shadow:0 8px 18px rgba(18,32,58,.25); }
        .rr-chip.active b{ background:rgba(255,255,255,.16); color:#fff; }
        .rr-dot{ width:8px; height:8px; border-radius:50%; flex:0 0 auto; }
        .rr-dot-all{ background:linear-gradient(135deg,#1f75fe,#18a058); }
        .rr-dot-draft{ background:#f0a020; }
        .rr-dot-ack{ background:#1f75fe; }
        .rr-dot-ok{ background:#18a058; }
        .rr-dot-no{ background:#e5484d; }

        /* ===== Panel (container untuk container query) ===== */
        .rr-panel{
            position:relative;
            container-type:inline-size;
            container-name:rrpanel;
            background:
                radial-gradient(720px 260px at 90% -20%, rgba(31,117,254,.09), transparent 60%),
                radial-gradient(560px 240px at -10% 120%, rgba(24,154,82,.07), transparent 60%),
                #f6f8fc;
            border:1px solid var(--rr-line);
            border-radius:18px;
            padding:18px;
            min-width:0;
        }
        .rr-panel::before{
            content:'';
            position:absolute; inset:0; border-radius:inherit;
            background-image:radial-gradient(rgba(18,32,58,.05) 1px, transparent 1.4px);
            background-size:20px 20px;
            -webkit-mask-image:linear-gradient(180deg,#000 0%,transparent 55%);
            mask-image:linear-gradient(180deg,#000 0%,transparent 55%);
            pointer-events:none;
        }
        .rr-panel > *{ position:relative; }

        /* ===== Filter bar ===== */
        .filter-bar{ display:flex; gap:12px; margin:0 0 16px; align-items:center; flex-wrap:wrap; }
        .rr-search{ position:relative; flex:1 1 220px; min-width:0; }
        .rr-search i{
            position:absolute; left:14px; top:50%; transform:translateY(-50%);
            color:#93a1b5; font-size:13px; pointer-events:none;
        }
        .filter-bar .form-control{
            border-radius:10px; border:1px solid #dfe4ee;
            padding:9px 13px; font-size:13.5px; height:auto;
            background:#fff; color:var(--rr-ink);
            transition:border-color .2s, box-shadow .2s;
        }
        .rr-search .form-control{ padding-left:38px; width:100%; }
        .filter-bar .form-control:focus{
            border-color:var(--rr-blue);
            box-shadow:0 0 0 3px rgba(31,117,254,.14);
            outline:0;
        }
        .rr-select{
            width:230px; max-width:100%; flex:0 1 auto; cursor:pointer;
            -webkit-appearance:none; appearance:none;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b7a8f' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat:no-repeat;
            background-position:right 13px center;
            padding-right:36px;
        }
        .rr-count{
            margin-left:auto; font-size:12.5px; color:#7b8aa0;
            font-variant-numeric:tabular-nums; white-space:nowrap;
        }

        /* ===== Header kolom ===== */
        .rr-head{
            display:grid; grid-template-columns:var(--rr-cols); gap:16px;
            padding:4px 18px 12px 22px;
            font-family:var(--rr-display);
            font-size:10.5px; font-weight:600;
            letter-spacing:.14em; text-transform:uppercase; color:#8593a8;
            min-width:0;
        }
        .rr-head span{ display:flex; align-items:center; gap:6px; min-width:0; }
        .rr-head span::before{ content:''; width:5px; height:5px; border-radius:2px; background:#c3cddb; flex:0 0 auto; }

        /* ===== Kartu ===== */
        .leave-card{
            position:relative;
            background:#fff;
            border:1px solid var(--rr-line);
            border-radius:14px;
            padding:16px 18px 16px 22px;
            margin-bottom:12px;
            display:grid;
            grid-template-columns:var(--rr-cols);
            gap:16px;
            align-items:start;
            overflow:hidden;
            min-width:0;
            box-shadow:0 2px 6px rgba(18,32,58,.04);
            transition:box-shadow .22s ease, transform .22s ease, border-color .22s ease;
            animation:rr-in .5s cubic-bezier(.22,.7,.3,1) backwards;
        }
        .leave-card:last-of-type{ margin-bottom:0; }
        .leave-card::before{
            content:'';
            position:absolute; left:0; top:0; bottom:0; width:4px;
            background:#a8b6ca;
            transition:width .2s ease;
        }
        .leave-card[data-status="draft"]::before{ background:#f0a020; }
        .leave-card[data-status="acknowledge"]::before{ background:#1f75fe; }
        .leave-card[data-status="approved"]::before{ background:#18a058; }
        .leave-card[data-status^="reject"]::before{ background:#e5484d; }
        .leave-card:hover{
            box-shadow:0 10px 26px rgba(18,32,58,.10);
            transform:translateY(-2px);
            border-color:#dbe4f2;
        }
        .leave-card:hover::before{ width:6px; }

        .leave-meta,.leave-date,.leave-type,.leave-status{ min-width:0; }
        .leave-name,.leave-sub,.leave-date strong,.rr-alasan,.reject-info,.rr-jenis{ overflow-wrap:anywhere; }

        /* --- Kolom karyawan (tanpa avatar) --- */
        .leave-meta{ display:flex; flex-direction:column; align-items:flex-start; }
        .leave-name{
            font-family:var(--rr-display);
            font-size:15px; font-weight:700; letter-spacing:-.01em;
            color:#16233b; line-height:1.25;
        }
        .leave-sub{
            font-size:12.5px; color:#67788f; margin-top:4px;
            display:flex; align-items:center; gap:7px; flex-wrap:wrap;
        }
        .rr-nik{
            font-variant-numeric:tabular-nums;
            background:#f1f4f9; padding:1px 7px; border-radius:6px;
            font-size:11.5px; font-weight:600; color:#44546a;
        }
        .rr-doc{
            display:inline-flex; align-items:center; gap:6px;
            margin-top:8px; padding:4px 10px; border-radius:999px;
            background:#eaf2ff; color:var(--rr-blue);
            font-size:11.5px; font-weight:600; text-decoration:none;
            max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
            transition:all .18s ease;
        }
        .rr-doc:hover{
            background:var(--rr-blue); color:#fff; text-decoration:none;
            transform:translateY(-1px);
            box-shadow:0 5px 12px rgba(31,117,254,.3);
        }

        /* --- Kolom tanggal & jenis (desktop: vertikal) --- */
        .leave-date,
        .leave-type{ display:flex; flex-direction:column; gap:11px; }
        .leave-date small,
        .leave-type small{
            display:flex; align-items:center; gap:6px;
            color:#8a96a8; font-family:var(--rr-display);
            font-size:10.5px; font-weight:600;
            text-transform:uppercase; letter-spacing:.08em;
            margin-bottom:5px;
        }
        .leave-date small i,
        .leave-type small i{ font-size:10px; color:#aeb9ca; }
        .leave-date strong{
            font-family:var(--rr-display);
            font-size:13.5px; font-weight:600; color:#22334c;
            font-variant-numeric:tabular-nums;
            overflow-wrap:anywhere;
        }
        .rr-date-strong strong{
            display:inline-block;
            background:#eaf2ff; color:#135fce;
            padding:2px 8px; border-radius:6px;
        }

        .rr-jenis{
            display:inline-block;
            border:1px solid #dfe6f0; background:#f8fafd;
            padding:4px 10px; border-radius:7px;
            font-weight:700; font-size:12px; letter-spacing:.02em; color:#33465f;
        }
        .rr-alasan{
            margin:4px 0 0;
            font-size:13px; color:#44546a; line-height:1.5;
            display:-webkit-box;
            -webkit-line-clamp:3;
            -webkit-box-orient:vertical;
            overflow:hidden;
            overflow-wrap:anywhere;
        }

        /* --- Kolom status --- */
        .leave-status{ display:flex; flex-direction:column; align-items:stretch; gap:9px; min-width:0; }
        .rr-status-row{ display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .status-pill{
            display:inline-flex; align-items:center; gap:6px;
            padding:5px 11px; border-radius:999px;
            font-family:var(--rr-display);
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
        .status-ack::before{ animation:rr-pulse 1.8s ease-in-out infinite; }

        .rr-revert{
            width:27px; height:27px; border-radius:8px;
            display:inline-flex; align-items:center; justify-content:center;
            background:#f2f5fa; border:1px solid #e0e7f1; color:#5a6b83;
            font-size:12px; cursor:pointer; transition:all .2s ease;
            flex:0 0 auto;
        }
        .rr-revert:hover{ background:var(--rr-ink); border-color:var(--rr-ink); color:#fff; transform:rotate(-45deg); }
        .rr-revert.rr-revert-sm{
            width:22px; height:22px; border-radius:6px; font-size:10px;
            margin-left:auto;
            background:#fff;
        }

        .reject-info{
            width:100%;
            background:#fff7f7; border:1px solid #f7d7d7; border-left:3px solid #e5484d;
            border-radius:8px; padding:8px 11px;
            font-size:12px; color:#7c3d3d; line-height:1.5;
            overflow-wrap:anywhere;
        }
        .reject-info small{
            display:flex; align-items:center; gap:5px;
            font-weight:700; color:#c23b3b;
            margin-bottom:3px; font-size:10px;
            text-transform:uppercase; letter-spacing:.06em;
            white-space:nowrap;
        }
        .reject-info small i{ flex:0 0 auto; }

        /* --- Kolom aksi --- */
        .leave-actions{ display:flex; flex-direction:column; gap:8px; min-width:150px; }
        .leave-actions .btn{
            display:inline-flex; align-items:center; justify-content:center; gap:8px;
            width:100%;
            font-family:var(--rr-body); font-size:13px; font-weight:600;
            padding:8px 16px; border-radius:9px; border:0; cursor:pointer;
            transition:transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }
        .leave-actions .btn:active{ transform:translateY(1px) scale(.99); }
        .leave-actions .btn-success{
            color:#fff;
            background:linear-gradient(180deg,#27bb6e,#189a52);
            box-shadow:0 6px 14px rgba(24,154,82,.28);
        }
        .leave-actions .btn-success:hover{ transform:translateY(-2px); box-shadow:0 10px 20px rgba(24,154,82,.34); filter:brightness(1.04); }
        .leave-actions .btn-danger{
            color:#fff;
            background:linear-gradient(180deg,#f2666a,#d84a4f);
            box-shadow:0 6px 14px rgba(216,74,79,.25);
        }
        .leave-actions .btn-danger:hover{ transform:translateY(-2px); box-shadow:0 10px 20px rgba(216,74,79,.30); filter:brightness(1.04); }
        .rr-noaction{
            display:inline-flex; align-items:center; justify-content:center; gap:7px;
            width:100%; padding:8px 12px; border-radius:9px;
            font-size:12px; color:#93a1b5;
            background:#f6f8fb; border:1px dashed #dfe6f0;
        }
        .rr-noaction.rr-done{ color:#0d7a3f; background:#effaf3; border-color:#cdeeda; border-style:solid; }
        .rr-noaction.rr-rejected{ color:#c23b3b; background:#fff7f7; border-color:#f6caca; border-style:solid; }

        /* ===== Empty state ===== */
        .rr-empty{
            text-align:center; padding:46px 20px;
            background:#fff; border:1.5px dashed #dde5f0; border-radius:14px;
        }
        .rr-empty-icon{
            width:64px; height:64px; margin:0 auto 14px;
            display:flex; align-items:center; justify-content:center;
            border-radius:20px; font-size:26px;
            background:linear-gradient(180deg,#f2f6fd,#e8eefa); color:#9fb0c8;
            box-shadow:inset 0 0 0 1px #e3eaf5;
            animation:rr-float 3.2s ease-in-out infinite;
        }
        .rr-empty-title{ font-family:var(--rr-display); font-weight:600; font-size:15.5px; color:#33465f; }
        .rr-empty-sub{ font-size:13px; color:#8a97ab; margin-top:3px; }
        .rr-empty-mini{ padding:30px 16px; margin-top:12px; }

        /* ===== Animasi ===== */
        @keyframes rr-in{
            from{ opacity:0; transform:translateY(14px) scale(.99); }
            to{ opacity:1; transform:none; }
        }
        @keyframes rr-pulse{ 0%,100%{ opacity:1; } 50%{ opacity:.3; } }
        @keyframes rr-float{ 0%,100%{ transform:translateY(0); } 50%{ transform:translateY(-6px); } }

        .rr-chip:focus-visible,
        .rr-revert:focus-visible,
        .rr-doc:focus-visible,
        .leave-actions .btn:focus-visible{ outline:2px solid var(--rr-blue); outline-offset:2px; }

        /* =====================================================
           RESPONSIF — berbasis lebar PANEL (container query)
           ===================================================== */
        @container rrpanel (max-width: 940px){
            .rr-head{ display:none; }
            .leave-card{
                grid-template-columns:1fr 1fr;
                grid-template-areas:
                    "meta    meta"
                    "date    type"
                    "status  status"
                    "actions actions";
                gap:14px;
                padding:16px 16px 16px 20px;
            }
            .leave-meta{ grid-area:meta; }
            .leave-date{ grid-area:date; }
            .leave-type{ grid-area:type; }
            .leave-status{
                grid-area:status;
                border-top:1px dashed var(--rr-line);
                padding-top:12px;
            }
            .leave-actions{
                grid-area:actions;
                flex-direction:row;
                flex-wrap:wrap;
                min-width:0;
                border-top:1px dashed var(--rr-line);
                padding-top:12px;
            }
            .leave-actions .btn{ width:auto; flex:0 1 auto; min-width:150px; }
            .rr-noaction{ width:auto; }

            /* MOBILE/TABLET: Pengajuan|Resign & Jenis|Alasan jadi 1 baris */
            .leave-date,
            .leave-type{
                display:grid;
                grid-template-columns:1fr 1fr;
                gap:12px;
                align-content:start;
            }
        }

        @container rrpanel (max-width: 560px){
            .leave-card{
                grid-template-columns:1fr;
                grid-template-areas:
                    "meta"
                    "date"
                    "type"
                    "status"
                    "actions";
            }
            .leave-actions{ flex-direction:column; }
            .leave-actions .btn{ width:100%; }
        }

        /* Fallback viewport (browser lama tanpa container query) */
        @media (max-width:940px) and (not (container-type: inline-size)){
            .rr-head{ display:none; }
            .leave-card{
                grid-template-columns:1fr 1fr;
                grid-template-areas:
                    "meta    meta"
                    "date    type"
                    "status  status"
                    "actions actions";
                gap:14px;
            }
            .leave-meta{ grid-area:meta; }
            .leave-date{ grid-area:date; }
            .leave-type{ grid-area:type; }
            .leave-status{ grid-area:status; border-top:1px dashed var(--rr-line); padding-top:12px; }
            .leave-actions{ grid-area:actions; flex-direction:row; flex-wrap:wrap; min-width:0; border-top:1px dashed var(--rr-line); padding-top:12px; }
            .leave-actions .btn{ width:auto; flex:0 1 auto; min-width:150px; }
            .rr-noaction{ width:auto; }

            .leave-date,
            .leave-type{
                display:grid;
                grid-template-columns:1fr 1fr;
                gap:12px;
                align-content:start;
            }
        }

        @media (max-width:768px){
            .filter-bar{ flex-direction:column; align-items:stretch; }
            .rr-select{ width:100%; }
            .rr-count{ margin:0; text-align:center; }
            .rr-panel{ padding:13px; }
        }

        @media (max-width:560px) and (not (container-type: inline-size)){
            .leave-card{
                grid-template-columns:1fr;
                grid-template-areas: "meta" "date" "type" "status" "actions";
            }
            .leave-actions{ flex-direction:column; }
            .leave-actions .btn{ width:100%; }
        }

        @media (prefers-reduced-motion: reduce){
            .leave-card,
            .status-pill::before,
            .rr-empty-icon{ animation:none !important; }
            *{ transition:none !important; }
        }
    </style>

<?php if (!empty($list_usulan)) { ?>

    <?php
    $statusList = [
        1 => 'DRAFT',
        2 => 'ACKNOWLEDGE',
        3 => 'APPROVED',
        4 => 'REJECT ATASAN',
        5 => 'REJECT HRD'
    ];

    // hitung per status untuk chip ringkasan
    $cnt = ['all' => count($list_usulan), 'draft' => 0, 'acknowledge' => 0, 'approved' => 0, 'reject' => 0];
    foreach($list_usulan as $c){
        if($c['status'] == 1) $cnt['draft']++;
        elseif($c['status'] == 2) $cnt['acknowledge']++;
        elseif($c['status'] == 3) $cnt['approved']++;
        elseif($c['status'] == 4 || $c['status'] == 5) $cnt['reject']++;
    }
    ?>

    <p class="rr-sub">Ringkasan pengajuan pengunduran diri karyawan — klik status di bawah untuk memfilter cepat.</p>

    <div class="rr-stats">
        <button type="button" class="rr-chip active" data-status="all"><span class="rr-dot rr-dot-all"></span>Semua <b><?php echo $cnt['all'];?></b></button>
        <button type="button" class="rr-chip" data-status="draft"><span class="rr-dot rr-dot-draft"></span>Draft <b><?php echo $cnt['draft'];?></b></button>
        <button type="button" class="rr-chip" data-status="acknowledge"><span class="rr-dot rr-dot-ack"></span>Acknowledge <b><?php echo $cnt['acknowledge'];?></b></button>
        <button type="button" class="rr-chip" data-status="approved"><span class="rr-dot rr-dot-ok"></span>Approved <b><?php echo $cnt['approved'];?></b></button>
        <button type="button" class="rr-chip" data-status="reject"><span class="rr-dot rr-dot-no"></span>Reject <b><?php echo $cnt['reject'];?></b></button>
    </div>

    <div class="rr-panel">

        <div class="filter-bar">
            <div class="rr-search">
                <i class="fa fa-search" aria-hidden="true"></i>
                <input id="filter-search"
                       type="search"
                       class="form-control"
                       placeholder="Cari karyawan, NIK, jabatan, atau alasan...">
            </div>

            <select id="filter-status" class="form-control rr-select">
                <option value="all">Semua Status</option>
                <option value="draft">DRAFT</option>
                <option value="acknowledge">ACKNOWLEDGE</option>
                <option value="approved">APPROVED</option>
                <option value="reject">REJECT (Atasan + HRD)</option>
                <option value="reject atasan">REJECT ATASAN</option>
                <option value="reject hrd">REJECT HRD</option>
            </select>

            <span id="filter-count" class="rr-count"></span>
        </div>

        <div class="rr-head" aria-hidden="true">
            <span>Karyawan</span>
            <span>Tanggal</span>
            <span>Jenis &amp; Alasan</span>
            <span>Status</span>
            <span>Aksi</span>
        </div>

        <?php $i = 0; foreach($list_usulan as $o){ ?>

            <?php
                $status = isset($statusList[$o['status']]) ? $statusList[$o['status']] : '-';

                $statusClass = 'status-draft';
                switch($o['status']){
                    case 2: $statusClass = 'status-ack'; break;
                    case 3: $statusClass = 'status-approved'; break;
                    case 4:
                    case 5: $statusClass = 'status-reject'; break;
                }

                $is_reject = ($o['status'] == 4 || $o['status'] == 5);

                $delay = min($i * 45, 360);
            ?>

            <div class="leave-card"
                style="animation-delay:<?php echo $delay;?>ms"
                data-name="<?php echo htmlspecialchars(strtolower($o['nama_karyawan']), ENT_QUOTES);?>"
                data-nik="<?php echo htmlspecialchars(strtolower($o['nik']), ENT_QUOTES);?>"
                data-jabatan="<?php echo htmlspecialchars(strtolower($o['nama_jabatan']), ENT_QUOTES);?>"
                data-jenis="<?php echo htmlspecialchars(strtolower($o['jenis_resign']), ENT_QUOTES);?>"
                data-status="<?php echo strtolower($status);?>"
                data-alasan="<?php echo htmlspecialchars(strtolower($o['alasan_resign']), ENT_QUOTES);?>">

                <div class="leave-meta">
                    <div class="leave-name">
                        <?php echo htmlspecialchars(ucwords(strtolower($o['nama_karyawan'])), ENT_QUOTES);?>
                    </div>
                    <div class="leave-sub">
                        <span class="rr-nik"><?php echo htmlspecialchars($o['nik'], ENT_QUOTES);?></span>
                        <?php echo htmlspecialchars($o['nama_jabatan'], ENT_QUOTES);?>
                    </div>
                    <a href="javascript:void(0)" class="rr-doc" id_data="<?php echo $o['id']?>" onclick="ukr.showAttachment(this, event)">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i><?php echo htmlspecialchars($o['document'], ENT_QUOTES);?>
                    </a>
                </div>

                <div class="leave-date">
                    <div>
                        <small><i class="fa fa-calendar-o" aria-hidden="true"></i> Tanggal Pengajuan</small>
                        <strong><?php echo tglIndonesia($o['tanggal_pengajuan'], '-', ' '); ?></strong>
                    </div>
                    <div class="rr-date-strong">
                        <small><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Tanggal Resign</small>
                        <strong><?php echo tglIndonesia($o['tanggal_resign'], '-', ' '); ?></strong>
                    </div>
                </div>

                <div class="leave-type">
                    <div>
                        <small>Jenis Resign</small>
                        <span class="rr-jenis"><?php echo !empty($o['jenis_resign']) ? htmlspecialchars($o['jenis_resign'], ENT_QUOTES) : '-'; ?></span>
                    </div>
                    <div>
                        <small>Alasan</small>
                        <p class="rr-alasan" title="<?php echo htmlspecialchars($o['alasan_resign'], ENT_QUOTES);?>"><?php echo htmlspecialchars($o['alasan_resign'], ENT_QUOTES);?></p>
                    </div>
                </div>

                <div class="leave-status">

                    <div class="rr-status-row">
                        <span class="status-pill <?php echo $statusClass;?>"><?php echo $status;?></span>

                        <?php if(!$is_reject){ ?>
                            <?php if( $o['status'] == 2 && $o['ack_by'] == $config ){ ?>
                                <span revert="DRAFT" id_data="<?php echo $o['id'];?>" onclick="ukr.revert_status(this,event)" class="rr-revert"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Kembalikan ke Draft">
                                    <i class="fa fa-undo" aria-hidden="true"></i>
                                </span>
                            <?php } ?>
                            <?php if( $o['status'] == 3 && $o['approved_by'] == $config ){ ?>
                                <span revert="ACK" id_data="<?php echo $o['id'];?>" onclick="ukr.revert_status(this,event)" class="rr-revert"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Kembalikan ke Acknowledge">
                                    <i class="fa fa-undo" aria-hidden="true"></i>
                                </span>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <?php if($is_reject){ ?>
                        <div class="reject-info">
                            <small>
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                Keterangan Reject
                                <?php if( $o['status'] == 4 && $o['ack_by'] == $config ){ ?>
                                    <span revert="DRAFT" id_data="<?php echo $o['id'];?>" onclick="ukr.revert_status(this,event)" class="rr-revert rr-revert-sm"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Kembalikan ke Draft">
                                        <i class="fa fa-undo" aria-hidden="true"></i>
                                    </span>
                                <?php } ?>
                                <?php if( $o['status'] == 5 && $o['approved_by'] == $config ){ ?>
                                    <span revert="ACK" id_data="<?php echo $o['id'];?>" onclick="ukr.revert_status(this,event)" class="rr-revert rr-revert-sm"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Kembalikan ke Acknowledge">
                                        <i class="fa fa-undo" aria-hidden="true"></i>
                                    </span>
                                <?php } ?>
                            </small>
                            <?php echo htmlspecialchars($o['keterangan_reject'], ENT_QUOTES);?>
                        </div>
                    <?php } ?>

                </div>

                <div class="leave-actions">

                    <?php if($o['status'] == 1){ ?>

                        <?php if(isset($akses['a_ack']) && $akses['a_ack'] == 1){ ?>

                            <button class="btn btn-success" id_data="<?php echo $o['id'];?>" value="2" onclick="ukr.keputusanUsulan(this,event)">
                                <i class="fa fa-check"></i> Acknowledge
                            </button>

                            <button class="btn btn-danger" id_data="<?php echo $o['id'];?>" value="4" onclick="ukr.keputusanUsulan(this,event)">
                                <i class="fa fa-times"></i> Reject
                            </button>

                        <?php } else { ?>
                            <span class="rr-noaction"><i class="fa fa-hourglass-half" aria-hidden="true"></i> Menunggu acknowledge</span>
                        <?php } ?>

                    <?php } elseif($o['status'] == 2){ ?>

                        <?php if(isset($akses['a_approve']) && $akses['a_approve'] == 1){ ?>

                            <button class="btn btn-success" id_data="<?php echo $o['id'];?>" value="3" onclick="ukr.keputusanUsulan(this,event)">
                                <i class="fa fa-check"></i> Approve
                            </button>

                            <button class="btn btn-danger" id_data="<?php echo $o['id'];?>" value="5" onclick="ukr.keputusanUsulan(this,event)">
                                <i class="fa fa-times"></i> Reject
                            </button>

                        <?php } else { ?>
                            <span class="rr-noaction"><i class="fa fa-hourglass-half" aria-hidden="true"></i> Menunggu approve</span>
                        <?php } ?>

                    <?php } elseif($o['status'] == 3){ ?>
                        <span class="rr-noaction rr-done"><i class="fa fa-check-circle" aria-hidden="true"></i> Selesai diproses</span>
                    <?php } elseif($is_reject){ ?>
                        <span class="rr-noaction rr-rejected"><i class="fa fa-times-circle" aria-hidden="true"></i> Ditolak</span>
                    <?php } else { ?>
                        <span class="rr-noaction"><i class="fa fa-ban" aria-hidden="true"></i> Tidak ada aksi</span>
                    <?php } ?>

                </div>

            </div>

        <?php $i++; } ?>

        <div class="rr-empty rr-empty-mini" id="rr-noresult" hidden>
            <div class="rr-empty-icon"><i class="fa fa-search" aria-hidden="true"></i></div>
            <div class="rr-empty-title">Tidak ada hasil yang cocok</div>
            <div class="rr-empty-sub">Coba ubah kata kunci pencarian atau filter status.</div>
        </div>

    </div>

<?php } else { ?>

    <div class="rr-empty">
        <div class="rr-empty-icon"><i class="fa fa-inbox" aria-hidden="true"></i></div>
        <div class="rr-empty-title">Belum ada laporan resign</div>
        <div class="rr-empty-sub">Usulan pengunduran diri dari karyawan akan tampil di sini.</div>
    </div>

<?php } ?>

<script>
(function(){

    var search    = document.getElementById('filter-search');
    var selStatus = document.getElementById('filter-status');
    var countEl   = document.getElementById('filter-count');
    var noResult  = document.getElementById('rr-noresult');

    if(!search || !selStatus){ return; }

    var cards = Array.prototype.slice.call(document.querySelectorAll('.leave-card'));
    var chips = Array.prototype.slice.call(document.querySelectorAll('.rr-chip'));

    function apply(){

        var q  = search.value.trim().toLowerCase();
        var st = selStatus.value.toLowerCase();
        var visible = 0;

        cards.forEach(function(card){

            var text =
                card.dataset.name + ' ' +
                card.dataset.nik + ' ' +
                card.dataset.jabatan + ' ' +
                card.dataset.jenis + ' ' +
                card.dataset.alasan;

            var okSearch = !q || text.indexOf(q) > -1;
            var okStatus = (st === 'all') || card.dataset.status.indexOf(st) > -1;
            var show     = okSearch && okStatus;

            card.style.display = show ? '' : 'none';
            if(show) visible++;

        });

        if(countEl) countEl.textContent = 'Menampilkan ' + visible + ' dari ' + cards.length + ' usulan';
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

    search.addEventListener('keyup', apply);
    selStatus.addEventListener('change', apply);

    apply();

})();
</script>

</fieldset>
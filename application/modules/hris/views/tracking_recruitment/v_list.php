<style>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Public+Sans:wght@400;500;600;700&display=swap');

.tl-list{
    --tl-ink:#16233b;
    --tl-muted:#67788f;
    --tl-line:#e3e8f2;
    --tl-blue:#1f75fe;
    --tl-display:'Space Grotesk','Segoe UI',sans-serif;
    --tl-body:'Public Sans','Segoe UI',system-ui,sans-serif;
    font-family:var(--tl-body);
    color:var(--tl-ink);
    min-width:0;
}

/* ===== Kartu ===== */
.tl-card{
    position:relative;
    background:#fff;
    border:1px solid var(--tl-line);
    border-radius:14px;
    padding:14px 16px 16px 20px;
    margin-bottom:12px;
    overflow:hidden;
    box-shadow:0 2px 6px rgba(18,32,58,.04);
    transition:box-shadow .22s ease, transform .22s ease, border-color .22s ease;
    animation:tl-in .5s cubic-bezier(.22,.7,.3,1) backwards;
}
.tl-card::before{
    content:'';
    position:absolute; left:0; top:0; bottom:0; width:4px;
    background:linear-gradient(180deg,#1f75fe,#66a5ff);
    transition:width .2s ease;
}
.tl-card:hover{
    box-shadow:0 10px 26px rgba(18,32,58,.10);
    transform:translateY(-2px);
    border-color:#dbe4f2;
}
.tl-card:hover::before{ width:6px; }

/* ===== Header kartu ===== */
.tl-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:13px;
}
.tl-doc{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:6px 13px;
    border-radius:999px;
    background:#eaf2ff;
    border:1px solid #d5e5ff;
    color:#135fce;
    font-family:var(--tl-display);
    font-size:12.5px;
    font-weight:700;
    letter-spacing:.02em;
    min-width:0;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    max-width:100%;
}
.tl-doc i{ flex:0 0 auto; }

.tl-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    border:0;
    border-radius:9px;
    padding:8px 16px;
    font-family:var(--tl-body);
    font-size:13px;
    font-weight:600;
    color:#fff;
    cursor:pointer;
    background:linear-gradient(180deg,#4c6ef5,#3b5bdb);
    box-shadow:0 6px 14px rgba(59,91,219,.28);
    transition:transform .18s ease, box-shadow .18s ease, filter .18s ease;
    flex:0 0 auto;
}
.tl-btn:hover{
    transform:translateY(-2px);
    filter:brightness(1.05);
    box-shadow:0 10px 20px rgba(59,91,219,.34);
    color:#fff;
}
.tl-btn:active{ transform:translateY(1px) scale(.99); }

/* ===== Body: 3 grup info ===== */
.tl-body{
    display:grid;
    grid-template-columns:repeat(3, minmax(0,1fr));
    gap:14px;
}
.tl-group{
    display:flex;
    align-items:center;
    gap:12px;
    min-width:0;
}
.tl-group + .tl-group{
    border-left:1px dashed #e1e8f2;
    padding-left:14px;
}

.tl-icon{
    flex:0 0 auto;
    width:42px; height:42px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:17px;
    color:#fff;
    box-shadow:0 5px 12px rgba(18,32,58,.18);
}
.tl-ic-1{ background:linear-gradient(135deg,#1f75fe,#5ea2ff); }
.tl-ic-2{ background:linear-gradient(135deg,#5f3dc4,#9163f0); }
.tl-ic-3{ background:linear-gradient(135deg,#0ca678,#3dd598); }

.tl-item{ min-width:0; }
.tl-item strong{
    display:block;
    font-family:var(--tl-display);
    font-size:10.5px;
    font-weight:600;
    letter-spacing:.08em;
    text-transform:uppercase;
    color:#8a96a8;
    margin-bottom:3px;
}
.tl-item span{
    display:block;
    font-size:14px;
    font-weight:600;
    color:#22304a;
    line-height:1.4;
    overflow-wrap:anywhere;
}

/* ===== Empty state ===== */
.tl-empty{
    text-align:center;
    padding:40px 16px;
    background:#fff;
    border:1.5px dashed #dde5f0;
    border-radius:14px;
    color:#8a97ab;
    font-size:13px;
}
.tl-empty i{
    display:block;
    font-size:30px;
    color:#b9c6d8;
    margin-bottom:10px;
}

/* ===== Animasi ===== */
@keyframes tl-in{
    from{ opacity:0; transform:translateY(14px) scale(.99); }
    to{ opacity:1; transform:none; }
}
.tl-btn:focus-visible{ outline:2px solid var(--tl-blue); outline-offset:2px; }

/* ===== Tablet ===== */
@media (max-width: 991.98px){
    .tl-body{
        grid-template-columns:1fr 1fr;
    }
    .tl-group + .tl-group{
        border-left:0;
        padding-left:0;
    }
    .tl-group:nth-child(3){
        grid-column:1 / -1;
        border-top:1px dashed #e1e8f2;
        padding-top:12px;
    }
}

/* ===== Mobile ===== */
@media (max-width: 640px){
    .tl-card{ padding:12px 14px 14px 18px; }
    .tl-top{ flex-direction:column; align-items:stretch; }
    .tl-doc{ justify-content:center; }
    .tl-btn{ width:100%; }
    .tl-body{ grid-template-columns:1fr; gap:12px; }
    .tl-group:nth-child(3){
        border-top:1px dashed #e1e8f2;
        padding-top:12px;
    }
    .tl-group + .tl-group{
        border-top:1px dashed #e1e8f2;
        padding-top:12px;
    }
}

@media (prefers-reduced-motion: reduce){
    .tl-card{ animation:none !important; }
    *{ transition:none !important; }
}
</style>

<div class="tl-list">

    <?php if(!empty($list)){ ?>

        <?php $i = 0; foreach($list as $l){ ?>
            <div class="tl-card" style="animation-delay:<?php echo min($i * 45, 360); ?>ms">

                <div class="tl-top">
                    <span class="tl-doc">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        <?php echo $l['document'] ?>
                    </span>

                    <button type="button" class="tl-btn" document="<?php echo $l['document'] ?>" onclick="tr.show_tracking(this,event)">
                        <i class="fa fa-eye" aria-hidden="true"></i> Lihat Tracking
                    </button>
                </div>

                <div class="tl-body">

                    <div class="tl-group">
                        <span class="tl-icon tl-ic-1"><i class="fa fa-user" aria-hidden="true"></i></span>
                        <div class="tl-item">
                            <strong>Nama Pengusul</strong>
                            <span><?php echo ucwords(strtolower($l['nama_pengusul'])) ?></span>
                        </div>
                    </div>

                    <div class="tl-group">
                        <span class="tl-icon tl-ic-2"><i class="fa fa-briefcase" aria-hidden="true"></i></span>
                        <div class="tl-item">
                            <strong>Posisi yang Dibutuhkan</strong>
                            <span><?php echo $l['nama_jabatan'] ?></span>
                        </div>
                    </div>

                    <div class="tl-group">
                        <span class="tl-icon tl-ic-3"><i class="fa fa-users" aria-hidden="true"></i></span>
                        <div class="tl-item">
                            <strong>Jumlah</strong>
                            <span><?php echo $l['jumlah'] . ' Orang' ?></span>
                        </div>
                    </div>

                </div>

            </div>
        <?php $i++; } ?>

    <?php } else { ?>

        <div class="tl-empty">
            <i class="fa fa-inbox" aria-hidden="true"></i>
            Belum ada data tracking usulan.
        </div>

    <?php } ?>

</div>
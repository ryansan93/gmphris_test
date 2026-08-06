<fieldset class="mb-3">
    <legend>Laporan Resign</legend>

    <style>
        .leave-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(15,23,42,0.05);
            padding: 16px 18px;
            margin-bottom: 12px;
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr 1.5fr auto;
            align-items: center;
            gap: 16px;
            border: 1px solid #f1f3f5;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .leave-card:hover {
            box-shadow: 0 8px 22px rgba(15,23,42,0.08);
            transform: translateY(-1px);
        }

        .leave-name{
            font-size:15px;
            font-weight:600;
            color:#1a2332;
            margin-bottom: 2px;
        }

        .leave-sub{
            color:#6b7a85;
            font-size:13px;
            margin-bottom: 4px;
        }

        .leave-meta a {
            color: #1f75fe;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
        }

        .leave-meta a:hover {
            text-decoration: underline;
        }

        .status-pill{
            padding:6px 12px;
            border-radius:999px;
            font-size:11px;
            font-weight:700;
            letter-spacing: 0.3px;
            display:inline-block;
            text-transform: uppercase;
        }

        .status-approved{
            background:#e6f7ec;
            color:#0b6b2f;
        }

        .status-ack{
            background:#e8f1ff;
            color:#0b539f;
        }

        .status-draft{
            background:#fff7e6;
            color:#a66b00;
        }

        .status-reject{
            background:#fff0f0;
            color:#c23b3b;
        }

        .leave-date,
        .leave-type {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            align-content: start;
        }

        .leave-date small,
        .leave-type small {
            display: block;
            margin-bottom: 4px;
            color: #8a96a3;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space:nowrap;
        }

        .leave-date div strong,
        .leave-type div {
            font-size: 13px;
            color: #2c3e50;
        }

        .leave-status {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .leave-actions{
            display:flex;
            gap:8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .leave-actions .btn {
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 8px;
        }

        .reject-info {
            background: #fff8f8;
            border: 1px solid #ffe0e0;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            color: #8a3a3a;
            line-height: 1.4;
            flex: 1;
            min-width: 150px;
        }

        .reject-info small {
            display: block;
            font-weight: 600;
            color: #c23b3b;
            margin-bottom: 2px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .filter-bar {
            display: flex;
            gap: 12px;
            margin: 16px 0 20px 0;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-bar .form-control {
            border-radius: 8px;
            border: 1px solid #e1e5eb;
            padding: 8px 12px;
            font-size: 14px;
        }

        .filter-bar .form-control:focus {
            border-color: #1f75fe;
            box-shadow: 0 0 0 3px rgba(31,117,254,0.1);
            outline: none;
        }

        @media (max-width: 1200px) {
            .leave-card {
                grid-template-columns: 1fr 1fr 1fr 1.3fr auto;
                gap: 12px;
                padding: 14px 16px;
            }
        }

        @media (max-width: 992px) {
            .leave-card {
                grid-template-columns: 1fr 1fr;
                grid-template-rows: auto auto auto;
                gap: 12px;
                padding: 16px;
            }

            .leave-meta {
                grid-column: 1 / -1;
                grid-row: 1;
            }

            .leave-date {
                grid-column: 1 / -1;
                grid-row: 2;
                border-top: 1px solid #f1f3f5;
                padding-top: 12px;
            }

            .leave-type {
                grid-column: 1 / -1;
                grid-row: 3;
                border-top: 1px solid #f1f3f5;
                padding-top: 12px;
            }

            .leave-status {
                grid-column: 1 / -1;
                grid-row: 4;
                border-top: 1px solid #f1f3f5;
                padding-top: 12px;
            }

            .leave-actions {
                grid-column: 1 / -1;
                grid-row: 5;
                border-top: 1px solid #f1f3f5;
                padding-top: 12px;
            }
        }

        @media (max-width: 768px) {
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar .form-control {
                width: 100% !important;
            }

            .leave-card {
                grid-template-columns: 1fr;
                gap: 10px;
                padding: 15px;
            }

            .leave-meta,
            .leave-date,
            .leave-type,
            .leave-status,
            .leave-actions {
                grid-column: 1 / -1;
                border-top: 1px solid #f1f3f5;
                margin-top: 4px;
                padding-top: 12px;
            }

            .leave-date,
            .leave-type {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .leave-status {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }

            .leave-actions {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .leave-actions .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .leave-date,
            .leave-type {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <div class="filter-bar">
        <input id="filter-search"
               type="search"
               class="form-control"
               placeholder="Cari Karyawan / NIK / Jabatan / Alasan">

        <select id="filter-status" class="form-control" style="width:220px;">
            <option value="all">Semua Status</option>
            <option value="draft">DRAFT</option>
            <option value="acknowledge">ACKNOWLEDGE</option>
            <option value="approved">APPROVED</option>
            <option value="reject atasan">REJECT ATASAN</option>
            <option value="reject hrd">REJECT HRD</option>
        </select>
    </div>

<?php if (!empty($list_usulan)) { ?>

    <?php
    $statusList = [
        1 => 'DRAFT',
        2 => 'ACKNOWLEDGE',
        3 => 'APPROVED',
        4 => 'REJECT ATASAN',
        5 => 'REJECT HRD'
    ];
    ?>

    <?php foreach($list_usulan as $o){ ?>

        <?php

            $status = isset($statusList[$o['status']]) ? $statusList[$o['status']] : '-';

            $statusClass = 'status-draft';

            switch($o['status']){

                case 2:
                    $statusClass='status-ack';
                break;

                case 3:
                    $statusClass='status-approved';
                break;

                case 4:
                case 5:
                    $statusClass='status-reject';
                break;
            }

        ?>

        <div class="leave-card"
            data-name="<?php echo strtolower($o['nama_karyawan']);?>"
            data-nik="<?php echo strtolower($o['nik']);?>"
            data-jabatan="<?php echo strtolower($o['nama_jabatan']);?>"
            data-jenis="<?php echo strtolower($o['jenis_resign']);?>"
            data-status="<?php echo strtolower($status);?>"
            data-alasan="<?php echo strtolower($o['alasan_resign']);?>">

            <div class="leave-meta">

                <div class="leave-name">
                    <?php echo ucwords(strtolower($o['nama_karyawan']));?>
                </div>

                <div class="leave-sub">
                    <?php echo $o['nik'];?> • <?php echo $o['nama_jabatan'];?>
                </div>

                <small class="text-muted">
                    <a href="javascript:void(0)" id_data="<?php echo $o['id']?>" onclick="ukr.showAttachment(this, event)"><?php echo $o['document'];?></a>
                </small>

            </div>

            <div class="leave-date">

                <div>
                    <small>Tanggal Pengajuan</small>
                    <div><strong><?= tglIndonesia($o['tanggal_pengajuan'],'-',' '); ?></strong></div>
                </div>

                <div>
                    <small>Tanggal Resign</small>
                    <div><strong><?= tglIndonesia($o['tanggal_resign'],'-',' '); ?></strong></div>
                </div>

            </div>

            <div class="leave-type">

                <div>
                    <small>Jenis Resign</small>
                    <div><strong><?= !empty($o['jenis_resign']) ? $o['jenis_resign'] : '-'; ?></strong></div>
                </div>

                <div>
                    <small>Alasan</small>
                    <div><?= $o['alasan_resign']; ?></div>
                </div>

            </div>

            <div class="leave-status">

                <span class="status-pill <?php echo $statusClass;?>">
                    <?php echo $status;?>
                </span>
                
                <?php if( $o['status'] == 2 || $o['status'] == 4 ) { ?>

                    <?php if ($o['ack_by'] == $config){ ?>
                        <span revert="DRAFT" id_data="<?php echo $o['id'];?>" onclick="ukr.revert_status(this,event)" class="status-pill <?php echo $statusClass; ?>"
                            data-toggle="tooltip"
                            data-placement="top"
                            title="Kembalikan ke Draft">
                            <i class="fa fa-undo" aria-hidden="true"></i>
                        </span>
                    <?php } ?>  

                <?php } ?>  

                <?php if( $o['status'] == 3 || $o['status'] == 5 ) { ?>
                    <?php if ($o['approved_by'] == $config){ ?>
                        <span revert="ACK" id_data="<?php echo $o['id'];?>" onclick="ukr.revert_status(this,event)" class="status-pill <?php echo $statusClass; ?>"
                            data-toggle="tooltip"
                            data-placement="top"
                            title="Kembalikan ke Acknowledge">
                            <i class="fa fa-undo" aria-hidden="true"></i>
                        </span>
                    <?php } ?>  
                <?php } ?>

                <?php if( $o['status'] == 4 || $o['status'] == 5 ) { ?>
                    <div class="reject-info">
                        <small>Keterangan Reject</small>
                        <?php echo $o['keterangan_reject'];?>
                    </div>
                <?php } ?>

            </div>

            <div class="leave-actions">

                <?php if($o['status'] == 1){ ?>

                    <?php if(isset($akses['a_ack']) && $akses['a_ack']==1){ ?>

                        <button class="btn btn-success" id_data="<?php echo $o['id'];?>" value="2" onclick="ukr.keputusanUsulan(this,event)">
                            <i class="fa fa-check"></i>
                            Acknowledge
                        </button>

                        <button class="btn btn-danger" id_data="<?php echo $o['id'];?>" value="4" onclick="ukr.keputusanUsulan(this,event)">
                            <i class="fa fa-times"></i>
                            Reject
                        </button>

                    <?php } ?>

                <?php } ?>

                <?php if($o['status']==2){ ?>

                    <?php if(isset($akses['a_approve']) && $akses['a_approve']==1){ ?>

                        <button class="btn btn-success" id_data="<?php echo $o['id'];?>" value="3" onclick="ukr.keputusanUsulan(this,event)">
                            <i class="fa fa-check"></i>
                            Approve
                        </button>

                        <button class="btn btn-danger" id_data="<?php echo $o['id'];?>" value="5" onclick="ukr.keputusanUsulan(this,event)">
                            <i class="fa fa-times"></i>
                            Reject
                        </button>

                    <?php } ?>

                <?php } ?>

            </div>

        </div>

    <?php } ?>

<?php } else { ?>

    <div class="text-muted text-center py-4">
        <i class="fa fa-inbox" style="font-size: 32px; opacity: 0.3;"></i>
        <div class="mt-2">Tidak ada data</div>
    </div>

<?php } ?>

<script>

(function(){

    let search=document.getElementById('filter-search');
    let status=document.getElementById('filter-status');

    function apply(){

        let q=search.value.toLowerCase();
        let st=status.value.toLowerCase();

        document.querySelectorAll('.leave-card').forEach(function(card){

            let text=
                card.dataset.name+' '+
                card.dataset.nik+' '+
                card.dataset.jabatan+' '+
                card.dataset.jenis+' '+
                card.dataset.alasan;

            let okSearch=text.indexOf(q)>-1;

            let okStatus=(st=='all')
                ?true
                :card.dataset.status.indexOf(st)>-1;

            card.style.display=(okSearch && okStatus)?'':'none';

        });

    }

    search.addEventListener('keyup',apply);
    status.addEventListener('change',apply);

})();

</script>

</fieldset>
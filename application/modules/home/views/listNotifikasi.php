<style>
    .wrapper-notif {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 10px;
    }

    .card-notif {
        border: 1px solid #D9D9D9;
        border-left: 3px solid black;
        border-radius: 5px;
        padding: 10px;
        transition: all 0.2s ease;
        flex: 0 1 calc(25% - 10px);
        box-sizing: border-box;
        min-height: 50px;
        display: flex;
        flex-direction: column;
    }

    .card-notif:hover {
        cursor: pointer;
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        background-color: #f9f9f9;
    }

    .card-title {
        width: 100%;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 13px;
        color: #333;
    }

    .card-content {
        border: 1px solid #E8E8E8;
        padding: 8px;
        border-radius: 5px;
        background-color: #E8E8E8;
        font-size: 12px;
        flex: 1;
        overflow: hidden;
        word-break: break-word;
    }

    @media (max-width: 1200px) {
        .card-notif {
            flex: 0 1 calc(33.333% - 10px);
        }
    }

    @media (max-width: 768px) {
        .wrapper-notif {
            gap: 8px;
            padding: 8px;
        }
        
        .card-notif {
            flex: 0 1 calc(50% - 8px);
            padding: 8px;
            min-height: 110px;
        }
        
        .card-title {
            font-size: 12px;
            margin-bottom: 6px;
        }
        
        .card-content {
            font-size: 11px;
            padding: 6px;
        }
    }

    @media (max-width: 480px) {
        .wrapper-notif {
            gap: 6px;
            padding: 6px;
        }
        
        .card-notif {
            flex: 0 1 100%;
            padding: 10px;
            min-height: 100px;
        }
        
        .card-title {
            font-size: 13px;
            margin-bottom: 8px;
        }
        
        .card-content {
            font-size: 12px;
            padding: 8px;
        }
    }
</style>

<!-- < ?php echo '<pre>';print_r($data);die; ?>  -->
<?php if ( !empty($data) && count($data) > 0 ) { ?>
    <div class="wrapper-notif">
        <?php foreach($data as $index => $val){ ?>
            <?php if ( !empty($val['display']) ) { ?>
                <?php foreach($val['display'] as $row){ ?>
                    <div class="card-notif" onclick="window.location.href='<?php echo $val['link'] . '?kode=' . urlencode($row['key']) ?>'">
                        <div class="card-title">
                            <?php echo $val['nama_fitur']; ?>
                        </div>
                        <div class="card-content">
                            <?php echo $row['display']; ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </div>
<?php } else { ?>
    <span>Tidak ada notifikasi.</span>
<?php } ?>
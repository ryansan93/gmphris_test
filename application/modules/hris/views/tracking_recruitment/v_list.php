<style>
.tracking-card {
    border: 1px solid #102245;
    border-radius: 10px;
    padding: 10px;
    margin-bottom: 10px;
    border-left: 5px solid #2790c5;
}

.tracking-header {
    display: inline-block;
    background: #102245;
    color: white;
    padding: 5px 10px;
    border-radius: 10px;
    margin-bottom: 10px;
    font-size: 14px;
}

.tracking-body {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: flex-start;
}

.tracking-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 0 1 auto;
}

.tracking-item {
    display: flex;
    flex-direction:column;
    min-width: 250px;
    line-height: 1.4;
}

.tracking-action {
    flex : 0 0 auto;
    align-self: center;
}

@media (max-width: 991px) {
    .tracking-card {
        padding: 8px;
    }
    
    .tracking-header {
        font-size: 13px;
        padding: 4px 8px;
    }
}

@media (max-width: 767px) {
    .tracking-card {
        padding: 6px;
    }
    
    .tracking-header {
        font-size: 12px;
        width: 100%;
    }
}

.tracking-icon {
    border-radius: 10px; 
    padding: 5px; 
    background-color: #9CE1FF; 
    width: 40px; 
    height: 40px; 
    min-width: 40px;
    min-height: 40px;
    display: flex; 
    justify-content: center; 
    align-items: center; 
    color: #1D7BAD;
    font-size: 18px;
    flex-shrink: 0;
}

/* Desktop - Large screens */
@media (min-width: 992px) {
    .tracking-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .tracking-item strong {
        font-size: 13px;
    }
    
    .tracking-item span {
        font-size: 16px;
    }
}

@media (min-width: 768px) and (max-width: 991px) {
    .tracking-icon {
        width: 36px;
        height: 36px;
        padding: 4px;
        font-size: 16px;
    }
    
    .tracking-item strong {
        font-size: 12px;
    }
    
    .tracking-item span {
        font-size: 14px;
    }
    
    .tracking-body {
        gap: 12px;
    }
}

@media (max-width: 767px) {
    .tracking-icon {
        width: 32px;
        height: 32px;
        padding: 4px;
        font-size: 14px;
    }
    
    .tracking-item strong {
        font-size: 11px;
    }
    
    .tracking-item span {
        font-size: 13px;
    }
    
    .tracking-body {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .tracking-group {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tracking-item {
        width: auto;
    }

    .tracking-action {
        width: 100%;
        align-self: flex-start;
    }

    .tracking-action button {
        width: 100%;
        font-size: 13px;
    }
    
    .tracking-body > div[style*="border-left"] {
        display: none;
    }
}

@media (max-width: 480px) {
    .tracking-icon {
        width: 35px;
        height: 28px;
        padding: 3px;
        font-size: 12px;
        min-width: 28px;
    }
    
    .tracking-item {
        min-width: 200px !important;
    }
    
    .tracking-item strong {
        font-size: 10px;
        display: block;
        margin-bottom: 2px;
    }
    
    .tracking-item span {
        font-size: 12px;
    }
    
    .tracking-body {
        gap: 8px;
    }
    
    .tracking-group {
        gap: 6px;
    }
    
    .tracking-action button {
        font-size: 12px;
        padding: 6px 10px;
    }
}

</style>

<?php foreach($list as $l){ ?>
    <div class="tracking-card">

        <div class="tracking-header">
            <?php echo $l['document'] ?>
        </div>

        <div class="tracking-body">
            <div class="tracking-group">
                <div class="tracking-icon">
                    <i class="fa fa-user" aria-hidden="true"></i>
                </div>
                <div class="tracking-item">
                    <strong>Nama Pengusul</strong>
                    <span><?php echo ucwords( strtolower($l['nama_pengusul']) ) ?></span>
                </div>
            </div>

            <div class="tracking-group">
                <div class="tracking-icon">
                    <i class="fa fa-briefcase" aria-hidden="true"></i>
                </div>
                <div class="tracking-item">
                    <strong>Posisi yang Dibutuhkan</strong>
                    <span><?php echo $l['nama_jabatan'] ?></span>
                </div>
            </div>

            <div class="tracking-group">
                <div class="tracking-icon">
                    <i class="fa fa-users" aria-hidden="true"></i>
                </div>
                <div class="tracking-item">
                    <strong>Jumlah</strong>
                    <span><?php echo $l['jumlah'] . ' Orang' ?></span>
                </div>
            </div>

            <div class="tracking-action">
                <button document="<?php echo $l['document'] ?>" class="btn btn-primary" onclick="tr.show_tracking(this,event)">
                    <i class="fa fa-file" style="margin-right:5px;"></i> Lihat Tracking                     
                </button>
            </div>

        </div>

    </div>
<?php } ?>
<style>
.tracking-card {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 10px;
    margin-bottom: 10px;
}

.tracking-header {
    display: inline-block;
    background: #151b26;
    color: white;
    padding: 5px 10px;
    border-radius: 10px;
    margin-bottom: 10px;
}

.tracking-body {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
}

.tracking-item {
    /* flex: 1 1 250px; */
    min-width: 200px;
}

.tracking-action {
    flex: 0 0 auto;
}

@media (max-width: 768px) {
    .tracking-body {
        flex-direction: column;
        align-items: flex-start;
    }

    .tracking-item,
    .tracking-action {
        width: 100%;
    }

    .tracking-action button {
        width: 100%;
    }
}
</style>

<?php foreach($list as $l){ ?>
    <div class="tracking-card">

        <div class="tracking-header">
            <?php echo $l['document'] ?>
        </div>

        <div class="tracking-body">

            <div class="tracking-item">
                <strong>Nama Pengusul :</strong><br>
                <?php echo $l['nama_pengusul'] ?>
            </div>

            <div class="tracking-item">
                <strong>Posisi yang Dibutuhkan :</strong><br>
                <?php echo $l['nama_jabatan'] ?>
            </div>

            <div class="tracking-item">
                <strong>Jumlah :</strong><br>
                <?php echo $l['jumlah'] ?>
            </div>

            <div class="tracking-action">
                <button
                    document="<?php echo $l['document'] ?>"
                    class="btn btn-primary"
                    onclick="tr.show_tracking(this,event)">
                    <i class="fa fa-file" style="margin-right:5px;"></i>
                    Lihat Tracking
                </button>
            </div>

        </div>

    </div>
<?php } ?>
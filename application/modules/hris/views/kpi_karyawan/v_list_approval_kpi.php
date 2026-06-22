<style>
   .kpi-card {
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .avatar-kpi {
        width: 45px;
        height: 45px;
        line-height: 45px;
        background: #e9ecef;
        border-radius: 50%;
        font-weight: bold;
        font-size: 18px;
        margin: auto;
    }

    .nama-karyawan {
        font-size: 16px;
        font-weight: bold;
    }

    .nilai-mini {
        font-size: 22px;
        font-weight: bold;
        color: #1f75fe;
    }
</style>

<?php if(!empty($kpi_outstanding)){?>
    <?php foreach($kpi_outstanding as $o){?>
        <div class="panel panel-default kpi-card" style="<?php echo !empty($o['selected']) ? 'background-color: #FFF9D6;' : '' ?>">
            <div class="panel-body" style="padding:12px 15px;">
                <div class="row">

                    <div class="col-md-1 text-center">

                        <?php
                            $nama = $o['nama_karyawan'];
                            $kata = explode(' ', trim($nama));
                            $inisial = strtoupper(
                                substr($kata[0], 0, 1) .
                                (isset($kata[1]) ? substr($kata[1], 0, 1) : '')
                            );                        
                        ?>
                        <div class="avatar-kpi">
                            <?php echo $inisial?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="nama-karyawan">
                            <?php echo ucwords(strtolower($o['nama_karyawan'])) ?>
                        </div>
                        <small class="text-muted">
                            <?php echo $o['nik']?> • <?php echo $o['nama_jabatan']?>
                        </small>
                        
                        <span class="label <?php echo $o['status'] =='DRAFT' ? 'label-warning' : 'label-primary'?> "><?php echo $o['status']?></span>
                    </div>

                    <div class="col-md-2">
                        <small class="text-muted">Periode</small>
                        <div><b><?php echo $o['periode']?></b></div>
                    </div>

                    <div class="col-md-2">
                        <small class="text-muted">Total Nilai</small>
                        <div class="nilai-mini"><?php echo $o['total_nilai']?></div>
                    </div>

                    <div class="col-md-2">
                        <small class="text-muted">Progress</small>
                        <div class="progress" style="height:6px;margin-bottom:5px;">
                            <div class="progress-bar" style="width:<?php echo $o['total_nilai']?>%;"></div>
                        </div>
                        <small><?php echo $o['total_nilai']?> / 100</small>
                    </div>

                    <div class="col-md-2 text-right">
                        <button id_data="<?php echo $o['id'] ?>" class="btn btn-primary btn-sm" onclick="kpi.show_penilaian(this, event)"> <i class="fa fa-file"></i> Lihat Penilaian</button>
                    </div>

                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>

    <div>
        Tidak ada data
    </div>
<?php } ?>


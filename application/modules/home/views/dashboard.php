<style>
.dashboard-wrapper{
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 15px;
}

.dashboard-card{
    border: none;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,.08);
    transition: all .3s ease;
    width: 100%;
}

.dashboard-card:hover{
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0,0,0,.12);
	cursor: pointer;
}

.dashboard-card .card-body{
    padding: 15px;
}

.dashboard-card .title{
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 5px;
	margin-left: -0px;
    font-weight: 500;
}

.dashboard-card .value{
    font-size: 24px;
    font-weight: 700;
    color: #212529;
    line-height: 1;
}

.dashboard-card .icon-box{
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dashboard-card .icon-box i{
    font-size: 20px;
}

/* Variasi warna */
.bg-primary-soft{
    background: rgba(13,110,253,.12);
}

.bg-success-soft{
    background: rgba(25,135,84,.12);
}

.bg-warning-soft{
    background: rgba(255,193,7,.15);
}

.bg-info-soft{
    background: rgba(13,202,240,.15);
}

.bg-danger-soft{
    background: rgba(220,53,69,.12);
}

/* Tablet */
@media(max-width:992px){
    .dashboard-wrapper{
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Mobile */
@media(max-width:768px){
    .dashboard-wrapper{
        grid-template-columns: 1fr;
    }

    .dashboard-card .value{
        font-size:20px;
    }

    .dashboard-card .icon-box{
        width:42px;
        height:42px;
    }

    .dashboard-card .icon-box i{
        font-size:18px;
    }
}
</style>

<div class="row content-panel detailed">
	<div class="col-xs-12" style="padding: 0px 10px; height: 100%;">
		<h1 class="text-center">GRIYA MITRA POULTRY - HRIS</h1>
		<div class="col-xs-12 no-padding text-left"><hr class="hr-notifikasi" style="margin-top: 5px; margin-bottom: 15px;"></div>

		<br>
	
		<?php
			$selisih_hari = 0;

			if (!empty($day_off) && isset($day_off['date'])) {
				$selisih_hari = floor(
					(strtotime($day_off['date']) - strtotime(date('Y-m-d'))) / 86400
				);
			}
		?>

		<?php if (!empty($day_off) && $selisih_hari > 0) { ?>
			<div style="display: inline-block; border-left:5px solid #28a745; border-radius:0 5px 5px 0; background:#D1FFB8; padding:8px 12px; color:#155724;">
				<i class="fa fa-bell"></i>
				<?= $day_off['name']; ?>
				<small>
					(<?= tglIndonesia($day_off['date'], "-", " "); ?>)
					• <?= $selisih_hari; ?> hari lagi
				</small>
			</div>
		<?php } ?>

		<br>
		<br>
		
		<div class="dashboard-wrapper">
    
			<div class="card dashboard-card" onclick="window.location.href='parameter/Karyawan'">
				<div class="card-body d-flex justify-content-between align-items-center">
					<div>
						<div class="title">Total Karyawan</div>
						<div class="value"><?php echo count($karyawan_tetap) + count($karyawan_kontrak) ?></div>
					</div>

					<div class="icon-box bg-primary-soft">
						<i class="fa fa-users text-primary"></i>
					</div>
				</div>
			</div>

			<div class="card dashboard-card" onclick="window.location.href='parameter/Karyawan?getdata=1'">
				<div class="card-body d-flex justify-content-between align-items-center">
					<div>
						<div class="title">Karyawan Tetap</div>
						<div class="value"><?php echo count($karyawan_tetap) ?></div>
					</div>

					<div class="icon-box" style="background-color:#BFFFB0;" style="position:relative;">
						<i style="color:#3D8F2C;"  class="fa fa-user"></i>
						<i style="color:#3D8F2C; position:absolute; font-size:10px; margin-top:-17px;  margin-left:17px;" class="fa fa-check" aria-hidden="true"></i>
					</div>
				</div>
			</div>

			<div class="card dashboard-card" onclick="window.location.href='parameter/Karyawan?getdata=2'">
				<div class="card-body d-flex justify-content-between align-items-center">
					<div>
						<div class="title">Karyawan Training</div>
						<div class="value"><?php echo count($karyawan_kontrak) ?></div>
					</div>

				
					<div class="icon-box" style="background-color:#FFA08F;" style="position:relative;">
						<i style="color:#8F2E1F;" class="fa fa-user"></i>
						<i style="color:#8F2E1F; position:absolute; font-size:10px; margin-top:-17px; margin-left:17px;" class="fa fa-close" aria-hidden="true"></i>
					</div>
					
				</div>
			</div>

		</div>
	
		<br>
		<br>

		<fieldset style="margin-bottom: 15px;">
			<legend>
				<div class="col-xs-12 no-padding">
					<b>NOTIFIKASI</b>
				</div>
			</legend>
			<div class="col-xs-12 no-padding notifContain">
				Tidak ada notifikasi.
			</div>
		</fieldset>
</div>
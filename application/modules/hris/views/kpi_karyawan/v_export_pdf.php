 <!DOCTYPE html>
<html lang="en">

<head>
  <base href="<?php echo base_url() ?>" />
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">


  <script src="assets/toastr/js/toastr.min.js"></script>
  <title>HRIS - Print Usulan Karyawan</title>

  	<?php // CSS files ?>
  	<style type="text/css">

		@page {
			size: A4 portrait;
			margin: 10mm;
		}

		@media screen {

			html,
			body {
				margin: 0;
				padding: 0;
				background: #666;
				font-family: Arial, Helvetica, sans-serif;
			}

			.contain {
				width: 210mm;
				min-height: 297mm;
				margin: 20px auto;
				padding: 10mm;
				background: #fff;
				box-sizing: border-box;
			}

			.noPrint {
				position: fixed;
				top: 20px;
				right: 20px;
				background: #fff;
				padding: 10px;
				border-radius: 5px;
				box-shadow: 0 0 5px rgba(0,0,0,.2);
			}

		}


		@media print {

			html,
			body {
				margin: 0;
				padding: 0;
				background: #fff;
				width: auto;
				height: auto;
			}

			.noPrint {
				display: none !important;
			}

			.contain {
				width: 100%;
				min-height: auto;
				margin: 0;
				padding: 0;
				background: #fff;
				box-sizing: border-box;
			}

			table {
				page-break-inside: auto;
			}

			tr {
				page-break-inside: avoid;
			}

		}

		body {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 10pt;
		}

		.text-left {
			text-align: left;
		}

		.text-center {
			text-align: center;
		}

		.text-right {
			text-align: right;
		}

		.list_bobot {
			width: 100%;
			border-collapse: collapse;
			margin-top: 10px;
		}

		.list_bobot th,
		.list_bobot td {
			border: 1px solid #bebebe;
			padding: 6px;
			vertical-align: middle;
		}

		.list_bobot thead th {
			background: #d9d9d9;
			font-weight: bold;
			text-align: center;
		}

		.list_bobot tbody tr:first-child td {
			background: #efefef;
			font-weight: bold;
		}

		.col-xs-1{width:8.333333%;}
		.col-xs-2{width:16.666667%;}
		.col-xs-3{width:25%;}
		.col-xs-4{width:33.333333%;}
		.col-xs-5{width:41.666667%;}
		.col-xs-6{width:50%;}
		.col-xs-7{width:58.333333%;}
		.col-xs-8{width:66.666667%;}
		.col-xs-9{width:75%;}
		.col-xs-10{width:83.333333%;}
		.col-xs-11{width:91.666667%;}
		.col-xs-12{width:100%;}
	</style>
</head>

<body>
	<div class="noPrint">
		<button type="button" onclick="window.print()">PRINT</button>
	</div>
	
		<div class="contain">
			<div class="col-xs-12" style="display: inline; margin: 0px; padding: 0px;">
				<div class="col-xs-12" style="display: inline-block; text-align: left;">

				
					<table class="col-xs-12">
						<tbody>
							<tr>
								<td colspan="2">
									<label class="col-xs-12" style="font-size: 18pt; display: inline-block; margin-bottom: 10px; text-decoration: underline"><b>LAPORAN KPI KARYAWAN</b></label>
								</td>
							</tr>
							<tr>
								<td class="col-xs-6" style="vertical-align: top;">
									<div class="col-xs-12" style="display: inline; text-align: left; font-size: 12pt;">
										<label style="display: inline-block; width: 100%;"><b>PT. GRIYA MITRA POULTRY</b></label>
									</div>
									<div class="col-xs-12" style="display: inline; text-align: left; font-size: 10pt;">
										<label style="display: inline-block; width: 100%;"><?php echo strtoupper('JL. GAJAHMADA GANG XVIII NO.14 KALIWATES' . '<br>' . 'KAB JEMBER' .',  JAWA TIMUR'); ?></label>
									</div>
								</td>
								<?php
									$bulan = [
										1  => 'Januari',
										2  => 'Februari',
										3  => 'Maret',
										4  => 'April',
										5  => 'Mei',
										6  => 'Juni',
										7  => 'Juli',
										8  => 'Agustus',
										9  => 'September',
										10 => 'Oktober',
										11 => 'November',
										12 => 'Desember',
									];

									$bulan_get = $_GET['bulan'] ?? '';

									$periode = isset($bulan[$bulan_get]) ? $bulan[$bulan_get] : 'Semua Periode';
								?>
								<td class="col-xs-6" style="vertical-align: top; font-size: 10pt;">
									<div class="col-xs-12" style="display: inline; text-align: left;">
										<label style="display: inline-block; width: 18%;">Periode</label>
										<label style="display: inline-block; width: 2%;">:</label>
										<label style="display: inline-block; width: 75.5%;"><?php echo $periode; ?></label>
									</div>
									<!-- <div class="col-xs-12" style="display: inline; text-align: left;">
										<label style="display: inline-block; width: 18%;">Tanggal</label>
										<label style="display: inline-block; width: 2%;">:</label>
										<label style="display: inline-block; width: 75.5%;"></label>
									</div> -->
									
								</td>
							</tr>
						</tbody>
					</table>
					<hr>
					

                    <table class="list_bobot">
                        <thead>
                            <tr>
                                <th class="text-center">NIK</th>
                                <th class="text-center">Nama Karyawan</th>
                                <th class="text-center">Jabatan</th>
								<th class="text-center">Masa Kerja</th>
								<th class="text-center">Unit</th>
                                <th class="text-center">Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="tbl-laporan-kpi">
                            <?php if (!empty($laporan)) { ?>
                                <?php foreach ($laporan as $periode => $items) { ?>
 									<?php
											usort($items, function($a, $b) {
											return strcasecmp($a['nama_jabatan'], $b['nama_jabatan']);
										});
									?>

                                    <tr>
                                        <td colspan="6" style="font-weight:bold;background:#f5f5f5;">
                                            <?php echo $periode; ?>
                                        </td>
                                    </tr>

                                    <?php foreach ($items as $l) { ?>
                                        <tr>
                                            <td><?php echo $l['nik']; ?></td>
                                            <td><?php echo ucwords(strtolower($l['nama'])); ?></td>
                                            <td><?php echo $l['nama_jabatan']; ?></td>
											<td style="text-align:center;">
            
												<?php
													$tglMasuk   = new DateTime($l['tgl_masuk']);
													$hariIni    = new DateTime();
													$selisih    = $tglMasuk->diff($hariIni);

													// echo $selisih->y . ' Tahun ' . $selisih->m . ' Bulan ' . $selisih->d . ' Hari';
													echo $selisih->y . ' Tahun ' . $selisih->m . ' Bulan ';

												?>

											</td>
											<td><?php echo $l['nama_wilayah']; ?></td>
                                            <td class="text-right"><?php echo $l['total_nilai']; ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <i>Tidak ada data</i>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>



				</div>
			</div>
		</div>
</body>
</html>
 
 

<!DOCTYPE html>
<html lang="en">

<head>
  <base href="<?php echo base_url() ?>" />
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

  <title>HRIS - Print Usulan Karyawan</title>

  	<?php // CSS files ?>
  	<style type="text/css">
		@media print {
			html, body {
				height: 99%;
				width: 99.7%;
				max-width: 100%;
			}

			.noPrint {
				display: none;
				padding-bottom: 0px;
			}

			div.contain {
				padding: 0px;
				width: 210mm;
				height: 148mm;
				margin-bottom: 1rem;
			}

			table.maintable tbody { page-break-inside:auto }
			table.maintable tbody tr.data { page-break-inside:avoid; page-break-after:auto }
		}

		@media screen {
			html, body {
				height: 99%;
				width: 99.5%;
				max-width: 100%;
			}

			.noPrint {
				border-radius: 3px;
				padding: 10px;
				position: fixed;
				right: 1rem;
				top: 1rem;
				background-color: #ffffff;
			}

			div.contain {
				padding: 10px;
				width: 210mm;
				height: 148mm;
				margin-left: auto;
				margin-right: auto;
				margin-bottom: 1rem;
				font-family: arial;
			}
		}

		body {
			background-color: #666666;
		}

		div.contain {
			font-size: 9pt;
			background-color: #ffffff;
			/* padding: 10px; */
		}

		div.page-break {
			page-break-after: always;
		}

		div.page-break-avoid {
			page-break-after: auto;
		}

		p {
			margin: 0px;
		}

		ol { 
			counter-reset: item;
			margin: 0px;
			vertical-align: top;
		}
		li { 
			display: block; 
			margin: 0px;
			padding: 0px;
			vertical-align: top;
		}
		li:before { 
			content: counters(item, ".") ". ";
			counter-increment: item;
			vertical-align: top;
		}

		table.border-field td, table.border-field th {
			border-collapse: collapse;
			border: 1px solid;
			padding-left: 3px;
			padding-right: 3px;
			padding-top: 3px;
			padding-bottom: 3px;
		}
		
		table.border-field th {
			border: 1px solid;
		}

		table.border-field tr:not(.keterangan) td {        
			border: 1px solid;
		}

		table.border-field tr.keterangan td {        
			border: 1px solid;
		}

		table.border-field tr.total td {        
			border: 1px solid;
			font-size: 11pt;
			font-weight: bold;
		}

		table.border-field {
			border-collapse: collapse;
			border: 1px solid;
		}

		tr.foot td {
			padding-top: 0px;
			padding-bottom: 2px;
		}
		
		td.no-border-non-top {
			border-bottom : hidden!important;
		}

		td.no-border-top {
			border-top : hidden!important;
		}

		tr.foot td table tbody td.no-border {
			border: hidden;
		}

		/* td.no-border {
			border-top : hidden!important;
			border-bottom : hidden!important;
		} */

		.text-center {
			text-align: center;
		}

		.text-right {
			text-align: right;
		}

		.text-left {
			text-align: left;
		}

		.top td {
			border-top: 1px solid black;
		}

		.bottom td {
			border-bottom: 1px solid black;
		}

		td.kiri {
			border-left: 1px solid black;
			padding-left: 3px;
			padding-right: 3px;
		}

		td.kanan {
			border-right: 1px solid black;
			padding-left: 3px;
			padding-right: 3px;
		}

		.col-xs-1 {
			width: 8.33333333%;
		}
		.col-xs-2 {
			width: 16.66666667%;
		}
		.col-xs-3 {
			width: 25%;
		}
		.col-xs-4 {
			width: 33.33333333%;
		}
		.col-xs-5 {
			width: 41.66666667%;
		}
		.col-xs-6 {
			width: 50%;
		}
		.col-xs-7 {
			width: 58.33333333%;
		}
		.col-xs-8 {
			width: 66.66666667%;
		}
		.col-xs-9 {
			width: 75%;
		}
		.col-xs-10 {
			width: 83.33333333%;
		}
		.col-xs-11 {
			width: 91.66666667%;
		}
		.col-xs-12 {
			width: 100%;
		}

		@media print {
			@page {
				size: landscape;
			}

			body {
				display: flex;
				align-items: center;
				justify-content: center;
			}
		}
	</style>
</head>

<body>
	<div class="noPrint">
		<button type="button" onclick="window.print()">PRINT</button>
	</div>
	
		<div class="contain">
			<div class="col-xs-12" style="display: inline; margin: 0px; padding: 0px;">
				<div class="col-xs-12" style="display: inline-block; text-align: left;">
					<div class="col-xs-12 head" style="display: inline-block; text-align: left;">
						<table class="col-xs-12">
							<tbody>
								<tr>
									<td colspan="2">
										<label class="col-xs-12" style="font-size: 18pt; display: inline-block; margin-bottom: 10px; text-decoration: underline"><b>USULAN KARYAWAN BARU</b></label>
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
									<td class="col-xs-6" style="vertical-align: top; font-size: 10pt;">
										<div class="col-xs-12" style="display: inline; text-align: left;">
											<label style="display: inline-block; width: 18%;">Document</label>
											<label style="display: inline-block; width: 2%;">:</label>
											<label style="display: inline-block; width: 75.5%;"><?php echo strtoupper($data['document']); ?></label>
										</div>
										<div class="col-xs-12" style="display: inline; text-align: left;">
											<label style="display: inline-block; width: 18%;">Tanggal</label>
											<label style="display: inline-block; width: 2%;">:</label>
											<label style="display: inline-block; width: 75.5%;"><?php echo strtoupper(tglIndonesia(substr($data['tgl_pengusulan'], 0, 10), '-', ' ')); ?></label>
										</div>
										<!-- <div class="col-xs-12" style="display: inline; text-align: left;">
											<label style="display: inline-block; width: 18%;">Dari</label>
											<label style="display: inline-block; width: 2%;">:</label>
											<label style="display: inline-block; width: 75.5%;"><?php echo $data['pelanggan']; ?></label>
										</div> -->
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<br>
					<br>
					<div class="col-xs-12" style="display: inline-block; text-align: left; font-size: 10pt;">
						<table class="border-field" style="width: 100%;">
							<thead>
								<tr>
									<td colspan="3" style="height:30px; vertical-align: middle; text-align:center;">
										<span style="font-weight:bold; font-size:15px;">
											Data Pengusul
										</span>
									</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="text-left" style="width: 20%;"><span style="margin-left:15px;">Nama Pengusul </span></td>
									<td class="text-left" style="width: 2%;">:</td>
									<td class="text-left" style="width: 78%;"><?php echo ucwords(strtolower($data['nama_karyawan_pengusul']))?></td>
								</tr>
								<tr>
									<td class="text-left" style="width: 20%;"><span style="margin-left:15px;" >Unit </span></td>
									<td class="text-left" style="width: 2%;">:</td>
									<td class="text-left" style="width: 78%;"><?php echo $unit[$data['unit']]['nama']?></td>
								</tr>
								<tr>
									<td class="text-left" style="width: 1%;"><span style="margin-left:15px;" >Posisi </span></td>
									<td class="text-left" style="width: 10px;">:</td>
									<td class="text-left" style="width: 5%;"><?php echo $data['nama_jabatan']?></td>
								</tr>
								<tr>
									<td class="text-left" style="width: 1%;"><span style="margin-left:15px;" >Jumlah </span></td>
									<td class="text-left" style="width: 10px;">:</td>
									<td class="text-left" style="width: 5%;"><?php echo $data['jumlah']?> Orang</td>
								</tr>
								<tr>
									<td class="text-left" style="width: 1%;"><span style="margin-left:15px;" >Alasan </span></td>
									<td class="text-left" style="width: 10px;">:</td>
									<td class="text-left" style="width: 5%;"><?php echo $data['alasan']?></td>
								</tr>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						<table class="border-field" style="width: 100%;">
							<tr style="border:1px solid black; height:100px;">
								<td style="width: 70%;">
									<div style="margin-left:15px;">
										<b style="text-decoration: underline">Informasi Usulan</b>
									</div>
									<div style="margin-left:20px; display:flex; flex-direction:row">
										<div style="width:170px;">Acknowledge by </div>
										<div style="width:10px;">:</div> 
										<b> <?php echo !empty($data['tgl_ack'])  ? $data['acknowledged_rejected_by'] : ' - ' ?></b>
									</div>
									<div style="margin-left:20px; display:flex; flex-direction:row">
										<div style="width:170px;">Tanggal Acknowledge HRD </div>
										<div style="width:10px;">:</div> 
										<b> <?php echo !empty($data['tgl_ack'])  ? tglIndonesia($data['tgl_ack'], '-', ' ') . ', Pukul : ' . date("H:i:s", strtotime($data['tgl_ack'])) : '-'; ?></b>
									</div>
									<div style="margin-left:20px; display:flex; flex-direction:row">
										<div style="width:170px;">Approved by </div>
										<div style="width:10px;">:</div> 
										<b> <?php echo !empty($data['tgl_approve'])  ? $data['approved_rejected_by'] : ' - ' ?></b>
									</div>
									<div style="margin-left:20px; display:flex; flex-direction:row">
										<div style="width:170px;">Tanggal Approve </div>
										<div style="width:10px;">:</div> 
										<b> <?php echo !empty($data['tgl_approve'])  ? tglIndonesia($data['tgl_approve'], '-', ' ') . ', Pukul : ' . date("H:i:s", strtotime($data['tgl_approve'])) : '-'; ?></b>
									</div>
								</td>
								<td style="width: 30%; color:green;" class="text-center">
									<b><?php echo $data['status'] == 3 ? '[ APPROVED ]' : '' ?></b>
								</td>
							</tr>
							<tr style="border:1px solid black;">
								<td style="width: 70%;" class="text-center"></td>
								<td style="width: 30%;" class="text-center">
									<div>(.......................................)</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
</body>
</html>
<style>
.line-tracking {
    top: 55px;
    position: absolute;
    z-index: 1;
    width: 80%;
    border-top: 2px dashed #cbc8c8;
    transform: translateY(-50%);
    display: block;
}

/* Desktop view */
.tracking-container {
    display: flex;
    flex-direction: row;
    justify-content: center;
    position: relative;
    gap: 5%;
    min-width: 900px;
    overflow-x: auto;
    padding: 20px 10px;
}

.line-mobile {
    border-left: 0px dashed #cbc8c8;
}

.icon-tracking {
    z-index: 2;
    border: 5px solid #0c1575;
    border-radius: 50%;
    width: 70px;
    height: 70px;
    background-color: #979fff;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 30px;
    color: #0c1575;
}

.label-card {
    font-size: 16px;
    font-weight: bold;
}

/* Mobile view */
@media (max-width: 700px) {

    .icon-tracking {
        z-index: 0;
    }

    .tracking-container {
        flex-direction: column;
        gap: 30px;
        min-width: auto;
        overflow-x: visible;
        padding: 20px 10px;
    }

    .line-tracking {
        display: none;
    }

    .tracking-item {
        width: 100% !important;
        max-width: 250px;
        margin: 0 auto;
    }

    .line-mobile {
        border-left: 2px dashed #cbc8c8;
    }
}
</style>

<div style="overflow-x:auto; padding:20px 0;">

    <div class="tracking-container">
        <div class="line-tracking">
        </div>
        <div class="line-mobile" style="display:flex; flex-direction:column;  align-items:center; gap:10px;">
            <span class="icon-tracking">
                <i class="fa fa-user-plus" aria-hidden="true"></i>
            </span>
            <label for="">Usulan Karyawan Baru</label>

            <br><br>
            <div>
                <span class="label-card">• Data Usulan</span>
                <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; gap:10px;">
                    <div
                        style="border-radius:5px; min-height:180px; width:250px; border: 2px solid #ddd; padding:10px; ">

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Document</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['document'] ?>
                                </span>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Nama Pengusul</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span
                                    style="font-size:13px; font-weight:bold;"><?php echo ucwords(strtolower($data_usulan['nama_pengusul'])) ?>
                                </span>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Jumlah Kebutuhan</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span
                                    style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['jumlah_kandidat'] ?> Orang
                                </span>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Posisi</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span
                                    style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['nama_jabatan'] ?>
                                </span>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Tanggal Pengusulan</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span
                                    style="font-size:13px; font-weight:bold;"><?php echo tglIndonesia($data_usulan['tgl_pengusulan'], '-', ' ') ?>
                                </span>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Alasan</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['alasan'] ?>
                                </span>
                            </div>
                        </div>
                        
                        <br>

                        <?php 
                            $key = "secretkey";
                            $plaintext = $data_usulan['id_usulan'];
                        ?>

                        <button class="btn btn-primary" onclick="window.open('hris/FormAckUsulanKaryawan/printPreview?id=<?php echo $encrypted = openssl_encrypt($plaintext, "AES-128-ECB", $key); ?>', '_blank')"><i style="margin-right:5px;" class="fa fa-file" aria-hidden="true"></i> Lihat Detail Usulan</button>


                    </div>
                </div>
            </div>

            <div>
                <span class="label-card">• Data Keputusan</span>
                <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; gap:10px;">
                    <div
                        style="border-radius:5px; min-height:60px; width:250px; border: 2px solid #ddd; padding:10px; ">

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Document</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['document'] ?>
                                </span>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Status Usulan</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span style="font-size:13px; font-weight:bold;"><?php 
                                    $status = $data_usulan['status'];
                                    $status_text = [
                                        1 => '<i style="color:#E4E817;" class="fa fa-clock-o"></i> Draft',
                                        2 => '<i style="color:#63E329;" class="fa fa-check"></i> Acknowledge',
                                        3 => '<i style="color:#63E329;" class="fa fa-check"></i> Approve',
                                        4 => '<i style="color:#FF5736;" class="fa fa-close"></i> Reject HRD',
                                        5 => '<i style="color:#FF5736;" class="fa fa-close"></i> Reject CEO'
                                    ];
                                    echo isset($status_text[$status]) ? $status_text[$status] : 'Unknown';
                                ?>
                                </span>
                            </div>
                        </div>


                        <?php if($data_usulan['status'] ==2 || $data_usulan['status'] == 3) { ?>
                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Tanggal Acknowledge</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span
                                    style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['tgl_acknowledge'] ? tglIndonesia($data_usulan['tgl_acknowledge'], '-', ' ') : '-' ?>
                                </span>
                            </div>
                        </div>
                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Acknowledge Oleh</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span
                                    style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['acknowledged_rejected_by'] ? ucwords(strtolower($data_usulan['acknowledged_rejected_by'])) : '-' ?>
                                </span>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Tanggal Approve</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span
                                    style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['tgl_approve'] ? tglIndonesia($data_usulan['tgl_approve'], '-', ' ') : '-' ?>
                                </span>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Approve Oleh</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span
                                    style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['approved_rejected_by'] ? ucwords(strtolower($data_usulan['approved_rejected_by'])) : '-' ?>
                                </span>
                            </div>
                        </div>
                        <?php } ?>

                        <?php if($data_usulan['status'] == 4 || $data_usulan['status'] == 5) { ?>
                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Tanggal Reject</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span
                                    style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['tgl_reject'] ? tglIndonesia($data_usulan['tgl_reject'], '-', ' ') : '-' ?>
                                </span>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Keterangan Reject</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span
                                    style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['keterangan_hrd'] ? $data_usulan['keterangan_hrd'] : $data_usulan['keterangan_ceo'] ?>
                                </span>
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="line-mobile" style="display:flex; flex-direction:column;  align-items:center; gap:10px;">
            <span class="icon-tracking">
                <i class="fa fa-handshake-o" aria-hidden="true"></i>
            </span>
            <label for="">Data Kandidat Masuk</label>

            <br><br>
            <div>
                <?php if (!empty($data_kandidat)) { ?>
                    <span class="label-card">• Data Kandidat</span>
                    <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; gap:10px;">
                        <div style="border-radius:5px; height:180px; width:250px; border: 2px solid #ddd;  padding:10px;">

                            <div style="display:flex; flex-direction:column;">
                                <span style="font-size:13px; width:;">Document</span>
                                <div
                                    style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                    <span style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['document'] ?>
                                    </span>
                                </div>
                            </div>

                            <div style="display:flex; flex-direction:column;">
                                <span style="font-size:13px; width:;">Jumlah Kandidat Masuk</span>
                                <div
                                    style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                    <span style="font-size:13px; font-weight:bold;"><?php echo count($data_kandidat) ?>
                                        Orang
                                    </span>
                                </div>
                            </div>
                            <br>

                            <button class="btn btn-primary" onclick="tr.show_kandidat_table(this, event, 1)"><i style="margin-right:5px;" class="fa fa-file" aria-hidden="true"></i> Data Kandidat</button>
                        </div>
                    </div>
                <?php } else { ?>
                    <span class="label-card">• Data Kandidat</span>
                    <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; gap:10px;">
                        <div style="border-radius:5px; height:180px; width:250px; border: 2px solid #ddd;  padding:10px;">

                            <div style="display:flex; flex-direction:column;">
                                <span style="font-size:13px; width:;">Document</span>
                                <div
                                    style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                    <span style="font-size:13px; font-weight:bold;">-
                                    </span>
                                </div>
                            </div>

                            <div style="display:flex; flex-direction:column;">
                                <span style="font-size:13px; width:;">Jumlah Kandidat Masuk</span>
                                <div
                                    style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                    <span style="font-size:13px; font-weight:bold;">-
                                        
                                    </span>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div>
                <?php 
                    $data_kandidat_diterima = array_filter($data_kandidat, function($kandidat) {
                        return $kandidat['nik'] != null;
                    });
                ?>

                <?php if (!empty($data_kandidat_diterima)) { ?>
                <span class="label-card">• Data Kandidat Diterima</span>
                <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; gap:10px;">
                    <div style="border-radius:5px; height:180px; width:250px; border: 2px solid #ddd;  padding:10px;">

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Document</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span style="font-size:13px; font-weight:bold;"><?php echo $data_usulan['document'] ?>
                                </span>
                            </div>
                        </div>

                       

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Jumlah Kandidat Diterima</span>
                            <div
                                style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span
                                    style="font-size:13px; font-weight:bold;"><?php echo count($data_kandidat_diterima) ?>
                                    Orang
                                </span>
                            </div>
                        </div>
                        <br>

                        <button class="btn btn-primary" onclick="tr.show_kandidat_table(this, event, 2)"><i style="margin-right:5px;" class="fa fa-file" aria-hidden="true"></i> Data Kandidat</button>
                    </div>
                </div>
                <?php } else { ?>

                <span class="label-card">• Data Kandidat Diterima</span>
                <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; gap:10px;">
                    <div style="border-radius:5px; height:180px; width:250px; border: 2px solid #ddd;  padding:10px;">

                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Document</span>
                            <div style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span style="font-size:13px; font-weight:bold;">-</span>
                            </div>
                        </div>
                    
                        <div style="display:flex; flex-direction:column;">
                            <span style="font-size:13px; width:;">Jumlah Kandidat Diterima</span>
                            <div style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                <span style="font-size:13px; font-weight:bold;">-</span>
                            </div>
                        </div>
                    
                    </div>
                </div>

                <?php } ?>

            </div>
        </div>

        <div class="line-mobile" style="display:flex; flex-direction:column;  align-items:center; gap:10px;">
            <span class="icon-tracking">
                <i class="fa fa-suitcase" aria-hidden="true"></i>
            </span>
            <label for="">Probation Karyawan</label>

            <br><br>
            <?php if (!empty($data_karyawan)) { ?>

                <?php $no = 1; ?>
                <?php foreach($data_karyawan as $k){ ?>
                <div>
                    <span class="label-card">• Data Probation Karyawan <?php echo count($data_karyawan) > 0 ? $no++ : '' ?> </span>
                    <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; gap:10px;">
                        <div style="border-radius:5px; height:auto; width:250px; border: 2px solid #ddd; padding:10px;">

                            <div style="display:flex; flex-direction:column;">
                                <span style="font-size:13px; width:;">NIK</span>
                                <div
                                    style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                    <span style="font-size:13px; font-weight:bold;"><?php echo $k['nik'] ?></span>
                                </div>
                            </div>

                            <div style="display:flex; flex-direction:column;">
                                <span style="font-size:13px; width:;">Nama Karyawan</span>
                                <div
                                    style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                    <span style="font-size:13px; font-weight:bold;"><?php echo $k['nama_karyawan'] ?></span>
                                </div>
                            </div>

                            <div style="display:flex; flex-direction:column;">
                                <span style="font-size:13px; width:;">Duration</span>
                                <div
                                    style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                    <span style="font-size:13px; font-weight:bold;"><?php echo $k['duration'] ?> Bulan</span>
                                </div>
                            </div>

                            <div style="display:flex; flex-direction:column;">
                                <span style="font-size:13px; width:;">Tanggal Berlaku</span>
                                <div
                                    style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                    <span style="font-size:13px; font-weight:bold;"><?php echo tglIndonesia($k['tgl_berlaku'],'-', ' ') ?></span>
                                </div>
                            </div>

                            <div style="display:flex; flex-direction:column;">
                                <span style="font-size:13px; width:;">Tanggal Selesai</span>
                                <div
                                    style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                    <span style="font-size:13px; font-weight:bold;"><?php echo tglIndonesia($k['tgl_selesai'],'-', ' ') ?></span>
                                </div>
                            </div>
                            <br>
                            <button class="btn btn-primary" onclick="tr.show_karyawan_detail(this, event, '<?php echo $k['nik'] ?>')">Lihat Data Karyawan</button>

                        </div>
                    </div>
                </div>
                <?php } ?>

            <?php } else { ?>
            
                <div>
                    <span class="label-card">• Data Probation Karyawan </span>
                    <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; gap:10px;">
                        <div style="border-radius:5px; height:180px; width:250px; border: 2px solid #ddd; padding:10px;">

                            <div style="display:flex; flex-direction:column;">
                                <span style="font-size:13px; width:;">NIK</span>
                                <div
                                    style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                    <span style="font-size:13px; font-weight:bold;">-</span>
                                </div>
                            </div>

                            <div style="display:flex; flex-direction:column;">
                                <span style="font-size:13px; width:;">Nama Karyawan</span>
                                <div
                                    style="padding:4px; border-radius:5px; width:100%; background-color: #f0f0f0; border: 1px solid #f0f0f0; display:flex; align-items:center;">
                                    <span style="font-size:13px; font-weight:bold;">-</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


            <?php } ?>
        </div>
    </div>


    <div class="col-xs-12 no-padding list_data_kandidat" style="display:none;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Nama Kandidat</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data_kandidat)) { ?>

                <?php foreach($data_kandidat as $k){ ?>
                <tr>
                    <td>
                        <a href="<?php echo base_url('hris/HrisKandidatBaru/show_document_kandidat?id='. $k['id']) ?>"
                            target="_blank">
                            <?php echo $k['document'] ? $k['document'] : '-' ?>
                        </a>
                    </td>
                    <td><?php echo $k['nama'] ?></td>
                    <td style="text-align:center;">
                        <?php
                            if ($k['status_kandidat'] == 1) {
                                echo '<span style="display:inline-block; width:100px; border: 1px solid #f9e867; color:#f9e867; background-color: #fcf2a3; border-radius:20px; padding:5px;">Draft</span>';
                            } elseif ($k['status_kandidat'] == 2) {
                                echo '<span style="display:inline-block; width:100px; border: 1px solid #187718; color:#187718; background-color: #d4f7d4; border-radius:20px; padding:5px;">Approve</span>';
                            } elseif ($k['status_kandidat'] == 3) {
                                echo '<span style="display:inline-block; width:100px; border: 1px solid #771818; color:#771818; background-color: #f7a7a7; border-radius:20px; padding:5px;">Reject</span>';
                            }
                        ?>
                    </td>
                </tr>
                <?php } ?>

                <?php } else { ?>

                <tr>
                    <td colspan="2" style="text-align:center;">Tidak ada data kandidat</td>
                </tr>

                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="col-xs-12 no-padding list_data_karyawan" style="display:none;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Nama Karyawan</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data_kandidat_diterima)) { ?>
                    <?php foreach($data_kandidat_diterima as $k){ ?>
                    <tr>
                        <td>
                            <a href="<?php echo base_url('hris/HrisKandidatBaru/show_document_kandidat?id='. $k['id']) ?>"
                                target="_blank">
                                <?php echo $k['document'] ? $k['document'] : '-' ?>
                            </a>
                        </td>
                        <td><?php echo $k['nama'] ?></td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="2" style="text-align:center;">Tidak ada data karyawan diterima</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>





<!-- <script>

setTimeout(() => {
    $('#menu-toggle').click();
}, 200);

</script> -->
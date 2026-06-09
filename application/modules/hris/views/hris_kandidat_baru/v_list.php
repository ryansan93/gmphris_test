<div class="pull-right" style="display:flex; flex-direction:row; gap:20px; border: 1px solid #bbb9b9; border-radius:5px; margin-bottom:10px; padding:5px;">

    <div style="display:flex; flex-direction:row;  align-items:center; gap:5px; ">
        <div style="border:1px solid black; background-color:#FFF9D6; border-radius:50%; width:15px; height:15px"></div>
        <span>Data Terpilih</span>
    </div>

    <div style="display:flex; flex-direction:row;  align-items:center; gap:5px; ">
        <div style="border:1px solid black; background-color:#FFD252; border-radius:50%; width:15px; height:15px"></div>
        <span>Belum isi form</span>
    </div>

    <div style="display:flex; flex-direction:row;  align-items:center; gap:5px; ">
        <div style="border:1px solid black; background-color:#C9FF9C; border-radius:50%; width:15px; height:15px"></div>
        <span>Sudah isi form</span>
    </div>
</div>

<div>

</div>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center" style="width:5px;">#</th>
            <th class="text-center">Document</th>
            <th class="text-center">Nama Kandidat</th>
            <th class="text-center">Status Kandidat</th>
            <th class="text-center">Pengusul</th>
            <th class="text-center">Link Form</th>
            <th class="text-center">Keputusan</th>
            <!-- <th class="text-center">Action</th> -->
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
            <?php foreach($list as $l){?>
                <tr style="<?php echo !empty($l['selected']) ? 'background-color: #FFF9D6;' : '' ?>">
                    <td style="background-color:<?php echo $l['is_active'] == 'NONACTIVE' ? '#C9FF9C' : '#FFD252' ?>"></td>
                    <td class="text-center" style="white-space:nowrap;">
                        <a href="<?php echo base_url('hris/HrisKandidatBaru/show_document_kandidat?id='. $l['id_data_karyawan']) ?>" target="_blank">
                           <?php echo $l['document'] ? $l['document'] : '-' ?>
                        </a>
                    </td>
                    <td class="text-left" style="white-space:nowrap;"><?php echo $l['nama'] ?></td>
                    <td class="text-center">
                        <?php echo $l['status_kandidat'] == 3 ? 'Ditolak' : $l['nama_status'] ?>
                    
                    </td>
                    <td class="text-center" style="white-space:nowrap;"><?php echo ucwords(strtolower($l['nama_pengusul'])) . ' - ' . ucwords(strtolower($l['jabatan_pengusul'])) ?></td>
                    <td class="text-center" style="position:relative; white-space:nowrap;">

                        <?php 
                            $key = "secretkey";
                            $plaintext = $l['kategori'].'-'.$l['id_data_karyawan'];

                            $encrypted = openssl_encrypt($plaintext, "AES-128-ECB", $key);
                            // $url = "http://localhost/recruitment-gmp-dev/HrisGenerateForm?kode=" . urlencode($encrypted);
                            $url = "http://localhost/recruitment-gmp/Form?kode=" . urlencode($encrypted);
                        ?>

                        <a <?php echo $l['is_active'] == 'NONACTIVE' ? '' : 'url="'.$url.'"; onclick="hf.copy_link(this, event)" ' ?>  style="<?php echo $l['is_active'] == 'NONACTIVE' ? 'pointer-events:none; color:gray; cursor:not-allowed;' : 'color:blue;' ?>" >
                            <i style="margin-right:5px;" class="fa fa-link"></i> Generate Link
                        </a>
                    </td>
                    <td class="text-center">
                        <?php if ($l['is_active'] == 'NONACTIVE'){ ?>

                            <?php if (!empty($l['keterangan_reject'])) { ?>

                                <span title="Lihat Keterangan" keterangan="<?php echo $l['keterangan_reject']; ?>"  onclick="hf.show_keterangan(this, event)" style="cursor:pointer; color:#771818; background-color:#f7a7a7; border-radius:10px; padding:5px; text-align:center; font-weight:bold;">
                                    Reject
                                </span>
                                

                            <?php } else if (!empty($l['tgl_masuk'])) { ?>

                                <span>Tanggal Masuk :  (<?php echo tglIndonesia($l['tgl_masuk'], '-', ' '); ?>)</span>

                            <?php } else { ?>
                                <button type="button" id_data="<?= $l['id_data_karyawan'] ?>" class="btn btn-sm btn-success" onclick="hf.keputusan_akhir(this, event, 1)">Approve</button>
                                <button type="button" id_data="<?= $l['id_data_karyawan'] ?>" class="btn btn-sm btn-danger" onclick="hf.keputusan_akhir(this, event, 2)"> Reject</button>
                            <?php } ?>

                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                    <!-- <td class="text-center">
                        <button class="btn btn-sm btn-secondary" id_data="< ?= $l['id_data_karyawan']; ?>" onclick="hf.show_detail(this, event)" >Show Detail</button>
                    </td> -->
                </tr>
            <?php } ?>
        <?php } else { ?>

        <tr>
            <td colspan="8" style="text-align:center;">Tidak ada data</td>
        </tr>
        <?php } ?>


    </tbody>
</table>



    <div style="display:flex; flex-direction:column; gap:10px;">

        <div style="display:flex; flex-direction:column; gap:10px;">
            <span style="width:100px;">Kode Usulan</span>
            <span style="border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%;"><?php echo $data_detail['kode']; ?></span>
        </div>

        <div style="display:flex; flex-direction:column; gap:10px;">
            <span style="width:100px;">Tanggal Usulan</span>
            <span style="border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%;"><?php echo tglIndonesia($data_detail['tanggal']); ?></span>
        </div>

        <div style="display:flex; flex-direction:column; gap:10px;">
            <span style="width:100px;">Pengusul</span>
            <span style="border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%;"><?php echo ucwords(strtolower($data_detail['nama_pengusul'])); ?></span>
        </div>

        <div style="display:flex; flex-direction:column; gap:10px;">
            <span style="width:100px;">Karyawan</span>
            <span style="border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%;"><?php echo ucwords(strtolower($data_detail['nama_karyawan'])); ?></span>
        </div>


        <div style="display:flex; flex-direction:column; gap:10px;">
            <span style="width:100px;">Jenis</span>
            <div style="padding:10px;border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%;">
                

                <table class="table table-bordered">
                    <tr>
                        <th colspan="3" class="text-center">
                            <?php echo ucwords(strtolower($data_detail['jenis'])); ?>
                        </th>
                    </tr>
                    <tr>
                        <td class="text-center" style="width:45%; font-weight:bold;">
                            Jabatan Asal
                        </td>
                        <td class="text-center" style="width:10%; vertical-align:middle;" rowspan="2"><i class="fa fa-angle-double-right" aria-hidden="true"></i></td>
                        <td class="text-center" style="width:45%; font-weight:bold;">
                            Jabatan Tujuan
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <?php echo $data_detail['nama_jabatan_asal']; ?>
                        </td>
                        
                        <td class="text-center">
                            <?php echo $data_detail['nama_jabatan_tujuan']; ?>
                        </td>
                    </tr>
                </table>

            </div>                                     
        </div>


       

        

        <div style="display:flex; flex-direction:column; gap:10px;">
            <span style="width:100px;"> Alasan</span>
            <span style="border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%; height:100px;"><?php echo $data_detail['alasan']; ?></span>
        </div>

         <?php if ($data_detail['status'] == 4 || $data_detail['status'] == 5) { ?>
            <div style="display:flex; flex-direction:column; gap:10px;">
                <span style="width:100px;">Alasan Reject</span>
                <span style="border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%; height:100px;"><?php echo $data_detail['alasan_reject']; ?></span>
            </div>
        <?php } ?>
        
    </div>
    <br>
    <div style="width:100%; display:flex; flex-direction:row; gap:10px">
        <?php 
            $status = $data_detail['status'] ?? null;
            $kode   = $data_detail['kode'] ?? '';
        ?>

        <?php if ($status == 1) { ?>
            <button kode="<?php echo $kode; ?>" onclick="up.keputusan(this, 2)" class="btn btn-primary w-100">
                Acknowledge
            </button>
            <button kode="<?php echo $kode; ?>" onclick="up.keputusan(this, 4)" class="btn btn-danger w-100">
                Reject
            </button>
        <?php } else if($status == 2) { ?>
            <button kode="<?php echo $kode; ?>" onclick="up.keputusan(this, 3)" class="btn btn-success w-100">
                Approve
            </button>
            <button kode="<?php echo $kode; ?>" onclick="up.keputusan(this, 5)" class="btn btn-danger w-100">
                Reject
            </button>
        <?php } ?>
    </div>


    



<style>
    legend{
        min-width:30%;
    }

    @media (max-width:768px){
        legend{
            min-width:100%;
            white-space:normal;
            word-break:break-word;
        }

        .table-fasilitas {
            overflow-x: scroll;
        }

        .table-fasilitas input{
            width:200px
        }

        .table-fasilitas select{
            width:100px
        }
        
    }
</style>

<fieldset>
    <legend>Filter</legend>

    <select class="select2 document" onchange="ukr.show_verifikasi_clearance(this, event)">
        <option value="">-- Pilih Data --</option>
        <?php foreach ( $filter as $f) { ?>
            <option <?php echo (($_GET['kode'] ?? null) == $f['id']) ? 'selected' : ''; ?> value="<?php echo $f['id'] ?>"><?php echo $f['document'] . ' - ' . $f['nama_karyawan'] ?></option>
        <?php } ?>
    </select>
</fieldset>
<br>
<fieldset>
    <legend>Verifikasi dan Serah Terima Clearance</legend>

    <div class="form-group">
        <label>Data Karyawan</label>

        <!-- <div class="card p-3"> -->
            <?php if ($data_karyawan) { ?>
                <table class="table table-bordered">
                    <tr>
                        <td width="20%">No Dokumen</td>
                        <td><?php echo $data_karyawan['document']; ?></td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td><?php echo $data_karyawan['nik']; ?></td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td><?php echo $data_karyawan['nama_karyawan']; ?></td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td><?php echo $data_karyawan['nama_jabatan']; ?></td>
                    </tr>
                </table>
            <?php } ?>
        <!-- </div> -->
    </div>

    <?php if ($data_karyawan) { ?>
        <label>Daftar Fasilitas</label>
        <div class="form-group table-fasilitas">

            <table class="table table-bordered table-clearance">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Fasilitas</th>
                        <th>Kondisi Saat Diterima</th>
                        <th width="10%">Jumlah</th>
                        <th width="15%">Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>

                <tbody>

                    <?php $list_clearance = isset($data_clearance[0]) ? $data_clearance  : [$data_clearance];

                    foreach($list_clearance as $key => $val) { 
                    ?>

                    <tr>
                        <td style="white-space:nowrap">
                            <?php echo $key + 1; ?>
                        </td>

                        <td style="white-space:nowrap">
                            <a href="javascript:void(0)" src="<?php echo base_url() . $val['path_file']; ?>" onclick="ukr.show_attachment(this, event)" ><?php echo $val['nama_fasilitas']; ?></a>
                            <input type="hidden" 
                                name="data[<?php echo $key; ?>][id]"
                                value="<?php echo $val['id']; ?>">
                        </td style="white-space:nowrap">

                        <td style="white-space:nowrap">
                            <?php echo $val['kondisi_fasilitas']; ?>
                        </td style="white-space:nowrap">

                        <td style="white-space:nowrap">
                            <?php echo $val['jumlah']; ?>
                        </td>

                        <td>
                            <select <?php echo $data_karyawan['verification_clearance_date'] != null ? 'disabled' :'' ?> class="form-control" name="data[<?php echo $key; ?>][status]">
                                <option value="">Pilih</option>
                                <option  <?php echo $val['status_clearance'] == 'KEMBALI' ? 'selected' : '' ?> value="KEMBALI">Dikembalikan</option>
                                <option  <?php echo $val['status_clearance'] == 'TIDAK_ADA' ? 'selected' : '' ?> value="TIDAK_ADA">Tidak Ada</option>
                                <option  <?php echo $val['status_clearance'] == 'RUSAK' ? 'selected' : '' ?> value="RUSAK">Rusak</option>
                            </select>
                        </td>

                        <td >
                            <input  <?php echo $data_karyawan['verification_clearance_date'] != null ? 'disabled' :'' ?> class="form-control" name="data[<?php echo $key; ?>][catatan]" value="<?php echo $val['catatan_clearance'] ?>" placeholder="Catatan"> 
                        </td>
                    </tr>

                    <?php } ?>

                </tbody>
            </table>

        </div>

        <?php if ($data_karyawan['verification_clearance_date'] == null) {?>
            <button nik="<?php echo $data_karyawan['nik']; ?>" class="btn btn-primary" id_data="<?php echo $data_karyawan['id']; ?>" onclick="ukr.saveVerifikasiClearance(this,event)">Simpan Verifikasi</button>
        <?php } ?>

    <?php } ?>


</fieldset>
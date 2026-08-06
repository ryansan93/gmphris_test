<?php 
    $bulan_text = [
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
        12 => 'Desember'
    ];
?>

<div id="edit_penilaian" periode_lama="<?php echo date('n', strtotime($header[0]['periode'])) ?>"  nik_lama="<?php echo $header[0]['nik']?>" id_data="<?php echo $header[0]['id']?>" jabatan_real="<?php echo $header[0]['jabatan_id'] ?>">
    <fieldset style="margin-bottom: 15px;">
        <legend>
            <div class="col-xs-12 no-padding">
                <b>Data Karyawan</b>
            </div>
        </legend>
        <div class="col-xs-12 no-padding notifContain">
    
            <div style="display:flex; flex-direction:column;">
                <label for="">Periode</label>
                <?php $bulan = date('n', strtotime($header[0]['periode']));?>
                <select name="bulan" class="editselect2 bulan" disabled onchange="kpi.edit_checkKaryawanPeriode(this, event)">
                    <option value="">-- Pilih Bulan --</option>
                    <option <?php echo $bulan == 1 ? 'selected' : '' ?> value="1">Januari</option>
                    <option <?php echo $bulan == 2 ? 'selected' : '' ?> value="2">Februari</option>
                    <option <?php echo $bulan == 3 ? 'selected' : '' ?> value="3">Maret</option>
                    <option <?php echo $bulan == 4 ? 'selected' : '' ?> value="4">April</option>
                    <option <?php echo $bulan == 5 ? 'selected' : '' ?> value="5">Mei</option>
                    <option <?php echo $bulan == 6 ? 'selected' : '' ?> value="6">Juni</option>
                    <option <?php echo $bulan == 7 ? 'selected' : '' ?> value="7">Juli</option>
                    <option <?php echo $bulan == 8 ? 'selected' : '' ?> value="8">Agustus</option>
                    <option <?php echo $bulan == 9 ? 'selected' : '' ?> value="9">September</option>
                    <option <?php echo $bulan == 10 ? 'selected' : '' ?> value="10">Oktober</option>
                    <option <?php echo $bulan == 11 ? 'selected' : '' ?> value="11">November</option>
                    <option <?php echo $bulan == 12 ? 'selected' : '' ?> value="12">Desember</option>
                </select>
                <!-- <input style="display:none;" type="date" class="tgl_mulai">
                <input style="display:none;" type="date" class="tgl_selesai"> -->
            </div>
    
            <div style="display:flex; flex-direction:column;" class="select-penilai">
                <label for="">Penilai</label>
                <select class="editselect2 penilai" id="penilai" onchange="kpi.edit_get_karyawan_by_penilai(this, event)">
                    <option disabled selected>Pilih Penilai</option>
                    <?php foreach($karyawan as $k){ ?>
                        <option  <?php echo $header[0]['penilai'] == $k['nik'] ? 'selected' : ''  ?> value="<?php echo $k['nik'] ?>"><?php echo ucwords(strtolower($k['nama'])) ?></option>
                    <?php } ?>
                </select>
            </div>
    
            <div style="display:flex; flex-direction:column;" class="select-edit-karyawan">
                <label for="">Nama Karyawan</label>
                <select class="editselect2 karyawan" id="karyawan">
                    <option>Pilih Karyawan</option>
                    <?php foreach($karyawan as $k){ ?>
                        <option data-atasan="<?php echo $k['atasan_nik'] ?>" <?php echo $header[0]['nik'] == $k['nik'] ? 'selected' : ''  ?>  value="<?php echo $k['nik'] ?>"><?php echo ucwords(strtolower($k['nama'])) ?></option>
                    <?php } ?>
                </select>
            </div>
    
            <div style="display:flex; flex-direction:column;">
                <label for="">Jabatan</label>
                <input type="text" value="<?php echo $header[0]['nama_jabatan'] ?>" disabled class="form form-control nama-jabatan">
            </div>
    
        </div>
    </fieldset>


    <table class="table table-bordered" id="table_edit_penilaian">
        <thead>
            <tr>
                <th class="text-center" style="width: 10%">Kode Bobot</th>
                <th class="text-center" style="width: 25%">Nama Penilaian</th>
                <th class="text-center">Bobot</th>
                <th class="text-center">Nilai</th>
                <th class="text-center">Score</th>
                <th class="text-center" style="width: 20%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                function formatPersen($nilai)
                {
                    return rtrim(rtrim($nilai, '0'), '.') . '%';
                }
            ?>

            <?php if (!empty($detail)) { ?>

                <?php 
                    $bobot_total = 0; 
                    $score_total = 0;
                    $no = 1;

                    $kode = $detail[0]['kode_index'];
                    $parts = explode('/', $kode);
                    $periode_bulan = $parts[2];
                    $periode_tahun = $parts[3];

                    $bulan = (int) date('n', strtotime($header[0]['periode']));
                ?>

                 <?php if( $periode_bulan != $bulan ) { ?>
                    <tr>
                        <td colspan="6" style="background-color:#B0ECFF">
                            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Anda sedang menggunakan Index KPI periode <b><?php echo $bulan_text[$periode_bulan] ?></b>
                        </td>
                    </tr>
                <?php } ?>

               

                <?php foreach ($detail as $b) { ?>
                    <?php $bobot_total += $b['bobot']; ?>
                    <?php $score_total += $b['skor']; ?>

                    <tr class="tr_loop" kode_index="<?= $b['kode_index']; ?>">
                        <td class="text-center" style="width:100px;"><?= $no++; ?></td>
                        <td ><?= $b['nama_kpi']; ?></td>
                        <td class="text-center"><?php echo formatPersen($b['bobot']); ?></td>

                        <td style="width:100px;">
                            <input type="number" value="<?php echo number_format($b['nilai']) ?>" class="form-control nilai text-right"  max="100" oninput="if(this.value > 100) this.value = 100; if(this.value < 0) this.value = 0;" onchange="kpi.hitungScore(this)">
                        </td>

                        <td style="width:100px;">
                            <input type="number" value="<?php echo $b['skor'] ?>" class="form-control text-right" disabled>
                        </td>

                        <td>
                            <textarea class="form-control" style="height:34px;"><?php echo $b['catatan'] ?></textarea>
                        </td>
                    </tr>
                <?php } ?>

                <tr>
                    <td colspan="2"><b>Total Bobot</b></td>
                    <td class="text-center" style="width:100px;"> <?php echo $bobot_total .'%' ?></td>
                    <td></td>
                    <td><input type="number" disabled class="form form-control total_score text-right" value="<?php echo $score_total; ?>" style="width:100px;"></td>
                    <td></td>
                </tr>

            <?php } else { ?>
                <tr>
                    <td colspan="6" class="text-center">
                        Data bobot KPI tidak ditemukan
                    </td>
                </tr>
            <?php } ?>

        </tbody>
    </table>
</div>



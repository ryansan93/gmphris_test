
<div class="panel-heading no-padding">
    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#riwayat" data-tab="riwayat">DATA CUTI</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#action" data-tab="action">GENERATE CUTI</a>
        </li>
    </ul>
</div>


<div class="tab-content">

    <div id="riwayat" class="tab-pane fade show active" role="tabpanel" style="padding-top: 10px;">

        <fieldset>
            <legend>Filter Data</legend>

            <div class="generate-wrapper">
                <div class="form-group">
                    <label>Karyawan</label>
                    <select class="select2 fil-karyawan" onchange="mc.filter_list()">
                        <option value="">Pilih Karyawan</option>
                        <?php foreach ($karyawan as $k) { ?>
                            <option value="<?= $k['nik']; ?>"><?= ucwords(strtolower($k['nama'])); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <select class="select2 fil-tahun" onchange="mc.filter_list()">
                        <option value="">Pilih Tahun</option>
                        <?php
                            $tahun = date('Y');
                            for ($i = 0; $i < 5; $i++) {
                                $value = $tahun + $i;
                        ?>
                            <option value="<?= $value; ?>"><?= $value; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>


        </fieldset>
        <br>
        <table class="gmp-table">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th style="text-align:center;">Hak Cuti</th>
                    <th style="text-align:center;">Terpakai</th>
                    <th style="text-align:center;">Sisa</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>

            <tbody class="tb_list_data">
                <?php foreach($data_cuti as $tahun => $cuti) { ?>

                    <tr>
                        <td colspan="6">
                            <strong>Tahun : <?= $tahun ?></strong>
                        </td>
                    </tr>

                    <?php foreach($cuti as $d) { ?>
                        <tr>
                            <td><?= $d['nik'] ?></td>
                            <td><?= ucwords(strtolower($d['nama'])) ?></td>
                            <td style="text-align:center;"><?= (int)$d['hak_cuti'] ?> Hari</td>
                            <td style="text-align:center;"><?= (int)$d['cuti_terpakai'] ?> Hari</td>
                            <td style="text-align:center;"><?= (int)$d['sisa_cuti'] ?> Hari</td>
                            <td style="text-align:center;">
                                <button type="button" 
                                    class="btn btn-warning btn-sm"
                                    onclick="mc.edit_cuti(
                                        '<?= $d['id'] ?>',
                                        '<?= $d['nik'] ?>',
                                        '<?= $d['nama'] ?>',
                                        '<?= $d['hak_cuti'] ?>',
                                        '<?= $d['cuti_terpakai'] ?>',
                                        '<?= $d['sisa_cuti'] ?>'
                                    )">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button type="button" 
                                    class="btn btn-danger btn-sm"
                                    onclick="mc.delete_cuti(<?= $d['id'] ?>)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>

                <?php } ?>
            </tbody>
        </table>
    </div>

    <div id="action" class="tab-pane fade tab-detail" role="tabpanel" style="padding-top: 10px;">
        <fieldset class="fieldset-generate">
            <legend>
                Generate Master Cuti
            </legend>

            <div class="generate-wrapper">

                <div class="form-group">
                    <label>Tahun</label>
                    <select class="tahun select2" onchange="mc.select_for()">
                        <option value="">Pilih Tahun</option>
                            <?php
                                $tahun = date('Y');
                                for ($i = 0; $i < 5; $i++) {
                                    $value = $tahun + $i;
                            ?>
                            <option value="<?= $value; ?>"><?= $value; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Generate Untuk -->
                <div class="form-group">
                    <label>Generate Untuk</label>
                    <div class="radio-wrapper">
                        <label class="radio-item">
                            <input type="radio" name="generate" value="SELECT" onchange="mc.select_for()">
                            Pilih Karyawan
                        </label>

                        <label class="radio-item">
                            <input type="radio" name="generate" value="ALL" onchange="mc.select_for()">
                            Semua Karyawan Aktif
                        </label>
                    </div>
                </div>
            </div>


        </fieldset>
        <br>
        <div class="select-karyawan">
            
        </div>
        <button class="btn btn-primary mt-2"  onclick="mc.generate_cuti()">Generate</button>
    </div>

</div>


<style>
    .auto-resize {
        overflow-y: hidden;
        resize: none;
    }
</style>

<?php if ($config == 1) { ?>
    <?php 
    function formatPersen($nilai)
    {
        return rtrim(rtrim($nilai, '0'), '.') . '%';
    }
    ?>

    <fieldset style="margin-bottom: 15px;">
        <legend style="width:50%">
            <div class="col-xs-12 no-padding" >
                <b>Data Import KPI</b>
            </div>
        </legend>


        <div style="display:flex; flex-direction:column;">
            <label for="">Periode</label>
            <select name="bulan" class="select2 bulan" onchange="kpi.getPeriode();" disabled>
                <option value="">-- Pilih Bulan --</option>
                <option <?php echo $periode == 1 ? 'selected' : '' ?> value="1">Januari</option>
                <option <?php echo $periode == 2 ? 'selected' : '' ?> value="2">Februari</option>
                <option <?php echo $periode == 3 ? 'selected' : '' ?> value="3">Maret</option>
                <option <?php echo $periode == 4 ? 'selected' : '' ?> value="4">April</option>
                <option <?php echo $periode == 5 ? 'selected' : '' ?> value="5">Mei</option>
                <option <?php echo $periode == 6 ? 'selected' : '' ?> value="6">Juni</option>
                <option <?php echo $periode == 7 ? 'selected' : '' ?> value="7">Juli</option>
                <option <?php echo $periode == 8 ? 'selected' : '' ?> value="8">Agustus</option>
                <option <?php echo $periode == 9 ? 'selected' : '' ?> value="9">September</option>
                <option <?php echo $periode == 10 ? 'selected' : '' ?> value="10">Oktober</option>
                <option <?php echo $periode == 11 ? 'selected' : '' ?> value="11">November</option>
                <option <?php echo $periode == 12 ? 'selected' : '' ?> value="12">Desember</option>
            </select>
            <input style="display:none;" type="date" class="tgl_mulai">
            <input style="display:none;" type="date" class="tgl_selesai">
        </div>

        <div style="display:flex; flex-direction:column;" class="select-penilai">
            <label for="">Penilai</label>
            <select class="select2 penilai" id="penilai">
                <?php foreach($penilai as $p) { ?>
                <option <?php echo $karyawan['nik_atasan'] == $p['nik'] ? 'selected' : '' ?> value="<?php echo $p['nik'] ?>"><?php echo ucwords(strtolower($p['nama'])) ?></option>
                <?php }?>
            </select>
        </div>

        <div style="display:flex; flex-direction:column;" class="select-karyawan">
            <label for="">Nama Karyawan</label>
            <select class="select2 karyawan" id="karyawan" onchange="kpi.loadDataBobot(this, event)" disabled>
                <option nik_karyawan="<?php echo $karyawan['nik'] ?>"><?php echo ucwords(strtolower($karyawan['nama'])) ?></option>
            </select>
        </div>

        <div style="display:flex; flex-direction:column;">
            <label for="">Jabatan</label>
            <input type="text" kode_jabatan="<?php echo $karyawan['kode_jabatan'] ?>" value="<?php echo $karyawan['nama_jabatan'] ?>" disabled class="form form-control nama-jabatan">
        </div>

        <hr>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Nama KPI</th>
                    <th class="text-center">Bobot</th>
                    <th class="text-center">Nilai</th>
                    <th class="text-center">Score</th>
                    <th class="text-center">Keterangan</th>
                </tr>
            </thead>
            <tbody>

                <?php if (!empty($template_kpi)) { ?>
                    <?php 
                        $bobot_total = 0; 
                        $bobot_score = 0; 
                        $no = 1;
                    ?>

                    <?php foreach ($template_kpi as $b) { ?>
                        <?php $bobot_total += $b['bobot']; ?>
                        <?php $bobot_score += $b['score']; ?>

                        <tr class="tr_loop" kode_index="<?= $b['kode_index']; ?>">
                            <td class="text-center" style="width:100px;"><?= $no++; ?></td>
                            <td><?= $b['nama_kpi']; ?></td>
                            <td class="text-center"><?php echo formatPersen($b['bobot']); ?></td>

                            <td style="width:100px;">
                                <input value="<?php echo $b['nilai'] ?>" type="number" class="form-control nilai text-right" max="100" oninput="if(this.value > 100) this.value = 100;  if(this.value < 0) this.value = 0;" onchange="kpi.hitungScore(this)">
                            </td>

                            <td style="width:100px;">
                                <input value="<?php echo $b['score'] ?>"  type="number" class="form-control text-right" disabled>
                            </td>

                            <td>
                                <textarea class="form-control auto-resize" style="height:34px;"><?php echo $b['keterangan'] ?></textarea>
                            </td>
                        </tr>
                    <?php } ?>

                    <tr>
                        <td colspan="2"><b>Total Bobot</b></td>
                        <td class="text-center" style="width:100px;"> <?php echo $bobot_total .'%' ?></td>
                        <td></td>
                        <td><input type="number" disabled class="form form-control total_score text-right" value="<?php echo $bobot_score ?>" style="width:100px;"></td>
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

    </fieldset>

<?php } else { ?>
    <div style="border : 1px solid #2987E3; border-radius:5px; width:100%; background-color:#CCE6FF; text-align:center; color:#2987E3; padding:5px;">
        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Data sudah ada
    </div>
<?php } ?>

<script>
    document.querySelectorAll('.auto-resize').forEach(function (el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';

        el.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
</script>

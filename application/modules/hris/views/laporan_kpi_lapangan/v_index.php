<fieldset>
    <legend>Filter Data</legend>
    
    <div style="display:flex; flex-direction:row; gap:10px; width: 100%;">

        <div style="display:flex; flex-direction:column; gap:5px; flex: 1;">
            <label for="tahun">Tahun</label>
            <select onchange="kpi.filter_data();" id="tahun" name="tahun" class="select2" style="width: 100%;">
                <option value="">Pilih Tahun</option>
                <?php 
                    $tahun_sekarang = date('Y'); 
                    for ($i = $tahun_sekarang - 2; $i <= $tahun_sekarang + 2; $i++) {
                        $selected = ($i == $tahun_sekarang) ? 'selected' : '';
                        echo "<option value='{$i}' {$selected}>{$i}</option>";
                    }
                ?>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; gap:5px; flex: 1;">
            <label for="bulan">Bulan</label>
            <select onchange="kpi.filter_data();" id="bulan" name="bulan" class="select2" style="width: 100%;">
                <option value="">Pilih Bulan</option>
                <?php 
                    $bulan_sekarang = (int) date('n');
                    $nama_bulan = [
                        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];

                    for ($i = 1; $i <= 12; $i++) {
                        $selected = ($i == $bulan_sekarang) ? 'selected' : '';
                        echo "<option value='{$i}' {$selected}>{$nama_bulan[$i]}</option>";
                    }
                ?>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; gap:5px; flex: 1;">
            <label for="jabatan">Jabatan</label>
            <select onchange="kpi.filter_data();" id="jabatan" name="jabatan" class="select2" style="width: 100%;">
                <!-- <option value="">Pilih Jabatan</option> -->
                <option value="ppl">PPL</option>
                <option value="penimbang">Penimbang</option>
            </select>
        </div>
    </div>
</fieldset>

<div class="tbl-laporan-kpi" style="margin-top: 10px;">

</div>
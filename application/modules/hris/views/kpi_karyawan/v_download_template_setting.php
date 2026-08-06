<div id="filter_import">  
    
    <label for="">Jabatan</label>
    <select class="select2 jabatan" id="">
        <option>-- Pilih Jabatan --</option>
        <?php foreach($jabatan as $j){ ?>
            <!-- < ?php if($j['kode'] == 'ppl' || $j['kode'] == 'penimbang'){ ?> -->
                <option value="<?php echo $j['kode'] ?>"><?php echo $j['nama'] ?></option>
            <!-- < ?php } ?> -->
        <?php } ?>
        <!-- <option value="ppl">PPL</option>
        <option value="penimbang">Penimbang</option> -->
    </select>
    
    <label for="">Periode</label>
    <select class="select2 periode" id="">
        <option disabled selected>-- Pilih Periode --</option>
        <option value="1">Januari</option>
        <option value="2">Februari</option>
        <option value="3">Maret</option>
        <option value="4">April</option>
        <option value="5">Mei</option>
        <option value="6">Juni</option>
        <option value="7">Juli</option>
        <option value="8">Agustus</option>
        <option value="9">September</option>
        <option value="10">Oktober</option>
        <option value="11">November</option>
        <option value="12">Desember</option>
    </select>

</div>    


<style>
    .btn-import {
        border-radius:5px; 
        border:1px solid #C7C7C7; 
        background-color: #ffffff; 
        padding:5px; 
        width:30px; 
        text-align:center;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }
    .btn-import:hover{
        background-color: #666565; 
        color: #ffffff;
        cursor: pointer;
    }
</style>

<div style="border-radius:5px; border:1px solid #C7C7C7; background-color: #EBEBEB; padding:6px; display: flex; justify-content: space-between; align-items:center;">
    <i>Upload here</i>
    <span class="btn-import" onclick="kpi.exec_import_excel_setting(this, event)">
        <i class="fa fa-upload" aria-hidden="true"></i>
    </span>
</div>
<br>

<div class="kpi-area">
    <fieldset>
        <legend>
            <b>Data Import</b>
        </legend>

            <div>
                <label for="">Nama Template</label>
                <input type="text" class="form form-control nama" placeholder="Masukan nama templete kpi">
            </div>

            <div>
                <label for="">Jabatan</label>
                <select class="select2 jabatan" onchange="kpi.periodeOutstanding(this, event);">
                    <option disabled selected> Pilih Jabatan</option>
                    <?php foreach($jabatan as $j){ ?>
                        <!-- < ?php if($j['kode'] == 'ppl' || $j['kode'] == 'penimbang'){ ?> -->
                            <option value="<?php echo $j['kode'] ?>"><?php echo $j['nama'] ?></option>
                        <!-- < ?php } ?> -->
                    <?php } ?>
                </select>
            </div>

            <div>
                <label for="">Periode</label>
                <select class="select2 periode" id="">
                    <option disabled selected> Pilih Periode</option>
                    <option value="1">JANUARI</option>
                    <option value="2">FEBRUARI</option>
                    <option value="3">MARET</option>
                    <option value="4">APRIL</option>
                    <option value="5">MEI</option>
                    <option value="6">JUNI</option>
                    <option value="7">JULI</option>
                    <option value="8">AGUSTUS</option>
                    <option value="9">SEPTEMBER</option>
                    <option value="10">OKTOBER</option>
                    <option value="11">NOVEMBER</option>
                    <option value="12">DESEMBER</option>
                </select>
            </div>

            <div>
                <label for="">Keterangan</label>
                <textarea type="text" class="form form-control keterangan" rowspan="2"></textarea>
            </div>

            <br>

            <div class="detail-input-import" style="display:flex; flex-direction:column; gap:10px;">
           
            </div>
    </fieldset>
</div>
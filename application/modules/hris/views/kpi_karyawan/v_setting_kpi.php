<style>
    .btn-custom {
        border:1px solid orange; 
        background-color: #F5CC27; 
        border-radius:5px;
        font-size:12px;
        margin-left:15px;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .btn-custom:hover{
        background-color: orange; 
    }
</style>
<div id="setting_kpi">

    <div class="panel-heading no-padding">
        <ul class="nav nav-tabs nav-justified">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#list_data" data-tab="list_data">LIST DATA</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#action" data-tab="action">ADD DATA</a>
            </li>
        </ul>
    </div>
    <br>
    <div class="tab-content">

        <!-- LIST DATA -->
        <div id="list_data" class="tab-pane fade in active">

            <fieldset style="margin-bottom:15px;">
                <legend>
                    <div class="col-xs-12 no-padding">
                        <b>Filter</b>
                    </div>
                </legend>

                <div class="col-xs-12 no-padding notifContain">

                    <input type="text" class="form form-control" placeholder="Masukan kata kunci" oninput="kpi.filter_setting_kpi(this,event)">
                </div>
            </fieldset>

            <fieldset style="margin-bottom:15px;">
                <legend>
                    <div class="col-xs-12 no-padding">
                        <b>Setting Data KPI</b>
                    </div>
                </legend>

                <div class="col-xs-12 no-padding notifContain">

                    <div class="list_data_setting_kpi">

                    </div>

                </div>
            </fieldset>

            <div style="display:flex; justify-content:flex-end; align-items:center; gap:10px;">
                <button class="btn btn-default"
                    onclick="window.location.href='hris/KpiKaryawan'">
                    Kembali
                </button>
            </div>

        </div>

        <!-- ADD DATA -->
        <div id="action" class="tab-pane fade">

            <div>
                <label for="">Nama Template</label>
                <input type="text" class="form form-control nama" placeholder="Masukan nama templete kpi">
            </div>

            <div>
                <label for="">Jabatan</label>
                <select class="select2 jabatan" onchange="kpi.periodeOutstanding(this, event);">
                    <option disabled selected> Pilih Jabatan</option>
                    <?php foreach($jabatan as $j){ ?>
                        <?php if($j['kode'] == 'ppl' || $j['kode'] == 'penimbang'){ ?>
                            <option value="<?php echo $j['kode'] ?>"><?php echo $j['nama'] ?></option>
                        <?php } ?>
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
            <fieldset>
                <legend>
                    <b>Input Data Bobot</b>
                    <button class="btn-custom" onclick="kpi.getKpiPeriode(this, event)">Ambil KPI</button>
                </legend>


                <div class="detail-input" style="display:flex; flex-direction:column; gap:10px;">

                    <div class="row-input" style="display:flex; flex-direction:row; gap:10px;">
                        <input class="form form-control nama_kpi" type="text" placeholder="Masukan nama KPI">
                        <input class="form form-control keterangan_detail" type="text" placeholder="Masukan keterangan">
                        <input class="form form-control bobot" oninput="kpi.config_bobot(this, event)" type="number" style="width:25%" placeholder="Masukan bobot">
                        <button class="btn btn-primary add-row" onclick="kpi.add_row_setting(this, event)"><i class="fa fa-plus"></i></button>
                        <button class="btn btn-danger" onclick="kpi.delete_row_setting(this, event)"><i class="fa fa-close"></i></button>
                    </div>
                </div>
            </fieldset>
            <br>
            <button class=" pull-right btn btn-primary save-setting" onclick="kpi.save_setting(this, event)">Simpan</button>
            <br><br>

        </div>

    </div>

</div>
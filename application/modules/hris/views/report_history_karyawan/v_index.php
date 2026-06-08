

<div id="riwayat" class="tab-pane fade show active" role="tabpanel" style="padding-top: 10px;">


    <fieldset style="margin-bottom: 15px;">
        <legend>
            <div class="col-xs-12 no-padding">
                <b>FILTER</b>
            </div>
        </legend>

        <div style="display:flex; flex-direction:column; gap:15px;">

            <!-- Tanggal -->
            <div class="filter-row">
                <div class="filter-item">
                    <label>Tanggal Awal</label>
                    <div class="input-group date datetimepicker" id="tgl_awal">
                        <input type="text" name="tgl_awal" class="datepicker form-control text-center" placeholder="Tanggal Awal" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>

                <div class="filter-item">
                    <label>Tanggal Akhir</label>
                    <div class="input-group date datetimepicker" id="tgl_akhir">
                        <input type="text" name="tgl_akhir" class="datepicker form-control text-center" placeholder="Tanggal Akhir" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="filter-item">
                <label>Pengusul</label>
                <select class="select2 form-control pengusul">
                    <option selected value="">-- Pilih Karyawan --</option>
                    <?php foreach($karyawan as $k){ ?>
                        <option value="<?php echo $k['nik'] ?>"><?php echo ucwords(strtolower($k['nama'])) ?></option>
                    <?php } ?>
                </select>
            </div>

             <div class="filter-item">
                <label>Karyawan yang Diusulkan</label>
                <select class="select2 form-control karyawan">
                    <option selected value="">-- Pilih Karyawan --</option>
                    <?php foreach($karyawan as $k){ ?>
                        <option value="<?php echo $k['nik'] ?>"><?php echo ucwords(strtolower($k['nama'])) ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Button -->
            <div class="filter-actions">
                <button class="btn btn-primary" onclick="up.filter_data(this, event)">
                    <i class="fa fa-search" style="margin-right: 10px;"></i> Filter
                </button>

                <button class="btn btn-primary" onclick="up.cetak_data_pdf(this, event)">
                    <i class="fa fa-print" style="margin-right: 10px;"></i> Cetak Data
                </button>
            </div>

        </div>
    </fieldset>

    <fieldset style="margin-bottom: 15px;">
        <legend>
            <div class="col-xs-12 no-padding">
                <b>LIST DATA</b>
            </div>
        </legend>
        <div class="col-xs-12 no-padding list_data" style="overflow-x:scroll">
            <div class="spinner-load"></div>
        </div>
    </fieldset>

</div>



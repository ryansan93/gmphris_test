<div class="panel-heading no-padding">
    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#riwayat" data-tab="riwayat">RIWAYAT USULAN</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#action" data-tab="action">ADD DATA</a>
        </li>
    </ul>
</div>


<div class="tab-content">
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
                            <input type="text" name="tgl_awal" class="datepicker form-control text-center"
                                placeholder="Tanggal Awal" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>

                    <div class="filter-item">
                        <label>Tanggal Akhir</label>
                        <div class="input-group date datetimepicker" id="tgl_akhir">
                            <input type="text" name="tgl_akhir" class="datepicker form-control text-center"
                                placeholder="Tanggal Akhir" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Jabatan -->
                <div class="filter-full">
                    <label>Jabatan Yang Diusulkan</label>
                    <select class="select2 form-control jabatan_usulan">
                        <option selected value="">-- Pilih Jabatan --</option>
                        <?php foreach($jabatan as $j){ ?>
                        <option value="<?php echo $j['kode'] ?>"><?php echo $j['nama'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Button -->
                <div class="filter-actions">
                    <button class="btn btn-primary" onclick="up.filter_data(this, event)">
                        <i class="fa fa-search" style="margin-right: 10px;"></i> Filter
                    </button>

                    <button class="btn btn-primary" onclick="up.changeTabActive()">
                        <i class="fa fa-plus" style="margin-right: 10px;"></i> Add Data
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

    <div id="action" class="tab-pane fade tab-detail" role="tabpanel" style="padding-top: 10px;">
        <?php if ($akses['a_submit'] == 1) { ?>
        <style>
        .form-wrapper {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 10px;
        }

        .form-label {
            width: 200px;
            min-width: 200px;
            padding-top: 8px;
        }

        .form-separator {
            width: 20px;
            text-align: center;
            padding-top: 8px;
        }

        .form-content {
            flex: 1;
            min-width: 250px;
        }

        .double-column {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .column-item {
            flex: 1;
            min-width: 220px;
            display: flex;
            flex-direction: column;
        }

        .column-item span {
            margin-bottom: 5px;
        }

        .select2-container {
            width: 100% !important;
        }

        @media (max-width: 768px) {

            .form-row {
                flex-direction: column;
            }

            .form-label,
            .form-separator,
            .form-content {
                width: 100%;
                min-width: 100%;
            }

            .form-separator {
                display: none;
            }

            .double-column {
                flex-direction: column;
            }

            .pull-right {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .pull-right button {
                width: 100%;
            }
        }
        </style>

        <div class="panel panel-default">

            <div class="panel-heading">
                <span style="font-size:17px;">Tambah Data</span>
            </div>

            <div class="panel-body">

                <div class="form-wrapper">

                    <!-- Tanggal Usulan -->
                    <div class="form-row">
                        <span class="form-label">Tgl Usulan</span>
                        <span class="form-separator">:</span>
                        <div class="form-content">
                            <div class="input-group date datetimepicker" id="tgl_usulan">
                                <input type="text" name="tgl_usulan" class="datepicker form-control text-center"
                                    placeholder="Tanggal Usulan" />

                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>

                            </div>

                        </div>

                    </div>

                    <!-- Pengusul -->
                    <div class="form-row">

                        <span class="form-label">Pengusul</span>
                        <span class="form-separator">:</span>

                        <div class="form-content">
                            <select class="select2 pengusul" onchange="up.set_jabatan(this, event, 'pengusul')">
                                <option disabled selected> -- Pilih Karyawan -- </option>
                                <?php foreach ($karyawan as $k) { ?>
                                <option level="<?php echo $k['level']; ?>" id_atasan="<?php echo $k['id']; ?>" jabatan_val="<?php echo $k['jabatan']; ?>"
                                    jabatan_text="<?php echo $k['detail_jabatan']['nama']; ?>"
                                    value="<?php echo $k['nik']; ?>">
                                    <?php echo ucwords(strtolower($k['nama'])) ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Jabatan Pengusul -->
                    <div class="form-row">
                        <span class="form-label">Jabatan Pengusul</span>
                        <span class="form-separator">:</span>
                        <div class="form-content">
                            <input type="text" class="form-control jabatan_pengusul" disabled>
                        </div>
                    </div>

                    <!-- Karyawan -->
                    <div class="form-row">
                        <span class="form-label">Karyawan Yang di Usulkan</span>
                        <span class="form-separator">:</span>
                        <div class="form-content">
                            <?php $outstanding_nik = is_array($outstanding) ? array_column($outstanding, 'karyawan') : []; ?>
                            <select class="select2 karyawan" onchange="up.set_jabatan(this, event, 'karyawan')">
                                <option disabled selected> -- Pilih Karyawan -- </option>
                                <?php foreach ($karyawan as $k) { ?>

                                <?php $disabled = in_array($k['nik'], $outstanding_nik) ? 'disabled' : ''; ?>
                                <option <?php echo $disabled; ?> atasan="<?php echo $k['atasan']; ?>"
                                    id_karyawan="<?php echo $k['id']; ?>" jabatan_val="<?php echo $k['jabatan']; ?>"
                                    jabatan_text="<?php echo $k['detail_jabatan']['nama']; ?>"
                                    level="<?php echo $k['detail_jabatan']['level']; ?>"
                                    value="<?php echo $k['nik']; ?>">
                                    <?php echo ucwords(strtolower($k['nama'])) ?>
                                </option>

                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Jabatan Asal -->
                    <div class="form-row">
                        <span class="form-label">Jabatan Asal</span>
                        <span class="form-separator">:</span>
                        <div class="form-content">
                            <input type="text" class="form-control jabatan_asal" disabled>
                        </div>
                    </div>

                    <!-- Perwakilan + Unit Asal -->
                    <div class="form-row">

                        <span class="form-label">&nbsp;</span>
                        <span class="form-separator"></span>

                        <div class="form-content">
                            <div class="double-column">
                                <div class="column-item">
                                    <span>Perwakilan</span>
                                    <select disabled class="select2 perwakilan_asal select_multiple"
                                        name="perwakilan_asal[]" multiple="multiple">
                                    </select>
                                </div>

                                <div class="column-item">
                                    <span>Unit</span>
                                    <select disabled class="select2 unit_asal select_multiple" name="unit_asal[]"
                                        multiple="multiple">
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Jabatan Tujuan -->
                    <div class="form-row">
                        <span class="form-label">Jabatan Tujuan</span>
                        <span class="form-separator">:</span>
                        <div class="form-content">
                            <select class="select2 jabatan_tujuan" onchange="up.config_atasan_setara(this, event)">
                                <option disabled selected> -- Pilih Jabatan -- </option>
                                <?php foreach ($jabatan as $j) { ?>
                                <option level="<?php echo $j['level'] ?>" value="<?php echo $j['kode']; ?>">
                                    <?php echo $j['nama'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Perwakilan + Unit Tujuan -->
                    <div class="form-row">

                        <span class="form-label">&nbsp;</span>
                        <span class="form-separator"></span>
                        <div class="form-content">
                            <div class="double-column">
                                <div class="column-item">
                                    <span>Perwakilan</span>
                                    <select class="select2 perwakilan_tujuan select_multiple" name="perwakilan_tujuan[]"
                                        onchange="up.set_unit_by_wilayah(this, event)" multiple="multiple">
                                        <option value="all">All</option>
                                        <?php foreach($wilayah as $w){ ?>
                                        <option induk_wil="<?php echo $w['induk']?>" value="<?php echo $w['id']?>">
                                            <?php echo $w['nama']?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="column-item">
                                    <span>Unit</span>
                                    <select class="select2 unit_tujuan" name="unit_tujuan[]" multiple="multiple">
                                        <option value="all">All</option>
                                        <?php foreach($unit as $u){ ?>
                                        <option induk="<?php echo $u['induk']?>" value="<?php echo $u['id']?>">
                                            <?php echo $u['nama']?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Atasan Baru -->
                    <div class="form-row new_atasan" style="display:none">

                        <span class="form-label">Atasan Baru</span>
                        <span class="form-separator">:</span>

                        <div class="form-content">

                            <select class="select2 atasan_baru"></select>

                        </div>

                    </div>

                    <!-- Alasan -->
                    <div class="form-row">
                        <span class="form-label">Alasan</span>
                        <span class="form-separator">:</span>
                        <div class="form-content">
                            <textarea class="form-control alasan"></textarea>
                        </div>
                    </div>
                </div>

                <br>

                <div class="pull-right">

                    <button class="btn btn-secondary" onclick="window.location.href='hris/UsulanPromosi'">
                        <i class="fa fa-angle-left" style="margin-right:10px;" aria-hidden="true"></i>
                        Back
                    </button>

                    <button class="btn btn-primary" onclick="up.save(this, event)">
                        <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>
                        Save Data
                    </button>

                </div>
            </div>
        </div>

        <?php } else { ?>


        <?php }  ?>

    </div>
</div>
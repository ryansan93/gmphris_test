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

<div class="panel panel-default edit_page">

    <div class="panel-heading">
        <span style="font-size:17px;">Edit Data</span>
    </div>

    <div class="panel-body">

        <div class="form-wrapper">

            <!-- Kode Usulan -->
            <div class="form-row">

                <span class="form-label">Kode Usulan</span>
                <span class="form-separator">:</span>

                <div class="form-content">

                    <input type="text"
                           class="form-control kode_usulan"
                           value="<?php echo $data_edit['kode'] ?>"
                           readonly>

                </div>

            </div>

            <!-- Tgl Usulan -->
            <div class="form-row">

                <span class="form-label">Tgl Usulan</span>
                <span class="form-separator">:</span>

                <div class="form-content">

                    <div class="input-group date datetimepicker" id="tgl_usulan">

                        <input type="text"
                               name="tgl_usulan"
                               value="<?php echo $data_edit['tanggal'] ?>"
                               class="datepicker form-control text-center"
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

                    <select class="select2 pengusul"
                            onchange="up.set_jabatan(this, event, 'pengusul')">

                        <option disabled selected>
                            -- Pilih Karyawan --
                        </option>

                        <?php foreach ($karyawan as $k) { ?>

                            <option id_atasan="<?php echo $k['id']; ?>"
                                    jabatan_val="<?php echo $k['jabatan']; ?>"
                                    jabatan_text="<?php echo $k['detail_jabatan']['nama']; ?>"
                                    <?php echo $data_edit['pengusul'] == $k['nik'] ? 'selected' : '' ?>
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

                    <input type="text"
                           class="form-control jabatan_pengusul">

                </div>

            </div>

            <!-- Karyawan -->
            <div class="form-row">

                <span class="form-label">Karyawan Yang di Usulkan</span>
                <span class="form-separator">:</span>

                <div class="form-content">

                    <select class="select2 karyawan"
                            onchange="up.set_jabatan(this, event, 'karyawan')">

                        <option disabled selected>
                            -- Pilih Karyawan --
                        </option>

                        <?php foreach ($karyawan as $k) { ?>

                            <option atasan="<?php echo $k['atasan']; ?>"
                                    id_karyawan="<?php echo $k['id']; ?>"
                                    jabatan_val="<?php echo $k['jabatan']; ?>"
                                    jabatan_text="<?php echo $k['detail_jabatan']['nama']; ?>"
                                    level="<?php echo $k['detail_jabatan']['level']; ?>"
                                    <?php echo $data_edit['karyawan'] == $k['nik'] ? 'selected' : '' ?>
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

                    <input type="text"
                           class="form-control jabatan_asal"
                           disabled>

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

                            <select disabled
                                    class="select2 perwakilan_asal select_multiple"
                                    name="perwakilan_asal[]"
                                    multiple="multiple">

                            </select>

                        </div>

                        <div class="column-item">

                            <span>Unit</span>

                            <select disabled
                                    class="select2 unit_asal select_multiple"
                                    name="unit_asal[]"
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

                    <select class="select2 jabatan_tujuan">

                        <option value="">
                            -- Pilih Jabatan --
                        </option>

                        <?php foreach ($jabatan as $j) { ?>

                            <option level="<?php echo $j['level'] ?>"
                                    <?php echo $data_edit['jabatan_tujuan'] == $j['kode'] ? 'selected' : '' ?>
                                    value="<?php echo $j['kode']; ?>">

                                <?php echo $j['nama'] ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>

            </div>

            <!-- Perwakilan + Unit Tujuan -->
            <div class="form-row">

                <span class="form-label">&nbsp;</span>
                <span class="form-separator"></span>

                <div class="form-content">

                    <?php
                        $selected_perwakilan_tujuan = explode(',', $data_edit['perwakilan_tujuan']);
                        $selected_unit_tujuan = explode(',', $data_edit['unit_tujuan']);
                        $isAllSelected = in_array('all', $selected_perwakilan_tujuan);
                    ?>

                    <div class="double-column">

                        <div class="column-item">

                            <span>Perwakilan</span>

                            <select class="select2 perwakilan_tujuan select_multiple"
                                    name="perwakilan_tujuan[]"
                                    multiple="multiple">

                                <option value="all"
                                    <?php echo $isAllSelected ? 'selected' : '' ?>>

                                    All

                                </option>

                                <?php foreach($wilayah as $w){ ?>

                                    <option induk_wil="<?php echo $w['induk']?>"
                                            value="<?php echo $w['id']?>"
                                            <?php echo in_array($w['id'], $selected_perwakilan_tujuan) ? 'selected' : '' ?>>

                                        <?php echo $w['nama']?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                        <div class="column-item">

                            <span>Unit</span>

                            <select class="select2 unit_tujuan"
                                    name="unit_tujuan[]"
                                    multiple="multiple">

                                <option value="all">All</option>

                                <?php foreach($unit as $u){ ?>

                                    <option induk="<?php echo $u['induk']?>"
                                            value="<?php echo $u['id']?>"
                                            <?php echo in_array($u['id'], $selected_unit_tujuan) ? 'selected' : '' ?>>

                                        <?php echo $u['nama']?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Alasan -->
            <div class="form-row">

                <span class="form-label">Alasan</span>
                <span class="form-separator">:</span>

                <div class="form-content">

                    <textarea class="form-control alasan"><?php echo $data_edit['alasan'] ?></textarea>

                </div>

            </div>

        </div>

        <br>

        <div class="pull-right">

            <button class="btn btn-secondary"
                    onclick="window.location.href='hris/UsulanDemosi'">

                <i class="fa fa-angle-left"
                   style="margin-right:10px;"
                   aria-hidden="true"></i>

                Back

            </button>

            <button class="btn btn-primary"
                    onclick="up.update(this, event)">

                <i class="fa fa-floppy-o"
                   style="margin-right:10px;"
                   aria-hidden="true"></i>

                Save Data

            </button>

        </div>

    </div>

</div>
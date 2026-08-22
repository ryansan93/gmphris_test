<div class="row content-panel">
    <div class="col-lg-12">
        <!-- Search -->
        <div class="col-lg-8 search left-inner-addon no-padding">
            <i class="glyphicon glyphicon-search"></i>
            <input class="form-control" type="search" data-table="tbl_pegawai" 
                   placeholder="Search" onkeyup="filter_all(this)">
        </div>

        <!-- Action -->
        <div class="col-lg-4 action no-padding">
            <?php if ($akses['a_submit'] == 1) { ?>
                <!-- <button id="btn-add" type="button" data-href="peralatan" 
                        class="btn btn-primary cursor-p pull-right" title="ADD" 
                        onclick="pegawai.add_form(this)">
                    <i class="fa fa-plus" aria-hidden="true"></i> ADD
                </button> -->
            <?php } ?>
        </div>
    </div>

    <!-- Data Table -->
    <div class="col-lg-12 data" style="margin-top: 10px;">
        <div class="gmp-table-wrap">
            <table class="gmp-table tbl_pegawai">
                <thead>
                    <tr>
                        <th>Level</th>
                        <th>NIK</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan</th>
                        <th>Atasan</th>
                        <!-- <th class="col-md-1">Marketing</th>
                        <th class="col-md-1">Koordinator</th> -->
                        <th>Wilayah</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Tgl. Berlaku</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="12" class="text-center">Data tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


<fieldset class="filter-panel">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Filter</b>
        </div>
    </legend>

    <div class="filter-row">
        <div class="filter-item">
            <label>Level</label>
            <select class="select2 levelMax" onchange="so.filterStructur(this, event)">
                <option value="">Pilih Level</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
            </select>
        </div>

        <div class="filter-item">
            <label>Perwakilan</label>
            <select class="select2 perwakilan" onchange="so.filterStructur(this, event)">
                <option value="">Pilih Perwakilan</option>
                <?php foreach($perwakilan as $p){ ?>
                    <option value="<?php echo $p['nama_wilayah'] ?>"><?php echo $p['nama_wilayah'] ?></option>
                <?php } ?>                
            </select>
        </div>

        <div class="filter-item">
            <label>Unit</label>
            <select class="select2 unit" onchange="so.filterStructur(this, event)">
                <option value="">Pilih Unit</option>
                <?php foreach($unit as $u){ ?>
                    <option value="<?php echo $u['nama_unit'] ?>"><?php echo $u['nama_unit'] ?></option>
                <?php } ?>                
            </select>
        </div>

        <div class="filter-actions">
            <button class="btn btn-secondary" type="button" onclick="so.resetFilter();">Reset Filter</button>
        </div>
    </div>

</fieldset>

<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Struktur Organisasi</b>
        </div>
    </legend>
    <button class="btn btn-secondary" onclick="so.printTreePdf();"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
    <!-- <button class="btn btn-secondary" onclick="so.exportExcel();">Print Xls</button> -->

    <div class="zoom-area">
        <div onclick="so.zoomIn()"><i class="fa fa-minus" aria-hidden="true"></i></div>
        <div onclick="so.zoomOut()"><i class="fa fa-plus" aria-hidden="true"></i></div>
        <button class="btn btn-secondary" onclick="so.zoomReset()">Reset</button>
    </div>

    <div class="col-xs-12 no-padding so-area" style="width:100%;">

        <?php
            function renderTree($data)
            {
                if (empty($data)) {
                    return;
                }

                echo '<ul>';

                foreach ($data as $item) {

                    echo '<li class="' . (isset($item['one_child']) && $item['one_child'] == 1 ? 'one_child' : '') . '">';

                        echo '
                            <a href="javascript:void(0);">
                                <span>
                                    <div style="z-index:10; font-size:15px; border : 2px solid #ccc; border-radius:5px; padding:5px;">
                                    '.$item['nama_wilayah'].'
                                    <br>
                                    <span style="font-size:10px; ">Unit :  '.$item['nama_unit'].' </span>
                                    </div>
                                    <br>
                                    <br>
                                    <strong>'.$item['nama_jabatan'].'</strong><br>
                                    '.$item['nama'] . ' - ' . $item['level'] . '
                                  
                                </span>
                            </a>
                        ';

                        if (!empty($item['children'])) {
                            renderTree($item['children']);
                        }

                    echo '</li>';
                }

                echo '</ul>';
            }
        ?>
            
        <div class="tree" id="tree">
            <?php renderTree($struktur); ?>
        </div>
    </div>
</fieldset>



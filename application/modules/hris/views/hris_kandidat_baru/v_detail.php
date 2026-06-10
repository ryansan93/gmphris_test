<?php 
    $id_karyawan = $_GET['id'] ?? null;
    $data = $biodata[$id_karyawan] ?? [];

    $standalone = $data['standalone'] ?? [];
    $grouped    = $data['grouped'] ?? [];


    $standaloneMap = [];
    foreach ($standalone as $item) {
        $standaloneMap[$item['label']] = $item['value'];
    }

    $byTitle = [];

    foreach ($standalone as $item) {
        $title = $item['title'] ?? 'Lainnya';
        $byTitle[$title]['standalone'][] = $item;
    }


    foreach ($grouped as $group => $items) {
        $title = $items[0]['title'] ?? 'Lainnya';
        $byTitle[$title]['grouped'][$group] = $items;
    }

    // cetak_r($byTitle['Data Document Kandidat'],1);
?>

<?php if (!empty($byTitle)) { ?>

    <?php foreach ($byTitle as $title => $section) { ?>

        <fieldset style="margin-bottom:15px;">
            <legend><b><?= htmlspecialchars($title) ?></b></legend>

            <?php foreach ($section['standalone'] ?? [] as $item) { ?>

                <?php if (!isset($grouped[$item['label']])) { ?>

                    <div style="display:flex; margin-bottom:4px;">
                        <div style="width:200px;">
                            <?= htmlspecialchars($item['label']) ?>
                        </div>
                        <div style="width:20px;">:</div>
                        <div style="flex:1;">

                            <?php if (empty($item['value'])) { ?>

                                <?php if ($item['title'] == 'Data Document Kandidat') { ?>
                                
                                    <div style="display:flex; gap:5px; flex-direction:row; ">
                                    <div style=" border:1px solid #9b9b9b; border-radius:5px; padding:5px 10px; width:100%; min-height:33px; box-sizing:border-box; ">
                                            <i>Data tidak tersediaa</i>
                                        </div>

                                        <div id_data="<?= $item['id_data_karyawan'] ?>" label="<?php echo $item['label']; ?>" onclick="hf.upload_document(this, event)" style="cursor:pointer; display:flex; justify-content: center; align-items: center; min-width:33px; border:1px solid #7e6c2f; color: #7e6c2f; background-color: #fcdd79; border-radius:5px; padding:5px; height:33px;">
                                            <span title="upload document" ><i class="fa fa-upload" aria-hidden="true"></i></span>
                                        </div>

                                        <div id_data="<?= $item['id_data_karyawan'] ?>" label="<?php echo $item['label']; ?>" onclick="hf.delete_document(this, event)" style="cursor:pointer; display:flex; justify-content: center; align-items: center; min-width:33px; border:1px solid #7e2f2f; color: #7e2f2f; background-color: #fc7979; border-radius:5px; padding:5px; height:33px;">
                                            <span title="delete document" ><i class="fa fa-close" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                
                                    <div style="flex:1; flex-direction:row; display:flex; align-items: center;">
                                        <div style="width:200px; ">
                                            <?= htmlspecialchars($item['value']) ?>
                                        </div>

                                        <i onclick="hf.edit_item(this, event)" label="<?= htmlspecialchars($item['label']) ?>" value="<?= htmlspecialchars($item['value']) ?>" class="fa fa-edit" style="margin-top:5px; cursor:pointer;"></i>
                                    </div>

                                <?php } ?>


                            <?php } else { ?>
                                <?php if ($item['title'] == 'Data Document Kandidat') { ?>
                                    <div style="display:flex; gap:5px; flex-direction:row;">
                                        <?php if($item['value'] != '-') { ?>

                                            <div style=" border:1px solid #9b9b9b; border-radius:5px; padding:5px 10px; width:80%; min-height:33px; box-sizing:border-box; ">
                                                <a href="http://localhost/gmphris_test/uploads/recruitment/<?= htmlspecialchars($item['value']) ?>" target="_blank" style="color:#007bff; text-decoration:none;" >
                                                    <i class="fa fa-file" aria-hidden="true"></i> Show Document
                                                </a>
                                            </div>

                                            <div id_data="<?= $item['id_data_karyawan'] ?>" doc="<?php echo $item['value']; ?>" label="<?php echo $item['label']; ?>" onclick="hf.sinkron_document(this, event)" style="cursor:pointer; display:flex; justify-content: center; align-items: center; min-width:33px; border:1px solid #2f607e; color: #2f607e; background-color: #57b2eb; border-radius:5px; padding:5px; height:33px;">
                                                <span title="sinkron document" ><i class="fa fa-refresh" aria-hidden="true"></i></span>
                                            </div>

                                        <?php } else { ?>

                                            <div style=" border:1px solid #9b9b9b; border-radius:5px; padding:5px 10px; width:80%; min-height:33px; box-sizing:border-box; ">
                                                <i>Data tidak tersedia</i>
                                            </div>

                                        <?php } ?>

                                     

                                        <div id_data="<?= $item['id_data_karyawan'] ?>" label="<?php echo $item['label']; ?>" onclick="hf.upload_document(this, event)" style="cursor:pointer; display:flex; justify-content: center; align-items: center; min-width:33px; border:1px solid #7e6c2f; color: #7e6c2f; background-color: #fcdd79; border-radius:5px; padding:5px; height:33px;">
                                            <span title="upload document" ><i class="fa fa-upload" aria-hidden="true"></i></span>
                                        </div>

                                        <div id_data="<?= $item['id_data_karyawan'] ?>" label="<?php echo $item['label']; ?>" onclick="hf.delete_document(this, event)" style="cursor:pointer; display:flex; justify-content: center; align-items: center; min-width:33px; border:1px solid #7e2f2f; color: #7e2f2f; background-color: #fc7979; border-radius:5px; padding:5px; height:33px;">
                                            <span title="delete document" ><i class="fa fa-trash" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                <?php } else { ?>

                                    <div style="flex:1; flex-direction:row; display:flex; align-items: center;">
                                        <div style="width:200px; ">
                                            <?= htmlspecialchars($item['value']) ?>
                                        </div>

                                        <i onclick="hf.edit_item(this, event)" label="<?= htmlspecialchars($item['label']) ?>" value="<?= htmlspecialchars($item['value']) ?>" class="fa fa-edit" style="margin-top:5px; cursor:pointer;"></i>
                                    </div>

                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>

                <?php } ?>

            <?php } ?>

            <?php foreach ($section['grouped'] ?? [] as $group => $items) { ?>

                <div style="border:1px solid #ccc; border-radius:5px; padding:10px; margin-top:10px;">
                    
                    <div style="font-weight:bold; margin-bottom:8px;">
                        <?= htmlspecialchars($group) ?> :
                        <?= htmlspecialchars($standaloneMap[$group] ?? '-') ?>
                        <i onclick="hf.edit_item(this, event)" label="<?= htmlspecialchars($group) ?>" value="<?= htmlspecialchars($standaloneMap[$group]) ?>" class="fa fa-edit" style="margin-top:5px; cursor:pointer;"></i>
                    </div>

                    <?php foreach ($items as $item) { ?>

                        <div style="display:flex; margin-bottom:4px;">
                            <div style="width:200px;">
                                <?= htmlspecialchars($item['label']) ?>
                            </div>
                            <div style="width:20px;">:</div>
                            <div style="flex:1; flex-direction:row; display:flex;">
                                <div style="width:200px;">
                                    <?= htmlspecialchars($item['value']) ?>
                                </div>

                                <i onclick="hf.edit_item(this, event)" parent="<?= htmlspecialchars($group) ?>" label="<?= htmlspecialchars($item['label']) ?>" value="<?= htmlspecialchars($item['value']) ?>" class="fa fa-edit" style="margin-top:5px; cursor:pointer;"></i>
                            </div>
                        </div>

                    <?php } ?>

                </div>

            <?php } ?>

        </fieldset>

    <?php } ?>

<?php } else { ?>
    <i>Data biodata tidak tersedia</i>
<?php } ?>

 <?php if (empty($byTitle['Data Document Kandidat']) ){ ?>

    <?php 
        $key        = "secretkey";
        $plaintext  = "HRIS/K/004-".  $data_kandidat['id'];
        $encrypted  = openssl_encrypt($plaintext, "AES-128-ECB", $key);
        $url = "http://localhost/recruitment-gmp/FormUpload?kode=" . urlencode($encrypted);
    ?>

    <button class="btn btn-secondary" url="<?php echo $url; ?>" onclick="hf.copy_link(this, event)" ><i class="fa fa-cog"></i> Generate Link Document</button>

<?php } ?>

<button class="btn btn-secondary" onclick="window.location.href='<?php echo base_url();?>hris/HrisKandidatBaru/'">Kembali</button>
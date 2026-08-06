<div class="attachment-list d-flex flex-wrap">
    <?php foreach ($attachment as $key => $val) { ?>
        <div class="m-1 text-center">
            <div class="border rounded p-1" style="width:100px; cursor:pointer;"
                 onclick="ukr.previewAttachment('<?php echo base_url($val['file_path']); ?>')">

                <img src="<?php echo base_url($val['file_path']); ?>"
                     class="img-fluid rounded"
                     style="width:80px;height:80px;object-fit:cover;">

            </div>

            <small class="d-block text-truncate" style="width:100px;">
                <?php echo $val['nama_file']; ?>
            </small>
        </div>
    <?php } ?>
</div>

<hr>

<div id="attachment-preview" class="text-center">
    <span class="text-muted">Pilih attachment untuk preview</span>
</div>
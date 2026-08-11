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
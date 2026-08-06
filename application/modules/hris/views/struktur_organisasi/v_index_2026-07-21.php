
<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Struktur Organisasi</b>
        </div>
    </legend>

    <div>
        <label for="">Level</label>
        <div style="display:flex; flex-direction:row; gap:10px;">
            <!-- <select class="select2 levelMin" onchange="so.filterStructur(this, event)">
                <option disabled selected>Pilih Level</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
            </select> -->
            <select class="select2 levelMax" onchange="so.filterStructur(this, event)">
                <option disabled selected>Pilih Level</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
            </select>
        </div>
    </div>

</fieldset>

<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Struktur Organisasi</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding " style="width:100%;">

        <div class="col-xs-12 no-padding so-area" style=" width:100%; overflow-x:auto;overflow-y:hidden;">

            <div class="org-scroll">

                <?php
                function renderOrganisasi($data, $targetNik = null)
                {

                    echo '<div class="org-container">';

                    foreach($data as $item)
                    {
                        $hoverClass = '';

                        if($targetNik != null && $item['atasan_nik'] == $targetNik)
                        {
                            $hoverClass = ' has-hover';
                        }

                        echo '<div class="org-node'.$hoverClass.'">';

                            echo '
                            <div class="org-box">
                                <div class="org-title">'.$item['nama_jabatan'].'</div>
                                <div class="org-name">'.$item['nama'].'</div>
                            </div>
                            ';

                            if(!empty($item['children']))
                            {
                                echo '
                                <div class="line-down"></div>
                                <div class="children-wrapper">
                                ';

                                renderOrganisasi($item['children'], $item['nik']);

                                echo '
                                </div>
                                ';
                            }

                        echo '</div>';
                    }

                    echo '</div>';
                }

                renderOrganisasi($struktur);
                ?>


            </div>

        </div>

    </div>
</fieldset>
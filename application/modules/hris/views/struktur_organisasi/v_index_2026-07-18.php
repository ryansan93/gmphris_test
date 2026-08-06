<?php
    function generateTree($data)
    {

        echo "<ul>";

        foreach($data as $row){

            echo '
            <li>
                <div class="no-padding divSO expand">

                    <p>'.$row['nama_unit'].'</p>

                    <hr>

                    <p>
                        '.($row['nama'] ?? '-').'
                    </p>

                    <hr>

                    <p>
                        '.$row['jabatan'].'<br>
                        '.$row['nama_jabatan'].'
                    </p>

                    <hr>

                    <p>
                        
                    </p>

                </div>
            ';


            // jika punya anak
            if(!empty($row['children'])){

                generateTree($row['children']);

            }


            echo "</li>";

        }


        echo "</ul>";

    }
?>


<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Struktur Organisasi</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding list_data" style="width:100%;">

        <div id="view_struktur_organisasi" class="form-group col-md-12">
            <div class="tab-content new-line">
                <div class="tab-pane active" id="for_grup_tssr">
                    <div class="tree col-md-12 text-center" id="tssr">
                        <ul style="margin-left: auto; margin-right: auto;">
                            <li style="display: block; margin-right: auto; justify-content: center;">
                                <div class="no-padding divSO expand">
                                    <p>DIVISI SOFTWARE DEVELOPMENT</p>
                                    <hr>
                                    <p>(-)</p>
                                    <hr>
                                    <p>Kepala Divisi<br>KADIV SOFTWARE DEVELOPMENT - AOT</p>
                                    <hr>
                                    <p></p>
                                </div>
                                <ul style="display: block;">
                                    <li style="width: 100%; height: 100%; top:16px;">
                                        <div class="no-padding divSO expand">
                                            <p>DEPARTEMEN SOFTWARE DEVELOPMENT</p>
                                            <hr>
                                            <p>ANDIKA RAFON SINUHAJI</p>
                                            <hr>
                                            <p>Kepala Departemen<br>KADEP SOFTWARE DEVELOPMENT - AOT</p>
                                            <hr>
                                            <p></p>
                                        </div>
                                        <ul style="display: block;">
                                            <li style="width: 100%; height: 100%; top:16px;">
                                                <div class="no-padding divSO expand" style="">
                                                    <p>SUB-DEP SOFTWARE DEVELOPMENT</p>
                                                    <hr>
                                                    <p>(-)</p>
                                                    <hr>
                                                    <p>Wakil Kepala Departemen<br>WAKADEP SOFTWARE DEVELOPMENT - AP - AOT</p>
                                                    <hr>
                                                    <p></p>
                                                </div>
                                                <ul class="expand-all" style="display: block;">
                                                    <li style="width: 100%; height: 100%; top:16px;">
                                                        <div class="no-padding divSO expand" style="">
                                                            <p>BAGIAN SOFTWARE DEVELOPMENT</p>
                                                            <hr>
                                                            <p>(-)</p>
                                                            <hr>
                                                            <p>Kepala Bagian<br>KABAG SOFTWARE DEVELOPMENT - FP - AOT</p>
                                                            <hr>
                                                            <p></p>
                                                        </div>
                                                        <ul class="expand-all" style="display: block;">
                                                            <li style="width: 100%; height: 100%; top:16px;">
                                                                <div class="no-padding divSO expand">
                                                                    <p>STAF SOFTWARE DEVELOPMENT</p>
                                                                    <hr>
                                                                    <p>(1) -, <br> (2) -</p>
                                                                    <hr>
                                                                    <p>Staff<br>STAF SOFTWARE DEVELOPMENT - FP - AOT</p>
                                                                    <hr>
                                                                    <p></p>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li style="width: 100%; height: 100%; top:16px;">
                                                        <div class="no-padding divSO expand">
                                                            <p>BAGIAN SOFTWARE DEVELOPMENT</p>
                                                            <hr>
                                                            <p>-</p>
                                                            <hr>
                                                            <p>Kepala Bagian<br>KABAG SOFTWARE DEVELOPMENT - RPA - AOT</p>
                                                            <hr>
                                                            <p></p>
                                                        </div>
                                                        <ul class="expand-all" style="display: block;">
                                                            <li style="width: 100%; height: 100%; top:16px;">
                                                                <div class="no-padding divSO expand">
                                                                    <p>STAF SOFTWARE DEVELOPMENT</p>
                                                                    <hr>
                                                                    <p>(1) -, <br> (2) -</p>
                                                                    <hr>
                                                                    <p>Staff<br>STAF SOFTWARE DEVELOPMENT - RPA - AOT</p>
                                                                    <hr>
                                                                    <p></p>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li style="width: 100%; height: 100%; top:16px;">
                                                <div class="no-padding divSO expand">
                                                    <p>SUB-DEP SOFTWARE DEVELOPMENT</p>
                                                    <hr>
                                                    <p>(-)</p>
                                                    <hr>
                                                    <p>Wakil Kepala Departemen<br>WAKADEP SOFTWARE DEVELOPMENT - BREEDING - AOT</p>
                                                    <hr>
                                                    <p></p>
                                                </div>
                                                <ul class="expand-all" style="display: block;">
                                                    <li style="width: 100%; height: 100%; top:16px;">
                                                        <div class="no-padding divSO expand">
                                                            <p>BAGIAN SOFTWARE DEVELOPMENT</p>
                                                            <hr>
                                                            <p>(-)</p>
                                                            <hr>
                                                            <p>Kepala Bagian<br>KABAG SOFTWARE DEVELOPMENT - BREEDING - AOT</p>
                                                            <hr>
                                                            <p></p>
                                                        </div>
                                                        <ul class="expand-all" style="display: block;">
                                                            <li style="width: 100%; height: 100%; top:16px;">
                                                                <div class="no-padding divSO expand">
                                                                    <p>STAF SOFTWARE DEVELOPMENT</p>
                                                                    <hr>
                                                                    <p>(1) -, <br> (2) -</p>
                                                                    <hr>
                                                                    <p>Staff<br>STAF SOFTWARE DEVELOPMENT - BREEDING - AOT</p>
                                                                    <hr>
                                                                    <p></p>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li style="width: 100%; height: 100%; top:16px;">
                                                <div class="no-padding divSO expand">
                                                    <p>SUB-DEP SOFTWARE DEVELOPMENT</p>
                                                    <hr>
                                                    <p>(-)</p>
                                                    <hr>
                                                    <p>Wakil Kepala Departemen<br>WAKADEP SOFTWARE DEVELOPMENT - CORPORATE - AOT</p>
                                                    <hr>
                                                    <p></p>
                                                </div>
                                                <ul class="expand-all" style="display: block;">
                                                    <li style="width: 100%; height: 100%; top:16px;">
                                                        <div class="no-padding divSO expand">
                                                            <p>BAGIAN SOFTWARE DEVELOPMENT</p>
                                                            <hr>
                                                            <p>-</p>
                                                            <hr>
                                                            <p>Kepala Bagian<br>KABAG SOFTWARE DEVELOPMENT - CORPORATE - AOT</p>
                                                            <hr>
                                                            <p></p>
                                                        </div>
                                                        <ul class="expand-all" style="display: block;">
                                                            <li style="width: 100%; height: 100%; top:16px;">
                                                                <div class="no-padding divSO expand">
                                                                    <p>STAF SOFTWARE DEVELOPMENT</p>
                                                                    <hr>
                                                                    <p>(1) -, <br> (2) -, <br> (3) -</p>
                                                                    <hr>
                                                                    <p>Staff<br>STAF SOFTWARE DEVELOPMENT - CORPORATE - AOT</p>
                                                                    <hr>
                                                                    <p></p>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li style="width: 100%; height: 100%; top:16px;">
                                                <div class="no-padding divSO expand">
                                                    <p>SUB-DEP SOFTWARE DEVELOPMENT</p>
                                                    <hr>
                                                    <p>-</p>
                                                    <hr>
                                                    <p>Wakil Kepala Departemen<br>WAKADEP SOFTWARE DEVELOPMENT - FM - AOT</p>
                                                    <hr>
                                                    <p></p>
                                                </div>
                                                <ul class="expand-all" style="display: block;">
                                                    <li style="width: 100%; height: 100%; top:16px;">
                                                        <div class="no-padding divSO expand">
                                                            <p>BAGIAN SOFTWARE DEVELOPMENT</p>
                                                            <hr>
                                                            <p>-</p>
                                                            <hr>
                                                            <p>Kepala Bagian<br>KABAG SOFTWARE DEVELOPMENT - FM - AOT</p>
                                                            <hr>
                                                            <p></p>
                                                        </div><ul class="expand-all" style="display: block;">
                                                            <li style="width: 100%; height: 100%; top:16px;">
                                                                <div class="no-padding divSO expand">
                                                                    <p>STAF SOFTWARE DEVELOPMENT</p>
                                                                    <hr>
                                                                    <p>(1) -, <br> (2) -, <br> (3) -</p>
                                                                    <hr>
                                                                    <p>Staff<br>STAF SOFTWARE DEVELOPMENT - FM - AOT</p>
                                                                    <hr>
                                                                    <p></p>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>			
                    </div>
                </div>
            </div>
        </div>

        </div>

    </div>
</fieldset>
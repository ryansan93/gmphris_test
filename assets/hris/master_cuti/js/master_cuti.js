let mc = {

    select_for: () => {
        let selected = $('input[name="generate"]:checked').val();

        if (selected == "SELECT") {
            let params = {
                tahun: $(".tahun").val(),
            };

            $.ajax({
                url: 'hris/MasterCuti/SelectKaryawan',
                type: 'POST',
                data: params,
                dataType: 'html',
                beforeSend: function () {
                    showLoading();
                },
                success: function (res) {
                    hideLoading();

                    $(".select-karyawan").html(res);
                    $(".select2").select2();
                }
            });
        } else if (selected == "ALL") {
            console.log("Generate semua karyawan aktif");

             $(".select-karyawan").html('');
        }
    },

    addrow: function() {
        let option = $('#list-karyawan .row-karyawan:first .karyawan').html();

        let html = `
            <div class="row-karyawan" style="display:flex; flex-direction:row; gap:10px; margin-top:10px;">
                <select class="karyawan select2" ">
                    ${option}
                </select>

                <button class="btn btn-secondary" type="button" onclick="mc.addrow()">
                    <i class="fa fa-plus"></i>
                </button>

                <button class="btn btn-danger" type="button" onclick="mc.removerow(this)">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        `;

        $('#list-karyawan').append(html);

        $('#list-karyawan .karyawan').last().select2({
            width: '100%'
        });

        mc.refreshOption();
    },

    removerow: function(elm) {
        if ($('#list-karyawan .row-karyawan').length > 1) {
            $(elm).closest('.row-karyawan').remove();
            mc.refreshOption();
        }
    },

    refreshOption: function() {
        // Ambil semua yang dipilih
        let selected = [];

        $('.karyawan').each(function () {
            let val = $(this).val();
            if (val) {
                selected.push(val);
            }
        });

        // Disable option yang sudah dipilih di select lain
        $('.karyawan').each(function () {
            let current = $(this).val();

            $(this).find('option').prop('disabled', false);

            selected.forEach((nik) => {
                if (nik != current) {
                    $(this).find('option[value="' + nik + '"]').prop('disabled', true);
                }
            });

            $(this).select2('destroy').select2({
                width: '100%'
            });
        });
    },

    generate_cuti: () => {
        let generate = $('input[name="generate"]:checked').val();

        if (!generate) {
            bootbox.alert("Silahkan pilih Generate Untuk terlebih dahulu");
            return false;
        }

        let params = {
            generate: generate,
            tahun: $(".tahun").val(),
            hak_cuti: $(".hak_cuti").val(),
            karyawan: []
        };

        // Jika pilih karyawan
        if (generate == "SELECT") {

            $(".karyawan").each(function() {
                let nik = $(this).val();

                if (nik) {
                    params.karyawan.push(nik);
                }
            });

            if (params.karyawan.length == 0) {
                bootbox.alert("Silahkan pilih karyawan terlebih dahulu");
                return false;
            }
        }

        // Jika semua karyawan aktif
        else if (generate == "ALL") {
            params.karyawan = [];
        }


        $.ajax({
            url: 'hris/MasterCuti/generate_cuti',
            type: 'POST',
            data: params,
            dataType: 'JSON',
            beforeSend: function() {
                showLoading();
            },
            success: function(res) {
                hideLoading();

                if (res.status) {
                    bootbox.alert("Generate berhasil", function () {
                        window.location.reload();
                    });
                } else {
                    bootbox.alert(res.message);
                }
            },
            error: function(xhr) {
                hideLoading();
                console.log(xhr.responseText);
            }
        });
    },

    delete_cuti: function(id){

        bootbox.confirm("Apakah data cuti akan dihapus?", function(result){

            if(result){

                $.ajax({
                    url: 'hris/MasterCuti/delete_cuti',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        id: id
                    },

                    beforeSend:function(){
                        showLoading();
                    },

                    success:function(res){

                        hideLoading();

                        if(res.status){

                            bootbox.alert("Data berhasil dihapus", function(){
                                location.reload();
                            });

                        }else{

                            bootbox.alert(res.message);

                        }

                    },

                    error:function(xhr){

                        hideLoading();
                        console.log(xhr.responseText);

                    }
                });

            }

        });

    },

    edit_cuti: function(id, nik, nama, hak_cuti, cuti_terpakai, sisa_cuti) {

        hak_cuti = parseInt(hak_cuti) || 0;
        cuti_terpakai = parseInt(cuti_terpakai) || 0;
        sisa_cuti = parseInt(hak_cuti);

        let html = `
            <div class="form-group">
                <label>NIK</label>
                <input type="text" class="form-control" value="${nik}" readonly>
            </div>

            <div class="form-group">
                <label>Nama</label>
                <input type="text" class="form-control" value="${nama}" readonly>
            </div>

            <div class="form-group">
                <label>Hak Cuti</label>
                <input type="number" class="form-control edit-hak-cuti" value="${hak_cuti}">
            </div>

            <div class="form-group">
                <label>Terpakai</label>
                <input type="number" class="form-control edit-cuti-terpakai" value="${cuti_terpakai}">
            </div>

            <div class="form-group">
                <label>Sisa Cuti</label>
                <input type="number" class="form-control edit-sisa-cuti" value="${sisa_cuti}" readonly>
            </div>

        `;


        bootbox.dialog({
            title: "Edit Master Cuti",
            message: html,

            buttons: {
                cancel: {
                    label: "Batal",
                    className: "btn-default"
                },

                save: {
                    label: "Simpan",
                    className: "btn-primary",

                    callback: function(){

                        let hak_cuti_baru = $(".edit-hak-cuti").val();

                        if(hak_cuti_baru == ""){
                            bootbox.alert("Hak cuti tidak boleh kosong");
                            return false;
                        }


                        $.ajax({
                            url: 'hris/MasterCuti/update_cuti',
                            type: 'POST',
                            dataType: 'JSON',
                            data: {
                                id: id,
                                hak_cuti: $(".edit-hak-cuti").val(),
                                cuti_terpakai: $(".edit-cuti-terpakai").val()
                            },
                            beforeSend:function(){
                                showLoading();
                            },

                            success:function(res){

                                hideLoading();

                                if(res.status){

                                    bootbox.alert("Data berhasil diupdate", function(){
                                        location.reload();
                                    });

                                }else{

                                    bootbox.alert(res.message);

                                }

                            },

                            error:function(xhr){
                                hideLoading();
                                console.log(xhr.responseText);
                            }
                        });

                        return false;
                    }
                }
            }
        });

    },


    filter_list : () => {
        let params = {
            karyawan: $(".fil-karyawan").val(),
            tahun: $(".fil-tahun").val(),
        }
        
        $.ajax({
            url: 'hris/MasterCuti/filter_list',
            type: 'POST',
            dataType: 'html',
            data: params,
            beforeSend:function(){
                showLoading();
            },
            success:function(res){
                hideLoading();
                $(".tb_list_data").html(res);
            },

            error:function(xhr){
                hideLoading();
                console.log(xhr.responseText);
            }
        });
    }

}

$(document).ready(function () {
    $(".select2").select2();

    
})

$(document).on('change', '.karyawan', function () {
    mc.refreshOption();
});
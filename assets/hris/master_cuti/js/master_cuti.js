let mc = {

    select_for: () => {
        let selected = $('input[name="generate"]:checked').val();

        if (selected == "SELECT") {
            
            let params = {
                tahun: $(".tahun").val(),
                perwakilan: [],
                unit: []
            };

            if (params.tahun == '') {
                toastr.info("Silahkan pilih tahun");
                return false;
            }

            $.ajax({
                url: 'hris/MasterCuti/getDataUnitPerwakilan',
                type: 'POST',
                success: function (res) {

                    let data = typeof res === 'string' ? JSON.parse(res) : res;

                    let optionPerwakilan = '';

                    // =========================
                    // PERWAKILAN
                    // =========================
                    $.each(data.perwakilan, function (i, item) {
                        optionPerwakilan += `
                            <option value_nama="${item.nama}" value="${item.id}">
                                ${item.nama}
                            </option>
                        `;
                    });

                    // =========================
                    // BOOTBOX
                    // =========================
                    let dialog = bootbox.confirm({
                        title: 'Konfirmasi Perwakilan',

                        message: `
                            <div class="form-group">
                                <label>Perwakilan</label>
                                <select class="form-control"
                                        id="bootbox_perwakilan"
                                        multiple>
                                    ${optionPerwakilan}
                                </select>
                            </div>

                            <div class="form-group mt-3">
                                <label>Unit</label>
                                <select class="form-control"
                                        id="bootbox_unit"
                                        multiple>
                                </select>
                            </div>
                        `,

                        buttons: {
                            confirm: {
                                label: 'Tampilkan',
                                className: 'btn-primary'
                            },
                            cancel: {
                                label: 'Batal',
                                className: 'btn-secondary'
                            }
                        },

                        callback: function (result) {

                            if (!result) {
                                return;
                            }

                            params.tahun        = $(".tahun").val();
                            params.perwakilan = $('#bootbox_perwakilan option:selected')
                                .map(function () {
                                    return $(this).attr('value_nama');
                                })
                                .get();

                            params.unit = $('#bootbox_unit option:selected')
                                .map(function () {
                                    return $(this).attr('value_nama');
                                })
                                .get();

                            // console.log('PARAMS:', params);

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

                        }
                    });

                    // =========================
                    // SETELAH BOOTBOX TAMPIL
                    // =========================
                    dialog.on('shown.bs.modal', function () {

                        // =========================
                        // SELECT2 PERWAKILAN
                        // =========================
                        $('#bootbox_perwakilan').select2({
                            placeholder: 'Pilih Perwakilan',
                            width: '100%',
                            multiple: true,
                            dropdownParent: dialog
                        });

                        // =========================
                        // SELECT2 UNIT
                        // =========================
                        $('#bootbox_unit').select2({
                            placeholder: 'Pilih Unit',
                            width: '100%',
                            multiple: true,
                            dropdownParent: dialog
                        });

                        // =========================
                        // PERWAKILAN CHANGE
                        // =========================
                        $('#bootbox_perwakilan').on('change', function () {

                            let selectedPerwakilan = $(this).val() || [];
                            let optionUnit = '';

                            console.log('Perwakilan:', selectedPerwakilan);
                            console.log('Data Unit:', data.unit);

                            $.each(data.unit, function (i, item) {

                                let induk = String(item.induk);

                                // Tidak ada perwakilan
                                // → tampilkan semua unit
                                //
                                // Ada perwakilan
                                // → filter berdasarkan induk
                                if (
                                    selectedPerwakilan.length === 0 ||
                                    selectedPerwakilan.includes(induk)
                                ) {

                                    optionUnit += `
                                        <option value_nama="${item.nama}" value="${item.kode}">
                                            ${item.nama}
                                        </option>
                                    `;
                                }
                            });

                            $('#bootbox_unit')
                                .empty()
                                .append(optionUnit)
                                .val(null)
                                .trigger('change');
                        });

                        // =========================
                        // INITIAL LOAD
                        // =========================
                        // Belum ada Perwakilan
                        // → tampilkan SEMUA Unit
                        $('#bootbox_perwakilan').trigger('change');
                    });
                }
            });

            // console.log(params);

            // $.ajax({
            //     url: 'hris/MasterCuti/SelectKaryawan',
            //     type: 'POST',
            //     data: params,
            //     dataType: 'html',
            //     beforeSend: function () {
            //         showLoading();
            //     },
            //     success: function (res) {
            //         hideLoading();

            //         $(".select-karyawan").html(res);
            //         $(".select2").select2();
            //     }
            // });
        } else if (selected == "ALL") {
            console.log("Generate semua karyawan aktif");

             $(".select-karyawan").html('');
        }
    },

    // addrow: function(btn) {
    //     // Ambil option dari select pertama sebagai template
    //     let option = $('#list-karyawan .row-karyawan:first .karyawan').html();

    //     // Hitung nomor urut
    //     let rowCount = $('#list-karyawan .row-karyawan').length + 1;

    //     let html = `
    //         <tr class="row-karyawan">
    //             <td class="row-number">${rowCount}</td>
    //             <td>
    //                 <div class="karyawan-select-wrapper">
    //                     <select class="karyawan select2">
    //                         ${option}
    //                     </select>
    //                 </div>
    //             </td>
    //             <td>
    //                 <div class="karyawan-actions">
    //                     <button type="button" class="btn btn-add" onclick="mc.addrow(this)" title="Tambah Baris">
    //                         <i class="fa fa-plus"></i>
    //                     </button>
    //                     <button type="button" class="btn btn-remove" onclick="mc.removerow(this)" title="Hapus Baris">
    //                         <i class="fa fa-trash"></i>
    //                     </button>
    //                 </div>
    //             </td>
    //         </tr>
    //     `;

    //     $('#list-karyawan').append(html);

    //     // Inisialisasi Select2 untuk row baru
    //     $('#list-karyawan .row-karyawan').last().find('.karyawan').select2({
    //         width: '100%',
    //         placeholder: 'Pilih Karyawan',
    //         allowClear: true
    //     });

    //     mc.refreshOption();
    //     mc.updateCount();
    // },

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
            karyawan: []
        };

        // Jika pilih karyawan
        if (generate == "SELECT") {

            params.karyawan = []; 

            $("#list-karyawan").find(".row-karyawan").each(function() {
                let nik = $(this).find("td:eq(1) input").attr("nik");        
                let hak_cuti = $(this).find("td:eq(3) input").val();

                if (nik && nik.trim() !== "") {
                    params.karyawan.push({
                        nik: nik,
                        hak_cuti: hak_cuti
                    });
                }
            });

            if (params.karyawan.length == 0) {
                bootbox.alert("Silahkan pilih karyawan terlebih dahulu");
                return false;
            }
        }

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
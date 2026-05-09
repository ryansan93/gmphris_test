
let masterOptions = '';

let up ={

    start_up : () =>{
        $('#tgl_awal').datetimepicker({
            locale: 'id',
                format: 'DD MMM YYYY'
        });

        $('#tgl_akhir').datetimepicker({
            locale: 'id',
                format: 'DD MMM YYYY'
        });

        $('#tgl_usulan').datetimepicker({
            locale: 'id',
                format: 'DD MMM YYYY'
        });


        $('#tgl_awal').on('dp.change', function(e) {
            $('#tgl_akhir').data('DateTimePicker').minDate(e.date);
        });

        $('.select2').select2();
        
    },

    load_form : () => {
        $.ajax({
            url : 'hris/UsulanPromosi/load_form',
            // data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                // showLoading(); 
            },
            success : function(html){
                hideLoading();

                $(".list_data").html(html)
               
            },
        });
    },

    filter_data: () => {

        let picker_awal = $('#tgl_awal').data('DateTimePicker');
        let picker_akhir = $('#tgl_akhir').data('DateTimePicker');

        let tgl_awal = ''; 
        let tgl_akhir = '';

        if (picker_awal && picker_awal.date()) {
            tgl_awal = picker_awal.date().format('YYYY-MM-DD');
        } 

        if (picker_akhir && picker_akhir.date()) {
            tgl_akhir = picker_akhir.date().format('YYYY-MM-DD');
        } 

        let params = {
            tgl_awal: tgl_awal,
            tgl_akhir: tgl_akhir,
            jabatan_usulan : $(".jabatan_usulan").val(),
        };

        // console.log(params)

        $.ajax({
            url : 'hris/UsulanPromosi/filter_data',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading();

                $(".list_data").html(html)
               
            },
        });

    },

    changeTabActive: () => {
        $('a[href="#action"]').tab('show');
    },

    set_jabatan: (elm, e, kolom) => {

        let data = {
            jabatan_text : $(elm).find("option:selected").attr("jabatan_text"),
            jabatan_val  : $(elm).find("option:selected").attr("jabatan_val"),
            id_karyawan  : $(elm).find("option:selected").attr("id_karyawan") ?? null,
            id_atasan    : $(elm).find("option:selected").attr("id_atasan") ?? null,
        }
      

        if (data.id_karyawan){
            // console.log(data.id_karyawan)
            $.ajax({
                url : 'hris/UsulanPromosi/get_unit_wilayah_json',
                data : data,
                type : 'POST',
                dataType : 'json',
                beforeSend : function(){ 
                    showLoading(); 
                },
                success : function(result){
                    hideLoading();

                    console.log(result);

                    let unit = result.unit;
                    let wilayah = result.wilayah;

                    let $perwakilan = $('.perwakilan_asal');
                    $perwakilan.empty();

                    $.each(wilayah, function (i, v) {
                        let option = new Option(v.nama, v.id, true, true);
                        $perwakilan.append(option);
                    });
                    $perwakilan.trigger('change');


                    let $unit = $('.unit_asal');
                    $unit.empty();

                    $.each(unit, function (i, v) {
                        let option = new Option(v.nama, v.id, true, true);
                        $unit.append(option);
                    });
                    $unit.trigger('change');
                                    
                },
            });     

        }   

        if(kolom == 'pengusul'){

            $(".jabatan_pengusul").val(data.jabatan_text);
            $(".jabatan_pengusul").attr("jabatan_val", data.jabatan_val);

            up.set_karyawan_bawahan(data.id_atasan);

        } else {

            $(".jabatan_asal").val(data.jabatan_text);
            $(".jabatan_asal").attr("jabatan_val", data.jabatan_val);

            let level = parseInt($(elm).find("option:selected").attr("level"));
            
            if( $(".edit_page").length < 1 ){

               let level = parseInt($(elm).find("option:selected").attr("level"));

                if (!window.allJabatanOption) {
                    window.allJabatanOption = $('.jabatan_tujuan').html();
                }

                $('.jabatan_tujuan').html(window.allJabatanOption);
                $('.jabatan_tujuan option').each(function () {

                    let optionLevel = parseInt($(this).attr('level'));

                    if (isNaN(optionLevel)) return;

      
                    if (optionLevel >= level) {
                        $(this).remove();
                    }

                });

            
                $('.jabatan_tujuan').val(null).trigger('change');
                $('.jabatan_tujuan').trigger('change.select2');
                $(".jabatan_tujuan").val("").trigger("change");

                console.log('add_page')
            } else {
                console.log('edit_page');

                let level = parseInt($(elm).find("option:selected").attr("level"));

                $(".jabatan_tujuan option").each(function () {

                    let optionLevel = parseInt($(this).attr("level"));
                    if (optionLevel >= level) {

                        $(this).remove();

                    }

                });

            }
        }

    

    },

    set_karyawan_bawahan: (id_karyawan) => {

        let select = $(".karyawan");

        if (!select.data("original_option")) {
            select.data(
                "original_option",
                select.html()
            );
        }

        select.html(
            select.data("original_option")
        );

        select.prepend(
            '<option value="">-- Pilih Karyawan --</option>'
        );

        select.find("option").each(function () {

            let atasan = $(this).attr("atasan");

            if (!$(this).val()) {
                return;
            }

            if (String(id_karyawan) !== String(atasan)) {
                $(this).remove();
            }

        });

    },


    save: () => {

        let picker = $('#tgl_usulan').data('DateTimePicker');

        let tgl_usulan = ''; 
        if (picker && picker.date()) {
            tgl_usulan = picker.date().format('YYYY-MM-DD');
        } else {
            toastr.info("Tanggal usulan belum dipilih");
            return false;
        }

        let params = {
            tgl_usulan          : tgl_usulan,
            pengusul            : $(".pengusul").val(),
            jabatan_pengusul    : $(".jabatan_pengusul").attr("jabatan_val"),
            karyawan            : $(".karyawan").val(),
            jabatan_asal        : $(".jabatan_asal").attr("jabatan_val"),
            jabatan_tujuan      : $(".jabatan_tujuan").val(),
            alasan              : $(".alasan").val(),
            perwakilan_tujuan   : $('.perwakilan_tujuan').val(),
            unit_tujuan         : $('.unit_tujuan').val(),
            perwakilan_asal     : $('.perwakilan_asal').val(),
            unit_asal           : $('.unit_asal').val(),
        };

        if (!params.pengusul) {
            toastr.warning("Pengusul wajib dipilih");
            $(".pengusul").focus();
            return false;
        }

        if (!params.jabatan_pengusul) {
            toastr.warning("Jabatan pengusul wajib dipilih");
            $(".jabatan_pengusul").focus();
            return false;
        }

        if (!params.perwakilan_tujuan) {
            toastr.warning("Perwakilan / Wilayah pengusul wajib dipilih");
            $(".perwakilan_tujuan").focus();
            return false;
        }

        if (!params.karyawan) {
            toastr.warning("Karyawan wajib dipilih");
            $(".karyawan").focus();
            return false;
        }

        if (!params.jabatan_asal) {
            toastr.warning("Jabatan asal wajib dipilih");
            $(".jabatan_asal").focus();
            return false;
        }

        if (!params.jabatan_tujuan) {
            toastr.warning("Jabatan tujuan wajib dipilih");
            $(".jabatan_tujuan").focus();
            return false;
        }

        if (!params.alasan || params.alasan.trim() === '') {
            toastr.warning("Alasan wajib diisi");
            $(".alasan").focus();
            return false;
        }

        $.ajax({
            url : 'hris/UsulanPromosi/save',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    
                    // up.load_form();
                    // $('a[href="#riwayat"]').tab('show');

                    window.location.reload(true);
                });
            },
            error : function(xhr){
                hideLoading();
                toastr.error("Terjadi kesalahan saat menyimpan data");
            }
        });

    },

    show_detail : (elm, e) => {
        let params = {
            kode : $(elm).attr("id_data"),
            status : $(elm).attr("status"),
        }

        // console.log(params)

        $.ajax({
            url : 'hris/UsulanPromosi/show_detail',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                // showLoading(); 
            },
            success : function(html){

                let btns = {
                    tutup: {
                        label: '<i class="fa fa-close"></i> Tutup',
                        className: 'btn-secondary',
                    }
                };

                let dialog = bootbox.dialog({
                    title: "Data Detail",
                    message: html,
                    size: 'large',
                    buttons: btns,
                });

                dialog.on('shown.bs.modal', function () {

                    let is_delete = dialog.find('.is_delete').attr('config');
                    let is_edit   = dialog.find('.is_edit').attr('config');

                    // console.log(is_delete)
                    
                    let footer = dialog.find('.modal-footer');

                    if (params.status == 1 && is_edit == 1) {

                        footer.prepend(`
                            <button type="button" class="btn btn-warning btn-edit">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                        `);

                        dialog.find('.btn-edit').on('click', function () {
                            window.location.href =
                                "hris/UsulanPromosi/edit_data?kode=" + params.kode;
                        });
                    }

                    if (params.status == 1 && is_delete == 1) {

                        footer.prepend(`
                            <button type="button" class="btn btn-danger btn-delete">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        `);

                        dialog.find('.btn-delete').on('click', function () {
                            up.delete_data(params.kode);
                        });
                    }

                });
            },
        });
    },

    update: () => {

        let picker = $('#tgl_usulan').data('DateTimePicker');

        let tgl_usulan = ''; 
        if (picker && picker.date()) {
            tgl_usulan = picker.date().format('YYYY-MM-DD');
        } else {
            toastr.info("Tgl belum dipilih")
            return false
        }

        let params = {
            kode                : $(".kode_usulan").val(),
            tgl_usulan          : tgl_usulan,
            pengusul            : $(".pengusul").val(),
            jabatan_pengusul    : $(".jabatan_pengusul").attr("jabatan_val"),
            karyawan            : $(".karyawan").val(),
            jabatan_asal        : $(".jabatan_asal").attr("jabatan_val"),
            jabatan_tujuan      : $(".jabatan_tujuan").val(),
            alasan              : $(".alasan").val(),
            perwakilan_tujuan   : $('.perwakilan_tujuan').val(),
            unit_tujuan         : $('.unit_tujuan').val(),
            perwakilan_asal     : $('.perwakilan_asal').val(),
            unit_asal           : $('.unit_asal').val(),
            
        }

        $.ajax({
            url : 'hris/UsulanPromosi/update',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    
                    // up.load_form();
                    window.location.href = "hris/UsulanPromosi";
                });
            },
        });

    },

    delete_data: (kode ) => {

        let params = {
            kode : kode,
        }

        bootbox.confirm('Yakin mau hapus?', function(result) {
            if (result) {
                $.ajax({
                    url : 'hris/UsulanPromosi/delete',
                    data : params,
                    type : 'POST',
                    dataType : 'json',
                    beforeSend : function(){ 
                        showLoading(); 
                    },
                    success : function(data){
                        hideLoading();

                        bootbox.alert(data.message, function () {
                            up.load_form();
                        });
                    },
                });
            }
        });

    },

    keputusan: (elm, val) => {

        const STATUS = {
            DRAFT: 1,
            ACK: 2,
            APPROVE: 3,
            REJECTHRD: 4,
            REJECTCEO: 5,
        };

        let kode = $(elm).attr("kode"); 

        let text = val == STATUS.ACK ? 'Acknowledge' 
                : val == STATUS.APPROVE ? 'Approve' 
                : val == STATUS.REJECTHRD ? 'Reject' 
                : val == STATUS.REJECTCEO ? 'Reject' 
                : 'DRAFT';

        if (val == STATUS.REJECTHRD || val == STATUS.REJECTCEO) {

            bootbox.prompt({
                title: "Masukkan alasan reject",
                inputType: 'textarea',
                callback: function(result) {
                    if (result === null) return;

                    if (!result.trim()) {
                        bootbox.alert('Keterangan wajib diisi!');
                        return false;
                    }

                    up.exec_keputusan(kode, val, result);
                }
            });

        } else if (val == STATUS.APPROVE) {

            let html = `
                <div class="form-group">
                    <label>Tanggal Berlaku</label>
                    <div class="input-group date datetimepicker" id="tgl_berlaku">
                        <input type="text" class="form-control text-center" placeholder="Tanggal Berlaku" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            `;

            let dialog = bootbox.dialog({
                title: "Approve Pengusulan",
                message: html,
                buttons: {
                    cancel: {
                        label: "Batal",
                        className: "btn-secondary"
                    },
                    confirm: {
                        label: "Approve",
                        className: "btn-success",
                        callback: function () {

                            let picker = $('#tgl_berlaku').data('DateTimePicker');

                            if (!picker || !picker.date()) {
                                bootbox.alert("Tanggal wajib diisi!");
                                return false;
                            }

                            let tgl = picker.date().format('YYYY-MM-DD');

                            up.exec_keputusan(kode, val, null, tgl);
                        }
                    }
                }
            });

            dialog.on('shown.bs.modal', function () {
                $('#tgl_berlaku').datetimepicker({
                    locale: 'id',
                    format: 'DD MMM YYYY'
                });
            });

    
        } else {

            bootbox.confirm(`Apakah Anda yakin ingin ${text} pengusulan ini?`, function(result){
                if(!result) return;

                up.exec_keputusan(kode, val, null, null);
            });

        }
    },

    exec_keputusan:(kode, val, keterangan = null, tgl_berlaku = null) => {

        let params = {
            keputusan : val,
            kode : kode,
            keterangan : keterangan,
            tgl_berlaku : tgl_berlaku,
        };

        $.ajax({
            url : 'hris/UsulanPromosi/keputusan',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    bootbox.hideAll();
                    // up.load_form();

                    window.location.reload(true);
                });
            },
        });
    },

    set_unit_by_wilayah: (elm, e) => {

        let selected_wilayah = [];

        $(elm).find("option:selected").each(function () {
            selected_wilayah.push($(this).val());
        });

        let select_unit = $(".unit_tujuan");

        if (!select_unit.data("original_option")) {
            select_unit.data("original_option", select_unit.html());
        }

        select_unit.html(select_unit.data("original_option"));

   
        if (selected_wilayah.includes('all')) {

            select_unit.html('<option value="all" selected>All</option>');

            select_unit.trigger("change");
            return;
        }

        select_unit.find("option").each(function () {

            let induk = $(this).attr("induk");

            if (!selected_wilayah.includes(induk)) {
                $(this).remove();
            }

        });

        select_unit.trigger("change");
    },

    reset_form: function () {

        $('[name="tgl_usulan"]').val('');
        $('.pengusul').val(null).trigger('change');
        $('.karyawan').val(null).trigger('change');
        $('.jabatan_tujuan').val(null).trigger('change');
        $('.perwakilan_asal').val(null).trigger('change');
        $('.unit_asal').val(null).trigger('change');
        $('.perwakilan_tujuan').val(null).trigger('change');
        $('.unit_tujuan').val(null).trigger('change');
        $('.jabatan_pengusul').val('');
        $('.jabatan_asal').val('');
        $('.alasan').val('');

    }
};


$(document).ready(function() {

    up.load_form();
    up.start_up()

    
    let $pengusul = $('.pengusul');
    let $karyawan = $('.karyawan');
    let $perwakilan_tujuan = $('.perwakilan_tujuan');

    if ($pengusul.val()) {
        $pengusul.trigger('change');
        $karyawan.trigger('change');
    }

    if ($perwakilan_tujuan.val()){
        $perwakilan_tujuan.trigger('change');
    }

    
    
    
    
});


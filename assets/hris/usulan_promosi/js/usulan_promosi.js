
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

        

        $('input[name="tgl_awal"]').on('dp.change', function(e) {
            $('input[name="tgl_akhir"]').data('DateTimePicker').minDate(e.date);
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
            jabatan_val : $(elm).find("option:selected").attr("jabatan_val"),
        }

        if(kolom == 'pengusul'){
            $(".jabatan_pengusul").val(data.jabatan_text);
            $(".jabatan_pengusul").attr("jabatan_val" , data.jabatan_val);
        } else {
            $(".jabatan_asal").val(data.jabatan_text);
            $(".jabatan_asal").attr("jabatan_val" , data.jabatan_val);
        }
        // console.log(data);
        
    },

    save: () => {

        let picker = $('#tgl_usulan').data('DateTimePicker');

        let tgl_usulan = ''; 
        if (picker && picker.date()) {
            tgl_usulan = picker.date().format('YYYY-MM-DD');
        } else {
            toastr.info("Tgl belum dipilih")
            return false
        }

        let params = {
            tgl_usulan          : tgl_usulan,
            pengusul            : $(".pengusul").val(),
            jabatan_pengusul    : $(".jabatan_pengusul").attr("jabatan_val"),
            karyawan            : $(".karyawan").val(),
            jabatan_asal        : $(".jabatan_asal").attr("jabatan_val"),
            jabatan_tujuan      : $(".jabatan_tujuan").val(),
            alasan              : $(".alasan").val(),
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
                    
                    up.load_form();

                    $('a[href="#riwayat"]').tab('show');
                });
            },
        });
        // console.log(params);

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
                                "hris/UsulanPromosi/edit_attr?kode=" + params.kode;
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
                    up.load_form();
                });
            },
        });
    },
};


$(document).ready(function() {

    up.load_form();
    up.start_up()

    
    let $pengusul = $('.pengusul');
    let $karyawan = $('.karyawan');

    if ($pengusul.val()) {
        $pengusul.trigger('change');
        $karyawan.trigger('change');
    }
    
    
});


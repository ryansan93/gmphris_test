
let masterOptions = '';

let up ={

    start_up : () =>{
        $('.datepicker').datetimepicker({
            locale: 'id',
                format: 'DD MMM YYYY'
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

        let picker = $('input[name="tgl_usulan"]').data('DateTimePicker');

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
                });
            },
        });
        // console.log(params);

    },

    show_detail : (elm, e) => {
        let params = {
            kode : $(elm).attr("id_data"),
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
               
                bootbox.dialog({
                    title: "Data Detail",
                    message: html,
                    size: 'large',

                    buttons: {
                        cancel: {
                            label: 'Batal',
                            className: 'btn-secondary',
                            callback: function () {
                                console.log('Batal diklik');
                            }
                        },
                        confirm: {
                            label: 'Simpan',
                            className: 'btn-primary',
                            callback: function () {
                                console.log('Simpan diklik');
                            }
                        }
                    }
                });
               
            },
        });
    }
};


$(document).ready(function() {

    up.load_form();
    up.start_up()

    
});


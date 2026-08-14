$(document).ready(function(){
    ud.load_data();
    ud.setting_up();
})


let ud = {

    setting_up : () => {

        $(".select2").select2();

    },

    load_data : () => {
        $.ajax({
            url : 'hris/UserDeactivated/load_data',
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                // showLoading(); 
            },
            success : function(html){
                hideLoading();

                $("#notification-list").html(html)
               
            },
        });
    },

    exec_nonaktifkanUser : (el) => {
        let nik = $(el).attr('nik');    
        let id_data = $(el).attr('id_data');  
        let tgl_berjalan = $(el).attr('tgl_berjalan');
        let tgl_resign = $(el).attr('tgl_resign');
        let tglIndonesia = $(el).attr('tglIndonesia');

        let lanjut = function() {
            $.ajax({
                url : 'hris/UserDeactivated/exec_nonaktifkanUser',
                type : 'POST',
                data : {
                    nik : nik,
                    id_data : id_data,
                },
                dataType : 'json',
                beforeSend : function(){
                    showLoading();
                },
                success : function(res){
                    hideLoading();

                    if(res.status == '1'){
                        bootbox.alert({
                            message: 'Operasi berhasil.',
                            callback: function(){
                                location.reload();
                            }
                        });
                    } else {
                        bootbox.alert('Gagal memproses permintaan.');
                    }
                },
                error: function(){
                    hideLoading();
                    bootbox.alert('Terjadi kesalahan saat menghubungi server.');
                }
            });
        };


        if (tgl_berjalan < tgl_resign) {
            bootbox.confirm({
                message: 'Tanggal resign <b>' + tglIndonesia + '</b> belum melewati tanggal saat ini. Apakah Anda yakin ingin tetap menonaktifkan user?',
                buttons: {
                    confirm: {
                        label: 'Ya',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'Tidak',
                        className: 'btn-danger'
                    }
                },
                callback: function(result){
                    if(result){
                        lanjut();
                    }
                }
            });

        } else {
            lanjut();
        }
    },

    exec_aktifkanUser : (el) => {
        let nik = $(el).attr('nik');    
        let id_data = $(el).attr('id_data');    

        $.ajax({
            url : 'hris/UserDeactivated/exec_aktifkanUser',
            type : 'POST',
            data : {
                nik : nik,
                id_data : id_data,
            },
            dataType : 'json',
            beforeSend : function(){
                showLoading();
            },
            success : function(res){
                hideLoading();
                if(res.status == '1'){
                    bootbox.alert({
                        message: 'Operasi berhasil.',
                        callback: function(){
                            location.reload();
                        }
                    });
                } else {
                    bootbox.alert('Gagal memproses permintaan.');
                }
            },
            error: function(){
                hideLoading();
                bootbox.alert('Terjadi kesalahan saat menghubungi server.');
            }
        });
    },

    filter_list : () => {
        let params = {
            status : $(".status").val() ?? null,
            jabatan : $(".jabatan").val() ?? null,
        }

        $.ajax({
            url: 'hris/UserDeactivated/filter_data',
            method: 'POST',
            data: params,
            dataType: 'html',
            beforeSend: function(){
                showLoading();
            },
            success: function(html){
                hideLoading();      
                $("#notification-list").html(html)

            }
        });
    }

}
let tr ={

     load_form : () => {
        $.ajax({
            url : 'hris/TrackingRecruitment/load_form',
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

    show_tracking: (elm, e) => {

        let document = $(elm).attr('document');
        window.open(`hris/TrackingRecruitment/show_tracking?document=${document}`, '_blank');

    },

    show_kandidat_table: (elm, e, value ) => {

        let table = '';

        if (value == 1) {
            table = $(".list_data_kandidat").clone();
        } else {
            table = $(".list_data_karyawan").clone();
        }

        table.removeAttr('style');

        bootbox.dialog({
            title : 'Data Kandidat',
            size : 'large',
            message : table,
            buttons: {
                close: {
                    label: 'Tutup',
                    className: 'btn-secondary',
                    callback: function() {  
           
                    }
                }
            }
        });
    },

    show_karyawan_detail: (elm, e, nik) => {

        $.ajax({
            url: 'hris/TrackingRecruitment/show_karyawan_detail',
            type: 'POST',
            data: { nik: nik },
            dataType: 'html',
            success: function(html) {

                bootbox.dialog({
                    title: 'Detail Data Karyawan',
                    message: html,
                    size: 'large',
                    buttons: {
                        close: {
                            label: 'Tutup',
                            className: 'btn-secondary',
                            callback: function() {
                            }
                        }
                    }
                });
            }
        });
    },


    filter_data: () => {
        let key = $(".filter").val().toLowerCase();
        
        if (key === '') {
            $(".tracking-card").show();
        } else {
            $(".tracking-card").each(function() {
                let text = $(this).text().toLowerCase();
                if (text.includes(key)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    },
}

$(document).ready(function() {
    tr.load_form();
    
    $(".filter").on('keyup', function() {
        tr.filter_data();
    });
});


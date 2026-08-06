let report = {

    setting_up : () => {
        $(".select2").select2();
    },

    load_data : (elm, e) => {

        $.ajax({
            url: 'hris/ReportCutiKaryawan/load_data',
            method: 'POST',
            // data: { load_empty: 1 },
            dataType: 'html',
            success: function(r){

                $(".list-area").html(r);

            }
        });
    },

    filter_list : () => {

        let params = {
            jenis : $(".jenis").val() ?? null,
            jabatan : $(".jabatan").val() ?? null,
            bulan : $(".bulan").val() ?? null,
            karyawan : $(".karyawan").val() ?? null,
            status : $(".status").val() ?? null,
        }

        console.log(params);

        $.ajax({
            url: 'hris/ReportCutiKaryawan/filter_data',
            method: 'POST',
            data: params,
            dataType: 'html',
            beforeSend: function(){
                showLoading();
            },
            success: function(r){
                hideLoading();      
                $(".list-area").html(r);

            }
        });

    },
 

    show_detail_data: (elm, e) => {

        let params = {
            id_data : $(elm).attr("id_data"),
        }

        $.ajax({
            url: 'hris/ReportCutiKaryawan/show_detail_data',
            method: 'POST',
            data: params,
            dataType: 'html',
            success: function(html){

                bootbox.dialog({
                    title: 'Laporan Resign',
                    message: html,
                    size: 'large',
                    buttons: {
                        close: {
                            label: 'Tutup',
                            className: 'btn-secondary',
                            callback: function() {
                                // cukup return, biarkan bootbox menutup sendiri
                                bootbox.hideAll();

                            }
                        }
                    }
                });

            }
        });
    },


    detail_selectImage : (elm, e) => {

        let params = {
            path : $(elm).attr("src"),
            nama_barang : $(elm).attr("nama_barang"),
            kondisi_barang : $(elm).attr("kondisi_barang"),
            jumlah : $(elm).attr("jumlah"),
        }

        let image = `<img src="` + params.path +`" style="width:100%; height:220px; object-fit:cover; object-position:center;">`
        $(".image-selected").html(image)

        $(".nama_barang").html(params.nama_barang)
        $(".kondisi_barang").html(params.kondisi_barang)
        $(".jumlah_barang").html(params.jumlah)
        // console.log(params)

    },

    show_attachment: (elm, e) => {
        let img = $(elm).attr('src');

        fetch(img)
            .then(res => res.blob())
            .then(blob => {
                let url = URL.createObjectURL(blob);
                window.open(url, '_blank');
            });
    },

    export_excel: () => {

        let params = {
            jenis: $(".jenis").val() ?? '',
            jabatan: $(".jabatan").val() ?? '',
            bulan: $(".bulan").val() ?? '',
            karyawan: $(".karyawan").val() ?? '',
            status : $(".status").val() ?? '',
        };

        let query = $.param(params);

        window.location.href = 'hris/ReportCutiKaryawan/export_excel?' + query;
    },
}

$(document).ready(function()
{
    report.load_data();
    report.setting_up();
})
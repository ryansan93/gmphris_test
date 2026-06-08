
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
            url : 'hris/ReportHistoryKaryawan/load_form',
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

        if (!tgl_awal) {
            bootbox.alert('Start Date tidak boleh kosong.');
            return;
        }

        if (!tgl_akhir) {
            bootbox.alert('End Date tidak boleh kosong.');
            return;
        }

        let params = {
            startdate : tgl_awal,
            enddate : tgl_akhir,
            karyawan : $(".karyawan").val(),
            pengusul : $(".pengusul").val(),
        };

        // console.log(params)

        $.ajax({
            url : 'hris/ReportHistoryKaryawan/filter_data',
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

    cetak_data_pdf: () => {

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

        if (!tgl_awal) {
            bootbox.alert('Tanggal awal tidak boleh kosong.');
            return;
        }

        if (!tgl_akhir) {
            bootbox.alert('Tanggal akhir tidak boleh kosong.');
            return;
        }

        let kode = [];

        $(".table-list tbody tr").each(function () {
            let temp = $(this).find("td:eq(0)").text();
            kode.push(temp);
        });

        $.ajax({
            url: 'hris/ReportHistoryKaryawan/cetak_data_pdf',
            type: 'POST',
            dataType: 'json',
            data: { 
                kode : kode,
                tgl_awal : tgl_awal,
                tgl_akhir : tgl_akhir,
            },

            beforeSend: function () {
                showLoading();
            },

            success: function (res) {
                hideLoading();

                if (res.status === true) {
                    window.open(res.url, '_blank'); // 🔥 buka PDF GET
                } else {
                    alert("Gagal generate PDF");
                }
            },

            error: function () {
                hideLoading();
                alert("Server error");
            }
        });
    }
  
};


$(document).ready(function() {

    up.load_form();
    up.start_up()
    
});


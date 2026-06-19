let kpi = {
    
    setting_up: () => {
        $(".select2").select2();
    },

    loadDataBobot: (elm, e) => {

        let params = {
            nik : $(elm).val() ?? null,
            jabatan : $(elm).find("option:selected").attr("jabatan") ?? null,
            nama_jabatan : $(elm).find("option:selected").attr("nama_jabatan") ?? null,
            bulan : $('.bulan').val(),
        }

        $.ajax({
            url : 'hris/KpiKaryawan/loadDataBobot',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading(); 
                $(".nama-jabatan").val(params.nama_jabatan);

                // console.log(html)
                $(".list_bobot").find('tbody').html(html);
            },
        });
    },

    getPeriode: (elm, e) => {
        let bulan = $('.bulan').val();
        let penilai = $('.penilai').val() ?? null;
        // let tglMulai = $('.tgl_mulai').val();
        // let tglSelesai = $('.tgl_selesai').val();

        if (bulan) {
            let tahun = new Date().getFullYear();

            let firstDay = new Date(tahun, bulan - 1, 1);
            let lastDay = new Date(tahun, bulan, 0);

            let format = (d) => {
                let year = d.getFullYear();
                let month = String(d.getMonth() + 1).padStart(2, '0');
                let day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            $('.tgl_mulai').val(format(firstDay));
            $('.tgl_selesai').val(format(lastDay));

            kpi.config_data_penilaian(format(firstDay), format(lastDay), penilai);
        } else {
            toastr.info("Periode masih kosong")
        }

        kpi.loadDataBobot();

    },

    config_data_penilaian :(startdate, enddate, penilai) => {

        let params = {
            startdate : startdate,
            enddate : enddate,
            penilai : penilai,
        }

        $.ajax({
            url : 'hris/KpiKaryawan/configDataPenilaian',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success: function(data){
                hideLoading();

                $(".select-karyawan").html(data);

                $('.karyawan').select2();
                $('.penilai').select2();
                
            },
        });

    },

    hitungScore: (elm) => {
        let tr    = $(elm).closest('tr');
        let bobot = parseFloat( tr.find('td:eq(2)').text() ) || 0;
        let nilai = parseFloat( $(elm).val() ) || 0;
        let score = (nilai * bobot) / 100;

        tr.find('td:eq(4) input').val(score.toFixed(2));

        kpi.hitungTotal();
    },

    hitungTotal: () => {
        let totalNilai = 0;
        let totalScore = 0;

        $('table tbody tr').each(function () {

            let nilai = parseFloat($(this).find('.nilai').val()) || 0;

            // ambil score dari kolom input di row itu
            let score = parseFloat($(this).find('td:eq(4) input').val()) || 0;

            totalNilai += nilai;
            totalScore += score;
        });

        // $('.total_nilai').val(totalNilai.toFixed(2));
        $('.total_score').val(totalScore.toFixed(2));
    },

    save : (elm, e) => {

        let header = {
            nik         : $(".karyawan").val(),
            jabatan     : $(".karyawan").find("option:selected").attr("jabatan"),
            total_score : $(".list_bobot").find(".total_score").val(),
            tgl_mulai   : $(".tgl_mulai").val(),
            tgl_selesai : $(".tgl_selesai").val(),
        };

   

        let detail = [];
        let valid = true;
        let pesan = '';

        $(".list_bobot tbody .tr_loop").each(function(index){

            let nilai = $(this).find("td:eq(3) input").val();

            if ($.trim(nilai) == '') {
                valid = false;

                let nama_kpi = $(this).find("td:eq(1)").text().trim();

                pesan = 'Nilai KPI "' + nama_kpi + '" belum diisi.';
                return false;
            }

            let temp = {
                id_kpi     : $(this).attr("id_kpi"),
                kode_kpi   : $(this).find("td:eq(0)").html().trim(),
                nama_kpi   : $(this).find("td:eq(1)").html().trim(),
                nilai      : nilai,
                score      : $(this).find("td:eq(4) input").val(),
                keterangan : $(this).find("td:eq(5) textarea").val(),
            };

            detail.push(temp);
        });

        if (!valid) {
            toastr.error(pesan);
            return;
        }

        let params = {
            header : header,
            detail : detail,
        };

        bootbox.confirm({
            title: '<i class="glyphicon glyphicon-question-sign"></i> Konfirmasi',
            message: 'Apakah Anda yakin ingin menyimpan penilaian KPI ini?',
            buttons: {
                confirm: {
                    label: 'Ya',
                    className: 'btn-primary'
                },
                cancel: {
                    label: 'Batal',
                    className: 'btn-default'
                }
            },
            callback: function(result) {

                if (!result) {
                    return;
                }

                $.ajax({
                    url : 'hris/KpiKaryawan/save',
                    data : params,
                    type : 'POST',
                    dataType : 'json',
                    beforeSend : function(){
                        showLoading();
                    },
                    success : function(data){
                        hideLoading();

                        if(data.status == 1){
                            toastr.success(data.message);

                            setTimeout(function(){
                                window.location.href = 'hris/KpiKaryawan';
                            }, 1000);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error : function() {
                        hideLoading();
                        toastr.error('Terjadi kesalahan sistem.');
                    }
                });

            }
        });
    },

    random_value:() => {
        document.querySelectorAll('.nilai').forEach(function (input) {
            let random = Math.floor(Math.random() * 51) + 50; // 50 - 100
            input.value = random;

            kpi.hitungScore(input);
        });
    },


    load_data_approval: () => {

        let params = {};
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('kode')) {
            params.kode = urlParams.get('kode');
        }

        $.ajax({
            url : 'hris/KpiKaryawan/loadDataApproval',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading(); 

                $(".list_approval").html(html);
            },
        });

    },


    show_penilaian : (elm, e) => {

        let params = {
            id_data : $(elm).attr("id_data"),
        }

        $.ajax({
            url : 'hris/KpiKaryawan/showPenilaian',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(resp){
                hideLoading(); 

                bootbox.dialog({
                    title: '<b>Detail Penilaian KPI</b>',
                    message: resp,
                    size: 'large',
                    backdrop: true,
                    onEscape: true,
                    buttons: {
                        close: {
                            label: 'Tutup',
                            className: 'btn-secondary'
                        }
                    }
                });

                $(".btn-detail").attr("id_data", params.id_data)
                
            },
        });
    },


    load_data_setting: () => {

        $.ajax({
            url : 'hris/KpiKaryawan/loadDataSetting',
            // data : params,
            // type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading(); 

                $(".list_data_setting_kpi").html(html);
            },
        });

    },

    add_row_setting: (elm, e) => {
        e.preventDefault();

        let row = $(elm).closest('.row-input').clone();
        row.find('input').val('');
        $('.detail-input').append(row);
    },

    delete_row_setting: (elm, e) => {
        e.preventDefault();

        let totalRow = $('.detail-input .row-input').length;

        if (totalRow <= 1) {
            toastr.info("Minimal harus ada 1 baris");
            return;
        }

        $(elm).closest('.row-input').remove();
        kpi.config_bobot();
    },

    config_bobot: (elm, e) => {
        let totalBobot = 0;

        $(".bobot").each(function () {
            totalBobot += parseFloat($(this).val()) || 0;
        });

        if (totalBobot > 100) {
            let nilaiSaatIni = parseFloat($(elm).val()) || 0;
            let sisaBobot = 100 - (totalBobot - nilaiSaatIni);

            $(elm).val(sisaBobot > 0 ? sisaBobot : 0);

            toastr.warning("Total bobot tidak boleh lebih dari 100%");
        }

        totalBobot = 0;
        $(".bobot").each(function () {
            totalBobot += parseFloat($(this).val()) || 0;
        });

        $(".add-row").prop("disabled", totalBobot >= 100);

        return true;
    },

    save_setting : () => {

        let header = {
            nama : $("#setting_kpi").find(".nama").val(),
            jabatan : $("#setting_kpi").find(".jabatan").val(),
            periode : $("#setting_kpi").find(".periode").val(),
            keterangan : $("#setting_kpi").find(".keterangan").val(),
        }

        let detail = [];

        $(".row-input").each(function(){
            let temp = {
                index_kpi : $(this).find(".nama_kpi").val(),
                keterangan : $(this).find(".keterangan_detail").val(),
                bobot : $(this).find(".bobot").val(),
            }

            detail.push(temp);
        })

        let params = {
            header : header,
            detail : detail,
        }

        // console.log(params);

        bootbox.confirm({
            title: '<i class="glyphicon glyphicon-question-sign"></i> Konfirmasi',
            message: 'Apakah Anda yakin ingin menyimpan Setting KPI ini?',
            buttons: {
                confirm: {
                    label: 'Ya',
                    className: 'btn-primary'
                },
                cancel: {
                    label: 'Batal',
                    className: 'btn-default'
                }
            },
            callback: function(result) {

                if (!result) {
                    return;
                }

                $.ajax({
                    url : 'hris/KpiKaryawan/saveSetting',
                    data : params,
                    type : 'POST',
                    dataType : 'json',
                    beforeSend : function(){
                        showLoading();
                    },
                    success : function(data){
                        hideLoading();

                        if(data.status == 1){
                            toastr.success(data.message);

                            setTimeout(function(){
                                window.location.href = 'hris/KpiKaryawan';
                            }, 1000);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error : function() {
                        hideLoading();
                        toastr.error('Terjadi kesalahan sistem.');
                    }
                });
            }
        });
    },

    setting_edit: (elm, e) => {

        let params = {
            id_data : $(elm).attr("id_data"),
        }

        $.ajax({
            url : 'hris/KpiKaryawan/settingEdit',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(resp){
                hideLoading(); 

                let dialog = bootbox.dialog({
                    title: '<b>Edit Setting KPI</b>',
                    message: resp,
                    size: 'xl',
                    backdrop: 'static',
                    onEscape: true,
                    buttons: {
                        close: {
                            label: 'Tutup',
                            className: 'btn-default'
                        }, 
                        save: {
                            label: 'Simpan',
                            className: 'btn-primary',
                            callback: function () {
                                kpi.exec_edit_setting();
                            }
                        }
                    }
                });

                dialog.on('shown.bs.modal', function () {
                    $(this).find('.select2').select2({
                        width: '100%',
                        dropdownParent: dialog
                    });
                });

                dialog.find('.modal-dialog').css({
                    'width': '100%'
                });

                kpi.config_bobot();
            },
        });

    },


    exec_edit_setting : () =>{

        let header = {
            nama : $("#setting_edit").find(".nama").val(),
            jabatan : $("#setting_edit").find(".jabatan").val(),
            keterangan : $("#setting_edit").find(".keterangan").val(),
            id_header : $("#setting_edit").find("#id_edit_setting").attr("id_data"),
        }

        let detail = [];

        $("#setting_edit").find(".row-input").each(function(){
            let temp = {
                index_kpi : $(this).find(".nama_kpi").val(),
                keterangan : $(this).find(".keterangan_detail").val(),
                bobot : $(this).find(".bobot").val(),
            }

            detail.push(temp);
        })

        let params = {
            header : header,
            detail : detail,
        }

        // console.log(params)

        bootbox.confirm({
            title: '<i class="glyphicon glyphicon-question-sign"></i> Konfirmasi',
            message: 'Apakah Anda yakin ingin menyimpan Setting KPI ini?',
            backdrop: 'static',
            buttons: {
                confirm: {
                    label: 'Ya',
                    className: 'btn-primary'
                },
                cancel: {
                    label: 'Batal',
                    className: 'btn-default'
                }
            },
            callback: function(result) {

                if (!result) {
                    return;
                }

                $.ajax({
                    url : 'hris/KpiKaryawan/execEditSetting',
                    data : params,
                    type : 'POST',
                    dataType : 'json',
                    beforeSend : function(){
                        showLoading();
                    },
                    success : function(data){
                        hideLoading();

                        if(data.status == 1){
                            toastr.success(data.message);

                            setTimeout(function(){
                                // window.location.href = 'hris/KpiKaryawan';
                                kpi.load_data_setting();
                            }, 1000);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error : function() {
                        hideLoading();
                        toastr.error('Terjadi kesalahan sistem.');
                    }
                });
            }
        });

    },

    setting_delete: (elm, e) => {

        let params = {
            id_data : $(elm).attr("id_data"),
        }

        bootbox.confirm({
            title: '<i class="glyphicon glyphicon-question-sign"></i> Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus Setting KPI ini?',
            backdrop: 'static',
            buttons: {
                confirm: {
                    label: 'Ya',
                    className: 'btn-primary'
                },
                cancel: {
                    label: 'Batal',
                    className: 'btn-default'
                }
            },
            callback: function(result) {

                if (!result) {
                    return;
                }

                $.ajax({
                    url : 'hris/KpiKaryawan/execDeleteSetting',
                    data : params,
                    type : 'POST',
                    dataType : 'json',
                    beforeSend : function(){
                        showLoading();
                    },
                    success : function(data){
                        hideLoading();

                        if(data.status == 1){
                            toastr.success(data.message);

                            setTimeout(function(){
                                window.location.href = 'hris/KpiKaryawan';
                            }, 1000);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error : function() {
                        hideLoading();
                        toastr.error('Terjadi kesalahan sistem.');
                    }
                });
            }
        });

    },

    get_data_periode : () => {

        let periode = $(".periode_kpi").val();

        $.ajax({
            url : 'hris/KpiKaryawan/getDataPeriode',
            data : {
                periode : periode,
            },
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){
                showLoading();
            },
            success : function(resp){
                hideLoading();

                $(".index_content").html(resp);
            },
            error : function() {
                hideLoading();
                toastr.error('Terjadi kesalahan sistem.');
            }
        });

    },

    keputusanKpi : (elm, e) => {

        let params = {
            id_data : $(elm).attr("id_data"),
            val : $(elm).attr("val"),
        }

         $.ajax({
            url : 'hris/KpiKaryawan/keputusanKpi',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){
                showLoading();
            },
            success : function(data){
                hideLoading();

                if(data.status == 1){
                    toastr.success(data.message);

                    setTimeout(function(){
                        window.location.href = 'hris/KpiKaryawan/approvalKpi';
                    }, 1000);
                } else {
                    toastr.error(data.message);
                }
            },
            error : function() {
                hideLoading();
                toastr.error('Terjadi kesalahan sistem.');
            }
        });
    },



    filter_report_by_periode: (elm, e) =>{

        let params = {
            bulan : $(elm).val(),
        }

        $.ajax({
            url : 'hris/KpiKaryawan/filterLaporanKpi',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading(); 

                
                $(".tbl-laporan-kpi").html(html);
            },
        });
    },
}

$(document).ready(function() {

    kpi.setting_up();

    kpi.load_data_approval();

    if ($("#setting_kpi").length) {
        kpi.load_data_setting();
    }

    if ($(".index_content").length) {
        kpi.get_data_periode();
    }

});
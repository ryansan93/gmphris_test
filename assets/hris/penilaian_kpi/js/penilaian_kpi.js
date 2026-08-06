let option_karyawan = [];

let penilaian = {
    
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

        if(params.nama_jabatan){

            $.ajax({
                url : 'hris/PenilaianKpi/loadDataBobot',
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
        }
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

            penilaian.config_data_penilaian(format(firstDay), format(lastDay), penilai);
        } else {
            // toastr.info("Periode masih kosong")
        }

        penilaian.loadDataBobot();

    },

    config_data_penilaian :(startdate, enddate, penilai) => {

        let params = {
            startdate : startdate,
            enddate : enddate,
            penilai : penilai,
        }

        $.ajax({
            url : 'hris/PenilaianKpi/configDataPenilaian',
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
            penilai     : $(".penilai").val(),
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
                kode_index : $(this).attr("kode_index"),
                // kode_kpi   : $(this).find("td:eq(0)").html().trim(),
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

        let config_tr = $(".list_bobot tbody .tr_loop").length;
        
        if (config_tr >= 1){

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
                        url : 'hris/PenilaianKpi/save',
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
                                    window.location.href = 'hris/PenilaianKpi/penilaianKpi';
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

        } else {
            toastr.info("Bobot periode tersebut tidak tersedia")
        }
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
            url : 'hris/PenilaianKpi/loadDataApproval',
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
            url : 'hris/PenilaianKpi/showPenilaian',
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
            url : 'hris/PenilaianKpi/loadDataSetting',
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


    load_data_penilaian: () => {

        $.ajax({
            url : 'hris/PenilaianKpi/loadPenilaianKpi',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading(); 

                $(".list_data_penilaian_kpi").html(html);
            },
        });

    },

    edit_penilaian : (elm ,e ) =>{

        let params = {
            id_penilaian : $(elm).attr('id_penilaian'),
            nik : $(elm).attr('nik'),
        }

        $.ajax({
            url : 'hris/PenilaianKpi/edit_penilaian',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading();

                let dialog = bootbox.dialog({
                    title: '<b>Edit Penilaian KPI</b>',
                    message: html,
                    size: 'large',
                    backdrop: true,
                    onEscape: true,
                    buttons: {
                        close: {
                            label: 'Tutup',
                            className: 'btn-secondary'
                        },
                        save: {
                            label: 'Simpan',
                            className: 'btn-primary btn-save-edit',
                            callback: function (){
                                penilaian.exec_edit_penilaian();
                                return false;
                            }
                        }
                    }
                });
                dialog.on('shown.bs.modal', function () {
                    dialog.find('.editselect2').select2({
                        width: '100%',
                        dropdownParent: dialog.find('.modal-content')
                    });

                    
                    $(".penilai").trigger("change");
                    setTimeout(function(){
                        $(".karyawan").val(params.nik).trigger("change");
                    }, 300)

                    $("#edit_penilaian .karyawan").on("select2:opening", function () {
                        $("#edit_penilaian .penilai").select2("close");
                    });

                    $("#edit_penilaian .penilai").on("select2:opening", function () {
                        $("#edit_penilaian .karyawan").select2("close");
                    });
                });
                
                dialog.on('hidden.bs.modal', function () {
                   
                    kpi.setting_up();

                });
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

        $(".kpi-area").find(".bobot").each(function () {
            totalBobot += parseFloat($(this).val()) || 0;
        });

        // console.log(totalBobot)

        if (totalBobot > 100) {
            let nilaiSaatIni = parseFloat($(elm).val()) || 0;
            let sisaBobot = 100 - (totalBobot - nilaiSaatIni);

            $(elm).val(sisaBobot > 0 ? sisaBobot : 0);

            toastr.warning("Total bobot tidak boleh lebih dari 100%");
        }

        totalBobot = 0;
        $(".kpi-area").find(".bobot").each(function () {
            totalBobot += parseFloat($(this).val()) || 0;
        });

        $(".add-row").prop("disabled", totalBobot >= 100);

        return true;
    },

    save_setting : () => {

        let header = {
            nama : $("#setting_kpi").find(".nama").val(),
            jabatan : $("#setting_kpi").find(".jabatan").val(),
            jabatan_nama : $("#setting_kpi").find(".jabatan").find("option:selected").text(),
            periode : $("#setting_kpi").find(".periode").val(),
            periode_nama : $("#setting_kpi").find(".periode").find("option:selected").text(),
            keterangan : $("#setting_kpi").find(".keterangan").val(),
        };

        if (!header.nama) {
            toastr.error('Nama template harus diisi.');
            return;
        }

        if (!header.jabatan) {
            toastr.error('Jabatan harus dipilih.');
            return;
        }

        if (!header.periode) {
            toastr.error('Periode '+ header.periode_nama + ' untuk jabatan ' + header.jabatan_nama +' sudah di setting');
            return;
        }

        let detail = [];
        let totalBobot = 0;
        let valid = true;

        $(".row-input").each(function(index){

            let namaKpi = $(this).find(".nama_kpi").val();
            let keterangan = $(this).find(".keterangan_detail").val();
            let bobot = parseFloat($(this).find(".bobot").val()) || 0;

            if (!namaKpi) {
                toastr.error(`Nama KPI pada baris ${index + 1} harus diisi.`);
                valid = false;
                return false;
            }

            if (bobot <= 0) {
                toastr.error(`Bobot pada baris ${index + 1} harus lebih dari 0.`);
                valid = false;
                return false;
            }

            totalBobot += bobot;

            detail.push({
                index_kpi : namaKpi,
                keterangan : keterangan,
                bobot : bobot
            });
        });

        if (!valid) {
            return;
        }

        if (detail.length === 0) {
            toastr.error('Minimal harus ada 1 KPI.');
            return;
        }

        if (totalBobot !== 100) {
            toastr.error(`Total bobot harus 100%. Saat ini ${totalBobot}%.`);
            return;
        }

        let params = {
            header : header,
            detail : detail,
        };

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
                    url : 'hris/PenilaianKpi/saveSetting',
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
                                window.location.href = 'hris/PenilaianKpi/settingKpi';
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
            url : 'hris/PenilaianKpi/settingEdit',
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
                kode_index : $(this).attr("kode_index"),
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
                    url : 'hris/PenilaianKpi/execEditSetting',
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

    filter_setting_kpi:(elm, e) => {

        let keyword = $(elm).val().toLowerCase();

        $('.list_data_setting_kpi tbody tr').filter(function() {
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(keyword) > -1
            );
        });

    },


    filter_approval_kpi:(elm, e) => {

        let keyword = $(elm).val().toLowerCase();

        $('.list_approval').filter(function() {
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(keyword) > -1
            );
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
                    url : 'hris/PenilaianKpi/execDeleteSetting',
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

                            // setTimeout(function(){
                                // window.location.href = 'hris/PenilaianKpi/';
                            // }, 1000);

                            kpi.load_data_setting();
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
            url : 'hris/PenilaianKpi/getDataPeriode',
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

                $('.btn-penilaian').attr('onclick',"window.location.href='hris/PenilaianKpi/penilaianKpi?periode=" + periode + "'");
                
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
            url : 'hris/PenilaianKpi/keputusanKpi',
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
                        window.location.href = 'hris/PenilaianKpi/approvalKpi';
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
            url : 'hris/PenilaianKpi/filterLaporanKpi',
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

    getKpiPeriode: () => {

        let params = {
            jabatan: $(".jabatan").val()
        };

        $.ajax({
            url: 'hris/PenilaianKpi/getKpiPeriode',
            data: params,
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                showLoading();
            },
            success: function(data) {
                hideLoading();

                let option = '<option value="">Pilih KPI</option>';

                $.each(data, function(i, v) {
                    if (v.jabatan_id == params.jabatan) {
                        option += `
                            <option value="${v.id}">
                                ${v.nama_template} - Periode ${v.periode}
                            </option>
                        `;
                    }
                });

                bootbox.dialog({
                    title: 'Pilih KPI',
                    message: `
                        <div class="form-group">
                            <label>KPI</label>
                            <select id="kpi_select" class="form-control" style="width:100%">
                                ${option}
                            </select>
                        </div>
                    `,
                    buttons: {
                        cancel: {
                            label: 'Batal',
                            className: 'btn-secondary'
                        },
                        confirm: {
                            label: 'Generate',
                            className: 'btn-primary',
                            callback: function() {
                                let id = $('#kpi_select').val();

                                if (!id) {
                                    bootbox.alert('Silakan pilih KPI');
                                    return false;
                                }

                                let selected = data.find(x => x.id == id);

                                let html = '';
                                selected.detail.forEach(item => {
                                    html += `
                                        <div class="row-input" style="display:flex; flex-direction:row; gap:10px; margin-bottom:5px;">
                                            <input 
                                                class="form form-control nama_kpi" 
                                                type="text" 
                                                value="${item.nama_kpi ?? ''}"
                                                placeholder="Masukan nama KPI">

                                            <input 
                                                class="form form-control keterangan_detail" 
                                                type="text" 
                                                value="${item.keterangan ?? ''}"
                                                placeholder="Masukan keterangan">

                                            <input 
                                                class="form form-control bobot"
                                                oninput="kpi.config_bobot(this, event)"
                                                type="number"
                                                style="width:25%"
                                                value="${Number(item.bobot)}"
                                                placeholder="Masukan bobot">

                                            <button class="btn btn-primary add-row" onclick="kpi.add_row_setting(this, event)">
                                                <i class="fa fa-plus"></i>
                                            </button>

                                            <button class="btn btn-danger" onclick="kpi.delete_row_setting(this, event)">
                                                <i class="fa fa-close"></i>
                                            </button>
                                        </div>
                                    `;
                                });

                                $(".detail-input").html(html);
                            }
                        }
                    },
                    onShown: function() {
                        $('#kpi_select').select2({
                            dropdownParent: $('.bootbox')
                        });
                    }
                });
            }
        });
    },

    periodeOutstanding: () => {

        $.ajax({
            url: 'hris/PenilaianKpi/getKpiPeriode',
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                showLoading();
            },
            success: function(data) {

                hideLoading();

                let used = data.map(item => `${item.periode}|${item.jabatan_id}`);

                console.log('used KPI:', used);

                $('.periode option').each(function() {

                    let periode = $(this).val();

                    let jabatan = $('.jabatan').val();

                    let key = `${periode}|${jabatan}`;

                    if (used.includes(key)) {
                        $(this).prop('disabled', true);
                    }
                });

                $('.periode').trigger('change');
            }
        });

    },


    detail_chart_periode: (elm, e) => {

        let detail = $(elm).closest("tr").find(".detail").html();

        let index = $(elm).attr("index");

        if (!detail || detail.trim() === '') {
            detail = '<p class="text-muted">Tidak ada data.</p>';
        }

        bootbox.dialog({
            title: 'Nama KPI : ' + index,
            message: detail,
            size: 'large',
            buttons: {
                tutup: {
                    label: '<i class="fa fa-close"></i> Tutup',
                    className: 'btn btn-secondary',
                    callback: function() {
                        bootbox.hideAll();
                    }
                },
            }
        });

    },

    ranking_by_periode: (elm, e) => {

        let params = {
            periode : $(elm).val(),
        }

        $.ajax({
            url : 'hris/PenilaianKpi/ranking_by_periode',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){
                showLoading();
            },
            success : function(resp){
                hideLoading();  
                    $(".list_ranking_kpi").find("tbody").html(resp);
            }
        });
    },


    view_detail_nilai: (elm, e) => {

        e.preventDefault();

        let params = {
            nik : $(elm).attr("nik"),
            bulan : $(".bulan").val(),
            total_score : $(elm).attr("total_score"),
            nama_karyawan  : $(elm).closest("tr").find("td:eq(01)").html(),
        }

        $.ajax({
            url : 'hris/PenilaianKpi/getRankingByPeriodeDetail',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){
                showLoading();
            },
            success : function(resp){
                hideLoading();
                bootbox.dialog({
                    title: '<b>Detail Nilai KPI</b>',
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
            },
            error : function() {
                hideLoading();
                toastr.error('Terjadi kesalahan sistem.');
            }
        });
    },


    cetakLaporanPdf: (elm, e) => {

        if (e) {
            e.preventDefault();
        }

        let params = {
            bulan: $(".bulan").val(),
        };

        window.open(
            'hris/PenilaianKpi/cetakLaporanPdf?bulan=' + encodeURIComponent(params.bulan),
            '_blank'
        );

    },

    importXlsPenilaian : (elm, e) =>{
        
        $.ajax({
            url : 'hris/PenilaianKpi/loadViewExportPenilaian',
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){
                showLoading();
            },
            success : function(resp){

                let dialog = bootbox.dialog({
                    title: 'Import Penilaian by Excel',
                    message: resp,
                    size: 'large',
                    buttons: {
                        tutup: {
                            label: '<i class="fa fa-close"></i> Tutup',
                            className: 'btn btn-secondary',
                            callback: function() {
                                bootbox.hideAll();
                            }
                        },
                        simpan: {
                            label: '<i class="fa fa-save"></i> Simpan',
                            className: 'btn-save-import btn btn-primary',
                            callback: function() {
                                bootbox.hideAll();

                                kpi.exec_save_import();
                            }
                        },
                    }
                })

                dialog.on('shown.bs.modal', function () {
                    $('body').css('overflow', 'hidden');


                    $(this).css('overflow-y', 'scroll');

                    $(".btn-save-import").prop("disabled", true);
                });

                dialog.on('hidden.bs.modal', function () {
                    $('body').css('overflow', '');
                    $('.select2').select2()
                });



            },
            error : function() {
                hideLoading();
                toastr.error('Terjadi kesalahan sistem.');
            }
        });

    },


    exec_import_excel_penilaian: (elm, e) => {

        e.preventDefault();

        

        const uploader = $('<input type="file" accept=".xls,.xlsx">');
        uploader.trigger("click");

        uploader.on("change", function () {

            $(".kpi-area").html(`
                <div class="d-flex justify-content-center align-items-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                    <span style="font-size:10px; margin-left:5px;">Memuat data...</span>
                </div>
            `);

            const file = this.files[0];

            if (!file) return;

            const ext = file.name.split(".").pop().toUpperCase();

            if (!["XLS", "XLSX"].includes(ext)) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: 'Import hanya boleh format ".xls" atau ".xlsx"'
                });
                return;
            }

            const reader = new FileReader();

            reader.onload = function (event) {

                const data = new Uint8Array(event.target.result);

                const workbook = XLSX.read(data, {
                    type: "array"
                });

                const sheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[sheetName];

                const rows = XLSX.utils.sheet_to_json(worksheet, {
                    header: 1
                });

                const data_rows = rows.slice(2).filter(row => row.length > 0);


                const data_mapping = data_rows.map(row => ({
                    nik: row[0],
                    kode: row[1],
                    nilai: row[4],
                    keterangan: row[5]
                }));

                // console.log(data_mapping);

                
                const nikKosong = data_mapping.find(item => !item.nik);
                if (nikKosong) {
                    toastr.info("Masih ada NIK yang kosong pada file Excel.");
                    return;
                }


                let params = [];
                let valid = true;

                const totalBobot = data_mapping.reduce((total, item) => {
                    return total + Number(item.bobot);
                }, 0);

                if (totalBobot > 100) {
                    toastr.info(`Total bobot tidak boleh lebih dari 100%. Saat ini: ${totalBobot}%`);
                    return;
                }

                // console.log(totalBobot);


                const dataTidakValid = data_mapping.find(item =>
                    item.nilai === "" ||
                    item.nilai === null ||
                    item.nilai === undefined ||
                    Number(item.nilai) > 100
                );

                if (dataTidakValid) {
                    toastr.info("Nilai tidak boleh kosong dan tidak boleh lebih dari 100.");
                    return;
                }

                params = data_mapping.map(item => ({
                    nik: item.nik,
                    kode: item.kode,
                    nilai: item.nilai,
                    keterangan: item.keterangan
                }));

                if (!valid) {
                    toastr.info("Masih ada nilai yang kosong!");
                    return;
                } else {

                    $.ajax({
                        url: 'hris/PenilaianKpi/exec_data_penilaian',
                        type: 'POST',
                        data: JSON.stringify(params),
                        contentType: 'application/json',
                        processData: false,
                        dataType: 'html',
                        beforeSend: function () {
                            showLoading();
                        },
                        success: function (resp) {
                            hideLoading();
                          
                            $(".kpi-area").html(resp)

                            $(".select2").select2();
                            
                            let bulan   = $(".kpi-area").find('.bulan').val();
                            let penilai = $(".kpi-area").find('.penilai').val() ?? null;
                            // console.log(bulan)
                            if (bulan) {
                                toastr.success('Import berhasil');
                                
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

                            }

                            let trloop = $(".tr_loop").length;

                            if (trloop > 0 ){
                                $(".btn-save-import").prop("disabled", false);
                            }


                            $('.select2').select2({
                                width: '100%',
                                dropdownParent: $(".bootbox"),
                            });

                        },
                        error: function () {
                            hideLoading();
                            toastr.error('Terjadi kesalahan sistem.');
                        }
                    });
                }

               

            };

            reader.readAsArrayBuffer(file);

        });

    },

    exec_save_import: () => {

        let header = {
            nik         : $(".kpi-area").find(".karyawan").find("option:selected").attr("nik_karyawan"),
            jabatan     : $(".kpi-area").find(".nama-jabatan").attr("kode_jabatan"),
            penilai     : $(".kpi-area").find(".penilai").val(),
            total_score : $(".kpi-area").find(".table").find(".total_score").val(),
            tgl_mulai   : $(".kpi-area").find(".tgl_mulai").val(),
            tgl_selesai : $(".kpi-area").find(".tgl_selesai").val(),
        };

        // console.log(header);
        // return false;

        let detail = [];
        let valid = true;
        let pesan = '';

        $(".kpi-area .table tbody .tr_loop").each(function(index){

            let nilai = $(this).find("td:eq(3) input").val();

            if ($.trim(nilai) == '') {
                valid = false;

                let nama_kpi = $(this).find("td:eq(1)").text().trim();

                pesan = 'Nilai KPI "' + nama_kpi + '" belum diisi.';
                return false;
            }

            let temp = {
                kode_index : $(this).attr("kode_index"),
                // kode_kpi   : $(this).find("td:eq(0)").html().trim(),
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

        // console.log(params);
        // return false;

        let config_tr = $(".kpi-area .table tbody .tr_loop").length;
        
        if (config_tr >= 1){

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
                        url : 'hris/PenilaianKpi/save',
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
                                    // window.location.href = 'hris/PenilaianKpi/PenilaianKpi';
                                    kpi.load_data_penilaian();

                                    $('a[data-tab="list_data"]').trigger('click');
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

        } else {
            toastr.info("Bobot periode tersebut tidak tersedia")
        }
    },


    downloadTemplatePenilaian: (elm, e) => {

         $.ajax({
            url: 'hris/PenilaianKpi/downloadTemplatePenilaian',
            type: 'POST',
            dataType: 'html',
            beforeSend: function () {
                showLoading();
            },
            success: function (resp) {
                hideLoading();

                let dialog = bootbox.dialog({
                    title: 'Download Template Penilaian',
                    message: resp,
                    size: 'medium',
                    buttons: {
                        tutup: {
                            label: '<i class="fa fa-close"></i> Tutup',
                            className: 'btn btn-secondary',
                            callback: function() {
                                bootbox.hideAll();
                            }
                        },
                        download: {
                            label: '<i class="fa fa-download"></i> Download',
                            className: 'btn-save-import btn btn-primary',
                            callback: function() {
                                

                                let params = {
                                    periode : dialog.find(".periode").val(),
                                    jabatan : dialog.find(".jabatan").val(),
                                    karyawan : dialog.find(".karyawan").val(),
                                }

                                if (!params.periode) {
                                    toastr.warning("Silakan pilih periode.");
                                    dialog.find(".periode").focus();
                                    return;
                                }

                                if (!params.jabatan) {
                                    toastr.warning("Silakan pilih jabatan.");
                                    dialog.find(".jabatan").focus();
                                    return;
                                }


                                kpi.execDownloadTemplatePenilaian(params);
                            }
                        },
                    }
                })

                dialog.on('shown.bs.modal', function () {
                    $(this).find('.select2').select2({
                        width: '100%',
                        dropdownParent: dialog
                    });
                });
                
            },
            error: function () {
                hideLoading();
                toastr.error('Terjadi kesalahan sistem.');
            }
        });

    },

    downloadTemplateSetting: (elm, e) => {

         $.ajax({
            url: 'hris/PenilaianKpi/downloadTemplateSetting',
            type: 'POST',
            dataType: 'html',
            beforeSend: function () {
                showLoading();
            },
            success: function (resp) {
                hideLoading();

                let dialog = bootbox.dialog({
                    title: 'Download Template Setting',
                    message: resp,
                    size: 'medium',
                    buttons: {
                        tutup: {
                            label: '<i class="fa fa-close"></i> Tutup',
                            className: 'btn btn-secondary',
                            callback: function() {
                                bootbox.hideAll();
                            }
                        },
                        download: {
                            label: '<i class="fa fa-download"></i> Download',
                            className: 'btn-save-import btn btn-primary',
                            callback: function() {
                                

                                let params = {
                                    periode : dialog.find(".periode").val(),
                                    jabatan : dialog.find(".jabatan").val(),
                                }

                                if (!params.periode) {
                                    toastr.warning("Silakan pilih periode.");
                                    dialog.find(".periode").focus();
                                    return;
                                }

                                if (!params.jabatan) {
                                    toastr.warning("Silakan pilih jabatan.");
                                    dialog.find(".jabatan").focus();
                                    return;
                                }


                                kpi.execDownloadTemplateSetting(params);
                            }
                        },
                    }
                })

                dialog.on('shown.bs.modal', function () {
                    $(this).find('.select2').select2({
                        width: '100%',
                        dropdownParent: dialog
                    });
                });
                
            },
            error: function () {
                hideLoading();
                toastr.error('Terjadi kesalahan sistem.');
            }
        });

    },

    selectImportKaryawan: (elm, e) => {

        let params = {
            nik: $(elm).val(),
            jabatan: $(elm).find("option:selected").attr("jabatan"),
        };

        let $jabatan = $("#filter_import").find(".jabatan");

        const exist = $jabatan.find(`option[value="${params.jabatan}"]`).length > 0;

        if (exist) {
            $jabatan.val(params.jabatan).prop("disabled", true).trigger("change");
            $(".btn-save-import").prop("disabled", false);
        } else {
            $jabatan.prop("selectedIndex", 0).prop("disabled", true).trigger("change");
            $(".btn-save-import").prop("disabled", true);
        }

        $.ajax({
            url: 'hris/PenilaianKpi/configDataPeriodeImport',
            data: params,
            type: 'POST',
            dataType: 'json',
            success: function(data) {

               const $periode = $(".periode");

                if (!$periode.data("default-options")) {
                    $periode.data("default-options", $periode.html());
                }

                $periode.html($periode.data("default-options"));

                const bulanAda = new Set();

                Object.values(data).forEach(items => {
                    items.forEach(item => {
                        const bulan = new Date(item.tanggal_mulai).getMonth() + 1;
                        bulanAda.add(bulan);
                    });
                });

                if (bulanAda.size === 0) {
                    $periode.prop("selectedIndex", 0).trigger("change");
                    return;
                }

                $periode.find("option").each(function () {
                    const value = parseInt($(this).val());

                    if (bulanAda.has(value)) {
                        $(this).remove();
                    }
                });

                $periode.prop("selectedIndex", 0).trigger("change");

            }
        });
    },

    execDownloadTemplatePenilaian: (params) => {
        bootbox.hideAll();

        
        $.ajax({
            url: 'hris/PenilaianKpi/checkDataTemplatePenilaian',
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    window.location.href = 'hris/PenilaianKpi/execDownloadTemplatePenilaian?' + $.param(params);
                } else {
                    toastr.info(res.message);
                }
            }
        });
        
        // window.location.href = 'hris/PenilaianKpi/execDownloadTemplate?periode=' + encodeURIComponent(params.periode) + '&jabatan=' + encodeURIComponent(params.jabatan);
    },

    execDownloadTemplateSetting: (params) => {
        bootbox.hideAll();

        // console.log(params);
        // return false;

        
        // $.ajax({
        //     url: 'hris/PenilaianKpi/checkDataTemplateSetting',
        //     type: 'GET',
        //     data: params,
        //     dataType: 'json',
        //     success: function(res) {
        //         if (res.status) {
                    window.location.href = 'hris/PenilaianKpi/execDownloadTemplateSetting?' + $.param(params);
        //         } else {
        //             toastr.info(res.message);
        //         }
        //     }
        // });
        
        // window.location.href = 'hris/PenilaianKpi/execDownloadTemplate?periode=' + encodeURIComponent(params.periode) + '&jabatan=' + encodeURIComponent(params.jabatan);
    },



    importXlsKpiSetting : () => {

        $.ajax({
            url : 'hris/PenilaianKpi/loadViewExportSettingKpi',
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){
                showLoading();
            },
            success : function(resp){

                let dialog = bootbox.dialog({
                    title: 'Import Setting KPI by Excel',
                    message: resp,
                    size: 'large',
                    buttons: {
                        tutup: {
                            label: '<i class="fa fa-close"></i> Tutup',
                            className: 'btn btn-secondary',
                            callback: function() {
                                bootbox.hideAll();
                            }
                        },
                        simpan: {
                            label: '<i class="fa fa-save"></i> Simpan',
                            className: 'btn-save-import btn btn-primary',
                            callback: function() {
                                bootbox.hideAll();
                                
                                kpi.save_import_excel_setting();

                                
                            }
                        },
                    }
                })

                dialog.on('shown.bs.modal', function () {

                    $(this).find('.select2').select2({
                        width: '100%',
                        dropdownParent: dialog
                    });

                    $('body').css('overflow', 'hidden');
                });

                dialog.on('hidden.bs.modal', function () {
                    $('body').css('overflow', '');
                });

                dialog.on('shown.bs.modal', function () {
                    $(this).css('overflow-y', 'scroll');

                    $(".btn-save-import").prop("disabled", true)
                });

            },
            error : function() {
                hideLoading();
                toastr.error('Terjadi kesalahan sistem.');
            }
        });

    },

    exec_import_excel_setting: (elm, e) => {

        e.preventDefault();

        const uploader = $('<input type="file" accept=".xls,.xlsx">');
        uploader.trigger("click");

        uploader.on("change", function () {

            const file = this.files[0];

            if (!file) return;

            const ext = file.name.split(".").pop().toUpperCase();

            if (!["XLS", "XLSX"].includes(ext)) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: 'Import hanya boleh format ".xls" atau ".xlsx"'
                });
                return;
            }

            const reader = new FileReader();

            reader.onload = function (event) {

                const data = new Uint8Array(event.target.result);

                const workbook = XLSX.read(data, {
                    type: "array"
                });

                const sheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[sheetName];

                const rows = XLSX.utils.sheet_to_json(worksheet, {
                    header: 1
                });

                // console.log(rows);
                // const header = [
                //     worksheet["A1"]?.v,
                //     worksheet["A3"]?.v,
                //     worksheet["B3"]?.v,
                //     worksheet["C3"]?.v
                // ];
                // console.log(header);

                const header = {
                    nama_template: 'KPI ' + worksheet["A8"]?.v + ' Periode ' + worksheet["B8"]?.v,
                    jabatan: worksheet["A8"]?.v,
                    periode: worksheet["B8"]?.v,
                    keterangan: worksheet["C8"]?.v
                };

                const detail = rows.slice(10)
                    .filter(row => row[0]) 
                    .map(row => ({
                        nama_kpi: row[0],
                        bobot: row[1],
                        keterangan: row[2]
                    }));

                

                const totalBobot = detail.reduce((total, item) => {
                    return total + Number(item.bobot);
                }, 0);

                if (totalBobot > 100) {
                    toastr.info(`Total bobot tidak boleh lebih dari 100%. Total saat ini: ${totalBobot}%`);
                    return;
                }
                        


                const bulan = {
                    JANUARI: "1",
                    FEBRUARI: "2",
                    MARET: "3",
                    APRIL: "4",
                    MEI: "5",
                    JUNI: "6",
                    JULI: "7",
                    AGUSTUS: "8",
                    SEPTEMBER: "9",
                    OKTOBER: "10",
                    NOVEMBER: "11",
                    DESEMBER: "12"
                };

                if(detail.length > 0){

                    $.ajax({
                        url : 'hris/PenilaianKpi/checkDataSetting',
                        data : {
                            periode : bulan[header.periode],
                            jabatan : header.jabatan
                        },
                        type : 'POST',
                        dataType : 'json',
                        beforeSend : function(){
                            showLoading();
                        },
                        success : function(data){
                            hideLoading();
    
                            if(data.status == 0){
    
                                $(".kpi-area").find(".nama").val(header.nama_template);
                                $(".kpi-area").find(".jabatan").val((header.jabatan || "").toLowerCase()).trigger("change");
                                $(".kpi-area").find(".periode").val(bulan[(header.periode || "").toUpperCase()]).trigger("change");
                                $(".kpi-area").find(".keterangan").val(header.keterangan);
    
                                let loop = [];
    
                                // Header
                                loop.push(`
                                    <div class="row mb-2 fw-bold">
                                        <div class="col-5">Nama KPI</div>
                                        <div class="col-5">Keterangan</div>
                                        <div class="col-2">Bobot (%)</div>
                                    </div>
                                `);
    
                                detail.forEach(function(item) {
                                    loop.push(`
                                        <div class="row mb-3 align-items-center row-input">
                                            <div class="col-5">
                                                <input class="form-control nama_kpi" type="text" value="${item.nama_kpi}">
                                            </div>
    
                                            <div class="col-5">
                                                <input class="form-control keterangan_detail" type="text" value="${item.keterangan ?? ''}">
                                            </div>
    
                                            <div class="col-2">
                                                <input class="form-control bobot text-right" type="number" value="${item.bobot}" oninput="kpi.config_bobot(this, event)">
                                            </div>
                                        </div>
                                    `);
                                });
    
                                $(".detail-input").html(loop.join(""));
    
                                toastr.success("Import berhasil");
                                $(".btn-save-import").prop("disabled", false);

                               
                            } else {
                                toastr.error(data.message);
                                $(".btn-save-import").prop("disabled", true);
                            }
                        },
                        error : function() {
                            hideLoading();
                            toastr.error('Terjadi kesalahan sistem.');
                        }
                    });
                } else {
                    toastr.info("KPI Kosong")
                }


            };

            reader.readAsArrayBuffer(file);

        });

    },

    save_import_excel_setting: () =>{

        let header = {
            nama : $(".kpi-area").find(".nama").val(),
            jabatan : $(".kpi-area").find(".jabatan").val(),
            periode : $(".kpi-area").find(".periode").val(),
            keterangan : $(".kpi-area").find(".keterangan").val(),
        };

        if (!header.nama) {
            toastr.error('Nama template harus diisi.');
            return;
        }

        if (!header.jabatan) {
            toastr.error('Jabatan harus dipilih.');
            return;
        }

        if (!header.periode) {
            toastr.error('Periode harus dipilih.');
            return;
        }

        let detail = [];
        let totalBobot = 0;
        let valid = true;

        $(".kpi-area .row-input").each(function(index){

            let namaKpi = $(this).find(".nama_kpi").val();
            let keterangan = $(this).find(".keterangan_detail").val();
            let bobot = parseFloat($(this).find(".bobot").val()) || 0;

            if (!namaKpi) {
                toastr.error(`Nama KPI pada baris ${index + 1} harus diisi.`);
                valid = false;
                return false;
            }

            if (bobot <= 0) {
                toastr.error(`Bobot pada baris ${index + 1} harus lebih dari 0.`);
                valid = false;
                return false;
            }

            totalBobot += bobot;

            detail.push({
                index_kpi : namaKpi,
                keterangan : keterangan,
                bobot : bobot
            });
        });

        if (!valid) {
            return;
        }

        if (detail.length === 0) {
            toastr.error('Minimal harus ada 1 KPI.');
            return;
        }

        if (totalBobot !== 100) {
            toastr.error(`Total bobot harus 100%. Saat ini ${totalBobot}%.`);
            return;
        }

        let params = {
            header : header,
            detail : detail,
        };

        // console.log(params);
        // return false;

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
                    url : 'hris/PenilaianKpi/saveSetting',
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
                                window.location.href = 'hris/PenilaianKpi/settingKpi';
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

    edit_get_karyawan_by_penilai: (elm, e) => {

        let params = {
            penilai : $(elm).val(),
            jabatan : $("#edit_penilaian").attr("jabatan_real"),
        }

        $.ajax({
            url : 'hris/PenilaianKpi/configGetKaryawanByPenilai',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success: function(data){
                hideLoading();

                $(".select-edit-karyawan").html(data);

                $('.karyawan').select2();
                $('.penilai').select2();


                if ($(".karyawan").find("option").length <= 2) {
                    $(".btn-save-edit").prop("disabled", true);
                    $("#table_edit_penilaian").hide();

                    toastr.info("Tidak ada karyawan untuk penilai dan jabatan tersebut.");
                } else {
                    $(".btn-save-edit").prop("disabled", false);
                    $("#table_edit_penilaian").show();
                }

                $(".bootbox").css({
                    "overflow-y": "auto",
                    "overflow-x": "hidden"
                });
                
            },
        });

    },

    exec_edit_penilaian: () =>{

        let header = {
            id_data     : $("#edit_penilaian").attr("id_data"),
            nik         : $("#edit_penilaian").find(".karyawan").val(),
            penilai     : $("#edit_penilaian").find(".penilai").val(),
            jabatan     : $("#edit_penilaian").find(".karyawan").find("option:selected").attr("jabatan"),
            total_score : $("#edit_penilaian").find("#table_edit_penilaian").find(".total_score").val(),
            bulan       : $("#edit_penilaian").find(".bulan").val(),
        };

        if (!header.nik) {
            toastr.info("Karyawan belum dipilih.");
            $("#edit_penilaian").find(".karyawan").select2("open");
            return;
        }
   

        let detail = [];
        let valid = true;
        let pesan = '';

        $("#table_edit_penilaian tbody .tr_loop").each(function(index){

            let nilai = $(this).find("td:eq(3) input").val();

            if ($.trim(nilai) == '') {
                valid = false;

                let nama_kpi = $(this).find("td:eq(1)").text().trim();

                pesan = 'Nilai KPI "' + nama_kpi + '" belum diisi.';
                return false;
            }

            let temp = {
                kode_index : $(this).attr("kode_index"),
                // kode_kpi   : $(this).find("td:eq(0)").html().trim(),
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

        // console.log(params);
        // return false;

        let config_tr = $("#table_edit_penilaian tbody .tr_loop").length;
        
        if (config_tr >= 1){

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
                        url : 'hris/PenilaianKpi/exec_edit_penilaian',
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
                                $(".bootbox").modal("hide");

                                setTimeout(function(){
                                    penilaian.loadDataBobot();
                                    // window.location.href = 'hris/PenilaianKpi/penilaianKpi';
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

        } else {
            toastr.info("Bobot periode tersebut tidak tersedia")
        }

    },

    delete_penilaian: (elm, e) => {

        let params = {
            id_data : $(elm).attr("id_penilaian"),
        }

        bootbox.confirm({
            title: "Konfirmasi",
            message: "Yakin ingin menghapus data penilaian KPI ini?",
            buttons: {
                confirm: {
                    label: "Ya",
                    className: "btn-danger"
                },
                cancel: {
                    label: "Batal",
                    className: "btn-secondary"
                }
            },
            callback: function (result) {

                if (result) {

                    $.ajax({
                        url: 'hris/PenilaianKpi/delete_penilaian',
                        type: 'POST',
                        dataType: 'JSON',
                        data: params,
                        beforeSend: function () {
                            showLoading();
                        },
                        success: function (data) {

                            hideLoading();

                            bootbox.alert(data.message, function () {
                                if (data.status == 1) {
                                //    window.location.reload();

                                kpi.load_data_penilaian();
                                }
                            });

                        },
                        error: function (xhr) {

                            hideLoading();

                            bootbox.alert('Terjadi kesalahan pada server.');

                            console.log(xhr.responseText);
                        }
                    });

                }

            }
        });

    },


    edit_checkKaryawanPeriode: (elm, e) => {

        let params = {
            bulan           : $("#edit_penilaian").find(".bulan").val(),
            bulan_lama      : $("#edit_penilaian").attr("periode_lama"),      
            nik_lama        : $("#edit_penilaian").attr("nik_lama"),
            jabatan         : $("#edit_penilaian").attr("jabatan_real"),
            nik_karyawan    : $("#edit_penilaian").find(".karyawan").val(),
        }

        // console.log(params)
        if (params.nik_lama != params.nik_karyawan || params.bulan_lama != params.bulan){
            
            $.ajax({
                url: 'hris/PenilaianKpi/edit_checkKaryawanPeriode',
                type: 'POST',
                dataType: 'JSON',
                data: params,
                beforeSend: function () {
                    showLoading();
                },
                success: function (data) {
    
                    hideLoading();

                    if ( data.status == 0 ){
                        toastr.info(data.message)
                        $(".btn-save-edit").prop("disabled", true);
                        $("#table_edit_penilaian").css("display", "none");
                    } else {
                        $("#table_edit_penilaian").css("display", "block");
                        $(".btn-save-edit").prop("disabled", false);
                    }
    
                },
                error: function (xhr) {
    
                    hideLoading();
    
                    bootbox.alert('Terjadi kesalahan pada server.');
    
                    console.log(xhr.responseText);
                }
            });

        }  else {
            $(".btn-save-edit").prop("disabled", false);
            $("#table_edit_penilaian").css("display", "block");
        }

        

    },
}

$(document).ready(function() {

    penilaian.setting_up();

    if ($("#penilaian_id").length) {
        $(".bulan").trigger("change");
        penilaian.load_data_penilaian();
        penilaian.setting_up();
    }

    if( $("#edit_penilaian").length ){
        $(".penilai").trigger("change");
        // console.log(123)
    }
});


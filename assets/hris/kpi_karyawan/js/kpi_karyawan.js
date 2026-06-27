let kpi = {
    
    setting_up: () => {
        $(".select2").select2();

    },

    loadCharts: (elm, e) => {

        e.preventDefault();

        let opt = $(elm).find("option:selected");

        let rawLabel = opt.attr('label');  
        let rawNilai = opt.attr('nilai');    

        if (!rawLabel || !rawNilai) return;

        let label = rawLabel.split(',').map(v => v.replace(/['"]/g, '').trim());
        let nilai = rawNilai.split(',').map(v => parseFloat(v.trim()));

        const ctx = document.getElementById('kpiChart');

        if (window.kpiChartInstance) {
            window.kpiChartInstance.destroy();
        }

        window.kpiChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: label,
                datasets: [{
                    label: 'Total Nilai KPI - ' + opt.val(),
                    data: nilai,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Target KPI',
                    data: Array(label.length).fill(80),
                    borderColor: '#ef4444',
                    borderDash: [5, 5],
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: false
                }]
            }
        });
    },

    loadChartsPeriode: (elm, e) => {
        let params = {
            bulan : $(".periode-chart").val(),
            jabatan : $(".jabatan-chart").val(),
        }

        $.ajax({
            url : 'hris/KpiKaryawan/loadChartsPeriode',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading();
                $("#periodeChart").html(html);
            },
        });
      
        
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

                $('.btn-penilaian').attr('onclick',"window.location.href='hris/KpiKaryawan/penilaianKpi?periode=" + periode + "'");
                
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

    getKpiPeriode: () => {
        $.ajax({
            url: 'hris/KpiKaryawan/getKpiPeriode',
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                showLoading();
            },
            success: function(data) {
                hideLoading();

                let option = '<option value="">Pilih KPI</option>';

                $.each(data, function(i, v) {
                    option += `
                        <option value="${v.id}">
                            ${v.nama_template} - Periode ${v.periode}
                        </option>
                    `;
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
            url: 'hris/KpiKaryawan/getKpiPeriode',
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
            title: index,
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
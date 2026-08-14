$(document).ready(function(){
    // pc.loadList();
    // pc.loadForm();
    pc.settingUp();

    let nik_login = $('input[name="nik_login"]').val();

    if(nik_login){
        console.log("NIK Login : " + nik_login);
        $("#select_nik").val(nik_login).trigger("change");
    }

    
});

let pc= {
    attachmentDT: new DataTransfer(),

    settingUp: () => {
        if (typeof base_controller === 'undefined'){
            var $bc = $('#base_controller');
            if ($bc.length){
                window.base_controller = $bc.val();
            }
        }
        $(".select2").select2();
        // when nik changes, re-run date duplicate check
        $(document).off('change', 'select[name="nik"], input[name="nik"]').on('change', 'select[name="nik"], input[name="nik"]', function(){
            try{ pc.checkTanggalPengajuan(); }catch(e){}
        });

        if ($('.date').length && typeof $.fn.datetimepicker === 'function'){

            $('.date').datetimepicker({
                locale: 'id',
                format: 'DD MMM YYYY',
                useCurrent: false
            });


            $('#tanggal_mulai_edit')
                .off('dp.change')
                .on('dp.change', function(e){
                    // console.log('EDIT CHANGE');
                    pc.checkTanggalPengajuanEdit(this, e);
                });

            $('#tanggal_selesai_edit')
                .off('dp.change')
                .on('dp.change', function(e){
                    // console.log('EDIT CHANGE');
                    pc.checkJumlahLibur();
                });

            $('.date').datetimepicker({
                locale: 'id',
                format: 'DD MMM YYYY',
                useCurrent: false
            });
            // when start changes, set min date for end
            $('#tanggal_mulai').on('dp.change', function(e){
                if ($('#tanggal_selesai').data('DateTimePicker')){
                    $('#tanggal_selesai').data('DateTimePicker').minDate(e.date);
                }
                // when start date changes, run duplicate check and recalc total days
                try { pc.checkTanggalPengajuan(); } catch (ex) { /* ignore */ }
                try { pc.checkJumlahLibur(); } catch (ex) { /* ignore */ }
            });
            $('#tanggal_selesai').on('dp.change', function(e){
                try { pc.checkJumlahLibur(); } catch (ex) { /* ignore */ }
            });
            // set initial minDate for tanggal_mulai depending on jenis_cuti
            var initJenis = $('select[name="jenis_cuti"]').val();
            if ($('#tanggal_mulai').data('DateTimePicker')){
                if (initJenis === 'cuti_sakit' || initJenis === 'cuti_force_majeure'){
                    $('#tanggal_mulai').data('DateTimePicker').minDate(false);
                } else {
                    $('#tanggal_mulai').data('DateTimePicker').minDate(moment().startOf('day'));
                }
            }
            // when jenis_cuti changes, adjust minDate and enforce current value
           $(document).off('change', 'select[name="jenis_cuti"]').on('change', 'select[name="jenis_cuti"]', function(){
                var jenis = $(this).val();
                var $mulai = $('#tanggal_mulai');
                var $selesai = $('#tanggal_selesai');
                if ($mulai.data('DateTimePicker')){
                    if (jenis === 'cuti_sakit' || jenis === 'cuti_force_majeure'){
                        $mulai.data('DateTimePicker').minDate(false);
                    } else {
                        var today = moment().locale('id').startOf('day');
                        $mulai.data('DateTimePicker').minDate(today);
                        var cur = $mulai.val();
                        if (cur && typeof moment === 'function'){
                            var m = moment(cur, [
                                'DD MMM YYYY',
                                'DD MM YYYY'
                            ], 'id');
                            if (m.isValid() && m.isBefore(today)){
                                // set tanggal mulai
                                $mulai.data('DateTimePicker').date(today);
                                $mulai.val(today.format('DD MMM YYYY'));


                                // set tanggal selesai ikut hari ini
                                if ($selesai.data('DateTimePicker')) {
                                    $selesai.data('DateTimePicker').date(today);
                                }

                                $selesai.val(today.format('DD MMM YYYY'));
                            }
                        }
                    }
                }

                pc.checkTanggalPengajuan();
                pc.checkJumlahLibur();

            });
        }
        // open datepicker when clicking calendar icon inside input-group
        $(document).off('click', '.input-group .input-group-text').on('click', '.input-group .input-group-text', function(){
            var $input = $(this).closest('.input-group').find('.date');
            if ($input.length) {
                $input.focus();
                if ($input.data('DateTimePicker')) {
                    $input.data('DateTimePicker').show();
                }
            }
        });
        // delegate change handler for attachment inputs
        $(document).off('change', 'input[name="attachment[]"]').on('change', 'input[name="attachment[]"]', function(e){
            // files just selected by the user
            var selectedFiles = Array.from(e.target.files || []);
            handleAttachmentInput(selectedFiles, this);
            // preview only the newly selected files (not all accumulated attachments)
            previewAttachments(selectedFiles);
        });
        // delegate click handler to mark existing attachments for deletion (defer actual delete to save)
        $(document).off('click', '.btn-delete-existing').on('click', '.btn-delete-existing', function(e){
            e.preventDefault();
            var $btn = $(this);
            var id = $btn.data('id');
            if (!id) return;
            bootbox.confirm({
                message: 'Hapus attachment ini? (akan dihapus saat Simpan)',
                buttons: {
                    confirm: { label: 'Hapus', className: 'btn-danger' },
                    cancel: { label: 'Batal', className: 'btn-secondary' }
                },
                callback: function(result){
                    if (!result) return;
                    // add hidden input to form to indicate this attachment should be deleted on server-side upon save
                    var $form = $('#form-pengajuan-cuti');
                    if ($form.length) {
                        var $hidden = $('<input>').attr({type: 'hidden', name: 'deleted_attachment_ids[]', value: id});
                        $form.append($hidden);
                    }
                    // remove from UI immediately
                    $btn.closest('.existing-attachment').remove();
                }
            });
        });
    },

    // expose preview function if needed elsewhere
    previewAttachments: (files) => previewAttachments(files),

    loadList:() => {
        $.ajax({
            url: base_controller + '/load_form',
            method: 'GET',
            success: function(r){
      
                if ($('#table-pengajuan').length) {
                    $('#table-pengajuan tbody').html(r);
                } else {
                    $('#list-pengajuan').html(r);
                }
            }
        });
    },
    
    loadForm:() => {
        $.ajax({
            url: base_controller + '/edit_data',
            method: 'POST',
            data: { load_empty: 1 },
            success: function(r){
                $('#form-pengajuan').html(r);
                $(".select2").select2()
                pc.settingUp();

                
            }
        });
    },
    
    savePengajuan : ()=> {
   
        var form = $('#form-pengajuan-cuti')[0];

        function normalizeDateInputVal(val){
            if (!val) return val;
            var bulan = {
                Jan: '01',
                Feb: '02',
                Mar: '03',
                Apr: '04',
                Mei: '05',
                May: '05',
                Jun: '06',
                Jul: '07',
                Ags: '08',
                Agu: '08',
                Aug: '08',
                Sep: '09',
                Okt: '10',
                Oct: '10',
                Nov: '11',
                Des: '12',
                Dec: '12'
            };
            var arr = val.trim().split(' ');
            if (arr.length === 3) {
                var day = arr[0];
                var month = arr[1];
                var year = arr[2];
                if (bulan[month]) {
                    return day + ' ' + bulan[month] + ' ' + year;
                }
                if (!isNaN(month)) {
                    return val;
                }
            }
            return val;
        }

        var $mulai  = $('#tanggal_mulai').length ? $('#tanggal_mulai') : $('#tanggal_mulai_edit');
        var $selesai = $('#tanggal_selesai').length ? $('#tanggal_selesai') : $('#tanggal_selesai_edit');
        var oldMulai = $mulai.val();
        var oldSelesai = $selesai.val();
        $mulai.val(normalizeDateInputVal(oldMulai));
        $selesai.val(normalizeDateInputVal(oldSelesai));

        var formData = new FormData(form);

        // restore visible values (with month name)
        $mulai.val(oldMulai);
        $selesai.val(oldSelesai);
        // detect if editing (has id)
        var idField = $('#form-pengajuan-cuti').find('input[name="id"]');
        var url = base_controller + '/save';
        if (idField.length && idField.val() !== '') {
            url = base_controller + '/update';
        }

        // validate dates: selesai >= mulai
        var tglMulai = $('#tanggal_mulai').val();
        var tglSelesai = $('#tanggal_selesai').val();
        if (tglMulai && tglSelesai) {
            if (typeof moment === 'function') {
                if (moment(tglSelesai, 'DD MM YYYY').isBefore(moment(tglMulai, 'DD MM YYYY'))) {
                    bootbox.alert('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai');
                    return;
                }
            } else {
                var mulaiParts = tglMulai.split(' ');
                var selesaiParts = tglSelesai.split(' ');
                if (mulaiParts.length === 3 && selesaiParts.length === 3) {
                    var mulaiDate = new Date(mulaiParts[2], parseInt(mulaiParts[1], 10) - 1, mulaiParts[0]);
                    var selesaiDate = new Date(selesaiParts[2], parseInt(selesaiParts[1], 10) - 1, selesaiParts[0]);
                    if (selesaiDate < mulaiDate) {
                        bootbox.alert('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai');
                        return;
                    }
                }
            }
        }

        var jenis = $('select[name="jenis_cuti"]').val();
        // validate attachments when jenis is Cuti Sakit or Force Majeure
        var existingCount = $('#attachment-existing .existing-attachment').length;
        var newFilesCount = (pc.attachmentDT && pc.attachmentDT.files) ? pc.attachmentDT.files.length : 0;
        if ((jenis === 'cuti_sakit' || jenis === 'cuti_force_majeure' || jenis === 'cuti_jatah_liburan') && (existingCount + newFilesCount) === 0) {
            bootbox.alert('Attachment wajib diunggah untuk Cuti Sakit, Cuti Jatah Liburan dan Cuti Force Majeure.');
            return;
        }

        if (!$mulai.val() || $mulai.val().trim() === '') {
            bootbox.alert('Tanggal mulai wajib diisi');
            return false;
        }

        if (!$selesai.val() || $selesai.val().trim() === '') {
            bootbox.alert('Tanggal selesai wajib diisi');
            return false;
        }
                

        var nik = $('select[name="nik"]').val();
        if (!nik || nik.trim() === '') {
            bootbox.alert('NIK wajib diisi');
            return false;
        }

        var ket = $('textarea[name="alasan"]').val().trim();
        if (ket === '' || /^-+$/.test(ket)) {
            bootbox.alert('Keterangan cuti wajib di isi, tidak boleh strip / spasi');
            return false;
        }


        let change_tanggal = $(".check_tanggal").attr("tanggal");
        let today = new Date().toISOString().split("T")[0];
        let html = `Apakah kamu yakin akan menyimpan data ini?`;

        // console.log(change_tanggal, today);

        if(change_tanggal < today){
            html += `
            <div style="margin-top:15px; padding:15px; background:#fff8e1; border:1px solid #ffe082; border-radius:8px; text-align:left;">
                <div style=" display:flex; align-items:center; gap:8px; margin-bottom:10px; font-weight:600; color:#8a6d1d; ">
                    <i class="fa fa-exclamation-triangle"></i> Perubahan Tanggal Sebelumnya
                </div>

                <div style=" font-size:13px; color:#555; margin-bottom:10px;">
                    Silakan isi alasan perubahan sebelum melanjutkan
                </div>

                <textarea 
                    class="form-control alasan_perubahan"
                    rows="3"
                    placeholder="Masukkan alasan perubahan..."
                ></textarea>
            </div>`;
        }


        bootbox.confirm({
            message: html,
            buttons: {
                confirm: { 
                    label: 'Ok', 
                    className: 'btn-primary' 
                },
                cancel: { 
                    label: 'Batal', 
                    className: 'btn-secondary' 
                }
            },

            callback: function(result) {

                if (!result) return;


                // Jika edit tanggal mundur, wajib isi alasan
                if(change_tanggal < today){

                    let edit_note = $(".alasan_perubahan").val().trim();

                    if(edit_note == ""){
                        bootbox.alert({
                            message: "Alasan perubahan wajib diisi.",
                            callback: function(){
                                $(".alasan_perubahan").focus();
                            }
                        });

                        return;
                    }

                    formData.append("edit_note", edit_note);
                }


                $(".btn-save").prop('disabled', true);


                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function(res){

                        showLoading();

                        let d;

                        try {
                            d = (typeof res == 'string') 
                                ? JSON.parse(res) 
                                : res;
                        } catch(e){
                            d = res;
                        }


                        if(d && d.status == 1){

                            bootbox.alert({
                                message: d.message || 'Data berhasil di update.',
                                callback: function(){
                                    window.location.reload();
                                }
                            });

                        } else {

                            if(d && d.message){

                                bootbox.alert({
                                    message: d.message,
                                    callback: function(){
                                        bootbox.hideAll();
                                        $(".btn-save").prop('disabled', false);
                                    }
                                });

                            }

                        }

                    },

                    error: function(){

                        toastr.error('Terjadi kesalahan saat menyimpan data.');
                        $(".btn-save").prop('disabled', false);

                    }
                });

            }
        });
    },
    
    editPengajuan : (id) => {
        $.post(base_controller + '/edit_data', { id: id }, function(r){
            $('#tambah-tab').tab('show');
            $('#form-pengajuan').html(r);
            $(".select2").select2()
            pc.settingUp();

            // let sisa_cuti = $("input[name=sisa_cuti]").val()
            // if (sisa_cuti == 0){
            //     $("select[name='jenis_cuti'] option[value='cuti']").remove();
            //     $("select[name='jenis_cuti']").trigger('change');
            // }
            
        });
    },
    
    deletePengajuan : (id, tanggalLabel) => {
        var msg = 'Hapus data ini?';
        if (typeof tanggalLabel !== 'undefined' && tanggalLabel) {
            msg = 'Hapus pengajuan tanggal ' + tanggalLabel + '?';
        }
        bootbox.confirm({
            message: msg,
            buttons: {
                confirm: { label: 'Hapus', className: 'btn-danger' },
                cancel: { label: 'Batal', className: 'btn-secondary' }
            },
            callback: function(result){
                if (!result) return;
                $.post(base_controller + '/delete', { id: id }, function(res){
                    var d;
                    try { d = (typeof res == 'string')? JSON.parse(res) : res; } catch(e){ d = res; }

                    if (d && d.status == 1) {
                        bootbox.alert({
                            message: d.message || 'Data berhasil dihapus.',
                            callback: function () {
                                window.location.reload();
                            }
                        });
                    } else {
                        if (d && d.message) {
                            bootbox.alert(d.message);
                        }
                    }

                }).fail(function(){
                    toastr.error('Gagal menghapus data.');
                });
            }
        });
    },

    setDataKaryawan : (el) => {
        let params  = {
            nik : $(el).val(),
            jabatan : $(el).find(':selected').attr('jabatan'),
            sisa_cuti: Number($(el).find(':selected').attr('sisa_cuti') ?? 0),
            cuti_terpakai: Number($(el).find(':selected').attr('cuti_terpakai') ?? 0)
        };

        // console.log('params', params);

        $(".nik_karyawan").val(params.nik);
        $(".jabatan").val(params.jabatan);
        $(".sisa_cuti").val(params.sisa_cuti);
        $(".cuti_terpakai").val(params.cuti_terpakai);

        // console.log('sisa', params.sisa_cuti);
        
        if (params.sisa_cuti == 0) {

            // $("select[name='jenis_cuti'] option[value='cuti']").remove();
            // $("select[name='jenis_cuti']").trigger('change');

        } else {
  
        }
    },

    // checkTanggalPengajuan : (el) => {
    //     // determine selected values
    //     var jenis = $('select[name="jenis_cuti"]').val();
    //     var $mulai = $('#tanggal_mulai');
    //     var val = $mulai.val();

    //     if (!val) return;
    //     if (typeof moment === 'function'){
          
    //         var m = moment(val, ['DD MMM YYYY','DD MM YYYY'], 'id');
    //         if (!m || !m.isValid()) return;
    //         var today = moment().startOf('day');
    //         var isBeforeToday = m.isBefore(today);

    //         // allow selecting previous dates only for sakit or force majeure
    //         if (isBeforeToday && !(jenis === 'cuti_sakit' || jenis === 'cuti_force_majeure')){
    //             bootbox.alert('Untuk jenis cuti selain Cuti Sakit atau Force Majeure, tanggal mulai tidak boleh sebelum hari ini.');
    //             // reset to today
    //             $mulai.data('DateTimePicker') && $mulai.data('DateTimePicker').date(today);
    //             $mulai.val(today);
    //         }
    //     }
    //     // additionally, when adding a new pengajuan, check if same tanggal_mulai already exists
    //     try {
    //         var idField = $('#form-pengajuan-cuti').find('input[name="id"]');
    //         var isNew = !(idField.length && idField.val() !== '');
    //         if (isNew && typeof moment === 'function'){

    //             var nik = $('select[name="nik"]').val() || $('input[name="nik"]').val() || $('.nik_karyawan').val();
    //             if (nik && m && m.isValid()){
    //                 var tanggalYmd = m.format('YYYY-MM-DD');
    //                 $.post(base_controller + '/checkTanggalPengajuan', { nik: nik, tanggal: tanggalYmd }, function(res){
    //                     var d;
    //                     try { d = (typeof res === 'string') ? JSON.parse(res) : res; } catch(e) { d = res; }
    //                     if (d && d.status == 0){
    //                         // disable save and notify user
    //                         $('.btn-save').prop('disabled', true);
    //                         bootbox.alert(d.message || 'Tanggal sudah pernah diajukan.');

    //                         console.log(d)
    //                     } else {
    //                         // enable save when date is ok
    //                         $('.btn-save').prop('disabled', false);
    //                     }
    //                 }).fail(function(){
    //                     // on failure, do not block save
    //                     $('.btn-save').prop('disabled', false);
    //                 });
    //             }
    //         }

    //         pc.setJumlahHari();
                
    //     } catch (e) {
    //         // ignore
    //     }
    // },

    checkTanggalPengajuan: () => {

        var jenis = $('select[name="jenis_cuti"]').val();
        var $mulai = $('#tanggal_mulai');
        var $selesai = $('#tanggal_selesai');

        var val = $mulai.val();
        if (!val || typeof moment !== 'function') {
            return;
        }

        var mulaiDate = moment(val, ['DD MMM YYYY', 'DD MM YYYY'], 'id');
        if (!mulaiDate.isValid()) {
            return;
        }

        var today = moment().startOf('day');

        // Selain sakit & force majeure tidak boleh sebelum hari ini
        if (
            !['cuti_sakit', 'cuti_force_majeure'].includes(jenis) &&
            mulaiDate.isBefore(today)
        ) {

            bootbox.alert('Untuk jenis cuti selain Cuti Sakit atau Force Majeure, tanggal mulai tidak boleh sebelum hari ini.');

            if ($mulai.data('DateTimePicker')) {
                $mulai.data('DateTimePicker').date(today);
            }

            if ($selesai.data('DateTimePicker')) {
                $selesai.data('DateTimePicker').date(today);
            }

            $mulai.val(today.locale('id').format('DD MMM YYYY'));
            $selesai.val(today.locale('id').format('DD MMM YYYY'));

            pc.setJumlahHari();
            return;
        }

        // Jika tanggal selesai lebih kecil, otomatis samakan
        if ($selesai.val()) {

            var selesaiDate = moment($selesai.val(), ['DD MMM YYYY', 'DD MM YYYY'], 'id');

            if (selesaiDate.isValid() && selesaiDate.isBefore(mulaiDate, 'day')) {

                if ($selesai.data('DateTimePicker')) {
                    $selesai.data('DateTimePicker').date(mulaiDate);
                }

                $selesai.val(mulaiDate.locale('id').format('DD MMM YYYY'));
            }
        }

        pc.setJumlahHari();
        pc.checkJumlahLibur();
    },


    // checkTanggalPengajuanEdit: (elm, e) => {

    //     let nik = $(".nik").val();
    //     let id = $('input[name=id]').val();
    //     let jenis = $('select[name=jenis_cuti]').val();
    //     let $mulai = $('#tanggal_mulai_edit');
    //     let $selesai = $('#tanggal_selesai_edit');


    //     let tanggal_awal = $mulai.val();
    //     // let tanggal_selesai = $selesai.val();

    //     if (typeof moment === 'function') {

    //         // let m = moment(tanggal, ['DD MMM YYYY', 'DD MM YYYY']).locale('id');
    //         let m = moment(tanggal_awal, ['DD MMM YYYY', 'DD MM YYYY'], 'id');
    //         // let s = moment(tanggal_selesai, ['DD MMM YYYY', 'DD MM YYYY'], 'id');

    //         if (!m.isValid()) {
    //             return;
    //         }

    //         let today = moment().startOf('day');

    //         // Jenis cuti biasa tidak boleh memilih tanggal sebelum hari ini
    //         if (['cuti', 'cuti_jatah_liburan'].includes(jenis) && m.isBefore(today)) {

    //             bootbox.alert('Tanggal mulai cuti tidak boleh sebelum hari ini.', function () {
    //                 // $('.btn-save').prop('disabled', true);
    //             });

    //             if ($mulai.data('DateTimePicker')) {
    //                 $mulai.data('DateTimePicker').date(today);
    //             }

    //             $mulai.val(today.locale('id').format('DD MMM YYYY'));
    //             $selesai.val(today.locale('id').format('DD MMM YYYY'));

    //             pc.setJumlahHari();
                
    //             return;
    //         } else {
    //             // $('.btn-save').prop('disabled', false);
    //         }

    //         tanggal = m.format('YYYY-MM-DD');
    //     }

    //     $.post(base_controller + '/checkTanggalPengajuan', {
    //         id: id,
    //         nik: nik,
    //         tanggal: tanggal
    //     }, function(res) {

    //         if (res.status == 0) {
    //             $('.btn-save').prop('disabled', true);
    //             bootbox.alert(res.message);
    //         } else {
    //             $('.btn-save').prop('disabled', false);
    //         }

    //     }, 'JSON');
    // },

    checkTanggalPengajuanEdit: () => {

        let jenis = $('select[name=jenis_cuti]').val();
        let $mulai = $('#tanggal_mulai_edit');
        let $selesai = $('#tanggal_selesai_edit');

        let tanggalMulai = $mulai.val();
        let tanggalSelesai = $selesai.val();

        if (!tanggalMulai || typeof moment !== 'function') {
            return;
        }

        let mulaiDate = moment(tanggalMulai, ['DD MMM YYYY', 'DD MM YYYY'], 'id');

        if (!mulaiDate.isValid()) {
            return;
        }

        let today = moment().startOf('day');

        // Cuti biasa & jatah liburan tidak boleh sebelum hari ini
        if (['cuti', 'cuti_jatah_liburan'].includes(jenis) && mulaiDate.isBefore(today)) {

            bootbox.alert('Tanggal mulai cuti tidak boleh sebelum hari ini.');

            if ($mulai.data('DateTimePicker')) {
                $mulai.data('DateTimePicker').date(today);
            }

            if ($selesai.data('DateTimePicker')) {
                $selesai.data('DateTimePicker').date(today);
            }

            $mulai.val(today.locale('id').format('DD MMM YYYY'));
            $selesai.val(today.locale('id').format('DD MMM YYYY'));

            pc.setJumlahHari();
            return;
        }

        // Tanggal selesai tidak boleh lebih kecil dari tanggal mulai
        if (tanggalSelesai) {
            let selesaiDate = moment(tanggalSelesai, ['DD MMM YYYY', 'DD MM YYYY'], 'id');
            if (selesaiDate.isValid() && selesaiDate.isBefore(mulaiDate, 'day')) {
                // bootbox.alert('Tanggal selesai harus lebih besar atau sama dengan tanggal mulai.');
                if ($selesai.data('DateTimePicker')) {
                    $selesai.data('DateTimePicker').date(mulaiDate);
                }
                $selesai.val(mulaiDate.locale('id').format('DD MMM YYYY'));
            }
        }

        // Hitung ulang jumlah hari
        pc.setJumlahHari();

        // Hitung ulang validasi jumlah hari
        pc.checkJumlahLibur();
    },

    applyFilter : (el, event) => {
        var status = $('#filter_status').val();
        var rows = $('#table-pengajuan tbody tr');
        if (!status) {
            rows.show();
            return;
        }
        status = status.toLowerCase();
        rows.each(function(){
            var rowStatus = $(this).find('.cuti-status').text().trim().toLowerCase();
            if (status === 'reject') {
                if (rowStatus.indexOf('reject') !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
                return;
            }
            if (rowStatus.indexOf(status) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    },

    resetFilter : () => {
        $('#filter_status').val('').trigger('change');
        $('#table-pengajuan tbody tr').show();
    },

    showAttachment : (elm, e) => {
        e.preventDefault();
        var id = $(elm).attr('id_data');
        if (!id) return;

        $.ajax({
            url:  'hris/PengajuanCuti/showAttachment',
            type: 'POST',
            data: { id: id },
            beforeSend: function () {
                showLoading();
            },
            success: function (resp) {
                hideLoading();
                bootbox.dialog({
                    title: 'Attachment',
                    message: resp,
                    size: 'large',
                    centerVertical: true,
                    buttons: {
                        close: {
                            label: 'Tutup',
                            className: 'btn-secondary',
                            callback: function() {
                                bootbox.hideAll();
                            }
                        }
                    }
                });
            },
            error: function () {
                bootbox.hideAll();
                bootbox.alert({
                    title: 'Error',
                    message: 'Gagal mengambil attachment.'
                });
            }
        });
    },

    previewAttachment : (url) => {
        var html = '<img src="' + url + '" class="img-fluid rounded" style="max-height:400px;">';
        $('#attachment-preview').html(html);
    },

    keputusanPengajuan : (elm, e) => {
        e.preventDefault();
        var id = $(elm).attr('id_data');
        var status = $(elm).val();
        if (!id || !status) return;

        var submitDecision = function(note) {
            $.post('hris/PengajuanCuti/change_status', { id: id, status: status, note: note }, function(res){
                var d;
                try { d = (typeof res === 'string') ? JSON.parse(res) : res; } catch(e) { d = res; }
                var message = (d && d.message) ? d.message : 'Data berhasil disimpan.';
                if (d && d.status == 1) {
                    bootbox.alert({
                        message: message,
                        callback: function() {
                            window.location.reload();
                        }
                    });
                } else {
                    bootbox.alert({ message: message });
                }
            }).fail(function(){
                bootbox.alert({ message: 'Gagal menyimpan keputusan.' });
            });
        };

        if (status == 4 || status == 5) {
            bootbox.prompt({
                title: 'Apakah kamu yakin? Keterangan reject wajib diisi',
                inputType: 'textarea',
                callback: function(result) {
                    if (result === null) return;
                    if ($.trim(result) === '') {
                        bootbox.alert('Keterangan reject wajib diisi.');
                        return;
                    }
                    submitDecision(result);
                }
            });
        } else {
            bootbox.confirm({
                message: 'Apakah kamu yakin dengan keputusan ini?',
                buttons: {
                    confirm: { label: 'Ya', className: 'btn-primary' },
                    cancel: { label: 'Tidak', className: 'btn-secondary' }
                },
                callback: function(result) {
                    if (!result) return;
                    submitDecision('');
                }
            });
        }
    },

    revert_status : (elm, e) => {

        let id_data = $(elm).attr("id_data");
        let revert = $(elm).attr("revert");
        let revert_note = $(elm).attr("revert_note")

        bootbox.dialog({
            title: "Konfirmasi Revert",
            message: `
                <p>Apakah Anda yakin ingin mengembalikan status menjadi <b>${revert}</b>?</p>
                <div class="form-group">
                    <label>Keterangan Revert</label>
                    <textarea class="form-control" id="revert_note" placeholder="Masukkan alasan revert">`+ revert_note +`</textarea>
                </div>
            `,
            buttons: {
                confirm: {
                    label: '<i class="fa fa-check"></i> Ya',
                    className: 'btn-success',
                    callback: function(){

                        let note = $('#revert_note').val().trim();

                        if(note == ''){
                            bootbox.alert('Keterangan revert wajib diisi.');
                            return false;
                        }

                        let params = {
                            id_data : id_data,
                            revert : revert,
                            revert_note : note
                        }

                        $.ajax({
                            url: 'hris/PengajuanCuti/revert_status',
                            type: 'POST',
                            dataType: 'JSON',
                            data: params,

                            beforeSend: function(){
                                showLoading();
                            },

                            success: function(res){
                                hideLoading();

                                if(res.status){
                                    bootbox.alert(res.message, function(){
                                        location.reload();
                                    });
                                }else{
                                    bootbox.alert(res.message);
                                }
                            },

                            error: function(xhr){
                                hideLoading();
                                bootbox.alert(xhr.responseText);
                            }
                        });

                    }
                },
                cancel: {
                    label: '<i class="fa fa-times"></i> Tidak',
                    className: 'btn-danger'
                }
            }
        });

    },

    checkJumlahLibur: () => {

        var tanggal_mulai  = $('#tanggal_mulai').length ? $('#tanggal_mulai').val() : $('#tanggal_mulai_edit').val();
        var tanggal_selesai = $('#tanggal_selesai').length ? $('#tanggal_selesai').val() : $('#tanggal_selesai_edit').val();
        // var tanggal_mulai = $("#tanggal_mulai").val();
        // var tanggal_selesai = $("#tanggal_selesai").val();
        var sisa_cuti = Number($(".sisa_cuti").val());
        var jenis = $("select[name='jenis_cuti']").val();

        if (!tanggal_mulai || !tanggal_selesai) {
            return;
        }

        var mulaiDate;
        var selesaiDate;
        if (typeof moment === 'function') {
            mulaiDate = moment(tanggal_mulai, ['DD MMM YYYY', 'DD MM YYYY'], 'id');
            selesaiDate = moment(tanggal_selesai, ['DD MMM YYYY', 'DD MM YYYY'], 'id');
            if (!mulaiDate.isValid() || !selesaiDate.isValid()) {
                return;
            }
        } else {
            var parseDate = function(value) {
                var parts = value.split(' ');
                if (parts.length === 3) {
                    var day = parseInt(parts[0], 10);
                    var month = parseInt(parts[1], 10) - 1;
                    var year = parseInt(parts[2], 10);
                    return new Date(year, month, day);
                }
                return new Date(value);
            };
            mulaiDate = parseDate(tanggal_mulai);
            selesaiDate = parseDate(tanggal_selesai);
        }

        if (selesaiDate.isValid ? selesaiDate.isBefore(mulaiDate, 'day') : selesaiDate < mulaiDate) {

            var $selesai = $('#tanggal_selesai').length 
                ? $('#tanggal_selesai') 
                : $('#tanggal_selesai_edit');

            // set tanggal selesai sama dengan tanggal mulai
            if ($selesai.data('DateTimePicker')) {
                $selesai.data('DateTimePicker').date(mulaiDate);
            }

            $selesai.val(mulaiDate.locale('id').format('DD MMM YYYY'));

            $(".btn-save").prop("disabled", false);

            // hitung ulang jumlah hari
            pc.setJumlahHari();

            return;

        } else {
            $(".btn-save").prop("disabled", false);
        }
 
        var jumlah_hari = 0;
        var current = (typeof moment === 'function') ? mulaiDate.clone() : new Date(mulaiDate);
        var end = (typeof moment === 'function') ? selesaiDate.clone() : new Date(selesaiDate);

        while ((typeof moment === 'function') ? current.isSameOrBefore(end, 'day') : current <= end) {
            var dayOfWeek = (typeof moment === 'function') ? current.day() : current.getDay();
            if (dayOfWeek !== 0) {
                jumlah_hari++;
            }
            if (typeof moment === 'function') {
                current.add(1, 'day');
            } else {
                current.setDate(current.getDate() + 1);
            }
        }

        let jenis_cuti_limit = [
            'cuti',
            'cuti_sakit',
            'cuti_force_majeure'
        ];

        // console.log(jenis)

        // if (jumlah_hari > sisa_cuti && jenis_cuti_limit.includes(jenis)) {
        //     bootbox.alert("Jumlah hari cuti melebihi sisa cuti (" + sisa_cuti + " hari).");
        //     $(".btn-save").prop("disabled", true);
        //     return;
        // }

        // if (jumlah_hari > sisa_cuti && jenis_cuti_limit.includes(jenis)) {

        //     let html = `<div style="border:1px solid orange; padding:10px; border-left:3px solid orange; background-color:#FFEAA6;">
        //         <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Jumlah hari cuti melebihi sisa cuti (` + sisa_cuti + ` hari)
        //     </div>`

        //     $(".alert-notif").html(html)
        //     return;
        // }

        // pc.checkTanggalPengajuan();
        pc.setJumlahHari();
        // console.log("Jumlah hari cuti:", jumlah_hari);
    },

    setJumlahHari: () => {
        var jumlah_hari = 0;

        var tanggal_mulai   = $('#tanggal_mulai').length ? $('#tanggal_mulai').val() : $('#tanggal_mulai_edit').val();
        var tanggal_selesai = $('#tanggal_selesai').length ? $('#tanggal_selesai').val() : $('#tanggal_selesai_edit').val();

        if (typeof moment === 'function') {
            mulaiDate = moment(tanggal_mulai, ['DD MMM YYYY', 'DD MM YYYY'], 'id');
            selesaiDate = moment(tanggal_selesai, ['DD MMM YYYY', 'DD MM YYYY'], 'id');

            if (!mulaiDate.isValid() || !selesaiDate.isValid()) {
                return;
            }

            var current = mulaiDate.clone();

            while (current.isSameOrBefore(selesaiDate, 'day')) {
                // 0 = Minggu
                if (current.day() !== 0) {
                    jumlah_hari++;
                }

                current.add(1, 'day');
            }
        }

        $("input[name=jumlah_hari]").val(jumlah_hari)

        // console.log("Jumlah hari cuti:", jumlah_hari);
    }
}

function handleAttachmentInput(files, inputElem){
    // ensure we operate on form-scoped input
    var $form = $('#form-pengajuan-cuti');
    // pc.attachmentDT stores current selection across events
    for (var i=0;i<files.length;i++){
        var f = files[i];
        // avoid duplicates by name+size+lastModified
        var exists = false;
        for (var j=0;j<pc.attachmentDT.files.length;j++){
            var ex = pc.attachmentDT.files[j];
            if (ex.name === f.name && ex.size === f.size && ex.lastModified === f.lastModified){ exists = true; break; }
        }
        if (!exists){ pc.attachmentDT.items.add(f); }
    }
    // update native input.files
    if (inputElem) inputElem.files = pc.attachmentDT.files;
    renderAttachmentThumbs();
}

function renderAttachmentThumbs(){
    var $container = $('#attachment-thumbs');
    var $newWrap = $container.find('.attachment-new');
    $newWrap.empty();
    var files = pc.attachmentDT.files || [];
    for (var i=0;i<files.length;i++){
        (function(i){
            var f = files[i];
            var url = URL.createObjectURL(f);
            var $card = $("<div class='m-1 p-1 border rounded text-center existing-attachment' style='width:120px;flex:0 0 auto;position:relative;'></div>");
            var $thumb = $("<div style='height:70px;overflow:hidden;margin-bottom:6px;'></div>");
            if (f.type.indexOf('image/') === 0){
                $thumb.append('<img src="'+url+'" style="max-width:100%;max-height:70px;display:block;margin:0 auto;"/>');
            } else if (f.type === 'application/pdf'){
                $thumb.append('<i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>');
            } else {
                $thumb.append('<i class="fa fa-file-o fa-2x" aria-hidden="true"></i>');
            }
            var $actions = $("<div></div>");
            var $remove = $('<button type="button" class="btn btn-sm btn-danger text-danger remove-attachment" style="margin-right:4px;"><i style="color:white;" class="fa fa-trash"></i></button>');
            $remove.on('click', function(){ removeAttachmentIndex(i); });
            $actions.append($remove);
            $card.append($thumb);
            $card.append($actions);
            $newWrap.append($card);
        })(i);
    }
}

function removeAttachmentIndex(idx){
    var newDT = new DataTransfer();
    for (var i=0;i<pc.attachmentDT.files.length;i++){
        if (i === idx) continue;
        newDT.items.add(pc.attachmentDT.files[i]);
    }
    pc.attachmentDT = newDT;
    // update all file inputs in the form
    $('#form-pengajuan-cuti').find('input[name="attachment[]"]').each(function(){ this.files = pc.attachmentDT.files; });
    renderAttachmentThumbs();
}

    


function previewAttachments(files){
    if (!files || files.length === 0) return;
    var urls = [];
    var html = '<div class="d-flex flex-wrap justify-content-center">';
    for (var i=0;i<files.length;i++){
        var f = files[i];
        var url = URL.createObjectURL(f);
        urls.push(url);
        var fileHtml = '<div class="m-2" style="width:260px">';
        fileHtml += '<div class="card">';
        fileHtml += '<div class="card-body text-center">';
        if (f.type.indexOf('image/') === 0){
            fileHtml += '<img src="'+url+'" style="max-width:100%;max-height:200px;display:block;margin:0 auto;"/>';
        } else if (f.type === 'application/pdf'){
            fileHtml += '<iframe src="'+url+'" style="width:100%;height:240px;border:0;display:block;margin:0 auto;"></iframe>';
        } else {
            fileHtml += '<p class="text-truncate">'+f.name+'</p>';
        }
            fileHtml += '<p class="small text-muted">'+Math.round(f.size/1024)+' KB</p>';
        fileHtml += '</div></div></div>';
        html += fileHtml;
    }
    html += '</div>';

    var dlg = bootbox.dialog({
        title: 'Preview Attachment',
        message: html,
        size: 'large',
        buttons: {
            ok: {
                label: 'Tutup',
                className: 'btn-secondary'
            }
        }
    });

    dlg.on('hidden.bs.modal', function(){
        for(var j=0;j<urls.length;j++){
            try{ URL.revokeObjectURL(urls[j]); }catch(e){}
        }
    });
}


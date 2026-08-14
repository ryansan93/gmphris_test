let ukr = {

    setting_up : () => {
        $('.select2').select2();

        $('.tgl_pengajuan').datetimepicker({
            locale: 'id',
            format: 'DD MMM YYYY',
            maxDate: moment()
        });

        $('.tgl_resign').datetimepicker({
            locale: 'id',
            format: 'DD MMM YYYY',
            useCurrent: false
        });

        $('.tgl_pengajuan').on('dp.change', function(e) {
            if (e.date) {
                $('.tgl_resign').data('DateTimePicker').minDate(e.date);
            }
        });

        $('.tgl_resign').on('dp.change', function(e) {
            if (e.date) {
                $('.tgl_pengajuan').data('DateTimePicker').maxDate(e.date);
            }
        });
    },


    load_data : () => {
        $.ajax({
            url : 'hris/UsulanKaryawanResign/load_form',
            // data : params,
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

    // attachment_files : [],
    attachment_files : [],
    attachment_index : 0,

    upload_attachment: (elm,e) => {

        e.preventDefault();
        let card = $(elm).closest('.upload-card');
        let index = card.data('index');
        let uploader = $('<input type="file" accept=".jpg,.jpeg,.png,.pdf">');

        uploader.trigger('click');
        uploader.on('change',function(){
            let file = this.files[0];

            if(!file) return;
            ukr.attachment_files[index] = file;
            let reader = new FileReader();
            reader.onload=function(e){
                let html='';
                // console.log(file)
                if(file.type.startsWith('image')){
                    html = `
                        <img style="cursor:pointer;" src="${e.target.result}" onclick="ukr.show_attachment(this, event)">
                    `;
                }else{
                    html = `
                       <i class="fa fa-upload" onclick="ukr.upload_attachment(this,event)" style="font-size:50px; cursor:pointer"></i>
                    `;
                }
                card.find(".nama-file").html(file.name);
                card.find('.thumbnail').html(html);
            };

            reader.readAsDataURL(file);
        });
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

    add_attachment: () => {
        
        $(".text-muted").css("display", "none");

        if ($('.upload-card').length > ukr.attachment_index) {
            ukr.attachment_index = $('.upload-card').length;
        }

        let index = ukr.attachment_index++;
        // console.log(index)
        let html = `
            <div class="upload-card" data-index="${index}">
                <span style="color:red" onclick="ukr.remove_attachment(this,event)"><i class="fa fa-times"></i> </span>
                <div class="thumbnail">
                    <i class="fa fa-upload" onclick="ukr.upload_attachment(this,event)" style="font-size:50px; cursor:pointer"></i>
                </div>
                <div class="nama-file">
                    Upload Attachment
                </div>
            </div>
        `;

        $('.upload-area').append(html);

    },

    remove_attachment: (elm,e)=>{

        e.preventDefault();
        let card  = $(elm).closest('.upload-card');
        let index = card.data('index');
        ukr.attachment_files.splice(index, 1);
        card.remove();
        $('.upload-card').each(function(i){
            $(this).attr('data-index', i);
        });

        if ($(".upload-card").length == 0){
            $(".text-muted").css("display", "block");
        }

    },

    // save : () => {

    //     let picker_tgl_pengajuan = $('.tgl_pengajuan').data('DateTimePicker');
    //     let picker_tgl_resign    = $('.tgl_resign').data('DateTimePicker');

    //     let tgl_pengajuan = ''; 
    //     let tgl_resign    = ''; 

    //     if (picker_tgl_pengajuan && picker_tgl_pengajuan.date() &&
    //         picker_tgl_resign && picker_tgl_resign.date()) {

    //         tgl_pengajuan = picker_tgl_pengajuan.date().format('YYYY-MM-DD');
    //         tgl_resign    = picker_tgl_resign.date().format('YYYY-MM-DD');

    //         if (tgl_pengajuan > tgl_resign) {
    //             toastr.info("Tanggal pengajuan tidak boleh lebih besar dari tanggal resign");
    //             return false;
    //         } 

    //     } else {
    //         toastr.info("Tanggal pengajuan dan tanggal resign belum dipilih");
    //         return false;
    //     }

    //     let nik                 = $(".nik").val();
    //     let tanggal_pengajuan   = tgl_pengajuan;
    //     let tanggal_resign      = tgl_resign;
    //     let alasan_resign       = $(".alasan_resign").val();
    //     let jenis               = $(".jenis").val();

    //     let formData = new FormData();

    //     formData.append('nik', nik);
    //     formData.append('tanggal_pengajuan', tanggal_pengajuan);
    //     formData.append('tanggal_resign', tanggal_resign);
    //     formData.append('alasan_resign', alasan_resign);
    //     formData.append('jenis', jenis);

    //     ukr.attachment_files.forEach(function(file,index){
    //         if(file){
    //             formData.append('attachment[]', file);
    //         }
    //     });
        

    //     $.ajax({
    //         url: 'hris/UsulanKaryawanResign/save',
    //         type: 'POST',
    //         data: formData,
    //         processData:false,
    //         contentType:false,
    //         beforeSend:function(){
    //             showLoading();
    //         },
    //         success:function(result){
    //             hideLoading();
    //             // let result = JSON.parse(res);
    //             if(result.status){
    //                 // toastr.success(result.message);
    //                 bootbox.alert({
    //                     title: "Berhasil",
    //                     message: result.message,
    //                     buttons: {
    //                         ok: {
    //                             label: "OK",
    //                             className: "btn-primary"
    //                         }
    //                     },
    //                     callback: function () {
    //                         window.location.reload();
    //                     }
    //                 });
    //             }else{
    //                 toastr.error(result.message);
    //             }
    //         },

    //         error:function(){
    //             hideLoading();
    //             toastr.error('Gagal menyimpan data');
    //         }
    //     });
    // },

    // save : () => {

    //     let picker_tgl_pengajuan = $('.tgl_pengajuan').data('DateTimePicker');
    //     let picker_tgl_resign    = $('.tgl_resign').data('DateTimePicker');

    //     let tgl_pengajuan = ''; 
    //     let tgl_resign    = ''; 

    //     if (picker_tgl_pengajuan && picker_tgl_pengajuan.date() &&
    //         picker_tgl_resign && picker_tgl_resign.date()) {

    //         tgl_pengajuan = picker_tgl_pengajuan.date().format('YYYY-MM-DD');
    //         tgl_resign    = picker_tgl_resign.date().format('YYYY-MM-DD');

    //         if (tgl_pengajuan > tgl_resign) {
    //             toastr.info("Tanggal pengajuan tidak boleh lebih besar dari tanggal resign");
    //             return false;
    //         } 

    //     } else {
    //         toastr.info("Tanggal pengajuan dan tanggal resign belum dipilih");
    //         return false;
    //     }

    //     let nik                 = $(".nik").val();
    //     let tanggal_pengajuan   = tgl_pengajuan;
    //     let tanggal_resign      = tgl_resign;
    //     let alasan_resign       = $(".alasan_resign").val();
    //     let jenis               = $(".jenis").val();

    //     if (!nik || nik.trim() === '') {
    //         bootbox.alert('NIK wajib diisi');
    //         return false;
    //     }

    //     // Validasi attachment khusus RESIGN
    //     if (jenis === "RESIGN" || jenis === "PENSIUN") {
    //         if (!ukr.attachment_files || ukr.attachment_files.length === 0) {
    //             toastr.info("Attachment wajib diupload untuk jenis Resign");
    //             return false;
    //         }
    //     }

    //     let formData = new FormData();

    //     formData.append('nik', nik);
    //     formData.append('tanggal_pengajuan', tanggal_pengajuan);
    //     formData.append('tanggal_resign', tanggal_resign);
    //     formData.append('alasan_resign', alasan_resign);
    //     formData.append('jenis', jenis);

    //     ukr.attachment_files.forEach(function(file,index){
    //         if(file){
    //             formData.append('attachment[]', file);
    //         }
    //     });

    //     $.ajax({
    //         url: 'hris/UsulanKaryawanResign/save',
    //         type: 'POST',
    //         data: formData,
    //         processData:false,
    //         contentType:false,
    //         beforeSend:function(){
    //             showLoading();
    //         },
    //         success:function(result){
                

    //             if(result.status){
    //                 bootbox.alert({
    //                     title: "Berhasil",
    //                     message: result.message,
    //                     buttons: {
    //                         ok: {
    //                             label: "OK",
    //                             className: "btn-primary"
    //                         }
    //                     },
    //                     callback: function () {
    //                         hideLoading();
    //                         window.location.reload();
    //                     }
    //                 });
    //             }else{
    //                 toastr.error(result.message);
    //             }
    //         },

    //         error:function(){
    //             hideLoading();
    //             toastr.error('Gagal menyimpan data');
    //         }
    //     });
    // },

    save : () => {

        let picker_tgl_pengajuan = $('.tgl_pengajuan').data('DateTimePicker');
        let picker_tgl_resign    = $('.tgl_resign').data('DateTimePicker');

        let tgl_pengajuan = ''; 
        let tgl_resign    = ''; 

        if (picker_tgl_pengajuan && picker_tgl_pengajuan.date() &&
            picker_tgl_resign && picker_tgl_resign.date()) {

            tgl_pengajuan = picker_tgl_pengajuan.date().format('YYYY-MM-DD');
            tgl_resign    = picker_tgl_resign.date().format('YYYY-MM-DD');

            if (tgl_pengajuan > tgl_resign) {
                toastr.info("Tanggal pengajuan tidak boleh lebih besar dari tanggal resign");
                return false;
            } 

        } else {
            toastr.info("Tanggal pengajuan dan tanggal resign belum dipilih");
            return false;
        }

        let nik                 = $(".nik").val();
        let tanggal_pengajuan   = tgl_pengajuan;
        let tanggal_resign      = tgl_resign;
        let alasan_resign       = $(".alasan_resign").val();
        let jenis               = $(".jenis").val();

        if (!nik || nik.trim() === '') {
            bootbox.alert('NIK wajib diisi');
            return false;
        }

        // Validasi attachment khusus RESIGN / PENSIUN
        if (jenis === "RESIGN" || jenis === "PENSIUN") {
            if (!ukr.attachment_files || ukr.attachment_files.length === 0) {
                toastr.info("Attachment wajib diupload untuk jenis Resign");
                return false;
            }
        }

        // Konfirmasi sebelum menyimpan
        bootbox.confirm({
            title: "Konfirmasi",
            message: "Apakah Anda yakin ingin menyimpan pengajuan resign ini?",
            buttons: {
                confirm: {
                    label: "Ya, Simpan",
                    className: "btn-primary"
                },
                cancel: {
                    label: "Batal",
                    className: "btn-secondary"
                }
            },
            callback: function(result) {

                if (!result) {
                    return;
                }

                let formData = new FormData();

                formData.append('nik', nik);
                formData.append('tanggal_pengajuan', tanggal_pengajuan);
                formData.append('tanggal_resign', tanggal_resign);
                formData.append('alasan_resign', alasan_resign);
                formData.append('jenis', jenis);

                ukr.attachment_files.forEach(function(file, index){
                    if(file){
                        formData.append('attachment[]', file);
                    }
                });

                $.ajax({
                    url: 'hris/UsulanKaryawanResign/save',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,

                    beforeSend: function(){
                        showLoading();
                    },

                    success: function(result){

                        if(result.status){
                            bootbox.alert({
                                title: "Berhasil",
                                message: result.message,
                                buttons: {
                                    ok: {
                                        label: "OK",
                                        className: "btn-primary"
                                    }
                                },
                                callback: function () {
                                    hideLoading();
                                    window.location.reload();
                                }
                            });
                        } else {
                            hideLoading();
                            toastr.error(result.message);
                        }
                    },

                    error: function(){
                        hideLoading();
                        toastr.error('Gagal menyimpan data');
                    }
                });
            }
        });
    },


    edit_data: (elm, e) => {

        let params = {
            id_data : $(elm).attr("id_data"),
        }

        $.ajax({
            url : 'hris/UsulanKaryawanResign/edit_data',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading();

                bootbox.dialog({
                    title: 'Edit Data',
                    message: html,
                    size: 'large',
                    onShown: function (e) {
              
                        $('.select2').select2({
                            dropdownParent: $('.bootbox')
                        });

                        $('input:first').focus();

                        var tglPengajuan = $('.bootbox #tgl_pengajuan input').val();

                        $('.bootbox #tgl_pengajuan').datetimepicker({
                            locale: 'id',
                            format: 'DD MMM YYYY',
                            useCurrent: false
                        });

                        if (tglPengajuan) {
                            $('.bootbox #tgl_pengajuan')
                                .data('DateTimePicker')
                                .date(moment(tglPengajuan, 'DD MMM YYYY'));
                        }
                        

                        $('.bootbox #tgl_resign').datetimepicker({
                            locale: 'id',
                            format: 'DD MMM YYYY',
                            useCurrent: false
                        });


                        // Tanggal resign minimal sama dengan tanggal pengajuan
                        $('.bootbox #tgl_pengajuan').on('dp.change', function(e) {
                            if (e.date) {
                                $('.bootbox #tgl_resign')
                                    .data('DateTimePicker')
                                    .minDate(e.date);
                            }
                        });

                        ukr.load_attachment_edit().then(()=>{
                            console.log('attachment siap', ukr.attachment_files);
                        });
                        
                    },
                    buttons: {
                        cancel: {
                            label: 'Tutup',
                            className: 'btn-default'
                        },
                        save: {
                            label: 'Update',
                            className: 'btn-primary btn-edit-usulan',
                            callback: function () {
                 
                                ukr.exec_edit_data();
                                return false; 
                            }
                        }
                    }
                });
               
            },
        });
    },

    load_attachment_edit: () => {

        let detail_attachment = $('.bootbox .upload-area').attr('data-attachment');
        let base_url = $('.bootbox .upload-area').attr('base_url');
        // console.log('detail_attachment raw:', detail_attachment);
        if(!detail_attachment){
            return Promise.resolve();
        }
        detail_attachment = JSON.parse(detail_attachment);
        // console.log('detail_attachment array:', detail_attachment);
        ukr.attachment_files = [];
        let promises = detail_attachment.map((item,index)=>{
            let url = base_url + item.file_path;
            // console.log('url:', url);
            return fetch(url)
            .then(response => {
                // console.log('status:', response.status);
                // console.log('content-type:', response.headers.get('content-type'));
                return response.blob();
            })
            .then(blob => {
                // console.log('blob:', blob.type, blob.size);
                let file = new File(
                    [blob],
                    item.nama_file,
                    {
                        type: blob.type
                    }
                );
                ukr.attachment_files[index] = file;
            });
        });

        return Promise.all(promises).then(()=>{
            ukr.attachment_index = ukr.attachment_files.length;
        });

    },


    exec_edit_data: () => {


        let picker_tgl_pengajuan = $(".bootbox").find('.tgl_pengajuan').data('DateTimePicker');
        let picker_tgl_resign    = $(".bootbox").find('.tgl_resign').data('DateTimePicker');

        let tgl_pengajuan = ''; 
        let tgl_resign    = ''; 

        if (picker_tgl_pengajuan && picker_tgl_pengajuan.date() &&
            picker_tgl_resign && picker_tgl_resign.date()) {

            tgl_pengajuan = picker_tgl_pengajuan.date().format('YYYY-MM-DD');
            tgl_resign    = picker_tgl_resign.date().format('YYYY-MM-DD');

            if (tgl_pengajuan > tgl_resign) {
                toastr.info("Tanggal pengajuan tidak boleh lebih besar dari tanggal resign");
                return false;
            } 

        } else {
            toastr.info("Tanggal pengajuan dan tanggal resign belum dipilih");
            return false;
        }

        // console.log(tgl_pengajuan, tgl_resign);
        // return false;

        let id                  = $(".bootbox .id_data").val();
        let nik                 = $(".bootbox .nik").val();
        let tanggal_pengajuan   = tgl_pengajuan;
        let tanggal_resign      = tgl_resign;
        let alasan_resign       = $(".bootbox .alasan_resign").val();
        let jenis               = $(".bootbox .jenis").val();

        // Validasi attachment khusus RESIGN
        if (jenis === "RESIGN" || jenis === "PENSIUN") {
            if (!ukr.attachment_files || ukr.attachment_files.length === 0) {
                toastr.info("Attachment wajib diupload untuk jenis Resign");
                return false;
            }
        }


        let formData = new FormData();

        formData.append('id', id);
        formData.append('nik', nik);
        formData.append('tanggal_pengajuan', tanggal_pengajuan);
        formData.append('tanggal_resign', tanggal_resign);
        formData.append('alasan_resign', alasan_resign);
        formData.append('jenis', jenis);


        ukr.attachment_files.forEach(function(file,index){

            if(file){
                formData.append('attachment[]', file);
            }

        });


        $.ajax({

            url: 'hris/UsulanKaryawanResign/update',
            type: 'POST',
            data: formData,
            processData:false,
            contentType:false,

            beforeSend:function(){
                showLoading();
            },

            success:function(result){

                hideLoading();

                if(result.status){

                    bootbox.alert({
                        title: "Berhasil",
                        message: result.message,
                        buttons: {
                            ok: {
                                label: "OK",
                                className: "btn-primary"
                            }
                        },
                        callback:function(){
                            window.location.reload();
                        }
                    });

                }else{

                    toastr.error(result.message);

                }

            },


            error:function(){

                hideLoading();
                toastr.error('Gagal menyimpan data');

            }

        });

    },

    delete_attachment: (elm, e) => {

        let id_attachment = $(elm).attr("id_attachment");
        let card = $(elm).closest(".upload-card");
        let index = card.data('index');


        bootbox.confirm({
            message: "Apakah Anda yakin ingin menghapus attachment ini?",
            callback: function(result){

                if(result){

                    $.ajax({
                        url: 'hris/UsulanKaryawanResign/delete_attachment',
                        type: 'POST',
                        data: {
                            id_header: $(".id_data").val(),
                            id_attachment: id_attachment
                        },
                        dataType: 'json',
                        success:function(res){

                            if(res.status == 1){

                                // hapus file dari array
                                ukr.attachment_files[index] = null;

                                // hapus card
                                card.remove();

                                // reset index card
                                $('.upload-card').each(function(i){
                                    $(this).attr('data-index', i);
                                });

                            }

                        }
                    });

                }

            }
        });

    },

    delete: (elm, e) => {

        let params = {
            id_data : $(elm).attr("id_data"),
        }

        bootbox.confirm({
            title: "Konfirmasi",
            message: "Apakah data usulan resign ini akan dihapus?",
            buttons: {
                confirm: {
                    label: 'Ya',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'Tidak',
                    className: 'btn-secondary'
                }
            },
            callback: function(result) {
                if (result) {
                    $.ajax({
                        url: 'hris/UsulanKaryawanResign/delete',
                        type: 'POST',
                        data: params,
                        dataType: 'json',
                        beforeSend: function() {
                            showLoading();
                        },
                        success: function(res) {
                            hideLoading();
                            if (res.status == 1) {

                                bootbox.alert({
                                    message: res.message,
                                    callback: function() {
                                        // reload datatable
                                        // ukr.load_data();
                                        window.location.reload();
                                    }
                                });
                            } else {
                                bootbox.alert(res.message);
                            }
                        },
                        error: function(xhr) {
                            hideLoading();
                            bootbox.alert('Terjadi kesalahan saat menghapus data.');
                            console.log(xhr.responseText);
                        }
                    });

                }

            }
        });

    },

    keputusanUsulan: (elm, e) => {
        if (e) e.preventDefault();

        let id = $(elm).attr('id_data');
        let status = parseInt($(elm).val());

        let text = '';

        switch(status){
            case 2:
                text = 'Yakin ingin meng-acknowledge pengajuan resign ini?';
            break;

            case 3:
                text = 'Yakin ingin meng-approve pengajuan resign ini?';
            break;

            case 4:
                text = 'Yakin ingin me-reject pengajuan resign ini sebagai Atasan?';
            break;

            case 5:
                text = 'Yakin ingin me-reject pengajuan resign ini sebagai HRD?';
            break;
        }

        // Reject membutuhkan keterangan
        if (status == 4 || status == 5) {

            bootbox.dialog({
                title: 'Reject Pengajuan',
                message:
                    '<div class="form-group">' +
                        '<label>Keterangan Reject</label>' +
                        '<textarea class="form-control keterangan_reject" rows="4" placeholder="Masukkan alasan reject..."></textarea>' +
                    '</div>',
                buttons: {
                    cancel: {
                        label: 'Batal',
                        className: 'btn-default'
                    },
                    ok: {
                        label: 'Simpan',
                        className: 'btn-primary',
                        callback: function(){

                            let ket = $('.bootbox').find('.keterangan_reject').val().trim();

                            if (ket == '') {
                                bootbox.alert('Keterangan reject harus diisi.');
                                return false;
                            }

                            ukr.simpanKeputusan(id, status, ket);
                        }
                    }
                }
            });

            return;
        }

        bootbox.confirm({
            title: 'Konfirmasi',
            message: text,
            callback: function(result){
                if(result){
                    ukr.simpanKeputusan(id, status, '');
                }
            }
        });
    },

    simpanKeputusan: (id, status, keterangan) => {
        $.ajax({
            url: 'hris/UsulanKaryawanResign/updateKeputusan',
            type: 'POST',
            dataType: 'JSON',
            data: {
                id: id,
                status: status,
                keterangan_reject: keterangan
            },
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
    },

    addFasilitas_edit: (elm, e) => {
        e.preventDefault();

        let idx = ukr.attachment_fasilitas_edit++;

        var html = `
            <div class="card-facility" data-id="${idx}">
                <div class="nama">
                    <input placeholder="Nama Fasilitas" type="text" class="nama_fasilitas form-control">
                </div>

                <div class="row-kondisi">
                    <input placeholder="Kondisi Fasilitas" type="text" class="kondisi_fasilitas form-control">
                    <input placeholder="Jumlah" onchange="if (this.value < 0) this.value = 0;" type="number" class="jumlah_fasilitas form-control">
                </div>

                <div class="row-action">
                    <div class="upload-box">
                        <span class="nama-file">Upload Attachment</span>

                        <div class="upload-icon ml-auto"
                            onclick="ukr.upload_attachment_clearance(this, event)">
                            <i class="fa fa-upload"></i>
                        </div>

                        <div class="data_attachment" data-index="${idx}"></div>
                    </div>

                    <button class="btn btn-primary" onclick="ukr.addFasilitas_edit(this, event)">
                        <i class="fa fa-plus"></i>
                    </button>

                    <button class="btn btn-danger" onclick="ukr.removeFasilitas_edit(this, event)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `;

        $(elm).closest('.form-group').append(html);
    },

    removeFasilitas: (elm, e) => {
        e.preventDefault();
        var total = $('.card-facility').length;
        if (total <= 1) {
            toastr.info('Minimal harus ada 1 fasilitas');
            return;
        }
        console.log(total)
        $(elm).closest('.card-facility').remove();
    },

    removeFasilitas_edit: (elm, e) => {
        e.preventDefault();
        var total = $('.card-facility').length;
        if (total <= 1) {
            toastr.info('Minimal harus ada 1 fasilitas');
            return;
        }
        let card = $(elm).closest('.card-facility');
        let id = card.attr('data-id');



        console.log('hapus id:', id);
        console.log('sebelum hapus:', ukr.attachment_fasilitas_files);
        
        if (ukr.attachment_fasilitas_files && ukr.attachment_fasilitas_files[id]) {
            delete ukr.attachment_fasilitas_files[id];
        }
        card.remove();
        console.log('Attachment setelah hapus:', ukr.attachment_fasilitas_files);
    },

    save_clearance : (elm, e) => {

        let facility = [];

        $(".form-group").find(".card-facility").each(function(){

            let temp = {
                id                  : $(this).attr('data-id'),
                nama_fasilitas      : $(this).find(".nama_fasilitas").val(),
                kondisi_fasilitas   : $(this).find(".kondisi_fasilitas").val(),
                jumlah_fasilitas    : $(this).find(".jumlah_fasilitas").val(),
            }

            facility.push(temp);
        });

        let formData = new FormData();

        formData.append(
            'id_data',
            $(elm).attr("id_data")
        );

        formData.append(
            'data',
            JSON.stringify(facility)
        );

        // append attachment fasilitas
        Object.keys(ukr.attachment_fasilitas_files).forEach(function(id){
            formData.append(
                'attachment['+id+']',
                ukr.attachment_fasilitas_files[id]
            );
        });

        // Object.keys(ukr.attachment_fasilitas_files).forEach(function(id){
        //     let file = ukr.attachment_fasilitas_files[id];
        //     if(file instanceof File){
        //         formData.append(
        //             'attachment['+id+']',
        //             file
        //         );
        //     }
        // });

        $.ajax({
            url: 'hris/UsulanKaryawanResign/save_clearance',
            type: 'POST',
            dataType: 'JSON',
            data: formData,
            processData: false,
            contentType: false,
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

    },

    saveVerifikasiClearance: (elm, e) => {
        
        e.preventDefault();

        bootbox.confirm({
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin memverifikasi dan menyelesaikan proses serah terima clearance?',
            buttons: {
                confirm: {
                    label: 'Ya',
                    className: 'btn-primary'
                },
                cancel: {
                    label: 'Batal',
                    className: 'btn-secondary'
                }
            },
            callback: function(result) {

                if (!result) {
                    return;
                }

                var id_data = $(elm).attr('id_data');
                var nik = $(elm).attr('nik');

                var data = [];
                var valid = true;

                $('.table-clearance tbody tr').each(function() {

                    var id = $(this).find('input[name*="[id]"]').val();
                    var status = $(this).find('select[name*="[status]"]').val();
                    var catatan = $(this).find('input[name*="[catatan]"]').val();

                    // console.log(status)
                    if (status == null || status === '' || status === undefined) {
                        bootbox.alert('Status pada semua data wajib dipilih.');
                        valid = false;
                        return false;
                    }
                    // if (catatan === '') {
                    //     bootbox.alert('Catatan pada semua data wajib diisi.');
                    //     valid = false;
                    //     return false;
                    // }

                    data.push({
                        id: id,
                        status: status, 
                        catatan: catatan 
                    });

                });

                if (!valid) {
                    return;
                }

                // console.log(data)


                $.ajax({
                    url: 'hris/UsulanKaryawanResign/saveVerifikasiClearance',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_data: id_data,
                        nik: nik,
                        data: data
                    },
                    beforeSend: function() {
                        $(elm).prop('disabled', true);
                    },
                    success: function(res) {

                        if (res.status == 1) {
                            bootbox.alert({
                                message: res.message,
                                callback: function() {
                                    // window.location.href = 'home/Home';

                                    window.location.reload();
                                }
                            });
                        } else {
                            bootbox.alert({
                                message: res.message
                            });
                        }

                        $(elm).prop('disabled', false);
                    },
                    error: function() {

                        bootbox.alert({
                            message: 'Terjadi kesalahan sistem'
                        });

                        $(elm).prop('disabled', false);
                    }
                });
            }
        });
    },

    show_verifikasi_clearance: function(elm) {
        const document = $(elm).val();
        window.location.href = "hris/UsulanKaryawanResign/verifikasiClearance?kode=" + encodeURIComponent(document);
    },


    showAttachment: (elm, e) => {
        e.preventDefault();

        var id = $(elm).attr('id_data');

        $.ajax({
            url: 'hris/UsulanKaryawanResign/showAttachment',
            type: 'POST',
            data: {
                id: id
            },
            beforeSend: function () {
                bootbox.dialog({
                    title: 'Attachment',
                    message: '<div class="text-center">Loading...</div>',
                    size: 'large',
                    centerVertical: true,
                    closeButton: false
                });
            },
            success: function (resp) {

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
                                // cukup return, biarkan bootbox menutup sendiri
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
                    message: 'Gagal mengambil attachment'
                });
            }
        });
    },

    previewAttachment: (url) =>
    {
        $('#attachment-preview').html(`
            <img src="${url}" 
                class="img-fluid rounded"
                style="max-height:400px;">
        `);
    },
    


    
    attachment_fasilitas: 1,
    attachment_fasilitas_edit : 0,
    attachment_fasilitas_files: {},

    addFasilitas_insert: (elm, e) => {

        e.preventDefault();

        let idx = ukr.attachment_fasilitas++;

        let html = `
            <div class="card-facility" data-id="${idx}">
                <div class="nama">
                    <input placeholder="Nama Fasilitas" type="text" class="nama_fasilitas form-control">
                </div>

                <div class="row-kondisi">
                    <input placeholder="Kondisi Fasilitas" type="text"class="kondisi_fasilitas form-control">
                    <input placeholder="Jumlah" onchange="if (this.value < 0) this.value = 0;" type="number" class="jumlah_fasilitas form-control">
                </div>

                <div class="row-action">
                    <div class="upload-box">
                        <span class="nama-file">Upload Attachment</span>
                        <div class="upload-icon ml-auto" onclick="ukr.upload_attachment_clearance(this,event)">
                            <i class="fa fa-upload"></i>
                        </div>
                        <div class="data_attachment"></div>
                    </div>

                    <button class="btn btn-primary" onclick="ukr.addFasilitas_insert(this,event)">
                        <i class="fa fa-plus"></i>
                    </button>

                    <button class="btn btn-danger" onclick="ukr.removeFasilitas(this,event)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $(elm).closest('.form-group').append(html);
    },

    upload_attachment_clearance: (elm,e) => {

        e.preventDefault();

        let card = $(elm).closest('.card-facility');
        let id = card.attr('data-id');
        let uploader = $('<input type="file" accept=".jpg,.jpeg,.png,.pdf">');

        uploader.trigger('click');

        uploader.on('change',function(){

            let file = this.files[0];

            if(!file){
                return;
            }

            let reader = new FileReader();

            reader.onload = function(e){

                let html = '';

                if(file.type.startsWith('image/')){
                    html = `
                        <div class="text-center">
                            <img src="${e.target.result}"
                                class="img-fluid rounded"
                                style="max-height:400px">
                        </div>
                    `;
                }else if(file.type === 'application/pdf'){

                    html = `
                        <iframe src="${e.target.result}" width="100%" height="500px"></iframe>
                    `;

                }

                bootbox.dialog({
                    title:'Preview Attachment',
                    message:html,
                    size:'large',
                    buttons:{
                        cancel:{
                            label:'Tutup',
                            className:'btn-secondary'
                        },
                        confirm:{
                            label:'Gunakan File',
                            className:'btn-primary',
                            callback:function(){
                                // simpan file ke object
                                ukr.attachment_fasilitas_files[id] = file;
                                card.find('.nama-file').text(file.name);
                            }
                        }
                    }
                });
            };

            reader.readAsDataURL(file);
        });

    },

    load_attachment_fasilitas: () => {

        ukr.attachment_fasilitas_files = {};

        let promises = [];

        $('.card-facility').each(function(){

            let card = $(this);

            let id = card.data('id');
            let file_path = card.data('file-path');
            let nama_file = card.data('file-name');

            if(file_path){

                let promise = fetch(file_path)
                    .then(res => res.blob())
                    .then(blob => {

                        let file = new File(
                            [blob],
                            nama_file,
                            {
                                type: blob.type
                            }
                        );

                        ukr.attachment_fasilitas_files[id] = file;

                        card.find('.nama-file').html(`
                            <a href="javascript:void(0)"
                            onclick="ukr.show_attachment(this, event)"
                            src="${file_path}">
                                ${nama_file}
                            </a>
                        `);

                        card.find('.data_attachment')
                            .data('file', file);

                        return file; // tambahkan ini
                    });

                promises.push(promise);

            }

        });

        return Promise.all(promises);

    },

    showDetailUsulan: (elm, e) => {

        let params ={
            id : $(elm).attr("id_data"),
        }

        $.ajax({
            url : 'hris/UsulanKaryawanResign/showAttachment',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading();

                bootbox.dialog({
                    title:'Preview Attachment',
                    message: html,
                    size:'large',
                    buttons:{
                        cancel:{
                            label:'Tutup',
                            className:'btn-secondary'
                        },
                    }
                });
               
            },
        });

    },


    revert_status : (elm, e) => {
        let params = {
            id_data : $(elm).attr("id_data"),
            revert : $(elm).attr("revert"),
        }

        $.ajax({
            url: 'hris/UsulanKaryawanResign/revert_status',
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

}

$(document).ready(function(){
    ukr.setting_up();
    ukr.load_data();

    if ($(".edit_clearance").length > 0){
        ukr.load_attachment_fasilitas();
    }
})
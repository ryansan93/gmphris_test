// const { data } = require("autoprefixer");

let mj ={

    start_up : () => {
        $(".select2").select2({
            width: '100%',
        }); 
    },

    add_row: (elm, e) => {

        let html = `
            <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

                <div style="display:flex; flex-direction:row; gap:15px; align-items:center;">

                    <div style="display:flex; flex-direction:column; width:20%;">
                        <label>Kode</label>
                        <input type="text" class="form form-control kode_jabatan">
                    </div>

                    <div style="display:flex; flex-direction:column; width:20%;">
                        <label>Nama Jabatan</label>
                        <input type="text" class="form form-control nama_jabatan">
                    </div>

                    <div style="display:flex; flex-direction:column; width:20%;">
                        <label>Level</label>
                        <input type="number" class="form form-control level">
                    </div>

                    <div style="display:flex; flex-direction:column; width:20%;">
                        <label>Kode Document</label>
                        <input type="text" class="form form-control kode_dokumen" style="text-transform: uppercase;">
                    </div>

                    <div style="display:flex; flex-direction:column; width:30%;">
                        <label>Jabatan Atasan</label>
                        <select class="select2 jabatan_atasan">
                            <option value="">Pilih Jabatan Atasan</option>
                        </select>
                    </div>

                    <div style="width:40%; text-align:right">
                        <button class="btn btn-warning" onclick="mj.add_row(this, event);"><span class="fa fa-plus"></span></button>
                        <button class="btn btn-danger" onclick="mj.delete_row(this, event);"><span class="fa fa-close"></span></button>   
                    </div>
                </div>
            </div>`;

        $(".detail_area").append(html);
        mj.start_up();
    },
    
    delete_row : (elm, e) => {

        let dtl = $(".detail_form").length;

        if ( dtl <= 1 ){
            bootbox.alert('Baris tidak boleh lebih dari 1');
        } else {
            $(elm).closest(".detail_form").remove();
        }
    },

    save: (elm, e) => {

        let detail          = [];
        let isValidDetail   = true;

        $(".detail_area").find(".detail_form").each(function(index){

            let nama_jabatan   = $(this).find(".nama_jabatan").val().trim();
            let level          = $(this).find(".level").val().trim();
            let kode_jabatan   = $(this).find(".kode_jabatan").val().trim();
            let jabatan_atasan = $(this).find(".jabatan_atasan").val().trim();
            let kode_dokumen   = $(this).find(".kode_dokumen").val().trim();

            if (nama_jabatan === "") {
                bootbox.alert(`Nama Jabatan pada baris ke-${index + 1} tidak boleh kosong!`);
                $(this).find(".nama_jabatan").focus();
                isValidDetail = false;
                return false;
            }

            if (level === "") {
                bootbox.alert(`Level pada baris ke-${index + 1} tidak boleh kosong!`);
                $(this).find(".level").focus();
                isValidDetail = false;
                return false;
            }

            if (kode_jabatan === "") {
                bootbox.alert(`Kode Jabatan pada baris ke-${index + 1} tidak boleh kosong!`);
                $(this).find(".kode_jabatan").focus();
                isValidDetail = false;
                return false;
            }

            detail.push({
                nama_jabatan: nama_jabatan,
                level: level,
                kode_jabatan: kode_jabatan,
                jabatan_atasan: jabatan_atasan,
                kode_dokumen: kode_dokumen
            });
        });

        if (!isValidDetail) return;

        if (detail.length === 0) {
            return bootbox.alert("Minimal harus ada 1 detail!");
        }

        let params = {
            detail: detail
        };

        $.ajax({
            url: 'hris/MasterJabatan/save',
            type: 'POST',
            data: params,
            dataType: 'json',
            beforeSend: function () {
                showLoading();
            },
            success: function (data) {
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'hris/MasterJabatan';
                });
            }
        });
    },

    load_form : () => {
        $.ajax({
            url : 'hris/MasterJabatan/load_form',
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

    filter_data : () => {
        let params ={
            kategori : $("#kategori").val(),
        }

        $.ajax({
            url : 'hris/MasterJabatan/filter_data',
            data : params,
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

    show_detail :(elm, e) =>{

        let params ={
            id : $(elm).attr("id"),
        }

        $.ajax({
            url : 'hris/MasterJabatan/show_detail',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading();

                let dialog  = bootbox.dialog({
                    title: 'Detail Data', 
                    message: html,
                    size: 'large',
                    buttons: {
                        cancel: {
                            label: 'Tutup',
                            className: 'btn-secondary'
                        },
                        edit: {
                            label: 'Edit',
                            className: 'btn-primary',
                            callback: function () {
                                mj.edit(params);
                            }
                        },
                        delete: {
                            label: 'Hapus',
                            className: 'btn-danger',
                            callback: function () {
  
                                bootbox.confirm('Yakin mau hapus?', function(result) {
                                    if (result) {
                                        mj.delete(params);
                                    }
                                });
                            }
                        }
                    },
                    
                });

               
               
            },
        });
    },

    edit: (elm, e) => {

        let params = {
            kode_jabatan : $(elm).attr("kode_jabatan"),    
        }

        $.ajax({
            url : 'hris/MasterJabatan/edit_data',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading();

                let dialog  = bootbox.dialog({
                    title: 'Detail Data', 
                    message: html,
                    size: 'large',
                    buttons: {
                        cancel: {
                            label: 'Tutup',
                            className: 'btn-secondary'
                        },
                        edit: {
                            label: 'Update',
                            className: 'btn-primary',
                            callback: function () {

                                let modal = $(".bootbox");

                                let data_params = {
                                    kode_jabatan : modal.find(".kode_jabatan").val(),
                                    nama_jabatan : modal.find(".nama_jabatan").val(),
                                    level :  modal.find(".level").val(),
                                    jabatan_atasan : modal.find(".jabatan_atasan").val(),
                                    kode_dokumen : modal.find(".kode_dokumen").val()
                                }

                                // console.log(data_params);
                                mj.update(data_params);
                            }
                        },
                    }
                });

                dialog.on('shown.bs.modal', function () {
                    $(".select2").select2();
                });
               
            },
        });
    },

    changeTabActive: () => {
        $('a[href="#action"]').tab('show');
    },


    update: (data_params)  => {

        $.ajax({
            url : 'hris/MasterJabatan/update',
            data : data_params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    // window.location.href = 'hris/Ma';

                    mj.load_form();
                });
               
            },
        });
    },


    delete: (elm, e) =>{

        let params = {
            kode_jabatan : $(elm).attr('kode_jabatan'),
        }

        bootbox.confirm('Yakin mau hapus?', function(result) {
            if (result) {
                $.ajax({
                    url : 'hris/MasterJabatan/delete',
                    data : params,
                    type : 'POST',
                    dataType : 'json',
                    beforeSend : function(){ 
                        showLoading(); 
                    },
                    success : function(data){
                        hideLoading();

                        bootbox.alert(data.message, function () {
                            window.location.href = 'hris/MasterJabatan'; 
                        });
                    },
                });
            }
        });
    },


    filterall: (elm, e) => {

        let params = {
            search : $(elm).val(),
        }   

        console.log(params.search);

        $(".list_data").find("table tbody tr").each(function(index){
            let nama_jabatan = $(this).find(".nama_jabatan").text().toLowerCase();
            let kode_jabatan = $(this).find(".kode_jabatan").text().toLowerCase();
            let level = $(this).find(".level").text().toLowerCase();


            if(nama_jabatan.includes(params.search.toLowerCase()) || kode_jabatan.includes(params.search.toLowerCase()) || level.includes(params.search.toLowerCase())){
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    },
}




$(document).ready(function() {
    mj.load_form();
    mj.start_up();
});


let hf = {
    add_row: (elm, e) => {

        let selectPengusul = $(elm).closest('.detail_form').find('.usulan').html(); 

        let html = `
            <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

                <div style="display:flex; flex-direction:row; gap:20px; align-items:center;">
                    <label style="width:10%;">Nama karyawan</label>
                    <input type="text" class="form form-control nama_kategori" style="width:30%;">
                    
                    <div style="width:10%; text-align:right">
                        <button class="btn btn-warning" onclick="hf.add_row(this, event);"><span class="fa fa-plus"></span></button>
                        <button class="btn btn-danger" onclick="hf.delete_row(this, event);"><span class="fa fa-close"></span></button>   
                    </div>
                </div>
                
            </div>`;

        $(".detail_area").append(html);
    },

    delete_row : (elm, e) => {

        let dtl = $(".detail_form").length;

        if(dtl <= 1){
            bootbox.alert('Baris tidak boleh lebih dari 1');
        } else {
            $(elm).closest(".detail_form").remove();
        }
    },

    save: (elm, e)  => {

        let detail = [];
        let isValidDetail = true;

        $(".detail_area").find(".detail_form").each(function(){
            let nama_karyawan   = $(this).find(".nama_kategori").val().trim();
            let status_karyawan = $(".status_karyawan").val();
            let usulan          = $(".usulan").val();

            if (nama_karyawan === "") {
                isValidDetail = false;
                bootbox.alert(`Label pada detail ke-${nama_karyawan + 1} tidak boleh kosong!`);
                return false;
            }

            if (usulan === null) {
                isValidDetail = false;
                bootbox.alert(`Usulan wajib di isi`);
                return false;
            }

            detail.push({
                nama_karyawan: nama_karyawan,
                status_karyawan: status_karyawan,
                usulan : usulan,
            });
        });

        if (!isValidDetail) return;

        if (detail.length === 0) {
            return bootbox.alert("Minimal harus ada 1 detail!");
        }

        let params = {
            detail : detail,
        }

        $.ajax({
            url : 'hris/HrisKandidatBaru/save',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'hris/HrisKandidatBaru';
                });
            },
        });
    },

    load_form : () => {
        $.ajax({
            url : 'hris/HrisKandidatBaru/load_form',
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

    copy_link:(elm, e) => {
        e.preventDefault();

        let url = $(elm).attr("url");
        // console.log(url)
        
        navigator.clipboard.writeText(url)
        .then(() => {
            toastr.info("Link berhasil disalin!");
        })
        .catch(() => {
            toastr.info("Gagal copy link");
        });
    },

    generate_form_karyawan_baru : (id_data, callback) =>{
       $.ajax({
            url : 'hris/HrisKandidatBaru/generate_form_karyawan_baru',
            data : {
                id_data : id_data,
            },
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                // showLoading(); 
            },
            success : function(html){
                hideLoading();

                callback(html);
            },
        });
    },


    keputusan_akhir: (elm, e, val) => {
        let params = {
            keputusan: val,
            id_data: $(elm).attr("id_data"),
        };

        if (params.keputusan == 1) {
            
          hf.generate_form_karyawan_baru(params.id_data ,function(html) {

                let dialog = bootbox.dialog({
                    title: "Masukkan Tanggal Masuk Kerja",
                    size: 'large',
                    message: `
                        <div class="input-group date datetimepicker" id="tgl_masuk">
                            <input type="text" class="form-control text-center" placeholder="Tanggal Masuk Kerja" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    ` + html,
                    buttons: {
                        cancel: {
                            label: 'Batal',
                            className: 'btn-secondary'
                        },
                        confirm: {
                            label: 'Simpan',
                            className: 'btn-primary',
                            callback: function () {
                                let picker = $('#tgl_masuk').data('DateTimePicker');
                                let date = picker ? picker.date() : null;

                                if (!date) {
                                    bootbox.alert("Tanggal wajib diisi!");
                                    return false;
                                }

                                let tgl     = dateSQL(date);
                                let level   = $('select.level').val();
                                let atasan  = $('select.atasan').val();
                                let nama    = $('input.nama_pegawai').val();
                                let wilayah = $('select.wilayah').select2().val();
                                let koordinator = $('select.koordinator').val();
                                let marketing   = $('select.marketing').val();
                                let unit        = $('select.unit').select2().val();
                                let jabatan     = $('input.jabatan').attr("value_fix");

                                params.tgl_masuk = tgl;
                                params.level = level;
                                params.atasan = atasan;
                                params.nama = nama;
                                params.wilayah = wilayah;
                                params.koordinator = koordinator;
                                params.marketing = marketing;
                                params.unit = unit;
                                params.jabatan = jabatan;

                                hf.exec_keputusan_akhir(params);
                            }
                        }
                    }
                });

                dialog.on('shown.bs.modal', function () {
                    $('#tgl_masuk').datetimepicker({
                        locale: 'id',
                        format: 'DD MMM YYYY'
                    });

                    $(".select2").select2();

                    $('.wilayah').select2();
                    $('.wilayah').next('span.select2').css('width', '100%');

                    $('.unit').select2();
                    $('.unit').next('span.select2').css('width', '100%');
                });

            });
        } else if (params.keputusan == 2) {
            bootbox.confirm({
                message: "Apakah anda yakin ingin reject kandidat ini? <br><br> <textarea class='form form-control ket-reject' rows='3'></textarea>",
                buttons: {
                    confirm: {
                        label: 'Ya',
                        className: 'btn-danger'
                    },
                    cancel: {
                        label: 'Batal',
                        className: 'btn-secondary'
                    }
                },
                callback: function (result) {
                    if (result) {
                        // console.log(params);

                        let keterangan_reject = $('.ket-reject').val();
                        params.keterangan_reject = keterangan_reject;
                        hf.exec_keputusan_akhir(params)
                    }
                }
            });
        }
    },

    exec_keputusan_akhir: (params) =>{
        // console.log(params);
        // return false;


         $.ajax({
            url : 'hris/HrisKandidatBaru/exec_keputusan_akhir',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();
                // console.log(resp)   
                 bootbox.alert(data.message, function () {
                    window.location.href = 'hris/HrisKandidatBaru';
                });        
            },
        });
    },

    set_atasan : function (jabatan, tipe=null) {
		var div = $('div.body');

		$.ajax({
            url : 'hris/HrisKandidatBaru/get_atasan',
            data : {'jabatan' : jabatan},
            type : 'POST',
            dataType : 'JSON',
            beforeSend : function(){},
            success : function(data){
                if (data.status) {
                	if ( !empty(data.content) ) {
                		var select = $(div).find('select.atasan');
                		var option = "<option value=''>-- Pilih Atasan --</option>";
                		for (var i = 0; i < data.content.length; i++) {
                			var urut = null;
                			if ( data.content[i].jabatan == 'koordinator' ) {
                				urut = data.content[i].kordinator;
                				option += "<option value='"+data.content[i].id+"'>"+data.content[i].jabatan.toUpperCase() + ' (' + urut + ') <b>|</b> ' + data.content[i].nama+"</option>";
                			} else if ( data.content[i].jabatan == 'marketing' ) {
                				urut = data.content[i].marketing;
                				option += "<option value='"+data.content[i].id+"'>"+data.content[i].jabatan.toUpperCase() + ' (' + urut + ') <b>|</b> ' + data.content[i].nama+"</option>";
                			} else {
                				option += "<option value='"+data.content[i].id+"'>"+data.content[i].jabatan.toUpperCase() + ' <b>|</b> ' + data.content[i].nama+"</option>";
                			};
                		};

                		$(select).html(option);

                		if ( tipe == 'edit' ) {
                			var id_atasan = $(select).data('atasan');
                			$(select).find('option[value="'+id_atasan+'"]').prop('selected', true);
                		};
                	};
                }
            }
        });
	}, // end - set_atasan

    show_detail : () => {
        
    },
}

$(document).ready(function() {
    // app.init();
    hf.load_form();

    $('.select2').select2();
});

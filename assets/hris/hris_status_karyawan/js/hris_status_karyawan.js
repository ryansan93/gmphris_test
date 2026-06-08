
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

        let params = {};
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('kode')) {
            params.kode = urlParams.get('kode');
        }


        $.ajax({
            url : 'hris/HrisStatusKaryawan/load_form',
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

    update_status: (elm, e) => {

        let params = {
            id_data : $(elm).attr("id_data"),
            tgl_selesai : $(elm).attr("tgl_selesai"),
        };

        let kategoriSaatIni = $(elm).attr('last_kategori'); // HRIS/K/005

        if (!kategoriSaatIni) {
            console.log('kategori tidak ada');
            return;
        }

        let pecah       = kategoriSaatIni.split('/');
        let lastNumber  = parseInt(pecah[pecah.length - 1]); 
        let nextNumber  = String(lastNumber + 1).padStart(3, '0'); 

        let optionKategori = '';

        $('.kategori option').each(function () {

            let val         = $(this).val();
            let duration    = $(this).attr("duration");

            if (!val || !duration) {
                return;
            }

            let kategoriBagian = val.split('/');
            let nomorKategori = parseInt(kategoriBagian[kategoriBagian.length - 1]);

            if (nomorKategori <= lastNumber) {
                return;
            }

            let isSelected = val.includes('/' + nextNumber) ? 'selected': '';

            optionKategori += `
                <option duration="${duration}" value="${val}" ${isSelected}>
                    ${$(this).text()}
                </option>
            `;
        });



        let dialog = bootbox.dialog({
            title: "Update Kategori",
            size: 'large',
            message: `
                <div>
                    <label>Kategori</label>
                    <select class="form-control kategori_baru">
                        ${optionKategori}
                    </select>
                </div>

                <br>
                <label>Tanggal Berlaku</label>
                <div class="input-group date datetimepicker" id="tgl_berlaku">
                    <input type="text" class="form-control text-center" placeholder="Tanggal Berlaku" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>

                <br>
                <label>Alasan</label>
                <textarea class="form form-control alasan_new" rowspan="3"></textarea>
                
            `,
            buttons: {
                cancel: {
                    label: 'Batal',
                    className: 'btn-secondary'
                },
                confirm: {
                    label: 'Simpan',
                    className: 'btn-primary',
                    callback: function () {

                        let picker = $('#tgl_berlaku').data('DateTimePicker');
                        let date = picker ? picker.date() : null;

                        if (!date) {
                            bootbox.alert("Tanggal wajib diisi!");
                            return false;
                        }

                        params.tgl_berlaku  = dateSQL(date);
                        params.kategori     = $('.kategori_baru').val();
                        params.alasan       = $('.alasan_new').val();
                        params.duration     = $('.kategori_baru option:selected').attr('duration');

                        up.exec_update_status(params);
                    }
                }
            }
        });

        dialog.on('shown.bs.modal', function () {

            $('.kategori_baru').select2({
                dropdownParent: $('.bootbox')
            });

            $('#tgl_berlaku').datetimepicker({
                locale: 'id',
                format: 'DD MMM YYYY',
                minDate: moment(params.tgl_selesai, 'YYYY-MM-DD')
            });
        });

    },

    exec_update_status: (params) => {

        // console.log(params);
        // return false;

         $.ajax({
            url : 'hris/HrisStatusKaryawan/update_status',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    
                    // up.load_form();
                    // $('a[href="#riwayat"]').tab('show');

                    window.location.reload(true);
                });
            },
            error : function(xhr){
                hideLoading();
                toastr.error("Terjadi kesalahan saat menyimpan data");
            }
        });

    },

  
    changeTabActive: () => {
        $('a[href="#action"]').tab('show');
    },


    filter_data: () => {

        let picker_awal = $('#tgl_awal').data('DateTimePicker');
        // let picker_akhir = $('#tgl_akhir').data('DateTimePicker');

        let tgl_awal = ''; 
        // let tgl_akhir = '';

        if (picker_awal && picker_awal.date()) {
            tgl_awal = picker_awal.date().format('YYYY-MM-DD');
        } 

        // if (picker_akhir && picker_akhir.date()) {
        //     tgl_akhir = picker_akhir.date().format('YYYY-MM-DD');
        // } 

        if (!tgl_awal) {
            bootbox.alert('Tanggal tidak boleh kosong.');
            return;
        }

        let params = {
            tanggal : tgl_awal,
            // enddate : tgl_akhir,
            nik : $(".nik").val(),
        };

        // console.log(params)

        $.ajax({
            url : 'hris/HrisStatusKaryawan/filter_data',
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

    
    cetak_data_pdf: () => {

        let kode = [];

        $(".table-list tbody tr").each(function () {
            let temp = $(this).attr("id_data");
            kode.push(temp);
        });

        $.ajax({
            url: 'hris/HrisStatusKaryawan/cetak_data_pdf',
            type: 'POST',
            dataType: 'json',
            data: { id: kode },
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

    
    let $pengusul = $('.pengusul');
    let $karyawan = $('.karyawan');
    let $perwakilan_tujuan = $('.perwakilan_tujuan');


    
    
    
    
});


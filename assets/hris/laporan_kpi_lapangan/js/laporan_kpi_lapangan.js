let option_karyawan = [];

let kpi = {
    
    setting_up: () => {
        $(".select2").select2({
            closeOnSelect: true
        });

        $("#tahun").trigger('change');
    },


    filter_data: () => {
        // 1. Simpan ID tab yang sedang aktif sebelum request AJAX (contoh: '#ppl' atau '#penimbang')
        
        let params = {
            tahun : $("#tahun").val() ?? null,
            bulan : $("#bulan").val() ?? null,
            jabatan : $("#jabatan").val(),
        }

        if (!params.jabatan || params.jabatan === "") {
            alert("Silakan pilih jabatan terlebih dahulu.");
            $("#jabatan").focus();
            return;
        }
            
        $.ajax({
            url : 'hris/LaporanKpiLapangan/filter_data',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading(); 

                // 2. Replace HTML seperti biasa
                $(".tbl-laporan-kpi").html(html);

            },
            error: function() {
                hideLoading();
                alert("Terjadi kesalahan saat memuat data.");
            }
        });
    },


    search_datatable: (elm) => {
        // 1. Ambil nilai input, hapus spasi depan/belakang, dan ubah ke huruf kecil
        let value = $(elm).val().toLowerCase().trim();
        let $fieldset = $(elm).closest('fieldset');
        let $rows = $fieldset.find('.gmp-table tbody tr');

        // Jika input kosong, tampilkan semua baris
        if (value === "") {
            $rows.show();
            return;
        }

        // --- PASS 1: Evaluasi Baris Data (Bukan Header) ---
        $rows.each(function() {
            let $row = $(this);
            let isHeader = $row.find('td[colspan]').length > 0;

            if (!isHeader) {
                // Simpan status match di data attribute agar bisa dibaca di Pass 2
                if ($row.text().toLowerCase().indexOf(value) > -1) {
                    $row.data('match', true);
                    $row.show(); // Tampilkan sementara
                } else {
                    $row.data('match', false);
                    $row.hide(); // Sembunyikan sementara
                }
            }
        });

        // --- PASS 2: Evaluasi Header Wilayah & Sinkronisasi dengan Anaknya ---
        $rows.each(function() {
            let $row = $(this);
            let isHeader = $row.find('td[colspan]').length > 0;

            if (isHeader) {
                let headerTextMatch = $row.text().toLowerCase().indexOf(value) > -1;
                let hasMatchingChild = false;
                let $next = $row.next();
                
                // Cek apakah ada anak yang match
                while ($next.length > 0 && $next.find('td[colspan]').length === 0) {
                    if ($next.data('match')) {
                        hasMatchingChild = true;
                    }
                    $next = $next.next();
                }

                // LOGIKA TAMPILAN:
                if (headerTextMatch) {
                    // Jika Header cocok (misal user ketik "Malang"), tampilkan Header 
                    // DAN paksa semua anak-anaknya untuk muncul
                    $row.show();
                    let $child = $row.next();
                    while ($child.length > 0 && $child.find('td[colspan]').length === 0) {
                        $child.show();
                        $child = $child.next();
                    }
                } 
                else if (hasMatchingChild) {
                    // Jika Header tidak cocok, tapi ada anak yang cocok, tampilkan Header saja sebagai penanda
                    $row.show();
                } 
                else {
                    // Jika tidak ada yang cocok, sembunyikan Header
                    $row.hide();
                }
            }
        });
    },


    export_excel : (elm) => {
      
      
        let $tbody = $('.gmp-table tbody');
        let $rows = $tbody.find('tr');

        // 2. Cek apakah tabel benar-benar kosong
        let isEmpty = false;

        if ($rows.length === 0) {
            isEmpty = true; // Tidak ada baris sama sekali
        } else if ($rows.length === 1) {
            // Cek jika hanya ada 1 baris dan isinya pesan "Tidak ada data"
            let text = $rows.first().text().trim().toLowerCase();
            if (text.includes('tidak ada data')) {
                isEmpty = true; 
            }
        }

        // 3. Tampilkan Bootbox & Batalkan jika kosong
        if (isEmpty) {
            bootbox.alert({
                message: "<i class='fa fa-exclamation-triangle text-warning'></i> <b>Data kosong!</b><br>Tidak ada data untuk di-export pada periode ini.",
                title: "Peringatan",
                callback: function() { /* opsional */ }
            });
            return; // Hentikan fungsi, jangan lanjut ke export
        }

        // 4. Lanjutkan Proses Export
        let params = {
            jabatan  : $(elm).attr('jabatan'),
            tahun : $("#tahun").val() ?? null,
            bulan : $("#bulan").val() ?? null,
        }

        let query = $.param(params);
        window.location.href = 'hris/LaporanKpiLapangan/export_excel?' + query;
    },



    show_detail_rhpp: (elm) => {

        let params = {
            nama : $(elm).attr("nama"),
            jabatan : $(elm).attr("jabatan"),
            tahun : $("#tahun").val() ?? null,
            bulan : $("#bulan").val() ?? null,
        }

        $.ajax({
            url : 'hris/LaporanKpiLapangan/show_detail_rhpp',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading(); 

                bootbox.dialog({
                    title: 'Detail Data RHPP', 
                    message: html,
                    size: 'large',
                    buttons: {
                        cancel: {
                            label: 'Tutup',
                            className: 'btn-secondary'
                        },
                    },
                    
                });
            },
            error: function() {
                hideLoading();
                alert("Terjadi kesalahan saat memuat data.");
            }
        });

    }
   
}

$(document).ready(function() {

    kpi.setting_up();
    

});


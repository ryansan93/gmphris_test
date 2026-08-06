let so = {

    settingUp : () => {

        $(".select2").select2();
        
        const addedDivs = [];

        document.querySelectorAll('.one_child').forEach(el => {

            el.parentNode.style.position = 'relative';
            const line = document.createElement('span');
            line.style.position = 'absolute';
            line.style.marginLeft = '-10px';
            el.parentNode.insertBefore(line, el);
            addedDivs.push(line);

        });

        // hapus border-top hanya untuk one_child
        const style = document.createElement('style');
        style.innerHTML = `
            .tree li.one_child::before {
                border-top: none !important;
                border-radius: 0 !important;
            }
        `;
        document.head.appendChild(style);
    },

    filterStructur: () => {

        let params = {
            level_max : $(".levelMax").val(),
            unit : $(".unit").val(),
            wilayah : $(".perwakilan").val(),
        }

        $.ajax({
            url : 'hris/StrukturOrganisasi/filterStruktur',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading(); 

                $(".so-area").html(html);

                const addedDivs = [];
                document.querySelectorAll('.one_child').forEach(el => {
                    el.parentNode.style.position = 'relative';
                    const line = document.createElement('span');
                    line.style.position = 'absolute';
                    el.parentNode.insertBefore(line, el);
                    addedDivs.push(line);
                });

                const style = document.createElement('style');
                style.innerHTML = `
                    .tree li.one_child::before {
                        border-top: none !important;
                        border-radius: 0 !important;
                    }
                `;
                document.head.appendChild(style);
                
            },
        });

    },

    resetFilter: () => {
        $(".levelMax").val('').trigger('change');
        $(".perwakilan").val('').trigger('change');
        $(".unit").val('').trigger('change');
        so.filterStructur();
    },

    export_excel : () => {

        let params = {
            level_max : $(".levelMax").val() ?? 7,
        }

        let query = new URLSearchParams(params).toString();

        window.location.href = 'hris/StrukturOrganisasi/exportExcel?' + query;

    },


    printTreePdf : () => {

        let level = parseInt($(".levelMax").val()) || 7;

        if(level > 5){

            bootbox.confirm({

                title: "Konfirmasi Export PDF",

                message: `
                    Struktur organisasi sampai level ${level}. 
                    Ukuran PDF yang dihasilkan cukup besar dan proses export 
                    mungkin membutuhkan waktu lebih lama.<br><br>
                    Apakah tetap lanjut export?
                `,

                buttons:{
                    confirm:{
                        label:'Ya, Export',
                        className:'btn-primary'
                    },
                    cancel:{
                        label:'Batal',
                        className:'btn-secondary'
                    }
                },

                callback:function(result){

                    if(result){
                        showLoading;
                        so.exec_exportPdf_lv_big();
                    }

                }

            });

        } else {

            so.exec_exportPdf_lv_sm();

        }

    },


    exec_exportPdf_lv_big : () => {

        const element = document.getElementById("tree");
        element.classList.add("export-pdf");

        const widthPx = element.scrollWidth;
        const heightPx = element.scrollHeight;

        const oldOverflow = element.style.overflow;

        element.style.overflow = "visible";
        

        html2canvas(element, {
            scale: 1,
            useCORS: true,
            scrollX: 0,
            scrollY: 0,
            width: widthPx,
            height: heightPx
        }).then(canvas => {

            const imgData = canvas.toDataURL('image/png', 1.0);

            const { jsPDF } = window.jspdf;

            const pdf = new jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: [
                    widthPx * 0.264583 * 0.5,
                    heightPx * 0.264583 * 1
                ]
            });


            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = canvas.height * pdfWidth / canvas.width;


            const headerHeight = 30;

            const oldPdfHeight = pdf.internal.pageSize.getHeight();

            pdf.setFont("helvetica", "bold");
            pdf.setFontSize(18);
            pdf.text("STRUKTUR ORGANISASI", pdfWidth / 2, 12, {
                align: "center"
            });

            pdf.setFont("helvetica", "normal");
            pdf.setFontSize(15);
            pdf.text("PT. Griya Mitra Poultry", pdfWidth / 2, 20, {
                align: "center"
            });

            pdf.setFont("courier", "normal");
            pdf.setFontSize(10);
            pdf.text("JL. GAJAHMADA GANG XVIII NO.14 KALIWATES KAB JEMBER, JAWA TIMUR", pdfWidth / 2, 26, {
                align: "center"
            });

            pdf.setLineWidth(0.5);
            pdf.line(
                10,
                30,
                pdfWidth - 10,
                30
            );

            pdf.addImage(
                imgData,
                'PNG',
                0,
                headerHeight,
                pdfWidth,
                pdfHeight
            );


            const filename = 'Struktur Organisasi PT. Griya Mitra Poultry_' + Date.now() + '.pdf';
            pdf.save(filename);


            element.style.overflow = oldOverflow;
            element.classList.remove("export-pdf");

        });

    },

    // exec_exportPdf_lv_sm : () => {

    //     const element = document.getElementById("tree");

    //     const widthPx = element.scrollWidth;
    //     const heightPx = element.scrollHeight;

    //     const oldOverflow = element.style.overflow;
    //     const oldWidth = element.style.width;
    //     const oldHeight = element.style.height;

    //     element.style.overflow = 'visible';
    //     element.style.width = widthPx + 'px';
    //     element.style.height = heightPx + 'px';

    //     html2canvas(element, {
    //         scale: 2,
    //         useCORS: true,
    //         width: widthPx,
    //         height: heightPx,
    //         scrollX: 0,
    //         scrollY: 0
    //     }).then(canvas => {

    //         const imgData = canvas.toDataURL('image/jpeg', 1.0);

    //         const pdf = new jspdf.jsPDF({
    //             orientation: 'landscape',
    //             unit: 'mm',
    //             format: [
    //                 widthPx * 0.264583,
    //                 heightPx * 0.264583
    //             ]
    //         });

    //         const pageWidth = pdf.internal.pageSize.getWidth();
    //         const pageHeight = canvas.height * pageWidth / canvas.width;

    //         pdf.setFont("helvetica", "bold");
    //         pdf.setFontSize(18);
    //         pdf.text("STRUKTUR ORGANISASI", pageWidth / 2, 15, {
    //             align: "center"
    //         });

    //         pdf.setFont("helvetica", "normal");
    //         pdf.setFontSize(12);
    //         pdf.text("PT. Griya Mitra Poultry", pageWidth / 2, 23, {
    //             align: "center"
    //         });

    //         pdf.addImage(
    //             imgData,
    //             'JPEG',
    //             0,
    //             0,
    //             pageWidth,
    //             pageHeight
    //         );

    //         const filename = 'Struktur Organisasi PT. Griya Mitra Poultry_'  + Date.now() + '.pdf';
    //         pdf.save(filename); 


    //         // kembalikan style awal
    //         element.style.overflow = oldOverflow;
    //         element.style.width = oldWidth;
    //         element.style.height = oldHeight;

    //     });

    // }

    exec_exportPdf_lv_sm : () => {

        const element = document.getElementById("tree");
        
        
        const widthPx = element.scrollWidth;
        const heightPx = element.scrollHeight;

        const oldOverflow = element.style.overflow;
        const oldWidth = element.style.width;
        const oldHeight = element.style.height;

        element.style.overflow = 'visible';
        element.style.width = widthPx + 'px';
        element.style.height = heightPx + 'px';

        // console.log(document.querySelectorAll('.one_child').length);

        // const addedDivs = [];
        // document.querySelectorAll('.one_child').forEach(el => {
        //     el.parentNode.style.position = 'relative';
        //     const line = document.createElement('span');
        //     // line.textContent = '|';
        //     line.style.position = 'absolute';
        //     el.parentNode.insertBefore(line, el);
        //     addedDivs.push(line);
        // });

        // const style = document.createElement('style');
        // style.innerHTML = `
        //     .tree li.one_child::before {
        //         border-top: none !important;
        //     }
        // `;
        // document.head.appendChild(style);

        html2canvas(element, {
            scale: 2,
            useCORS: true,
            width: widthPx,
            height: heightPx,
            scrollX: 0,
            scrollY: 0
        }).then(canvas => {

            const imgData = canvas.toDataURL('image/jpeg', 1.0);

            const headerHeight = 30; 

            const pdf = new jspdf.jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: [
                    widthPx * 0.264583,
                    (heightPx * 0.264583) + headerHeight
                ]
            });

            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = canvas.height * pageWidth / canvas.width;

            pdf.setFont("helvetica", "bold");
            pdf.setFontSize(18);
            pdf.text("STRUKTUR ORGANISASI", pageWidth / 2, 12, {
                align: "center"
            });

            pdf.setFont("helvetica", "normal");
            pdf.setFontSize(15);
            pdf.text("PT. Griya Mitra Poultry", pageWidth / 2, 20, {
                align: "center"
            });

            pdf.setFont("courier", "normal");
            pdf.setFontSize(10);
            pdf.text("JL. GAJAHMADA GANG XVIII NO.14 KALIWATES KAB JEMBER, JAWA TIMUR", pageWidth / 2, 26, {
                align: "center"
            });

            pdf.setLineWidth(0.5);
            pdf.line(
                10,              // kiri
                30,              // posisi Y
                pageWidth - 10,  // kanan
                30               // posisi Y sama
            );


            pdf.addImage(
                imgData,
                'JPEG',
                0,
                headerHeight,
                pageWidth,
                pageHeight
            );

            const filename = 'Struktur Organisasi PT. Griya Mitra Poultry_'  + Date.now() + '.pdf';
            pdf.save(filename); 


            element.style.overflow = oldOverflow;
            element.style.width = oldWidth;
            element.style.height = oldHeight;

            

        });

    }
}

$(document).ready(function(){

    so.settingUp();

});
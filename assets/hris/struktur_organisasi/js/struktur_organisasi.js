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
            level_max   : $(".levelMax").val(),
            unit        : $(".unit").val(),
            wilayah     : $(".perwakilan").val(),
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

        let level   = parseInt($(".levelMax").val()) || 7;
        let unit    = $(".unit").val();
        let wilayah = $(".perwakilan").val();

        if (unit != '') {
            so.exec_exportPdf_lv_sm();
        } else if (wilayah != '') {
            so.exec_exportPdf_lv_sm();
        } else if (level > 5) {
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


    exec_exportPdf_lv_big: function () {
    const td = window.__treeData;
    if (!td || !td.nodes || !td.nodes.length) {
        alert("Data tree kosong. Tidak ada yang bisa di-export.");
        return;
    }

    const { nodes, rootLevel } = td;
    const NODE_W = td.NODE_W || 220;
    const NODE_H = td.NODE_H || 210;
    const PAD = 20;

    // ==== 1. BOUNDING BOX ====
    const xs = nodes.map(n => n.x), ys = nodes.map(n => n.y);
    const minX = Math.min(...xs), maxX = Math.max(...xs);
    const minY = Math.min(...ys), maxY = Math.max(...ys);

    const W = (maxX - minX) + NODE_W + PAD * 2;
    const H = (maxY - minY) + NODE_H + PAD * 2;

    // ==== 2. UKURAN HALAMAN ====
    const MM = 25.4 / 96;
    const PTMM = 0.3528;
    const MARGIN = 10, HEADER_H = 32, BOTTOM_PADDING = 80;

    // ✅ S dihitung dari KONTEN saja (margin tidak ikut)
    const MAX_MM = 5000;
    const S = Math.min(1, MAX_MM / (W * MM), MAX_MM / (H * MM));
    const RADIUS = 5 * MM * S;   // proporsional dengan scale

    // ✅ margin ditambahkan SETELAH scale
    const pageW = W * MM * S + MARGIN * 2;
    const pageH = H * MM * S + HEADER_H + MARGIN * 2 + BOTTOM_PADDING;

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({
        orientation: pageW >= pageH ? "landscape" : "portrait",
        unit: "mm",
        format: [Math.min(pageW, pageH), Math.max(pageW, pageH)]
    });

    const finalPageW = pdf.internal.pageSize.getWidth();
    const finalPageH = pdf.internal.pageSize.getHeight();
    console.log(`[PDF] page: ${finalPageW.toFixed(0)} x ${finalPageH.toFixed(0)} mm, scale: ${S.toFixed(3)}, NODE_W: ${NODE_W}`);

    // ==== 3. HELPER KOORDINAT ====
    const mx = v => MARGIN + (v - minX + PAD) * MM * S;
    const my = v => HEADER_H + MARGIN + (v - minY + PAD) * MM * S;

    // ==== HELPER TEKS: wrap + auto-shrink ====
    const wrapDraw = (text, basePt, cx, maxW, topY, maxH, color, bold) => {
        let pt = basePt, lines, lh;
        do {
            pdf.setFont("helvetica", bold ? "bold" : "normal");
            pdf.setFontSize(pt);
            lines = pdf.splitTextToSize(text, maxW);
            if (!Array.isArray(lines)) lines = [lines];
            lh = pt * PTMM * 1.3;
            if (lines.length * lh <= maxH) break;
            pt -= 0.5; // auto-shrink jika tidak muat
        } while (pt > 4.5);
        pdf.setTextColor(color[0], color[1], color[2]);
        pdf.text(lines, cx, topY + lh * 0.8, { align: "center" });
        return { height: lines.length * lh, pt };
    };

    const measureWrapHeight = (text, basePt, maxW, maxH) => {
        let pt = basePt, lines, lh;
        do {
            pdf.setFont("helvetica");
            pdf.setFontSize(pt);
            lines = pdf.splitTextToSize(text, maxW);
            if (!Array.isArray(lines)) lines = [lines];
            lh = pt * PTMM * 1.3;
            if (lines.length * lh <= maxH) break;
            pt -= 0.5;
        } while (pt > 4.5);
        return { height: lines.length * lh, pt };
    };

    // ==== 4. HEADER ====
    pdf.setFont("helvetica", "bold");
    pdf.setFontSize(18);
    pdf.setTextColor(0, 0, 0);
    pdf.text("STRUKTUR ORGANISASI", finalPageW / 2, 12, { align: "center" });

    pdf.setFont("helvetica", "normal");
    pdf.setFontSize(15);
    pdf.text("PT. Griya Mitra Poultry", finalPageW / 2, 20, { align: "center" });

    pdf.setFont("courier", "normal");
    const alamat = "JL. GAJAHMADA GANG XVIII NO.14 KALIWATES KAB JEMBER, JAWA TIMUR";
    let fs = 10;
    pdf.setFontSize(fs);
    while (pdf.getTextWidth(alamat) > finalPageW - 20 && fs > 5) {
        fs -= 0.5;
        pdf.setFontSize(fs);
    }
    pdf.text(alamat, finalPageW / 2, 26, { align: "center" });

    pdf.setLineWidth(0.5);
    pdf.setDrawColor(0, 0, 0);
    pdf.line(10, 30, finalPageW - 10, 30);

    // ==== 5. GARIS KONEKTOR ====
    pdf.setDrawColor(183, 184, 191);
    pdf.setLineWidth(0.4);

    (function drawLines(n) {
        if (!n.children || !n.children.length) return;
        const px   = mx(n.x + NODE_W / 2);
        const bot  = my(n.y + NODE_H);
        const busY = bot + 34 * MM * S;

        pdf.line(px, bot, px, busY);

        const cxs = n.children.map(c => mx(c.x + NODE_W / 2));
        pdf.line(Math.min(...cxs), busY, Math.max(...cxs), busY);

        n.children.forEach(c => {
            const cx = mx(c.x + NODE_W / 2);
            pdf.line(cx, busY, cx, my(c.y));
            drawLines(c);
        });
    })(nodes.find(n => n.level === rootLevel));

    // ==== 6. NODE / KARTU ====
    // ✅ UKURAN FONT DASAR (TIDAK DIKALI S, BIARKAN WRAPDRAW AUTO-SHRINK)
    const FONT_WILAYAH = 5;
    const FONT_UNIT    = 5;
    const FONT_ROLE    = 7;
    const FONT_NAME    = 7;

    nodes.forEach(n => {
        const x = mx(n.x), y = my(n.y);
        const w = NODE_W * MM * S, h = NODE_H * MM * S;

        // kotak luar
        pdf.setFillColor(253, 253, 254);
        pdf.setDrawColor(159, 160, 168);
        pdf.setLineWidth(0.3);
        pdf.roundedRect(x, y, w, h, RADIUS, RADIUS, "FD");

        // kotak dalam
        const boxX = x + 14 * MM * S, boxY = y + 16 * MM * S;
        const boxW = (NODE_W - 28) * MM * S, boxH = 110 * MM * S;
        pdf.setFillColor(255, 255, 255);
        pdf.roundedRect(boxX, boxY, boxW, boxH, RADIUS, RADIUS, "FD");

        const padIn   = 2.5 * MM * S;
        const maxWBox = boxW - padIn * 2;
        const gap     = 2 * MM * S;

        const maxH_wilayah = boxH * 0.45;
        const maxH_unit    = boxH * 0.50;
        
        // Gunakan FONT DASAR untuk kalkulasi tinggi
        const wilayahInfo = measureWrapHeight(n.t || "-", FONT_WILAYAH, maxWBox, maxH_wilayah);
        const unitInfo    = measureWrapHeight("UNIT : " + (n.u || "-"), FONT_UNIT, maxWBox, maxH_unit);

        const totalH = wilayahInfo.height + gap + unitInfo.height;
        const startY = boxY + (boxH - totalH) / 2;

        // Gambar wilayah
        const wilayahY = startY;
        wrapDraw(n.t || "-", FONT_WILAYAH, boxX + boxW / 2, maxWBox,
                wilayahY, maxH_wilayah, [90, 90, 99], true);

        // Gambar unit
        const unitY = wilayahY + wilayahInfo.height + gap;
        wrapDraw("UNIT : " + (n.u || "-"), FONT_UNIT, boxX + boxW / 2, maxWBox,
                unitY, maxH_unit, [124, 124, 133], true);

        // Role
        const maxWCard = w - 8 * MM * S;
        const rTop = y + 140 * MM * S;
        const hR = wrapDraw(n.role || "-", FONT_ROLE, x + w / 2, maxWCard,
                            rTop, 16 * MM * S, [107, 107, 116], true).height;

        // ✅ NAMA (HAPUS n.level agar tidak ada angka di belakangnya)
        const nTop = rTop + hR + 1 * MM * S;
        wrapDraw(n.name || "-", FONT_NAME, x + w / 2, maxWCard,
                nTop, (y + h - 4 * MM * S) - nTop, [107, 107, 116], false);
    });

    // ==== 7. FOOTER ====
    pdf.setFontSize(8);
    pdf.setTextColor(100, 100, 100);
    pdf.text(`Dicetak: ${new Date().toLocaleString("id-ID")}`, MARGIN, finalPageH - 12);

    // ==== 8. SAVE ====
    pdf.save(`Struktur Organisasi PT. Griya Mitra Poultry_${Date.now()}.pdf`);
},
        
        exec_exportPdf_lv_sm: () => {
        const treeEl = document.getElementById("tree");

        // ====== 1. SIMPAN ZOOM SAAT INI ======
        const originalZoom = so.zoomLevel || 1;

        // ====== 2. RESET ZOOM KE 1 SEBELUM TELEPORT ======
        treeEl.style.zoom = "1";
        void treeEl.offsetWidth; // force reflow

        // ✅ BUFFER dinaikkan untuk antisipasi border/shadow
        const BUFFER = 32;
        const widthPx  = parseInt(treeEl.dataset.realWidth  || treeEl.offsetWidth,  10) + BUFFER;
        const heightPx = parseInt(treeEl.dataset.realHeight || treeEl.offsetHeight, 10) + BUFFER;

        // ==== TELEPORT ke container sementara ====
        const stage = document.createElement("div");
        stage.style.cssText = `
            position: fixed; top: 0; left: 0;
            width: ${widthPx}px; height: ${heightPx}px;
            background: #ffffff; z-index: -9999; overflow: visible;
        `;
        document.body.appendChild(stage);

        const parentOrig = treeEl.parentNode;
        const nextSib    = treeEl.nextSibling;
        stage.appendChild(treeEl);
        treeEl.classList.add("export-pdf");
        void treeEl.offsetWidth;

        const scale = widthPx > 5000 ? 1 : 2;

        html2canvas(treeEl, {
            scale,
            useCORS: true,
            backgroundColor: "#ffffff",
            scrollX: 0,
            scrollY: 0,
            width: widthPx,
            height: heightPx,
            windowWidth: widthPx,
            windowHeight: heightPx,
            logging: false
        }).then(canvas => {

            // ==== KEMBALIKAN tree ====
            if (nextSib) parentOrig.insertBefore(treeEl, nextSib);
            else         parentOrig.appendChild(treeEl);
            treeEl.classList.remove("export-pdf");
            document.body.removeChild(stage);

            // ====== 3. KEMBALIKAN ZOOM KE NILAI SEMULA ======
            treeEl.style.zoom = originalZoom;

            const imgData = canvas.toDataURL("image/jpeg", 0.95);
            const { jsPDF } = window.jspdf;

            const PX_TO_MM = 0.264583;
            const MARGIN   = 5;
            const HEADER_H = 32;
            const BOTTOM_PADDING = 20;

            const imgW = (canvas.width  / scale) * PX_TO_MM;
            const imgH = (canvas.height / scale) * PX_TO_MM;
            const pageW = imgW + MARGIN * 2;
            const pageH = imgH + HEADER_H + MARGIN * 2 + BOTTOM_PADDING;

            const pdf = new jsPDF({
                orientation: pageW >= pageH ? "landscape" : "portrait",
                unit: "mm",
                format: [Math.min(pageW, pageH), Math.max(pageW, pageH)]
            });

            const pdfWidth  = pdf.internal.pageSize.getWidth();
            const pdfHeight = pdf.internal.pageSize.getHeight();
            console.log(`[PDF-sm] ${pdfWidth.toFixed(0)} x ${pdfHeight.toFixed(0)} mm, NODE_W=${window.__treeData?.NODE_W}`);

            // ==== HEADER ====
            pdf.setFont("helvetica", "bold");
            pdf.setFontSize(18);
            pdf.text("STRUKTUR ORGANISASI", pdfWidth / 2, 12, { align: "center" });

            pdf.setFont("helvetica", "normal");
            pdf.setFontSize(15);
            pdf.text("PT. Griya Mitra Poultry", pdfWidth / 2, 20, { align: "center" });

            pdf.setFont("courier", "normal");
            const alamat = "JL. GAJAHMADA GANG XVIII NO.14 KALIWATES KAB JEMBER, JAWA TIMUR";
            let size = 10;
            pdf.setFontSize(size);
            while (pdf.getTextWidth(alamat) > pdfWidth - 20 && size > 5) {
                size -= 0.5;
                pdf.setFontSize(size);
            }
            pdf.text(alamat, pdfWidth / 2, 26, { align: "center" });

            pdf.setLineWidth(0.5);
            pdf.line(10, 30, pdfWidth - 10, 30);

            pdf.addImage(imgData, "JPEG", MARGIN, HEADER_H + MARGIN, imgW, imgH, undefined, "FAST");

            pdf.setFontSize(8);
            pdf.text(
                "Dicetak: " + new Date().toLocaleString("id-ID"),
                MARGIN,
                pdfHeight - BOTTOM_PADDING / 2
            );

            pdf.save("Struktur Organisasi PT. Griya Mitra Poultry_" + Date.now() + ".pdf");

        }).catch(err => {
            console.error("Export gagal:", err);

            // ====== KEMBALIKAN ZOOM JUGA DI catch ======
            if (treeEl && originalZoom !== undefined) {
                treeEl.style.zoom = originalZoom;
            }

            if (nextSib) parentOrig.insertBefore(treeEl, nextSib);
            else         parentOrig.appendChild(treeEl);
            treeEl.classList.remove("export-pdf");
            if (stage.parentNode) document.body.removeChild(stage);
            alert("Gagal export PDF: " + err.message);
        });
    },

    
    zoomLevel: 1,

    zoomIn: () => {
        so.zoomLevel -= 0.1;

        if (so.zoomLevel < 0.1) {
            so.zoomLevel = 0.1;
        }

        $('#tree').css({
            zoom: so.zoomLevel
        });
    },

    zoomOut: () => {
        so.zoomLevel += 0.1;

        $('#tree').css({
            zoom: so.zoomLevel
        });
    },

    zoomReset: () => {
        so.zoomLevel = 1;

        $('#tree').css({
            zoom: 1
        });
    },
}

$(document).ready(function(){

    so.settingUp();

    let isDragging = false;
    let startX = 0;
    let startY = 0;
    let startScrollLeft = 0;
    let startScrollTop = 0;

    $('.so-area').on('mousedown', function (e) {

        if (e.which !== 1) return;

        isDragging = true;

        startX = e.pageX;
        startY = e.pageY;

        startScrollLeft = this.scrollLeft;
        startScrollTop = this.scrollTop;

        $(this).addClass('grabbing');

        e.preventDefault();
    });

    $(document).on('mousemove', function (e) {

        if (!isDragging) return;
        const area = $('.so-area')[0];

        if (!area) return;
        const dx = e.pageX - startX;
        const dy = e.pageY - startY;

        area.scrollLeft = startScrollLeft - dx;
        area.scrollTop = startScrollTop - dy;

        e.preventDefault();
    });

    $(document).on('mouseup', function () {

        if (!isDragging) return;
        isDragging = false;

        $('.so-area').removeClass('grabbing');
    });

});
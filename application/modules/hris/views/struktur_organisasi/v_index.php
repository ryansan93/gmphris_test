<fieldset class="filter-panel">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Filter</b>
        </div>
    </legend>

    <div class="filter-row">
        <div class="filter-item">
            <label>Level</label>
            <select class="select2 levelMax" onchange="so.filterStructur(this, event)">
                <option value="">Pilih Level</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
            </select>
        </div>

        <div class="filter-item">
            <label>Perwakilan</label>
            <select class="select2 perwakilan" onchange="so.filterStructur(this, event)">
                <option value="">Pilih Perwakilan</option>
                <?php foreach($perwakilan as $p){ ?>
                    <option value="<?php echo $p['alias'] ?>"><?php echo $p['nama_wilayah'] ?></option>
                <?php } ?>                
            </select>
        </div>

        <div class="filter-item">
            <label>Unit</label>
            <select class="select2 unit" onchange="so.filterStructur(this, event)">
                <option value="">Pilih Unit</option>
                <?php foreach($unit as $u){ ?>
                    <option value="<?php echo $u['alias'] ?>"><?php echo $u['nama_unit'] ?></option>
                <?php } ?>                 
            </select>
        </div>

        <div class="filter-actions">
            <button class="btn btn-secondary" type="button" onclick="so.resetFilter();"><i class="fa fa-undo"></i> Reset Filter</button>
        </div>
    </div>

</fieldset>

<fieldset class="so-fieldset" style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding"><b>Struktur Organisasi</b></div>
    </legend>

    <!-- ✅ 1 container: zoom + petunjuk -->
    <div class="toolbar-area">
        <div class="zoom-area">
            <div onclick="so.zoomIn()"><i class="fa fa-minus" aria-hidden="true"></i></div>
            <div onclick="so.zoomOut()"><i class="fa fa-plus" aria-hidden="true"></i></div>
            <button class="btn btn-secondary" type="button" onclick="so.zoomReset()"><i class="fa fa-undo" aria-hidden="true"></i> Reset</button>
            <button class="btn btn-secondary" onclick="so.printTreePdf();"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>

            <!-- ✅ wrapper search agar turun ke bawah -->
            <div class="search-area">
                <input type="text" id="soSearch" class="form form-control" placeholder="Cari karyawan/nik/jabatan" autocomplete="off" oninput="so.searchData()" onchange="so.searchData()" onkeydown="if(event.key==='Enter'){ event.preventDefault(); so.nextMatch(); }">
                <span id="searchInfo" style="font-size:12px;color:#888"></span>
                <a href="javascript:void(0)" onclick="so.clearSearch()" title="Hapus" style="color:#888;text-decoration:none">✕</a>
            </div>
        </div>
        
        <div class="hint-area">
            💡 Petunjuk: Klik dan tahan gambar, lalu geser untuk melihat area lainnya
        </div>
    </div>

    <div class="col-xs-12 no-padding so-area" style="width:100%; margin-top:10px;">
        <div id="wrap">
            <div id="tree" class="tree"></div>
        </div>
        <!-- script render tree tetap di sini -->
         
        <script>
            const data    = <?php echo json_encode($struktur, JSON_UNESCAPED_UNICODE); ?>;
            const NODE_H  = 210;
            const H_GAP   = 40, V_GAP = 85, PAD = 20;
            const treeEl  = document.getElementById("tree");
            const nodes   = [];
            let cursor    = 0;

            // ✅ ukur lebar teks akurat
            function measureText(text, font) {
                const c = measureText.c || (measureText.c = document.createElement('canvas'));
                const ctx = c.getContext('2d');
                ctx.font = font;
                return ctx.measureText(text).width;
            }

            // ✅ NODE_W auto: seragam, dari konten terpanjang (clamp 180–320)
            function autoNodeWidth(root) {
                const collect = [];
                (function walk(n) { collect.push(n); if (n.children) n.children.forEach(walk); })(root);

                const PAD_INNER = 28;
                let maxW = 180;

                collect.forEach(n => {
                    const wt = measureText(n.t || '-',           'bold 14px "Segoe UI", Arial');
                    const wu = measureText('UNIT : ' + (n.u||'-'), 'bold 10px "Segoe UI", Arial');
                    const wr = measureText(n.role || '-',         'bold 11px "Segoe UI", Arial');
                    const wn = measureText((n.name||'-') + '  -  ' + n.level, '12px "Segoe UI", Arial');
                    maxW = Math.max(maxW, Math.max(wt, wu, wr, wn) + PAD_INNER + 16);
                });

                return Math.min(Math.max(Math.round(maxW), 180), 320);
            }

            const NODE_W = autoNodeWidth(data);
            document.documentElement.style.setProperty('--node-w', NODE_W + 'px');

            function layout(n) {
                if (n.children && n.children.length) {
                    n.children.forEach(layout);
                    const first = n.children[0], last = n.children[n.children.length - 1];
                    n.x = (first.x + last.x) / 2;
                } else {
                    n.x = cursor;
                    cursor += NODE_W + H_GAP;
                }
                n.y = (n.level - data.level) * (NODE_H + V_GAP);
                nodes.push(n);
            }
            layout(data);

            const width  = cursor - H_GAP + PAD * 2;
            const height = Math.max(...nodes.map(n => n.y)) + NODE_H + PAD * 2;
            treeEl.style.width  = width + "px";
            treeEl.style.height = height + "px";

            window.__treeData = {
                nodes: nodes,
                width: width,
                height: height,
                rootLevel: data.level,
                data: data,
                NODE_W: NODE_W,
                NODE_H: NODE_H
            };

            /* garis konektor (SVG) */
            let d = "";
            (function lines(n) {
                if (!n.children || !n.children.length) return;
                const px   = n.x + PAD + NODE_W / 2;
                const busY = n.y + PAD + NODE_H + 34;
                d += `M ${px} ${n.y + PAD + NODE_H} L ${px} ${busY} `;
                const xs = n.children.map(c => c.x + PAD + NODE_W / 2);
                d += `M ${Math.min(...xs)} ${busY} L ${Math.max(...xs)} ${busY} `;
                n.children.forEach(c => {
                    const cx = c.x + PAD + NODE_W / 2;
                    d += `M ${cx} ${busY} L ${cx} ${c.y + PAD} `;
                    lines(c);
                });
            })(data);

            treeEl.innerHTML = `
                <svg id="lines" width="${width}" height="${height}">
                    <path d="${d}" fill="none" stroke="#b7b8bf" stroke-width="1.5"/>
                </svg>`;

            // ==== RENDER NODE + HOVER ATASAN/BAWAHAN ====
            const nodeEls = new Map();

            nodes.forEach(n => {
                const el = document.createElement("div");
                el.className = "node";
                el.style.left = (n.x + PAD) + "px";
                el.style.top  = (n.y + PAD) + "px";
                el.innerHTML = `
                    <div class="box">
                        <div class="t">${n.t}</div>
                        <div class="u">UNIT : ${n.u}</div>
                    </div>
                    <div class="role">${n.role}</div>
                    <div class="name">${n.name}</div>`;
                el.__nodeData = n;
                n.__el = el;  
                nodeEls.set(n, el);
                treeEl.appendChild(el);
            });

            // ✅ referensi atasan (jalan sekali per render)
            (function setParent(n, p) {
                n.__parent = p || null;
                (n.children || []).forEach(c => setParent(c, n));
            })(data);

            // ✅ nyalakan / matikan sorotan
            function paint(n, on) {
                let p = n.__parent;
                while (p) {
                    const pe = nodeEls.get(p);
                    if (pe) pe.classList.toggle("hl-up", on);
                    p = p.__parent;
                }
                (function down(c) {
                    (c.children || []).forEach(k => {
                        const ke = nodeEls.get(k);
                        if (ke) ke.classList.toggle("hl-down", on);
                        down(k);
                    });
                })(n);
            }

            treeEl.addEventListener("mouseover", e => {
                const el = e.target.closest(".node");
                if (el === treeEl.__hover) return;
                if (treeEl.__hover) paint(treeEl.__hover.__nodeData, false);
                treeEl.__hover = el;
                if (el) paint(el.__nodeData, true);
            });

            treeEl.addEventListener("mouseleave", () => {
                if (treeEl.__hover) paint(treeEl.__hover.__nodeData, false);
                treeEl.__hover = null;
            });
        </script>

    </div>
</fieldset>
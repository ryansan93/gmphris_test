<style>
  /* ===== 1. GLOBAL VARIABLES & RESET ===== */
  :root {
    --primary: #4f7cff;
    --primary-soft: #eaf0ff;
    --success: #22c55e;
    --success-soft: #e7f9ee;
    --warn: #f59e0b;
    --warn-soft: #fff6e0;
    --bg-page: #f5f7fb;
    --card: #ffffff;
    --border: #e3e8f0;
    --text: #2c3e50;
    --muted: #7a869a;
    --shadow: 0 4px 14px rgba(30, 45, 80, 0.06);
    --shadow-hover: 0 8px 22px rgba(30, 45, 80, 0.10);
  }


  /* ===== 2. FIELDSET & LEGEND (Card Style) ===== */
  .bootbox fieldset {
    background: var(--card);
    border: 1px solid var(--border);
    /* border-radius: 14px; */
    padding: 22px 20px 20px;
    margin: 0 0 20px;
    box-shadow: var(--shadow);
    transition: box-shadow 0.25s ease;
  }
  .bootbox fieldset:hover {
    box-shadow: var(--shadow-hover);
  }

  .bootbox legend {
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.3px;
    color: var(--primary);
    /* background: var(--primary-soft); */
    padding: 6px 16px;
    /* border-radius: 999px; */
    border: 1px solid #d6e0ff;
  }

  /* Color coding per section */
  /* fieldset.fs-clearance legend { color: var(--success); background: var(--success-soft); border-color: #c9efd7; } */
  /* fieldset.fs-deactivate legend { color: var(--warn); background: var(--warn-soft); border-color: #f5e3b8; } */

  .fieldset-inner {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  /* ===== 3. IMAGE VIEWER (Unified for Detail & Clearance) ===== */
  .viewer-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .main-image-wrapper {
    width: 100%;
    height: 260px;
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    background: #f8f9fc;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .main-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* Atau 'cover' jika ingin memenuhi kotak */
    transition: opacity 0.3s ease;
  }

  .main-image-wrapper .img-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.6));
    color: #fff;
    font-size: 12px;
    padding: 24px 12px 8px;
    font-weight: 500;
    text-align: center;
  }

  .thumb-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .thumb-btn {
    padding: 8px 14px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: #fff;
    font-size: 12px;
    color: var(--text);
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
  }

  .thumb-btn:hover {
    border-color: var(--primary);
    background: var(--primary-soft);
  }

  .thumb-btn.active {
    border-color: var(--primary);
    background: var(--primary);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(79, 124, 255, 0.25);
  }

  /* ===== 4. INFO GRID (Data Clearance) ===== */
  .info-grid {
    display: grid;
    grid-template-columns: 130px 20px 1fr;
    gap: 8px;
    align-items: baseline;
    padding: 10px 14px;
    background: #fbfcff;
    border: 1px solid var(--border);
    border-radius: 8px;
  }

  .info-grid .label {
    font-weight: 600;
    color: var(--text);
    font-size: 13px;
  }

  .info-grid .colon {
    color: var(--muted);
    text-align: center;
  }

  .info-grid .value {
    font-size: 14px;
    color: var(--text);
    font-weight: 500;
    word-break: break-word;
    margin: 0;
  }

  /* ===== 5. SIGNATURE ROWS ===== */
  .section {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .section > small {
    font-size: 12px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    font-weight: 600;
  }

  .sign-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .sign-name, .sign-date {
    border: 1px solid var(--border);
    padding: 10px 12px;
    border-radius: 10px;
    background: #fbfcff;
    font-size: 14px;
    transition: background 0.2s ease, border-color 0.2s ease;
  }

  .sign-name {
    flex: 7;
    min-width: 180px;
    font-weight: 600;
    color: var(--text);
  }

  .sign-date {
    flex: 3;
    min-width: 120px;
    color: var(--muted);
    text-align: right;
    font-variant-numeric: tabular-nums;
  }

  /* Color accents for roles */
  /* .section.ack .sign-name { border-left: 3px solid var(--primary); }
  .section.app .sign-name { border-left: 3px solid var(--success); }
  .section.do  .sign-name { border-left: 3px solid var(--warn); } */

  .sign-row:hover .sign-name,
  .sign-row:hover .sign-date {
    background: #ffffff;
    border-color: #cdd6e6;
  }

  /* ===== 6. RESPONSIVE BREAKPOINTS ===== */
  @media (max-width: 768px) {
    .bootbox fieldset { padding: 18px 14px; }
    
    .main-image-wrapper { height: 220px; }
    
    .info-grid {
      grid-template-columns: 120px 20px 1fr;
    }
  }

  @media (max-width: 500px) {
    .main-image-wrapper { height: 180px; }
    
    .thumb-btn {
      flex: 1 1 calc(50% - 4px);
      text-align: center;
      font-size: 11px;
      padding: 6px 8px;
    }

    /* Stack info grid vertically on mobile */
    .info-grid {
      grid-template-columns: 1fr;
      gap: 4px;
      padding: 12px;
    }
    .info-grid .label {
      font-size: 11px;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }
    .info-grid .colon { display: none; }
    .info-grid .value { font-size: 14px; }

    /* Stack signatures vertically */
    .sign-row { flex-direction: column; }
    .sign-name, .sign-date {
      flex: 1 1 100%;
      width: 100%;
      text-align: left;
    }
  }
</style>

<!-- ========================================== -->
<!-- 1. DATA DETAIL -->
<!-- ========================================== -->
<fieldset>
  <legend>Data Detail</legend>
  <div class="fieldset-inner">

    <div class="viewer-container">
      <div class="main-image-wrapper">
        <img id="detailMainImg" style="cursor:pointer;" onclick="report.show_attachment(this, event)" 
             src="<?php echo isset($lampiran[0]['file_path']) ? base_url() . $lampiran[0]['file_path'] : ''; ?>"
             alt="lampiran">
        <div class="img-label" id="detailImgLabel">
          <?php echo isset($lampiran[0]['nama_file']) ? $lampiran[0]['nama_file'] : 'Tidak ada gambar'; ?>
        </div>
      </div>

      <?php if (isset($lampiran) && count($lampiran) > 1) { ?>
      <div class="thumb-row">
        <?php foreach ($lampiran as $i => $l) { ?>
          <div class="thumb-btn <?php echo $i === 0 ? 'active' : ''; ?>"
               data-src="<?php echo base_url() . $l['file_path']; ?>"
               data-name="<?php echo $l['nama_file']; ?>"
               onclick="switchDetailImage(this, 'detailMainImg', 'detailImgLabel')">
            <?php echo $l['nama_file']; ?>
          </div>
        <?php } ?>
      </div>
      <?php } ?>
    </div>

    <div class="section ack">
      <small>Di Acknowledge oleh</small>
      <div class="sign-row">
        <div class="sign-name"><?php echo !empty($list[0]['ack_by']) ? ucwords(strtolower($list[0]['ack_by'])) : '-' ?></div>
        <div class="sign-date"><?php echo !empty($list[0]['ack_date']) ? tglIndonesia($list[0]['ack_date'], '-', ' ') : '-' ?></div>
      </div>
    </div>

    <div class="section app">
      <small>Di Approve oleh</small>
      <div class="sign-row">
        <div class="sign-name"><?php echo !empty($list[0]['approved_by']) ? ucwords(strtolower($list[0]['approved_by'])) : '-' ?></div>
        <div class="sign-date"><?php echo !empty($list[0]['approve_reject_date']) ? tglIndonesia($list[0]['approve_reject_date'], '-', ' ') : '-' ?></div>
      </div>
    </div>

  </div>
</fieldset>

<!-- ========================================== -->
<!-- 2. DATA CLEARANCE -->
<!-- ========================================== -->
<fieldset class="fs-clearance">
  <legend>Data Clearance</legend>
  <div class="fieldset-inner">

    <div class="viewer-container">
      <div class="main-image-wrapper">
        <img id="clearanceMainImg" src="<?php echo base_url(); ?>assets/images/no-image.png" style="cursor:pointer;" onclick="report.show_attachment(this, event)" alt="clearance" style="display:none;">
        <div class="img-label" id="clearanceImgLabel">Pilih gambar di bawah</div>
      </div>

      <?php if (isset($clearance) && count($clearance) > 0) { ?>
      <div class="thumb-row" id="clearanceThumbs">
        <?php foreach ($clearance as $i => $c) { ?>
          <div <?php echo $i == 0 ? 'id="first"' : '' ?> class="thumb-btn <?php echo $i === 0 ? 'active' : ''; ?>"
               data-src="<?php echo base_url() . $c['path_file']; ?>"
               data-nama="<?php echo $c['nama_fasilitas']; ?>"
               data-kondisi="<?php echo $c['kondisi_fasilitas']; ?>"
               data-jumlah="<?php echo $c['jumlah']; ?>"
               onclick="switchClearanceImage(this)">
            <?php echo $c['nama_fasilitas']; ?>
          </div>
        <?php } ?>
      </div>
      <?php } else { ?>
        <p style="color: var(--muted); font-size: 13px; text-align: center;">Tidak ada data clearance.</p>
      <?php } ?>
    </div>

    <div class="info-grid">
      <div class="label">No. Dokumen</div>
      <div class="colon">:</div>
      <p class="value"><?php echo !empty($list[0]['document']) ? $list[0]['document'] : '-' ?></p>
    </div>

    <div class="info-grid">
      <div class="label">Nama Barang</div>
      <div class="colon">:</div>
      <p class="value nama_barang">-</p>
    </div>

    <div class="info-grid">
      <div class="label">Kondisi Barang</div>
      <div class="colon">:</div>
      <p class="value kondisi_barang">-</p>
    </div>

    <div class="info-grid">
      <div class="label">Jumlah Barang</div>
      <div class="colon">:</div>
      <p class="value jumlah_barang">-</p>
    </div>

    <div class="section ack">
      <small>Di buat oleh</small>
      <div class="sign-row">
        <div class="sign-name"><?php echo !empty($list[0]['nama_karyawan']) && $list[0]['clearance_date'] != null ? ucwords(strtolower($list[0]['nama_karyawan'])) : '-' ?></div>
        <div class="sign-date"><?php echo !empty($list[0]['clearance_date']) ? tglIndonesia($list[0]['clearance_date'], '-', ' ') : '-' ?></div>
      </div>
    </div>

    <div class="section app">
      <small>Di Verifikasi oleh</small>
      <div class="sign-row">
        <div class="sign-name"><?php echo !empty($list[0]['verification_by']) && $list[0]['verification_clearance_date'] != null ? ucwords(strtolower($list[0]['verification_by'])) : '-' ?></div>
        <div class="sign-date"><?php echo !empty($list[0]['verification_clearance_date']) ? tglIndonesia($list[0]['verification_clearance_date'], '-', ' ') : '-' ?></div>
      </div>
    </div>

  </div>
</fieldset>

<!-- ========================================== -->
<!-- 3. PENONAKTIFAN USER -->
<!-- ========================================== -->
<fieldset class="fs-deactivate">
  <legend>Penonaktifan User</legend>
  <div class="fieldset-inner">

    <div class="section do">
      <small>Di Kerjakan oleh</small>
      <div class="sign-row">
        <div class="sign-name"><?php echo !empty($list[0]['nonactive_user_by']) && $list[0]['nonactive_user_date'] != null ? ucwords(strtolower($list[0]['nonactive_user_by'])) : '-' ?></div>
        <div class="sign-date"><?php echo !empty($list[0]['nonactive_user_date']) ? tglIndonesia($list[0]['nonactive_user_date'], '-', ' ') : '-' ?></div>
      </div>
    </div>

  </div>
</fieldset>

<!-- ========================================== -->
<!-- JAVASCRIPT -->
<!-- ========================================== -->
<script>
  // 1. Fungsi Switch Image untuk Data Detail
  function switchDetailImage(el, imgId, labelId) {
    document.querySelectorAll('#' + imgId).forEach(() => {
      // Hapus active dari semua thumb di container yang sama
      el.parentElement.querySelectorAll('.thumb-btn').forEach(btn => btn.classList.remove('active'));
    });
    el.classList.add('active');

    const img = document.getElementById(imgId);
    const label = document.getElementById(labelId);

    img.style.opacity = 0;
    setTimeout(() => {
      img.src = el.dataset.src;
      label.textContent = el.dataset.name;
      img.style.opacity = 1;
    }, 200);
  }

  // 2. Fungsi Switch Image untuk Data Clearance (Update Gambar + Info)
  function switchClearanceImage(el) {
    // Hapus active dari semua thumb clearance
    document.querySelectorAll('#clearanceThumbs .thumb-btn').forEach(btn => btn.classList.remove('active'));
    el.classList.add('active');

    const img = document.getElementById('clearanceMainImg');
    const label = document.getElementById('clearanceImgLabel');

    // Tampilkan gambar jika sebelumnya hidden
    img.style.display = 'block';
    img.style.opacity = 0;
    
    setTimeout(() => {
      img.src = el.dataset.src;
      label.textContent = el.dataset.nama;
      
      // Update info text
      document.querySelector('.nama_barang').textContent = el.dataset.nama || '-';
      document.querySelector('.kondisi_barang').textContent = el.dataset.kondisi || '-';
      document.querySelector('.jumlah_barang').textContent = el.dataset.jumlah || '-';
      
      img.style.opacity = 1;
    }, 200);
  }

  // 3. Auto-select gambar pertama pada Data Clearance saat halaman dimuat
  document.addEventListener('DOMContentLoaded', function () {
    const firstClearanceBtn = document.querySelector('#clearanceThumbs .thumb-btn');
    if (firstClearanceBtn) {
      firstClearanceBtn.click(); // Memicu fungsi switchClearanceImage secara otomatis
    }
  });

    $("#first").trigger("click");
</script>
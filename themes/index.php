<!DOCTYPE html>
<html lang="id">

<head>
  <base href="<?php echo base_url(); ?>" />
  <link rel="shortcut icon" type="image/x-icon" href="assets/images/hris.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="GMP HRIS - Sistem Informasi SDM">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

  <title>GMP - HRIS</title>

  <?php if (isset($css_files) && is_array($css_files)) : ?>
      <?php foreach ($css_files as $css) : ?>
          <?php if ( ! is_null($css)) : ?>
              <link rel="stylesheet" href="<?php echo $css; ?>?v=<?php echo $this->settings->site_version; ?>"><?php echo "\n"; ?>
          <?php endif; ?>
      <?php endforeach; ?>
  <?php endif; ?>

  <style>
  /* ============================================
     NAVBAR — satu baris flex di semua layar
     (tanpa collapse, tanpa hamburger)
     ============================================ */
  #wrapper .navbar{
    display:flex !important;
    align-items:center;
    flex-wrap:nowrap;
    gap:10px;
    min-height:0;
  }
  #wrapper .navbar > a#menu-toggle{
    align-self:center;
    flex:0 0 auto;
    margin-left:6px;
  }
  #wrapper .navbar > ul{
    display:flex;
    align-items:center;
    margin:0 !important;
    float:none !important;
    flex:0 0 auto;
  }
  #wrapper .navbar > ul.user-nav{
    margin-left:auto;
    margin-right:4px;
  }
  #wrapper .navbar .title{
    flex:1 1 auto;
    min-width:0;
    margin:0;
    padding:0 4px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    font-weight:600;
    color:#2c3e50;
  }

  /* ===== SIDEBAR HEADING (DARK) ===== */
  .sidebar-heading.sh-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 16px;
    margin: 0;
    text-align: left;
    background: transparent;
    border-bottom: 1px solid rgba(255,255,255,.07);
  }
  .sh-logo-box {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(145deg, rgba(255,255,255,.10), rgba(255,255,255,.03));
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 12px;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
  }
  .sh-logo {
    width: 30px;
    height: 30px;
    object-fit: contain;
    /* border-radius: 6px; */
  }
  .sh-text {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
  }
  .sh-title {
    font-size: 25px;
    font-weight: 800;
    letter-spacing: .8px;
    color: #ffffff;
    line-height: 1;
  }
  .sh-accent { color: #fb923c; }
  .sh-subtitle {
    font-size: .62rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.4px;
    color: #7c8ba1;
    line-height: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .sidebar-dark .sh-title    { color: #ffffff; }
  .sidebar-dark .sh-subtitle { color: #cbd5e1; }

  @media (max-width: 480px){
    #wrapper .navbar .title{ display:none; }
  }

  /* ============================================
     DROPDOWN — paksa absolute (overlay)
     ============================================ */
  #wrapper .navbar li.dropdown{ position:relative; }
  #wrapper .navbar .dropdown-menu{
    position:absolute !important;
    top:100%;
    display:none;
    z-index:1050;
  }
  #wrapper .navbar li.open > .dropdown-menu,
  #wrapper .navbar li.show > .dropdown-menu,
  #wrapper .navbar .dropdown-menu.show{ display:block; }
  #wrapper .navbar .dropdown-menu-right{ right:0; left:auto; }
  #wrapper .navbar .dropdown-menu-left{ left:0; right:auto; }

  #wrapper .navbar .dropdown-menu.extended.notification{
    min-width:280px;
    max-width:340px;
    padding:0;
    border-radius:8px;
    box-shadow:0 10px 25px rgba(0,0,0,.12);
  }
  #wrapper .navbar .notification .dropdown-item{
    padding:8px 14px;
    white-space:normal;
    font-size:13px;
  }
  #wrapper .navbar .notification .dropdown-item.setting.bg-warning{
    background:#fff8e1 !important;
    color:#8a6d00;
    font-size:12.5px;
    border-bottom:1px solid #f0e4b8;
  }

  #wrapper .navbar .notif{
    position:relative;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
  }
  #wrapper .navbar .notif .badge{
    position:absolute;
    top:-4px;
    right:-6px;
    font-size:10px;
    padding:2px 6px;
    border-radius:999px;
    min-width:18px;
    text-align:center;
  }

  #wrapper .navbar .img-circle{
    border-radius:50%;
    object-fit:cover;
    cursor:pointer;
    border:2px solid #e1e5eb;
    transition:border-color .2s ease;
  }
  #wrapper .navbar .img-circle:hover{ border-color:#1f75fe; }

  /* ============================================
     SIDEBAR — scrollbar native ringan
     ============================================ */
  #sidebar-wrapper .content{
    overflow-y:auto;
    max-height:calc(100vh - 120px);
    scrollbar-width:thin;
    scrollbar-color:#4a5568 transparent;
  }
  #sidebar-wrapper .content::-webkit-scrollbar{ width:6px; }
  #sidebar-wrapper .content::-webkit-scrollbar-thumb{ background:#4a5568; border-radius:3px; }
  #sidebar-wrapper .content::-webkit-scrollbar-track{ background:transparent; }

  /* ============================================
    ACTIVE MENU — parent expand + highlight
    ============================================ */


  /* Child menu aktif */
  li.menu.active > a {
    background: rgba(255,255,255,.05) !important;
    /* color: #ffffff !important;
    border-radius: 6px;
    margin: 2px 8px;
    font-weight: 600; */
  }
  li.menu.active > a:hover {
    background: rgba(255,255,255,.05) !important;
  }

  .list-group-item.dropdown-toggle:hover {
    background: rgba(255,255,255,.05) !important;
  }

  /* ============================================
     MOBILE — dropdown jadi full-width fixed
     ============================================ */
  @media (max-width: 768px){
    #wrapper .navbar .dropdown-menu.extended.notification{
      position:fixed !important;
      top:60px;
      left:8px;
      right:8px;
      max-width:none;
      width:auto;
    }
  }
  </style>
</head>

<body>

  <div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <div class="bg-light-black" id="sidebar-wrapper" style="width: 17rem;">
      <div class="sidebar-heading sh-wrap">
        <div class="sh-logo-box">
          <img src="assets/images/hris.png" width="30" height="30" alt="GMP HRIS" class="sh-logo">
        </div>
        <div class="sh-text">
            <span class="sh-title">GMP <span class="sh-accent">HRIS</span></span>
            <span class="sh-subtitle">PT. Griya Mitra Poultry</span>
        </div>
      </div>
      <div class="divider-heading" style="padding: 0rem 1rem;">
        <div class="dropdown-divider" style="margin-top: 0rem;"></div>
      </div>
      <div class="list-group list-group-flush content" style="max-width: 20rem; width: 17rem;">
        <ul class="list-unstyled components">
          <li class="active">
            <a class="list-group-item list-group-item-action bg-light-black menu" data-txt="Dashboard" href="#">
              <i class="fa fa-dashboard"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <?php
            $arr_fitur    = isset($this->session->userdata()['Fitur']) ? $this->session->userdata()['Fitur'] : [];
            $current_path = trim($this->uri->uri_string(), '/');
          ?>
          <?php foreach ($arr_fitur as $key => $v_fitur): ?>
            <?php 
                // Cek apakah ada child menu yang cocok dengan URL saat ini
                $isParentActive = false;
                foreach ($v_fitur['detail'] as $v_mdetail) {
                    if (trim($v_mdetail['path_detfitur'], '/') === $current_path) {
                        $isParentActive = true;
                        break;
                    }
                }
            ?>
            <li>
              <a href="<?php echo '#'.$v_fitur['id_header_fitur'] ?>" 
                 data-toggle="collapse" 
                 aria-expanded="<?php echo $isParentActive ? 'true' : 'false' ?>" 
                 data-val="0" 
                 class="dropdown-toggle list-group-item list-group-item-action bg-light-black <?php echo $isParentActive ? 'active-parent' : '' ?>">
                <?php echo htmlspecialchars($v_fitur['header_fitur'], ENT_QUOTES); ?>
              </a>
              <!-- "show" (BS4/5) + "in" (BS3) agar kompatibel keduanya -->
              <ul class="collapse list-unstyled <?php echo $isParentActive ? 'show in' : '' ?>" 
                  id="<?php echo $v_fitur['id_header_fitur'] ?>">
                <?php foreach ($v_fitur['detail'] as $key => $v_mdetail): 
                    $childPath     = trim($v_mdetail['path_detfitur'], '/');
                    $isChildActive = ($childPath === $current_path);
                ?>
                    <li class="menu <?php echo $isChildActive ? 'active' : '' ?>">
                      <a href="<?php echo $v_mdetail['path_detfitur']; ?>" 
                         class="list-group-item list-group-item-action bg-light-black menu <?php echo $isChildActive ? 'active' : '' ?>" 
                         data-txt="<?php echo htmlspecialchars($v_mdetail['nama_detfitur'], ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($v_mdetail['nama_detfitur'], ENT_QUOTES); ?>
                      </a>
                    </li>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">

      <!-- NAVBAR: tanpa .collapse & tanpa hamburger.
           Urutan: toggle | bell | judul | user -->
      <nav class="navbar navbar-light bg-light border-bottom no-padding">
        <a id="menu-toggle" title="Hide Menu" role="button" aria-label="Toggle menu" style="cursor:pointer;">
          <i class="fa fa-angle-left cursor-p left"></i>
          <i class="fa fa-navicon cursor-p"></i>
          <i class="fa fa-angle-right cursor-p right" hidden></i>
        </a>

        <?php
          $notif = [];
          $arr_fitur = isset($this->session->userdata()['Fitur']) ? $this->session->userdata()['Fitur'] : [];

          foreach ($arr_fitur as $key => $v_fitur) {
            foreach ($v_fitur['detail'] as $key => $v_mdetail) {
              $akses = hakAkses('/'.$v_mdetail['path_detfitur']);

              if ( isset($akses['a_ack']) && $akses['a_ack'] == 1 ) {
                $status = getStatus('submit');
              } elseif ( isset($akses['a_approve']) && $akses['a_approve'] == 1 ) {
                $status = getStatus('ack');
              } else {
                continue;
              }

              $data = Modules::run( $v_mdetail['path_detfitur'].'/model', $status);

              if ( !empty($data) && is_array($data) && count($data) > 0 ) {
                $notif[$v_mdetail['path_detfitur']] = [
                  'data'       => $data,
                  'path'       => $v_mdetail['path_detfitur'],
                  'nama_fitur' => $v_mdetail['nama_detfitur']
                ];
              }
            }
          }

          $jml_notif = 0;
          foreach ($notif as $v_notif) {
            $jml_notif += is_array($v_notif['data']) ? count($v_notif['data']) : 0;
          }
        ?>

        <!-- Bell notifikasi -->
        <ul class="navbar-nav notify-row">
          <li id="header_notification_bar" class="nav-item dropdown">
            <a data-toggle="dropdown" class="notif" aria-label="Notifikasi">
              <i class="fa fa-bell-o cursor-p" style="border: 1px solid black; padding: 7px; border-radius: 5px;"></i>
              <?php if ( $jml_notif > 0 ): ?>
                <span class="badge bg-warning"><?php echo $jml_notif; ?></span>
              <?php endif; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-left extended notification no-padding">
              <li class="dropdown-item setting bg-warning">
                <div class="yellow">
                  <i class="fa fa-exclamation-circle"></i>
                  <b>You have <?php echo $jml_notif; ?> new notifications</b>
                </div>
              </li>
              <?php if ( !empty($notif) && count($notif) > 0 ) : ?>
                <?php foreach ($notif as $v_notif) : ?>
                  <?php foreach ($v_notif['data'] as $v_data) : ?>
                    <?php
                      $params = '';
                      if ( isset($v_data['params']) && !empty($v_data['params']) ) {
                        $params = '/index/'.$v_data['params'];
                      }

                      $status = $v_data['status_data'];
                      if ( is_numeric($status) ) {
                        $status = getStatus($status);
                      }
                      $status = ucfirst(strtolower($status));

                      $keterangan = htmlspecialchars($v_notif['nama_fitur'], ENT_QUOTES);
                      if ( isset($v_data['keterangan']) && !empty($v_data['keterangan']) ) {
                        $keterangan .= " (" . strtoupper($v_data['keterangan']) . ")";
                      }
                    ?>
                    <li class="dropdown-item no-padding cursor-p">
                      <a href="<?php echo $v_notif['path'].$params; ?>" title="<?php echo htmlspecialchars($v_notif['nama_fitur'] . ' (' . $v_data['next_state'] . ')', ENT_QUOTES); ?>">
                        <div class="col-md-12 no-padding">
                          <div class="col-md-8 text-left"><?php echo $keterangan; ?></div>
                          <div class="col-md-4 text-right"><?php echo '(' . $status . ') ' . $v_data['jumlah']; ?></div>
                        </div>
                      </a>
                    </li>
                  <?php endforeach; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </li>
        </ul>

        <!-- Judul halaman -->
        <div class="title"><?php echo isset($title_menu) ? htmlspecialchars($title_menu, ENT_QUOTES) : ''; ?></div>

        <!-- User dropdown (selalu tampil) -->
        <ul class="navbar-nav user-nav">
          <li id="header_user_bar" class="nav-item dropdown">
            <?php
              $detail_user = isset($this->session->userdata()['detail_user']) ? $this->session->userdata()['detail_user'] : [];
              $user_nama   = isset($detail_user['nama_detuser']) ? $detail_user['nama_detuser'] : 'User';
              $user_avatar = (isset($detail_user['avatar_detuser']) && !empty($detail_user['avatar_detuser']))
                               ? 'uploads/'.$detail_user['avatar_detuser']
                               : 'assets/images/icon-user.png';
            ?>
            <span class="control-label d-none d-sm-inline" style="margin-right: 1rem;">
              <?php echo htmlspecialchars($user_nama, ENT_QUOTES); ?>
            </span>
            <img data-toggle="dropdown"
                 src="<?php echo htmlspecialchars($user_avatar, ENT_QUOTES); ?>"
                 class="img-circle"
                 aria-expanded="false"
                 width="30" height="30"
                 alt="Avatar <?php echo htmlspecialchars($user_nama, ENT_QUOTES); ?>">
            <ul class="dropdown-menu dropdown-menu-right extended notification">
              <li class="dropdown-item setting">Setting</li>
              <div class="dropdown-divider no-padding"></div>
              <li class="dropdown-item">
                <a class="cursor-p" onclick="go_to_profile()" role="button">
                  <i class="fa fa-user m-r-5 m-l-5"></i>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;My Profile
                </a>
              </li>
              <li class="dropdown-item">
                <a class="cursor-p" data-toggle="modal" data-target="#logoutModal" role="button">
                  <i class="fa fa-power-off m-r-5 m-l-5"></i>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Logout
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>

      <div class="container-fluid">
        <div class="main">
          <?php echo $view; ?>
        </div>
      </div>
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <!-- Logout Modal -->
  <div class="modal" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="logoutModalLabel">Alert</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
          <span>Apakah anda yakin ingin keluar ?</span>
        </div>
        <div class="modal-footer">
          <a class="btn btn-primary" href="user/Login/logout">Ya</a>
          <button data-dismiss="modal" class="btn btn-danger" type="button">Tidak</button>
        </div>
      </div>
    </div>
  </div>

  <?php if (isset($js_files) && is_array($js_files)) : ?>
      <?php foreach ($js_files as $js) : ?>
          <?php if ( ! is_null($js)) : ?>
              <?php echo "\n"; ?><script type="text/javascript" src="<?php echo $js; ?>?v=<?php echo $this->settings->site_version; ?>"></script><?php echo "\n"; ?>
          <?php endif; ?>
      <?php endforeach; ?>
  <?php endif; ?>

  <!-- Menu Toggle Script -->
  <script>
    $(function(){

      // Menu toggle sidebar
      $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");

        if ( $("#wrapper").hasClass("toggled") ) {
          $("#wrapper").find('a#menu-toggle').attr('title', 'Show Menu');
          $("#wrapper").find('i.left').attr('hidden', true);
          $("#wrapper").find('i.right').removeAttr('hidden');
          $(".tu-float-btn-left").removeClass('toggled');
        } else {
          $("#wrapper").find('a#menu-toggle').attr('title', 'Hide Menu');
          $("#wrapper").find('i.left').removeAttr('hidden');
          $("#wrapper").find('i.right').attr('hidden', true);
          $(".tu-float-btn-left").addClass('toggled');
        }
      });

      $("a.menu").click(function(e) {
        if (typeof _getDataSummaryPanenDanDoc !== 'undefined' && _getDataSummaryPanenDanDoc && typeof _getDataSummaryPanenDanDoc.abort === 'function') {
          try { _getDataSummaryPanenDanDoc.abort(); } catch(err){}
        }
        if (typeof _getDataPanjualanDanHarga !== 'undefined' && _getDataPanjualanDanHarga && typeof _getDataPanjualanDanHarga.abort === 'function') {
          try { _getDataPanjualanDanHarga.abort(); } catch(err){}
        }
        if (typeof _getDataPlasmaMerah !== 'undefined' && _getDataPlasmaMerah && typeof _getDataPlasmaMerah.abort === 'function') {
          try { _getDataPlasmaMerah.abort(); } catch(err){}
        }
      });

      if (typeof $.fn.mCustomScrollbar !== 'undefined' && $("#content-1").length) {
        $("#content-1").mCustomScrollbar({ theme: "minimal" });
      }

    });

    $(".dropdown-toggle").click(function(e) {
      $(this).closest('li').toggleClass("open");
    });

    function go_to_profile() {
      var url = 'master/User/profile';
      if (typeof goToURL === 'function') {
        goToURL(url);
      } else {
        window.location.href = url;
      }
    }
  </script>

</body>

</html>
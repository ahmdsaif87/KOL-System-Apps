<!DOCTYPE html>
<html>
<head>
  <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_ASSET; ?>images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="64x64" href="<?= BASE_ASSET; ?>images/favicon.png">
  <link rel="shortcut icon" href="<?= BASE_ASSET; ?>images/favicon.ico">
  <link rel="apple-touch-icon" href="<?= BASE_ASSET; ?>images/favicon.png">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="<?= get_option('site_description'); ?>">
  <meta name="keywords" content="<?= get_option('keywords'); ?>">
  <meta name="author" content="<?= get_option('author'); ?>">

  <title><?= ($group = get_user_first_group()) ? $group->name : 'Admin' ?> | <?= get_option('site_name'); ?> | <?= $template['title']; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/admin-lte/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/admin-lte/dist/css/AdminLTE.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/admin-lte/dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/admin-lte/plugins/iCheck/flat/blue.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/admin-lte/plugins/morris/morris.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/admin-lte/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/admin-lte/plugins/datepicker/datepicker3.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/admin-lte/plugins/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/admin-lte/plugins/iCheck/all.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/sweet-alert/sweetalert.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/toastr/build/toastr.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/fancy-box/source/jquery.fancybox.css?v=2.1.5" media="screen" />
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/chosen/chosen.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>/css/custom.css?timestamp=201803311526">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>css/modern-ui.css?v=<?= is_file(FCPATH . 'asset/css/modern-ui.css') ? filemtime(FCPATH . 'asset/css/modern-ui.css') : time(); ?>">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>css/mobile-responsive.css?v=<?= is_file(FCPATH . 'asset/css/mobile-responsive.css') ? filemtime(FCPATH . 'asset/css/mobile-responsive.css') : time(); ?>">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>datetimepicker/jquery.datetimepicker.css"/>
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>js-scroll/style/jquery.jscrollpane.css" rel="stylesheet" media="all" />
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>flag-icon/css/flag-icon.css" rel="stylesheet" media="all" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .content-wrapper {
      min-height: calc(100vh - 101px) !important;
    }
    #kol-notif-badge:empty, #scraping-inbox-badge:empty {
      display: none !important;
    }
    .sidebar-app-logo {
      display: none;
      width: 100%;
      height: 54px;
      align-items: center;
      justify-content: center;
      position: sticky;
      top: 0;
      z-index: 100;
      background-color: #222d32;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      user-select: none;
      pointer-events: none;
      cursor: default;
    }
    .sidebar-app-logo img {
      width: auto !important;
      height: 32px !important;
      max-width: 85% !important;
      max-height: 32px !important;
      object-fit: contain !important;
      display: block !important;
      margin: 0 auto !important;
      -webkit-user-drag: none;
    }
    @media (max-width: 768px) {
      .main-sidebar {
        padding-top: 0 !important;
      }
      .sidebar-app-logo {
        display: flex !important;
      }
    }
  </style>
  <?= $this->cc_html->getCssFileTop(); ?>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <script src="<?= BASE_ASSET; ?>/admin-lte/plugins/jQuery/jquery-2.2.3.min.js"></script>
  <script src="<?= BASE_ASSET; ?>/admin-lte/plugins/iCheck/icheck.min.js"></script>
  <script src="<?= BASE_ASSET; ?>/sweet-alert/sweetalert-dev.js"></script>
  <script src="<?= BASE_ASSET; ?>/admin-lte/plugins/input-mask/jquery.inputmask.js"></script>
  <script src="<?= BASE_ASSET; ?>/admin-lte/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
  <script src="<?= BASE_ASSET; ?>/admin-lte/plugins/input-mask/jquery.inputmask.extensions.js"></script>
  <script src="<?= BASE_ASSET; ?>/toastr/toastr.js"></script>
  <script src="<?= BASE_ASSET; ?>/fancy-box/source/jquery.fancybox.js?v=2.1.5"></script>
  <script src="<?= BASE_ASSET; ?>/datetimepicker/build/jquery.datetimepicker.full.js"></script>
  <script src="<?= BASE_ASSET; ?>/editor/dist/js/medium-editor.min.js"></script>
  <script src="<?= BASE_ASSET; ?>js/cc-extension.js"></script>
  <script src="<?= BASE_ASSET; ?>/js/cc-page-element.js"></script>
  <script>
    var BASE_URL = "<?= base_url(); ?>";
    var HTTP_REFERER = "<?= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/' ; ?>";
    var csrf = '<?= $this->security->get_csrf_token_name(); ?>';
    var token = '<?= $this->security->get_csrf_hash(); ?>';

    $(document).ready(function(){

      toastr.options = {
        "positionClass": "toast-top-center",
      }

      window.addEventListener('pageshow', function (event) {
        if (event.persisted && window.toastr) {
          toastr.clear();
        }
      });

      var f_message = '<?= $this->session->flashdata('f_message'); ?>';
      var f_type = '<?= $this->session->flashdata('f_type'); ?>';

      if (f_message.length > 0) {
        toastr[f_type](f_message);
      }

      $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
        checkboxClass: 'icheckbox_minimal-red',
        radioClass: 'iradio_minimal-red'
      });
    });
  </script>
  <?= $this->cc_html->getScriptFileTop(); ?>
</head>
<?php $is_iframe = $this->input->get('is_iframe') == 'true'; ?>
<body class="<?= $is_iframe ? 'sidebar-collapse' : 'sidebar-mini skin-black fixed web-body' ?>" style="<?= $is_iframe ? 'background: #f8fafc !important;' : '' ?>">
<div class="<?= $is_iframe ? '' : 'wrapper' ?>">

  <?php if (!$is_iframe): ?>
  <header class="main-header">

     <?php 
      $logo =  get_option('logo');
      if ($logo) {
        $logo = 'uploads/setting/'. get_option('logo');
      } else {
        $logo = 'asset/logo.png';
      }

      if (!is_file(FCPATH .'/'. $logo)) {
        $logo = 'asset/logo.png';
      }
      ?>

    <a href="<?= site_url('/'); ?>" class="logo">
      <span class="logo-mini"><b><img src="<?= BASE_ASSET ?>logo.png" height="30px" style="max-height: 30px; max-width: 44px; object-fit: contain;"></b></span>
      <span class="logo-lg"><b><img src="<?= base_url($logo) ?>" height="36px" style="max-height: 36px; width: auto;"></b></span>
    </a>
    <nav class="navbar navbar-static-top">

      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <?php if ($this->aauth->get_user()): ?>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- WhatsApp Console Quick Access with Dynamic Unread Badge -->
          <li class="dropdown messages-menu" id="waNavbarItem">
            <a href="<?= site_url('administrator/whatsapp'); ?>" title="Komunikasi WhatsApp KOL" style="padding: 15px 14px; display: flex; align-items: center; gap: 6px; color: #16a34a; font-weight: 600; position: relative;">
              <i class="fa fa-whatsapp" style="font-size: 18px; color: #16a34a;"></i>
              <span class="hidden-xs" style="font-size: 13px; color: #334155;">WhatsApp</span>
              <?php
                $ci =& get_instance();
                $initial_wa_unread = 0;
                if ($ci->db->table_exists('whatsapp_messages')) {
                   $initial_wa_unread = (int)$ci->db->from('whatsapp_messages')
                      ->join('kols', 'kols.id = whatsapp_messages.kol_id', 'inner')
                      ->where('whatsapp_messages.direction', 'INBOUND')
                      ->where('whatsapp_messages.status !=', 'READ')
                      ->where('kols.status', 'ACTIVE')
                      ->count_all_results();
                }
              ?>
              <?php if ($initial_wa_unread > 0): ?>
              <span class="label label-danger" id="waUnreadNavbarBadge" style="border-radius: 10px; font-size: 10px; font-weight: 700; padding: 2px 6px; margin-left: 2px; background-color: #ef4444 !important;"><?= $initial_wa_unread > 99 ? '99+' : $initial_wa_unread; ?></span>
              <?php endif; ?>
            </a>
          </li>

          <?php if (is_kol_user()): ?>
          <!-- KOL Notification Inbox (Only for KOL creators) -->
          <li class="dropdown notifications-menu" id="kol-notifications-container">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Pemberitahuan">
              <i class="fa fa-bell-o"></i>
              <span id="kol-notif-badge" style="display:none;"></span>
            </a>
            <ul class="dropdown-menu" style="width: 360px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; border-radius: 6px;">
              <li class="header" style="background: #ffffff; color: #1e293b; padding: 10px 14px; border-bottom: 1px solid #f1f5f9; font-weight: 600; font-size: 12.5px; display: flex; justify-content: space-between; align-items: center;">
                <span>Pemberitahuan</span>
                <span id="kol-notif-header-action">
                  <a href="javascript:void(0)" onclick="markAllKolNotificationsRead(event)" class="btn btn-xs btn-default" style="font-size: 11px; padding: 2px 7px; border-radius: 3px; color: #475569; border-color: #cbd5e1; font-weight: 500;" title="Tandai semua sebagai sudah dibaca">
                    Tandai Dibaca
                  </a>
                </span>
              </li>
              <li>
                <ul class="menu" id="kol-notif-menu" style="max-height: 360px; overflow-y: auto; padding: 0; margin: 0; list-style: none;">
                  <li style="padding: 24px 16px; text-align: center; color: #94a3b8; font-size: 12px;">
                    <i class="fa fa-spinner fa-spin fa-2x" style="margin-bottom: 8px; display: block; color: #3b82f6;"></i> Memuat pemberitahuan...
                  </li>
                </ul>
              </li>
              <li class="footer" style="padding: 10px 16px; text-align: center; background: #f8fafc; border-top: 1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
                <a href="<?= site_url('administrator/kol_notifications'); ?>" style="color: #3b82f6; font-size: 12px; font-weight: 600; text-decoration: none;">
                  Lihat Semua Notifikasi &rarr;
                </a>
              </li>
            </ul>
          </li>
          <?php endif; ?>

          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?= BASE_URL.'uploads/user/'.(!empty(get_user_data('avatar')) ? get_user_data('avatar') :'default.png'); ?>" class="user-image" alt="User" onerror="this.src='<?= BASE_ASSET ?>admin-lte/dist/img/avatar.png'">
              <span class="hidden-xs"><?= _ent(ucwords(clean_snake_case(get_user_data('full_name')))); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?= BASE_URL.'uploads/user/'.(!empty(get_user_data('avatar')) ? get_user_data('avatar') :'default.png'); ?>" class="img-circle" alt="User" onerror="this.src='<?= BASE_ASSET ?>admin-lte/dist/img/avatar.png'">

                <p>
                  <?= _ent(ucwords(clean_snake_case($this->aauth->get_user()->full_name))); ?> 
                  <small>Last Login, <?= date('Y-M-D', strtotime(get_user_data('last_login'))); ?></small>
                </p>
              </li>
              
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?= site_url('administrator/user/profile'); ?>" class="btn btn-default btn-flat"><?= cclang('profile'); ?></a>
                </div>
                <div class="pull-right">
                  <a href="<?= site_url('administrator/auth/logout'); ?>" class="btn btn-default btn-flat"><?= cclang('sign_out'); ?></a>
                </div>
              </li>
            </ul>
          </li>
          <li class="dropdown ">
             <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
             <span class="flag-icon <?= get_current_initial_lang(); ?>"></span> <?= get_current_lang(); ?> </a>
             <ul class="dropdown-menu" role="menu">
             <?php foreach (get_langs() as $lang): ?>
                <li><a href="<?= site_url('web/switch_lang/'.$lang['folder_name']); ?>"><span class="flag-icon <?= $lang['icon_name']; ?>"></span> <?= $lang['name']; ?></a></li>
              <?php endforeach; ?>
             </ul>
          </li>
        </ul>
      </div>
      <?php endif ?>

    </nav>
  </header>
  <aside class="main-sidebar">

    <section class="sidebar" style="padding-top:0% !important">
      <div class="sidebar-app-logo">
        <img src="<?= BASE_ASSET ?>logo-light.png" alt="App Icon" style="width: auto; max-height: 32px; max-width: 85%;">
      </div>
      <ul class="sidebar-menu  sidebar-admin tree"  data-widget="tree">
        
        <?= display_menu_admin(0, 1); ?>
      </ul>
    </section>

  </aside>
  <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
  <?php endif; ?>

  <div class="content-wrapper" style="<?= $is_iframe ? 'margin-left: 0 !important; padding: 0 !important; min-height: 100vh !important; background: #f8fafc !important;' : '' ?>">
    <?php cicool()->eventListen('backend_content_top'); ?>
    <?= $template['partials']['content']; ?>
    <?php cicool()->eventListen('backend_content_bottom'); ?>

    <div class="modal fade  " id="modalPopUp">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"> <span class="popup-title"></span></h4>
          </div>
          <div class="modal-body">
           
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>

</script>
  <?php if (!$is_iframe): ?>
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b><?= cclang('version') ?></b> <?= VERSION ?>
    </div>
    <strong>Copyright &copy; 2020 -<?=date('Y'); ?> <a href="#"><?= get_option('site_name'); ?></a>.</strong> All rights
    reserved.
  </footer>
  
  <div class="control-sidebar-bg"></div>
  <?php endif; ?>
</div>


<?= $this->cc_html->getHtmlFileBottom(); ?>

<?= $this->cc_html->getCssFileBottom(); ?>
<?= $this->cc_html->getScriptFileBottom(); ?>
<script>
        var AdminLTEOptions = {
          sidebarExpandOnHover: false,
           navbarMenuSlimscroll: false,
        };
      </script>


<?php if (is_kol_user()): ?>
<script>
  // KOL Activity & Notification Polling (KOL users only)
  function refreshKolNotifications() {
      $.ajax({
          url: BASE_URL + 'administrator/kol_notifications/get_inbox',
          type: 'GET',
          dataType: 'json',
          cache: false,
          success: function(res) {
              if (res.success && res.notifications) {
                  var unread = res.unread_count || 0;
                  if (unread > 0) {
                      $('#kol-notif-badge')
                          .attr('class', 'label label-danger')
                          .text(unread > 99 ? '99+' : unread)
                          .attr('style', 'display:inline-block !important; font-size:10px; border-radius:10px; position:absolute; top:9px; right:7px; padding:2px 5px; line-height:1;')
                          .show();
                      $('#kol-notif-header-action').html(`
                          <a href="javascript:void(0)" onclick="markAllKolNotificationsRead(event)" class="btn btn-xs btn-default" style="font-size: 11px; padding: 2px 8px; border-radius: 4px; color: #3b82f6; border-color: #cbd5e1; font-weight: 500;" title="Tandai semua sebagai sudah dibaca">
                              <i class="fa fa-check-square-o"></i> Tandai Dibaca
                          </a>
                      `);
                  } else {
                      $('#kol-notif-badge')
                          .removeClass('label label-danger')
                          .empty()
                          .attr('style', 'display:none !important;')
                          .hide();
                      $('#kol-notif-header-action').html(`
                          <span style="font-size: 11px; color: #10b981; font-weight: 500;">
                              <i class="fa fa-check-circle"></i> Semua dibaca
                          </span>
                      `);
                  }
                  
                  var html = '';
                  if (res.notifications.length > 0) {
                      res.notifications.forEach(function(item) {
                          var bgStyle = item.is_unread ? 'background: #f8fafc;' : 'background: #ffffff;';
                          var unreadDot = item.is_unread ? '<span style="display:inline-block; width:5px; height:5px; background:#2563eb; border-radius:50%; margin-right:5px;" title="Belum dibaca"></span>' : '';
                          
                          html += '<li class="kol-notif-item ' + (item.is_unread ? 'is-unread' : 'is-read') + '" style="' + bgStyle + ' border-bottom: 1px solid #f1f5f9; padding: 7px 12px; transition: background 0.12s ease;">';
                          html += '<a href="' + item.url + '" style="display: block; color: inherit; text-decoration: none; padding: 0; white-space: normal;">';
                          html += '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">';
                          html += '<div style="font-weight: 600; font-size: 12px; color: ' + (item.is_unread ? '#0f172a' : '#475569') + '; display: flex; align-items: center;">';
                          html += unreadDot + item.title;
                          html += '</div>';
                          html += '<span class="label ' + item.badge_class + '" style="font-size: 9px; padding: 1px 5px; border-radius: 3px; font-weight: 500;">' + item.badge + '</span>';
                          html += '</div>';
                          html += '<div style="font-size: 11px; color: ' + (item.is_unread ? '#334155' : '#64748b') + '; line-height: 1.35; margin-bottom: 2px;">' + item.message + '</div>';
                          html += '<div style="font-size: 9.5px; color: #94a3b8;">' + item.time_ago + '</div>';
                          html += '</a>';
                          html += '</li>';
                      });
                  } else {
                      html = '<li style="padding: 24px 16px; text-align: center; color: #94a3b8; font-size: 11.5px;">Belum ada pemberitahuan baru.</li>';
                  }
                  $('#kol-notif-menu').html(html);
              }
          },
          error: function(xhr) {
              $('#kol-notif-menu').html('<li style="padding: 14px; text-align: center; color: #ef4444; font-size: 11px;">Gagal memuat pemberitahuan.</li>');
          }
      });
  }

  function markAllKolNotificationsRead(e) {
      if(e) e.preventDefault();
      
      $('#kol-notif-header-action').html('<span style="font-size: 11px; color: #94a3b8;">Menyimpan...</span>');
      
      $.ajax({
          url: BASE_URL + 'administrator/kol_notifications/mark_all_read',
          type: 'POST',
          dataType: 'json',
          data: {
              '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
          },
          success: function(res) {
              if (res.success) {
                  $('#kol-notif-badge')
                      .removeClass('label label-danger')
                      .empty()
                      .attr('style', 'display:none !important;')
                      .hide();
                  $('#kol-notif-header-action').html(`
                      <span style="font-size: 11px; color: #10b981; font-weight: 500;">
                          Semua dibaca
                      </span>
                  `);
                  // Update items visually to read state
                  $('#kol-notif-menu li.kol-notif-item').each(function() {
                      $(this).css('background', '#ffffff');
                      $(this).find('span[title="Belum dibaca"]').remove();
                  });
              }
          },
          error: function() {
              refreshKolNotifications();
          }
      });
  }

  <?php if (!$is_iframe): ?>
  $(document).ready(function() {
      refreshKolNotifications();
      setInterval(refreshKolNotifications, 20000); // Poll every 20s
  });
  <?php endif; ?>
</script>
<?php endif; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.min.js" type="text/javascript"></script>
<script src="<?= BASE_ASSET; ?>jquery-ui/jquery-ui.js"></script>
<script src="<?= BASE_ASSET; ?>jquery-switch-button/jquery.switchButton.js"></script>
<script src="<?= BASE_ASSET; ?>/js/jquery.ui.touch-punch.js"></script>
<script src="<?= BASE_ASSET; ?>/admin-lte/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= BASE_ASSET; ?>/admin-lte/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?= BASE_ASSET; ?>/admin-lte/plugins/fastclick/fastclick.js"></script>
<script src="<?= BASE_ASSET; ?>/admin-lte/dist/js/app.min.js"></script>
<script src="<?= BASE_ASSET; ?>/admin-lte/dist/js/adminlte.js"></script>
<script src="<?= BASE_ASSET; ?>js-scroll/script/jquery.jscrollpane.min.js"></script>
<script src="<?= BASE_ASSET; ?>jquery-switch-button/jquery.switchButton.js"></script>
<script src="<?= BASE_ASSET; ?>/js/custom.js"></script>

<?php if ($this->aauth->get_user()): ?>
<script type="text/javascript">
$(document).ready(function(){
   window.checkWaUnreadBadge = function() {
      if (document.hidden) return;
      $.ajax({
         url: '<?= site_url("administrator/whatsapp/ajax_unread_counts"); ?>',
         type: 'GET',
         dataType: 'json',
         success: function(res) {
            if (res && res.success) {
                var count = parseInt(res.total_unread, 10) || 0;
                var $link = $('#waNavbarItem > a');
                var $badge = $('#waUnreadNavbarBadge');
                if (count > 0) {
                   if ($badge.length === 0) {
                      $badge = $('<span class="label label-danger" id="waUnreadNavbarBadge" style="border-radius: 10px; font-size: 10px; font-weight: 700; padding: 2px 6px; margin-left: 2px; background-color: #ef4444 !important;"></span>');
                      $link.append($badge);
                   }
                   $badge.text(count > 99 ? '99+' : count).show();
                } else {
                   $badge.remove();
                }
                $(document).trigger('wa:unread_counts_updated', [res]);
            }
         }
      });
   };

   // Run on load and poll every 12 seconds
   window.checkWaUnreadBadge();
   setInterval(window.checkWaUnreadBadge, 12000);
});
</script>
<?php endif; ?>
<script>
$(document).ready(function() {
  // Ensure sidebar backdrop exists in DOM
  if (!$('.sidebar-backdrop').length) {
    $('<div class="sidebar-backdrop" id="sidebar-backdrop"></div>').insertAfter('.main-sidebar');
  }

  // Dismiss mobile sidebar when clicking the backdrop
  $(document).on('click', '.sidebar-backdrop', function(e) {
    e.preventDefault();
    $('body').removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
  });

  // Fallback: dismiss when clicking outside sidebar if open
  $(document).on('click', function(e) {
    if ($('body').hasClass('sidebar-open')) {
      if (!$(e.target).closest('.main-sidebar, .sidebar-toggle, [data-toggle="offcanvas"], [data-toggle="push-menu"]').length) {
        $('body').removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
      }
    }
  });
});
</script>
</body>
</html>

<?php
$current_user = $this->aauth->get_user();
$current_user_name = !empty($current_user->full_name) ? $current_user->full_name : (!empty($current_user->username) ? $current_user->username : 'Staff Operasional');
?>
<style>
/* WhatsApp Chat Bubble Styling - Permanent & Unified */
.chat-message-item {
   display: flex;
   margin-bottom: 10px;
   width: 100%;
   clear: both;
}
.chat-message-item.chat-item-outbound {
   justify-content: flex-end;
}
.chat-message-item.chat-item-inbound {
   justify-content: flex-start;
}
.chat-bubble {
   max-width: 75%;
   min-width: 80px;
   width: fit-content;
   padding: 8px 12px;
   border-radius: 8px;
   box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
   position: relative;
   text-align: left;
   word-break: break-word;
}
.chat-bubble-out {
   background: #d9fdd3;
   color: #111b21;
   border-top-right-radius: 0;
}
.chat-bubble-in {
   background: #ffffff;
   color: #111b21;
   border-top-left-radius: 0;
}
.chat-bubble-sender {
   font-size: 11px;
   font-weight: 700;
   margin-bottom: 3px;
   text-align: left;
}
.chat-bubble-sender.sender-out {
   color: #15803d;
}
.chat-bubble-sender.sender-in {
   color: #0369a1;
}
.chat-bubble-text {
   font-size: 13px;
   line-height: 1.45;
   word-break: break-word;
   white-space: pre-wrap;
   text-align: left;
}
.chat-bubble-meta {
   display: flex;
   justify-content: flex-end;
   align-items: center;
   gap: 4px;
   margin-top: 4px;
   font-size: 10px;
   color: #64748b;
   float: right;
   clear: both;
}
.chat-bubble-meta .fa {
   font-size: 11px;
}

/* WhatsApp Mobile & Tablet Full-Page Navigation (Like Native WhatsApp App) */
.btn-chat-back {
   display: none !important;
}

@media (max-width: 991px) {
   /* Hide page header when in a roomchat on mobile/tablet */
   .wa-chat-page.has-selected-kol .content-header {
      display: none !important;
   }
   .wa-chat-page.has-selected-kol .content {
      padding: 6px 8px !important;
   }
   
   /* In roomchat: hide contact list, expand roomchat to full screen */
   .wa-chat-page.has-selected-kol .wa-panel-list {
      display: none !important;
   }
   .wa-chat-page.has-selected-kol .wa-panel-chat {
      display: block !important;
      width: 100% !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
   }

   /* WhatsApp Mobile Back Button */
   .wa-chat-page.has-selected-kol .btn-chat-back {
      display: inline-flex !important;
      align-items: center;
      justify-content: center;
      background: #ffffff;
      border: 1px solid #cbd5e1;
      color: #334155;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      min-width: 36px;
      padding: 0;
      margin-right: 6px;
      font-size: 15px;
      flex-shrink: 0;
      cursor: pointer;
      text-decoration: none;
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
      transition: background 0.15s ease, color 0.15s ease;
   }
   .wa-chat-page.has-selected-kol .btn-chat-back:hover,
   .wa-chat-page.has-selected-kol .btn-chat-back:active {
      background: #f1f5f9;
      color: #0f172a;
   }

   /* In contact list: hide chat panel, expand list to full screen */
   .wa-chat-page.no-selected-kol .wa-panel-chat {
      display: none !important;
   }
   .wa-chat-page.no-selected-kol .wa-panel-list {
      display: block !important;
      width: 100% !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
   }
   .wa-chat-page.no-selected-kol .content {
      padding: 10px 8px !important;
   }

   /* Responsive Chat Boxes on Tablet & Mobile */
   .wa-chat-box {
      height: calc(100vh - 120px) !important;
      min-height: 520px !important;
      margin-bottom: 10px !important;
   }
}

@media (max-width: 576px) {
   .wa-chat-box {
      height: calc(100vh - 85px) !important;
      min-height: 460px !important;
      border-radius: 4px !important;
   }
   .chat-header-actions .btn {
      font-size: 11px !important;
      padding: 4px 8px !important;
   }
}
</style>

<div class="wa-chat-page <?= $selected_kol ? 'has-selected-kol' : 'no-selected-kol'; ?>">
<section class="content-header"
   style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 6px;">
   <div>
      <h1 style="margin: 0; font-size: 24px;">
         Komunikasi WhatsApp
      </h1>
   </div>
   <div style="display: flex; gap: 8px;">
      <button type="button" class="btn btn-success btn-flat" id="btnOpenBroadcastModal"
         style="border-radius: 4px; font-weight: 700; box-shadow: 0 2px 6px rgba(22,163,74,0.3); font-size: 13px; padding: 7px 16px;">
         <i class="fa fa-bullhorn"></i> Broadcast Pesan KOL
      </button>
   </div>
</section>

<section class="content">
   <div class="row">

      <!-- ======================================================= -->
      <!-- PANEL KIRI: DAFTAR KOL & PERCAKAPAN (35% WIDTH)        -->
      <!-- ======================================================= -->
      <div class="col-md-4 col-sm-12 wa-panel-list" id="panelKolList">
         <div class="box box-solid wa-chat-box"
            style="border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; height: 720px; display: flex; flex-direction: column;">

            <!-- Box Header & WAHA Status -->
            <div class="box-header with-border" style="padding: 14px 16px; background: #f8fafc;">
               <div style="display: flex; justify-content: space-between; align-items: center;">
                  <div style="display: flex; align-items: center; gap: 6px;">
                     <h3 class="box-title" style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">
                        <i class="fa fa-whatsapp text-success" style="font-size: 18px; margin-right: 5px;"></i> Kontak
                        KOL
                     </h3>
                     <button type="button" class="btn btn-xs btn-default btn-flat" id="btnQuickBroadcast"
                        title="Kirim Pesan Massal ke Multiple KOL"
                        style="border-radius: 10px; font-size: 10px; font-weight: 600; color: #15803d; border-color: #bbf7d0; background: #f0fdf4; padding: 1px 7px;">
                        <i class="fa fa-bullhorn"></i> Broadcast
                     </button>
                  </div>

                  <!-- Dynamic WAHA Status Badge -->
                  <div id="wahaStatusBadgeContainer">
                     <?php if ($waha_status === 'WORKING'): ?>
                        <button type="button" class="btn btn-success btn-xs btn-flat btn-logout-waha"
                           style="font-size: 11px; padding: 2px 8px; border-radius: 12px; font-weight: 600; border: none; outline: none;"
                           title="Klik untuk ganti nomor (Logout)">
                           <i class="fa fa-check-circle" style="margin-right: 3px;"></i> WA Connected
                        </button>
                     <?php elseif ($waha_status === 'SCAN_QR_CODE'): ?>
                        <button type="button" class="btn btn-warning btn-xs btn-flat" id="btnShowQrModal"
                           style="border-radius: 12px; font-size: 11px; font-weight: 600;">
                           <i class="fa fa-qrcode"></i> Scan QR Code
                        </button>
                     <?php elseif ($waha_status === 'STARTING'): ?>
                        <span class="label label-info"
                           style="font-size: 11px; padding: 4px 8px; border-radius: 12px; font-weight: 600;">
                           <i class="fa fa-spinner fa-spin"></i> Menghubungkan...
                        </span>
                     <?php else: ?>
                        <span class="label label-danger"
                           style="font-size: 11px; padding: 4px 8px; border-radius: 12px; font-weight: 600;"
                           title="WAHA Server Belum Terhubung">
                           <i class="fa fa-circle" style="font-size: 8px; margin-right: 3px;"></i> WA Offline
                        </span>
                     <?php endif; ?>
                  </div>
               </div>

               <!-- Search Input -->
               <div style="margin-top: 12px; position: relative;">
                  <input type="text" id="kolSearchInput" class="form-control input-sm"
                     placeholder="Cari nama, handle, atau nomor HP..."
                     style="border-radius: 4px; padding-left: 30px; height: 32px;">
               </div>
            </div>

            <!-- Scrollable KOL Directory List -->
            <div class="box-body" id="kolListContainer" style="padding: 0; overflow-y: auto; flex: 1;">
               <?php if (empty($kols)): ?>
                  <div style="padding: 30px 20px; text-align: center; color: #94a3b8;">
                     <i class="fa fa-users" style="font-size: 32px; margin-bottom: 8px;"></i>
                     <div>Belum ada data KOL terdaftar.</div>
                  </div>
               <?php else: ?>
                  <ul class="nav nav-pills nav-stacked" id="kolItemList" style="margin: 0;">
                     <?php foreach ($kols as $k):
                        $is_active = ($selected_kol && $selected_kol->id == $k->id);
                        $initial = strtoupper(mb_substr($k->name, 0, 1));
                        $has_phone = !empty($k->phone);
                        $formatted_phone = $has_phone ? $k->phone : 'Belum ada nomor';
                        $last_snippet = !empty($k->last_chat_snippet) ? mb_substr($k->last_chat_snippet, 0, 38) . '...' : 'Belum ada obrolan';
                        ?>
                        <li class="<?= $is_active ? 'active' : ''; ?>" style="border-bottom: 1px solid #f1f5f9;">
                           <a href="<?= site_url('administrator/whatsapp/chat/' . $k->id); ?>" class="kol-item-link"
                              data-name="<?= strtolower($k->name); ?>" data-phone="<?= $k->phone; ?>"
                              style="padding: 12px 16px; border-radius: 0; <?= $is_active ? 'background: #f0fdf4 !important; border-left: 3px solid #16a34a !important;' : ''; ?>">
                              <div style="display: flex; align-items: center; gap: 12px;">

                                 <!-- Avatar Circle -->
                                 <div
                                    style="width: 42px; height: 42px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #cbd5e1;">
                                    <?php if (!empty($k->avatar_url)): ?>
                                       <img src="<?= $k->avatar_url; ?>" alt="<?= _ent($k->name); ?>"
                                          style="width: 100%; height: 100%; object-fit: cover;"
                                          onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                       <span style="display: none; font-weight: 700; color: #475569;"><?= $initial; ?></span>
                                    <?php else: ?>
                                       <span style="font-weight: 700; color: #475569; font-size: 16px;"><?= $initial; ?></span>
                                    <?php endif; ?>
                                 </div>

                                 <!-- KOL Meta Details -->
                                 <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                       <div
                                          style="font-weight: 700; color: <?= $is_active ? '#166534' : '#1e293b'; ?>; font-size: 13px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                          <?= _ent($k->name); ?>
                                       </div>
                                       <span style="font-size: 10px; color: #64748b; margin-left: 4px;">
                                          <?= $k->tier_name ?: 'Tier'; ?>
                                       </span>
                                    </div>

                                    <div
                                       style="font-size: 11px; color: #94a3b8; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; margin-top: 2px;">
                                       <?= _ent($last_snippet); ?>
                                    </div>

                                    <div
                                       style="display: flex; justify-content: space-between; align-items: center; margin-top: 3px;">
                                       <div
                                          style="font-size: 11px; color: #64748b; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                          <i class="fa fa-phone" style="font-size: 10px;"></i> <?= _ent($formatted_phone); ?>
                                       </div>
                                       <div style="display: flex; align-items: center; gap: 4px;">
                                          <?php if ($k->active_campaign_count > 0): ?>
                                             <span class="label label-info"
                                                style="font-size: 9px; padding: 2px 5px; border-radius: 4px;">Active Deal</span>
                                          <?php endif; ?>
                                          <?php
                                          $unread_k_count = isset($unread_by_kol[$k->id]) ? (int) $unread_by_kol[$k->id] : 0;
                                          $show_kol_badge = ($unread_k_count > 0 && !$is_active);
                                          ?>
                                          <span class="badge bg-green kol-unread-badge" id="kol-unread-badge-<?= $k->id; ?>"
                                             style="font-size: 10px; padding: 2px 6px; border-radius: 10px; <?= $show_kol_badge ? 'display: inline-block;' : 'display: none !important;'; ?>"><?= $show_kol_badge ? ($unread_k_count > 99 ? '99+' : $unread_k_count) : ''; ?></span>
                                       </div>
                                    </div>
                                 </div>

                              </div>
                           </a>
                        </li>
                     <?php endforeach; ?>
                  </ul>
               <?php endif; ?>
            </div>

            <!-- Footer Quick Info -->
            <div class="box-footer"
               style="padding: 10px 16px; font-size: 11px; color: #64748b; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
               <div><i class="fa fa-users text-primary"></i> Total: <b><?= count($kols); ?> KOL</b></div>
               <a href="javascript:void(0);" id="btnRefreshSessionStatus" title="Periksa Ulang Status WAHA"
                  style="color: #64748b;"><i class="fa fa-refresh"></i> Refresh Status</a>
            </div>

         </div>
      </div>

      <!-- ======================================================= -->
      <!-- PANEL KANAN: KONSOL PERCAKAPAN WHATSAPP (65% WIDTH)     -->
      <!-- ======================================================= -->
      <div class="col-md-8 col-sm-12 wa-panel-chat" id="panelChatRoom">
         <div class="box box-solid wa-chat-box"
            style="border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; height: 720px; display: flex; flex-direction: column;">

            <?php if (!$selected_kol): ?>
               <!-- Empty State Placeholder -->
               <div
                  style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center; color: #94a3b8;">
                  <div
                     style="width: 80px; height: 80px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                     <i class="fa fa-comments-o" style="font-size: 38px; color: #64748b;"></i>
                  </div>
                  <h4 style="color: #334155; font-weight: 700; margin-bottom: 6px;">Pilih KOL untuk Memulai Percakapan</h4>
                  <p style="max-width: 360px; font-size: 13px;">Pilih kontak KOL dari daftar di sisi kiri untuk membuka
                     riwayat percakapan WhatsApp atau mengirimkan brief.</p>
               </div>
            <?php else: ?>

               <!-- Header Chat Area -->
               <div class="box-header with-border"
                  style="padding: 12px 18px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                  <div
                     style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">

                     <!-- Left: Avatar & Meta -->
                     <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                        <!-- WhatsApp Mobile/Tablet Back to List Button -->
                        <a href="<?= site_url('administrator/whatsapp'); ?>" class="btn-chat-back" id="btnBackToKolList" title="Kembali ke Daftar Percakapan">
                           <i class="fa fa-arrow-left"></i>
                        </a>
                        <div
                           style="width: 44px; height: 44px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; border: 2px solid #22c55e;">
                           <?php if (!empty($selected_kol->avatar_url)): ?>
                              <img src="<?= $selected_kol->avatar_url; ?>" alt="<?= _ent($selected_kol->name); ?>"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                           <?php else: ?>
                              <span
                                 style="font-weight: 700; color: #475569; font-size: 18px;"><?= strtoupper(mb_substr($selected_kol->name, 0, 1)); ?></span>
                           <?php endif; ?>
                        </div>
                        <div>
                           <div style="display: flex; align-items: center; gap: 8px;">
                              <h3 class="box-title" style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">
                                 <?= _ent($selected_kol->name); ?>
                              </h3>
                              <span class="label label-primary"
                                 style="font-size: 10px;"><?= $selected_kol->tier_name ?: 'KOL'; ?></span>
                           </div>
                           <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                              <i class="fa fa-whatsapp text-success"></i>
                              <b><?= $selected_kol->phone ?: '<span class="text-danger">Nomor belum diisi</span>'; ?></b>
                              <?php if ($campaign_context): ?>
                                 &bull; <span class="text-primary"><i class="fa fa-bullhorn"></i>
                                    <?= _ent($campaign_context->campaign_name); ?></span>
                              <?php endif; ?>
                           </div>
                        </div>
                     </div>

                     <!-- Right: Context Action Buttons -->
                     <div style="display: flex; gap: 6px;">
                        <a href="<?= site_url('administrator/kols/view/' . $selected_kol->id); ?>"
                           class="btn btn-default btn-sm btn-flat" target="_blank" title="Lihat Profil Master KOL">
                           <i class="fa fa-user"></i> Profil
                        </a>
                        <a href="<?= site_url('administrator/kols/performance_card/' . $selected_kol->id); ?>"
                           class="btn btn-info btn-sm btn-flat" target="_blank" title="Buka Performance Card KOL">
                           <i class="fa fa-id-card-o"></i> Performance Card
                        </a>
                     </div>

                  </div>
               </div>

               <!-- Phone Warning Banner if invalid or missing -->
               <?php
               $clean_digits = preg_replace('/[^0-9]/', '', $selected_kol->phone ?? '');
               $is_phone_invalid = (empty($selected_kol->phone) || strlen($clean_digits) < 9);
               ?>
               <?php if ($is_phone_invalid): ?>
                  <div
                     style="background: #fffbeb; border-bottom: 1px solid #fef3c7; padding: 10px 18px; font-size: 12px; color: #92400e; display: flex; align-items: center; gap: 8px;">
                     <i class="fa fa-exclamation-triangle" style="font-size: 14px;"></i>
                     <span>Nomor telepon KOL ini belum valid atau kurang dari 9 digit (tercatat:
                        <b><?= _ent($selected_kol->phone ?: 'Kosong'); ?></b>). Anda perlu melengkapi nomor WhatsApp valid di
                        <a href="<?= site_url('administrator/kols/edit/' . $selected_kol->id); ?>"
                           style="text-decoration: underline; font-weight: bold; color: #78350f;">Edit Profil KOL</a> agar
                        dapat mengirim pesan.</span>
                  </div>
               <?php endif; ?>

               <!-- Conversation Message Stream (AdminLTE Direct Chat) -->
               <div class="box-body" id="chatMessageStream"
                  style="flex: 1; overflow-y: auto; padding: 18px 22px; background: #efeae2; background-image: radial-gradient(#dcd5cb 0.75px, transparent 0.75px); background-size: 16px 16px;">

                  <!-- Date Divider -->
                  <div style="text-align: center; margin-bottom: 18px;">
                     <span
                        style="background: rgba(255,255,255,0.85); padding: 4px 12px; border-radius: 12px; font-size: 11px; color: #64748b; box-shadow: 0 1px 3px rgba(0,0,0,0.05); font-weight: 600;">
                        Percakapan WhatsApp Terintegrasi Database & WAHA
                     </span>
                  </div>

                  <!-- Messages Loop -->
                  <div id="messageList">
                     <?php if (empty($messages)): ?>
                        <div id="emptyChatMessage" style="padding: 40px 20px; text-align: center; color: #94a3b8;">
                           <i class="fa fa-comments-o" style="font-size: 36px; margin-bottom: 8px; color: #cbd5e1;"></i>
                           <div style="font-weight: 700; color: #475569; font-size: 14px;">Belum Ada Percakapan</div>
                           <div
                              style="font-size: 12px; margin-top: 4px; max-width: 400px; margin-left: auto; margin-right: auto;">
                              Belum ada riwayat pesan WhatsApp dengan <?= _ent($selected_kol->name); ?>. Kirim pesan atau
                              gunakan template di bawah untuk memulai obrolan.</div>
                        </div>
                     <?php endif; ?>
                     <?php
                     $last_rendered_id = 0;
                     foreach ($messages as $msg):
                        $is_outbound = ($msg['direction'] === 'OUTBOUND');
                        if (!empty($msg['id']) && $msg['id'] > $last_rendered_id) {
                           $last_rendered_id = $msg['id'];
                        }
                        ?>
                        <div class="chat-message-item <?= $is_outbound ? 'chat-item-outbound' : 'chat-item-inbound'; ?>" data-id="<?= $msg['id']; ?>">
                           <div class="chat-bubble <?= $is_outbound ? 'chat-bubble-out' : 'chat-bubble-in'; ?>">
                              
                              <div class="chat-bubble-sender <?= $is_outbound ? 'sender-out' : 'sender-in'; ?>">
                                 <?= _ent($msg['sender_name']); ?>
                              </div>

                              <!-- Media Attachment Rendering -->
                              <?php if (!empty($msg['media_url'])): ?>
                                 <?php
                                 $is_img = (!empty($msg['media_mime']) && strpos($msg['media_mime'], 'image/') === 0) || preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $msg['media_url']);
                                 ?>
                                 <?php if ($is_img): ?>
                                    <div
                                       style="margin-bottom: 8px; border-radius: 6px; overflow: hidden; max-width: 320px; border: 1px solid rgba(0,0,0,0.08); background: #f1f5f9;">
                                       <a href="javascript:void(0);" class="preview-media" data-type="image"
                                          data-url="<?= $msg['media_url']; ?>" title="Klik untuk perbesar gambar">
                                          <img src="<?= $msg['media_url']; ?>" alt="Lampiran WhatsApp"
                                             style="width: 100%; max-height: 260px; object-fit: cover; display: block; border-radius: 4px;">
                                       </a>
                                    </div>
                                 <?php else: ?>
                                    <div
                                       style="margin-bottom: 8px; padding: 8px 12px; background: rgba(0,0,0,0.04); border-radius: 6px; border: 1px solid rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: space-between; gap: 10px; max-width: 320px;">
                                       <div style="display: flex; align-items: center; gap: 8px; min-width: 0; padding-right: 8px;">
                                          <i class="fa fa-file-text-o text-primary" style="font-size: 20px; flex-shrink: 0;"></i>
                                          <div
                                             style="font-size: 12px; font-weight: 600; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                             <?= _ent(!empty($msg['filename']) ? $msg['filename'] : urldecode(basename($msg['media_url']))); ?>
                                          </div>
                                       </div>
                                       <div style="display: flex; gap: 4px; flex-shrink: 0;">
                                          <a href="javascript:void(0);" data-url="<?= $msg['media_url']; ?>" data-type="document"
                                             class="preview-media btn btn-default btn-xs btn-flat"
                                             style="border-radius: 4px; background: #fff; border-color: #cbd5e1;"
                                             title="Lihat Dokumen">
                                             <i class="fa fa-eye" style="color: #475569;"></i>
                                          </a>
                                          <a href="<?= $msg['media_url']; ?>" target="_blank" download
                                             class="btn btn-default btn-xs btn-flat"
                                             style="border-radius: 4px; background: #fff; border-color: #cbd5e1;"
                                             title="Unduh Dokumen">
                                             <i class="fa fa-download" style="color: #475569;"></i>
                                          </a>
                                       </div>
                                    </div>
                                 <?php endif; ?>
                              <?php endif; ?>

                              <?php if (!empty($msg['text'])): ?>
                                 <div class="chat-bubble-text"><?= _ent(trim($msg['text'])); ?></div>
                              <?php endif; ?>

                              <div class="chat-bubble-meta">
                                 <span><?= $msg['time']; ?></span>
                                 <?php if ($is_outbound): ?>
                                    <?php if ($msg['status'] === 'READ'): ?>
                                       <span title="Dibaca"><i class="fa fa-check text-primary" style="margin-right: -4px;"></i><i
                                             class="fa fa-check text-primary"></i></span>
                                    <?php elseif ($msg['status'] === 'DELIVERED'): ?>
                                       <span title="Tersampaikan"><i class="fa fa-check text-muted"
                                             style="margin-right: -4px;"></i><i class="fa fa-check text-muted"></i></span>
                                    <?php elseif ($msg['status'] === 'SENT'): ?>
                                       <span title="Terkirim ke Server"><i class="fa fa-check text-muted"></i></span>
                                    <?php else: ?>
                                       <span title="Pending / Tersimpan"><i class="fa fa-clock-o text-muted"></i></span>
                                    <?php endif; ?>
                                 <?php endif; ?>
                              </div>

                           </div>
                        </div>
                     <?php endforeach; ?>
                  </div>

               </div>

               <!-- Quick Template Toolbar -->
               <div
                  style="padding: 8px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; overflow-x: auto; white-space: nowrap;">
                  <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                     <i class="fa fa-bolt text-yellow"></i> Template:
                  </span>

                  <!-- Dropdown Full Templates -->
                  <div class="btn-group">
                     <button type="button" class="btn btn-default btn-xs btn-flat dropdown-toggle" data-toggle="dropdown"
                        style="font-weight: 600; border-radius: 4px;">
                        Pilih Template Lengkap <span class="caret"></span>
                     </button>
                     <ul class="dropdown-menu" role="menu" style="min-width: 280px; padding: 6px 0;">
                        <?php foreach ($templates as $tpl): ?>
                           <li>
                              <a href="javascript:void(0);" class="btn-insert-template"
                                 data-text="<?= htmlspecialchars($tpl['text']); ?>"
                                 style="padding: 6px 14px; font-size: 12px;">
                                 <span class="label label-default"
                                    style="font-size: 9px; margin-right: 4px;"><?= $tpl['category']; ?></span>
                                 <b><?= _ent($tpl['title']); ?></b>
                              </a>
                           </li>
                        <?php endforeach; ?>
                     </ul>
                  </div>

                  <!-- Shortcut Pills -->
                  <?php if (isset($templates[1])): ?>
                     <button type="button" class="btn btn-default btn-xs btn-flat btn-quick-pill"
                        data-text="<?= htmlspecialchars($templates[1]['text']); ?>"
                        style="border-radius: 12px; font-size: 11px; padding: 2px 10px;">
                        📄 Kirim Brief VT
                     </button>
                  <?php endif; ?>
                  <?php if (isset($templates[2])): ?>
                     <button type="button" class="btn btn-default btn-xs btn-flat btn-quick-pill"
                        data-text="<?= htmlspecialchars($templates[2]['text']); ?>"
                        style="border-radius: 12px; font-size: 11px; padding: 2px 10px;">
                        📦 Resi Pengiriman
                     </button>
                  <?php endif; ?>
                  <?php if (isset($templates[3])): ?>
                     <button type="button" class="btn btn-default btn-xs btn-flat btn-quick-pill"
                        data-text="<?= htmlspecialchars($templates[3]['text']); ?>"
                        style="border-radius: 12px; font-size: 11px; padding: 2px 10px;">
                        ⏰ Reminder Draft H-1
                     </button>
                  <?php endif; ?>
                  <?php if (isset($templates[4])): ?>
                     <button type="button" class="btn btn-default btn-xs btn-flat btn-quick-pill"
                        data-text="<?= htmlspecialchars($templates[4]['text']); ?>"
                        style="border-radius: 12px; font-size: 11px; padding: 2px 10px;">
                        🚀 Reminder Posting
                     </button>
                  <?php endif; ?>
               </div>

               <!-- Footer Input Controls -->
               <div class="box-footer" style="padding: 12px 18px; background: #ffffff; border-top: 1px solid #e2e8f0;">
                  <form id="formSendMessage" onsubmit="return false;">
                     <input type="hidden" id="selectedKolId" value="<?= $selected_kol->id; ?>">
                     <input type="hidden" id="campaignKolId"
                        value="<?= $campaign_context ? $campaign_context->id : ''; ?>">
                     <input type="hidden" id="lastRenderedMsgId" value="<?= $last_rendered_id; ?>">

                     <!-- Hidden File Input for Media Attachments -->
                     <input type="file" id="mediaFileInput" name="media_file"
                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip" style="display: none;">

                     <!-- Attachment Preview Bar (Hidden by default) -->
                     <div id="attachmentPreviewBar"
                        style="display: none; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; margin-bottom: 8px; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                           <div id="attachmentThumbnail"
                              style="width: 38px; height: 38px; border-radius: 4px; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #cbd5e1;">
                              <i class="fa fa-paperclip text-muted" style="font-size: 16px;"></i>
                           </div>
                           <div style="min-width: 0;">
                              <div id="attachmentFileName"
                                 style="font-size: 12px; font-weight: 700; color: #1e293b; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 320px;">
                                 file.pdf</div>
                              <div id="attachmentFileSize" style="font-size: 10px; color: #64748b;">0 KB</div>
                           </div>
                        </div>
                        <button type="button" id="btnCancelAttachment" class="btn btn-default btn-xs btn-flat"
                           title="Batalkan Lampiran"
                           style="border-radius: 50%; width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">
                           <i class="fa fa-times text-danger"></i>
                        </button>
                     </div>

                     <div style="display: flex; gap: 8px; align-items: flex-end;">

                        <!-- Attachment Icon -->
                        <button type="button" id="btnAttachFile" class="btn btn-default btn-flat"
                           style="height: 42px; border-radius: 4px;" title="Lampirkan Dokumen PDF / Gambar"
                           <?= $is_phone_invalid ? 'disabled' : ''; ?>>
                           <i class="fa fa-paperclip text-muted" style="font-size: 16px;"></i>
                        </button>

                        <!-- Textarea -->
                        <div style="flex: 1;">
                           <textarea id="chatInputMessage" class="form-control" rows="1"
                              placeholder="Ketik pesan WhatsApp untuk <?= _ent($selected_kol->name); ?> (Tekan Enter untuk kirim)..."
                              style="resize: none; border-radius: 4px; padding: 10px 12px; min-height: 42px; max-height: 120px; font-size: 13px;"
                              <?= $is_phone_invalid ? 'disabled' : ''; ?>></textarea>
                        </div>

                        <!-- Send Button -->
                        <button type="button" id="btnSubmitMessage" class="btn btn-success btn-flat"
                           style="height: 42px; padding: 0 20px; font-weight: 700; border-radius: 4px; display: flex; align-items: center; gap: 6px; background-color: #22c55e; border-color: #16a34a;"
                           <?= $is_phone_invalid ? 'disabled' : ''; ?>>
                           <i class="fa fa-send"></i> <span>Kirim</span>
                        </button>

                     </div>
                     <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px; font-size: 11px; color: #94a3b8;">
                        <span><kbd>Enter</kbd> untuk kirim, <kbd>Shift+Enter</kbd> untuk baris baru &bull; Polling Realtime
                           Aktif</span>
                        <span id="charCount">0 karakter</span>
                     </div>
                  </form>
               </div>

            <?php endif; ?>

         </div>
      </div>

   </div>
</section>
</div><!-- /.wa-chat-page -->

<!-- ======================================================= -->
<!-- MODAL QR CODE UNTUK PAIRING WAHA                       -->
<!-- ======================================================= -->
<div class="modal fade" id="modalWahaQr" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content" style="border-radius: 8px;">
         <div class="modal-header"
            style="background: #15803d; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;">
            <button type="button" class="close" data-dismiss="modal"
               style="color: #ffffff; opacity: 0.9;">&times;</button>
            <h4 class="modal-title" style="font-size: 15px; font-weight: 700;"><i class="fa fa-whatsapp"></i> Hubungkan
               WhatsApp</h4>
         </div>
         <div class="modal-body text-center" style="padding: 24px 16px;">
            <p style="font-size: 12px; color: #64748b; margin-bottom: 16px;">
               Buka WhatsApp di ponsel Anda &gt; Pengaturan &gt; Perangkat Tertaut &gt; Tautkan Perangkat.
            </p>
            <div id="qrCodeContainer"
               style="min-height: 200px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; margin-bottom: 16px;">
               <div id="qrLoading">
                  <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                  <div style="font-size: 11px; color: #94a3b8; margin-top: 6px;">Memuat QR Code...</div>
               </div>
               <img id="qrCodeImage" src="" alt="WhatsApp QR Code"
                  style="display: none; max-width: 100%; border-radius: 4px;">
            </div>
            <button type="button" class="btn btn-default btn-sm btn-flat btn-block" id="btnRefreshQr">
               <i class="fa fa-refresh"></i> Muat Ulang QR
            </button>
         </div>
      </div>
   </div>
</div>

<!-- ======================================================= -->
<!-- MODAL WHATSAPP BROADCASTING MASSAL                      -->
<!-- ======================================================= -->
<div class="modal fade" id="modalBroadcastWhatsApp" tabindex="-1" role="dialog" data-backdrop="static">
   <div class="modal-dialog modal-lg" role="document" style="width: 95%; max-width: 1024px; margin: 25px auto;">
      <div class="modal-content"
         style="border-radius: 12px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: none;">

         <!-- Modal Header -->
         <div class="modal-header"
            style="background-color: #0f172a; color: white; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: none; position: relative;">
            <div style="display: flex; align-items: center; gap: 12px; text-align: left; width: 100%;">
               <div
                  style="background-color: #22c55e; width: 40px; height: 40px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                  <i class="fa fa-bullhorn" style="font-size: 18px; color: white;"></i>
               </div>
               <div style="flex: 1;">
                  <h4 class="modal-title"
                     style="font-weight: 700; font-size: 18px; line-height: 1.25; margin: 0; color: white;">
                     WhatsApp Broadcasting
                  </h4>
                  <p style="margin: 2px 0 0 0; font-size: 12px; color: #94a3b8;">
                     Kirim pesan serentak dengan template & personalisasi.
                  </p>
               </div>
            </div>
            <button type="button" class="close" data-dismiss="modal"
               style="color: #94a3b8; opacity: 1; text-shadow: none; font-size: 24px; line-height: 1; position: absolute; right: 24px; top: 24px; border: none; background: transparent; cursor: pointer; float: none;">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>

         <!-- Modal Body -->
         <div class="modal-body"
            style="padding: 0; background-color: #ffffff; display: flex; height: 65vh; min-height: 500px; position: relative;">
            <div id="bcMainContentWrapper" style="display: flex; width: 100%;">

               <!-- PANEL KIRI: TARGET PENERIMA (40% width) -->
               <div
                  style="width: 40%; border-right: 1px solid #e2e8f0; display: flex; flex-direction: column; background-color: #f8fafc;">
                  <div style="padding: 16px; border-bottom: 1px solid #e2e8f0; flex-shrink: 0;">
                     <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h3
                           style="margin: 0; font-weight: 600; color: #1e293b; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                           <i class="fa fa-users" style="color: #2563eb;"></i> Pilih Target (<span id="bcSelectedBadge"
                              style="display: inline;">0</span>)
                        </h3>
                        <div style="display: flex; gap: 8px; align-items: center;">
                           <a href="javascript:void(0)" id="bcBtnSelectAll"
                              style="font-size: 12px; color: #2563eb; font-weight: 500; text-decoration: none;">Pilih
                              Semua</a>
                           <span style="color: #cbd5e1;">|</span>
                           <a href="javascript:void(0)" id="bcBtnDeselectAll"
                              style="font-size: 12px; color: #64748b; text-decoration: none;">Reset</a>
                        </div>
                     </div>

                     <!-- Search & Filter Controls -->
                     <div style="display: flex; flex-direction: column; gap: 8px;">
                        <div style="position: relative;">
                           <input type="text" id="bcSearchInput" class="form-control" placeholder="Cari nama, No HP..."
                              style="width: 100%; padding-left: 36px; padding-right: 12px; padding-top: 8px; padding-bottom: 8px; background-color: white; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; box-shadow: none;">
                        </div>
                        <div style="display: flex; gap: 8px;">
                           <select id="bcFilterTier" class="form-control"
                              style="width: 50%; background-color: white; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12px; padding: 6px 8px; height: 34px; box-shadow: none;">
                              <option value="">Semua Tier</option>
                              <?php if (!empty($all_tiers)): ?>
                                 <?php foreach ($all_tiers as $t): ?>
                                    <option value="<?= $t->id; ?>"><?= _ent($t->name); ?></option>
                                 <?php endforeach; ?>
                              <?php endif; ?>
                           </select>
                           <select id="bcFilterCategory" class="form-control"
                              style="width: 50%; background-color: white; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12px; padding: 6px 8px; height: 34px; box-shadow: none;">
                              <option value="">Semua Kategori</option>
                              <?php if (!empty($all_categories)): ?>
                                 <?php foreach ($all_categories as $c): ?>
                                    <option value="<?= $c->id; ?>"><?= _ent($c->name); ?></option>
                                 <?php endforeach; ?>
                              <?php endif; ?>
                           </select>
                        </div>
                        <div id="bcTotalCounter" style="display: none;">0 KOL</div> <!-- Hidden, kept for js logic -->
                     </div>
                  </div>

                  <!-- Scrollable KOL Directory List -->
                  <div id="bcKolListContainer" style="flex: 1; overflow-y: auto; padding: 8px;">
                     <div id="bcLoadingKols" style="padding: 30px 10px; text-align: center; color: #94a3b8;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <div style="margin-top: 8px; font-size: 12px;">Memuat data KOL...</div>
                     </div>
                  </div>
               </div>

               <!-- PANEL KANAN: KOMPOSISI PESAN & PREVIEW (60% width) -->
               <div style="width: 60%; display: flex; flex-direction: column; background-color: white;">
                  <div style="padding: 24px; flex: 1; overflow-y: auto; display: flex; flex-direction: column;">
                     <h3
                        style="margin: 0 0 16px 0; font-weight: 600; color: #1e293b; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-commenting" style="color: #2563eb;"></i> Template Pesan
                     </h3>

                     <!-- Template Selection -->
                     <div style="margin-bottom: 20px;">
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;" id="bcTemplateButtonGroup">
                           <button type="button" class="btn bc-tpl-btn active" data-tpl="TPL_DEAL"
                              style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid #bfdbfe; background-color: #eff6ff; color: #1d4ed8; box-shadow: none; outline: none;">
                              <i class="fa fa-handshake-o" style="margin-right: 4px;"></i> Undangan
                           </button>
                           <button type="button" class="btn bc-tpl-btn" data-tpl="TPL_BRIEF"
                              style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid #e2e8f0; background-color: #f8fafc; color: #475569; box-shadow: none; outline: none;">
                              <i class="fa fa-pencil-square-o" style="margin-right: 4px;"></i> Brief VT
                           </button>
                           <button type="button" class="btn bc-tpl-btn" data-tpl="TPL_REMINDER_DRAFT"
                              style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid #e2e8f0; background-color: #f8fafc; color: #475569; box-shadow: none; outline: none;">
                              <i class="fa fa-clock-o" style="margin-right: 4px;"></i> Draft (H-1)
                           </button>
                           <button type="button" class="btn bc-tpl-btn" data-tpl="TPL_REMINDER_POST"
                              style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid #e2e8f0; background-color: #f8fafc; color: #475569; box-shadow: none; outline: none;">
                              <i class="fa fa-bullhorn" style="margin-right: 4px;"></i> Posting
                           </button>
                           <button type="button" class="btn bc-tpl-btn" data-tpl="TPL_SHIPMENT"
                              style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid #e2e8f0; background-color: #f8fafc; color: #475569; box-shadow: none; outline: none;">
                              <i class="fa fa-cube" style="margin-right: 4px;"></i> Resi
                           </button>
                           <button type="button" class="btn bc-tpl-btn" data-tpl="CUSTOM"
                              style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid #e2e8f0; background-color: #f8fafc; color: #475569; box-shadow: none; outline: none;">
                              <i class="fa fa-pencil" style="margin-right: 4px;"></i> Kustom
                           </button>
                        </div>
                     </div>

                     <!-- Editor -->
                     <div
                        style="display: flex; flex-direction: column; flex: 1; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 20px;">
                        <!-- Toolbar -->
                        <div
                           style="background-color: #f8fafc; padding: 8px 12px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; flex-shrink: 0;">
                           <span
                              style="font-size: 10px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; flex-shrink: 0; margin-right: 4px;">Variabel:</span>
                           <button type="button" class="btn btn-insert-bc-tag" data-tag="[Nama KOL]"
                              style="padding: 4px 8px; background-color: white; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 10px; font-weight: 500; color: #475569; box-shadow: none; flex-shrink: 0;"
                              title="Klik untuk menyisipkan Nama KOL"><i class="fa fa-user"
                                 style="margin-right: 4px; color: #94a3b8;"></i> Nama KOL</button>
                           <button type="button" class="btn btn-insert-bc-tag" data-tag="[Nama Campaign]"
                              style="padding: 4px 8px; background-color: white; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 10px; font-weight: 500; color: #475569; box-shadow: none; flex-shrink: 0;"
                              title="Klik untuk menyisipkan Nama Campaign"><i class="fa fa-tag"
                                 style="margin-right: 4px; color: #94a3b8;"></i> Nama Campaign</button>
                           <button type="button" class="btn btn-insert-bc-tag" data-tag="[Judul VT]"
                              style="padding: 4px 8px; background-color: white; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 10px; font-weight: 500; color: #475569; box-shadow: none; flex-shrink: 0;"
                              title="Klik untuk menyisipkan Judul VT"><i class="fa fa-video-camera"
                                 style="margin-right: 4px; color: #94a3b8;"></i> Judul VT</button>
                           <button type="button" class="btn btn-insert-bc-tag" data-tag="[Tgl Posting]"
                              style="padding: 4px 8px; background-color: white; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 10px; font-weight: 500; color: #475569; box-shadow: none; flex-shrink: 0;"
                              title="Klik untuk menyisipkan Tanggal Posting"><i class="fa fa-calendar"
                                 style="margin-right: 4px; color: #94a3b8;"></i> Tgl Posting</button>

                           <select id="bcCampaignSelect" class="form-control"
                              style="margin-right: auto; width: 140px; background-color: transparent; border: 1px solid #e2e8f0; color: #475569; font-size: 11px; font-weight: 500; padding: 4px 6px; height: auto; box-shadow: none; cursor: pointer; border-radius: 4px;">
                              <option value="">-- Pilih Campaign --</option>
                              <?php if (!empty($all_campaigns)): ?>
                                 <?php foreach ($all_campaigns as $camp): ?>
                                    <option value="<?= $camp->id; ?>" data-name="<?= _ent($camp->name); ?>">

                                       <?= _ent($camp->name); ?>
                                    </option>
                                 <?php endforeach; ?>
                              <?php endif; ?>
                           </select>
                        </div>
                        <textarea id="bcMessageText" class="form-control"
                           style="width: 100%; flex: 1; min-height: 200px; padding: 16px; font-size: 14px; color: #334155; border: none; outline: none; resize: none; box-shadow: none; line-height: 1.5;"
                           placeholder="Ketik pesan disini..."></textarea>
                     </div>
                     <div
                        style="display: flex; justify-content: space-between; font-size: 11px; color: #94a3b8; margin-top: -15px; margin-bottom: 20px; padding: 0 4px;">
                        <span style="display: none;" id="bcPreviewRecipientInfo"></span> <!-- hidden to simplify -->
                        <span id="bcCharCount">0 karakter</span>
                     </div>

                     <!-- Preview -->
                     <div style="margin-top: auto; padding-top: 12px;">
                        <div
                           style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                           <h4
                              style="margin: 0; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">
                              <i class="fa fa-whatsapp" style="color: #22c55e; margin-right: 4px;"></i> Preview (<span
                                 id="bcPreviewKolName">-</span>)
                           </h4>
                        </div>
                        <div
                           style="background-color: #efeae2; border-radius: 12px; padding: 12px 16px; border: 1px solid #dcd7cf;">
                           <div style="display: flex; justify-content: flex-end;">
                              <div
                                 style="background-color: #d9fdd3; border-radius: 8px; border-top-right-radius: 0; padding: 8px 12px; max-width: 85%; box-shadow: 0 1px 2px rgba(0,0,0,0.05); position: relative;">
                                 <div id="bcPreviewBody"
                                    style="font-size: 13px; color: #111b21; line-height: 1.4; white-space: pre-wrap; word-break: break-word;">
                                    Halo Kak...
                                 </div>
                                 <div
                                    style="font-size: 10px; color: #667781; text-align: right; margin-top: 2px; display: flex; justify-content: flex-end; align-items: center; gap: 4px;">
                                    <span id="bcPreviewTime"><?= date('H:i'); ?></span>
                                    <i class="fa fa-check" style="color: #53bdeb; margin-right: -4px;"></i>
                                    <i class="fa fa-check" style="color: #53bdeb;"></i>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>

                  </div>
               </div>
            </div>

            <!-- Overlay Progress State -->
            <div id="bcProgressOverlay"
               style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #ffffff; z-index: 10; flex-direction: column; padding: 40px;">
               <div style="text-align: center; margin-bottom: 24px;">
                  <div
                     style="width: 64px; height: 64px; border-radius: 50%; background: #dcfce7; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                     <i class="fa fa-paper-plane text-success" style="font-size: 28px; color: #22c55e;"></i>
                  </div>
                  <h3 style="font-weight: 700; font-size: 20px; color: #1e293b; margin: 0 0 8px 0;">Mengirim
                     Broadcast...</h3>
                  <p style="color: #64748b; font-size: 14px; margin: 0;">Mohon jangan tutup jendela ini selama proses
                     berlangsung.</p>
               </div>

               <div
                  style="max-width: 600px; width: 100%; margin: 0 auto; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                     <span id="bcProgressStatus" style="font-weight: 600; font-size: 14px; color: #1e293b;">Menyiapkan
                        pengiriman...</span>
                     <span id="bcProgressPercent" style="font-weight: 700; font-size: 16px; color: #16a34a;">0%</span>
                  </div>
                  <div class="progress"
                     style="height: 12px; margin-bottom: 16px; border-radius: 6px; background-color: #e2e8f0; box-shadow: none;">
                     <div class="progress-bar progress-bar-success" id="bcProgressBar"
                        style="width: 0%; box-shadow: none;"></div>
                  </div>
                  <div id="bcLogContainer"
                     style="height: 200px; overflow-y: auto; font-size: 12px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; color: #475569;">
                  </div>
               </div>
            </div>
         </div>

         <!-- Modal Footer -->
         <div class="modal-footer"
            style="background-color: #f8fafc; padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; text-align: left;">
            <div style="flex: 1;">
               <p style="margin: 0; font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 6px;">
                  <i class="fa fa-shield" style="color: #94a3b8;"></i> Pacing interval ~800ms aktif
               </p>
            </div>
            <div style="display: flex; gap: 12px; align-items: center; justify-content: flex-end;">
               <button type="button" class="btn btn-default" data-dismiss="modal" id="bcBtnClose"
                  style="padding: 8px 20px; font-size: 14px; font-weight: 500; color: #475569; background-color: transparent; border: none; box-shadow: none;">Batal</button>
               <button type="button" class="btn btn-danger" id="bcBtnStop"
                  style="display: none; padding: 8px 24px; font-size: 14px; font-weight: 600; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                  <i class="fa fa-stop-circle"></i> Hentikan Sisa Antrean
               </button>
               <button type="button" class="btn btn-success" id="bcBtnStartSend"
                  style="padding: 8px 24px; font-size: 14px; font-weight: 600; border-radius: 8px; background-color: #22c55e; border-color: #22c55e; box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.2), 0 2px 4px -2px rgba(34, 197, 94, 0.2); display: flex; align-items: center; gap: 8px;"
                  disabled>
                  <i class="fa fa-paper-plane"></i> Kirim Broadcast (<span id="bcBtnCount">0</span>)
               </button>
               <button type="button" class="btn btn-default" id="bcBtnBackToEdit"
                  style="display: none; padding: 8px 24px; font-size: 14px; font-weight: 500; border-radius: 8px; box-shadow: none;">
                  <i class="fa fa-arrow-left"></i> Kembali ke Form
               </button>
               <span id="bcSummaryText" style="display: none;"></span> <!-- hidden -->
            </div>
         </div>

      </div>
   </div>
</div>

<!-- Modal Media Preview -->
<div class="modal fade" id="modalImagePreview" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.8);">
   <div class="modal-dialog modal-lg" role="document" style="margin-top: 5vh; width: 85%; max-width: 1000px;">
      <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
         <div class="modal-header" style="border: none; padding-bottom: 0; text-align: right;">
            <button type="button" class="close" data-dismiss="modal"
               style="color: #fff; opacity: 1; font-size: 36px; text-shadow: 0 1px 4px rgba(0,0,0,0.5);">&times;</button>
         </div>
         <div class="modal-body text-center" style="padding: 0;">
            <img id="previewModalImage" src="" alt="Preview"
               style="max-width: 100%; max-height: 75vh; object-fit: contain; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); display: none;">
            <iframe id="previewModalIframe" src=""
               style="width: 100%; height: 75vh; border: none; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); background: #fff; display: none;"></iframe>
            <div style="margin-top: 15px;">
               <a id="previewModalDownloadBtn" href="" download class="btn btn-default btn-flat"><i
                     class="fa fa-download"></i> <span id="previewModalDownloadText">Unduh</span></a>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- ======================================================= -->
<!-- JAVASCRIPT LOGIC UNTUK INTERAKTIVITAS BACKEND/FRONTEND  -->
<!-- ======================================================= -->
<script type="text/javascript">
   $(document).ready(function () {
      // Smooth back button on mobile & tablet
      $(document).on('click', '#btnBackToKolList', function (e) {
         if (window.history.length > 1 && document.referrer && document.referrer.indexOf('/administrator/whatsapp') !== -1) {
            e.preventDefault();
            window.history.back();
         }
      });
      var currentUserName = <?= json_encode($current_user_name); ?>;

      // 1. Instant Search KOL in left panel
      $('#kolSearchInput').on('keyup', function () {
         var query = $(this).val().toLowerCase().trim();
         $('#kolItemList li').each(function () {
            var name = $(this).find('.kol-item-link').data('name') || '';
            var phone = String($(this).find('.kol-item-link').data('phone') || '');
            if (name.indexOf(query) !== -1 || phone.indexOf(query) !== -1) {
               $(this).show();
            } else {
               $(this).hide();
            }
         });
      });

      // 2. Scroll chat message stream to bottom
      function scrollToBottom() {
         var stream = document.getElementById('chatMessageStream');
         if (stream) {
            stream.scrollTop = stream.scrollHeight;
         }
      }
      scrollToBottom();

      // 3. Template inserter logic
      $(document).on('click', '.btn-insert-template, .btn-quick-pill', function (e) {
         e.preventDefault();
         var templateText = $(this).attr('data-text');
         if (templateText) {
            $('#chatInputMessage').val(templateText).focus();
            $('#charCount').text(templateText.length + ' karakter');
            autoResizeTextarea();
         }
      });

      // 4. Character count & Auto-resize textarea
      function autoResizeTextarea() {
         var textarea = document.getElementById('chatInputMessage');
         if (textarea) {
            textarea.style.height = '42px';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
         }
      }

      $('#chatInputMessage').on('input', function () {
         var len = $(this).val().length;
         $('#charCount').text(len + ' karakter');
         autoResizeTextarea();
      });

      // 5. File Attachment Handling
      var attachedFile = null;

      $('#btnAttachFile').on('click', function () {
         $('#mediaFileInput').click();
      });

      $('#mediaFileInput').on('change', function () {
         var files = this.files;
         if (!files || !files.length) return;
         var file = files[0];

         if (file.size > 15 * 1024 * 1024) {
            alert('Ukuran file maksimal adalah 15 MB.');
            $(this).val('');
            return;
         }

         attachedFile = file;
         $('#attachmentFileName').text(file.name);

         var sizeText = (file.size < 1024 * 1024)
            ? (file.size / 1024).toFixed(1) + ' KB'
            : (file.size / (1024 * 1024)).toFixed(2) + ' MB';
         $('#attachmentFileSize').text(sizeText);

         if (file.type.indexOf('image/') === 0) {
            var reader = new FileReader();
            reader.onload = function (e) {
               $('#attachmentThumbnail').html('<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover;">');
            };
            reader.readAsDataURL(file);
         } else if (file.type === 'application/pdf' || file.name.endsWith('.pdf')) {
            $('#attachmentThumbnail').html('<i class="fa fa-file-pdf-o text-danger" style="font-size: 18px;"></i>');
         } else {
            $('#attachmentThumbnail').html('<i class="fa fa-file-text-o text-primary" style="font-size: 18px;"></i>');
         }

         $('#attachmentPreviewBar').css('display', 'flex');
         $('#chatInputMessage').attr('placeholder', 'Ketik caption untuk lampiran ini (opsional)...').focus();
      });

      function resetAttachment() {
         attachedFile = null;
         $('#mediaFileInput').val('');
         $('#attachmentPreviewBar').hide();
         $('#attachmentThumbnail').html('<i class="fa fa-paperclip text-muted" style="font-size: 16px;"></i>');
         $('#chatInputMessage').attr('placeholder', 'Ketik pesan WhatsApp (Tekan Enter untuk kirim)...');
      }

      $('#btnCancelAttachment').on('click', function () {
         resetAttachment();
      });

      function buildMediaHtml(msg) {
         if (!msg.media_url) return '';
         var isImg = (msg.media_mime && msg.media_mime.indexOf('image/') === 0) || (/\.(jpg|jpeg|png|webp|gif)$/i.test(msg.media_url));
         if (isImg) {
            return `
            <div style="margin-bottom: 8px; border-radius: 6px; overflow: hidden; max-width: 320px; border: 1px solid rgba(0,0,0,0.08); background: #f1f5f9;">
               <a href="javascript:void(0);" class="preview-media" data-type="image" data-url="${msg.media_url}" title="Klik untuk perbesar gambar">
                  <img src="${msg.media_url}" alt="Lampiran WhatsApp" style="width: 100%; max-height: 260px; object-fit: cover; display: block; border-radius: 4px;">
               </a>
            </div>
         `;
         } else {
            var fname = msg.filename || decodeURIComponent(msg.media_url.split('/').pop()) || 'Dokumen';
            return `
            <div style="margin-bottom: 8px; padding: 8px 12px; background: rgba(0,0,0,0.04); border-radius: 6px; border: 1px solid rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: space-between; gap: 10px; max-width: 320px;">
               <div style="display: flex; align-items: center; gap: 8px; min-width: 0; padding-right: 8px;">
                  <i class="fa fa-file-text-o text-primary" style="font-size: 20px; flex-shrink: 0;"></i>
                  <div style="font-size: 12px; font-weight: 600; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                     ${escapeHtml(fname)}
                  </div>
               </div>
               <div style="display: flex; gap: 4px; flex-shrink: 0;">
                  <a href="javascript:void(0);" data-url="${msg.media_url}" data-type="document" class="preview-media btn btn-default btn-xs btn-flat" style="border-radius: 4px; background: #fff; border-color: #cbd5e1;" title="Lihat Dokumen">
                     <i class="fa fa-eye" style="color: #475569;"></i>
                  </a>
                  <a href="${msg.media_url}" target="_blank" download class="btn btn-default btn-xs btn-flat" style="border-radius: 4px; background: #fff; border-color: #cbd5e1;" title="Unduh Dokumen">
                     <i class="fa fa-download" style="color: #475569;"></i>
                  </a>
               </div>
            </div>
         `;
         }
      }

      // 6. Send message action (Text & Media)
      function triggerSendMessage() {
         var kolId = $('#selectedKolId').val();
         var campaignKolId = $('#campaignKolId').val();
         var text = $.trim($('#chatInputMessage').val());

         if (!kolId) return;
         if (!text && !attachedFile) return;

         var now = new Date();
         var hours = String(now.getHours()).padStart(2, '0');
         var minutes = String(now.getMinutes()).padStart(2, '0');
         var timeStr = hours + ':' + minutes;
         var tempId = 'temp_' + Date.now();

         // Case A: Send Media File
         if (attachedFile) {
            var currentFile = attachedFile;
            var isImg = (currentFile.type.indexOf('image/') === 0);
            var mediaPreviewHtml = '';

            if (isImg) {
               mediaPreviewHtml = `
               <div style="margin-bottom: 8px; border-radius: 6px; overflow: hidden; max-width: 320px; border: 1px solid rgba(0,0,0,0.08); background: #f1f5f9;">
                  <img id="${tempId}_preview" src="" style="width: 100%; max-height: 260px; object-fit: cover; display: block; border-radius: 4px;">
               </div>
            `;
            } else {
               mediaPreviewHtml = `
               <div style="margin-bottom: 8px; padding: 8px 12px; background: rgba(0,0,0,0.04); border-radius: 6px; border: 1px solid rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: space-between; gap: 10px; max-width: 320px;">
                  <div style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                     <i class="fa fa-file-text-o text-primary" style="font-size: 20px; flex-shrink: 0;"></i>
                     <div style="font-size: 12px; font-weight: 600; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        ${escapeHtml(currentFile.name)}
                     </div>
                  </div>
               </div>
            `;
            }

            var bubbleHtml = `
            <div class="chat-message-item chat-item-outbound" id="${tempId}">
               <div class="chat-bubble chat-bubble-out">
                  <div class="chat-bubble-sender sender-out">
                     ${escapeHtml(currentUserName)}
                  </div>
                  ${mediaPreviewHtml}
                  ${text ? `<div class="chat-bubble-text">${escapeHtml(text.trim())}</div>` : ''}
                  <div class="chat-bubble-meta">
                     <span>${timeStr}</span>
                     <span class="status-indicator" title="Mengirim media..."><i class="fa fa-clock-o text-muted"></i></span>
                  </div>
               </div>
            </div>
         `;

            $('#emptyChatMessage').hide();
            $('#messageList').append(bubbleHtml);

            if (isImg) {
               var reader = new FileReader();
               reader.onload = function (e) {
                  $('#' + tempId + '_preview').attr('src', e.target.result);
               };
               reader.readAsDataURL(currentFile);
            }

            $('#chatInputMessage').val('');
            $('#charCount').text('0 karakter');
            autoResizeTextarea();
            resetAttachment();
            scrollToBottom();

            var formData = new FormData();
            formData.append('kol_id', kolId);
            formData.append('campaign_kol_id', campaignKolId);
            formData.append('caption', text);
            formData.append('media_file', currentFile);
            formData.append(csrf, token);

            $.ajax({
               url: BASE_URL + 'administrator/whatsapp/ajax_send_media',
               type: 'POST',
               data: formData,
               processData: false,
               contentType: false,
               dataType: 'json',
               success: function (res) {
                  if (res.csrf_hash) token = res.csrf_hash;
                  if (res.success) {
                     var $el = $('#' + tempId);
                     if (res.message_data && res.message_data.id) {
                        $el.attr('data-id', res.message_data.id);
                        $('#lastRenderedMsgId').val(res.message_data.id);
                     }
                     $el.find('.status-indicator').html('<i class="fa fa-check text-muted" title="Terkirim ke WhatsApp"></i>');
                     if (typeof toastr !== 'undefined') toastr.success('File media berhasil terkirim ke WhatsApp');
                  } else {
                     $('#' + tempId).remove();
                     alert(res.message || 'Gagal mengirim file media.');
                  }
               },
               error: function () {
                  $('#' + tempId).remove();
                  alert('Gagal mengirim file ke server atau WhatsApp.');
               }
            });
            return;
         }

         // Case B: Send Regular Text
         var bubbleHtml = `
         <div class="chat-message-item chat-item-outbound" id="${tempId}">
            <div class="chat-bubble chat-bubble-out">
               <div class="chat-bubble-sender sender-out">
                  ${escapeHtml(currentUserName)}
               </div>
               <div class="chat-bubble-text">${escapeHtml(text.trim())}</div>
               <div class="chat-bubble-meta">
                  <span>${timeStr}</span>
                  <span class="status-indicator" title="Mengirim..."><i class="fa fa-clock-o text-muted"></i></span>
               </div>
            </div>
         </div>
      `;

         $('#emptyChatMessage').hide();
         $('#messageList').append(bubbleHtml);
         $('#chatInputMessage').val('');
         $('#charCount').text('0 karakter');
         autoResizeTextarea();
         scrollToBottom();

         // AJAX post to backend
         $.ajax({
            url: BASE_URL + 'administrator/whatsapp/ajax_send_message',
            type: 'POST',
            dataType: 'json',
            data: {
               kol_id: kolId,
               campaign_kol_id: campaignKolId,
               message: text,
               [csrf]: token
            },
            success: function (res) {
               if (res.csrf_hash) {
                  token = res.csrf_hash;
               }
               if (res.success) {
                  var $el = $('#' + tempId);
                  if (res.message_data && res.message_data.id) {
                     $el.attr('data-id', res.message_data.id);
                     $('#lastRenderedMsgId').val(res.message_data.id);
                  }
                  $el.find('.status-indicator').html('<i class="fa fa-check text-muted" title="Terkirim ke WhatsApp"></i>');
                  if (typeof toastr !== 'undefined') {
                     toastr.success('Pesan terkirim ke WhatsApp');
                  }
               } else {
                  $('#' + tempId).remove();
                  if ($('#messageList .chat-message-item').length === 0) {
                     $('#emptyChatMessage').show();
                  }
                  alert(res.message || 'Gagal mengirim pesan via WAHA.');
               }
            },
            error: function (xhr) {
               $('#' + tempId).remove();
               if ($('#messageList .chat-message-item').length === 0) {
                  $('#emptyChatMessage').show();
               }
               alert('Gagal menghubungi server WAHA atau backend. Pastikan microservice WAHA aktif.');
            }
         });
      }

      $('#btnSubmitMessage').on('click', function () {
         triggerSendMessage();
      });

      // 7. Enter key listener
      $('#chatInputMessage').on('keydown', function (e) {
         if (e.keyCode === 13 && !e.shiftKey) {
            e.preventDefault();
            triggerSendMessage();
         }
      });

      // 8. Background Polling for New Inbound Messages (Every 7 seconds)
      var pollingInterval = setInterval(function () {
         var kolId = $('#selectedKolId').val();
         var lastId = $('#lastRenderedMsgId').val() || 0;

         if (!kolId) return;

         $.ajax({
            url: BASE_URL + 'administrator/whatsapp/ajax_poll_messages',
            type: 'GET',
            dataType: 'json',
            data: {
               kol_id: kolId,
               last_id: lastId
            },
            success: function (res) {
               if (res.success && res.messages && res.messages.length > 0) {
                  res.messages.forEach(function (msg) {
                     if ($('.chat-message-item[data-id="' + msg.id + '"]').length === 0) {
                        var isOutbound = (msg.direction === 'OUTBOUND');
                        var checkHtml = '';
                        if (isOutbound) {
                           if (msg.status === 'READ') {
                              checkHtml = '<span title="Dibaca"><i class="fa fa-check text-primary" style="margin-right: -4px;"></i><i class="fa fa-check text-primary"></i></span>';
                           } else if (msg.status === 'DELIVERED') {
                              checkHtml = '<span title="Tersampaikan"><i class="fa fa-check text-muted" style="margin-right: -4px;"></i><i class="fa fa-check text-muted"></i></span>';
                           } else {
                              checkHtml = '<span title="Terkirim"><i class="fa fa-check text-muted"></i></span>';
                           }
                        }

                        var mediaHtml = buildMediaHtml(msg);
                        var textHtml = msg.text ? `<div class="chat-bubble-text">${escapeHtml(msg.text.trim())}</div>` : '';

                        var bubble = `
                        <div class="chat-message-item ${isOutbound ? 'chat-item-outbound' : 'chat-item-inbound'}" data-id="${msg.id}">
                           <div class="chat-bubble ${isOutbound ? 'chat-bubble-out' : 'chat-bubble-in'}">
                              <div class="chat-bubble-sender ${isOutbound ? 'sender-out' : 'sender-in'}">
                                 ${escapeHtml(msg.sender_name || (isOutbound ? currentUserName : 'KOL'))}
                              </div>
                              ${mediaHtml}
                              ${textHtml}
                              <div class="chat-bubble-meta">
                                 <span>${msg.time}</span>
                                 ${checkHtml}
                              </div>
                           </div>
                        </div>
                     `;
                        $('#messageList').append(bubble);
                        $('#lastRenderedMsgId').val(msg.id);
                     }
                  });
                  scrollToBottom();
                  if (typeof window.checkWaUnreadBadge === 'function') {
                     window.checkWaUnreadBadge();
                  }
               }
            }
         });
      }, 7000);

      // Dynamic reactive updates for KOL sidebar unread badges
      $(document).on('wa:unread_counts_updated', function (e, res) {
         var activeKolId = $('#selectedKolId').val();

         $('.kol-unread-badge').each(function () {
            var idStr = $(this).attr('id') || '';
            var kolId = idStr.replace('kol-unread-badge-', '');
            if (kolId == activeKolId) {
               $(this).text('').hide().attr('style', 'display: none !important;');
            } else {
               var count = 0;
               if (res && res.by_kol && typeof res.by_kol === 'object' && res.by_kol[kolId]) {
                  count = parseInt(res.by_kol[kolId], 10) || 0;
               }
               if (count > 0) {
                  $(this).text(count > 99 ? '99+' : count).show().attr('style', 'font-size: 10px; padding: 2px 6px; border-radius: 10px; display: inline-block;');
               } else {
                  $(this).text('').hide().attr('style', 'display: none !important;');
               }
            }
         });
      });

      // 8. Session QR Code & Status Handling
      $('#btnShowQrModal, #btnOpenQrModal').on('click', function () {
         $('#modalWahaQr').modal('show');
         fetchSessionQr();
      });

      $('#btnRefreshQr, #btnRefreshSessionStatus').on('click', function () {
         fetchSessionQr();
      });

      var qrRetryTimer = null;

      function fetchSessionQr() {
         clearTimeout(qrRetryTimer);
         $('#qrLoading').show();
         $('#qrCodeImage').hide();

         $.ajax({
            url: BASE_URL + 'administrator/whatsapp/ajax_session_status',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
               $('#qrLoading').hide();
               if (res.status === 'SCAN_QR_CODE') {
                  var qrSrc = res.qr_url || (res.qr && res.qr.url) || (res.qr && res.qr.data && res.qr.data.url) || '';
                  if (qrSrc) {
                     $('#qrCodeContainer').html('<img id="qrCodeImage" src="' + qrSrc + '" alt="WhatsApp QR Code" style="max-width: 100%; border-radius: 4px;">');
                  } else {
                     $('#qrCodeContainer').html('<div class="text-warning"><i class="fa fa-spinner fa-spin fa-2x"></i><div style="margin-top: 8px; font-size: 12px;">Menyiapkan QR Code...</div></div>');
                     qrRetryTimer = setTimeout(fetchSessionQr, 2000);
                  }
               } else if (res.status === 'STARTING') {
                  $('#qrCodeContainer').html('<div class="text-warning"><i class="fa fa-spinner fa-spin fa-2x"></i><div style="margin-top: 8px; font-size: 12px;">Menghubungkan ke WhatsApp... Memuat QR code...</div></div>');
                  qrRetryTimer = setTimeout(fetchSessionQr, 2000);
               } else if (res.status === 'WORKING') {
                  $('#qrCodeContainer').html('<div class="text-success"><i class="fa fa-check-circle fa-2x"></i><div style="margin-top: 8px; font-weight: bold;">WhatsApp Terhubung!</div></div>');
                  $('#wahaStatusBadgeContainer').html('<button type="button" class="btn btn-success btn-xs btn-flat btn-logout-waha" style="font-size: 11px; padding: 2px 8px; border-radius: 12px; font-weight: 600; border: none; outline: none;" title="Klik untuk ganti nomor (Logout)"><i class="fa fa-check-circle" style="margin-right: 3px;"></i> WA Connected</button>');
               } else {
                  $('#qrCodeContainer').html('<div class="text-danger"><i class="fa fa-exclamation-circle fa-2x"></i><div style="margin-top: 8px; font-size: 12px;">Status WA: <b>' + res.status + '</b></div></div>');
               }
            },
            error: function () {
               $('#qrLoading').hide();
               $('#qrCodeContainer').html('<div class="text-danger"><i class="fa fa-exclamation-circle fa-2x"></i><div style="margin-top: 8px; font-size: 12px;">Gagal memuat status WhatsApp microservice.</div></div>');
            }
         });
      }

      $('#modalWahaQr').on('hidden.bs.modal', function () {
         clearTimeout(qrRetryTimer);
      });

      // Handle Media Preview Click
      $(document).on('click', '.preview-media', function (e) {
         e.preventDefault();
         var url = $(this).attr('data-url');
         var type = $(this).attr('data-type');
         if (url) {
            $('#previewModalDownloadBtn').attr('href', url);
            if (type === 'image') {
               $('#previewModalIframe').hide().attr('src', '');
               $('#previewModalImage').attr('src', url).show();
               $('#previewModalDownloadText').text('Unduh Gambar');
            } else {
               $('#previewModalImage').hide().attr('src', '');
               $('#previewModalIframe').attr('src', url).show();
               $('#previewModalDownloadText').text('Unduh Dokumen');
            }
            $('#modalImagePreview').modal('show');
         }
      });

      // Stop iframe loading when modal closes to save resources
      $('#modalImagePreview').on('hidden.bs.modal', function () {
         $('#previewModalIframe').attr('src', '');
      });


      // Handle WA Logout
      $(document).on('click', '.btn-logout-waha', function () {
         if (confirm('Apakah Anda yakin ingin logout dari perangkat WhatsApp saat ini?\nAnda harus melakukan Scan QR ulang untuk menghubungkan nomor kembali.')) {
            var $btn = $(this);
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Loading...').prop('disabled', true);

            $.ajax({
               url: BASE_URL + 'administrator/whatsapp/ajax_logout_waha',
               type: 'POST',
               dataType: 'json',
               data: {
                  [csrf]: csrf_hash
               },
               success: function (res) {
                  if (res.csrf_hash) csrf_hash = res.csrf_hash;
                  if (res.success) {
                     // Re-fetch status so it updates to SCAN_QR_CODE
                     setTimeout(fetchSessionStatus, 1500);
                  } else {
                     alert('Gagal logout: ' + (res.message || 'Error'));
                     $btn.html('<i class="fa fa-check-circle"></i> WA Connected').prop('disabled', false);
                  }
               },
               error: function () {
                  alert('Terjadi kesalahan jaringan.');
                  $btn.html('<i class="fa fa-check-circle"></i> WA Connected').prop('disabled', false);
               }
            });
         }
      });

      function escapeHtml(text) {
         var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
         };
         return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
      }

      // =======================================================
      // WHATSAPP BULK BROADCASTING CONTROLLER LOGIC
      // =======================================================
      var bcKolsData = [];
      var bcSelectedIds = new Set();
      var bcIsBroadcasting = false;
      var bcStopRequested = false;

      var BC_TEMPLATES = {
         'TPL_DEAL': 'Halo Kak [Nama KOL], salam hangat dari tim Brand! Kami ingin mengajak kolaborasi untuk campaign "[Nama Campaign]". Apakah slot kolaborasi Kakak tersedia di bulan ini? Kami siap diskusikan brief dan rate card Kakak.',
         'TPL_BRIEF': 'Halo Kak [Nama KOL], berikut kami lampirkan brief panduan konten untuk "[Judul VT]" (Varian: Varian Utama). Mohon dipelajari panduan angle, key message, dan hashtag wajibnya ya Kak. Jika ada hal yang ingin ditanyakan, jangan ragu untuk kontak kami.',
         'TPL_REMINDER_DRAFT': 'Halo Kak [Nama KOL], mengingatkan kembali bahwa jadwal pengumpulan draft video "[Judul VT]" adalah besok. Apakah proses shooting sudah selesai atau ada kendala Kak? Ditunggu link draft-nya ya.',
         'TPL_REMINDER_POST': 'Halo Kak [Nama KOL], hari ini adalah jadwal live posting untuk video "[Judul VT]" ([Tgl Posting]). Mohon kabari jika videonya sudah tayang beserta link postingannya ya Kak. Terima kasih banyak!',
         'TPL_SHIPMENT': 'Halo Kak [Nama KOL], paket sampel produk untuk campaign "[Nama Campaign]" sudah kami kirimkan via JNE/J&T dengan nomor resi terlampir. Mohon konfirmasi jika paketnya sudah sampai ya Kak.',
         'CUSTOM': 'Halo Kak [Nama KOL],\n\n'
      };

      // Open Broadcast Modal
      $('#btnOpenBroadcastModal, #btnQuickBroadcast').on('click', function (e) {
         e.preventDefault();
         openBroadcastModal();
      });

      function openBroadcastModal(preselectedIds) {
         $('#modalBroadcastWhatsApp').modal('show');
         if (bcKolsData.length === 0) {
            fetchBroadcastKols(preselectedIds);
         } else {
            if (preselectedIds && preselectedIds.length > 0) {
               bcSelectedIds.clear();
               preselectedIds.forEach(function (id) { bcSelectedIds.add(parseInt(id, 10)); });
            }
            renderBcKols();
            updateBcSelectionCounters();
            updateBcPreview();
         }
      }

      function fetchBroadcastKols(preselectedIds) {
         $('#bcLoadingKols').show();
         $('#bcKolListContainer').html('<div style="padding:30px; text-align:center; color:#94a3b8;"><i class="fa fa-spinner fa-spin fa-2x"></i><div style="margin-top:8px;">Memuat data KOL...</div></div>');

         $.ajax({
            url: BASE_URL + 'administrator/whatsapp/ajax_get_broadcast_kols',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
               if (res.success && res.kols) {
                  bcKolsData = res.kols;
                  if (preselectedIds && preselectedIds.length > 0) {
                     bcSelectedIds.clear();
                     preselectedIds.forEach(function (id) { bcSelectedIds.add(parseInt(id, 10)); });
                  } else if (bcSelectedIds.size === 0) {
                     // By default, do not check all so user selects intentionally
                  }
                  renderBcKols();
                  updateBcSelectionCounters();
                  updateBcPreview();
               } else {
                  $('#bcKolListContainer').html('<div class="alert alert-danger" style="margin:10px;">Gagal memuat data KOL.</div>');
               }
            },
            error: function () {
               $('#bcKolListContainer').html('<div class="alert alert-danger" style="margin:10px;">Kesalahan jaringan saat memuat data KOL.</div>');
            }
         });
      }

      function renderBcKols() {
         var q = ($('#bcSearchInput').val() || '').toLowerCase().trim();
         var tierFilter = $('#bcFilterTier').val();
         var catFilter = $('#bcFilterCategory').val();

         var filtered = bcKolsData.filter(function (k) {
            if (q) {
               var matchName = k.name.toLowerCase().indexOf(q) !== -1;
               var matchPhone = k.phone.toLowerCase().indexOf(q) !== -1;
               var matchCity = (k.city || '').toLowerCase().indexOf(q) !== -1;
               if (!matchName && !matchPhone && !matchCity) return false;
            }
            if (tierFilter && String(k.tier_id) !== String(tierFilter)) {
               return false;
            }
            if (catFilter) {
               var hasCat = k.category_ids && k.category_ids.indexOf(parseInt(catFilter, 10)) !== -1;
               if (!hasCat) return false;
            }
            return true;
         });

         $('#bcTotalCounter').text(filtered.length + ' dari ' + bcKolsData.length + ' KOL');

         if (filtered.length === 0) {
            $('#bcKolListContainer').html('<div style="padding: 24px; text-align: center; color: #94a3b8;"><i class="fa fa-info-circle"></i> Tidak ada KOL yang cocok dengan filter.</div>');
            return;
         }

         var html = '<div style="display: flex; flex-direction: column; gap: 4px;">';
         filtered.forEach(function (k) {
            var isChecked = bcSelectedIds.has(k.id);
            var hasPhone = k.has_valid_phone;
            var initial = (k.name || 'K').charAt(0).toUpperCase();

            html += `
            <div class="bc-kol-item" data-id="${k.id}" style="display: flex; align-items: center; gap: 8px; padding: 6px 8px; border-radius: 4px; background: ${isChecked ? '#f0fdf4' : '#ffffff'}; border: 1px solid ${isChecked ? '#86efac' : '#e2e8f0'}; cursor: pointer; transition: background 0.15s;">
               <div style="flex-shrink: 0;">
                  <input type="checkbox" class="bc-kol-chk" value="${k.id}" ${isChecked ? 'checked' : ''} ${!hasPhone ? 'disabled' : ''} style="cursor: pointer;">
               </div>
               <div style="width: 28px; height: 28px; border-radius: 50%; background: #e2e8f0; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 11px; font-weight: 700; color: #475569;">
                  ${k.avatar_url ? `<img src="${k.avatar_url}" style="width:100%; height:100%; object-fit:cover;" onerror="this.style.display='none';">` : initial}
               </div>
               <div style="flex: 1; min-width: 0;">
                  <div style="display: flex; justify-content: space-between; align-items: center;">
                     <div style="font-size: 12px; font-weight: 700; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        ${escapeHtml(k.name)}
                     </div>
                     <span class="label label-info" style="font-size: 9px; padding: 1px 5px; border-radius: 3px;">${escapeHtml(k.tier_name)}</span>
                  </div>
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1px;">
                     <span style="font-size: 10.5px; color: ${hasPhone ? '#15803d' : '#ef4444'};">
                        <i class="fa fa-phone"></i> ${hasPhone ? escapeHtml(k.clean_phone) : 'Tanpa No. HP'}
                     </span>
                     ${k.category_names ? `<span style="font-size: 9.5px; color: #64748b; max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(k.category_names)}</span>` : ''}
                  </div>
               </div>
            </div>
         `;
         });
         html += '</div>';

         $('#bcKolListContainer').html(html);
      }

      // Search & Filter event handlers
      $('#bcSearchInput, #bcFilterTier, #bcFilterCategory').on('input change', function () {
         renderBcKols();
      });

      // Select All / Deselect All
      $('#bcBtnSelectAll').on('click', function (e) {
         e.preventDefault();
         bcKolsData.forEach(function (k) {
            if (k.has_valid_phone) {
               bcSelectedIds.add(k.id);
            }
         });
         renderBcKols();
         updateBcSelectionCounters();
         updateBcPreview();
      });

      $('#bcBtnDeselectAll').on('click', function (e) {
         e.preventDefault();
         bcSelectedIds.clear();
         renderBcKols();
         updateBcSelectionCounters();
         updateBcPreview();
      });

      // Individual checkbox toggle
      $(document).on('change', '.bc-kol-chk', function (e) {
         var id = parseInt($(this).val(), 10);
         if ($(this).is(':checked')) {
            bcSelectedIds.add(id);
         } else {
            bcSelectedIds.delete(id);
         }
         var parentItem = $(this).closest('.bc-kol-item');
         if ($(this).is(':checked')) {
            parentItem.css({ 'background': '#f0fdf4', 'border-color': '#86efac' });
         } else {
            parentItem.css({ 'background': '#ffffff', 'border-color': '#e2e8f0' });
         }
         updateBcSelectionCounters();
         updateBcPreview();
      });

      // Clicking item card toggles checkbox
      $(document).on('click', '.bc-kol-item', function (e) {
         if ($(e.target).is('input[type="checkbox"]')) return;
         var chk = $(this).find('.bc-kol-chk');
         if (chk.prop('disabled')) return;
         chk.prop('checked', !chk.prop('checked')).trigger('change');
      });

      function updateBcSelectionCounters() {
         var count = bcSelectedIds.size;
         $('#bcSelectedBadge').text(count + ' Terpilih');
         $('#bcBtnCount').text(count);
         if (count > 0 && !bcIsBroadcasting) {
            $('#bcBtnStartSend').prop('disabled', false);
         } else {
            $('#bcBtnStartSend').prop('disabled', true);
         }
      }

      // Template Picker Buttons
      $(document).on('click', '.bc-tpl-btn', function () {
         // Reset all buttons to default inactive style
         $('.bc-tpl-btn').removeClass('active').attr('style', 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid #e2e8f0; background-color: #f8fafc; color: #475569; box-shadow: none; outline: none;');

         // Set active style to clicked button
         $(this).addClass('active').attr('style', 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid #bfdbfe; background-color: #eff6ff; color: #1d4ed8; box-shadow: none; outline: none;');

         var code = $(this).data('tpl');
         var tplText = BC_TEMPLATES[code] || '';
         $('#bcMessageText').val(tplText);
         updateBcCharCount();
         updateBcPreview();
      });

      // Initial template load
      $('#bcMessageText').val(BC_TEMPLATES['TPL_DEAL']);
      updateBcCharCount();

      // Insert dynamic tags at cursor
      $(document).on('click', '.btn-insert-bc-tag', function () {
         var tag = $(this).data('tag');
         var textarea = document.getElementById('bcMessageText');
         if (!textarea) return;

         var start = textarea.selectionStart || 0;
         var end = textarea.selectionEnd || 0;
         var text = textarea.value;
         textarea.value = text.substring(0, start) + tag + text.substring(end);
         textarea.focus();
         textarea.selectionStart = textarea.selectionEnd = start + tag.length;

         updateBcCharCount();
         updateBcPreview();
      });

      $('#bcMessageText, #bcCampaignSelect').on('input change', function () {
         updateBcCharCount();
         updateBcPreview();
      });

      function updateBcCharCount() {
         var len = ($('#bcMessageText').val() || '').length;
         $('#bcCharCount').text(len + ' karakter');
      }

      function updateBcPreview() {
         var text = $('#bcMessageText').val() || '';
         var sampleKol = null;

         if (bcSelectedIds.size > 0) {
            var firstId = Array.from(bcSelectedIds)[0];
            sampleKol = bcKolsData.find(function (k) { return k.id === firstId; });
         } else if (bcKolsData.length > 0) {
            sampleKol = bcKolsData[0];
         }

         var sampleName = sampleKol ? sampleKol.name : 'Amanda Putri';
         var sampleCampaign = $('#bcCampaignSelect option:selected').data('name') || (sampleKol && sampleKol.active_campaign_name ? sampleKol.active_campaign_name : 'Glowing Skin Serum Q3');
         var sampleVt = (sampleKol && sampleKol.latest_task_title) ? sampleKol.latest_task_title : 'VT #1 Review Produk Serum';
         var sampleDate = (sampleKol && sampleKol.latest_task_date) ? sampleKol.latest_task_date : '15 Sep 2026';

         $('#bcPreviewKolName').text(sampleName);

         var rendered = text;
         rendered = rendered.replace(/\[Nama KOL\]/g, sampleName);
         rendered = rendered.replace(/\[Nama Campaign\]/g, sampleCampaign);
         rendered = rendered.replace(/\[Judul VT\]/g, sampleVt);
         rendered = rendered.replace(/\[Tgl Posting\]/g, sampleDate);

         $('#bcPreviewBody').text(rendered || '(Teks pesan kosong)');
      }

      // Broadcast Execution Engine
      $('#bcBtnStartSend').on('click', function () {
         if (bcSelectedIds.size === 0) {
            alert('Pilih minimal satu KOL penerima.');
            return;
         }
         var msgText = ($('#bcMessageText').val() || '').trim();
         if (!msgText) {
            alert('Teks pesan broadcast tidak boleh kosong.');
            $('#bcMessageText').focus();
            return;
         }

         var confirmMsg = 'Mulai mengirim broadcast WhatsApp ke ' + bcSelectedIds.size + ' KOL terpilih?\n\nPesan akan dikirim berurutan secara otomatis.';
         if (!confirm(confirmMsg)) return;

         runBroadcastBatch(msgText);
      });

      $('#bcBtnStop').on('click', function () {
         if (confirm('Apakah Anda yakin ingin membatalkan sisa antrean broadcast?')) {
            bcStopRequested = true;
            $('#bcBtnStop').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menghentikan...');
         }
      });

      $('#bcBtnBackToEdit').on('click', function () {
         $('#bcProgressOverlay').hide();
         $('#bcBtnStartSend').show().prop('disabled', false);
         $('#bcBtnClose').show().prop('disabled', false);
         $('#bcBtnStop').hide().html('<i class="fa fa-stop-circle"></i> Hentikan Sisa Antrean').prop('disabled', false);
         $('#bcSummaryText').hide();
         $(this).hide();
         bcIsBroadcasting = false;
      });

      async function runBroadcastBatch(messageText) {
         bcIsBroadcasting = true;
         bcStopRequested = false;

         var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
         var currentCsrfHash = '<?= $this->security->get_csrf_hash(); ?>';

         var recipientList = [];
         bcSelectedIds.forEach(function (id) {
            var k = bcKolsData.find(function (item) { return item.id === id; });
            if (k && k.has_valid_phone) {
               recipientList.push(k);
            }
         });

         if (recipientList.length === 0) {
            alert('Tidak ada nomor valid yang siap dikirim.');
            bcIsBroadcasting = false;
            return;
         }

         // UI state transition
         $('#bcBtnStartSend').prop('disabled', true).hide();
         $('#bcBtnClose').prop('disabled', true).hide();
         $('#bcBtnStop').prop('disabled', false).show();
         $('#bcProgressOverlay').css('display', 'flex');
         $('#bcLogContainer').empty();
         $('#bcSummaryText').hide();

         var total = recipientList.length;
         var successCount = 0;
         var failedCount = 0;
         var selectedCampaignId = $('#bcCampaignSelect').val() || '';
         var selectedCampaignName = $('#bcCampaignSelect option:selected').data('name') || '';

         for (var i = 0; i < total; i++) {
            if (bcStopRequested) {
               $('#bcLogContainer').prepend('<div class="text-warning" style="padding: 2px 0;"><b>[Dihentikan Pengguna]</b> Sisa antrean (' + (total - i) + ' pesan) dibatalkan.</div>');
               break;
            }

            var currentKol = recipientList[i];
            var percent = Math.round(((i + 1) / total) * 100);

            $('#bcProgressStatus').text('Mengirim (' + (i + 1) + '/' + total + '): ' + currentKol.name + '...');
            $('#bcProgressPercent').text(percent + '%');
            $('#bcProgressBar').css('width', percent + '%').text(percent + '%');

            try {
               var postData = {
                  kol_id: currentKol.id,
                  message_text: messageText,
                  campaign_id: selectedCampaignId,
                  campaign_name: selectedCampaignName
               };
               postData[csrfTokenName] = currentCsrfHash;

               var res = await $.ajax({
                  url: BASE_URL + 'administrator/whatsapp/ajax_send_broadcast_item',
                  type: 'POST',
                  dataType: 'json',
                  data: postData
               });

               if (res && res.csrf_hash) {
                  currentCsrfHash = res.csrf_hash;
               }

               if (res && res.success) {
                  successCount++;
                  $('#bcLogContainer').prepend(`
                  <div class="text-success" style="padding: 2px 0; border-bottom: 1px solid #f1f5f9;">
                     <i class="fa fa-check-circle"></i> [${res.sent_at || 'OK'}] <b>${escapeHtml(currentKol.name)}</b> (${currentKol.clean_phone}): Berhasil terkirim.
                  </div>
               `);
               } else {
                  failedCount++;
                  var failMsg = (res && res.message) ? res.message : 'Error pengiriman';
                  $('#bcLogContainer').prepend(`
                  <div class="text-danger" style="padding: 2px 0; border-bottom: 1px solid #f1f5f9;">
                     <i class="fa fa-times-circle"></i> <b>${escapeHtml(currentKol.name)}</b> (${currentKol.clean_phone}): Gagal - ${escapeHtml(failMsg)}
                  </div>
               `);
               }
            } catch (err) {
               failedCount++;
               var errorMsg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : (err.statusText || 'Kesalahan jaringan atau server timeout.');
               $('#bcLogContainer').prepend(`
               <div class="text-danger" style="padding: 2px 0; border-bottom: 1px solid #f1f5f9;">
                  <i class="fa fa-times-circle"></i> <b>${escapeHtml(currentKol.name)}</b>: Gagal - ${escapeHtml(errorMsg)}
               </div>
            `);
            }

            // Anti-spam safe delay: wait 800ms between messages
            if (i < total - 1 && !bcStopRequested) {
               await new Promise(function (resolve) { setTimeout(resolve, 800); });
            }
         }

         // Finish state
         bcIsBroadcasting = false;
         $('#bcBtnStop').hide();
         $('#bcBtnBackToEdit').show();

         $('#bcProgressStatus').text('Broadcast selesai.');
         $('#bcLogContainer').prepend('<div class="text-info" style="padding: 6px 0; border-bottom: 1px solid #f1f5f9; text-align: center; font-weight: bold; margin-top: 10px;"><i class="fa fa-flag-checkered text-success"></i> Selesai: <span class="text-success">' + successCount + ' Berhasil</span>, <span class="text-danger">' + failedCount + ' Gagal</span>.</div>');
      }

      // Auto-detect broadcast query parameter from KOLs list
      var urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('broadcast') === '1') {
         var preselected = [];
         var kolIdsParam = urlParams.get('kol_ids');
         if (kolIdsParam) {
            preselected = kolIdsParam.split(',').map(function (id) { return parseInt(id.trim(), 10); }).filter(Boolean);
         }
         setTimeout(function () {
            openBroadcastModal(preselected);
         }, 500);
      }

   });
</script>
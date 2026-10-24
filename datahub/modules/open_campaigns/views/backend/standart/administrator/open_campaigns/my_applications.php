<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      My Applications <small>Riwayat Pendaftaran Campaign Anda</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/open_campaigns'); ?>">Open Campaigns</a></li>
      <li class="active">My Applications</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <!-- Flash Message -->
         <?php if ($this->session->flashdata('f_message')): ?>
            <div class="alert alert-<?= $this->session->flashdata('f_type') ?: 'info'; ?> alert-dismissible">
               <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
               <?= $this->session->flashdata('f_message'); ?>
            </div>
         <?php endif; ?>

         <div class="box box-solid" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 20px;">
            <div class="box-body" style="padding: 16px 20px; background: #fff; border-radius: 8px;">
               <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                  <div>
                     <h4 style="margin: 0; font-weight: 700; color: #1e293b;">
                        <i class="fa fa-file-text-o text-primary"></i> Status Pendaftaran Campaign
                     </h4>
                     <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">
                        Pantau status peninjauan pengajuan kerjasama Anda dengan berbagai brand secara real-time.
                     </p>
                  </div>
                  <div>
                     <a href="<?= site_url('administrator/open_campaigns'); ?>" class="btn btn-success btn-flat" style="font-weight: 600;">
                        <i class="fa fa-search"></i> Jelajahi Campaign Terbuka
                     </a>
                  </div>
               </div>
            </div>
         </div>

         <div class="box box-solid" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
            <div class="box-body" style="padding: 15px;">
               <?php if (empty($applications)): ?>
                  <div style="text-align: center; padding: 40px 20px;">
                     <div style="font-size: 48px; color: #cbd5e1; margin-bottom: 12px;"><i class="fa fa-inbox"></i></div>
                     <h4 style="font-weight: 700; color: #475569; margin-bottom: 6px;">Belum Ada Pendaftaran</h4>
                     <p style="color: #94a3b8; font-size: 13px; margin-bottom: 16px;">Anda belum mengajukan pendaftaran ke campaign terbuka manapun.</p>
                     <a href="<?= site_url('administrator/open_campaigns'); ?>" class="btn btn-primary btn-flat" style="font-weight: 600;">
                        <i class="fa fa-bullhorn"></i> Jelajahi Campaign Sekarang
                     </a>
                  </div>
               <?php else: ?>
                  <div class="table-responsive">
                     <table class="table table-bordered table-hover" style="margin-bottom: 0;">
                        <thead>
                           <tr style="background: #f8fafc; color: #475569;">
                              <th style="width: 50px; text-align: center;">#</th>
                              <th>Campaign & Brand</th>
                              <th style="width: 140px;">Tanggal Daftar</th>
                              <th style="width: 140px;">Tarif Diajukan</th>
                              <th>Pitching Anda</th>
                              <th style="width: 130px; text-align: center;">Kualifikasi</th>
                              <th style="width: 120px; text-align: center;">Status Review</th>
                              <th style="width: 100px; text-align: center;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php foreach ($applications as $idx => $app): ?>
                              <tr>
                                 <td style="vertical-align: middle; text-align: center;"><?= $idx + 1; ?></td>
                                 <td style="vertical-align: middle;">
                                    <div style="font-weight: 700; color: #1e293b;">
                                       <a href="<?= site_url('administrator/open_campaigns/view/' . $app->campaign_id); ?>">
                                          <?= _ent($app->campaign_name); ?>
                                       </a>
                                    </div>
                                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                       <span class="label label-primary"><?= _ent($app->campaign_type); ?></span>
                                       <?= !empty($app->brand_name) ? '• ' . _ent($app->brand_name) : ''; ?>
                                    </div>
                                 </td>
                                 <td style="vertical-align: middle; font-size: 12px; color: #475569;">
                                    <?= date('d M Y H:i', strtotime($app->applied_at)); ?>
                                 </td>
                                 <td style="vertical-align: middle; font-size: 12px;">
                                    <?php if (!empty($app->proposed_rate) && (float)$app->proposed_rate > 0): ?>
                                       <strong style="color: #00a65a;">Rp <?= number_format((float)$app->proposed_rate, 0, ',', '.'); ?></strong>
                                    <?php else: ?>
                                       <span class="text-muted">Standar</span>
                                    <?php endif; ?>
                                 </td>
                                 <td style="vertical-align: middle;">
                                    <?php if (!empty($app->pitch_message)): ?>
                                       <div style="font-size: 12px; color: #475569; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= _ent($app->pitch_message); ?>">
                                          <em>"<?= _ent($app->pitch_message); ?>"</em>
                                       </div>
                                    <?php else: ?>
                                       <span class="text-muted" style="font-size: 11px;">-</span>
                                    <?php endif; ?>
                                 </td>
                                 <td style="vertical-align: middle; text-align: center;">
                                    <?php if ($app->is_qualified): ?>
                                       <span class="label label-success" style="font-size: 11px; padding: 4px 8px; border-radius: 4px;">
                                          <i class="fa fa-check-circle"></i> Memenuhi Syarat
                                       </span>
                                    <?php else: ?>
                                       <span class="label" style="font-size: 11px; padding: 4px 8px; border-radius: 4px; background: #f59e0b; color: #fff;">
                                          <i class="fa fa-info-circle"></i> Sebagian Belum Sesuai
                                       </span>
                                    <?php endif; ?>
                                    <div style="margin-top: 3px;">
                                       <a href="javascript:void(0)" class="btn-detail-qualification text-primary" style="font-size: 11px; text-decoration: underline;" 
                                          data-notes='<?= htmlspecialchars($app->qualification_notes ?: "{}", ENT_QUOTES, "UTF-8"); ?>' 
                                          data-campaign="<?= _ent($app->campaign_name); ?>">
                                          Lihat Detail
                                       </a>
                                    </div>
                                 </td>
                                 <td style="vertical-align: middle; text-align: center;">
                                    <?php if ($app->status === 'PENDING'): ?>
                                       <span class="label label-warning" style="font-size: 11px; padding: 4px 8px;"><i class="fa fa-clock-o"></i> Pending</span>
                                    <?php elseif ($app->status === 'ACCEPTED'): ?>
                                       <span class="label label-success" style="font-size: 11px; padding: 4px 8px;"><i class="fa fa-check"></i> Accepted</span>
                                       <div style="font-size: 10px; color: #15803d; margin-top: 2px; font-weight: 600;">Ditugaskan</div>
                                    <?php elseif ($app->status === 'REJECTED'): ?>
                                       <span class="label label-danger" style="font-size: 11px; padding: 4px 8px;" title="<?= _ent($app->rejection_reason ?: 'Tidak terpilih'); ?>"><i class="fa fa-times"></i> Rejected</span>
                                        <?php if (!empty($app->rejection_reason)): ?>
                                           <div style="font-size: 10px; color: #b91c1c; margin-top: 2px;" title="<?= _ent($app->rejection_reason); ?>">
                                              <?= _ent(function_exists('character_limiter') ? character_limiter($app->rejection_reason, 25) : (strlen($app->rejection_reason) > 25 ? substr($app->rejection_reason, 0, 22) . '...' : $app->rejection_reason)); ?>
                                           </div>
                                        <?php endif; ?>
                                     <?php endif; ?>
                                 </td>
                                 <td style="vertical-align: middle; text-align: center;">
                                    <a href="<?= site_url('administrator/open_campaigns/view/' . $app->campaign_id); ?>" class="btn btn-xs btn-default btn-flat" title="Lihat Detail Campaign">
                                       <i class="fa fa-eye text-primary"></i> Detail
                                    </a>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                        </tbody>
                     </table>
                  </div>
               <?php endif; ?>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Modal Detail Kriteria Kualifikasi -->
<div class="modal fade" id="modalQualificationDetail" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document" style="max-width: 940px; width: 92%; margin: 30px auto;">
      <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25); overflow: hidden;">
         <div class="modal-header" style="background: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 22px 26px; position: relative;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; right: 22px; top: 22px; width: 32px; height: 32px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; opacity: 1; font-size: 16px; color: #64748b; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease; outline: none;">&times;</button>
            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #6366f1; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
               <i class="fa fa-shield"></i> Evaluasi Kualifikasi Campaign
            </div>
            <h4 class="modal-title" style="font-size: 19px; font-weight: 700; color: #0f172a; margin: 0; padding-right: 45px; line-height: 1.3;" id="modal_camp_name"></h4>
         </div>
         <div class="modal-body" style="padding: 24px 26px; background: #ffffff;">
            <div id="modal_criteria_summary"></div>
            <div id="modal_criteria_content"></div>
         </div>
         <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #f1f5f9; padding: 14px 26px; display: flex; justify-content: flex-end;">
            <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 8px; font-weight: 600; font-size: 13px; padding: 8px 24px; background: #ffffff; border: 1px solid #cbd5e1; color: #334155; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.15s ease;">Tutup</button>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
   function getCriteriaIcon(key) {
      switch(key) {
         case 'account_status': return '<i class="fa fa-user" style="color: #6366f1;"></i>';
         case 'min_followers':
         case 'followers': return '<i class="fa fa-users" style="color: #0ea5e9;"></i>';
         case 'min_er':
         case 'engagement_rate': return '<i class="fa fa-line-chart" style="color: #ec4899;"></i>';
         case 'tier': return '<i class="fa fa-trophy" style="color: #f59e0b;"></i>';
         case 'category': return '<i class="fa fa-tags" style="color: #8b5cf6;"></i>';
         case 'platform': return '<i class="fa fa-share-alt" style="color: #10b981;"></i>';
         default: return '<i class="fa fa-check-square-o" style="color: #64748b;"></i>';
      }
   }

   $('.btn-detail-qualification').on('click', function() {
      var campName = $(this).data('campaign');
      var notesRaw = $(this).attr('data-notes');
      var criteria = [];

      try {
         var parsed = JSON.parse(notesRaw);
         if (Array.isArray(parsed)) criteria = parsed;
      } catch(e) {}

      $('#modal_camp_name').text(campName || 'Campaign');

      var totalCount = criteria.length;
      var passedCount = criteria.filter(function(c) { return !!c.passed; }).length;

      // Dynamic Summary Banner
      var summaryHtml = '';
      if (totalCount > 0) {
         if (passedCount === totalCount) {
            summaryHtml = '<div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 14px;">' +
               '<div style="width: 42px; height: 42px; border-radius: 10px; background: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;"><i class="fa fa-check"></i></div>' +
               '<div style="flex: 1;">' +
                  '<div style="font-weight: 700; color: #065f46; font-size: 14px;">Semua Kriteria Terpenuhi</div>' +
                  '<div style="font-size: 12px; color: #047857; margin-top: 2px;">Profil Anda memenuhi seluruh ' + totalCount + ' syarat kualifikasi campaign ini.</div>' +
               '</div>' +
               '<div style="font-size: 12px; font-weight: 700; color: #065f46; background: #d1fae5; padding: 5px 12px; border-radius: 20px; white-space: nowrap;">' + passedCount + '/' + totalCount + ' Lolos</div>' +
            '</div>';
         } else if (passedCount > 0) {
            summaryHtml = '<div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 14px;">' +
               '<div style="width: 42px; height: 42px; border-radius: 10px; background: #f59e0b; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;"><i class="fa fa-exclamation-triangle"></i></div>' +
               '<div style="flex: 1;">' +
                  '<div style="font-weight: 700; color: #92400e; font-size: 14px;">Sebagian Syarat Belum Sesuai</div>' +
                  '<div style="font-size: 12px; color: #b45309; margin-top: 2px;">' + passedCount + ' dari ' + totalCount + ' kriteria terpenuhi. Brand tetap dapat mempertimbangkan dan menerima pengajuan Anda.</div>' +
               '</div>' +
               '<div style="font-size: 12px; font-weight: 700; color: #92400e; background: #fef3c7; padding: 5px 12px; border-radius: 20px; white-space: nowrap;">' + passedCount + '/' + totalCount + ' Sesuai</div>' +
            '</div>';
         } else {
            summaryHtml = '<div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 14px;">' +
               '<div style="width: 42px; height: 42px; border-radius: 10px; background: #ef4444; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;"><i class="fa fa-times"></i></div>' +
               '<div style="flex: 1;">' +
                  '<div style="font-weight: 700; color: #991b1b; font-size: 14px;">Kriteria Belum Terpenuhi</div>' +
                  '<div style="font-size: 12px; color: #b91c1c; margin-top: 2px;">Profil saat ini belum memenuhi kriteria kualifikasi yang ditentukan brand.</div>' +
               '</div>' +
               '<div style="font-size: 12px; font-weight: 700; color: #991b1b; background: #fee2e2; padding: 5px 12px; border-radius: 20px; white-space: nowrap;">0/' + totalCount + '</div>' +
            '</div>';
         }
      }
      $('#modal_criteria_summary').html(summaryHtml);

      // Criteria Table Breakdown
      var html = '<div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #ffffff;">' +
         '<table class="table" style="margin-bottom: 0; width: 100%; table-layout: fixed; border-collapse: collapse;">' +
            '<thead>' +
               '<tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">' +
                  '<th style="padding: 14px 18px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border: none; width: 34%;">Kriteria Syarat</th>' +
                  '<th style="padding: 14px 18px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border: none; width: 24%;">Ketentuan Brand</th>' +
                  '<th style="padding: 14px 18px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border: none; width: 24%;">Data Profil Anda</th>' +
                  '<th style="padding: 14px 18px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border: none; text-align: center; width: 18%; white-space: nowrap;">Hasil</th>' +
               '</tr>' +
            '</thead>' +
            '<tbody>';

      if (criteria.length > 0) {
         criteria.forEach(function(item) {
            var badge = item.passed 
               ? '<span style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; white-space: nowrap;"><i class="fa fa-check-circle" style="color: #10b981;"></i> Lulus</span>' 
               : '<span style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; white-space: nowrap;"><i class="fa fa-times-circle" style="color: #ef4444;"></i> Tidak Lulus</span>';
            
            var noteHtml = item.note ? '<div style="font-size: 11px; color: #94a3b8; margin-top: 3px; font-weight: normal;">' + item.note + '</div>' : '';
            var iconHtml = getCriteriaIcon(item.key);

            html += '<tr style="border-bottom: 1px solid #f1f5f9;">' +
               '<td style="padding: 14px 18px; vertical-align: middle; border: none; width: 34%;">' +
                  '<div style="display: flex; align-items: center; gap: 10px;">' +
                     '<div style="width: 30px; height: 30px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0;">' + iconHtml + '</div>' +
                     '<div style="min-width: 0;">' +
                        '<div style="font-weight: 600; color: #1e293b; font-size: 13px; word-break: break-word;">' + (item.label || item.key) + '</div>' +
                        noteHtml +
                     '</div>' +
                  '</div>' +
               '</td>' +
               '<td style="padding: 14px 18px; vertical-align: middle; border: none; width: 24%; color: #475569; font-size: 13px; font-weight: 500; word-break: break-word;">' + (item.required || '-') + '</td>' +
               '<td style="padding: 14px 18px; vertical-align: middle; border: none; width: 24%; color: #0f172a; font-size: 13px; font-weight: 600; word-break: break-word;">' + (item.actual || '-') + '</td>' +
               '<td style="padding: 14px 18px; vertical-align: middle; border: none; width: 18%; text-align: center; white-space: nowrap;">' + badge + '</td>' +
            '</tr>';
         });
      } else {
         html += '<tr><td colspan="4" style="padding: 30px; text-align: center; color: #94a3b8; font-size: 13px;">Data kualifikasi belum tercatat.</td></tr>';
      }
      html += '</tbody></table></div>';

      $('#modal_criteria_content').html(html);
      $('#modalQualificationDetail').modal('show');
   });
});
</script>

<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
   function domo() {
      $('*').bind('keydown', 'Ctrl+e', function assets() {
         $('#btn_edit').trigger('click');
         return false;
      });

      $('*').bind('keydown', 'Ctrl+x', function assets() {
         $('#btn_back').trigger('click');
         return false;
      });
   }

   jQuery(document).ready(domo);
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Tasks <small><?= cclang('detail', ['Tasks']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a href="<?= site_url('administrator/tasks'); ?>">Tasks</a></li>
      <li class="active"><?= cclang('detail'); ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12 task-view-container">
 
          <!-- PERFORMANCE / SCRAPING METRICS CARD -->
          <?php
          $live_url = !empty($posting->live_url) ? $posting->live_url : (!empty($tasks->live_url) ? $tasks->live_url : '');
          ?>
          <?php if (!empty($live_url) || !empty($latest_metrics)): ?>
             <div class="box task-card-perf"
                style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10); margin-bottom: 20px; border-top: 4px solid #00a65a;">
                <div class="box-body" style="padding: 20px;">
                   <div class="task-sec-header collapsed" data-toggle="collapse" data-target="#collapsePerf" aria-expanded="false"
                      style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid #edf2f7; padding-bottom: 15px; margin-bottom: 18px;">
                      <div>
                         <h4 style="margin: 0; color: #172b4d; font-weight: 700; font-size: 17px;"><i
                               class="fa fa-line-chart text-success" style="margin-right: 6px;"></i> Live Post Performance &
                            Apify Metrics</h4>
                         <div class="task-perf-url-wrap" style="margin-top: 4px; color: #6b778c; font-size: 13px;">
                            Live URL: <a href="<?= htmlspecialchars($live_url); ?>" target="_blank" class="task-live-url-link"
                               style="font-weight: 600; color: #0073b7;"><i class="fa fa-external-link"></i>
                               <?= htmlspecialchars($live_url); ?></a>
                         </div>
                      </div>
                      <div style="display: flex; align-items: center; gap: 10px;">
                         <button type="button" class="btn btn-sm btn-success btn-flat" id="btn_sync_detail"
                            data-id="<?= $tasks->id; ?>" style="font-weight: 600; border-radius: 4px;">
                            <i class="fa fa-refresh"></i> Sync / Scrape Video Metrics
                         </button>
                         <i class="fa fa-chevron-down task-sec-chevron"></i>
                      </div>
                   </div>

                   <div id="collapsePerf" class="collapse task-sec-body">

                  <?php if (!empty($latest_metrics)): ?>
                     <!-- Metric KPI Cards -->
                     <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 12px;">
                           <div
                              style="background: #f4f7fa; border-radius: 6px; padding: 14px; text-align: center; border: 1px solid #e2e8f0;">
                              <div
                                 style="font-size: 11px; text-transform: uppercase; color: #718096; font-weight: 700; letter-spacing: 0.5px;">
                                 <i class="fa fa-eye text-primary"></i> Views
                              </div>
                              <div style="font-size: 22px; font-weight: 700; color: #1a202c; margin-top: 4px;">
                                 <?= number_format($latest_metrics->views); ?>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 12px;">
                           <div
                              style="background: #f4f7fa; border-radius: 6px; padding: 14px; text-align: center; border: 1px solid #e2e8f0;">
                              <div
                                 style="font-size: 11px; text-transform: uppercase; color: #718096; font-weight: 700; letter-spacing: 0.5px;">
                                 <i class="fa fa-heart text-danger"></i> Likes
                              </div>
                              <div style="font-size: 22px; font-weight: 700; color: #1a202c; margin-top: 4px;">
                                 <?= number_format($latest_metrics->likes); ?>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 12px;">
                           <div
                              style="background: #f4f7fa; border-radius: 6px; padding: 14px; text-align: center; border: 1px solid #e2e8f0;">
                              <div
                                 style="font-size: 11px; text-transform: uppercase; color: #718096; font-weight: 700; letter-spacing: 0.5px;">
                                 <i class="fa fa-comments text-info"></i> Comments
                              </div>
                              <div style="font-size: 22px; font-weight: 700; color: #1a202c; margin-top: 4px;">
                                 <?= number_format($latest_metrics->comments); ?>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 12px;">
                           <div
                              style="background: #f4f7fa; border-radius: 6px; padding: 14px; text-align: center; border: 1px solid #e2e8f0;">
                              <div
                                 style="font-size: 11px; text-transform: uppercase; color: #718096; font-weight: 700; letter-spacing: 0.5px;">
                                 <i class="fa fa-share text-warning"></i> Shares
                              </div>
                              <div style="font-size: 22px; font-weight: 700; color: #1a202c; margin-top: 4px;">
                                 <?= number_format($latest_metrics->shares); ?>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 12px;">
                           <div
                              style="background: #e8f7ee; border-radius: 6px; padding: 14px; text-align: center; border: 1px solid #c3e6cb;">
                              <div
                                 style="font-size: 11px; text-transform: uppercase; color: #1e7e34; font-weight: 700; letter-spacing: 0.5px;">
                                 <i class="fa fa-pie-chart"></i> Avg ER
                              </div>
                              <div style="font-size: 22px; font-weight: 700; color: #155724; margin-top: 4px;">
                                 <?= round($latest_metrics->engagement_rate * 100, 2); ?>%
                              </div>
                           </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 12px;">
                           <div
                              style="background: #f4f7fa; border-radius: 6px; padding: 14px; text-align: center; border: 1px solid #e2e8f0;">
                              <div
                                 style="font-size: 11px; text-transform: uppercase; color: #718096; font-weight: 700; letter-spacing: 0.5px;">
                                 <i class="fa fa-money text-success"></i> CPV
                              </div>
                              <div style="font-size: 18px; font-weight: 700; color: #1a202c; margin-top: 6px;">
                                 <?= $latest_metrics->cpv !== null ? 'Rp ' . number_format($latest_metrics->cpv, 0, ',', '.') : '-'; ?>
                              </div>
                           </div>
                        </div>
                     </div>

                     <?php if (!empty($metric_histories) && count($metric_histories) > 1): ?>
                        <!-- Historical Snapshots Table -->
                        <div style="margin-top: 15px;">
                           <h5 style="font-weight: 700; color: #4a5568; margin-bottom: 8px;"><i class="fa fa-history"></i>
                              Snapshot History (<?= count($metric_histories); ?> Scrapes)</h5>
                           <div class="table-responsive mscroll">
                              <table class="table table-bordered table-striped" style="font-size: 12px; margin-bottom: 0;">
                                 <thead style="background: #edf2f7; color: #4a5568;">
                                    <tr>
                                       <th>Snapshot Time</th>
                                       <th>Views</th>
                                       <th>Likes</th>
                                       <th>Comments</th>
                                       <th>Shares</th>
                                       <th>Saves</th>
                                       <th>Engagement Rate</th>
                                       <th>CPV</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php foreach ($metric_histories as $mh): ?>
                                       <tr>
                                          <td><?= $mh->snapshot_at; ?></td>
                                          <td><strong><?= number_format($mh->views); ?></strong></td>
                                          <td><?= number_format($mh->likes); ?></td>
                                          <td><?= number_format($mh->comments); ?></td>
                                          <td><?= number_format($mh->shares); ?></td>
                                          <td><?= number_format($mh->saves); ?></td>
                                          <td><span class="label label-success"><?= round($mh->engagement_rate * 100, 2); ?>%</span>
                                          </td>
                                          <td><?= $mh->cpv !== null ? 'Rp ' . number_format($mh->cpv, 0, ',', '.') : '-'; ?></td>
                                       </tr>
                                    <?php endforeach; ?>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     <?php endif; ?>

                  <?php else: ?>
                     <div class="alert alert-info"
                        style="margin-bottom: 0; background-color: #ebf8ff; border-color: #bee3f8; color: #2b6cb0;">
                        <i class="fa fa-info-circle"></i> Live URL sudah dimasukkan, tetapi metrik video belum di-scrape. Klik
                        tombol <strong>"Sync / Scrape Video Metrics"</strong> di atas untuk mengambil data real-time dari
                        Apify.
                     </div>
                  <?php endif; ?>

                  </div> <!-- /#collapsePerf -->

               </div>
            </div>
         <?php endif; ?>

         <!-- ============================================================ -->
         <!-- AUDIENCE SENTIMENT & COMMENT INTELLIGENCE CARD               -->
         <!-- ============================================================ -->
         <?php
         // Pre-decode JSON fields from the sentiment DB record
         $s = $sentiment ?? null;
         $s_total    = $s ? (int) $s->total_comments   : 0;
         $s_pos      = $s ? (int) $s->positive_count   : 0;
         $s_neu      = $s ? (int) $s->neutral_count    : 0;
         $s_neg      = $s ? (int) $s->negative_count   : 0;
         $s_pos_pct  = $s_total > 0 ? round(($s_pos / $s_total) * 100, 1) : 0;
         $s_neu_pct  = $s_total > 0 ? round(($s_neu / $s_total) * 100, 1) : 0;
         $s_neg_pct  = $s_total > 0 ? round(($s_neg / $s_total) * 100, 1) : 0;
         $s_highlights = ($s && !empty($s->highlight_comments)) ? json_decode($s->highlight_comments, true) : ['positive' => [], 'neutral' => [], 'negative' => []];
         $s_keywords   = ($s && !empty($s->frequent_keywords))  ? json_decode($s->frequent_keywords, true)  : [];

         // The "Analyze Sentiment" button is disabled when no live URL exists
         $has_live_url = !empty($live_url);
         ?>

         <div class="box task-card-sentiment" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10); margin-bottom: 20px; border-top: 4px solid #8e44ad;">
            <div class="box-body" style="padding: 20px;">

               <!-- Card Header -->
               <div class="task-sec-header collapsed" data-toggle="collapse" data-target="#collapseSentiment" aria-expanded="false" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid #edf2f7; padding-bottom: 15px; margin-bottom: 18px;">
                  <div>
                     <h4 style="margin: 0; color: #172b4d; font-weight: 700; font-size: 17px;">
                        <i class="fa fa-comments" style="color: #8e44ad; margin-right: 6px;"></i>
                        Analisis Sentimen Komentar
                        <span style="font-size: 11px; font-weight: 600; background: #f3e8ff; color: #6b21a8; border-radius: 10px; padding: 2px 8px; margin-left: 8px; vertical-align: middle;">IndoBERT</span>
                     </h4>
                     <?php if ($s): ?>
                        <div style="margin-top: 4px; color: #6b778c; font-size: 12px;">
                           Terakhir dianalisis: <strong><?= date('d M Y, H:i', strtotime($s->updated_at)); ?> WIB</strong>
                           <?php if (!empty($s->platform)): ?>
                              &mdash; Platform: <span style="text-transform: capitalize;"><?= htmlspecialchars($s->platform); ?></span>
                           <?php endif; ?>
                        </div>
                     <?php endif; ?>
                  </div>
                  <div style="display: flex; align-items: center; gap: 8px;">
                      <span class="label label-<?= ($s && $s_total > 0) ? 'primary' : 'default'; ?>" style="font-size: 11px; padding: 4px 8px;">
                         <?= ($s && $s_total > 0) ? number_format($s_total) . ' Komentar' : 'Belum Ada'; ?>
                      </span>
                      <?php if ($has_live_url): ?>
                         <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="display: flex; flex-direction: column;">
                               <label for="sentiment_limit" style="font-size: 10px; color: #6b778c; font-weight: 600; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.4px;">Max</label>
                               <input type="number" id="sentiment_limit" value="500" min="50" max="1000" step="50"
                                  style="width: 65px; border: 1px solid #d0d7de; border-radius: 4px; padding: 3px 5px; font-size: 12px; font-weight: 600; color: #172b4d; text-align: center; line-height: 1;">
                            </div>
                            <button type="button" class="btn btn-sm btn-flat" id="btn_analyze_sentiment"
                               data-id="<?= $tasks->id; ?>"
                               style="background: #8e44ad; color: #fff; font-weight: 600; border-radius: 4px; align-self: flex-end;">
                               <i class="fa fa-magic"></i> Analyze
                            </button>
                         </div>
                      <?php endif; ?>
                      <i class="fa fa-chevron-down task-sec-chevron"></i>
                  </div>
               </div>

               <div id="collapseSentiment" class="collapse task-sec-body">

               <?php if ($s && $s_total > 0): ?>

                  <!-- KPI Summary Grid -->
                  <div class="row" style="margin-bottom: 18px;">
                     <!-- Total Comments -->
                     <div class="col-md-3 col-sm-6" style="margin-bottom: 10px;">
                        <div style="background: #eef2ff; border-radius: 8px; padding: 14px 16px; border: 1px solid #c7d2fe;">
                           <div style="font-size: 11px; text-transform: uppercase; color: #4338ca; font-weight: 700; letter-spacing: 0.5px;">
                              <i class="fa fa-comment-o"></i> Total Komentar
                           </div>
                           <div style="font-size: 26px; font-weight: 800; color: #312e81; margin-top: 4px;">
                              <?= number_format($s_total); ?>
                           </div>
                        </div>
                     </div>
                     <!-- Positive -->
                     <div class="col-md-3 col-sm-6" style="margin-bottom: 10px;">
                        <div style="background: #f0fdf4; border-radius: 8px; padding: 14px 16px; border: 1px solid #bbf7d0;">
                           <div style="font-size: 11px; text-transform: uppercase; color: #15803d; font-weight: 700; letter-spacing: 0.5px;">
                              <i class="fa fa-smile-o"></i> Positif
                           </div>
                           <div style="font-size: 26px; font-weight: 800; color: #14532d; margin-top: 4px;">
                              <?= number_format($s_pos); ?>
                              <span style="font-size: 13px; font-weight: 600; color: #16a34a;"><?= $s_pos_pct; ?>%</span>
                           </div>
                           <div style="background: #dcfce7; border-radius: 4px; height: 6px; margin-top: 8px; overflow: hidden;">
                              <div style="width: <?= $s_pos_pct; ?>%; background: #16a34a; height: 100%; border-radius: 4px;"></div>
                           </div>
                        </div>
                     </div>
                     <!-- Neutral -->
                     <div class="col-md-3 col-sm-6" style="margin-bottom: 10px;">
                        <div style="background: #f8fafc; border-radius: 8px; padding: 14px 16px; border: 1px solid #cbd5e1;">
                           <div style="font-size: 11px; text-transform: uppercase; color: #475569; font-weight: 700; letter-spacing: 0.5px;">
                              <i class="fa fa-meh-o"></i> Netral
                           </div>
                           <div style="font-size: 26px; font-weight: 800; color: #1e293b; margin-top: 4px;">
                              <?= number_format($s_neu); ?>
                              <span style="font-size: 13px; font-weight: 600; color: #64748b;"><?= $s_neu_pct; ?>%</span>
                           </div>
                           <div style="background: #e2e8f0; border-radius: 4px; height: 6px; margin-top: 8px; overflow: hidden;">
                              <div style="width: <?= $s_neu_pct; ?>%; background: #64748b; height: 100%; border-radius: 4px;"></div>
                           </div>
                        </div>
                     </div>
                     <!-- Negative -->
                     <div class="col-md-3 col-sm-6" style="margin-bottom: 10px;">
                        <div style="background: #fff1f2; border-radius: 8px; padding: 14px 16px; border: 1px solid #fecdd3;">
                           <div style="font-size: 11px; text-transform: uppercase; color: #be123c; font-weight: 700; letter-spacing: 0.5px;">
                              <i class="fa fa-frown-o"></i> Negatif
                           </div>
                           <div style="font-size: 26px; font-weight: 800; color: #881337; margin-top: 4px;">
                              <?= number_format($s_neg); ?>
                              <span style="font-size: 13px; font-weight: 600; color: #e11d48;"><?= $s_neg_pct; ?>%</span>
                           </div>
                           <div style="background: #ffe4e6; border-radius: 4px; height: 6px; margin-top: 8px; overflow: hidden;">
                              <div style="width: <?= $s_neg_pct; ?>%; background: #e11d48; height: 100%; border-radius: 4px;"></div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Topics & Keywords -->
                  <?php if (!empty($s->main_topics) || !empty($s_keywords)): ?>
                  <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 18px;">

                     <?php if (!empty($s->main_topics)): ?>
                     <div style="flex: 1; min-width: 260px; background: #fefce8; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px;">
                        <div style="font-size: 11px; text-transform: uppercase; color: #92400e; font-weight: 700; margin-bottom: 8px;">
                           <i class="fa fa-lightbulb-o"></i> Topik Utama Dibahas
                        </div>
                        <div style="color: #78350f; font-size: 13px; line-height: 1.6;">
                           <?= nl2br(htmlspecialchars($s->main_topics)); ?>
                        </div>
                     </div>
                     <?php endif; ?>

                     <?php if (!empty($s_keywords)): ?>
                     <div style="flex: 1; min-width: 260px; background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 8px; padding: 14px 16px;">
                        <div style="font-size: 11px; text-transform: uppercase; color: #5b21b6; font-weight: 700; margin-bottom: 10px;">
                           <i class="fa fa-tags"></i> Kata Kunci Populer
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                           <?php foreach ($s_keywords as $kw): ?>
                              <span class="label label-default" style="font-size: 12px; padding: 4px 10px; border-radius: 12px; background: #ede9fe; color: #5b21b6; font-weight: 600; border: 0;">
                                 <?= htmlspecialchars($kw); ?>
                              </span>
                           <?php endforeach; ?>
                        </div>
                     </div>
                     <?php endif; ?>

                  </div>
                  <?php endif; ?>

                  <!-- Highlight Comments — 3 Tabs -->
                  <div>
                     <div style="font-size: 11px; text-transform: uppercase; color: #4a5568; font-weight: 700; margin-bottom: 12px;">
                        <i class="fa fa-quote-left"></i> Highlight Komentar
                     </div>

                     <ul class="nav nav-tabs" id="sentiment-tabs" role="tablist" style="margin-bottom: 0;">
                        <li class="active">
                           <a href="#tab-positive" data-toggle="tab">
                              <span style="color: #16a34a; font-weight: 700;">🟢 Positif (<?= count($s_highlights['positive'] ?? []); ?>)</span>
                           </a>
                        </li>
                        <li>
                           <a href="#tab-neutral" data-toggle="tab">
                              <span style="color: #64748b; font-weight: 700;">⚪ Netral (<?= count($s_highlights['neutral'] ?? []); ?>)</span>
                           </a>
                        </li>
                        <li>
                           <a href="#tab-negative" data-toggle="tab">
                              <span style="color: #e11d48; font-weight: 700;">🔴 Negatif (<?= count($s_highlights['negative'] ?? []); ?>)</span>
                           </a>
                        </li>
                     </ul>

                     <div class="tab-content" style="border: 1px solid #e2e8f0; border-top: 0; border-radius: 0 0 6px 6px; padding: 16px; background: #fff;">

                        <!-- Positive Tab -->
                        <div class="tab-pane active" id="tab-positive">
                           <?php if (!empty($s_highlights['positive'])): ?>
                              <?php foreach ($s_highlights['positive'] as $cmt): ?>
                                 <div style="border-left: 4px solid #16a34a; background: #f0fdf4; padding: 10px 14px; border-radius: 0 6px 6px 0; margin-bottom: 8px; font-size: 13px; color: #14532d; line-height: 1.5;">
                                    <i class="fa fa-quote-left" style="color: #86efac; margin-right: 6px; font-size: 10px;"></i>
                                    <?= htmlspecialchars($cmt); ?>
                                 </div>
                              <?php endforeach; ?>
                           <?php else: ?>
                              <p style="color: #94a3b8; font-style: italic; margin: 0;">Tidak ada komentar highlight positif.</p>
                           <?php endif; ?>
                        </div>

                        <!-- Neutral Tab -->
                        <div class="tab-pane" id="tab-neutral">
                           <?php if (!empty($s_highlights['neutral'])): ?>
                              <?php foreach ($s_highlights['neutral'] as $cmt): ?>
                                 <div style="border-left: 4px solid #64748b; background: #f8fafc; padding: 10px 14px; border-radius: 0 6px 6px 0; margin-bottom: 8px; font-size: 13px; color: #1e293b; line-height: 1.5;">
                                    <i class="fa fa-quote-left" style="color: #94a3b8; margin-right: 6px; font-size: 10px;"></i>
                                    <?= htmlspecialchars($cmt); ?>
                                 </div>
                              <?php endforeach; ?>
                           <?php else: ?>
                              <p style="color: #94a3b8; font-style: italic; margin: 0;">Tidak ada komentar highlight netral.</p>
                           <?php endif; ?>
                        </div>

                        <!-- Negative Tab -->
                        <div class="tab-pane" id="tab-negative">
                           <?php if (!empty($s_highlights['negative'])): ?>
                              <?php foreach ($s_highlights['negative'] as $cmt): ?>
                                 <div style="border-left: 4px solid #e11d48; background: #fff1f2; padding: 10px 14px; border-radius: 0 6px 6px 0; margin-bottom: 8px; font-size: 13px; color: #881337; line-height: 1.5;">
                                    <i class="fa fa-quote-left" style="color: #fda4af; margin-right: 6px; font-size: 10px;"></i>
                                    <?= htmlspecialchars($cmt); ?>
                                 </div>
                              <?php endforeach; ?>
                           <?php else: ?>
                              <p style="color: #94a3b8; font-style: italic; margin: 0;">Tidak ada komentar highlight negatif.</p>
                           <?php endif; ?>
                        </div>

                     </div>
                  </div>

               <?php elseif ($s && $s_total === 0): ?>

                  <!-- Zero comments state -->
                  <div class="alert alert-warning" style="margin-bottom: 0; border-radius: 6px;">
                     <i class="fa fa-exclamation-triangle"></i>
                     Analisis sudah dijalankan pada <strong><?= date('d M Y, H:i', strtotime($s->updated_at)); ?></strong>,
                     namun tidak ditemukan komentar yang dapat dianalisis pada URL ini (komentar mungkin dinonaktifkan atau video belum mendapatkan komentar).
                  </div>

               <?php else: ?>

                  <!-- Empty state — never analyzed yet -->
                  <div style="text-align: center; padding: 28px 20px; color: #94a3b8;">
                     <i class="fa fa-comments-o" style="font-size: 40px; margin-bottom: 12px; color: #ddd6fe;"></i>
                     <p style="margin: 0 0 6px 0; font-size: 14px; color: #64748b;">Belum ada analisis sentimen untuk task ini.</p>
                     <?php if ($has_live_url): ?>
                        <p style="margin: 0; font-size: 12px; color: #94a3b8;">Klik tombol <strong>Analyze Sentiment</strong> di atas untuk memulai analisis komentar.</p>
                     <?php else: ?>
                        <p style="margin: 0; font-size: 12px; color: #94a3b8;">Masukkan <strong>Live URL</strong> terlebih dahulu agar analisis sentimen dapat dijalankan.</p>
                     <?php endif; ?>
                  </div>

               <?php endif; ?>

               </div> <!-- /#collapseSentiment -->
            </div>
         </div>
         <!-- END SENTIMENT CARD -->

         <!-- MAIN TASK DETAILS -->
         <div class="box task-card-main" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
            <div class="box-body" style="padding: 20px;">

               <div class="box box-widget widget-user-2">
                  <div
                     style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid #e7edf3; margin-bottom: 20px;">
                     <div>
                        <h3 style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;">Task Information</h3>
                        <div style="margin-top: 5px; color: #6b778c; font-size: 13px;"><?= isset($tasks->title) ? _ent($tasks->title) : ''; ?></div>
                     </div>
                     <div>
                        <?= isset($tasks->status) ? render_status_badge($tasks->status) : ''; ?>
                     </div>
                  </div>

                  <div class="form-horizontal" name="form_tasks" id="form_tasks">
                     <div class="table-responsive mscroll">
                        <table class="table table-bordered kv-stack" style="margin-bottom: 0;">
                           <tr>
                              <td
                                 style="width: 220px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">
                                 Task ID / Nomor Task</td>
                              <td style="vertical-align: middle;">#<?= _ent($tasks->id); ?> &mdash;
                                 <strong><?= _ent($tasks->task_number); ?></strong>
                              </td>
                           </tr>
                           <tr>
                              <td
                                 style="width: 220px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">
                                 Judul Task</td>
                              <td style="vertical-align: middle;"><?= _ent($tasks->title); ?></td>
                           </tr>
                           <tr>
                              <td
                                 style="width: 220px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">
                                 KOL / Influencer</td>
                              <td style="vertical-align: middle;">
                                 <?php if ($tasks->campaign_kol_id): ?>
                                    <a href="<?= site_url('administrator/campaign_kols/view/' . $tasks->campaign_kol_id); ?>"
                                       target="_blank" style="font-weight: 600;">
                                       <i class="fa fa-user"></i> <?= _ent($tasks->campaign_kols_kol_id); ?>
                                    </a>
                                 <?php else: ?>
                                    -
                                 <?php endif; ?>
                              </td>
                           </tr>
                           <tr>
                              <td
                                 style="width: 220px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">
                                 Campaign</td>
                              <td style="vertical-align: middle;"><?= _ent($tasks->campaign_title ?: '-'); ?></td>
                           </tr>
                           <tr>
                              <td
                                 style="width: 220px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">
                                 Konsep Konten</td>
                              <td style="vertical-align: middle;"><?= _ent($tasks->variants_name ?: '-'); ?></td>
                           </tr>
                           <tr>
                              <td
                                 style="width: 220px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">
                                 Platform</td>
                              <td style="vertical-align: middle;">
                                 <span class="label label-default"
                                    style="font-size: 12px;"><?= _ent($tasks->platforms_name ?: '-'); ?></span>
                              </td>
                           </tr>
                           <tr>
                              <td
                                 style="width: 220px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">
                                 Budget</td>
                              <td style="vertical-align: middle; font-weight: 700; color: #2e7d32;">
                                 Rp <?= number_format($tasks->budget, 0, ',', '.'); ?>
                                 <?= _ent($tasks->currency_code ?: 'IDR'); ?>
                              </td>
                           </tr>
                           <tr>
                              <td
                                 style="width: 220px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">
                                 Link & Berkas Konten</td>
                              <td style="vertical-align: middle;">
                                 <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                                    <?php if (!empty($tasks->master_sow)): ?>
                                       <a href="<?= base_url('uploads/campaign_kols/' . $tasks->master_sow); ?>"
                                          target="_blank" class="btn btn-xs btn-info btn-flat"><i
                                             class="fa fa-file-text"></i> Brief</a>
                                    <?php endif; ?>
                                    <?php if (!empty($tasks->brief_file)): ?>
                                       <a href="<?= base_url('uploads/tasks/' . $tasks->brief_file); ?>" target="_blank"
                                          class="btn btn-xs btn-warning btn-flat"><i class="fa fa-file-pdf-o"></i> Brief
                                          Revisi</a>
                                    <?php endif; ?>
                                    <?php if (!empty($tasks->draft_url)): ?>
                                       <a href="<?= htmlspecialchars($tasks->draft_url); ?>" target="_blank"
                                          class="btn btn-xs btn-primary btn-flat"><i class="fa fa-play-circle"></i> Draft
                                          Video</a>
                                    <?php endif; ?>
                                    <?php if (!empty($live_url)): ?>
                                       <a href="<?= htmlspecialchars($live_url); ?>" target="_blank"
                                          class="btn btn-xs btn-success btn-flat"><i class="fa fa-external-link"></i> Live
                                          URL</a>
                                    <?php endif; ?>
                                    <?php if (empty($tasks->master_sow) && empty($tasks->brief_file) && empty($tasks->draft_url) && empty($live_url)): ?>
                                       <span style="color: #999; font-style: italic;">Tidak ada file atau link
                                          terlampir</span>
                                    <?php endif; ?>
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <td
                                 style="width: 220px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">
                                 Estimasi Tgl Posting</td>
                              <td style="vertical-align: middle;"><?= _ent($tasks->estimated_posting_date ?: '-'); ?>
                              </td>
                           </tr>
                           <tr>
                              <td
                                 style="width: 220px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">
                                 Catatan</td>
                              <td style="vertical-align: middle;"><?= nl2br(_ent($tasks->notes ?: '-')); ?></td>
                           </tr>
                           <tr>
                              <td
                                 style="width: 220px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">
                                 Dibuat Oleh</td>
                              <td style="vertical-align: middle;"><?= _ent($tasks->aauth_users_full_name ?: '-'); ?>
                                 pada <?= _ent($tasks->created_at); ?></td>
                           </tr>
                        </table>
                     </div>
                     <div class="cal-swipe-hint" style="margin-top: 6px; font-size: 11px;">
                        <i class="fa fa-arrows-h text-primary"></i> <span>Geser tabel ke samping untuk melihat seluruh detail</span>
                     </div>

                      <!-- BRIEF & SOW SECTION -->
                      <div class="task-inner-sec" style="margin-top: 25px;">
                         <div class="task-sec-header collapsed" data-toggle="collapse" data-target="#collapseBrief" aria-expanded="false" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <h4 style="margin: 0; color: #172b4d; font-size: 16px; font-weight: 700;">
                               <i class="fa fa-file-text-o" style="color: #3b82f6;"></i> Brief & SOW Detail
                            </h4>
                            <div style="display: flex; align-items: center; gap: 8px;">
                               <?php if (!empty($brief)): ?>
                                  <span class="label label-<?= ($brief->status === 'APPROVED' ? 'success' : ($brief->status === 'REVISION' ? 'warning' : 'info')); ?>" style="font-size: 11px; padding: 4px 8px;">
                                     <?= $brief->status; ?> (Rev: <?= (int)$brief->revision_count; ?>)
                                  </span>
                               <?php else: ?>
                                  <span class="label label-default" style="font-size: 11px; padding: 3px 8px;">Belum Ada</span>
                               <?php endif; ?>
                               <i class="fa fa-chevron-down task-sec-chevron"></i>
                            </div>
                         </div>
                         <div id="collapseBrief" class="collapse task-sec-body">
                         <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; margin-bottom: 12px;">
                            <p style="margin: 0 0 8px 0; font-weight: 600; color: #334155;">
                               <strong>Judul Brief:</strong> <?= !empty($brief) ? _ent($brief->title) : _ent($tasks->title); ?>
                            </p>
                            <p style="margin: 0 0 10px 0; color: #475569; font-size: 13px; line-height: 1.5;">
                               <?= (!empty($brief) && !empty($brief->content)) ? nl2br(_ent($brief->content)) : ($tasks->notes ? nl2br(_ent($tasks->notes)) : '<em style="color:#94a3b8;">Belum ada instruksi brief tertulis.</em>'); ?>
                            </p>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                               <?php if (!empty($tasks->master_sow)): ?>
                                  <a href="<?= base_url('uploads/campaign_kols/' . $tasks->master_sow); ?>" target="_blank" class="btn btn-xs btn-info btn-flat"><i class="fa fa-download"></i> Unduh Master SOW</a>
                               <?php endif; ?>
                               <?php if (!empty($tasks->brief_file)): ?>
                                  <a href="<?= base_url('uploads/tasks/' . $tasks->brief_file); ?>" target="_blank" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-download"></i> Unduh File Brief/Revisi</a>
                               <?php endif; ?>
                            </div>
                         </div>
                         <?php if (!empty($brief_approvals)): ?>
                            <div class="table-responsive mscroll" style="margin-bottom: 15px;">
                               <table class="table table-bordered table-condensed" style="font-size: 12px; margin-bottom: 0; background: #fff;">
                                  <thead style="background: #f1f5f9;">
                                     <tr>
                                        <th style="width: 150px;">Waktu Review</th>
                                        <th style="width: 110px;">Status</th>
                                        <th style="width: 130px;">Reviewer</th>
                                        <th>Catatan Leader</th>
                                     </tr>
                                  </thead>
                                  <tbody>
                                     <?php foreach ($brief_approvals as $ba): ?>
                                        <tr>
                                           <td><?= $ba->created_at; ?></td>
                                           <td>
                                              <span class="label label-<?= $ba->status === 'APPROVED' ? 'success' : 'warning'; ?>"><?= $ba->status; ?></span>
                                           </td>
                                           <td><?= _ent($ba->user_full_name ?: '-'); ?></td>
                                           <td><?= _ent($ba->note ?: '-'); ?></td>
                                        </tr>
                                     <?php endforeach; ?>
                                  </tbody>
                               </table>
                            </div>
                         <?php endif; ?>
                      </div> <!-- /#collapseBrief -->
                      </div> <!-- /.task-inner-sec -->

                      <!-- DRAFT SUBMISSIONS & REVIEWS SECTION -->
                      <div class="task-inner-sec" style="margin-top: 25px;">
                         <div class="task-sec-header collapsed" data-toggle="collapse" data-target="#collapseDrafts" aria-expanded="false" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <h4 style="margin: 0; color: #172b4d; font-size: 16px; font-weight: 700;">
                               <i class="fa fa-film" style="color: #6366f1;"></i> Draft Konten & Riwayat Review
                            </h4>
                            <div style="display: flex; align-items: center; gap: 8px;">
                               <span class="label label-<?= !empty($drafts_list) ? 'primary' : 'default'; ?>" style="font-size: 11px; padding: 3px 8px;">
                                  <?= !empty($drafts_list) ? count($drafts_list) . ' Versi' : 'Belum Ada'; ?>
                               </span>
                               <i class="fa fa-chevron-down task-sec-chevron"></i>
                            </div>
                         </div>
                         <div id="collapseDrafts" class="collapse task-sec-body">
                         <?php if (!empty($drafts_list)): ?>
                            <div class="table-responsive mscroll" style="margin-bottom: 15px;">
                               <table class="table table-bordered table-striped" style="font-size: 12px; margin-bottom: 0;">
                                  <thead style="background: #edf2f7; color: #4a5568;">
                                     <tr>
                                        <th style="width: 80px;">Versi</th>
                                        <th>Link Draft Video</th>
                                        <th style="width: 120px;">Status</th>
                                        <th style="width: 160px;">Tanggal Unggah</th>
                                        <th>Catatan / Keterangan</th>
                                     </tr>
                                  </thead>
                                  <tbody>
                                     <?php foreach ($drafts_list as $drf): ?>
                                        <tr>
                                           <td><strong>v<?= (int)$drf->version; ?></strong></td>
                                           <td>
                                              <?php if (!empty($drf->draft_url)): ?>
                                                 <a href="<?= htmlspecialchars($drf->draft_url); ?>" target="_blank" class="btn btn-xs btn-primary btn-flat" style="font-size: 11px;">
                                                    <i class="fa fa-play-circle"></i> Buka Link Draft
                                                 </a>
                                                 <span style="font-size: 11px; color: #64748b; margin-left: 6px;"><?= htmlspecialchars($drf->draft_url); ?></span>
                                              <?php else: ?>
                                                 -
                                              <?php endif; ?>
                                           </td>
                                           <td>
                                              <?php
                                              $drf_label = 'default';
                                              if ($drf->status === 'APPROVED') $drf_label = 'success';
                                              elseif ($drf->status === 'SUBMITTED') $drf_label = 'primary';
                                              elseif ($drf->status === 'REVISION') $drf_label = 'warning';
                                              ?>
                                              <span class="label label-<?= $drf_label; ?>"><?= $drf->status; ?></span>
                                           </td>
                                           <td><?= $drf->created_at; ?></td>
                                           <td><?= _ent($drf->notes ?: '-'); ?></td>
                                        </tr>
                                     <?php endforeach; ?>
                                  </tbody>
                               </table>
                            </div>
                         <?php else: ?>
                            <div class="alert alert-default" style="background: #f8fafc; border: 1px dashed #cbd5e1; color: #64748b; font-size: 13px; margin-bottom: 15px;">
                               <i class="fa fa-info-circle"></i> Belum ada draft video yang diunggah untuk task ini.
                            </div>
                         <?php endif; ?>

                         <?php if (!empty($draft_reviews)): ?>
                            <h5 style="margin: 10px 0 8px 0; color: #475569; font-weight: 700; font-size: 13px;"><i class="fa fa-comments-o"></i> Log Riwayat Review Draft:</h5>
                            <div class="table-responsive mscroll" style="margin-bottom: 15px;">
                               <table class="table table-bordered table-condensed" style="font-size: 11px; margin-bottom: 0; background: #fff;">
                                  <thead style="background: #f8fafc;">
                                     <tr>
                                        <th style="width: 140px;">Waktu</th>
                                        <th style="width: 70px;">Versi</th>
                                        <th style="width: 100px;">Aksi</th>
                                        <th style="width: 120px;">Reviewer</th>
                                        <th>Feedback / Catatan Review</th>
                                     </tr>
                                  </thead>
                                  <tbody>
                                     <?php foreach ($draft_reviews as $drev): ?>
                                        <tr>
                                           <td><?= $drev->created_at; ?></td>
                                           <td>v<?= (int)$drev->draft_version; ?></td>
                                           <td>
                                              <span class="label label-<?= ($drev->action === 'APPROVED' ? 'success' : ($drev->action === 'REVISION' ? 'warning' : 'primary')); ?>"><?= $drev->action; ?></span>
                                           </td>
                                           <td><?= _ent($drev->user_full_name ?: '-'); ?></td>
                                           <td><?= _ent($drev->note ?: '-'); ?></td>
                                        </tr>
                                     <?php endforeach; ?>
                                  </tbody>
                               </table>
                            </div>
                         <?php endif; ?>
                      </div>

                         </div> <!-- /#collapseDrafts -->
                      </div>

                     <!-- AUDIT / STATUS HISTORY -->
                     <?php if (!empty($status_histories)): ?>
                        <div class="task-inner-sec" style="margin-top: 25px;">
                           <div class="task-sec-header collapsed" data-toggle="collapse" data-target="#collapseTimeline" aria-expanded="false" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                              <h4 style="margin: 0; color: #172b4d; font-size: 16px; font-weight: 700;"><i
                                    class="fa fa-history"></i> Status & Pipeline Timeline</h4>
                              <div style="display: flex; align-items: center; gap: 8px;">
                                 <span class="label label-default" style="font-size: 11px; padding: 3px 8px;">
                                    <?= count($status_histories); ?> Log
                                 </span>
                                 <i class="fa fa-chevron-down task-sec-chevron"></i>
                              </div>
                           </div>
                           <div id="collapseTimeline" class="collapse task-sec-body">
                           <div class="table-responsive mscroll">
                              <table class="table table-bordered table-striped" style="font-size: 12px; margin-bottom: 0;">
                                 <thead style="background: #edf2f7; color: #4a5568;">
                                    <tr>
                                       <th style="width: 160px;">Waktu</th>
                                       <th style="width: 140px;">Status</th>
                                       <th style="width: 140px;">Diubah Oleh</th>
                                       <th>Catatan</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php foreach ($status_histories as $sh): ?>
                                       <tr>
                                          <td><?= $sh->created_at; ?></td>
                                          <td><?= render_status_badge($sh->status); ?></td>
                                          <td><?= _ent($sh->user_full_name ?: '-'); ?></td>
                                          <td><?= _ent($sh->note ?: '-'); ?></td>
                                       </tr>
                                    <?php endforeach; ?>
                                 </tbody>
                              </table>
                           </div>
                              </div> <!-- /#collapseTimeline -->
                        </div>
                     <?php endif; ?>

                     <div class="view-nav" style="margin-top: 22px;">
                        <?php is_allowed('tasks_update', function () use ($tasks) { ?>
                           <a class="btn btn-flat btn-primary btn_edit btn_action" id="btn_edit" data-stype='back'
                              title="edit tasks (Ctrl+e)"
                              href="<?= site_url('administrator/tasks/edit/' . $tasks->id); ?>"><i
                                 class="fa fa-pencil"></i>
                              <?= cclang('update', ['Tasks']); ?> </a>
                        <?php }) ?>
                        <?php 
                           $src = isset($_GET['src']) ? $_GET['src'] : '';
                           $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                           if ($src === 'postings' || strpos($referer, 'view=live_posting') !== false) {
                              $back_url = site_url('administrator/tasks?view=live_posting');
                              $back_text = 'Go To Live Posting';
                           } elseif ($src === 'drafts' || strpos($referer, 'view=review_draft') !== false) {
                              $back_url = site_url('administrator/tasks?view=review_draft');
                              $back_text = 'Go To Review Draft';
                           } else {
                              $back_url = site_url('administrator/tasks?view=monitoring');
                              $back_text = 'Go To Monitoring Task';
                           }
                        ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)"
                           href="<?= $back_url ?>"><i class="fa fa-undo"></i>
                           <?= $back_text; ?></a>
                     </div>

                  </div>
               </div>
            </div>
         </div>

      </div>
   </div>
</section>

<!-- Page Script for Sync Button -->
<script>
   $(document).ready(function () {
      $('#btn_sync_detail').click(function () {
         var $btn = $(this);
         var taskId = $btn.data('id');
         var originalHtml = $btn.html();

         $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Scraping data...');

         var postData = { id: taskId };
         var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
         var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
         postData[csrfName] = csrfHash;

         $.ajax({
            url: BASE_URL + 'administrator/tasks/sync_posting_metrics/' + taskId,
            type: 'POST',
            dataType: 'json',
            data: postData,
            success: function (res) {
               $btn.prop('disabled', false).html(originalHtml);
               if (res.success) {
                  swal({
                     title: "Scraping Berhasil!",
                     text: res.message,
                     type: "success",
                     confirmButtonText: "OK"
                  }, function () {
                     window.location.reload();
                  });
               } else {
                  swal({
                     title: "Gagal Sync",
                     text: res.message || "Gagal melakukan scraping video.",
                     type: "error",
                     confirmButtonText: "Tutup"
                  });
               }
            },
            error: function (xhr) {
               $btn.prop('disabled', false).html(originalHtml);
               swal({
                  title: "Error",
                  text: "Terjadi kesalahan saat menghubungi server Apify.",
                  type: "error",
                  confirmButtonText: "Tutup"
               });
            }
         });
      });
   });
</script>

<!-- Sentiment Analysis AJAX Handler -->
<script>
   $(document).ready(function () {
      $('#btn_analyze_sentiment').click(function () {
         var $btn    = $(this);
         var taskId  = $btn.data('id');
         var commentLimit = parseInt($('#sentiment_limit').val(), 10) || 500;
         if (commentLimit < 50)  commentLimit = 50;
         if (commentLimit > 1000) commentLimit = 1000;

         // SweetAlert confirmation to prevent accidental clicks on expensive API calls
         swal({
            title: "Analisis Sentimen",
            text: "Proses ini akan mengambil maksimal " + commentLimit + " komentar dari postingan dan menganalisisnya menggunakan IndoBERT. Proses dapat memakan waktu 10–30 detik. Lanjutkan?",
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#8e44ad",
            confirmButtonText: "Ya, Analisis Sekarang",
            cancelButtonText: "Batal"
         }, function (isConfirm) {
            if (!isConfirm) return;

            // Double-click guard: disable immediately
            var origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sedang menganalisis komentar...');

             var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
             var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
             var postData = {};
             postData[csrfName] = csrfHash;
             postData['limit'] = commentLimit;

            $.ajax({
               url:      BASE_URL + 'administrator/tasks/analyze_sentiment/' + taskId,
               type:     'POST',
               dataType: 'json',
               data:     postData,
               timeout:  90000, // 90s — generous for scraping + inference

               success: function (res) {
                  // Sync refreshed CSRF token
                  if (res[csrfName]) {
                     csrfHash = res[csrfName];
                  }

                  $btn.prop('disabled', false).html(origHtml);

                  if (res.status) {
                     swal({
                        title: "Analisis Berhasil!",
                        text: res.message,
                        type: "success",
                        confirmButtonText: "Lihat Hasil"
                     }, function () {
                        window.location.reload();
                     });
                  } else {
                     swal({
                        title: "Analisis Gagal",
                        text: res.message || "Terjadi kesalahan yang tidak diketahui.",
                        type: "warning",
                        confirmButtonText: "Tutup"
                     });
                  }
               },

               error: function (xhr) {
                  $btn.prop('disabled', false).html(origHtml);

                  var msg = "Terjadi kesalahan saat menghubungi server. Pastikan Laragon aktif dan coba lagi.";
                  if (xhr.status === 401) {
                     msg = "Sesi Anda telah berakhir. Silakan refresh halaman dan login kembali.";
                  } else if (xhr.status === 0) {
                     msg = "Koneksi timeout atau microservice tidak merespons setelah 90 detik. Coba lagi beberapa saat.";
                  }

                  swal({
                     title: "Gagal Terhubung",
                     text: msg,
                     type: "error",
                     confirmButtonText: "Tutup"
                  });
               }
            });
         });
      });
   });
</script>

<!-- Collapse Mobile Interaction Handler -->
<script>
   $(document).ready(function () {
      // Cegah klik tombol / link / input di dalam header memicu toggle collapse
      $('.task-sec-header a, .task-sec-header button, .task-sec-header input, .task-sec-header label').on('click', function (e) {
         e.stopPropagation();
      });

      // Sinkronisasi status class collapsed agar animasi panah kebawah akurat
      $('.task-sec-body').on('show.bs.collapse', function () {
         var targetId = $(this).attr('id');
         $('[data-target="#' + targetId + '"]').removeClass('collapsed').attr('aria-expanded', 'true');
      });
      $('.task-sec-body').on('hide.bs.collapse', function () {
         var targetId = $(this).attr('id');
         $('[data-target="#' + targetId + '"]').addClass('collapsed').attr('aria-expanded', 'false');
      });
   });
</script>
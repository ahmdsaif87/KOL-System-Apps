<style>
/* Modern Compact UI for My Tasks (Creator Deliverables Hub) */
.task-header-area {
   margin-bottom: 20px;
}
.task-header-area h1 {
   font-size: 22px;
   font-weight: 800;
   color: #0f172a;
   letter-spacing: -0.02em;
   margin: 0;
}
.task-header-area p {
   color: #64748b;
   font-size: 13px;
   margin: 3px 0 0 0;
}

/* Stat Cards */
.task-stat-card {
   background: #ffffff;
   border-radius: 10px;
   padding: 14px 18px;
   box-shadow: 0 1px 3px rgba(0,0,0,0.03);
   border: 1px solid #e2e8f0;
   margin-bottom: 16px;
   display: flex;
   align-items: center;
   transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.task-stat-card:hover {
   transform: translateY(-2px);
   box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.task-stat-card .stat-icon-wrapper {
   width: 42px;
   height: 42px;
   border-radius: 8px;
   display: flex;
   align-items: center;
   justify-content: center;
   font-size: 18px;
   margin-right: 14px;
   flex-shrink: 0;
}
.task-stat-card .stat-title {
   font-size: 10.5px;
   font-weight: 700;
   color: #64748b;
   text-transform: uppercase;
   letter-spacing: 0.5px;
   display: block;
}
.task-stat-card .stat-number {
   font-size: 22px;
   font-weight: 800;
   color: #0f172a;
   line-height: 1.2;
   margin-top: 1px;
   display: block;
}

/* Navigation & Filter Bar */
.task-filter-container {
   background: #ffffff;
   border: 1px solid #e2e8f0;
   border-radius: 10px;
   padding: 10px 16px;
   margin-bottom: 16px;
   display: flex;
   justify-content: space-between;
   align-items: center;
   flex-wrap: wrap;
   gap: 12px;
   box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.task-pills-nav {
   display: flex;
   flex-wrap: wrap;
   gap: 6px;
   align-items: center;
}
.task-pill-btn {
   display: inline-flex;
   align-items: center;
   gap: 6px;
   padding: 6px 13px;
   border-radius: 20px;
   font-size: 12px;
   font-weight: 600;
   text-decoration: none !important;
   transition: all 0.15s ease-in-out;
   border: 1px solid transparent;
}
.task-pill-btn.active {
   background: #0f172a;
   color: #ffffff !important;
   box-shadow: 0 2px 6px rgba(15,23,42,0.18);
}
.task-pill-btn:not(.active) {
   background: #f8fafc;
   color: #475569;
   border-color: #e2e8f0;
}
.task-pill-btn:not(.active):hover {
   background: #f1f5f9;
   color: #0f172a;
   border-color: #cbd5e1;
}
.task-pill-badge {
   font-size: 11px;
   font-weight: 700;
   padding: 1px 7px;
   border-radius: 10px;
   line-height: 1.35;
}
.task-pill-btn.active .task-pill-badge {
   background: rgba(255, 255, 255, 0.25);
   color: #ffffff;
}
.task-pill-btn:not(.active) .task-pill-badge {
   background: #e2e8f0;
   color: #475569;
}

/* Search Input */
.task-search-wrapper {
   position: relative;
   max-width: 260px;
   width: 100%;
}
.task-search-input {
   width: 100%;
   height: 36px;
   padding: 6px 14px 6px 34px;
   border-radius: 20px;
   border: 1px solid #cbd5e1;
   font-size: 12px;
   color: #1e293b;
   background: #f8fafc;
   outline: none;
   transition: all 0.2s ease;
}
.task-search-input:focus {
   background: #ffffff;
   border-color: #2563eb;
   box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
}
.task-search-icon {
   position: absolute;
   left: 12px;
   top: 10px;
   color: #94a3b8;
   font-size: 13px;
   pointer-events: none;
}
.task-search-reset {
   position: absolute;
   right: 10px;
   top: 9px;
   color: #94a3b8;
   cursor: pointer;
   font-size: 13px;
}
.task-search-reset:hover {
   color: #ef4444;
}

/* Compact Deliverable Card */
.deliverable-card {
   background: #ffffff;
   border: 1px solid #e2e8f0;
   border-radius: 10px;
   padding: 16px 20px;
   margin-bottom: 12px;
   box-shadow: 0 1px 3px rgba(0,0,0,0.02);
   transition: all 0.2s ease;
}
.deliverable-card:hover {
   border-color: #cbd5e1;
   box-shadow: 0 4px 14px rgba(0,0,0,0.05);
}

.deliverable-main-row {
   display: flex;
   justify-content: space-between;
   align-items: center;
   flex-wrap: wrap;
   gap: 16px;
}
.deliverable-content {
   flex: 1;
   min-width: 320px;
}
.deliverable-actions-col {
   display: flex;
   flex-direction: column;
   align-items: flex-end;
   justify-content: center;
   gap: 8px;
   flex-shrink: 0;
}
.deliverable-buttons-wrap {
   display: flex;
   align-items: center;
   flex-wrap: wrap;
   gap: 8px;
}

/* Content Elements */
.deliverable-top-meta {
   display: flex;
   align-items: center;
   flex-wrap: wrap;
   gap: 6px;
   margin-bottom: 6px;
}
.card-campaign-chip {
   display: inline-flex;
   align-items: center;
   gap: 5px;
   background: #eff6ff;
   color: #1d4ed8;
   font-size: 10.5px;
   font-weight: 700;
   padding: 2px 8px;
   border-radius: 20px;
   text-transform: uppercase;
   letter-spacing: 0.3px;
}
.card-task-title {
   margin: 0 0 6px 0;
   font-size: 16px;
   font-weight: 700;
   color: #0f172a;
   line-height: 1.3;
}
.meta-chip {
   display: inline-flex;
   align-items: center;
   gap: 4px;
   background: #f1f5f9;
   color: #334155;
   padding: 2px 7px;
   border-radius: 4px;
   font-weight: 600;
   font-size: 11px;
}

/* Inline Details (Draft History & Brief Downloads) */
.deliverable-inline-details {
   display: flex;
   align-items: center;
   flex-wrap: wrap;
   gap: 10px;
   margin-top: 6px;
}
.draft-history-inline {
   display: inline-flex;
   align-items: center;
   flex-wrap: wrap;
   gap: 6px;
}
.draft-history-label {
   font-size: 11.5px;
   font-weight: 600;
   color: #64748b;
}
.draft-chip {
   display: inline-flex;
   align-items: center;
   gap: 5px;
   background: #f8fafc;
   border: 1px solid #e2e8f0;
   padding: 2px 8px;
   border-radius: 5px;
   font-size: 11.5px;
}
.draft-chip .ver {
   font-weight: 700;
   color: #0f172a;
}
.draft-chip a {
   color: #2563eb;
   font-weight: 600;
   text-decoration: none;
}
.draft-chip a:hover {
   text-decoration: underline;
}
.draft-chip .time {
   color: #94a3b8;
   font-size: 10.5px;
}

/* Brief files inline button */
.btn-brief-file {
   display: inline-flex;
   align-items: center;
   gap: 4px;
   background: #ffffff;
   border: 1px solid #cbd5e1;
   color: #334155 !important;
   padding: 3px 8px;
   border-radius: 4px;
   font-size: 11px;
   font-weight: 600;
   text-decoration: none !important;
}
.btn-brief-file:hover {
   background: #f1f5f9;
   border-color: #94a3b8;
}

/* Badges */
.badge-state {
   display: inline-flex;
   align-items: center;
   gap: 5px;
   padding: 4px 10px;
   border-radius: 20px;
   font-size: 11.5px;
   font-weight: 700;
   letter-spacing: 0.2px;
}
.badge-state-waiting {
   background: #fef3c7;
   color: #92400e;
   border: 1px solid #fde68a;
}
.badge-state-review {
   background: #f5f3ff;
   color: #6d28d9;
   border: 1px solid #ddd6fe;
}
.badge-state-revision {
   background: #fee2e2;
   color: #b91c1c;
   border: 1px solid #fecaca;
}
.badge-state-approved {
   background: #dcfce7;
   color: #15803d;
   border: 1px solid #bbf7d0;
}
.badge-state-posted {
   background: #e0f2fe;
   color: #0369a1;
   border: 1px solid #bae6fd;
}
.badge-state-done {
   background: #ecfdf5;
   color: #065f46;
   border: 1px solid #a7f3d0;
}

.btn-open-live-link {
   display: inline-flex;
   align-items: center;
   gap: 5px;
   background: #ecfdf5;
   color: #059669 !important;
   border: 1px solid #a7f3d0;
   border-radius: 6px;
   padding: 6px 12px;
   font-size: 12px;
   font-weight: 600;
   text-decoration: none !important;
   transition: all 0.15s ease;
}
.btn-open-live-link:hover {
   background: #d1fae5;
   border-color: #6ee7b7;
   color: #047857 !important;
}

/* Action Buttons */
.btn-action-primary {
   display: inline-flex;
   align-items: center;
   gap: 5px;
   background: #2563eb;
   color: #ffffff !important;
   border: 1px solid #1d4ed8;
   padding: 6px 14px;
   border-radius: 6px;
   font-size: 12.5px;
   font-weight: 600;
   cursor: pointer;
   transition: all 0.15s ease;
   box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.btn-action-primary:hover {
   background: #1d4ed8;
   box-shadow: 0 2px 6px rgba(37,99,235,0.25);
}
.btn-action-warning {
   display: inline-flex;
   align-items: center;
   gap: 5px;
   background: #f59e0b;
   color: #ffffff !important;
   border: 1px solid #d97706;
   padding: 6px 14px;
   border-radius: 6px;
   font-size: 12.5px;
   font-weight: 600;
   cursor: pointer;
   transition: all 0.15s ease;
   box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.btn-action-warning:hover {
   background: #d97706;
}
.btn-action-success {
   display: inline-flex;
   align-items: center;
   gap: 5px;
   background: #059669;
   color: #ffffff !important;
   border: 1px solid #047857;
   padding: 6px 14px;
   border-radius: 6px;
   font-size: 12.5px;
   font-weight: 600;
   cursor: pointer;
   transition: all 0.15s ease;
   box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.btn-action-success:hover {
   background: #047857;
   box-shadow: 0 2px 6px rgba(5,150,105,0.25);
}
.text-review-notice {
   display: inline-flex;
   align-items: center;
   gap: 5px;
   color: #7c3aed;
   font-size: 12px;
   font-weight: 600;
   background: #f5f3ff;
   border: 1px solid #ddd6fe;
   padding: 5px 10px;
   border-radius: 6px;
}

/* Secondary expandable details */
.card-brief-box {
   background: #f8fafc;
   border: 1px solid #e2e8f0;
   border-radius: 6px;
   padding: 8px 12px;
   margin-top: 8px;
   font-size: 12px;
}
.card-brief-title {
   font-size: 11px;
   font-weight: 700;
   text-transform: uppercase;
   color: #475569;
   display: block;
}
.card-revision-alert {
   background: #fffbeb;
   border: 1px solid #fde68a;
   border-radius: 6px;
   padding: 10px 12px;
   margin-top: 8px;
}
.card-revision-title {
   color: #b45309;
   font-weight: 700;
   font-size: 12px;
   margin-bottom: 2px;
   display: flex;
   align-items: center;
   gap: 5px;
}
.card-revision-content {
   color: #78350f;
   font-size: 12px;
   line-height: 1.45;
}
</style>

<!-- Content Header -->
<section class="content-header task-header-area">
   <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      <div>
         <h1>My Tasks</h1>
         <p>Manage deliverables, submit video drafts for review, and register live post URLs.</p>
      </div>
      <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0; font-size: 12px;">
         <li><a href="<?= site_url('administrator/dashboard'); ?>" style="color: #64748b;"><i class="fa fa-dashboard"></i> Home</a></li>
         <li class="active" style="color: #0f172a; font-weight: 600;">My Tasks</li>
      </ol>
   </div>
</section>

<!-- Main content -->
<section class="content" style="padding-top: 0;">
   <div class="row">
      <div class="col-md-12">

         <?php if (!empty($unlinked_kol)): ?>
            <!-- Account Unlinked State -->
            <div style="background: #ffffff; border: 1px solid #fed7aa; border-radius: 10px; padding: 32px 20px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
               <div style="width: 50px; height: 50px; background: #fef3c7; color: #d97706; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px;">
                  <i class="fa fa-user-circle-o"></i>
               </div>
               <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">Account Not Linked to a KOL Profile</h3>
               <p style="color: #64748b; max-width: 460px; margin: 0 auto 16px; font-size: 13px; line-height: 1.5;">
                  Your login email is not yet linked to a verified creator profile in the system. Please reach out to the brand management team to link your profile.
               </p>
               <a href="<?= site_url('administrator/user/profile'); ?>" class="btn btn-default btn-flat" style="border-radius: 6px; font-weight: 600; padding: 6px 14px;">
                  <i class="fa fa-user"></i> View Account Profile
               </a>
            </div>
         <?php else: ?>

            <!-- 1. Stats Counter Cards -->
            <div class="row" style="margin-bottom: 4px;">
               <div class="col-md-3 col-sm-6">
                  <div class="task-stat-card">
                     <div class="stat-icon-wrapper" style="background: #eff6ff; color: #2563eb;">
                        <i class="fa fa-tasks"></i>
                     </div>
                     <div>
                        <span class="stat-title">Total Tasks</span>
                        <span class="stat-number"><?= (int)($count_summary['all'] ?? 0); ?></span>
                     </div>
                  </div>
               </div>
               <div class="col-md-3 col-sm-6">
                  <div class="task-stat-card">
                     <div class="stat-icon-wrapper" style="background: #fffbeb; color: #d97706;">
                        <i class="fa fa-cloud-upload"></i>
                     </div>
                     <div>
                        <span class="stat-title">Needs Draft Upload</span>
                        <span class="stat-number" style="color: #d97706;"><?= (int)($count_summary['needs_draft'] ?? 0); ?></span>
                     </div>
                  </div>
               </div>
               <div class="col-md-3 col-sm-6">
                  <div class="task-stat-card">
                     <div class="stat-icon-wrapper" style="background: #f5f3ff; color: #7c3aed;">
                        <i class="fa fa-hourglass-half"></i>
                     </div>
                     <div>
                        <span class="stat-title">Under Review</span>
                        <span class="stat-number" style="color: #7c3aed;"><?= (int)($count_summary['in_review'] ?? 0); ?></span>
                     </div>
                  </div>
               </div>
               <div class="col-md-3 col-sm-6">
                  <div class="task-stat-card">
                     <div class="stat-icon-wrapper" style="background: #ecfdf5; color: #059669;">
                        <i class="fa fa-check-circle"></i>
                     </div>
                     <div>
                        <span class="stat-title">Approved & Live</span>
                        <span class="stat-number" style="color: #059669;"><?= (int)(($count_summary['ready_to_post'] ?? 0) + ($count_summary['live_done'] ?? 0)); ?></span>
                     </div>
                  </div>
               </div>
            </div>

            <!-- 2. Filter Tabs & Search Bar -->
            <div class="task-filter-container">
               <div class="task-pills-nav">
                  <a href="<?= site_url('administrator/my_tasks?tab=all' . (!empty($q) ? '&q=' . urlencode($q) : '')); ?>" 
                     class="task-pill-btn <?= $tab === 'all' ? 'active' : ''; ?>">
                     <span>All Tasks</span>
                     <span class="task-pill-badge"><?= $count_summary['all']; ?></span>
                  </a>
                  <a href="<?= site_url('administrator/my_tasks?tab=needs_draft' . (!empty($q) ? '&q=' . urlencode($q) : '')); ?>" 
                     class="task-pill-btn <?= $tab === 'needs_draft' ? 'active' : ''; ?>">
                     <span>Needs Draft</span>
                     <span class="task-pill-badge"><?= $count_summary['needs_draft']; ?></span>
                  </a>
                  <a href="<?= site_url('administrator/my_tasks?tab=in_review' . (!empty($q) ? '&q=' . urlencode($q) : '')); ?>" 
                     class="task-pill-btn <?= $tab === 'in_review' ? 'active' : ''; ?>">
                     <span>Under Review</span>
                     <span class="task-pill-badge"><?= $count_summary['in_review']; ?></span>
                  </a>
                  <a href="<?= site_url('administrator/my_tasks?tab=ready_to_post' . (!empty($q) ? '&q=' . urlencode($q) : '')); ?>" 
                     class="task-pill-btn <?= $tab === 'ready_to_post' ? 'active' : ''; ?>">
                     <span>Ready to Post</span>
                     <span class="task-pill-badge"><?= $count_summary['ready_to_post']; ?></span>
                  </a>
                  <a href="<?= site_url('administrator/my_tasks?tab=live_done' . (!empty($q) ? '&q=' . urlencode($q) : '')); ?>" 
                     class="task-pill-btn <?= $tab === 'live_done' ? 'active' : ''; ?>">
                     <span>Live & Completed</span>
                     <span class="task-pill-badge"><?= $count_summary['live_done']; ?></span>
                  </a>
               </div>

               <!-- Search Form -->
               <form method="GET" action="<?= site_url('administrator/my_tasks'); ?>" class="task-search-wrapper">
                  <input type="hidden" name="tab" value="<?= _ent($tab); ?>">
                  <i class="fa fa-search task-search-icon"></i>
                  <input type="text" name="q" class="task-search-input" placeholder="Search task or campaign..." value="<?= _ent($q); ?>">
                  <?php if (!empty($q)): ?>
                     <a href="<?= site_url('administrator/my_tasks?tab=' . $tab); ?>" class="task-search-reset" title="Clear search">
                        <i class="fa fa-times"></i>
                     </a>
                  <?php endif; ?>
               </form>
            </div>

            <!-- 3. Deliverable Cards List -->
            <?php if (!empty($tasks)): ?>
               <div style="display: flex; flex-direction: column; gap: 4px;">
                  <?php foreach ($tasks as $t): ?>
                     <?php 
                        $is_revision = ($t->status === 'REVISION_DRAFT');
                        $is_approved = ($t->status === 'APPROVED_DRAFT');
                        $is_live     = in_array($t->status, ['POSTED', 'DONE']);
                        $is_waiting  = ($t->status === 'WAITING_DRAFT');
                        $is_review   = in_array($t->status, ['IN_REVIEW_DRAFT', 'PENDING_REVIEW']);
                     ?>
                     <div class="deliverable-card">
                        
                        <div class="deliverable-main-row">
                           <!-- Left: Content Info -->
                           <div class="deliverable-content">
                              <div class="deliverable-top-meta">
                                 <span class="card-campaign-chip">
                                    <i class="fa fa-bullhorn"></i> <?= _ent($t->campaign_title ?: 'Campaign'); ?>
                                 </span>
                                 <?php if (!empty($t->platforms_name)): ?>
                                    <span class="meta-chip">
                                       <i class="fa fa-globe text-primary"></i> <?= _ent($t->platforms_name); ?>
                                    </span>
                                 <?php endif; ?>
                                 <?php if (!empty($t->variants_name)): ?>
                                    <span class="meta-chip">
                                       <i class="fa fa-tag text-info"></i> <?= _ent($t->variants_name); ?>
                                    </span>
                                 <?php endif; ?>
                                 <span class="meta-chip" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <i class="fa fa-calendar-o text-muted"></i> Due: <strong><?= !empty($t->estimated_posting_date) ? date('d M Y', strtotime($t->estimated_posting_date)) : 'TBD'; ?></strong>
                                 </span>
                              </div>

                              <h3 class="card-task-title">
                                 <?= _ent($t->title); ?>
                              </h3>

                              <!-- Inline details: Draft history & Brief files -->
                              <div class="deliverable-inline-details">
                                 <?php if (!empty($t->drafts)): ?>
                                    <div class="draft-history-inline">
                                       <span class="draft-history-label"><i class="fa fa-history"></i> Draft History:</span>
                                       <?php foreach ($t->drafts as $dr): ?>
                                          <div class="draft-chip">
                                             <span class="ver">v<?= $dr->version; ?></span>
                                             <a href="<?= _ent($dr->draft_url); ?>" target="_blank">
                                                <i class="fa fa-play-circle"></i> Watch Video
                                             </a>
                                             <span class="time"><?= date('d M H:i', strtotime($dr->created_at)); ?></span>
                                             <span class="badge" style="background: <?= $dr->status === 'APPROVED' ? '#10b981' : ($dr->status === 'REVISION' ? '#f59e0b' : '#64748b'); ?>; font-size: 10px; padding: 2px 6px;">
                                                <?= $dr->status; ?>
                                             </span>
                                          </div>
                                       <?php endforeach; ?>
                                    </div>
                                 <?php endif; ?>

                                 <?php if (!empty($t->brief_file) || !empty($t->master_sow)): ?>
                                    <div style="display: inline-flex; gap: 6px;">
                                       <?php if (!empty($t->brief_file)): ?>
                                          <a href="<?= base_url('uploads/tasks/' . $t->brief_file); ?>" target="_blank" class="btn-brief-file">
                                             <i class="fa fa-download text-primary"></i> Download Brief
                                          </a>
                                       <?php endif; ?>
                                       <?php if (!empty($t->master_sow)): ?>
                                          <a href="<?= base_url('uploads/campaign_kols/' . $t->master_sow); ?>" target="_blank" class="btn-brief-file">
                                             <i class="fa fa-file-pdf-o text-danger"></i> SOW Contract
                                          </a>
                                       <?php endif; ?>
                                    </div>
                                 <?php endif; ?>
                              </div>

                              <!-- Brief Text Notes (if any) -->
                              <?php if (!empty($t->brief_note)): ?>
                                 <div class="card-brief-box">
                                    <span class="card-brief-title"><i class="fa fa-file-text-o"></i> Brief Guidelines:</span>
                                    <div style="color: #334155; line-height: 1.45; margin-top: 3px;"><?= _ent($t->brief_note); ?></div>
                                 </div>
                              <?php endif; ?>

                              <!-- Revision Alert (if revision) -->
                              <?php if ($is_revision): ?>
                                 <div class="card-revision-alert">
                                    <div class="card-revision-title">
                                       <i class="fa fa-exclamation-triangle"></i> Revision Feedback from Brand:
                                    </div>
                                    <div class="card-revision-content">
                                       <?= !empty($t->draft_note) ? nl2br(_ent($t->draft_note)) : 'Please review the brand instructions and submit an updated draft revision.'; ?>
                                    </div>
                                 </div>
                              <?php endif; ?>
                           </div>

                           <!-- Right: Status & Actions Unified Cluster -->
                           <div class="deliverable-actions-col">
                              <div>
                                 <?php if ($t->status === 'WAITING_DRAFT'): ?>
                                    <span class="badge-state badge-state-waiting"><i class="fa fa-clock-o"></i> Needs Draft Upload</span>
                                 <?php elseif ($t->status === 'IN_REVIEW_DRAFT' || $t->status === 'PENDING_REVIEW'): ?>
                                    <span class="badge-state badge-state-review"><i class="fa fa-hourglass-half"></i> Under Brand Review</span>
                                 <?php elseif ($t->status === 'REVISION_DRAFT'): ?>
                                    <span class="badge-state badge-state-revision"><i class="fa fa-exclamation-circle"></i> Revision Requested</span>
                                 <?php elseif ($t->status === 'APPROVED_DRAFT'): ?>
                                    <span class="badge-state badge-state-approved"><i class="fa fa-check-circle"></i> Approved &bull; Ready to Post</span>
                                 <?php elseif ($t->status === 'POSTED'): ?>
                                    <span class="badge-state badge-state-posted"><i class="fa fa-share-alt"></i> Live Post Submitted</span>
                                 <?php elseif ($t->status === 'DONE'): ?>
                                    <span class="badge-state badge-state-done"><i class="fa fa-check-square"></i> Completed</span>
                                 <?php else: ?>
                                    <span class="badge-state badge-state-waiting"><?= _ent($t->status); ?></span>
                                 <?php endif; ?>
                                 <div style="font-size:11px; color:#94a3b8; margin-top:4px;">
                                    <?php if ($t->status === 'WAITING_DRAFT'): ?>Langkah 3/6: upload draft setelah produk diterima.<?php elseif ($is_review): ?>Langkah 4/6: menunggu review brand.<?php elseif ($is_approved): ?>Langkah 5/6: posting lalu submit link live.<?php elseif ($is_live): ?>Langkah 6/6: live tercatat, metrik diproses otomatis.<?php endif; ?>
                                 </div>
                              </div>

                              <div class="deliverable-buttons-wrap">
                                 <?php if (!empty($t->live_url)): ?>
                                    <a href="<?= _ent($t->live_url); ?>" target="_blank" class="btn-open-live-link">
                                       <i class="fa fa-external-link"></i> View Live Post
                                    </a>
                                 <?php endif; ?>

                                  <?php if (in_array($t->status, ['WAITING_DRAFT', 'REVISION_DRAFT', 'DRAFT'])): ?>
                                     <?php if (!empty($t->needs_product) && empty($t->can_upload_draft)): ?>
                                        <?php $sh = $t->latest_shipment; ?>
                                        <div class="card-revision-alert" style="border-color: #fde68a; background: #fffbeb; flex-basis: 100%; width: 100%;">
                                           <div class="card-revision-title" style="color: #92400e;">
                                              <i class="fa fa-truck"></i> Produk <?= $sh && $sh->status === 'DELIVERED' ? 'sudah sampai' : 'dalam pengiriman (' . _ent($sh ? $sh->status : 'PENDING') . ')'; ?>
                                           </div>
                                           <div class="card-revision-content" style="color: #78350f;">
                                              <?= $sh && !empty($sh->tracking_no) ? 'Resi: <strong>' . _ent($sh->tracking_no) . '</strong> (' . _ent($sh->courier ?: 'kurir') . '). ' : ''; ?>Konfirmasi penerimaan untuk membuka upload draft.
                                           </div>
                                        </div>
                                        <?php if ($sh && in_array($sh->status, ['SHIPPED', 'DELIVERED'])): ?>
                                        <button type="button" class="btn-action-primary btn-confirm-receipt"
                                           data-shipment-id="<?= $sh->id; ?>">
                                           <i class="fa fa-check-circle"></i> Konfirmasi Produk Diterima
                                        </button>
                                        <?php else: ?>
                                        <span class="text-review-notice">
                                           <i class="fa fa-clock-o"></i> Menunggu produk dikirim
                                        </span>
                                        <?php endif; ?>
                                     <?php elseif ($is_revision): ?>
                                        <button type="button" class="btn-action-warning btn-modal-draft" 
                                          data-id="<?= $t->id; ?>" 
                                          data-title="<?= _ent($t->title); ?>" 
                                          data-version="<?= (!empty($t->latest_draft) ? $t->latest_draft->version : 0) + 1; ?>">
                                          <i class="fa fa-refresh"></i> Upload Revised Draft (v<?= (!empty($t->latest_draft) ? $t->latest_draft->version : 0) + 1; ?>)
                                       </button>
                                    <?php else: ?>
                                       <button type="button" class="btn-action-primary btn-modal-draft" 
                                          data-id="<?= $t->id; ?>" 
                                          data-title="<?= _ent($t->title); ?>" 
                                          data-version="<?= (!empty($t->latest_draft) ? $t->latest_draft->version : 0) + 1; ?>">
                                          <i class="fa fa-cloud-upload"></i> Upload Draft Video
                                       </button>
                                    <?php endif; ?>
                                 <?php elseif ($is_review): ?>
                                    <span class="text-review-notice">
                                       <i class="fa fa-clock-o"></i> Review in progress
                                    </span>
                                 <?php endif; ?>

                                  <?php if (in_array($t->status, ['APPROVED_DRAFT', 'POSTED', 'DONE'])): ?>
                                     <?php if (in_array($t->campaign_status ?? '', ['ACTIVE', 'COMPLETED'])): ?>
                                     <button type="button" class="btn-action-success btn-modal-live" 
                                        data-id="<?= $t->id; ?>" 
                                        data-title="<?= _ent($t->title); ?>" 
                                        data-url="<?= _ent($t->live_url ?? ''); ?>"
                                        data-platform="<?= _ent($t->platforms_name ?: 'Platform'); ?>">
                                        <i class="fa fa-external-link"></i> <?= !empty($t->live_url) ? 'Update Live Link' : 'Submit Live Link'; ?>
                                     </button>
                                     <?php else: ?>
                                     <span class="text-review-notice" title="Live link hanya dapat disubmit setelah campaign ACTIVE">
                                        <i class="fa fa-clock-o"></i> Menunggu campaign ACTIVE
                                     </span>
                                     <?php endif; ?>
                                  <?php endif; ?>
                              </div>
                           </div>
                        </div>

                     </div>
                  <?php endforeach; ?>
               </div>
            <?php else: ?>
               <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 40px 20px; text-align: center; color: #64748b;">
                  <div style="width: 48px; height: 48px; background: #f1f5f9; color: #94a3b8; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 10px;">
                     <i class="fa fa-folder-open-o"></i>
                  </div>
                  <h4 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0;">No Tasks Found</h4>
                  <p style="font-size: 12.5px; max-width: 380px; margin: 0 auto; color: #94a3b8;">
                     <?= !empty($q) ? 'No deliverable tasks matched "' . _ent($q) . '".' : 'There are currently no tasks in this tab. Switch to another tab to view your deliverables.'; ?>
                  </p>
               </div>
            <?php endif; ?>

         <?php endif; ?>

      </div>
   </div>
</section>

<!-- Modal: Submit Draft Video -->
<div class="modal fade" id="modalSubmitDraft" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content" style="border-radius: 10px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.18); border: 0;">
         <div class="modal-header" style="background: #0f172a; color: #ffffff; padding: 14px 20px;">
            <button type="button" class="close" data-dismiss="modal" style="color: #ffffff; opacity: 0.8;">&times;</button>
            <h4 class="modal-title" style="font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
               <i class="fa fa-cloud-upload text-primary"></i> <span id="draftModalHeader">Upload Draft Video</span>
            </h4>
         </div>
         <form id="formKolsSubmitDraft">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" id="draft_csrf_token">
            <input type="hidden" name="task_id" id="draft_task_id">

            <div class="modal-body" style="padding: 18px 20px;">
               <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 12.5px; color: #1e40af;">
                  Task: <strong id="draftTaskTitle">-</strong> <span class="badge" style="background: #2563eb; margin-left: 6px;" id="draftVersionBadge">v1</span>
               </div>

               <div class="form-group">
                  <label style="font-size: 12px; font-weight: 700; color: #1e293b;">Draft Video Link (Google Drive / Video Preview) <span class="text-danger">*</span></label>
                  <input type="url" class="form-control" name="draft_url" id="input_draft_url" placeholder="https://drive.google.com/file/d/... or video preview link" required style="border-radius: 6px; height: 38px; border: 1px solid #cbd5e1;">
                  <small class="text-muted" style="font-size: 11px; margin-top: 4px; display: block;">
                     <i class="fa fa-info-circle text-info"></i> Make sure Google Drive sharing permission is set to <strong>"Anyone with the link can view"</strong> so the brand team can review.
                  </small>
               </div>

               <div class="form-group" style="margin-top: 12px;">
                  <label style="font-size: 12px; font-weight: 700; color: #1e293b;">Planned Caption &amp; Hashtags <small style="font-weight: normal; color: #64748b;">(Optional)</small></label>
                  <textarea class="form-control" name="caption" id="input_draft_caption" rows="3" placeholder="Paste your proposed caption and hashtags here..." style="border-radius: 6px; border: 1px solid #cbd5e1;"></textarea>
               </div>

               <div class="form-group" style="margin-top: 12px;">
                  <label style="font-size: 12px; font-weight: 700; color: #1e293b;">Notes for Brand Team <small style="font-weight: normal; color: #64748b;">(Optional)</small></label>
                  <input type="text" class="form-control" name="notes" id="input_draft_notes" placeholder="e.g. Revised hook at 00:05 as requested" style="border-radius: 6px; height: 38px; border: 1px solid #cbd5e1;">
               </div>

               <div id="draftSubmitError" class="alert alert-danger" style="display:none; margin-top: 12px; font-size: 12px; padding: 10px 14px; border-radius: 6px;"></div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 12px 20px;">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius: 6px; font-weight: 600;">Cancel</button>
               <button type="submit" class="btn btn-primary btn-flat" id="btnDraftSubmit" style="border-radius: 6px; font-weight: 600; background: #2563eb; border-color: #1d4ed8; padding: 7px 18px;">
                  <i class="fa fa-paper-plane"></i> Submit Draft
               </button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal: Submit Live URL -->
<div class="modal fade" id="modalSubmitLive" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content" style="border-radius: 10px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.18); border: 0;">
         <div class="modal-header" style="background: #065f46; color: #ffffff; padding: 14px 20px;">
            <button type="button" class="close" data-dismiss="modal" style="color: #ffffff; opacity: 0.8;">&times;</button>
            <h4 class="modal-title" style="font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
               <i class="fa fa-external-link"></i> Submit Live Post URL
            </h4>
         </div>
         <form id="formKolsSubmitLive">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" id="live_csrf_token">
            <input type="hidden" name="task_id" id="live_task_id">

            <div class="modal-body" style="padding: 18px 20px;">
               <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 12.5px; color: #065f46;">
                  Task: <strong id="liveTaskTitle">-</strong> &bull; Platform: <strong id="livePlatformLabel">Platform</strong>
               </div>

               <div class="form-group">
                  <label style="font-size: 12px; font-weight: 700; color: #1e293b;">Live Video / Post URL <span class="text-danger">*</span></label>
                  <input type="url" class="form-control" name="live_url" id="input_live_url" placeholder="https://www.tiktok.com/@username/video/... or https://instagram.com/p/..." required style="border-radius: 6px; height: 38px; border: 1px solid #cbd5e1;">
                  <small class="text-muted" style="font-size: 11px; margin-top: 4px; display: block;">
                     <i class="fa fa-info-circle text-info"></i> Paste the public URL directly from the app (TikTok/Instagram/YouTube). Analytics and view counts will begin tracking automatically.
                  </small>
               </div>

               <div id="liveSubmitError" class="alert alert-danger" style="display:none; margin-top: 12px; font-size: 12px; padding: 10px 14px; border-radius: 6px;"></div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 12px 20px;">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius: 6px; font-weight: 600;">Cancel</button>
               <button type="submit" class="btn btn-success btn-flat" id="btnLiveSubmit" style="border-radius: 6px; font-weight: 600; background: #059669; border-color: #047857; padding: 7px 18px;">
                  <i class="fa fa-check"></i> Save Live Link
               </button>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
$(document).ready(function() {
   // Confirm product receipt (KOL POV)
   $(document).on('click', '.btn-confirm-receipt', function() {
      var btn = $(this);
      var shipmentId = btn.data('shipment-id');
      swal({
         title: "Produk sudah diterima?",
         text: "Pastikan paket sampel sudah sampai dan lengkap sebelum konfirmasi.",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#00a65a",
         confirmButtonText: "Ya, Sudah Diterima!",
         cancelButtonText: "Batal",
         closeOnConfirm: true
      }, function(isConfirm) {
         if (!isConfirm) return;
         btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
         var postData = { shipment_id: shipmentId };
         postData[$('#draft_csrf_token').attr('name')] = $('#draft_csrf_token').val();
         $.ajax({
            url: BASE_URL + 'administrator/tasks/kol_confirm_receipt',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(res) {
               if (res.csrf_hash) {
                  $('#draft_csrf_token').val(res.csrf_hash);
                  $('#live_csrf_token').val(res.csrf_hash);
               }
               if (res.success) {
                  swal({ title: "Berhasil!", text: res.message, type: "success" }, function() {
                     location.reload();
                  });
               } else {
                  swal("Gagal", res.message || 'Gagal menyimpan konfirmasi.', "error");
                  btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Konfirmasi Produk Diterima');
               }
            },
            error: function() {
               swal("Gagal", "Terjadi kesalahan jaringan.", "error");
               btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Konfirmasi Produk Diterima');
            }
         });
      });
   });

   // Modal trigger: Draft
   $('.btn-modal-draft').click(function() {
      var id = $(this).data('id');
      var title = $(this).data('title');
      var ver = $(this).data('version') || 1;

      $('#draft_task_id').val(id);
      $('#draftTaskTitle').text(title);
      $('#draftVersionBadge').text('v' + ver);
      $('#draftModalHeader').text(ver > 1 ? 'Upload Revised Draft (v' + ver + ')' : 'Upload Draft Video');
      $('#input_draft_url').val('');
      $('#input_draft_caption').val('');
      $('#input_draft_notes').val('');
      $('#draftSubmitError').hide().text('');
      $('#modalSubmitDraft').modal('show');
   });

   // Handle AJAX Draft Submit
   $('#formKolsSubmitDraft').submit(function(e) {
      e.preventDefault();
      var taskId = $('#draft_task_id').val();
      var btn = $('#btnDraftSubmit');
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
      $('#draftSubmitError').hide();

      $.ajax({
         url: BASE_URL + 'administrator/tasks/kol_submit_draft/' + taskId,
         type: 'POST',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(res) {
            if (res.csrf_hash) {
               $('#draft_csrf_token').val(res.csrf_hash);
               $('#live_csrf_token').val(res.csrf_hash);
            }
            if (res.success) {
               $('#modalSubmitDraft').modal('hide');
               swal({
                  title: "Draft Submitted!",
                  text: res.message || "Your draft video has been submitted for brand review.",
                  type: "success"
               }, function() {
                  location.reload();
               });
            } else {
               $('#draftSubmitError').text(res.message || 'Failed to submit draft.').show();
               btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Submit Draft');
            }
         },
         error: function() {
            $('#draftSubmitError').text('A network or server error occurred. Please try again.').show();
            btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Submit Draft');
         }
      });
   });

   // Modal trigger: Live URL
   $('.btn-modal-live').click(function() {
      var id = $(this).data('id');
      var title = $(this).data('title');
      var url = $(this).data('url');
      var platform = $(this).data('platform') || 'Platform';

      $('#live_task_id').val(id);
      $('#liveTaskTitle').text(title);
      $('#livePlatformLabel').text(platform);
      $('#input_live_url').val(url);
      $('#liveSubmitError').hide().text('');
      $('#modalSubmitLive').modal('show');
   });

   // Handle AJAX Live Submit
   $('#formKolsSubmitLive').submit(function(e) {
      e.preventDefault();
      var taskId = $('#live_task_id').val();
      var btn = $('#btnLiveSubmit');
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
      $('#liveSubmitError').hide();

      $.ajax({
         url: BASE_URL + 'administrator/tasks/kol_submit_live_url/' + taskId,
         type: 'POST',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(res) {
            if (res.csrf_hash) {
               $('#draft_csrf_token').val(res.csrf_hash);
               $('#live_csrf_token').val(res.csrf_hash);
            }
            if (res.success) {
               $('#modalSubmitLive').modal('hide');
               swal({
                  title: "Live URL Saved!",
                  text: res.message || "Live video link has been saved. Performance analytics tracking will begin shortly.",
                  type: "success"
               }, function() {
                  location.reload();
               });
            } else {
               $('#liveSubmitError').text(res.message || 'Failed to save live URL.').show();
               btn.prop('disabled', false).html('<i class="fa fa-check"></i> Save Live Link');
            }
         },
         error: function() {
            $('#liveSubmitError').text('A network or server error occurred. Please try again.').show();
            btn.prop('disabled', false).html('<i class="fa fa-check"></i> Save Live Link');
         }
      });
   });
});
</script>

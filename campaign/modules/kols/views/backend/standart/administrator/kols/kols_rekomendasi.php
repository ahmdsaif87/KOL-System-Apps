<link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/select2/select2.min.css">
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script src="<?= BASE_ASSET; ?>admin-lte/plugins/select2/select2.full.min.js"></script>

<style>
/* ── Select2 Modern Styling ─────────────────────────────── */
.select2-container--default .select2-selection--multiple {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    min-height: 38px;
    padding: 2px 6px;
    background: #fff;
    box-shadow: none;
    transition: border-color 0.15s ease;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #2563eb;
    outline: 0;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
    border-radius: 4px;
    padding: 2px 8px;
    font-size: 12px;
    font-weight: 500;
    margin: 3px 4px 3px 0;
    line-height: 1.3;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #3b82f6;
    margin-right: 6px;
    font-weight: bold;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #1d4ed8;
}
.select2-dropdown {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    box-shadow: 0 4px 14px rgba(15,23,42,0.08);
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #2563eb;
    color: #fff;
}
.select2-container--default .select2-selection--single {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    height: 38px;
    padding: 4px 8px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px;
    color: #1e293b;
    font-size: 13px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

/* ── Recommendations Generator Styles ────────────────────── */
.rekom-header-card {
    background: #fff;
    border-radius: 8px;
    border: 1px solid #dfe6ee;
    padding: 20px 24px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(15,23,42,0.05);
}
.rekom-tab-btn {
    padding: 9px 18px;
    font-weight: 600;
    font-size: 13px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    background: #f8fafc;
    color: #475569;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.rekom-tab-btn.active {
    background: #2563eb;
    color: #ffffff;
    border-color: #1d4ed8;
    box-shadow: 0 2px 6px rgba(37,99,235,0.25);
}
.rekom-tab-btn:hover:not(.active) {
    background: #e2e8f0;
    color: #1e293b;
}

/* ── Result Cards ────────────────────────────────────────── */
#rekom-results-container {
    display: flex;
    flex-wrap: wrap;
}
.card-item-wrapper {
    display: flex;
    flex-direction: column;
}
.rekom-result-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    height: 100%;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.rekom-result-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 4px 14px rgba(37,99,235,0.10);
    transform: translateY(-2px);
}
.rekom-result-card.selected {
    border-color: #2563eb;
    background: #f8faff;
}
.rekom-avatar-initials {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1f4b6e 0%, #2f80b5 100%);
    color: #ffffff;
    font-size: 16px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.rekom-avatar-img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #cbd5e1;
    flex-shrink: 0;
}
.rekom-rank-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.rekom-rank-1 {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}
.rekom-rank-top {
    background: #e0f2fe;
    color: #075985;
    border: 1px solid #bae6fd;
}
.rekom-rank-other {
    background: #f1f5f9;
    color: #475569;
}

.rekom-metrics-grid {
    background: #ffffff;
    border-radius: 6px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    font-size: 11px;
    margin: 12px 0;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}
.rekom-metric-item {
    display: flex;
    flex-direction: column;
    padding: 10px;
    border-bottom: 1px solid #e2e8f0;
}
.rekom-metric-item:nth-child(odd) {
    border-right: 1px solid #e2e8f0;
}
.rekom-metric-item:nth-child(3), .rekom-metric-item:nth-child(4) {
    border-bottom: none;
}
.rekom-metric-label {
    color: #64748b;
    font-size: 10px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.rekom-metric-value {
    color: #0f172a;
    font-weight: 700;
    font-size: 13px;
    margin-top: 2px;
    font-variant-numeric: tabular-nums;
}

.rekom-avatar-container {
    position: relative;
}
.rekom-rank-badge-new {
    position: absolute;
    top: -8px;
    left: -8px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 12px;
    border: 1px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.rekom-match-score {
    position: absolute;
    top: 16px;
    right: 16px;
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    padding: 4px 10px;
    border-radius: 16px;
    font-weight: 700;
    font-size: 12px;
}
.btn-ghost {
    background: transparent;
    color: #334155;
    border: 1px solid #cbd5e1;
}
.btn-ghost:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #94a3b8;
}
</style>

<!-- Content Header -->
<section class="content-header">
   <h1>
      KOL Recommendations Generator <small>Lookalike Matching &amp; Criteria Sourcing</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#">Home</a></li>
      <li><a href="<?= site_url('administrator/kols'); ?>">Kols</a></li>
      <li class="active">Recommendations Generator</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">

         <!-- Hidden CSRF token -->
         <input type="hidden" id="rekom-csrf-token"
                name="<?= $this->security->get_csrf_token_name(); ?>"
                value="<?= $this->security->get_csrf_hash(); ?>" />

         <!-- ══════════════════════════════════════════════
              GENERATOR CONTROLS CARD
              ══════════════════════════════════════════════ -->
         <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.08); margin-bottom: 20px;">
            <div class="box-body" style="padding: 20px 24px;">

               <!-- Mode Switcher Tabs -->
               <div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px; flex-wrap: wrap; align-items: center;">
                  <button type="button" class="rekom-tab-btn <?= empty($baseline_kol_id) ? 'active' : ''; ?>" id="tab-btn-criteria" data-target="#panel-criteria">
                     Custom Criteria Sourcing
                  </button>
                  <button type="button" class="rekom-tab-btn <?= !empty($baseline_kol_id) ? 'active' : ''; ?>" id="tab-btn-lookalike" data-target="#panel-lookalike">
                     Lookalike Generator (By Reference KOL)
                  </button>
               </div>

               <!-- PANEL 1: Custom Criteria Form -->
               <div id="panel-criteria" style="<?= !empty($baseline_kol_id) ? 'display: none;' : ''; ?>">
                  <form id="form-criteria-generator">
                     
                     <!-- Section 1: Primary Filters -->
                     <div style="margin-bottom: 16px;">
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: #64748b; margin-bottom: 10px;">
                           Primary Filters
                        </div>
                        <div class="row">
                           <div class="col-md-3 col-sm-6" style="margin-bottom: 14px;">
                              <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
                                 Categories / Niches
                              </label>
                              <select class="form-control select2" name="categories[]" id="filter-categories" multiple="multiple" style="width: 100%;" data-placeholder="Choose categories...">
                                 <?php foreach ($meta['categories'] as $c): ?>
                                    <option value="<?= $c->id; ?>"><?= htmlspecialchars($c->name); ?></option>
                                 <?php endforeach; ?>
                              </select>
                           </div>

                           <div class="col-md-3 col-sm-6" style="margin-bottom: 14px;">
                              <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
                                 Channels / Platforms
                              </label>
                              <select class="form-control select2" name="platforms[]" id="filter-platforms" multiple="multiple" style="width: 100%;" data-placeholder="Choose platforms...">
                                 <?php foreach ($meta['platforms'] as $p): ?>
                                    <option value="<?= $p->id; ?>"><?= htmlspecialchars($p->name); ?></option>
                                 <?php endforeach; ?>
                              </select>
                           </div>

                           <div class="col-md-2 col-sm-6" style="margin-bottom: 14px;"><label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">KOL Tiers</label>
                              <select class="form-control select2" name="tiers[]" id="filter-tiers" multiple="multiple" style="width: 100%;" data-placeholder="Choose tiers...">
                                 <?php foreach ($meta['tiers'] as $t): ?>
                                    <option value="<?= $t->id; ?>"><?= htmlspecialchars($t->name); ?> (<?= number_format($t->min_followers); ?>+)</option>
                                 <?php endforeach; ?>
                              </select>
                           </div>
                           
                           <div class="col-md-3 col-sm-6" style="margin-bottom: 14px;">
<label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">Tipe Layanan</label>
<select class="form-control" name="service_type" id="filter-service-type" style="border-radius: 4px; height: 38px;">
<option value="">-- Pilih Tipe --</option>
<option value="price_vt">Short Form</option>
<option value="price_live">Live Streaming</option>
<option value="price_long_form">Long Form</option>
</select>
</div>
<div class="col-md-3 col-sm-6" style="margin-bottom: 14px;">
<label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">Maksimal Budget (IDR)</label>
<input type="number" class="form-control" name="max_budget" id="filter-max-budget" placeholder="e.g. 5000000" min="0" style="border-radius: 4px; height: 38px;">
</div>
                        </div>
                     </div>

                     <!-- Advanced Filters Toggle -->
                     <div style="margin-bottom: 16px;">
                        <a role="button" data-toggle="collapse" href="#advanced-filters-section" aria-expanded="false" aria-controls="advanced-filters-section" style="font-size: 12px; font-weight: 600; color: #2563eb; text-decoration: none;">
                           <i class="fa fa-sliders"></i> Show Advanced Filters
                        </a>
                     </div>

                     <!-- Section 2: Advanced Filters (Collapsed) -->
                     <div class="collapse" id="advanced-filters-section">
                        <div style="margin-bottom: 16px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
                           <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: #64748b; margin-bottom: 10px;">
                              Advanced Options
                           </div>
                           <div class="row">
                              <div class="col-md-3 col-sm-6" style="margin-bottom: 14px;">
                                 <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">Gender Preference</label>
                                 <select class="form-control" name="gender" id="filter-gender" style="border-radius: 4px; height: 38px;">
                                    <option value="">All Genders</option>
                                    <option value="Female">Female</option>
                                    <option value="Male">Male</option>
                                 </select>
                              </div>
                              <div class="col-md-3 col-sm-6" style="margin-bottom: 14px;">
                                 <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">City / Location</label>
                                 <input type="text" class="form-control" name="city" id="filter-city" placeholder="e.g. Jakarta" style="border-radius: 4px; height: 38px;">
                              </div>
                              <div class="col-md-2 col-sm-6" style="margin-bottom: 14px;">
                                 <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">Min. Avg Views</label>
                                 <input type="number" class="form-control" name="min_views" id="filter-min-views" placeholder="e.g. 25000" min="0" style="border-radius: 4px; height: 38px;">
                              </div>
                              <div class="col-md-2 col-sm-6" style="margin-bottom: 14px;">
                                 <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">Min. ER (%)</label>
                                 <input type="number" step="0.1" class="form-control" name="min_er" id="filter-min-er" placeholder="e.g. 2.5" min="0" max="100" style="border-radius: 4px; height: 38px;">
                              </div>
                              <div class="col-md-2 col-sm-12" style="margin-bottom: 14px;">
                                 <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">Optimization</label>
                                 <select class="form-control" name="priority" id="filter-priority" style="border-radius: 4px; font-weight: 600; height: 38px;">
                                    <option value="match" selected>Best Overall Match</option>
                                    <option value="er">Highest ER</option>
                                    <option value="views">Max Views</option>
                                    <option value="budget">Budget Efficiency</option>
                                    <option value="rating">Top Rating</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Actions Row -->
                     <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 16px; margin-top: 8px; flex-wrap: wrap; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                           <span style="font-size: 12px; color: #64748b; font-weight: 600;">Quantity:</span>
                           <select class="form-control input-sm" name="limit" id="filter-limit" style="width: 80px; display: inline-block; border-radius: 4px; height: 34px;">
                              <option value="3">3</option>
                              <option value="6" selected>6</option>
                              <option value="12">12</option>
                              <option value="20">20</option>
                              <option value="30">30</option>
                           </select>
                           <span style="font-size: 12px; color: #64748b;">candidates</span>
                        </div>

                        <div style="display: flex; gap: 10px;">
                           <button type="button" class="btn btn-default btn-flat" id="btn-reset-filters" style="border-radius: 4px; padding: 7px 16px; font-weight: 500;">
                              Reset
                           </button>
                           <button type="submit" class="btn btn-primary btn-flat" id="btn-submit-criteria" style="border-radius: 4px; font-weight: 600; background: #2563eb; border-color: #1d4ed8; padding: 7px 22px;">
                              Generate Recommendations
                           </button>
                        </div>
                     </div>
                  </form>
               </div>

               <!-- PANEL 2: Lookalike Generator Form -->
               <div id="panel-lookalike" style="<?= empty($baseline_kol_id) ? 'display: none;' : ''; ?>">
                  <form id="form-lookalike-generator">
                     <div class="row" style="display: flex; align-items: flex-end; flex-wrap: wrap;">
                        <div class="col-md-9 col-sm-12" style="margin-bottom: 14px;">
                           <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
                              Select Reference Baseline KOL *
                           </label>
                           <select class="form-control select2" name="baseline_kol_id" id="lookalike-baseline-kol" style="width: 100%;" required>
                              <option value="">-- Choose a Reference KOL to Match Against --</option>
                              <?php foreach ($meta['kols_list'] as $k): ?>
                                 <option value="<?= $k->id; ?>" <?= ($baseline_kol_id == $k->id) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($k->name); ?> (<?= htmlspecialchars($k->tier_name ?? 'Standard'); ?>)
                                 </option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                        <div class="col-md-3 col-sm-12" style="margin-bottom: 14px;">
                           <button type="submit" class="btn btn-primary btn-flat btn-block" id="btn-submit-lookalike" style="border-radius: 4px; height: 38px; font-weight: 600; background: #2563eb; border-color: #1d4ed8;">
                              Find Lookalikes
                           </button>
                        </div>
                     </div>
                     <small style="color: #94a3b8; font-size: 11px; margin-top: 4px; display: block;">
                        The engine will mathematically compute category overlap, audience scale similarity, and performance metrics relative to this creator.
                     </small>
                  </form>
               </div>
               
               <div style="text-align: right; margin-top: 16px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                  <span style="color: #94a3b8; font-size: 11px; font-weight: 500;">Powered by Hybrid Jaccard Similarity &amp; Multi-Attribute Vector Engine</span>
               </div>
            </div>
         </div>

         <!-- ══════════════════════════════════════════════
              RESULTS TOOLBAR
              ══════════════════════════════════════════════ -->
         <div id="rekom-results-toolbar" style="display: <?= !empty($initial_recommendations) ? 'flex' : 'none'; ?>; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 12px;">
               <h4 id="rekom-results-title" style="margin: 0; font-size: 16px; font-weight: 700; color: #1e293b;">
                  Generated Recommendations
                  <span id="rekom-count-badge" class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 12px; margin-left: 6px;">
                     <?= !empty($initial_recommendations) ? count($initial_recommendations) : 0; ?> creators
                  </span>
               </h4>
               <label style="font-weight: normal; font-size: 13px; color: #475569; margin: 0; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                  <input type="checkbox" id="check-select-all" style="cursor: pointer;"> Select All
               </label>
            </div>

            <div style="display: flex; gap: 8px;">
               <button type="button" class="btn btn-sm btn-default btn-flat" id="btn-export-results" style="border-radius: 4px; font-weight: 600;">
                  Export List (CSV)
               </button>
               <button type="button" class="btn btn-sm btn-primary btn-flat" id="btn-batch-assign" disabled style="border-radius: 4px; font-weight: 600; background: #2563eb; border-color: #1d4ed8;">
                  Invite Selected to Campaign (<span id="selected-count">0</span>)
               </button>
            </div>
         </div>

         <!-- ══════════════════════════════════════════════
              RESULTS GRID CONTAINER
              ══════════════════════════════════════════════ -->
         <div id="rekom-results-container" class="row">
            <?php if (!empty($initial_recommendations)): ?>
               <?php foreach ($initial_recommendations as $idx => $sim): ?>
                  <?php
                     $sim_name = $sim->name ?? 'KOL';
                     $sim_initials = strtoupper(
                         mb_substr($sim_name, 0, 1) .
                         (mb_substr(trim(strstr($sim_name, ' ')), 0, 1) ?: mb_substr($sim_name, 1, 1))
                     );
                     $match = $sim->match_percentage ?? 50;
                     $match_badge_class = ($match >= 85) ? 'label-success' : (($match >= 70) ? 'label-info' : 'label-default');
                     $rank = $idx + 1;
                     $rank_class = ($rank === 1) ? 'rekom-rank-1' : (($rank <= 3) ? 'rekom-rank-top' : 'rekom-rank-other');
                     $breakdown_title = "Category: {$sim->breakdown['category_match_pct']}% | Platform: {$sim->breakdown['platform_match_pct']}% | Audience Scale: {$sim->breakdown['audience_match_pct']}% | Performance: {$sim->breakdown['performance_match_pct']}%";
                  ?>
                  <div class="col-md-4 col-sm-6 col-xs-12 card-item-wrapper" style="margin-bottom: 20px;">
                     <div class="rekom-result-card" data-id="<?= $sim->id; ?>" data-name="<?= htmlspecialchars($sim_name); ?>">
                        <div>
                           <!-- Match Score Top Right -->
                           <div class="rekom-match-score" data-toggle="tooltip" title="<?= htmlspecialchars($breakdown_title); ?>">
                              <?= $match; ?>% Match
                           </div>

                           <!-- Creator Header -->
                           <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding-right: 80px;">
                              <div style="display: flex; align-items: center; gap: 10px;">
                                 <input type="checkbox" class="kol-select-check" value="<?= $sim->id; ?>" style="cursor: pointer; width: 16px; height: 16px; margin: 0;">
                                 
                                 <div class="rekom-avatar-container">
                                    <span class="rekom-rank-badge-new <?= $rank_class; ?>">
                                       #<?= $rank; ?>
                                    </span>
                                    <?php if (!empty($sim->avatar_url)): ?>
                                       <img src="<?= htmlspecialchars($sim->avatar_url); ?>" alt="<?= htmlspecialchars($sim_name); ?>" class="rekom-avatar-img" />
                                    <?php else: ?>
                                       <div class="rekom-avatar-initials"><?= htmlspecialchars($sim_initials); ?></div>
                                    <?php endif; ?>
                                 </div>
                              </div>

                              <div style="flex: 1; min-width: 0;">
                                 <a href="<?= site_url('administrator/kols/view/' . $sim->id); ?>" target="_blank" style="font-weight: 700; color: #1e293b; font-size: 15px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= htmlspecialchars($sim_name); ?>
                                 </a>
                                 <div style="display: flex; gap: 6px; align-items: center; margin-top: 3px; flex-wrap: wrap;">
                                    <span class="label label-primary" style="font-size: 10px; font-weight: 600; padding: 2px 6px;">
                                       <?= htmlspecialchars($sim->tier_name ?? 'Standard'); ?>
                                    </span>
                                    <?php if (!empty($sim->categories) && isset($sim->categories[0])): ?>
                                       <span class="label" style="background: #f1f5f9; color: #334155; font-size: 10px; border: 1px solid #e2e8f0; border-radius: 3px;">
                                          <?= htmlspecialchars($sim->categories[0]); ?>
                                       </span>
                                    <?php endif; ?>
                                 </div>
                              </div>
                           </div>

                           <!-- Metrics 2x2 Grid -->
                           <div class="rekom-metrics-grid">
                              <div class="rekom-metric-item">
                                 <span class="rekom-metric-label">Followers</span>
                                 <span class="rekom-metric-value"><?= number_format($sim->breakdown['total_followers']); ?></span>
                              </div>
                              <div class="rekom-metric-item">
                                 <span class="rekom-metric-label">Avg ER</span>
                                 <span class="rekom-metric-value"><?= number_format($sim->breakdown['avg_er'] * 100, 2); ?>%</span>
                              </div>
                              <div class="rekom-metric-item">
                                 <span class="rekom-metric-label">Avg Views</span>
                                 <span class="rekom-metric-value"><?= number_format($sim->breakdown['avg_views']); ?></span>
                              </div>
                              <div class="rekom-metric-item">
                                 <span class="rekom-metric-label">Rating</span>
                                 <span class="rekom-metric-value">
                                    <?= !empty($sim->review_summary['rating']) ? number_format($sim->review_summary['rating'], 1) . ' ★' : '-'; ?>
                                 </span>
                              </div>
                           </div>

                           <!-- Rate Card Estimate if exists -->
                           <?php if (!empty($sim->rate_card['min_rate'])): ?>
                              <div style="font-size: 11px; color: #047857; background: #ecfdf5; border-radius: 4px; padding: 4px 8px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                                 <span>Est. Rate:</span>
                                 <strong>Rp <?= number_format($sim->rate_card['min_rate'], 0, ',', '.'); ?></strong>
                              </div>
                           <?php endif; ?>
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: flex; gap: 8px; white-space: nowrap; margin-top: 12px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                           <a href="<?= site_url('administrator/kols/view/' . $sim->id); ?>" target="_blank" class="btn btn-sm btn-ghost btn-flat" style="flex: 1; border-radius: 4px; font-weight: 600; text-align: center;">
                              Profile
                           </a>
                           <button type="button" class="btn btn-sm btn-primary btn-flat btn-trigger-assign-single" 
                                   data-id="<?= $sim->id; ?>" 
                                   data-name="<?= htmlspecialchars($sim_name); ?>" 
                                   style="flex: 1.5; border-radius: 4px; font-weight: 600; background: #2563eb; border-color: #1d4ed8;">
                              Invite
                           </button>
                        </div>
                     </div>
                  </div>
               <?php endforeach; ?>
            <?php else: ?>
               <div class="col-md-12">
                  <div style="text-align: center; padding: 48px 24px; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                     <h4 style="color: #1e293b; font-weight: 700; margin-bottom: 8px; font-size: 16px;">Ready to Discover Matched Creators</h4>
                     <p style="color: #64748b; font-size: 13px; max-width: 520px; margin: 0 auto; line-height: 1.6;">
                        Select your target categories, platforms, or budget criteria above and click <strong>Generate Recommendations</strong>, or choose a reference creator in the Lookalike tab to find similar KOLs.
                     </p>
                  </div>
               </div>
            <?php endif; ?>
         </div>

         <!-- ══════════════════════════════════════════════
              MODAL: BATCH / SINGLE ASSIGN TO CAMPAIGN
              ══════════════════════════════════════════════ -->
         <div class="modal fade" id="modal-assign-campaign" tabindex="-1" role="dialog" aria-labelledby="modalAssignCampaignLabel">
            <div class="modal-dialog" role="document">
               <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
                  <form id="form-assign-campaign">
                     <input type="hidden" name="selected_kol_ids" id="assign-kol-ids" value="">
                     
                     <div class="modal-header" style="background: #1e293b; color: #fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:0.8;"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalAssignCampaignLabel" style="font-weight: 600;">
                           Invite Recommended Creators to Campaign
                        </h4>
                     </div>
                     
                     <div class="modal-body" style="padding: 20px;">
                        <div class="form-group">
                           <label style="font-weight: 600; color: #334155;">Creators to Invite (<span id="modal-kol-count">0</span>)</label>
                           <div id="modal-kol-names-preview" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px; max-height: 120px; overflow-y: auto; font-size: 12px; color: #334155;">
                              <!-- Rendered dynamic names -->
                           </div>
                        </div>

                        <div class="form-group">
                           <label style="font-weight: 600; color: #334155;">Target Campaign *</label>
                           <select class="form-control" name="campaign_id" id="modal-campaign-id" required style="border-radius: 4px;">
                              <option value="">-- Choose Active Campaign --</option>
                              <?php foreach ($meta['campaigns'] as $c): ?>
                                 <option value="<?= $c->id; ?>">
                                    <?= htmlspecialchars($c->name); ?> (<?= $c->status; ?>)
                                 </option>
                              <?php endforeach; ?>
                           </select>
                        </div>

                        <div class="form-group">
                           <label style="font-weight: 600; color: #334155;">Initial Dealing Status</label>
                           <select class="form-control" name="status" id="modal-deal-status" style="border-radius: 4px;">
                              <option value="NEGOTIATION" selected>NEGOTIATION (Drafting / Pitching)</option>
                              <option value="ASSIGNED">ASSIGNED (Deal Confirmed)</option>
                           </select>
                           <small style="color: #64748b; margin-top: 3px; display: block;">
                              Creators already assigned to this campaign will be automatically skipped to prevent duplicates.
                           </small>
                        </div>
                     </div>
                     
                     <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius: 4px;">Cancel</button>
                        <button type="submit" id="btn-submit-campaign-assignment" class="btn btn-primary btn-flat" style="border-radius: 4px; font-weight: 600; background-color: #2563eb; border-color: #1d4ed8;">
                           Confirm &amp; Invite Creators
                        </button>
                     </div>
                  </form>
               </div>
            </div>
         </div>

      </div>
   </div>
</section>

<!-- Scripts -->
<script type="text/javascript">
(function($) {
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    
    var customResults = null;
    var lookalikeResults = null;

    // Initialize Select2 dropdowns
    if ($.fn.select2) {
        $('.select2').select2({
            allowClear: true
        });
    }

    // Tab Switcher between Custom Criteria and Lookalike
    $('.rekom-tab-btn').on('click', function() {
        var target = $(this).data('target');
        $('.rekom-tab-btn').removeClass('active');
        $(this).addClass('active');

        $('#panel-criteria, #panel-lookalike').hide();
        $(target).fadeIn(150);

        // Render previous results if they exist, otherwise show empty state
        var resultsToRender = null;
        var sourceType = '';
        if (target === '#panel-criteria') {
            resultsToRender = customResults;
            sourceType = 'Custom';
        } else if (target === '#panel-lookalike') {
            resultsToRender = lookalikeResults;
            sourceType = 'Lookalike';
        }
        
        if (resultsToRender !== null) {
            renderRecommendationCards(resultsToRender, sourceType);
        } else {
            $('#rekom-results-toolbar').hide();
            $('#rekom-results-container').html(
                '<div class="col-md-12">' +
                '  <div style="text-align: center; padding: 48px 24px; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">' +
                '    <h4 style="color: #1e293b; font-weight: 700; margin-bottom: 8px; font-size: 16px;">Ready to Discover Matched Creators</h4>' +
                '    <p style="color: #64748b; font-size: 13px; max-width: 520px; margin: 0 auto; line-height: 1.6;">' +
                '      Select your target categories, platforms, or budget criteria above and click <strong>Generate Recommendations</strong>, or choose a reference creator in the Lookalike tab to find similar KOLs.' +
                '    </p>' +
                '  </div>' +
                '</div>'
            );
            $('#rekom-count-badge').text('0 creators');
            $('#check-select-all').prop('checked', false);
            updateSelectionState();
        }
    });

    // Reset Filters
    $('#btn-reset-filters').on('click', function() {
        $('#form-criteria-generator')[0].reset();
        if ($.fn.select2) {
            $('#filter-categories, #filter-platforms, #filter-tiers').val(null).trigger('change');
        }
        customResults = null;
        $('#rekom-results-toolbar').hide();
        $('#rekom-results-container').html(
            '<div class="col-md-12">' +
            '  <div style="text-align: center; padding: 48px 24px; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">' +
            '    <h4 style="color: #1e293b; font-weight: 700; margin-bottom: 8px; font-size: 16px;">Ready to Discover Matched Creators</h4>' +
            '    <p style="color: #64748b; font-size: 13px; max-width: 520px; margin: 0 auto; line-height: 1.6;">' +
            '      Select your target categories, platforms, or budget criteria above and click <strong>Generate Recommendations</strong>, or choose a reference creator in the Lookalike tab to find similar KOLs.' +
            '    </p>' +
            '  </div>' +
            '</div>'
        );
        $('#rekom-count-badge').text('0 creators');
        $('#check-select-all').prop('checked', false);
        updateSelectionState();
    });

    function updateSelectionState() {
        var checked = $('.kol-select-check:checked');
        var count = checked.length;
        $('#selected-count').text(count);
        $('#btn-batch-assign').prop('disabled', count === 0);

        $('.rekom-result-card').each(function() {
            var isChk = $(this).find('.kol-select-check').is(':checked');
            $(this).toggleClass('selected', isChk);
        });
    }

    // Checkbox selection changes
    $(document).on('change', '.kol-select-check', function() {
        updateSelectionState();
    });

    // Select All
    $('#check-select-all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.kol-select-check').prop('checked', isChecked);
        updateSelectionState();
    });

    // Render Cards Function
    function renderRecommendationCards(results, sourceType) {
        sourceType = sourceType || 'Recommendations';
        var $container = $('#rekom-results-container');
        $container.empty();

        if (!results || results.length === 0) {
            $('#rekom-results-toolbar').hide();
            $container.html(
                '<div class="col-md-12">' +
                '  <div style="text-align: center; padding: 48px 24px; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">' +
                '    <h4 style="color: #334155; font-weight: 700; margin-bottom: 8px; font-size: 16px;">No Matching Creators Found</h4>' +
                '    <p style="color: #64748b; font-size: 13px; margin: 0;">Try relaxing your filter criteria or broadening follower and view ranges.</p>' +
                '  </div>' +
                '</div>'
            );
            $('#rekom-count-badge').text('0 creators');
            $('#check-select-all').prop('checked', false);
            updateSelectionState();
            return;
        }

        $('#rekom-results-toolbar').css('display', 'flex');
        $('#rekom-results-title').html(sourceType + ' Results <span id="rekom-count-badge" class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 12px; margin-left: 6px;">' + results.length + ' creators</span>');
        $('#check-select-all').prop('checked', false);
        updateSelectionState();

        $.each(results, function(idx, sim) {
            var rank = idx + 1;
            var rankClass = (rank === 1) ? 'rekom-rank-1' : ((rank <= 3) ? 'rekom-rank-top' : 'rekom-rank-other');
            var rankLabel = (rank === 1) ? '#1 Top Pick' : ('#' + rank + ' Rank');

            var match = sim.match_percentage || 50;
            var badgeClass = (match >= 85) ? 'label-success' : ((match >= 70) ? 'label-info' : 'label-default');

            var bk = sim.breakdown || {};
            var breakdownTitle = 'Category: ' + (bk.category_match_pct || 0) + '% | Platform: ' + (bk.platform_match_pct || 0) + '% | Scale: ' + (bk.audience_match_pct || 0) + '% | Performance: ' + (bk.performance_match_pct || 0) + '%';

            var name = sim.name || 'KOL';
            var initials = name.substring(0, 1).toUpperCase();

            var avatarHtml = sim.avatar_url
                ? '<img src="' + sim.avatar_url + '" alt="' + name + '" class="rekom-avatar-img" />'
                : '<div class="rekom-avatar-initials">' + initials + '</div>';

            var catsHtml = '';
            if (sim.categories && sim.categories.length > 0) {
                catsHtml += '<div style="display: flex; gap: 4px; flex-wrap: wrap;">';
                $.each(sim.categories.slice(0, 3), function(i, c) {
                    catsHtml += '<span class="label" style="background: #f1f5f9; color: #334155; font-size: 10px; border: 1px solid #e2e8f0; border-radius: 3px;">' + c + '</span>';
                });
                if (sim.categories.length > 3) {
                    catsHtml += '<span class="label" style="background: #f8fafc; color: #94a3b8; font-size: 10px;">+' + (sim.categories.length - 3) + '</span>';
                }
                catsHtml += '</div>';
            } else {
                catsHtml = '<span style="font-size: 11px; color: #94a3b8; font-style: italic;">No category tagged</span>';
            }

            var followersFormatted = Number(bk.total_followers || 0).toLocaleString('id-ID');
            var erPercent = (Number(bk.avg_er || 0) * 100).toFixed(2);
            var viewsFormatted = Number(bk.avg_views || 0).toLocaleString('id-ID');

            var ratingStr = (sim.review_summary && sim.review_summary.rating)
                ? (Number(sim.review_summary.rating).toFixed(1) + ' ★')
                : '-';

            function fmtPrice(val) {
                if (!val) return '-';
                if (val >= 1000000) return (parseFloat(val)/1000000) + 'jt';
                return (parseFloat(val)/1000).toLocaleString('id-ID') + 'k';
            }
            var rateCardHtml = '<div style="font-size: 10px; color: #0f172a; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 6px; margin-bottom: 12px; text-align: center; font-weight: 600;">' +
                               '  Short Form: Rp ' + fmtPrice(sim.price_vt) + ' | Live: Rp ' + fmtPrice(sim.price_live) + ' <br>' +
                               '  Long Form: Rp ' + fmtPrice(sim.price_long_form) + ' | Unboxing leg.: Rp ' + fmtPrice(sim.price_unboxing) +
                               '</div>';

            var cardHtml = 
                '<div class="col-md-4 col-sm-6 col-xs-12 card-item-wrapper" style="margin-bottom: 20px;">' +
                '  <div class="rekom-result-card" data-id="' + sim.id + '" data-name="' + name + '">' +
                '    <div>' +
                '      <div class="rekom-match-score" data-toggle="tooltip" title="' + breakdownTitle + '">' +
                '        ' + match + '% Match' +
                '      </div>' +
                '      <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding-right: 80px;">' +
                '        <div style="display: flex; align-items: center; gap: 10px;">' +
                '          <input type="checkbox" class="kol-select-check" value="' + sim.id + '" style="cursor: pointer; width: 16px; height: 16px; margin: 0;">' +
                '          <div class="rekom-avatar-container">' +
                '             <span class="rekom-rank-badge-new ' + rankClass + '">#' + rank + '</span>' +
                '             ' + avatarHtml +
                '          </div>' +
                '        </div>' +
                '        <div style="flex: 1; min-width: 0;">' +
                '          <a href="' + BASE_URL + 'administrator/kols/view/' + sim.id + '" target="_blank" style="font-weight: 700; color: #1e293b; font-size: 15px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' +
                '            ' + name +
                '          </a>' +
                '          <div style="display: flex; gap: 6px; align-items: center; margin-top: 3px; flex-wrap: wrap;">' +
                '            <span class="label label-primary" style="font-size: 10px; font-weight: 600; padding: 2px 6px;">' + (sim.tier_name || 'Standard') + '</span>' +
                (sim.categories && sim.categories.length > 0 ? '            <span class="label" style="background: #f1f5f9; color: #334155; font-size: 10px; border: 1px solid #e2e8f0; border-radius: 3px;">' + sim.categories[0] + '</span>' : '') +
                '          </div>' +
                '        </div>' +
                '      </div>' +
                '      <div class="rekom-metrics-grid">' +
                '        <div class="rekom-metric-item">' +
                '          <span class="rekom-metric-label">Followers</span>' +
                '          <span class="rekom-metric-value">' + followersFormatted + '</span>' +
                '        </div>' +
                '        <div class="rekom-metric-item">' +
                '          <span class="rekom-metric-label">Avg ER</span>' +
                '          <span class="rekom-metric-value">' + erPercent + '%</span>' +
                '        </div>' +
                '        <div class="rekom-metric-item">' +
                '          <span class="rekom-metric-label">Avg Views</span>' +
                '          <span class="rekom-metric-value">' + viewsFormatted + '</span>' +
                '        </div>' +
                '        <div class="rekom-metric-item">' +
                '          <span class="rekom-metric-label">Rating</span>' +
                '          <span class="rekom-metric-value">' + ratingStr + '</span>' +
                '        </div>' +
                '      </div>' +
                rateCardHtml +
                '    </div>' +
                '    <div style="display: flex; gap: 6px; white-space: nowrap; margin-top: 10px; border-top: 1px solid #f1f5f9; padding-top: 10px;">' +
                '      <a href="' + BASE_URL + 'administrator/kols/view/' + sim.id + '" target="_blank" class="btn btn-xs btn-default btn-flat" style="flex: 1; border-radius: 3px; font-weight: 600;">' +
                '        Profile' +
                '      </a>' +
                '      <button type="button" class="btn btn-xs btn-primary btn-flat btn-trigger-assign-single" data-id="' + sim.id + '" data-name="' + name + '" style="flex: 1.3; border-radius: 3px; font-weight: 600; background: #2563eb; border-color: #1d4ed8;">' +
                '        Invite' +
                '      </button>' +
                '    </div>' +
                '  </div>' +
                '</div>';

            $container.append(cardHtml);
        });

        // Rebind tooltips
        $('[data-toggle="tooltip"]').tooltip();
    }

    // Submit Custom Criteria Generator Form
    $('#form-criteria-generator').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn-submit-criteria');
        $btn.prop('disabled', true).text('Generating...');

        var postData = {
            service_type:  $('#filter-service-type').val(),
            categories:    $('#filter-categories').val() || [],
            platforms:     $('#filter-platforms').val() || [],
            tiers:         $('#filter-tiers').val() || [],
            min_followers: $('#filter-min-followers').val(),
            max_followers: $('#filter-max-followers').val(),
            min_views:     $('#filter-min-views').val(),
            min_er:        $('#filter-min-er').val(),
            max_budget:    $('#filter-max-budget').val(),
            gender:        $('#filter-gender').val(),
            city:          $('#filter-city').val(),
            priority:      $('#filter-priority').val(),
            limit:         $('#filter-limit').val()
        };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/kols/generate_recommendations_ajax',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(res) {
                if (res.csrf_hash) {
                    csrfHash = res.csrf_hash;
                    $('#rekom-csrf-token').val(csrfHash);
                }

                if (res.success) {
                    customResults = res.results;
                    renderRecommendationCards(customResults, 'Custom');
                } else {
                    alert('Generation Failed:\n' + (res.message || 'Unknown server response.'));
                }
            },
            error: function(xhr, status, error) {
                alert('Request failed: ' + (error || status));
            },
            complete: function() {
                $btn.prop('disabled', false).text('Generate Recommendations');
            }
        });
    });

    // Submit Lookalike Generator Form
    $('#form-lookalike-generator').on('submit', function(e) {
        e.preventDefault();
        var kolId = $('#lookalike-baseline-kol').val();

        if (!kolId) {
            alert('Please select a reference KOL.');
            return;
        }

        var $btn = $('#btn-submit-lookalike');
        $btn.prop('disabled', true).text('Finding Lookalikes...');

        var postData = {
            kol_id: kolId,
            limit: 6
        };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/kols/get_similar_kols_ajax',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(res) {
                if (res.csrf_hash) {
                    csrfHash = res.csrf_hash;
                    $('#rekom-csrf-token').val(csrfHash);
                }

                if (res.success) {
                    lookalikeResults = res.results;
                    renderRecommendationCards(lookalikeResults, 'Lookalike');
                } else {
                    alert('Lookalike Search Failed:\n' + (res.message || 'Unknown server response.'));
                }
            },
            error: function(xhr, status, error) {
                alert('Request failed: ' + (error || status));
            },
            complete: function() {
                $btn.prop('disabled', false).text('Find Lookalikes');
            }
        });
    });

    // Trigger Single Invite Modal
    $(document).on('click', '.btn-trigger-assign-single', function() {
        var kid = $(this).data('id');
        var name = $(this).data('name');

        $('#assign-kol-ids').val(kid);
        $('#modal-kol-count').text('1');
        $('#modal-kol-names-preview').html('<strong>' + name + '</strong>');
        $('#modal-campaign-id').val('');
        $('#modal-assign-campaign').modal('show');
    });

    // Trigger Batch Invite Modal
    $('#btn-batch-assign').on('click', function() {
        var checked = $('.kol-select-check:checked');
        if (checked.length === 0) return;

        var ids = [];
        var names = [];
        checked.each(function() {
            ids.push($(this).val());
            var $card = $(this).closest('.rekom-result-card');
            names.push($card.data('name'));
        });

        $('#assign-kol-ids').val(ids.join(','));
        $('#modal-kol-count').text(ids.length);
        $('#modal-kol-names-preview').html(names.join(', '));
        $('#modal-campaign-id').val('');
        $('#modal-assign-campaign').modal('show');
    });

    // Submit Assign to Campaign Form
    $('#form-assign-campaign').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn-submit-campaign-assignment');
        var campaignId = $('#modal-campaign-id').val();
        var rawIds = $('#assign-kol-ids').val();
        var status = $('#modal-deal-status').val();

        if (!campaignId || !rawIds) {
            alert('Please select a campaign.');
            return;
        }

        var ids = rawIds.split(',').map(function(id) { return parseInt(id.trim(), 10); });

        $btn.prop('disabled', true).text('Assigning...');

        var postData = {
            campaign_id: campaignId,
            kol_ids: ids,
            status: status
        };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/kols/assign_to_campaign_ajax',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(res) {
                if (res.csrf_hash) {
                    csrfHash = res.csrf_hash;
                    $('#rekom-csrf-token').val(csrfHash);
                }

                if (res.success) {
                    alert(res.message);
                    $('#modal-assign-campaign').modal('hide');
                    $('.kol-select-check').prop('checked', false);
                    $('#check-select-all').prop('checked', false);
                    updateSelectionState();
                } else {
                    alert('Assignment Failed:\n' + (res.message || 'Unknown server response.'));
                }
            },
            error: function(xhr, status, error) {
                alert('Request failed: ' + (error || status));
            },
            complete: function() {
                $btn.prop('disabled', false).text('Confirm & Invite Creators');
            }
        });
    });

    // Export Results to CSV
    $('#btn-export-results').on('click', function() {
        var rows = [
            ['ID', 'Name', 'Match Score', 'Tier', 'City', 'Followers', 'Avg ER', 'Avg Views', 'Rating']
        ];

        $('.rekom-result-card').each(function() {
            var $c = $(this);
            var id = $c.data('id');
            var name = '"' + ($c.data('name') || '').replace(/"/g, '""') + '"';
            var match = $c.find('.label-success, .label-info, .label-default').first().text().replace('% Match', '').trim();
            var tier = '"' + $c.find('.label-primary').first().text().trim() + '"';
            var city = '"' + ($c.find('.fa-map-marker').parent().text() || '').trim() + '"';
            
            var vals = [];
            $c.find('.rekom-metric-value').each(function() {
                vals.push($(this).text().trim().replace(/\./g, ''));
            });

            var followers = vals[0] || '0';
            var er = vals[1] || '0%';
            var views = vals[2] || '0';
            var rating = vals[3] || '-';

            rows.push([id, name, match, tier, city, followers, er, views, rating]);
        });

        if (rows.length <= 1) {
            alert('No recommendations to export.');
            return;
        }

        var csvContent = "data:text/csv;charset=utf-8," + rows.map(e => e.join(",")).join("\n");
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "kol_recommendations_" + new Date().toISOString().slice(0,10) + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

})(jQuery);
</script>






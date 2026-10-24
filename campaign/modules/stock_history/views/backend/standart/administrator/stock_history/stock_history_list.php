
<section class="content-header">
   <h1>
      <?= cclang('stock_history') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/products'); ?>">Inventory</a></li>
      <li class="active"><?= cclang('stock_history') ?></li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
            <div class="box-body" style="padding: 20px;">
               <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid #e7edf3; margin-bottom: 20px;">
                  <div>
                     <h3 style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;"><?= cclang('stock_history') ?></h3>
                     <div style="margin-top: 5px; color: #6b778c; font-size: 13px;"><?= cclang('list_all', [cclang('stock_history')]); ?> <span style="display: inline-block; margin-left: 6px; padding: 2px 7px; border-radius: 10px; background: #e8f1fb; color: #1f4b6e; font-weight: 700;"><?= $stock_history_counts; ?> <?= cclang('items'); ?></span></div>
                  </div>
               </div>

               <form name="form_stock_history" id="form_stock_history" action="<?= base_url('administrator/stock_history/index'); ?>">

               <style>
                  .prod-filter-bar { margin-bottom: 14px; padding: 12px 12px 4px 12px; background: #f8fafc; border: 1px solid #e7edf3; border-radius: 6px; }
                  .prod-filter-bar .form-control,
                  .prod-filter-bar input[type="text"],
                  .prod-filter-bar select {
                     height: 38px !important;
                     min-height: 38px !important;
                     max-height: 38px !important;
                     line-height: 36px !important;
                     border-radius: 4px !important;
                     margin-bottom: 8px !important;
                     border: 1px solid #d2d6de !important;
                     box-sizing: border-box !important;
                     font-size: 13px !important;
                     padding: 0 10px !important;
                     background-color: #ffffff !important;
                  }
                  .prod-filter-bar .chosen-container {
                     width: 100% !important;
                     margin-bottom: 8px !important;
                     box-sizing: border-box !important;
                     font-size: 13px !important;
                  }
                  .prod-filter-bar .chosen-container-single .chosen-single {
                     height: 38px !important;
                     min-height: 38px !important;
                     max-height: 38px !important;
                     line-height: 36px !important;
                     border-radius: 4px !important;
                     border: 1px solid #d2d6de !important;
                     background: #ffffff !important;
                     box-shadow: none !important;
                     color: #333333 !important;
                     padding: 0 10px !important;
                     box-sizing: border-box !important;
                     display: block !important;
                  }
                  .prod-filter-bar .chosen-container-single .chosen-single span {
                     line-height: 36px !important;
                     margin-right: 20px !important;
                  }
                  .prod-filter-bar .chosen-container-single .chosen-single div {
                     top: 50% !important;
                     transform: translateY(-50%) !important;
                     right: 8px !important;
                     width: 18px !important;
                     height: 18px !important;
                     display: flex !important;
                     align-items: center !important;
                     justify-content: center !important;
                  }
                  .prod-filter-bar .chosen-container-single .chosen-single div b {
                     background-position: 0 2px !important;
                  }
                  .prod-filter-bar .chosen-container-active.chosen-with-drop .chosen-single div b {
                     background-position: -18px 2px !important;
                  }
                  .prod-filter-bar .btn {
                     width: 100% !important;
                     height: 38px !important;
                     min-height: 38px !important;
                     max-height: 38px !important;
                     line-height: 36px !important;
                     margin-bottom: 8px !important;
                     border-radius: 4px !important;
                     font-weight: 600 !important;
                     font-size: 13px !important;
                     padding: 0 12px !important;
                     box-sizing: border-box !important;
                     display: inline-flex !important;
                     align-items: center !important;
                     justify-content: center !important;
                     gap: 6px !important;
                  }
                  @media (max-width: 768px) {
                    #form_stock_history .prod-filter-bar > .pf-half { width: calc(50% - 3px) !important; max-width: calc(50% - 3px) !important; float: left !important; display: block !important; }
                    #form_stock_history .prod-filter-bar > .pf-half + .pf-half { margin-left: 6px !important; }
                    #form_stock_history button[name="sbtn"] { width: 100% !important; min-width: 0 !important; max-width: none !important; font-size: 13px !important; }
                    #form_stock_history button[name="sbtn"]::before { content: none !important; }
                    #form_stock_history a[name="reset"] { width: 100% !important; min-width: 0 !important; max-width: none !important; }
                    #form_stock_history a[name="reset"] .reset-txt { display: none !important; }
                  }
               </style>

               <div class="row prod-filter-bar" style="margin-left:0; margin-right:0;">
                  <div class="col-xs-12 col-sm-5" style="padding-left:4px; padding-right:4px;">
                     <input type="text" class="form-control" name="q" id="filter" placeholder="Cari nama produk / SKU / catatan..." value="<?= $this->input->get('q'); ?>">
                  </div>
                  <div class="col-xs-12 col-sm-3" style="padding-left:4px; padding-right:4px;">
                     <select class="form-control chosen chosen-select" name="type" id="type" data-placeholder="Semua Tipe">
                        <option value="">Semua Tipe Mutasi</option>
                        <option <?= $this->input->get('type') == 'in' ? 'selected' : ''; ?> value="in">Barang Masuk (In)</option>
                        <option <?= $this->input->get('type') == 'out' ? 'selected' : ''; ?> value="out">Barang Keluar (Out)</option>
                     </select>
                  </div>
                  <div class="col-xs-6 col-sm-2 pf-half" style="padding-left:4px; padding-right:4px;">
                     <button type="submit" class="btn btn-primary btn-flat" name="sbtn" id="sbtn" value="Apply" title="Filter" style="font-weight:600;"><i class="fa fa-search"></i> Filter</button>
                  </div>
                  <div class="col-xs-6 col-sm-2 pf-half" style="padding-left:4px; padding-right:4px;">
                     <a class="btn btn-default btn-flat" name="reset" id="reset" href="<?= base_url('administrator/stock_history'); ?>" title="Reset Filter" style="font-weight:600;"><i class="fa fa-undo"></i><span class="reset-txt"> Reset</span></a>
                  </div>
               </div>

                  <div class="table-responsive">
                     <table class="table table-hover" style="margin-bottom: 0; border: 1px solid #e7edf3; border-radius: 6px;">
                        <thead>
                           <tr style="background: #f6f8fb; color: #52606d; font-size: 11px; text-transform: uppercase; letter-spacing: 0.02em;">
                              <th style="border-bottom: 1px solid #e7edf3;">Date</th>
                              <th style="border-bottom: 1px solid #e7edf3;">Product</th>
                              <th style="border-bottom: 1px solid #e7edf3;">Type</th>
                              <th style="border-bottom: 1px solid #e7edf3;">Qty Change</th>
                              <th style="border-bottom: 1px solid #e7edf3;">Before</th>
                              <th style="border-bottom: 1px solid #e7edf3;">After</th>
                              <th style="border-bottom: 1px solid #e7edf3;">Notes</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php foreach ($stock_historys as $sh): ?>
                           <tr>
                              <td style="white-space: nowrap;"><?= date('d M Y H:i', strtotime($sh->created_at)); ?></td>
                              <td>
                                 <strong><?= _ent($sh->product_name); ?></strong>
                                 <br><small class="text-muted"><?= _ent($sh->product_sku); ?></small>
                              </td>
                              <td>
                                 <?php
                                 $type_styles = [
                                    'out' => ['label' => 'Out', 'bg' => '#fdecec', 'color' => '#b42318'],
                                    'in'  => ['label' => 'In', 'bg' => '#e6f6ec', 'color' => '#16803c']
                                 ];
                                 $t = $type_styles[$sh->change_type] ?? ['label' => $sh->change_type, 'bg' => '#eef1f5', 'color' => '#6b778c'];
                                 ?>
                                 <span style="display:inline-block; padding: 3px 9px; border-radius: 4px; background: <?= $t['bg']; ?>; color: <?= $t['color']; ?>; font-weight: 600; font-size: 12px; white-space: nowrap;"><?= strtoupper($t['label']); ?></span>
                              </td>
                              <td style="font-weight: 700; color: <?= $sh->qty_change > 0 ? '#00a65a' : '#dd4b39'; ?>;">
                                 <?= $sh->qty_change > 0 ? '+' : ''; ?><?= $sh->qty_change; ?>
                              </td>
                              <td><?= $sh->stock_before; ?></td>
                              <td><?= $sh->stock_after; ?></td>
                              <td style="font-size: 12px; color: #666;"><?= _ent($sh->notes); ?></td>
                           </tr>
                           <?php endforeach; ?>
                           <?php if ($stock_history_counts == 0): ?>
                           <tr>
                              <td colspan="7" class="text-center text-muted">Data history stock belum tersedia.</td>
                           </tr>
                           <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
                  <div class="row" style="margin-top: 16px;">
                     <div class="col-md-12 text-right">
                        <?= $pagination; ?>
                     </div>
                  </div>

               </form>
            </div>
         </div>
      </div>
   </div>
</section>

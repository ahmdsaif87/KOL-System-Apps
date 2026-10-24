<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Tagihan Saya <small>Daftar invoice honorarium kerja sama KOL yang diajukan ke Brand</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Tagihan Saya</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <?php if (!empty($unlinked_kol)): ?>
      <div class="callout callout-warning">
         <h4>Akun belum terhubung ke data KOL</h4>
         <p>Hubungi admin agar akun Anda dihubungkan ke profil KOL untuk melihat dan menerbitkan tagihan.</p>
      </div>
   <?php else: ?>

   <?php
      $total_all_invoices = count($invoices);
      $total_nominal = 0;
      $count_submitted = 0;
      $count_approved = 0;
      $count_paid = 0;
      $total_paid_all = 0;

      foreach ($invoices as $inv) {
         $total_nominal += (float)$inv->grand_total;
         if ($inv->status == 'SUBMITTED') $count_submitted++;
         elseif (in_array($inv->status, ['APPROVED', 'PARTIAL'])) $count_approved++;
         elseif ($inv->status == 'PAID') {
            $count_paid++;
            $total_paid_all += (float)$inv->grand_total;
         }
      }
   ?>

   <!-- Summary KPI Cards -->
   <div class="row">
      <div class="col-md-3 col-sm-6 col-xs-12">
         <div class="info-box" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <span class="info-box-icon bg-aqua" style="border-radius: 6px 0 0 6px;"><i class="fa fa-file-text-o"></i></span>
            <div class="info-box-content">
               <span class="info-box-text">Total Tagihan</span>
               <span class="info-box-number" style="font-size: 16px;"><?= $total_all_invoices; ?> Invoice</span>
               <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                  Rp <?= number_format($total_nominal, 0, ',', '.'); ?>
               </div>
            </div>
         </div>
      </div>

      <div class="col-md-3 col-sm-6 col-xs-12">
         <div class="info-box" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <span class="info-box-icon bg-yellow" style="border-radius: 6px 0 0 6px;"><i class="fa fa-clock-o"></i></span>
            <div class="info-box-content">
               <span class="info-box-text">Review Pajak Brand</span>
               <span class="info-box-number" style="font-size: 16px;"><?= $count_submitted; ?> Invoice</span>
               <div style="font-size: 11px; color: #b45309; margin-top: 2px;">
                  Menunggu approval
               </div>
            </div>
         </div>
      </div>

      <div class="col-md-3 col-sm-6 col-xs-12">
         <div class="info-box" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <span class="info-box-icon bg-blue" style="border-radius: 6px 0 0 6px;"><i class="fa fa-check-circle-o"></i></span>
            <div class="info-box-content">
               <span class="info-box-text">Siap Dibayar</span>
               <span class="info-box-number" style="font-size: 16px;"><?= $count_approved; ?> Invoice</span>
               <div style="font-size: 11px; color: #0284c7; margin-top: 2px;">
                  Pajak terverifikasi
               </div>
            </div>
         </div>
      </div>

      <div class="col-md-3 col-sm-6 col-xs-12">
         <div class="info-box" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <span class="info-box-icon bg-green" style="border-radius: 6px 0 0 6px;"><i class="fa fa-check"></i></span>
            <div class="info-box-content">
               <span class="info-box-text">Lunas Terbayar</span>
               <span class="info-box-number" style="font-size: 16px;"><?= $count_paid; ?> Invoice</span>
               <div style="font-size: 11px; color: #16a34a; margin-top: 2px;">
                  Rp <?= number_format($total_paid_all, 0, ',', '.'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="box-header with-border" style="display: flex; justify-content: space-between; align-items: center;">
               <h3 class="box-title" style="font-weight: 600; font-size: 15px;">
                  <i class="fa fa-money" style="color: #0284c7;"></i> Daftar Tagihan &amp; Invoice Honorarium
               </h3>
               <div>
                  <a href="<?= site_url('administrator/my_contracts'); ?>" class="btn btn-sm btn-default btn-flat" style="border-radius: 4px; font-weight: 600;">
                     <i class="fa fa-file-text-o"></i> Lihat Kontrak Saya
                  </a>
               </div>
            </div>
            
            <div class="box-body">
               <?php if (!empty($invoices)): ?>
               <div class="table-responsive">
                  <table class="table table-bordered table-striped" style="font-size: 12.5px;">
                     <thead>
                        <tr style="background: #f8fafc;">
                           <th style="width: 35px; text-align: center;">#</th>
                           <th>No. Invoice &amp; Tanggal</th>
                           <th>Campaign &amp; Brand</th>
                           <th>No. MoU / Kontrak</th>
                           <th>Rincian Honorarium</th>
                           <th>Status Tagihan</th>
                           <th>Bukti Potong Pajak</th>
                           <th style="width: 130px; text-align: center;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $no = 1; foreach ($invoices as $inv): ?>
                        <?php
                           $grand_total = (float)$inv->grand_total;
                           $total_paid = (float)$inv->total_paid;
                           $pct_paid = $grand_total > 0 ? min(100, round(($total_paid / $grand_total) * 100)) : 0;
                        ?>
                        <tr>
                           <td style="text-align: center;"><?= $no++; ?></td>
                           
                           <!-- NO INVOICE & TANGGAL -->
                           <td>
                              <div style="font-family: monospace; font-weight: 700; color: #0f172a; font-size: 13px;">
                                 <?= _ent($inv->invoice_no); ?>
                              </div>
                              <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                 Terbit: <?= !empty($inv->created_at) ? date('d M Y', strtotime($inv->created_at)) : '-'; ?>
                              </div>
                              <?php if (!empty($inv->due_date)): ?>
                                 <div style="font-size: 10.5px; color: #94a3b8;">
                                    Jatuh Tempo: <?= date('d M Y', strtotime($inv->due_date)); ?>
                                 </div>
                              <?php endif; ?>
                           </td>

                           <!-- CAMPAIGN & BRAND -->
                           <td>
                              <b style="color: #1e293b;"><?= _ent($inv->campaign_name ?: '-'); ?></b>
                              <?php if (!empty($inv->brand_name)): ?>
                                 <div style="font-size: 11px; color: #64748b;">Brand: <b><?= _ent($inv->brand_name); ?></b></div>
                              <?php endif; ?>
                           </td>

                           <!-- NO MOU / KONTRAK -->
                           <td>
                              <?php if (!empty($inv->contract_no)): ?>
                                 <div style="font-family: monospace; font-weight: 600; color: #0284c7;">
                                    <?= _ent($inv->contract_no); ?>
                                 </div>
                              <?php else: ?>
                                 <span class="text-muted">-</span>
                              <?php endif; ?>
                           </td>

                           <!-- RINCIAN HONORARIUM -->
                           <td>
                              <div style="font-size: 11px; color: #475569;">
                                 Nilai Deal: <strong>Rp <?= number_format((float)$inv->subtotal, 0, ',', '.'); ?></strong>
                              </div>
                              <?php if ((float)$inv->tax_amount > 0): ?>
                                 <div style="font-size: 10.5px; color: #dc2626;">
                                    Pot. <?= _ent($inv->tax_type ?: 'PPh 21'); ?> (<?= (float)$inv->tax_rate_percent; ?>%): -Rp <?= number_format((float)$inv->tax_amount, 0, ',', '.'); ?>
                                 </div>
                              <?php endif; ?>
                              <div style="font-size: 12px; font-weight: 700; color: #16a34a; margin-top: 3px;">
                                 Net Diterima: Rp <?= number_format($grand_total, 0, ',', '.'); ?>
                              </div>
                           </td>

                           <!-- STATUS TAGIHAN -->
                           <td>
                              <?php if ($inv->status == 'PAID'): ?>
                                 <span class="label label-success" style="font-size: 11px; padding: 4px 8px;"><i class="fa fa-check"></i> LUNAS</span>
                              <?php elseif ($inv->status == 'PARTIAL'): ?>
                                 <span class="label label-primary" style="font-size: 11px; padding: 4px 8px;"><i class="fa fa-adjust"></i> SEBAGIAN (<?= $pct_paid; ?>%)</span>
                              <?php elseif ($inv->status == 'APPROVED'): ?>
                                 <span class="label label-info" style="font-size: 11px; padding: 4px 8px;"><i class="fa fa-check-circle-o"></i> SIAP BAYAR</span>
                              <?php elseif ($inv->status == 'SUBMITTED'): ?>
                                 <span class="label label-warning" style="background-color: #f59e0b; font-size: 11px; padding: 4px 8px;" title="Invoice telah diajukan, menunggu review & verifikasi pajak Brand"><i class="fa fa-clock-o"></i> REVIEW PAJAK BRAND</span>
                              <?php elseif ($inv->status == 'REJECTED'): ?>
                                 <span class="label label-danger" style="font-size: 11px; padding: 4px 8px;"><i class="fa fa-times-circle"></i> DITOLAK</span>
                              <?php else: ?>
                                 <span class="label label-default" style="font-size: 11px; padding: 4px 8px;"><?= _ent($inv->status); ?></span>
                              <?php endif; ?>

                              <?php if ($total_paid > 0): ?>
                                 <div style="font-size: 11px; color: #475569; margin-top: 4px;">
                                    Terbayar: <b>Rp <?= number_format($total_paid, 0, ',', '.'); ?></b>
                                 </div>
                              <?php endif; ?>
                           </td>

                           <!-- BUKTI POTONG PAJAK -->
                           <td>
                              <?php if (!empty($inv->bupot_file)): ?>
                                 <a href="<?= base_url($inv->bupot_file); ?>" target="_blank" class="btn btn-xs btn-warning btn-flat" style="border-radius: 4px; font-weight: 600; padding: 4px 8px;" title="Unduh Bukti Potong Pajak Resmi">
                                    <i class="fa fa-certificate"></i> Unduh Bupot
                                 </a>
                                 <?php if (!empty($inv->bupot_no)): ?>
                                    <div style="font-size: 10px; font-family: monospace; color: #64748b; margin-top: 2px;">
                                       <?= _ent($inv->bupot_no); ?>
                                    </div>
                                 <?php endif; ?>
                              <?php else: ?>
                                 <span class="text-muted" style="font-size: 11px;">Belum diterbitkan</span>
                              <?php endif; ?>
                           </td>

                           <!-- AKSI -->
                           <td style="text-align: center; white-space: nowrap;">
                              <a href="<?= site_url('administrator/invoices/print_pdf/' . $inv->id); ?>" target="_blank" class="btn btn-xs btn-primary btn-flat" style="border-radius: 3px; font-weight: 600; background-color: #0284c7; border-color: #0284c7;" title="Cetak / Unduh PDF Invoice">
                                 <i class="fa fa-print"></i> PDF Tagihan
                              </a>
                              
                              <?php if (!empty($inv->contract_id)): ?>
                              <a href="<?= site_url('administrator/campaign_kol_contracts/download_pdf/' . $inv->contract_id); ?>" target="_blank" class="btn btn-xs btn-default btn-flat" style="border-radius: 3px; font-weight: 600; margin-left: 2px;" title="Lihat Dokumen MoU">
                                 <i class="fa fa-file-pdf-o"></i> MoU
                              </a>
                              <?php endif; ?>
                           </td>
                        </tr>
                        <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>
               <?php else: ?>
                  <div class="text-center" style="padding: 40px; color: #94a3b8;">
                     <i class="fa fa-file-text-o" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                     <p style="font-size: 14px; margin-bottom: 15px;">Belum ada tagihan atau invoice yang diajukan.</p>
                     <p style="font-size: 12px; color: #64748b;">
                        Invoice diterbitkan melalui menu <a href="<?= site_url('administrator/my_contracts'); ?>"><b>Kontrak Saya</b></a> setelah Surat Perjanjian Kerja Sama (MoU) ditandatangani.
                     </p>
                  </div>
               <?php endif; ?>
            </div>
         </div>
      </div>
   </div>
   <?php endif; ?>
</section>

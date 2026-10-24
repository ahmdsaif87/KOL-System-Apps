<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Kontrak Saya <small>Daftar kontrak kerja sama &amp; Surat Perjanjian (MoU)</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Kontrak Saya</li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <?php if (!empty($unlinked_kol)): ?>
      <div class="callout callout-warning">
         <h4>Akun belum terhubung ke data KOL</h4>
         <p>Hubungi admin agar akun Anda dihubungkan ke profil KOL untuk melihat kontrak.</p>
      </div>
   <?php else: ?>
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title">Daftar Kontrak &amp; MoU (<?= count($contracts); ?>)</h3>
            </div>
            <div class="box-body">
               <?php if (!empty($contracts)): ?>
               <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th style="width: 40px;">#</th>
                           <th>No. Kontrak</th>
                           <th>Campaign &amp; Brand</th>
                           <th>Nominal Imbalan</th>
                           <th>Status MoU</th>
                           <th>Tagihan / Invoice</th>
                           <th>Tanggal Dibuat</th>
                           <th style="width: 200px; text-align: center;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $no = 1; foreach ($contracts as $c): ?>
                        <tr>
                           <td><?= $no++; ?></td>
                           <td style="font-family: monospace; font-weight: 600;"><?= _ent($c->contract_no); ?></td>
                           <td>
                              <b><?= _ent($c->campaign_name ?: '-'); ?></b>
                              <?php if (!empty($c->brand_name)): ?>
                                 <div style="font-size: 11px; color: #64748b;">Brand: <?= _ent($c->brand_name); ?></div>
                              <?php endif; ?>
                           </td>
                           <td style="font-weight: 600; color: #16a34a;">Rp <?= number_format((float)($c->deal_amount ?: 0), 0, ',', '.'); ?></td>
                           <td>
                              <?php if (in_array($c->status, ['SIGNED', 'ACTIVE'])): ?>
                                 <span class="label label-success"><i class="fa fa-check-circle"></i> AKTIF (TTD Lengkap)</span>
                              <?php elseif (in_array($c->status, ['PENDING', 'WAITING_KOL_SIGNATURE'])): ?>
                                 <span class="label label-warning" style="background-color: #f59e0b;"><i class="fa fa-clock-o"></i> MENUNGGU TTD KOL</span>
                              <?php else: ?>
                                 <span class="label label-default"><?= _ent($c->status); ?></span>
                              <?php endif; ?>
                           </td>
                           <!-- STATUS TAGIHAN / INVOICE -->
                           <td>
                              <?php if (!empty($c->invoice_id)): ?>
                                 <?php if ($c->invoice_status == 'PAID'): ?>
                                    <span class="label label-success"><i class="fa fa-check"></i> LUNAS</span>
                                 <?php elseif ($c->invoice_status == 'APPROVED'): ?>
                                    <span class="label label-info"><i class="fa fa-check-circle-o"></i> SIAP BAYAR</span>
                                 <?php elseif ($c->invoice_status == 'SUBMITTED'): ?>
                                    <span class="label label-warning"><i class="fa fa-clock-o"></i> REVIEW BRAND</span>
                                 <?php elseif ($c->invoice_status == 'REJECTED'): ?>
                                    <span class="label label-danger"><i class="fa fa-times-circle"></i> DITOLAK</span>
                                 <?php else: ?>
                                    <span class="label label-default"><?= _ent($c->invoice_status); ?></span>
                                 <?php endif; ?>
                                 <div style="font-size: 11px; font-family: monospace; color: #334155; margin-top: 2px;">
                                    <?= _ent($c->invoice_no); ?>
                                 </div>
                                 <a href="<?= site_url('administrator/invoices/print_pdf/' . $c->invoice_id); ?>" target="_blank" class="btn btn-xs btn-default btn-flat" style="border-radius: 3px; margin-top: 3px; font-size: 10.5px;" title="Cetak / Unduh PDF Invoice">
                                    <i class="fa fa-print"></i> PDF Tagihan
                                 </a>
                              <?php elseif (in_array($c->status, ['SIGNED', 'ACTIVE'])): ?>
                                 <button type="button" class="btn btn-xs btn-success btn-flat btn-kol-issue-invoice" 
                                         data-contract-id="<?= $c->id; ?>" 
                                         data-contract-no="<?= _ent($c->contract_no); ?>" 
                                         data-deal-amount="<?= number_format((float)$c->deal_amount, 0, ',', '.'); ?>"
                                         style="border-radius: 3px; font-weight: 600;" 
                                         title="Terbitkan dan ajukan invoice awal honorarium ke Brand">
                                    <i class="fa fa-paper-plane"></i> Terbitkan Invoice
                                 </button>
                              <?php else: ?>
                                 <span class="text-muted" style="font-size: 11px;">Menunggu TTD MoU</span>
                              <?php endif; ?>
                           </td>
                           <td><?= !empty($c->created_at) ? date('d M Y', strtotime($c->created_at)) : '-'; ?></td>
                           <td style="text-align: center; white-space: nowrap;">
                              <a href="<?= site_url('administrator/campaign_kol_contracts/view/' . $c->id . '?popup=show'); ?>" class="btn btn-xs btn-info btn-flat popup-view" title="Lihat Detail Kontrak" style="border-radius: 3px; font-weight: 600;">
                                 <i class="fa fa-eye"></i> Detail
                              </a>
                              
                              <?php if (in_array($c->status, ['PENDING', 'WAITING_KOL_SIGNATURE'])): ?>
                              <button type="button" class="btn btn-xs btn-primary btn-flat btn-kol-open-sign" 
                                      data-id="<?= $c->id; ?>" 
                                      data-deal-amount="<?= number_format((float)$c->deal_amount, 0, ',', '.'); ?>" 
                                      style="border-radius: 3px; font-weight: 600; background-color: #0284c7; border-color: #0284c7;" 
                                      title="Review &amp; Tanda Tangan Surat Perjanjian">
                                 <i class="fa fa-pencil"></i> TTD MoU
                              </button>
                              <?php endif; ?>

                              <?php if (!empty($c->contract_file)): ?>
                              <a href="<?= site_url('administrator/campaign_kol_contracts/download_pdf/' . $c->id); ?>" target="_blank" class="btn btn-xs btn-default btn-flat" title="Lihat Dokumen PDF" style="border-radius: 3px; font-weight: 600;">
                                 <i class="fa fa-file-pdf-o"></i> PDF
                              </a>
                              <?php endif; ?>
                           </td>
                        </tr>
                        <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>
               <?php else: ?>
                  <div class="text-center" style="padding: 30px; color: #94a3b8;">
                     <i class="fa fa-file-text-o" style="font-size: 36px;"></i>
                     <p style="margin-top: 10px;">Belum ada kontrak untuk akun Anda.</p>
                  </div>
               <?php endif; ?>
            </div>
         </div>
      </div>
   </div>
   <?php endif; ?>
</section>

<!-- Modal Review & TTD MoU untuk KOL -->
<div class="modal fade" id="modal_kol_sign_mou" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document" style="width: 95%; max-width: 1150px;">
    <div class="modal-content" style="border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 8px 8px 0 0; padding: 15px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 24px;"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" style="font-weight: 700; color: #1e293b; font-size: 16px;">
           <i class="fa fa-file-text-o" style="color: #0284c7;"></i> Review &amp; Tanda Tangan Surat Perjanjian Kerja Sama (MoU)
        </h4>
      </div>
      
      <div class="modal-body" style="background: #f1f5f9; padding: 20px;">
        <input type="hidden" id="kol_sign_contract_id" value="">
        
        <div class="row">
          <!-- Left: A4 Contract Document Preview (with Brand S&K and Signatures) -->
          <div class="col-md-7">
            <div style="background: #334155; padding: 20px 15px; border-radius: 8px; max-height: 640px; overflow-y: auto; display: flex; justify-content: center; align-items: flex-start; box-shadow: inset 0 2px 6px rgba(0,0,0,0.4);">
               <div id="kol_contract_preview" style="background: #ffffff; width: 100%; max-width: 100%; height: auto; min-height: auto; padding: 30px 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); border-radius: 4px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; box-sizing: border-box; color: #1e293b; font-size: 11.5px; line-height: 1.5; word-wrap: break-word; overflow-wrap: break-word;">
                  <div style="text-align: center; color: #64748b; padding: 60px 20px;">
                     <i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Memuat naskah Surat Perjanjian...
                  </div>
               </div>
            </div>
          </div>
          
          <!-- Right: Signature Pad for KOL & Invoice Option -->
          <div class="col-md-5">
            <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
               <label style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 6px; display: block;">
                  <i class="fa fa-paint-brush" style="color: #0284c7;"></i> Tanda Tangan Digital (E-Sign KOL):
               </label>
               <p style="font-size: 11.5px; color: #64748b; margin-bottom: 12px;">Goreskan tanda tangan Anda pada area kanvas di bawah ini:</p>
               
               <div style="text-align: center; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 10px;">
                  <canvas id="kol-signature-pad" width="320" height="150" style="border: 1px solid #cbd5e1; border-radius: 4px; cursor: crosshair; background: #fff; max-width: 100%; touch-action: none;"></canvas>
                  <div style="margin-top: 8px; text-align: right;">
                     <button type="button" class="btn btn-default btn-xs" id="kol-clear-signature" style="border-radius: 4px;">
                        <i class="fa fa-eraser"></i> Hapus / Clear
                     </button>
                  </div>
               </div>
               
               <div style="margin-top: 15px;">
                  <label style="font-size: 11.5px; font-weight: normal; color: #334155; cursor: pointer; display: flex; align-items: flex-start; gap: 8px;">
                     <input type="checkbox" id="kol_agreement_check" style="margin-top: 2px;">
                     <span>Saya telah membaca, memahami, dan menyetujui seluruh klausul Surat Perjanjian (MoU) serta Syarat &amp; Ketentuan Brand di samping.</span>
                  </label>
               </div>

               <!-- Feature 4: Opsi terbitkan invoice awal langsung oleh KOL -->
               <div style="margin-top: 12px; padding: 10px 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px;">
                  <label style="font-size: 12px; font-weight: 600; color: #166534; cursor: pointer; display: flex; align-items: center; gap: 8px; margin: 0;">
                     <input type="checkbox" id="kol_issue_invoice_check" checked style="margin: 0; width: 16px; height: 16px;">
                     <span>Sekaligus terbitkan &amp; ajukan Invoice ke Brand</span>
                  </label>
                  <div style="font-size: 11px; color: #15803d; margin-left: 24px; margin-top: 2px;">
                     Invoice honorarium (<b id="kol_modal_deal_amount_label">Rp 0</b>) akan otomatis diajukan atas nama Anda ke Brand untuk verifikasi pajak.
                  </div>
               </div>

               <button type="button" class="btn btn-primary btn-block btn-flat" id="btn_submit_kol_sign" style="margin-top: 18px; font-weight: 600; border-radius: 6px; padding: 12px; font-size: 13.5px; background-color: #0284c7; border-color: #0284c7;">
                  <i class="fa fa-check-circle"></i> Tanda Tangani &amp; Setujui MoU
               </button>
            </div>
            
            <div style="margin-top: 15px; padding: 12px 15px; background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 6px; font-size: 11.5px; color: #0369a1;">
               <i class="fa fa-shield"></i> Dokumen perjanjian ini memiliki kekuatan hukum yang sah dan mengikat para pihak sejak ditandatangani secara elektronik.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var kolCanvas, kolCtx, isKolDrawing = false, kolPos = { x: 0, y: 0 };

function initKolSignPad() {
    if (kolCanvas) return;
    kolCanvas = document.getElementById('kol-signature-pad');
    if (!kolCanvas) return;
    kolCtx = kolCanvas.getContext('2d');
    
    function setPos(e) {
        var rect = kolCanvas.getBoundingClientRect();
        var clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
        var clientY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : 0);
        kolPos.x = clientX - rect.left;
        kolPos.y = clientY - rect.top;
    }
    
    function start(e) {
        if(e.type === 'mousedown' || e.type === 'touchstart') {
            setPos(e);
            isKolDrawing = true;
        }
    }
    
    function draw(e) {
        if (!isKolDrawing) return;
        e.preventDefault();
        kolCtx.beginPath();
        kolCtx.lineWidth = 2;
        kolCtx.lineCap = 'round';
        kolCtx.strokeStyle = '#000';
        kolCtx.moveTo(kolPos.x, kolPos.y);
        setPos(e);
        kolCtx.lineTo(kolPos.x, kolPos.y);
        kolCtx.stroke();
    }
    
    function stop() {
        if (isKolDrawing) {
            isKolDrawing = false;
            if ($('#preview-kol-sign-slot').length > 0) {
                $('#preview-kol-sign-slot').html(
                    '<img id="preview-kol-sign" src="' + kolCanvas.toDataURL('image/png') + '" style="height: 52px; max-width: 140px; object-fit: contain; display: block; margin: 0 auto 4px auto;">' +
                    '<div style="font-size: 9px; color: #16a34a; font-weight: 600;"><i class="fa fa-pencil"></i> Pratinjau Tanda Tangan</div>'
                );
            }
        }
    }
    
    kolCanvas.addEventListener('mousedown', start);
    kolCanvas.addEventListener('mousemove', draw);
    kolCanvas.addEventListener('mouseup', stop);
    kolCanvas.addEventListener('mouseleave', stop);
    
    kolCanvas.addEventListener('touchstart', start, {passive: false});
    kolCanvas.addEventListener('touchmove', draw, {passive: false});
    kolCanvas.addEventListener('touchend', stop);
}

function clearKolPad() {
    if (kolCtx && kolCanvas) {
        kolCtx.clearRect(0, 0, kolCanvas.width, kolCanvas.height);
    }
    if ($('#preview-kol-sign-slot').length > 0) {
        $('#preview-kol-sign-slot').html(
            '<div style="height: 52px; border: 1px dashed #cbd5e1; border-radius: 4px; background: #f8fafc; color: #94a3b8; font-size: 10px; font-style: italic; line-height: 52px; text-align: center;">(Menunggu tanda tangan)</div>'
        );
    }
}

function isKolBlank() {
    if (!kolCanvas) return true;
    var blank = document.createElement('canvas');
    blank.width = kolCanvas.width;
    blank.height = kolCanvas.height;
    return kolCanvas.toDataURL() === blank.toDataURL();
}

$(document).ready(function() {
    $(document).on('click', '.btn-kol-open-sign', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var dealAmount = $(this).data('deal-amount') || '0';
        
        $('#kol_sign_contract_id').val(id);
        $('#kol_agreement_check').prop('checked', false);
        $('#kol_issue_invoice_check').prop('checked', true);
        $('#kol_modal_deal_amount_label').text('Rp ' + dealAmount);
        $('#kol_contract_preview').html('<div style="text-align: center; color: #64748b; padding: 60px 20px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Memuat naskah Surat Perjanjian...</div>');

        $('#modal_kol_sign_mou').modal('show');
        initKolSignPad();
        clearKolPad();

        $.ajax({
            url: BASE_URL + 'administrator/campaign_kol_contracts/get_contract_html_ajax/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.html) {
                    $('#kol_contract_preview').html(res.html);
                    if (res.deal_amount) {
                        var formattedAmount = new Intl.NumberFormat('id-ID').format(res.deal_amount);
                        $('#kol_modal_deal_amount_label').text('Rp ' + formattedAmount);
                    }
                } else {
                    $('#kol_contract_preview').html('<div style="color: #e11d48; text-align: center; padding: 50px 20px;">Gagal memuat pratinjau perjanjian.</div>');
                }
            },
            error: function() {
                $('#kol_contract_preview').html('<div style="color: #e11d48; text-align: center; padding: 50px 20px;">Kesalahan jaringan saat memuat pratinjau perjanjian.</div>');
            }
        });
    });

    $('#kol-clear-signature').click(function(e) {
        e.preventDefault();
        clearKolPad();
    });

    // Submit Tanda Tangan MoU oleh KOL
    $('#btn_submit_kol_sign').click(function(e) {
        e.preventDefault();
        if (isKolBlank()) {
            alert('Silakan bubuhkan tanda tangan Anda pada kanvas terlebih dahulu.');
            return;
        }

        if (!$('#kol_agreement_check').is(':checked')) {
            alert('Harap centang persetujuan syarat dan ketentuan perjanjian terlebih dahulu.');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan TTD &amp; Mengaktifkan...');

        var postData = {
            id: $('#kol_sign_contract_id').val(),
            signature: kolCanvas.toDataURL('image/png'),
            issue_invoice: $('#kol_issue_invoice_check').is(':checked') ? 1 : 0
        };
        if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
            postData[csrf] = token;
        }

        $.ajax({
            url: BASE_URL + 'administrator/campaign_kol_contracts/kol_sign_action',
            type: 'POST',
            dataType: 'json',
            data: postData,
            success: function(res) {
                if (res.success) {
                    alert(res.message);
                    window.location.reload();
                } else {
                    alert('Gagal: ' + res.message);
                    btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Tanda Tangani &amp; Setujui MoU');
                }
            },
            error: function(xhr) {
                var msg = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : ((xhr && xhr.responseText) ? xhr.responseText.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim().substring(0, 250) : 'Terjadi kesalahan jaringan.');
                alert('Gagal: ' + msg);
                btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Tanda Tangani &amp; Setujui MoU');
            }
        });
    });

    // KOL Terbitkan Invoice On-Demand
    $(document).on('click', '.btn-kol-issue-invoice', function(e) {
        e.preventDefault();
        var btn = $(this);
        var contractId = btn.data('contract-id');
        var contractNo = btn.data('contract-no');
        var dealAmount = btn.data('deal-amount');

        if (!confirm('Terbitkan dan ajukan invoice honorarium (Rp ' + dealAmount + ') untuk kontrak ' + contractNo + ' ke Brand sekarang?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menerbitkan...');
        var postData = {
            contract_id: contractId
        };
        if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
            postData[csrf] = token;
        }

        $.ajax({
            url: BASE_URL + 'administrator/campaign_kol_contracts/kol_issue_invoice_ajax',
            type: 'POST',
            dataType: 'json',
            data: postData,
            success: function(res) {
                if (res.success) {
                    alert(res.message);
                    window.location.reload();
                } else {
                    alert('Gagal: ' + res.message);
                    btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Terbitkan Invoice');
                }
            },
            error: function() {
                alert('Terjadi kesalahan jaringan.');
                btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Terbitkan Invoice');
            }
        });
    });
});
</script>

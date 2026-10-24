<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
function reset_side_menu() {
   $('.sidebar-menu li').removeClass('active');
   $('.sidebar-menu li a[href*="my_contracts"]').parents('li').addClass('active');
}
$(document).ready(function() {
   reset_side_menu();
});
</script>

<section class="content-header">
   <h1>
      Kontrak Saya
      <small>Daftar Perjanjian Kerja Sama (KOL Contract)</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Kontrak Saya</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.08);">
            <div class="box-header with-border" style="padding: 16px 20px; background: #fafbfc; border-radius: 8px 8px 0 0;">
               <h3 class="box-title" style="font-size: 16px; font-weight: 700; color: #1e293b;">
                  <i class="fa fa-file-text-o text-primary" style="margin-right: 6px;"></i> Perjanjian Kerja Sama
               </h3>
            </div>
            
            <div class="box-body" style="padding: 20px;">
               <div class="table-responsive">
                  <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                     <thead>
                        <tr style="background: #f1f5f9; color: #334155; font-size: 13px;">
                           <th style="width: 50px; text-align: center;">No</th>
                           <th>Nomor Kontrak</th>
                           <th>Campaign</th>
                           <th>Brand</th>
                           <th>Nilai Kerja Sama (Fee)</th>
                           <th style="text-align: center;">Status</th>
                           <th style="text-align: center; width: 220px;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($contracts)): ?>
                           <?php $no = 1; foreach ($contracts as $c): ?>
                              <tr>
                                 <td style="text-align: center; vertical-align: middle;"><?= $no++; ?></td>
                                 <td style="vertical-align: middle; font-weight: 600; color: #0284c7;">
                                    <?= htmlspecialchars($c->contract_no); ?>
                                 </td>
                                 <td style="vertical-align: middle; font-weight: 600; color: #1e293b;">
                                    <?= htmlspecialchars($c->campaign_name ?? 'N/A'); ?>
                                 </td>
                                 <td style="vertical-align: middle; color: #475569;">
                                    <?= htmlspecialchars($c->brand_name ?? 'KOL System'); ?>
                                 </td>
                                 <td style="vertical-align: middle; font-weight: 700; color: #16a34a;">
                                    Rp <?= number_format($c->deal_amount ?? 0, 0, ',', '.'); ?>
                                 </td>
                                 <td style="text-align: center; vertical-align: middle;">
                                    <?php if ($c->status == 'PENDING' || $c->status == 'WAITING_KOL_SIGNATURE'): ?>
                                       <span class="label label-warning" style="padding: 6px 10px; font-size: 11.5px; border-radius: 4px; display: inline-block;">
                                          <i class="fa fa-clock-o"></i> Menunggu Tanda Tangan Anda
                                       </span>
                                    <?php elseif ($c->status == 'ACTIVE' || $c->status == 'SIGNED'): ?>
                                       <span class="label label-success" style="padding: 6px 10px; font-size: 11.5px; border-radius: 4px; display: inline-block;">
                                          <i class="fa fa-check-circle"></i> Selesai Ditandatangani
                                       </span>
                                    <?php elseif ($c->status == 'DRAFT'): ?>
                                       <span class="label label-default" style="padding: 6px 10px; font-size: 11.5px; border-radius: 4px; display: inline-block;">
                                          Draft Admin
                                       </span>
                                    <?php else: ?>
                                       <span class="label label-default" style="padding: 6px 10px; font-size: 11.5px; border-radius: 4px; display: inline-block;">
                                          <?= $c->status; ?>
                                       </span>
                                    <?php endif; ?>
                                 </td>
                                 <td style="text-align: center; vertical-align: middle;">
                                    <?php if ($c->status == 'PENDING' || $c->status == 'WAITING_KOL_SIGNATURE'): ?>
                                       <button type="button" class="btn btn-sm btn-primary btn-flat btn-open-kol-sign" data-id="<?= $c->id; ?>" style="border-radius: 4px; font-weight: 600;">
                                          <i class="fa fa-pencil"></i> Tanda Tangani Kontrak
                                       </button>
                                    <?php elseif ($c->status == 'ACTIVE' || $c->status == 'SIGNED'): ?>
                                       <?php if (!empty($c->contract_file)): ?>
                                          <a href="<?= site_url('administrator/my_contracts/download_pdf/' . $c->id); ?>" target="_blank" class="btn btn-sm btn-success btn-flat" style="border-radius: 4px; font-weight: 600;">
                                             <i class="fa fa-eye"></i> Lihat PDF
                                          </a>
                                       <?php else: ?>
                                          <button type="button" class="btn btn-sm btn-default btn-flat btn-open-kol-sign" data-id="<?= $c->id; ?>" style="border-radius: 4px;">
                                             <i class="fa fa-eye"></i> Lihat Dokumen
                                          </button>
                                       <?php endif; ?>
                                    <?php else: ?>
                                       <button type="button" class="btn btn-sm btn-default btn-flat btn-open-kol-sign" data-id="<?= $c->id; ?>" style="border-radius: 4px;">
                                          <i class="fa fa-eye"></i> Preview
                                       </button>
                                    <?php endif; ?>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr>
                              <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                                 <i class="fa fa-file-o fa-3x" style="color: #cbd5e1; margin-bottom: 12px;"></i><br>
                                 Belum ada kontrak kerja sama yang diterbitkan untuk akun Anda.
                              </td>
                           </tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Modal Sign Contract for KOL -->
<div class="modal fade" id="modal_kol_sign_contract" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document" style="width: 95%; max-width: 1050px;">
    <div class="modal-content" style="border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 8px 8px 0 0; padding: 15px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 24px;"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" style="font-weight: 700; color: #1e293b; font-size: 16px;">
           <i class="fa fa-pencil-square-o" style="color: #0284c7;"></i> Tanda Tangan Elektronik Kontrak Kerja Sama
        </h4>
      </div>
      
      <div class="modal-body" style="background: #f1f5f9; padding: 20px;">
        <input type="hidden" id="kol_sign_contract_id" value="">
        
        <div class="row">
          <!-- Left: A4 Portrait Contract Document Preview -->
          <div class="col-md-7">
            <div style="background: #334155; padding: 20px 10px; border-radius: 8px; max-height: 580px; overflow-y: auto; display: flex; justify-content: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);">
               <div id="modal_kol_contract_preview" style="background: #ffffff; width: 100%; max-width: 440px; min-height: 622px; padding: 30px 24px; box-shadow: 0 8px 20px rgba(0,0,0,0.35); border-radius: 2px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; box-sizing: border-box;">
                  <div style="text-align: center; color: #64748b; padding: 150px 20px;">
                     <i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Memuat pratinjau kontrak...
                  </div>
               </div>
            </div>
          </div>
          
          <!-- Right: Signature Form for KOL -->
          <div class="col-md-5">
            <div id="kol_sign_action_panel" style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
               <label style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 12px; display: block;">
                  <i class="fa fa-hand-o-up" style="color: #0284c7;"></i> Pilih Cara Tanda Tangan:
               </label>
               
               <!-- Nav Pills for Option 1 vs Option 2 -->
               <ul class="nav nav-pills nav-justified" id="kol_sign_option_tabs" style="margin-bottom: 18px; background: #f1f5f9; padding: 4px; border-radius: 6px;">
                  <li class="active">
                     <a data-toggle="pill" href="#tab_kol_draw_sign" style="border-radius: 5px; font-weight: 600; font-size: 12px; padding: 8px 10px;">
                        <i class="fa fa-paint-brush"></i> 1. Draw E-Sign
                     </a>
                  </li>
                  <li>
                     <a data-toggle="pill" href="#tab_kol_upload_sign" style="border-radius: 5px; font-weight: 600; font-size: 12px; padding: 8px 10px;">
                        <i class="fa fa-cloud-upload"></i> 2. Upload File
                     </a>
                  </li>
               </ul>

               <div class="tab-content">
                  <!-- Option 1: Draw Signature -->
                  <div id="tab_kol_draw_sign" class="tab-pane fade in active">
                     <p style="font-size: 12px; color: #64748b; margin-bottom: 8px;">Goreskan tanda tangan Anda pada kanvas berikut:</p>
                     
                     <div style="text-align: center; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 10px;">
                        <canvas id="kol-modal-signature-pad" width="320" height="150" style="border: 1px solid #cbd5e1; border-radius: 4px; cursor: crosshair; background: #fff; max-width: 100%; touch-action: none;"></canvas>
                        <div style="margin-top: 8px; text-align: right;">
                           <button type="button" class="btn btn-default btn-xs" id="kol-modal-clear-signature" style="border-radius: 4px;">
                              <i class="fa fa-eraser"></i> Hapus / Clear
                           </button>
                        </div>
                     </div>
                     
                     <button type="button" class="btn btn-primary btn-block btn-flat" id="btn_kol_submit_draw_sign" style="margin-top: 18px; font-weight: 600; border-radius: 6px; padding: 12px; font-size: 13.5px; background-color: #0284c7; border-color: #0284c7;">
                        <i class="fa fa-check-circle"></i> Setujui & Tanda Tangani Kontrak
                     </button>
                  </div>

                  <!-- Option 2: Upload File -->
                  <div id="tab_kol_upload_sign" class="tab-pane fade">
                     <p style="font-size: 12px; color: #64748b; margin-bottom: 8px;">Upload scan tanda tangan Anda (JPG/PNG) atau dokumen kontrak bertanda tangan:</p>
                     
                     <form id="form_kol_sign_contract_upload" enctype="multipart/form-data">
                        <div class="form-group" style="margin-bottom: 18px;">
                           <label style="font-size: 12px; font-weight: 600; color: #334155;">Pilih Gambar Tanda Tangan:</label>
                           <input type="file" name="contract_file" id="kol_sign_contract_file" class="form-control" accept="image/*,application/pdf" required style="padding: 6px; height: auto; border-radius: 4px;">
                        </div>
                        <button type="submit" class="btn btn-success btn-block btn-flat" id="btn_kol_submit_upload_sign" style="font-weight: 600; border-radius: 6px; padding: 12px; font-size: 13.5px; background-color: #059669; border-color: #059669;">
                           <i class="fa fa-cloud-upload"></i> Upload & Tanda Tangani Kontrak
                        </button>
                     </form>
                  </div>
               </div>
            </div>
            
            <div id="kol_signed_notice" style="display: none; padding: 20px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; text-align: center;">
               <i class="fa fa-check-circle fa-3x" style="color: #10b981; margin-bottom: 10px;"></i>
               <h4 style="font-weight: 700; color: #065f46; margin: 0 0 5px 0;">Kontrak Telah Ditandatangani</h4>
               <p style="font-size: 12.5px; color: #047857; margin-bottom: 0;">Dokumen ini telah resmi berlaku dan mengikat kedua belah pihak.</p>
            </div>

            <div style="margin-top: 15px; padding: 12px 15px; background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 6px; font-size: 11.5px; color: #0369a1;">
               <i class="fa fa-shield"></i> Dengan menandatangani secara elektronik, Anda menyatakan menyetujui seluruh butir perjanjian kerja sama yang tercantum pada dokumen.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var kolCanvas, kolCtx, isKolDrawing = false, kolSignPos = { x: 0, y: 0 };
var kolCurrentName = '<?= htmlspecialchars($this->aauth->get_user()->full_name ?? 'KOL / Creator'); ?>';
var kolAdminSignHtml = '';

function initKolSignaturePad() {
    if (kolCanvas) return;
    kolCanvas = document.getElementById('kol-modal-signature-pad');
    kolCtx = kolCanvas.getContext('2d');
    
    function setPos(e) {
        var rect = kolCanvas.getBoundingClientRect();
        var clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
        var clientY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : 0);
        kolSignPos.x = clientX - rect.left;
        kolSignPos.y = clientY - rect.top;
    }
    
    function startDraw(e) {
        if(e.type === 'mousedown' || e.type === 'touchstart') {
            setPos(e);
            isKolDrawing = true;
        }
    }
    
    function drawing(e) {
        if (!isKolDrawing) return;
        e.preventDefault();
        kolCtx.beginPath();
        kolCtx.lineWidth = 2;
        kolCtx.lineCap = 'round';
        kolCtx.strokeStyle = '#000';
        kolCtx.moveTo(kolSignPos.x, kolSignPos.y);
        setPos(e);
        kolCtx.lineTo(kolSignPos.x, kolSignPos.y);
        kolCtx.stroke();
    }
    
    function stopDraw() {
        if (isKolDrawing) {
            isKolDrawing = false;
            updateKolLivePreview(kolCanvas.toDataURL('image/png'));
        }
    }
    
    kolCanvas.addEventListener('mousedown', startDraw);
    kolCanvas.addEventListener('mousemove', drawing);
    kolCanvas.addEventListener('mouseup', stopDraw);
    kolCanvas.addEventListener('mouseleave', stopDraw);
    
    kolCanvas.addEventListener('touchstart', startDraw, {passive: false});
    kolCanvas.addEventListener('touchmove', drawing, {passive: false});
    kolCanvas.addEventListener('touchend', stopDraw);
}

function updateKolLivePreview(dataUrl) {
    if ($('#preview-kol-sign-img').length === 0) {
        var signBlock = '<div id="kol-preview-sign-wrapper" style="margin-top: 20px; border-top: 1px dashed #94a3b8; padding-top: 10px;">' +
            '<table style="width: 100%; border: none;">' +
                '<tr>' +
                    '<td style="width: 50%; text-align: left; vertical-align: top;">' +
                        '<div style="font-size: 9.5px; color: #64748b; font-weight: bold;">Pihak Kedua (KOL / Talent):</div>' +
                        '<div style="margin-top: 6px; margin-bottom: 6px; min-height: 45px;">' +
                            '<img id="preview-kol-sign-img" src="' + dataUrl + '" style="max-height: 45px;">' +
                        '</div>' +
                        '<div style="font-size: 10px; font-weight: bold; color: #0f172a; border-top: 1px solid #cbd5e1; display: inline-block; padding-top: 2px; min-width: 120px;">' + kolCurrentName + '</div>' +
                        '<div style="font-size: 8.5px; color: #64748b;">KOL / Creator</div>' +
                    '</td>' +
                    '<td style="width: 50%; text-align: right; vertical-align: top;">' +
                        '<div style="font-size: 9.5px; color: #64748b; font-weight: bold;">Pihak Pertama (Brand / Agency):</div>' +
                        '<div style="margin-top: 6px; margin-bottom: 6px; min-height: 45px;">' +
                            kolAdminSignHtml +
                        '</div>' +
                        '<div style="font-size: 10px; font-weight: bold; color: #0f172a; border-top: 1px solid #cbd5e1; display: inline-block; padding-top: 2px; min-width: 120px;">KOL-System Team / Authorized</div>' +
                        '<div style="font-size: 8.5px; color: #64748b;">Brand / Agency Authorized</div>' +
                    '</td>' +
                '</tr>' +
            '</table>' +
        '</div>';
        $('#modal_kol_contract_preview').append(signBlock);
    } else {
        $('#preview-kol-sign-img').attr('src', dataUrl);
    }
}

function clearKolSignaturePad() {
    if (kolCtx && kolCanvas) {
        kolCtx.clearRect(0, 0, kolCanvas.width, kolCanvas.height);
        $('#kol-preview-sign-wrapper').remove();
    }
}

function isKolCanvasEmpty() {
    if (!kolCanvas) return true;
    const blank = document.createElement('canvas');
    blank.width = kolCanvas.width;
    blank.height = kolCanvas.height;
    return kolCanvas.toDataURL() === blank.toDataURL();
}

$(document).ready(function() {
    $('.btn-open-kol-sign').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#kol_sign_contract_id').val(id);
        $('#kol_sign_contract_file').val('');
        $('#modal_kol_contract_preview').html('<div style="text-align: center; color: #64748b; padding: 150px 20px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Memuat pratinjau kontrak...</div>');
        
        $('#modal_kol_sign_contract').modal('show');
        initKolSignaturePad();
        clearKolSignaturePad();
        
        // Reset tabs
        $('#kol_sign_option_tabs a[href="#tab_kol_draw_sign"]').tab('show');
        
        $.ajax({
            url: BASE_URL + 'administrator/my_contracts/get_contract_html_ajax/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.html) {
                    if (res.kol_name) kolCurrentName = res.kol_name;
                    kolAdminSignHtml = res.admin_sign_html || '';
                    $('#modal_kol_contract_preview').html(res.html);
                    
                    if (res.status === 'ACTIVE' || res.status === 'SIGNED') {
                        $('#kol_sign_action_panel').hide();
                        $('#kol_signed_notice').show();
                    } else {
                        $('#kol_sign_action_panel').show();
                        $('#kol_signed_notice').hide();
                    }
                } else {
                    $('#modal_kol_contract_preview').html('<div style="color: #e11d48; text-align: center; padding: 50px 20px;">Gagal memuat dokumen kontrak.</div>');
                }
            },
            error: function() {
                $('#modal_kol_contract_preview').html('<div style="color: #e11d48; text-align: center; padding: 50px 20px;">Terjadi kesalahan jaringan.</div>');
            }
        });
    });

    $('#kol-modal-clear-signature').click(function(e) {
        e.preventDefault();
        clearKolSignaturePad();
    });

    // Auto-update preview when KOL uploads file
    $('#kol_sign_contract_file').on('change', function(e) {
        var file = e.target.files[0];
        if (file && file.type.match('image.*')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                updateKolLivePreview(e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // KOL Submit Draw E-Sign
    $('#btn_kol_submit_draw_sign').click(function(e) {
        e.preventDefault();
        if (isKolCanvasEmpty()) {
            alert('Silakan goreskan tanda tangan Anda terlebih dahulu pada kanvas.');
            return;
        }

        var signatureData = kolCanvas.toDataURL('image/png');
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Menyimpan Tanda Tangan...');

        var postData = {
            id: $('#kol_sign_contract_id').val(),
            signature: signatureData
        };
        if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
            postData[csrf] = token;
        }

        $.ajax({
            url: BASE_URL + 'administrator/my_contracts/kol_sign_action',
            type: 'POST',
            dataType: 'json',
            data: postData,
            success: function(res) {
                if (res.success) {
                    alert('Selamat! Kontrak berhasil ditandatangani dan diaktifkan.');
                    window.location.reload();
                } else {
                    alert('Gagal: ' + res.message);
                    btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Setujui & Tanda Tangani Kontrak');
                }
            },
            error: function() {
                alert('Terjadi kesalahan jaringan.');
                btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Setujui & Tanda Tangani Kontrak');
            }
        });
    });

    // KOL Submit Upload File
    $('#form_kol_sign_contract_upload').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#btn_kol_submit_upload_sign');
        btn.prop('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Mengunggah...');

        var formData = new FormData(this);
        formData.append('id', $('#kol_sign_contract_id').val());
        if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
            formData.append(csrf, token);
        }

        $.ajax({
            url: BASE_URL + 'administrator/my_contracts/kol_sign_action',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    alert('Selamat! Dokumen tanda tangan berhasil disimpan.');
                    window.location.reload();
                } else {
                    alert(res.message);
                    btn.prop('disabled', false).html('<i class="fa fa-cloud-upload"></i> Upload & Tanda Tangani Kontrak');
                }
            },
            error: function() {
                alert('Terjadi kesalahan jaringan.');
                btn.prop('disabled', false).html('<i class="fa fa-cloud-upload"></i> Upload & Tanda Tangani Kontrak');
            }
        });
    });
});
</script>

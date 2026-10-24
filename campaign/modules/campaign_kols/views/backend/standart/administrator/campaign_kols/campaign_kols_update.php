<!-- Fine Uploader Gallery CSS file
    ====================================================================== -->
<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<!-- Fine Uploader jQuery JS file
    ====================================================================== -->
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
  function domo() {

    // Binding keys
    $('*').bind('keydown', 'Ctrl+s', function assets() {
      $('#btn_save').trigger('click');
      return false;
    });

    $('*').bind('keydown', 'Ctrl+x', function assets() {
      $('#btn_cancel').trigger('click');
      return false;
    });

    $('*').bind('keydown', 'Ctrl+d', function assets() {
      $('.btn_save_back').trigger('click');
      return false;
    });

  }

  jQuery(document).ready(domo);
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Campaign Kols <small>Edit Campaign Kols</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class=""><a href="<?= site_url('administrator/campaign_kols'); ?>">Campaign Kols</a></li>
    <li class="active">Edit</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-warning">
        <div class="box-body ">
          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header ">
              <div class="widget-user-image">
                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="User Avatar">
              </div>
              <!-- /.widget-user-image -->
              <h3 class="widget-user-username">Campaign Kols</h3>
              <h5 class="widget-user-desc">Edit Campaign Kols</h5>
              <hr>
            </div>
            <?= form_open(base_url('administrator/campaign_kols/edit_save/' . $this->uri->segment(4)), [
              'name' => 'form_campaign_kols',
              'class' => 'form-horizontal',
              'id' => 'form_campaign_kols',
              'method' => 'POST'
            ]); ?>

            <div class="form-group ">
              <label for="campaign_id" class="col-sm-2 control-label">Campaign
                <i class="required">*</i>
              </label>
              <div class="col-sm-8">
                <select class="form-control chosen chosen-select-deselect" name="campaign_id" id="campaign_id"
                  data-placeholder="Select Campaign Id">
                  <option value=""></option>
                  <?php foreach (db_get_all_data('campaigns') as $row): ?>
                    <option <?= $row->id == $campaign_kols->campaign_id ? 'selected' : ''; ?> value="<?= $row->id ?>">
                      <?= $row->name; ?></option>
                  <?php endforeach; ?>
                </select>
                <small class="info help-block">
                </small>
              </div>
            </div>


            <div class="form-group ">
              <label for="kol_id" class="col-sm-2 control-label">Assignment KOL
                <i class="required">*</i>
              </label>
              <div class="col-sm-8">
                <select class="form-control chosen chosen-select-deselect" name="kol_id" id="kol_id"
                  data-placeholder="Select Kol Id">
                  <option value=""></option>
                  <?php foreach (db_get_all_data('kols', ['status' => 'ACTIVE']) as $row): ?>
                    <option <?= $row->id == $campaign_kols->kol_id ? 'selected' : ''; ?> value="<?= $row->id ?>" <?= (!empty($row->is_blacklisted) && $row->id != $campaign_kols->kol_id) ? 'disabled style="color:#dc2626;"' : ''; ?>>
                      <?= $row->name; ?><?= !empty($row->is_blacklisted) ? ' ⚠️ [BLACKLISTED: ' . _ent($row->blacklist_reason ?: 'Tidak Direkomendasikan') . ']' : ''; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <small class="info help-block">
                </small>
              </div>
            </div>


            <div class="form-group ">
              <label for="deal_scope" class="col-sm-2 control-label">Brief
                <i class="required">*</i>
              </label>
              <div class="col-sm-8">
                <div id="campaign_kols_deal_scope_galery"></div>
                <div id="campaign_kols_deal_scope_galery_listed">
                  <?php foreach ((array) explode(',', $campaign_kols->deal_scope) as $idx => $filename): ?>
                    <input type="hidden" class="listed_file_uuid" name="campaign_kols_deal_scope_uuid[<?= $idx ?>]"
                      value="" /><input type="hidden" class="listed_file_name"
                      name="campaign_kols_deal_scope_name[<?= $idx ?>]" value="<?= $filename; ?>" />
                  <?php endforeach; ?>
                </div>
                <small class="info help-block">
                </small>
              </div>
            </div>

            <div class="form-group ">
              <label for="status" class="col-sm-2 control-label">Status Assign
                <i class="required">*</i>
              </label>
              <div class="col-sm-8">
                <?php 
                  $locked_statuses = ['ASSIGNED', 'ACTIVE', 'COMPLETED'];
                  if (in_array($campaign_kols->status, $locked_statuses)): 
                ?>
                  <input type="text" class="form-control" value="<?= $campaign_kols->status; ?>" readonly style="background-color: #f4f6f9;">
                  <input type="hidden" name="status" id="status" value="<?= $campaign_kols->status; ?>">
                  <small class="info help-block">Status tidak dapat diubah karena sudah <b><?= $campaign_kols->status; ?></b> dan memiliki konfigurasi Task aktif.</small>
                <?php else: ?>
                  <select class="form-control chosen chosen-select" name="status" id="status"
                    data-placeholder="Select Status">
                    <option value=""></option>
                    <option <?= $campaign_kols->status == "REJECTED" ? 'selected' : ''; ?> value="REJECTED">REJECTED</option>
                    <option <?= $campaign_kols->status == "COMPLETED" ? 'selected' : ''; ?> value="COMPLETED">COMPLETED
                    </option>
                    <option <?= $campaign_kols->status == "ACTIVE" ? 'selected' : ''; ?> value="ACTIVE">ACTIVE</option>
                    <option <?= $campaign_kols->status == "ASSIGNED" ? 'selected' : ''; ?> value="ASSIGNED">ASSIGNED</option>
                    <option <?= $campaign_kols->status == "NEGOTIATION" ? 'selected' : ''; ?> value="NEGOTIATION">NEGOTIATION
                    </option>
                    <option <?= $campaign_kols->status == "CANCELLED" ? 'selected' : ''; ?> value="CANCELLED">CANCELLED
                    </option>
                  </select>
                  <small class="info help-block">
                  </small>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group " id="rejection_reason_group" style="display:none;">
              <label for="rejection_reason" class="col-sm-2 control-label">Rejection Reason
                <i class="required">*</i>
              </label>
              <div class="col-sm-8">
                <textarea class="form-control" name="rejection_reason" id="rejection_reason" rows="3"
                  placeholder="Masukkan alasan penolakan..."><?= set_value('rejection_reason', $campaign_kols->rejection_reason ?? ''); ?></textarea>
              </div>
            </div>

            <div class="form-group ">
              <label for="jumlah_vt" class="col-sm-2 control-label">Jumlah Post/VT
              </label>
              <div class="col-sm-8">
                <input type="number" class="form-control" name="jumlah_vt" id="jumlah_vt" placeholder="Jumlah Post/VT"
                  value="<?= set_value('jumlah_vt', $campaign_kols->jumlah_vt); ?>">
                <small class="info help-block">
                </small>
              </div>
            </div>

            <div class="form-group" id="tasks_configuration_section" style="display: none;">
              <div class="col-sm-12" style="padding: 0 15px;">
                <div class="box box-primary"
                  style="border-top: 3px solid #3c8dbc; border-radius: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); margin-top: 8px; margin-bottom: 20px;">
                  <div class="box-header with-border" style="background-color: #f9fafc; padding: 12px 15px;">
                    <h3 class="box-title" style="font-size: 14px; font-weight: 600; color: #3c8dbc;">
                      <i class="fa fa-tasks"></i> Konfigurasi Detail Task per Post / VT (<span
                        id="task_count_label">0</span> Task)
                    </h3>
                  </div>
                  <div class="box-body" style="padding: 0; overflow-x: auto;">
                    <table class="table table-bordered table-striped table-hover"
                      style="margin-bottom: 0; min-width: 920px;">
                      <thead style="background-color: #f4f6f9; color: #333; font-size: 12px;">
                        <tr>
                          <th style="width: 45px; text-align: center;">#</th>
                          <th style="min-width: 190px;">Title / Task Name <span class="text-danger">*</span></th>
                          <th style="min-width: 140px;">Platform <span class="text-danger">*</span></th>
                          <th style="min-width: 150px;">Konsep / Variant <span class="text-danger">*</span></th>
                          <th style="min-width: 130px;">Content Type</th>
                          <th style="min-width: 130px;">Estimasi Posting <span class="text-danger">*</span></th>
                          <th style="min-width: 120px;">Budget (IDR)</th>
                          <th style="min-width: 160px;">Notes / Brief</th>
                        </tr>
                      </thead>
                      <tbody id="tasks_table_tbody">
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group " style="display:none;">
              <label for="assigned_at" class="col-sm-2 control-label">Assigned At
                <i class="required">*</i>
              </label>
              <div class="col-sm-6">
                <div class="input-group date col-sm-8">
                  <input type="text" class="form-control pull-right datetimepicker" name="assigned_at"
                    placeholder="Assigned At" id="assigned_at"
                    value="<?= set_value('assigned_at', $campaign_kols->assigned_at); ?>">
                </div>
                <small class="info help-block">
                </small>
              </div>
            </div>

            <div class="form-group " style="display:none;">
              <label for="deal_date" class="col-sm-2 control-label">Deal Date
              </label>
              <div class="col-sm-6">
                <div class="input-group date col-sm-8">
                  <input type="text" class="form-control pull-right datetimepicker" name="deal_date"
                    placeholder="Deal Date" id="deal_date"
                    value="<?= set_value('deal_date', $campaign_kols->deal_date); ?>">
                </div>
                <small class="info help-block">
                </small>
              </div>
            </div>

            <div class="form-group ">
              <label for="deal_amount" class="col-sm-2 control-label">Deal Amount
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="deal_amount" id="deal_amount" placeholder="Deal Amount"
                  value="<?= set_value('deal_amount', number_format($campaign_kols->deal_amount, 0, ',', '.')); ?>">
                <small class="info help-block">
                </small>
              </div>
            </div>

            <div class="form-group " style="display:none;">
              <label for="task_count" class="col-sm-2 control-label">Task Count
              </label>
              <div class="col-sm-8">
                <input type="number" class="form-control" name="task_count" id="task_count" placeholder="Task Count"
                  value="<?= set_value('task_count', $campaign_kols->task_count); ?>">
                <small class="info help-block">
                </small>
              </div>
            </div>

            <div class="form-group " style="display:none;">
              <label for="task_completed_count" class="col-sm-2 control-label">Task Complete
              </label>
              <div class="col-sm-8">
                <input type="number" class="form-control" name="task_completed_count" id="task_completed_count"
                  placeholder="Task Complete"
                  value="<?= set_value('task_completed_count', $campaign_kols->task_completed_count); ?>">
                <small class="info help-block">
                </small>
              </div>
            </div>

            <div class="form-group ">
              <label for="currency_code" class="col-sm-2 control-label">Code Currency
              </label>
              <div class="col-sm-8">
                <select class="form-control chosen chosen-select" name="currency_code" id="currency_code"
                  data-placeholder="Pilih mata uang">
                  <option value="IDR" <?= set_value('currency_code', $campaign_kols->currency_code) == 'IDR' ? 'selected' : ''; ?>>IDR</option>
                  <option value="USD" <?= set_value('currency_code', $campaign_kols->currency_code) == 'USD' ? 'selected' : ''; ?>>USD</option>
                </select>
                <small class="info help-block">Pilih mata uang deal.</small>
              </div>
            </div>

            <div class="message"></div>
            <div class="row-fluid col-md-7">
              <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay'
                title="<?= cclang('save_button'); ?> (Ctrl+s)">
                <i class="fa fa-save"></i> <?= cclang('save_button'); ?>
              </button>
              <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save" data-stype='back'
                title="<?= cclang('save_and_go_the_list_button'); ?> (Ctrl+d)">
                <i class="ion ion-ios-list-outline"></i> <?= cclang('save_and_go_the_list_button'); ?>
              </a>
              <a class="btn btn-flat btn-default btn_action" id="btn_cancel"
                title="<?= cclang('cancel_button'); ?> (Ctrl+x)">
                <i class="fa fa-undo"></i> <?= cclang('cancel_button'); ?>
              </a>
              <span class="loading loading-hide">
                <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg">
                <i><?= cclang('loading_saving_data'); ?></i>
              </span>
            </div>
            <?= form_close(); ?>
          </div>
        </div>
        <!--/box body -->
      </div>
      <!--/box -->
    </div>
  </div>
</section>
<!-- /.content -->
<!-- Page script -->
<script>
  $(document).ready(function () {


    $(document).on('input', '#deal_amount, input[name^="tasks"][name$="[budget]"]', function () {
      var raw = $(this).val();
      var val = raw.toString().split(',')[0].replace(/[^0-9]/g, '');
      if (val) {
        $(this).val(parseInt(val, 10).toLocaleString('id-ID'));
      } else {
        $(this).val('');
      }
    });

    $('#btn_cancel').click(function () {
      swal({
        title: "Are you sure?",
        text: "the data that you have created will be in the exhaust!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes!",
        cancelButtonText: "No!",
        closeOnConfirm: true,
        closeOnCancel: true
      },
        function (isConfirm) {
          if (isConfirm) {
            window.location.href = BASE_URL + 'administrator/campaign_kols';
          }
        });

      return false;
    }); /*end btn cancel*/

    $('.btn_save').click(function () {
      $('.message').fadeOut();

      var form_campaign_kols = $('#form_campaign_kols');
      var deal_scope_files = $('#campaign_kols_deal_scope_galery_listed .listed_file_name').map(function () { return $(this).val(); }).get().filter(Boolean);
      if (deal_scope_files.length === 0) {
        $('.message').printMessage({ message: 'The Brief field is required.', type: 'warning' });
        $('.message').fadeIn();
        $('html, body').animate({ scrollTop: $('#campaign_kols_deal_scope_galery').offset().top - 100 }, 500);
        return false;
      }

      var status = $('#status').val();
      if (status === 'REJECTED' && !$('#rejection_reason').val().trim()) {
        $('.message').printMessage({ message: 'Rejection Reason wajib diisi ketika status REJECTED.', type: 'warning' });
        $('.message').fadeIn();
        $('html, body').animate({ scrollTop: $('#rejection_reason').offset().top - 100 }, 500);
        return false;
      }

      // Deal Amount Validation
      var allowedTaskStatuses = ['ASSIGNED', 'ACTIVE', 'COMPLETED'];
      if (allowedTaskStatuses.indexOf(status) !== -1) {
        var dealAmount = parseFloat($('#deal_amount').val().toString().replace(/\.00$/, '').replace(/\./g, '')) || 0;
        var totalTaskBudget = 0;
        $('#tasks_table_tbody tr.task-row').each(function () {
          var b = parseFloat($(this).find('input[name$="[budget]"]').val().toString().replace(/\.00$/, '').replace(/\./g, '')) || 0;
          totalTaskBudget += b;
        });

        if (dealAmount <= 0) {
          $('.message').printMessage({ message: 'Deal Amount harus lebih dari 0 jika status ' + status + '.', type: 'warning' });
          $('.message').fadeIn();
          $('html, body').animate({ scrollTop: $('#deal_amount').offset().top - 100 }, 500);
          return false;
        }

        if (totalTaskBudget !== dealAmount) {
          $('.message').printMessage({ message: 'Total budget tasks (Rp ' + totalTaskBudget.toLocaleString('id-ID') + ') tidak sama dengan Deal Amount (Rp ' + dealAmount.toLocaleString('id-ID') + ').', type: 'warning' });
          $('.message').fadeIn();
          $('html, body').animate({ scrollTop: $('#deal_amount').offset().top - 100 }, 500);
          return false;
        }
      }

      var data_post = form_campaign_kols.serializeArray();
      $.each(data_post, function (i, field) {
        if (field.name === 'deal_amount' || /^tasks\[\d+\]\[budget\]$/.test(field.name)) {
          var cleanNum = parseInt(field.value.toString().split(',')[0].replace(/[^0-9]/g, ''), 10);
          field.value = !isNaN(cleanNum) ? cleanNum : 0;
        }
      });
      var save_type = $(this).attr('data-stype');
      data_post.push({ name: 'save_type', value: save_type });

      $('.loading').show();

      $.ajax({
        url: form_campaign_kols.attr('action'),
        type: 'POST',
        dataType: 'json',
        data: data_post,
      })
        .done(function (res) {
          console.log('[Campaign KOLs Edit Save Response]', res);
          if (res.success) {
            var id = $('#campaign_kols_image_galery').find('li').attr('qq-file-id');
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }

            $('.message').printMessage({ message: res.message });
            $('.message').fadeIn();
            $('.data_file_uuid').val('');

          } else {
            console.warn('[Campaign KOLs Edit Save Error]', res.message);
            $('.message').printMessage({ message: res.message, type: 'warning' });
          }

        })
        .fail(function (xhr, status, error) {
          console.error('[Campaign KOLs Edit Save AJAX Failed]', status, error, xhr.responseText);
          var errorMsg = 'Error save data';
          if (xhr.responseText) {
            var stripped = xhr.responseText.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim();
            if (stripped) {
              errorMsg += ' (' + xhr.status + '): ' + stripped.substring(0, 300);
            }
          }
          $('.message').printMessage({ message: errorMsg, type: 'warning' });
        })
        .always(function () {
          $('.loading').hide();
          $('html, body').animate({ scrollTop: $(document).height() }, 2000);
        });

      return false;
    }); /*end btn save*/



    var params = {};
    params[csrf] = token;

    $('#campaign_kols_deal_scope_galery').fineUploader({
      template: 'qq-template-gallery',
      request: {
        endpoint: BASE_URL + '/administrator/campaign_kols/upload_deal_scope_file',
        params: params
      },
      deleteFile: {
        enabled: true,
        endpoint: BASE_URL + '/administrator/campaign_kols/delete_deal_scope_file',
      },
      thumbnails: {
        placeholders: {
          waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
          notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
        }
      },
      session: {
        endpoint: BASE_URL + 'administrator/campaign_kols/get_deal_scope_file/<?= $campaign_kols->id; ?>',
        refreshOnRequest: true
      },
      validation: {
        allowedExtensions: ["*"],
        sizeLimit: 0,
      },
      showMessage: function (msg) {
        toastr['error'](msg);
      },
      callbacks: {
        onComplete: function (id, name, xhr) {
          if (xhr.success) {
            var uuid = $('#campaign_kols_deal_scope_galery').fineUploader('getUuid', id);
            $('#campaign_kols_deal_scope_galery_listed').append('<input type="hidden" class="listed_file_uuid" name="campaign_kols_deal_scope_uuid[' + id + ']" value="' + uuid + '" /><input type="hidden" class="listed_file_name" name="campaign_kols_deal_scope_name[' + id + ']" value="' + xhr.uploadName + '" />');
          } else {
            toastr['error'](xhr.error);
          }
        },
        onDeleteComplete: function (id, xhr, isError) {
          if (isError == false) {
            $('#campaign_kols_deal_scope_galery_listed').find('.listed_file_uuid[name="campaign_kols_deal_scope_uuid[' + id + ']"]').remove();
            $('#campaign_kols_deal_scope_galery_listed').find('.listed_file_name[name="campaign_kols_deal_scope_name[' + id + ']"]').remove();
          }
        }
      }
    }); /*end deal_scope galery*/

    var platformsList = <?= json_encode($platforms ?? db_get_all_data('platforms')); ?>;
    var variantsList = <?= json_encode($variants ?? db_get_all_data('variants')); ?>;
    var contentTypeList = <?= json_encode(array_keys(kol_rate_content_types())); ?>;
    var contentTypeAliases = <?= json_encode(kol_content_type_aliases()); ?>;
    window.normalizeContentType = window.normalizeContentType || function (v) {
      var k = String(v === undefined || v === null ? '' : v).toLowerCase().trim();
      return contentTypeAliases[k] || v || '<?= kol_default_content_type(); ?>';
    };
    var existingTasks = <?= !empty($existing_tasks) ? json_encode($existing_tasks) : '[]'; ?>;

    function renderTaskCards() {
      var count = parseInt($('#jumlah_vt').val()) || 0;
      var status = $('#status').val();
      var allowedStatuses = ['ASSIGNED', 'ACTIVE', 'COMPLETED'];
      var $tbody = $('#tasks_table_tbody');
      var $section = $('#tasks_configuration_section');

      if (count <= 0 || allowedStatuses.indexOf(status) === -1) {
        $section.slideUp();
        $tbody.empty();
        return;
      }

      $('#task_count_label').text(count);
      $section.slideDown();

      var currentValues = [];
      $tbody.find('tr.task-row').each(function (idx) {
        currentValues[idx] = {
          id: $(this).find('input[name="tasks[' + idx + '][id]"]').val() || '',
          title: $(this).find('input[name="tasks[' + idx + '][title]"]').val() || '',
          platform_id: $(this).find('select[name="tasks[' + idx + '][platform_id]"]').val() || '',
          variant_id: $(this).find('select[name="tasks[' + idx + '][variant_id]"]').val() || '',
          content_type: $(this).find('select[name="tasks[' + idx + '][content_type]"]').val() || '',
          estimated_posting_date: $(this).find('input[name="tasks[' + idx + '][estimated_posting_date]"]').val() || '',
          budget: $(this).find('input[name="tasks[' + idx + '][budget]"]').val() || '',
          notes: $(this).find('textarea[name="tasks[' + idx + '][notes]"]').val() || ''
        };
      });

      var html = '';
      for (var i = 0; i < count; i++) {
        var existing = (existingTasks[i]) ? existingTasks[i] : {};
        var curr = (currentValues[i]) ? currentValues[i] : {};

        var id = curr.id !== undefined ? curr.id : (existing.id || '');
        var title = curr.title !== undefined && curr.title !== '' ? curr.title : (existing.title || 'Task #' + (i + 1));
        var platform_id = curr.platform_id !== undefined && curr.platform_id !== '' ? curr.platform_id : (existing.platform_id || '');
        var variant_id = curr.variant_id !== undefined && curr.variant_id !== '' ? curr.variant_id : (existing.variant_id || '');
        var content_type = normalizeContentType(curr.content_type !== undefined && curr.content_type !== '' ? curr.content_type : (existing.content_type || ''));
        var est_date = curr.estimated_posting_date !== undefined && curr.estimated_posting_date !== '' ? curr.estimated_posting_date : (existing.estimated_posting_date || '');
        var budget = curr.budget !== undefined && curr.budget !== '' ? curr.budget : (existing.budget || '');
        if (budget) {
          var cleanBudget = parseFloat(budget.toString().replace(/\.00$/, '').replace(/\./g, '')) || 0;
          budget = cleanBudget > 0 ? cleanBudget.toLocaleString('id-ID') : '';
        }
        var notes = curr.notes !== undefined ? curr.notes : (existing.notes || '');

        var platformOptions = '<option value="">- Pilih -</option>';
        $.each(platformsList, function (pIdx, p) {
          var sel = (platform_id == p.id) ? 'selected' : '';
          platformOptions += '<option value="' + p.id + '" ' + sel + '>' + p.name + '</option>';
        });

        var variantOptions = '<option value="">- Pilih -</option>';
        $.each(variantsList, function (vIdx, v) {
          var sel = (variant_id == v.id) ? 'selected' : '';
          variantOptions += '<option value="' + v.id + '" ' + sel + '>' + v.name + '</option>';
        });

        var contentTypeOptions = '';
        var types = contentTypeList;
        $.each(types, function (tIdx, t) {
          var sel = (content_type == t) ? 'selected' : '';
          contentTypeOptions += '<option value="' + t + '" ' + sel + '>' + t + '</option>';
        });

        html += '<tr class="task-row" data-task-index="' + i + '">';
        html += '  <td style="text-align: center; vertical-align: middle; font-weight: bold; color: #3c8dbc;">' + (i + 1) + '</td>';
        html += '  <td style="vertical-align: middle;">';
        html += '    <input type="hidden" name="tasks[' + i + '][id]" value="' + id + '" />';
        html += '    <input type="text" class="form-control input-sm" name="tasks[' + i + '][title]" placeholder="Judul Task" value="' + title + '" required style="border-radius: 3px;" />';
        html += '  </td>';
        html += '  <td style="vertical-align: middle;">';
        html += '    <select class="form-control input-sm" name="tasks[' + i + '][platform_id]" required style="border-radius: 3px;">' + platformOptions + '</select>';
        html += '  </td>';
        html += '  <td style="vertical-align: middle;">';
        html += '    <select class="form-control input-sm" name="tasks[' + i + '][variant_id]" required style="border-radius: 3px;">' + variantOptions + '</select>';
        html += '  </td>';
        html += '  <td style="vertical-align: middle;">';
        html += '    <select class="form-control input-sm" name="tasks[' + i + '][content_type]" style="border-radius: 3px;">' + contentTypeOptions + '</select>';
        html += '  </td>';
        html += '  <td style="vertical-align: middle;">';
        html += '    <input type="text" class="form-control input-sm task-datepicker" name="tasks[' + i + '][estimated_posting_date]" placeholder="YYYY-MM-DD" value="' + est_date + '" required style="border-radius: 3px;" />';
        html += '  </td>';
        html += '  <td style="vertical-align: middle;">';
        html += '    <input type="text" class="form-control input-sm" name="tasks[' + i + '][budget]" placeholder="0" value="' + budget + '" style="border-radius: 3px;" />';
        html += '  </td>';
        html += '  <td style="vertical-align: middle;">';
        html += '    <textarea class="form-control input-sm" name="tasks[' + i + '][notes]" rows="1" placeholder="Notes..." style="border-radius: 3px; resize: vertical;">' + notes + '</textarea>';
        html += '  </td>';
        html += '</tr>';
      }

      $tbody.html(html);

      $tbody.find('.task-datepicker').datetimepicker({
          timepicker: false,
          format: 'Y-m-d',
          formatDate: 'Y.m.d',
      });
      
      $tbody.find('.task-datepicker').inputmask({
          mask: "y-1-2",
          placeholder: "yyyy-mm-dd",
          leapday: "-02-29",
          separator: "-",
          alias: "yyyy/mm/dd"
      });
    }

    function toggleJumlahVt() {
      var status = $('#status').val() || 'NEGOTIATION';
      var $jumlahVt = $('#jumlah_vt');
      var $info = $jumlahVt.closest('.form-group').find('.help-block');
      var allowedInputStatuses = ['NEGOTIATION', 'ASSIGNED', 'ACTIVE', 'COMPLETED'];

      if (status === 'REJECTED') {
        $('#rejection_reason_group').slideDown();
      } else {
        $('#rejection_reason_group').slideUp();
      }

      if (allowedInputStatuses.indexOf(status) !== -1) {
        $jumlahVt.prop('readonly', false)
          .css({ 'background-color': '#ffffff', 'cursor': 'text' })
          .attr('placeholder', 'Jumlah Post/VT');
        if (status === 'NEGOTIATION') {
          $info.html('<span class="text-info"><i class="fa fa-info-circle"></i> Status NEGOTIATION dapat memasukkan estimasi Jumlah Post/VT. Konfigurasi detail task akan aktif setelah status menjadi <b>ASSIGNED, ACTIVE, atau COMPLETED</b>.</span>');
        } else {
          $info.html('<span class="text-muted">Masukkan jumlah Post / VT untuk KOL ini.</span>');
        }
        renderTaskCards();
      } else {
        $jumlahVt.prop('readonly', true)
          .css({ 'background-color': '#eeeeee', 'cursor': 'not-allowed' })
          .val('')
          .attr('placeholder', 'Hanya dapat diisi jika Status Assign adalah NEGOTIATION, ASSIGNED, ACTIVE, atau COMPLETED');
        $info.html('<span class="text-warning"><i class="fa fa-info-circle"></i> Jumlah Post/VT dinonaktifkan ketika Status Assign adalah <b>REJECTED atau CANCELLED</b>.</span>');
        renderTaskCards();
      }
    }

    toggleJumlahVt();
    $('#status').on('change', function () {
      toggleJumlahVt();
    });

    $('#jumlah_vt').on('input change keyup', function () {
      renderTaskCards();
    });

  }); /*end doc ready*/
</script>
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
<style>
  #kol_multi_chosen.chosen-with-drop .chosen-drop {
    display: block !important;
    left: 0 !important;
  }
  #kol_multi_results li.kol-result-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    cursor: pointer;
    list-style: none;
    transition: background-color 0.1s ease;
  }
  #kol_multi_results li.kol-result-item:hover:not(.disabled-result) {
    background-color: #3c8dbc !important;
    color: #ffffff !important;
  }
  #kol_multi_results li.kol-result-item.result-selected {
    background-color: #f0fdf4;
    font-weight: 600;
  }
  #kol_multi_results li.kol-result-item.result-selected:hover:not(.disabled-result) {
    background-color: #3c8dbc !important;
    color: #ffffff !important;
  }
  #kol_multi_results li.kol-result-item.disabled-result {
    opacity: 0.6;
    cursor: not-allowed;
  }
  #kol_multi_clear_btn {
    cursor: pointer;
  }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Campaign Kols <small><?= cclang('new', ['Campaign Kols']); ?> </small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class=""><a href="<?= site_url('administrator/campaign_kols'); ?>">Campaign Kols</a></li>
    <li class="active"><?= cclang('new'); ?></li>
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
              <h5 class="widget-user-desc"><?= cclang('new', ['Campaign Kols']); ?></h5>
              <hr>
            </div>
            <?= form_open('', [
              'name' => 'form_campaign_kols',
              'class' => 'form-horizontal',
              'id' => 'form_campaign_kols',
              'enctype' => 'multipart/form-data',
              'method' => 'POST'
            ]); ?>

            <div class="form-group ">
              <label for="campaign_id" class="col-sm-2 control-label">Campaign
                <i class="required">*</i>
              </label>
              <div class="col-sm-8">
                <select class="form-control chosen chosen-select-deselect" name="campaign_id" id="campaign_id"
                  data-placeholder="Pilih Campaign...">
                  <option value=""></option>
                  <?php foreach (db_get_all_data('campaigns') as $row): ?>
                    <option value="<?= $row->id ?>" data-sourcing-model="<?= $row->sourcing_model; ?>">
                      <?= _ent($row->name); ?> (<?= $row->sourcing_model === 'OPEN_POST' ? 'Open Post' : 'Appointed'; ?>)
                    </option>
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
                <!-- Hidden multi-select for form submission -->
                <select name="kol_id[]" id="kol_id" multiple style="display:none;">
                  <?php 
                    $kols_data = $this->db->select('k.*, t.name as tier_name')
                      ->from('kols k')
                      ->join('tiers t', 't.id = k.tier_id', 'left')
                      ->where('k.status', 'ACTIVE')
                      ->order_by('k.name', 'ASC')
                      ->get()->result();
                  ?>
                  <?php foreach ($kols_data as $row): ?>
                    <option value="<?= $row->id; ?>"><?= _ent($row->name); ?></option>
                  <?php endforeach; ?>
                </select>

                <!-- Identical Campaign-Styled Multi-Select Dropdown Container -->
                <div class="chosen-container chosen-container-single" id="kol_multi_chosen" style="width: 100%;">
                  <a class="chosen-single chosen-default" id="kol_multi_trigger" href="javascript:void(0);" tabindex="0">
                    <span id="kol_multi_display_text">Pilih KOL...</span>
                    <abbr class="search-choice-close" id="kol_multi_clear_btn" style="display: none;" title="Kosongkan pilihan"></abbr>
                    <div><b></b></div>
                  </a>
                  <div class="chosen-drop" id="kol_multi_dropdown" style="display: none;">
                    <div class="chosen-search">
                      <input type="text" id="kol_multi_search_input" autocomplete="off" placeholder="Cari KOL..."/>
                    </div>
                    <ul class="chosen-results" id="kol_multi_results" style="max-height: 240px; overflow-y: auto;">
                      <?php foreach ($kols_data as $row): ?>
                        <?php 
                          $tier_badge = !empty($row->tier_name) ? ('[' . $row->tier_name . '] ') : '';
                          $is_bl = !empty($row->is_blacklisted);
                          $display_label = $tier_badge . _ent($row->name) . ($is_bl ? ' ⚠️ [BLACKLISTED: ' . _ent($row->blacklist_reason ?: 'Tidak Direkomendasikan') . ']' : '');
                        ?>
                        <li class="active-result kol-result-item <?= $is_bl ? 'disabled-result' : ''; ?>" 
                            data-kol-id="<?= $row->id; ?>" 
                            data-kol-name="<?= _ent($row->name); ?>"
                            data-kol-label="<?= $tier_badge . _ent($row->name); ?>"
                            data-is-blacklisted="<?= $is_bl ? '1' : '0'; ?>"
                            style="display: flex; align-items: center; gap: 8px; padding: 6px 12px; cursor: <?= $is_bl ? 'not-allowed' : 'pointer'; ?>; <?= $is_bl ? 'color:#dc2626; opacity: 0.6;' : ''; ?>">
                          <input type="checkbox" class="kol-item-checkbox" value="<?= $row->id; ?>" <?= $is_bl ? 'disabled' : ''; ?> style="margin: 0; pointer-events: none; cursor: pointer;">
                          <span class="kol-item-text" style="flex: 1;"><?= $display_label; ?></span>
                        </li>
                      <?php endforeach; ?>
                      <li class="no-results" id="kol_multi_no_results" style="display: none; padding: 12px 10px; text-align: center;">
                        <div style="color: #64748b; font-size: 13px; margin-bottom: 8px;">Tidak ada KOL yang cocok.</div>
                        <button type="button" class="btn btn-xs btn-primary btn-flat" id="btn_quick_add_from_search" style="font-weight: 600; font-size: 11px;">
                          <i class="fa fa-plus"></i> Tambah "<span id="quick_add_search_name_preview"></span>" sebagai KOL
                        </button>
                      </li>
                    </ul>
                    <!-- Clean Dropdown Footer -->
                    <div class="kol-dropdown-footer" style="padding: 8px 12px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                      <span style="color: #64748b; font-size: 12px;"><i class="fa fa-info-circle text-muted"></i> KOL belum terdaftar?</span>
                      <button type="button" class="btn btn-xs btn-primary btn-flat" id="btn_open_quick_add_modal" style="font-weight: 600; padding: 4px 10px;">
                        <i class="fa fa-plus"></i> Tambah KOL
                      </button>
                    </div>
                  </div>
                </div>

                <small class="info help-block">
                </small>
              </div>
            </div>


            <div class="form-group ">
              <label for="deal_scope" class="col-sm-2 control-label">Brief
              </label>
              <div class="col-sm-8">
                <div id="campaign_kols_deal_scope_galery"></div>
                <div id="campaign_kols_deal_scope_galery_listed"></div>
                <small class="info help-block">
                </small>
              </div>
            </div>

            <div class="form-group " style="display:none;">
              <label for="status" class="col-sm-2 control-label">Status Assign
                <i class="required">*</i>
              </label>
              <div class="col-sm-8">
                <select class="form-control chosen chosen-select" name="status" id="status"
                  data-placeholder="Select Status">
                  <option value=""></option>
                  <option value="REJECTED">REJECTED</option>
                  <option value="COMPLETED">COMPLETED</option>
                  <option value="ACTIVE">ACTIVE</option>
                  <option value="ASSIGNED">ASSIGNED</option>
                  <option value="NEGOTIATION">NEGOTIATION</option>
                  <option value="CANCELLED">CANCELLED</option>
                </select>
                <small class="info help-block">
                </small>
              </div>
            </div>

            <div class="form-group ">
              <label for="jumlah_vt" class="col-sm-2 control-label">Jumlah Post/VT
              </label>
              <div class="col-sm-8">
                <input type="number" class="form-control" name="jumlah_vt" id="jumlah_vt" placeholder="Jumlah Post/VT"
                  value="<?= set_value('jumlah_vt'); ?>">
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
                  <input type="text" class="form-control pull-right datetimepicker" name="assigned_at" id="assigned_at">
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
                  <input type="text" class="form-control pull-right datetimepicker" name="deal_date" id="deal_date">
                </div>
                <small class="info help-block">
                </small>
              </div>
            </div>

            <div class="form-group " style="display:none;">
              <label for="task_count" class="col-sm-2 control-label">Task Count
              </label>
              <div class="col-sm-8">
                <input type="number" class="form-control" name="task_count" id="task_count" placeholder="Task Count"
                  value="<?= set_value('task_count'); ?>">
                <small class="info help-block">
                </small>
              </div>
            </div>

            <div class="form-group " style="display:none;">
              <label for="task_completed_count" class="col-sm-2 control-label">Task Complete
              </label>
              <div class="col-sm-8">
                <input type="number" class="form-control" name="task_completed_count" id="task_completed_count"
                  placeholder="Task Complete" value="<?= set_value('task_completed_count'); ?>">
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
                  <option value="IDR" <?= set_value('currency_code', 'IDR') == 'IDR' ? 'selected' : ''; ?>>IDR</option>
                  <option value="USD" <?= set_value('currency_code') == 'USD' ? 'selected' : ''; ?>>USD</option>
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

<!-- Quick Add KOL Modal -->
<div class="modal fade" id="modal_quick_add_kol" tabindex="-1" role="dialog" aria-labelledby="modalQuickAddKolLabel">
  <div class="modal-dialog modal-sm" role="document" style="margin-top: 120px;">
    <div class="modal-content" style="border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); border: none;">
      <div class="modal-header" style="background-color: #3c8dbc; color: #fff; border-top-left-radius: 4px; border-top-right-radius: 4px; padding: 12px 15px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.85;"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalQuickAddKolLabel" style="font-size: 15px; font-weight: 600;">
          <i class="fa fa-user-plus"></i> Tambah KOL Baru
        </h4>
      </div>
      <div class="modal-body" style="padding: 16px 18px;">
        <div class="form-group" style="margin-bottom: 0;">
          <label for="modal_quick_add_kol_name" style="font-size: 13px; font-weight: 600; color: #333;">Nama KOL <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="modal_quick_add_kol_name" placeholder="Masukkan nama KOL..." autocomplete="off" style="border-radius: 3px;">
          <p class="help-block" style="font-size: 11px; color: #888; margin-top: 6px; margin-bottom: 0;">
            <i class="fa fa-info-circle"></i> Hanya nama saja. Data profil & rate card lainnya dapat dilengkapi nanti.
          </p>
        </div>
      </div>
      <div class="modal-footer" style="padding: 10px 15px; background-color: #f9fafc; border-top: 1px solid #e5e5e5; display: flex; justify-content: flex-end; gap: 8px;">
        <button type="button" class="btn btn-default btn-sm btn-flat" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary btn-sm btn-flat" id="btn_modal_submit_kol" style="font-weight: 600;">
          <i class="fa fa-save"></i> Simpan & Pilih
        </button>
      </div>
    </div>
  </div>
</div>
<!-- /.content -->
<!-- Page script -->
<script>
  $(document).ready(function () {

    $(document).on('input', '#deal_amount, input[name^="tasks"][name$="[budget]"]', function () {
      var val = $(this).val().replace(/[^0-9]/g, '');
      if (val) {
        $(this).val(parseInt(val, 10).toLocaleString('id-ID'));
      } else {
        $(this).val('');
      }
    });

    $('#btn_cancel').click(function () {
      swal({
        title: "<?= cclang('are_you_sure'); ?>",
        text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
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
      var data_post = form_campaign_kols.serializeArray();
      $.each(data_post, function (i, field) {
        if (field.name === 'deal_amount' || /^tasks\[\d+\]\[budget\]$/.test(field.name)) {
          field.value = field.value.replace(/\./g, '');
        }
      });
      var save_type = $(this).attr('data-stype');

      data_post.push({ name: 'save_type', value: save_type });

      $('.loading').show();

      $.ajax({
        url: BASE_URL + '/administrator/campaign_kols/add_save',
        type: 'POST',
        dataType: 'json',
        data: data_post,
      })
        .done(function (res) {
          console.log('[Campaign KOLs Add Save Response]', res);
          if (res.success) {

            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }

            $('.message').printMessage({ message: res.message });
            $('.message').fadeIn();
            resetForm();
            $('#campaign_kols_deal_scope_galery').find('li').each(function () {
              $('#campaign_kols_deal_scope_galery').fineUploader('deleteFile', $(this).attr('qq-file-id'));
            });
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
            $('#kol_multi_clear_btn').trigger('click');

          } else {
            console.warn('[Campaign KOLs Add Save Error]', res.message);
            $('.message').printMessage({ message: res.message, type: 'warning' });
          }

        })
        .fail(function (xhr, status, error) {
          console.error('[Campaign KOLs Add Save AJAX Failed]', status, error, xhr.responseText);
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
    var existingTasks = [];

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
        html += '    <input type="text" class="form-control input-sm input-idr" name="tasks[' + i + '][budget]" placeholder="0" value="' + budget + '" style="border-radius: 3px;" />';
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

    // Identical Campaign-Styled Multi-Select Dropdown Logic
    var $kolMultiChosen = $('#kol_multi_chosen');
    var $kolMultiDropdown = $('#kol_multi_dropdown');
    var $kolMultiTrigger = $('#kol_multi_trigger');
    var $kolMultiSearch = $('#kol_multi_search_input');
    var $kolMultiText = $('#kol_multi_display_text');
    var $kolMultiClear = $('#kol_multi_clear_btn');
    var $kolSelect = $('#kol_id');

    function updateKolMultiDisplay() {
      var selectedLabels = [];
      var selectedValues = [];

      $('#kol_multi_results .kol-result-item').each(function () {
        var $chk = $(this).find('.kol-item-checkbox');
        if ($chk.is(':checked')) {
          selectedValues.push($(this).data('kol-id'));
          selectedLabels.push($(this).data('kol-name') || $(this).data('kol-label'));
        }
      });

      // Sync hidden multi-select
      $kolSelect.find('option').prop('selected', false);
      $.each(selectedValues, function (i, val) {
        $kolSelect.find('option[value="' + val + '"]').prop('selected', true);
      });

      if (selectedLabels.length === 0) {
        $kolMultiText.text('Pilih KOL...');
        $kolMultiTrigger.addClass('chosen-default');
        $kolMultiClear.hide();
      } else if (selectedLabels.length === 1) {
        $kolMultiText.text(selectedLabels[0]);
        $kolMultiTrigger.removeClass('chosen-default');
        $kolMultiClear.show();
      } else {
        var preview = selectedLabels.slice(0, 2).join(', ');
        if (selectedLabels.length > 2) {
          preview += ', ...';
        }
        $kolMultiText.text(selectedLabels.length + ' KOL Terpilih (' + preview + ')');
        $kolMultiTrigger.removeClass('chosen-default');
        $kolMultiClear.show();
      }
    }

    function openKolDropdown() {
      $kolMultiChosen.addClass('chosen-container-active chosen-with-drop');
      $kolMultiDropdown.show();
      setTimeout(function () {
        $kolMultiSearch.focus();
      }, 30);
    }

    function closeKolDropdown() {
      $kolMultiChosen.removeClass('chosen-container-active chosen-with-drop');
      $kolMultiDropdown.hide();
    }

    $kolMultiTrigger.on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      if ($kolMultiChosen.hasClass('chosen-with-drop')) {
        closeKolDropdown();
      } else {
        openKolDropdown();
      }
    });

    $(document).on('click', function (e) {
      if (!$(e.target).closest('#kol_multi_chosen').length) {
        closeKolDropdown();
      }
    });

    // Prevent clicks inside dropdown from closing it
    $kolMultiDropdown.on('click', function (e) {
      e.stopPropagation();
    });

    // Real-time search inside dropdown
    $kolMultiSearch.on('input keyup', function () {
      var query = $(this).val().toLowerCase().trim();
      var rawQuery = $(this).val().trim();
      var visibleCount = 0;

      $('#kol_multi_results .kol-result-item').each(function () {
        var text = $(this).text().toLowerCase();
        if (text.indexOf(query) !== -1) {
          $(this).show();
          visibleCount++;
        } else {
          $(this).hide();
        }
      });

      if (visibleCount === 0) {
        if (rawQuery !== '') {
          $('#quick_add_search_name_preview').text(rawQuery);
          $('#btn_quick_add_from_search').show();
        } else {
          $('#btn_quick_add_from_search').hide();
        }
        $('#kol_multi_no_results').show();
      } else {
        $('#kol_multi_no_results').hide();
      }
    });

    // Quick Add KOL Logic via Modal
    var $modalQuickAdd = $('#modal_quick_add_kol');
    var $modalKolNameInput = $('#modal_quick_add_kol_name');
    var $btnModalSubmitKol = $('#btn_modal_submit_kol');

    function openQuickAddModal(prefilledName) {
      closeKolDropdown();
      $modalKolNameInput.val(prefilledName || '');
      $modalQuickAdd.modal('show');
    }

    $modalQuickAdd.on('shown.bs.modal', function() {
      $modalKolNameInput.focus();
    });

    $('#btn_open_quick_add_modal').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var currentSearch = $kolMultiSearch.val().trim();
      openQuickAddModal(currentSearch);
    });

    $('#btn_quick_add_from_search').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      openQuickAddModal($kolMultiSearch.val().trim());
    });

    $modalKolNameInput.on('keydown', function(e) {
      if (e.which === 13) { // Enter key inside modal
        e.preventDefault();
        e.stopPropagation();
        $btnModalSubmitKol.trigger('click');
        return false;
      }
    });

    $btnModalSubmitKol.on('click', function(e) {
      e.preventDefault();
      var kolName = $.trim($modalKolNameInput.val());
      if (!kolName) {
        toastr['warning']('Silakan masukkan nama KOL.');
        $modalKolNameInput.focus();
        return;
      }

      var origBtnHtml = $btnModalSubmitKol.html();
      $btnModalSubmitKol.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

      var postData = { name: kolName };
      if (typeof csrf !== 'undefined') {
        var tokenVal = (typeof token !== 'undefined') ? token : $('input[name="' + csrf + '"]').val();
        if (tokenVal) {
          postData[csrf] = tokenVal;
        }
      }

      $.ajax({
        url: BASE_URL + 'administrator/campaign_kols/quick_add_kol',
        type: 'POST',
        dataType: 'json',
        data: postData
      })
      .done(function(res) {
        if (res.success && res.kol) {
          var kol = res.kol;
          var kId = kol.id;
          var kName = kol.name;
          var tierBadge = kol.tier_name ? ('[' + kol.tier_name + '] ') : '';
          var displayLabel = tierBadge + kName;

          // Check if option in #kol_id exists
          if (!$kolSelect.find('option[value="' + kId + '"]').length) {
            $kolSelect.append($('<option>', {
              value: kId,
              text: kName,
              selected: true
            }));
          } else {
            $kolSelect.find('option[value="' + kId + '"]').prop('selected', true);
          }

          // Check if item in #kol_multi_results exists
          var $existingItem = $('#kol_multi_results .kol-result-item[data-kol-id="' + kId + '"]');
          if ($existingItem.length) {
            $existingItem.removeClass('disabled-result').addClass('result-selected').show();
            $existingItem.find('.kol-item-checkbox').prop('checked', true).prop('disabled', false);
          } else {
            var newLi = $(
              '<li class="active-result kol-result-item result-selected" ' +
              'data-kol-id="' + kId + '" ' +
              'data-kol-name="' + $('<div>').text(kName).html() + '" ' +
              'data-kol-label="' + $('<div>').text(displayLabel).html() + '" ' +
              'data-is-blacklisted="0" ' +
              'style="display: flex; align-items: center; gap: 8px; padding: 6px 12px; cursor: pointer;">' +
              '<input type="checkbox" class="kol-item-checkbox" value="' + kId + '" checked style="margin: 0; pointer-events: none; cursor: pointer;">' +
              '<span class="kol-item-text" style="flex: 1;">' + $('<div>').text(displayLabel).html() + '</span>' +
              '</li>'
            );
            $('#kol_multi_results').prepend(newLi);
          }

          // Reset inputs and search
          $modalKolNameInput.val('');
          $kolMultiSearch.val('');
          $('#kol_multi_results .kol-result-item').show();
          $('#kol_multi_no_results').hide();

          updateKolMultiDisplay();

          $modalQuickAdd.modal('hide');
          toastr['success'](res.message || 'KOL berhasil ditambahkan!');
        } else {
          toastr['error'](res.message || 'Gagal menambahkan KOL.');
        }
      })
      .fail(function(xhr, status, error) {
        console.error('Quick Add KOL failed:', status, error, xhr.responseText);
        toastr['error']('Terjadi kesalahan saat menambahkan KOL.');
      })
      .always(function() {
        $btnModalSubmitKol.prop('disabled', false).html(origBtnHtml);
      });
    });

    // Prevent clicks inside dropdown footer from closing dropdown unexpectedly
    $('.kol-dropdown-footer').on('click', function(e) {
      e.stopPropagation();
    });

    // Click on option row toggles selection
    $('#kol_multi_results').on('click', '.kol-result-item', function (e) {
      e.preventDefault();
      e.stopPropagation();

      if ($(this).hasClass('disabled-result')) {
        return;
      }

      var $chk = $(this).find('.kol-item-checkbox');
      var nowChecked = !$chk.is(':checked');
      $chk.prop('checked', nowChecked);

      if (nowChecked) {
        $(this).addClass('result-selected');
      } else {
        $(this).removeClass('result-selected');
      }

      updateKolMultiDisplay();
    });

    // Clear all button (x)
    $kolMultiClear.on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      $('#kol_multi_results .kol-result-item:not(.disabled-result)').each(function () {
        $(this).removeClass('result-selected');
        $(this).find('.kol-item-checkbox').prop('checked', false);
      });

      updateKolMultiDisplay();
    });

    // Keyboard support: Escape closes dropdown
    $kolMultiSearch.on('keydown', function (e) {
      if (e.which === 27) { // Escape
        closeKolDropdown();
      }
    });

    // Handle Campaign Selection
    $('#campaign_id').on('change', function () {
      var campaignId = $(this).val();

      if (!campaignId) {
        return;
      }

      // Check existing assigned KOLs for this campaign to prevent duplicates
      $.ajax({
        url: BASE_URL + 'administrator/campaign_kols/get_campaign_assigned_kols/' + campaignId,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
          if (res.success && res.assigned_kol_ids) {
            var assignedIds = res.assigned_kol_ids.map(Number);

            $('#kol_multi_results .kol-result-item').each(function () {
              var kId = parseInt($(this).data('kol-id'));
              var isBlacklisted = $(this).data('is-blacklisted') == '1';
              var $chk = $(this).find('.kol-item-checkbox');
              var origText = $(this).data('orig-text') || $(this).find('.kol-item-text').text();
              $(this).data('orig-text', origText);

              if (assignedIds.indexOf(kId) !== -1) {
                $(this).addClass('disabled-result').removeClass('result-selected');
                $chk.prop('disabled', true).prop('checked', false);
                $(this).find('.kol-item-text').text(origText.replace(' (Sudah Terdaftar)', '') + ' (Sudah Terdaftar)');
                $(this).css({ opacity: '0.6', cursor: 'not-allowed' });
              } else if (!isBlacklisted) {
                $(this).removeClass('disabled-result');
                $chk.prop('disabled', false);
                $(this).find('.kol-item-text').text(origText.replace(' (Sudah Terdaftar)', ''));
                $(this).css({ opacity: '1', cursor: 'pointer' });
              }
            });

            updateKolMultiDisplay();
          }
        }
      });
    });

    updateKolMultiDisplay();

  }); /*end doc ready*/
</script>
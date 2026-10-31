<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>

<script type="text/javascript">
function domo(){
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

<?php $this->load->view('core_template/fine_upload'); ?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= cclang('brands'); ?> <small><?= cclang('new', ['Brands']); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/brands'); ?>"><?= cclang('brands'); ?></a></li>
        <li class="active"><?= cclang('new'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
                <div class="box-body" style="padding: 20px;">
                    <div class="box box-widget widget-user-2" style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid #e7edf3; margin-bottom: 20px;">
                            <div>
                                <h3 style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;">
                                    <i class="fa fa-plus-circle text-primary"></i> Tambah Brand Baru
                                </h3>
                                <div style="margin-top: 5px; color: #6b778c; font-size: 13px;">Daftarkan entitas brand baru ke dalam sistem</div>
                            </div>
                            <div>
                                <a href="<?= site_url('administrator/brands'); ?>" class="btn btn-default btn-flat">
                                    <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>

                    <?= form_open(base_url('administrator/brands/add_save'), [
                        'name'    => 'form_brands', 
                        'class'   => 'form-horizontal', 
                        'id'      => 'form_brands', 
                        'enctype' => 'multipart/form-data', 
                        'method'  => 'POST'
                    ]); ?>

                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Nama Brand <i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="name" id="name" placeholder="Nama Brand (contoh: Metafora Parfume, Somethinc Glow)" value="<?= set_value('name'); ?>" maxlength="150" required>
                            <small class="info help-block">Nama resmi brand atau merek dagang.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="code" class="col-sm-2 control-label">Kode Brand <i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="code" id="code" placeholder="Kode Unik (contoh: METAFORA, SOMETHINC)" value="<?= set_value('code'); ?>" maxlength="50" style="text-transform: uppercase;" required>
                            <small class="info help-block">Kode unik slug brand dalam huruf kapital tanpa spasi (maksimal 50 karakter).</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="company_name" class="col-sm-2 control-label">Badan Hukum (PT) <i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Nama PT / Badan Hukum Resmi (contoh: PT Metafora Multi Perkasa)" value="<?= set_value('company_name'); ?>" maxlength="200" required>
                            <small class="info help-block">Nama badan hukum resmi untuk pencantuman pada kontrak perjanjian (MoU).</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="legal_entity_name" class="col-sm-2 control-label">Alias Badan Hukum</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="legal_entity_name" id="legal_entity_name" placeholder="Alias Badan Hukum (opsional)" value="<?= set_value('legal_entity_name'); ?>" maxlength="255">
                            <small class="info help-block">Nama alias legal entity bila berbeda dari nama PT.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="logo" class="col-sm-2 control-label">Logo Brand</label>
                        <div class="col-sm-8">
                            <div id="brands_logo_galery"></div>
                            <input name="brands_logo_uuid" id="brands_logo_uuid" type="hidden" value="<?= set_value('brands_logo_uuid'); ?>">
                            <input name="brands_logo_name" id="brands_logo_name" type="hidden" value="<?= set_value('brands_logo_name'); ?>">
                            <input type="file" name="logo" id="logo" class="form-control" style="display: none;" accept="image/*">
                            <small class="info help-block">Format file yang didukung: PNG, JPG, JPEG, SVG (Maks. 2MB).</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="col-sm-2 control-label">Telepon / WhatsApp</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="phone" id="phone" placeholder="Nomor Telepon Kantor / WhatsApp (contoh: 081234567890)" value="<?= set_value('phone'); ?>" maxlength="50">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">Email Resmi</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" name="email" id="email" placeholder="contact@brand.id" value="<?= set_value('email'); ?>" maxlength="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="website" class="col-sm-2 control-label">Website</label>
                        <div class="col-sm-8">
                            <input type="url" class="form-control" name="website" id="website" placeholder="https://brand.id" value="<?= set_value('website'); ?>" maxlength="255">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pic_name" class="col-sm-2 control-label">Penanggung Jawab (PIC)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="pic_name" id="pic_name" placeholder="Nama Penanggung Jawab Brand" value="<?= set_value('pic_name'); ?>" maxlength="150">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="col-sm-2 control-label">Alamat Kantor</label>
                        <div class="col-sm-8">
                            <textarea id="address" name="address" rows="3" class="form-control" placeholder="Alamat lengkap domisili kantor brand"><?= set_value('address'); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-2 control-label">Deskripsi Brand</label>
                        <div class="col-sm-8">
                            <textarea id="description" name="description" rows="3" class="form-control" placeholder="Profil atau keterangan singkat mengenai brand"><?= set_value('description'); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="is_active" class="col-sm-2 control-label">Status Brand <i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <select class="form-control chosen chosen-select" name="is_active" id="is_active">
                                <option value="1" <?= set_value('is_active', '1') == '1' ? 'selected' : ''; ?>>Active (Aktif)</option>
                                <option value="0" <?= set_value('is_active') === '0' ? 'selected' : ''; ?>>Inactive (Non-aktif)</option>
                            </select>
                            <small class="info help-block">Brand yang dinonaktifkan tidak dapat digunakan untuk campaign baru dan akses staf akan diblokir.</small>
                        </div>
                    </div>

                    <div class="message" style="margin-top: 15px;"></div>

                    <div class="row-fluid col-md-7 col-md-offset-2" style="padding-left: 0; margin-top: 20px;">
                        <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="Simpan (Ctrl+s)">
                            <i class="fa fa-save"></i> <?= cclang('save_button'); ?>
                        </button>
                        <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save_back" data-stype='back' title="Simpan & Kembali (Ctrl+d)">
                            <i class="ion ion-ios-list-outline"></i> <?= cclang('save_and_go_the_list_button'); ?>
                        </a>
                        <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="Batal (Ctrl+x)" href="<?= site_url('administrator/brands'); ?>">
                            <i class="fa fa-undo"></i> <?= cclang('cancel_button'); ?>
                        </a>
                        <span class="loading loading-hide" style="margin-left: 10px;">
                            <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg"> <i><?= cclang('loading_saving_data'); ?></i>
                        </span>
                    </div>

                    <?= form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Page script -->
<script>
$(document).ready(function() {
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

    $('.btn_save').click(function() {
        $('.message').fadeOut();

        var form_brands = $('#form_brands');
        var data_post = form_brands.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({
            name: 'save_type',
            value: save_type
        });

        $('.loading').show();

        $.ajax({
            url: BASE_URL + 'administrator/brands/add_save',
            type: 'POST',
            dataType: 'json',
            data: data_post,
        })
        .done(function(res) {
            if (res.success) {
                if (save_type == 'back') {
                    window.location.href = res.redirect || (BASE_URL + 'administrator/brands');
                    return;
                }

                $('.message').printMessage({
                    message: res.message
                });
                $('.message').fadeIn();
                $('form input[type != hidden], form textarea').val('');
                $('#is_active').val('1').trigger('chosen:updated');
                if ($('#brands_logo_galery').find('li').length) {
                    var fileId = $('#brands_logo_galery').find('li').attr('qq-file-id');
                    $('#brands_logo_galery').fineUploader('deleteFile', fileId);
                }
            } else {
                $('.message').printMessage({
                    message: res.message,
                    type: 'warning'
                });
                $('.message').fadeIn();
            }
        })
        .fail(function() {
            $('.message').printMessage({
                message: 'Terjadi kesalahan koneksi server saat menyimpan data.',
                type: 'warning'
            });
            $('.message').fadeIn();
        })
        .always(function() {
            $('.loading').hide();
            $('html, body').animate({
                scrollTop: $(document).height()
            }, 500);
        });

        return false;
    });

    $('#brands_logo_galery').fineUploader({
        template: 'qq-template-gallery',
        request: {
            endpoint: BASE_URL + 'administrator/brands/upload_logo_file',
            params: {
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            }
        },
        deleteFile: {
            enabled: true,
            endpoint: BASE_URL + 'administrator/brands/delete_logo_file'
        },
        thumbnails: {
            placeholders: {
                waitingPath: BASE_URL + 'asset/fine-upload/placeholders/waiting-generic.png',
                notAvailablePath: BASE_URL + 'asset/fine-upload/placeholders/not_available-generic.png'
            }
        },
        multiple: false,
        validation: {
            allowedExtensions: ['jpeg', 'jpg', 'gif', 'png', 'svg']
        },
        showMessage: function(msg) {
            toastr['error'](msg);
        },
        callbacks: {
            onComplete: function(id, name) {
                var uuid = $('#brands_logo_galery').fineUploader('getUuid', id);
                $('#brands_logo_uuid').val(uuid);
                $('#brands_logo_name').val(name);
            },
            onSubmit: function(id, name) {
                var uuid = $('#brands_logo_uuid').val();
                if (uuid) {
                    $.get(BASE_URL + 'administrator/brands/delete_logo_file/' + uuid);
                }
            }
        }
    });
});
</script>


<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/select2/select2.min.css">
<style>
.select2-container--default .select2-selection--multiple {
    border: 1px solid #d2d6de;
    border-radius: 4px;
    min-height: 38px;
    padding: 2px 6px;
    background: #fff;
    box-shadow: none;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #3c8dbc;
    outline: 0;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
    border-radius: 4px;
    padding: 3px 8px;
    font-size: 13px;
    font-weight: 500;
    margin: 3px 5px 3px 0;
    line-height: 1.4;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #2563eb;
    margin-right: 6px;
    font-weight: bold;
    cursor: pointer;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #1e40af;
}
.select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
    margin-top: 5px;
    padding: 2px 4px;
    line-height: 22px;
}
</style>

<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
    function domo() {
        $('*').bind('keydown', 'Ctrl+s', function () {
            $('#btn_save').trigger('click');
            return false;
        });

        $('*').bind('keydown', 'Ctrl+x', function () {
            $('#btn_cancel').trigger('click');
            return false;
        });

        $('*').bind('keydown', 'Ctrl+d', function () {
            $('.btn_save_back').trigger('click');
            return false;
        });
    }

    jQuery(document).ready(domo);
</script>

<section class="content-header">
    <h1>
        Artikel <small>Edit</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/articles'); ?>">Artikel</a></li>
        <li class="active">Edit</li>
    </ol>
</section>

<?= form_open(base_url('administrator/articles/edit_save/' . $article->id), [
    'name'    => 'form_articles',
    'class'   => 'form-horizontal',
    'id'      => 'form_articles',
    'enctype' => 'multipart/form-data',
    'method'  => 'POST',
]); ?>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
                <div class="box-body" style="padding: 24px 28px;">
                    <div style="padding-bottom: 18px; border-bottom: 1px solid #e7edf3; margin-bottom: 24px;">
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <h3 style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;">Edit Artikel</h3>
                            <span style="display: inline-flex; align-items: center; border-radius: 999px; background: #eef2f7; color: #42526e; font-size: 11px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; padding: 4px 9px;"><?= _ent($article->status); ?></span>
                        </div>
                        <div style="margin-top: 5px; color: #6b778c; font-size: 13px;">
                            Terakhir diperbarui <?= _ent($article->updated_at); ?>.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label">Judul <i class="required">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="title" id="title" placeholder="Judul artikel" value="<?= set_value('title', $article->title); ?>">
                            <small class="info help-block">Maksimal 200 karakter.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="col-sm-2 control-label">Slug</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="slug" id="slug" placeholder="slug-artikel" value="<?= set_value('slug', $article->slug); ?>">
                            <small class="info help-block">Kosongkan untuk membuat slug otomatis dari judul.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category" class="col-sm-2 control-label">Kategori</label>
                        <div class="col-sm-10">
                            <?php
                            $selected_categories = array_filter(array_map('trim', explode(',', (string) $article->category)));
                            ?>
                            <select class="form-control select2" name="category[]" id="category" multiple="multiple" style="width: 100%;" data-placeholder="Pilih atau ketik kategori baru...">
                                <?php
                                $existing_cat_names = [];
                                foreach ($categories as $category):
                                    $existing_cat_names[] = $category->category;
                                    $is_sel = in_array($category->category, $selected_categories, true);
                                ?>
                                    <option value="<?= _ent($category->category); ?>" <?= $is_sel ? 'selected' : ''; ?>><?= _ent($category->category); ?></option>
                                <?php endforeach; ?>
                                <?php foreach ($selected_categories as $sel_cat): ?>
                                    <?php if (!in_array($sel_cat, $existing_cat_names, true)): ?>
                                        <option value="<?= _ent($sel_cat); ?>" selected><?= _ent($sel_cat); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <small class="info help-block">Pilih kategori yang tersedia atau ketik nama kategori baru lalu tekan <b>Enter</b>.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tags" class="col-sm-2 control-label">Tag</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="tags" id="tags" placeholder="KOL, Campaign, Insight" value="<?= set_value('tags', $article->tags); ?>">
                            <small class="info help-block">Pisahkan dengan koma.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="excerpt" class="col-sm-2 control-label">Ringkasan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="excerpt" id="excerpt" rows="3" maxlength="500" placeholder="Ringkasan singkat untuk kartu artikel"><?= set_value('excerpt', $article->excerpt); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="content" class="col-sm-2 control-label">Konten <i class="required">*</i></label>
                        <div class="col-sm-10">
                            <textarea id="content" name="content" rows="12" class="form-control"><?= set_value('content', $article->content); ?></textarea>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #e7edf3; margin: 24px 0 20px;"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Cover Image</label>
                        <div class="col-sm-10">
                            <div id="cover_image_gallery"></div>
                            <div id="cover_image_listed">
                                <?php if (!empty($article->cover_image)): ?>
                                    <div style="margin-top: 10px; display: inline-flex; align-items: center; gap: 12px; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 12px; background: #f8fafc;">
                                        <img src="<?= base_url('uploads/articles/' . $article->cover_image); ?>" alt="" style="height: 48px; width: 72px; object-fit: cover; border-radius: 4px;">
                                        <div>
                                            <div style="font-size: 13px; font-weight: 600; color: #1e293b;"><?= _ent($article->cover_image); ?></div>
                                            <label style="font-size: 12px; color: #ef4444; cursor: pointer; font-weight: normal; margin-top: 2px;">
                                                <input type="checkbox" name="remove_cover" value="1"> Hapus cover saat disimpan
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <small class="info help-block">Format: JPG, JPEG, PNG, WEBP. Maks 2MB.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <?php if ($this->aauth->is_allowed('articles_publish')): ?>
                                <select class="form-control select2" name="status" id="status" style="max-width: 240px;">
                                    <option value="DRAFT" <?= $article->status === 'DRAFT' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="PUBLISHED" <?= $article->status === 'PUBLISHED' ? 'selected' : ''; ?>>Published</option>
                                    <option value="ARCHIVED" <?= $article->status === 'ARCHIVED' ? 'selected' : ''; ?>>Archived</option>
                                </select>
                            <?php else: ?>
                                <p class="form-control-static">
                                    <span class="label label-default"><?= _ent($article->status); ?></span>
                                    <input type="hidden" name="status" value="<?= _ent($article->status); ?>">
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Featured</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_featured" value="1" <?= $article->is_featured ? 'checked' : ''; ?>>
                                    Jadikan artikel pilihan (tampil di posisi utama)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #e7edf3; margin: 24px 0 20px;"></div>

                    <div class="form-group">
                        <label for="meta_title" class="col-sm-2 control-label">Meta title</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="meta_title" id="meta_title" placeholder="Judul untuk SEO (opsional)" value="<?= set_value('meta_title', $article->meta_title); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="meta_description" class="col-sm-2 control-label">Meta description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="meta_description" id="meta_description" rows="3" maxlength="320" placeholder="Deskripsi untuk SEO"><?= set_value('meta_description', $article->meta_description); ?></textarea>
                        </div>
                    </div>

                    <div class="message"></div>

                    <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e7edf3;">
                        <button type="button" class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype="stay" title="Simpan (Ctrl+s)">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                        <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save_back" data-stype="back" title="Simpan dan kembali (Ctrl+d)">
                            <i class="ion ion-ios-list-outline"></i> Simpan &amp; Kembali
                        </a>
                        <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="Batal (Ctrl+x)">
                            <i class="fa fa-undo"></i> Batal
                        </a>
                        <span class="loading loading-hide" style="display: none; margin-left: 10px;">
                            <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg" style="width: 20px; height: 20px;">
                            <i>Menyimpan data...</i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= form_close(); ?>

<script src="<?= BASE_ASSET; ?>admin-lte/plugins/select2/select2.full.min.js"></script>
<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
<script>
    $(document).ready(function () {
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('content', { height: 420 });
        }

        if ($.fn.select2) {
            $('#category').select2({
                tags: true,
                tokenSeparators: [','],
                placeholder: 'Pilih atau ketik kategori baru...',
                width: '100%'
            });

            $('#status').select2({
                width: '240px'
            });
        }

        var slugEdited = $('#slug').val() !== '';
        $('#slug').data('edited', slugEdited);

        $('#title').on('keyup blur', function () {
            if ($('#slug').data('edited') === true) {
                return;
            }

            $('#slug').val(slugify($(this).val()));
        });

        $('#slug').on('keyup blur', function () {
            $(this).data('edited', $(this).val() !== '');
            $(this).val(slugify($(this).val()));
        });

        function slugify(value) {
            return String(value || '')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        var params = {};
        if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
            params[csrf] = token;
        }

        if ($.fn.fineUploader) {
            $('#cover_image_gallery').fineUploader({
                template: 'qq-template-gallery',
                request: {
                    endpoint: BASE_URL + 'administrator/articles/upload_cover_file',
                    params: params
                },
                deleteFile: {
                    enabled: true,
                    endpoint: BASE_URL + 'administrator/articles/delete_cover_file'
                },
                thumbnails: {
                    placeholders: {
                        waitingPath: BASE_URL + 'asset/fine-upload/placeholders/waiting-generic.png',
                        notAvailablePath: BASE_URL + 'asset/fine-upload/placeholders/not_available-generic.png'
                    }
                },
                validation: {
                    itemLimit: 1,
                    allowedExtensions: ['jpg', 'jpeg', 'png', 'webp'],
                    sizeLimit: 2097152
                },
                showMessage: function (msg) {
                    toastr.error(msg);
                },
                callbacks: {
                    onComplete: function (id, name, xhr) {
                        if (xhr.success) {
                            var uuid = $('#cover_image_gallery').fineUploader('getUuid', id);
                            $('#cover_image_listed').html(
                                '<input type="hidden" name="cover_image_uuid" value="' + uuid + '">' +
                                '<input type="hidden" name="cover_image_name" value="' + xhr.uploadName + '">'
                            );
                        } else {
                            toastr.error(xhr.error);
                        }
                    },
                    onDeleteComplete: function () {
                        $('#cover_image_listed').html('');
                    }
                }
            });
        }

        $('#btn_cancel').click(function () {
            if (typeof swal === 'function') {
                swal({
                    title: "Batalkan?",
                    text: "Perubahan yang belum disimpan akan hilang!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Ya, Batalkan!",
                    cancelButtonText: "Tidak",
                    closeOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        window.location.href = BASE_URL + 'administrator/articles';
                    }
                });
            } else if (confirm('Batalkan perubahan artikel?')) {
                window.location.href = BASE_URL + 'administrator/articles';
            }
            return false;
        });

        $('.btn_save').click(function () {
            $('.message').fadeOut();

            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.content) {
                $('#content').val(CKEDITOR.instances.content.getData());
            }

            if ($.trim($('#title').val()) === '') {
                toastr.warning('Judul artikel wajib diisi.');
                $('#title').focus();
                return false;
            }

            var form_articles = $('#form_articles');
            var data_post = form_articles.serializeArray();
            var save_type = $(this).attr('data-stype');

            data_post.push({ name: 'save_type', value: save_type });

            $('.loading').show();

            $.ajax({
                url: BASE_URL + 'administrator/articles/edit_save/<?= (int) $article->id; ?>',
                type: 'POST',
                dataType: 'json',
                data: data_post
            })
            .done(function (res) {
                if (res.success) {
                    if (save_type === 'back') {
                        window.location.href = res.redirect || (BASE_URL + 'administrator/articles');
                        return;
                    }

                    $('.message').printMessage({ message: res.message });
                    $('.message').fadeIn();
                    toastr.success('Artikel berhasil diperbarui.');
                } else {
                    $('.message').printMessage({ message: res.message, type: 'warning' });
                }
            })
            .fail(function (xhr, status, error) {
                var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : error;
                $('.message').printMessage({ message: 'Terjadi kesalahan saat menyimpan data: ' + errMsg, type: 'warning' });
            })
            .always(function () {
                $('.loading').hide();
                $('html, body').animate({ scrollTop: $(document).height() }, 800);
            });

            return false;
        });
    });
</script>

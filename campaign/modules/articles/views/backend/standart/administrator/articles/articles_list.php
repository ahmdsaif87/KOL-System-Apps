
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
    function domo() {
        $('*').bind('keydown', 'Ctrl+a', function () {
            window.location.href = BASE_URL + 'administrator/articles/add';
            return false;
        });

        $('*').bind('keydown', 'Ctrl+f', function () {
            $('#sbtn').trigger('click');
            return false;
        });

        $('*').bind('keydown', 'Ctrl+x', function () {
            $('#reset').trigger('click');
            return false;
        });
    }

    jQuery(document).ready(domo);
</script>

<section class="content-header">
    <h1>
        Artikel <small>Semua artikel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Artikel</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
                <div class="box-body" style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid #e7edf3; margin-bottom: 20px; flex-wrap: wrap;">
                        <div>
                            <h3 style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;">Artikel</h3>
                            <div style="margin-top: 5px; color: #6b778c; font-size: 13px;">
                                Kelola konten yang tampil di landing page.
                                <span style="display: inline-block; margin-left: 6px; padding: 2px 7px; border-radius: 10px; background: #e8f1fb; color: #1f4b6e; font-weight: 700;"><?= (int) $articles_counts; ?> item</span>
                            </div>
                        </div>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end;">
                            <?php is_allowed('articles_add', function () { ?>
                                <a class="btn btn-primary btn-flat" href="<?= site_url('administrator/articles/add'); ?>">
                                    <i class="fa fa-plus"></i> Artikel Baru
                                </a>
                            <?php }); ?>
                        </div>
                    </div>

                    <form name="form_articles" id="form_articles" action="<?= base_url('administrator/articles/index'); ?>" method="get">
                        <style>
                          .prod-filter-bar { margin-bottom: 14px; padding: 12px 12px 4px 12px; background: #f8fafc; border: 1px solid #e7edf3; border-radius: 6px; }
                          .prod-filter-bar .form-control { height: 38px; border-radius: 4px; margin-bottom: 8px; }
                          .prod-filter-bar .btn { width: 100%; min-height: 38px; margin-bottom: 8px; border-radius: 4px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
                          .prod-filter-bar .pf-sep { border-top: 1px dashed #e2e8f0; margin: 4px 0 12px 0; }
                          @media (max-width: 768px) {
                            #form_articles .prod-filter-bar > .pf-half { width: calc(50% - 3px) !important; max-width: calc(50% - 3px) !important; float: left !important; display: block !important; }
                            #form_articles .prod-filter-bar > .pf-half + .pf-half { margin-left: 6px !important; }
                            #form_articles .prod-filter-bar > .pf-bulkmain { width: calc(100% - 50px) !important; max-width: calc(100% - 50px) !important; float: left !important; display: block !important; }
                            #form_articles .prod-filter-bar > .pf-bulkbtn { width: 44px !important; max-width: 44px !important; float: left !important; display: block !important; margin-left: 6px !important; }
                            #form_articles button[name="sbtn"] { width: 100% !important; min-width: 0 !important; max-width: none !important; font-size: 13px !important; }
                            #form_articles button[name="sbtn"]::before { content: none !important; }
                            #form_articles a[name="reset"] { width: 100% !important; min-width: 0 !important; max-width: none !important; }
                            #form_articles a[name="reset"] .reset-txt { display: none !important; }
                          }
                        </style>

                        <div class="row prod-filter-bar" style="margin-left:0; margin-right:0;">
                            <div class="col-xs-12 col-sm-3" style="padding-left:4px; padding-right:4px;">
                                <input type="text" class="form-control" name="q" id="filter" placeholder="Cari judul artikel..." value="<?= _ent($this->input->get('q')); ?>">
                            </div>
                            <div class="col-xs-12 col-sm-2" style="padding-left:4px; padding-right:4px;">
                                <select class="form-control" name="f" id="field">
                                    <option value="">Semua kolom</option>
                                    <option <?= $this->input->get('f') === 'title' ? 'selected' : ''; ?> value="title">Judul</option>
                                    <option <?= $this->input->get('f') === 'slug' ? 'selected' : ''; ?> value="slug">Slug</option>
                                    <option <?= $this->input->get('f') === 'author_name' ? 'selected' : ''; ?> value="author_name">Penulis</option>
                                    <option <?= $this->input->get('f') === 'tags' ? 'selected' : ''; ?> value="tags">Tags</option>
                                </select>
                            </div>
                            <div class="col-xs-12 col-sm-2" style="padding-left:4px; padding-right:4px;">
                                <select class="form-control" name="status" id="status_filter">
                                    <option value="">Semua status</option>
                                    <option value="DRAFT" <?= $this->input->get('status') === 'DRAFT' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="PUBLISHED" <?= $this->input->get('status') === 'PUBLISHED' ? 'selected' : ''; ?>>Published</option>
                                    <option value="ARCHIVED" <?= $this->input->get('status') === 'ARCHIVED' ? 'selected' : ''; ?>>Archived</option>
                                </select>
                            </div>
                            <div class="col-xs-12 col-sm-3" style="padding-left:4px; padding-right:4px;">
                                <select class="form-control" name="category" id="category_filter">
                                    <option value="">Semua kategori</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= _ent($category->category); ?>" <?= $this->input->get('category') === $category->category ? 'selected' : ''; ?>><?= _ent($category->category); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-xs-6 col-sm-1 pf-half" style="padding-left:4px; padding-right:4px;">
                                <button type="submit" class="btn btn-primary btn-flat" name="sbtn" id="sbtn" value="Apply" title="Filter search"><i class="fa fa-search"></i> Filter</button>
                            </div>
                            <div class="col-xs-6 col-sm-1 pf-half" style="padding-left:4px; padding-right:4px;">
                                <a class="btn btn-default btn-flat" name="reset" id="reset" href="<?= base_url('administrator/articles'); ?>" title="Reset filter"><i class="fa fa-undo"></i><span class="reset-txt"> Reset</span></a>
                            </div>
                            <div class="col-xs-12 pf-sep" style="padding-left:4px; padding-right:4px;"></div>
                            <div class="col-xs-8 col-sm-2 pf-bulkmain" style="padding-left:4px; padding-right:4px;">
                                <select class="form-control" name="bulk" id="bulk">
                                    <option value="">Bulk</option>
                                    <option value="delete">Delete</option>
                                </select>
                            </div>
                            <div class="col-xs-4 col-sm-2 pf-bulkbtn" style="padding-left:4px; padding-right:4px;">
                                <button type="button" class="btn btn-default btn-flat" name="apply" id="apply" title="Apply bulk action"><i class="fa fa-check"></i> Apply</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover" style="margin-bottom: 0; border: 1px solid #e7edf3; border-radius: 6px;">
                                <thead>
                                    <tr style="background: #f6f8fb; color: #52606d; font-size: 11px; text-transform: uppercase; letter-spacing: 0.02em;">
                                        <th style="width: 34px;"><input type="checkbox" class="flat-red" id="check_all" title="Pilih semua"></th>
                                        <th style="width: 90px;">Cover</th>
                                        <th>Judul</th>
                                        <th>Kategori</th>
                                        <th>Penulis</th>
                                        <th>Status</th>
                                        <th>Terbit</th>
                                        <th>Views</th>
                                        <th style="width: 150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($articles as $article): ?>
                                        <tr>
                                            <td><input type="checkbox" class="flat-red check" name="id[]" value="<?= (int) $article->id; ?>"></td>
                                            <td>
                                                <?php if (!empty($article->cover_image) && is_file(FCPATH . 'uploads/articles/' . $article->cover_image)): ?>
                                                    <img src="<?= base_url('uploads/articles/' . $article->cover_image); ?>" alt="" style="width: 64px; height: 42px; object-fit: cover; border-radius: 4px;">
                                                <?php else: ?>
                                                    <span class="label label-default" style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 42px; border-radius: 4px;">
                                                        <i class="fa fa-picture-o"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= _ent($article->title); ?></strong>
                                                <div style="color: #6b778c; font-size: 12px;">/artikel/<?= _ent($article->slug); ?></div>
                                                <?php if (!empty($article->is_featured)): ?>
                                                    <span class="label label-info" style="font-size: 10px;">Featured</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= !empty($article->category) ? _ent($article->category) : '-'; ?></td>
                                            <td><?= !empty($article->author_name) ? _ent($article->author_name) : 'Admin'; ?></td>
                                            <td>
                                                <?php
                                                $status_class = 'label-default';
                                                if ($article->status === 'PUBLISHED') {
                                                    $status_class = 'label-success';
                                                } elseif ($article->status === 'ARCHIVED') {
                                                    $status_class = 'label-warning';
                                                }
                                                ?>
                                                <span class="label <?= $status_class; ?>"><?= _ent($article->status); ?></span>
                                            </td>
                                            <td><?= !empty($article->published_at) ? _ent($article->published_at) : '-'; ?></td>
                                            <td><?= (int) $article->views; ?></td>
                                            <td style="vertical-align: middle; white-space: nowrap;">
                                                <div style="display: flex; gap: 4px; align-items: center; white-space: nowrap;">
                                                    <?php is_allowed('articles_view', function () use ($article) { ?>
                                                        <a href="<?= site_url('administrator/articles/view/' . $article->id); ?>" class="btn btn-xs btn-default btn-flat" title="Lihat"><i class="fa fa-eye"></i></a>
                                                    <?php }); ?>

                                                    <?php is_allowed('articles_update', function () use ($article) { ?>
                                                        <a href="<?= site_url('administrator/articles/edit/' . $article->id); ?>" class="btn btn-xs btn-default btn-flat" title="Edit"><i class="fa fa-pencil"></i></a>
                                                    <?php }); ?>

                                                    <?php is_allowed('articles_publish', function () use ($article) { ?>
                                                        <?php if ($article->status !== 'PUBLISHED'): ?>
                                                            <button type="button" class="btn btn-xs btn-success btn-flat js-status" data-id="<?= (int) $article->id; ?>" data-action="publish" title="Publish"><i class="fa fa-check"></i></button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-xs btn-warning btn-flat js-status" data-id="<?= (int) $article->id; ?>" data-action="unpublish" title="Unpublish"><i class="fa fa-pause"></i></button>
                                                        <?php endif; ?>

                                                        <?php if ($article->status !== 'ARCHIVED'): ?>
                                                            <button type="button" class="btn btn-xs btn-default btn-flat js-status" data-id="<?= (int) $article->id; ?>" data-action="archive" title="Archive"><i class="fa fa-archive"></i></button>
                                                        <?php endif; ?>
                                                    <?php }); ?>

                                                    <?php is_allowed('articles_delete', function () use ($article) { ?>
                                                        <a href="javascript:void(0);" data-href="<?= site_url('administrator/articles/delete/' . $article->id); ?>" class="btn btn-xs btn-default btn-flat remove-data" title="Hapus"><i class="fa fa-trash"></i></a>
                                                    <?php }); ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php if (empty($articles)): ?>
                                        <tr>
                                            <td colspan="9" style="text-align: center; padding: 30px; color: #6b778c;">
                                                Belum ada artikel. Klik <strong>Artikel Baru</strong> untuk mulai menulis.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="row" style="margin-top: 16px;">
                            <div class="col-md-12 text-right">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    <?= $pagination; ?>
                                </div>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function () {
        $('.remove-data').click(function () {
            var url = $(this).attr('data-href');

            swal({
                title: "<?= cclang('are_you_sure'); ?>",
                text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
                cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    document.location.href = url;
                }
            });

            return false;
        });

        $(document).on('click', '.js-status', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var id = $btn.data('id');
            var action = $btn.data('action');
            var payload = {};

            var csrfName = typeof csrf !== 'undefined' ? csrf : 'kol_sys_csrf_token';
            var csrfToken = typeof token !== 'undefined' ? token : '';
            payload[csrfName] = csrfToken;

            $btn.prop('disabled', true).addClass('disabled');

            $.ajax({
                url: BASE_URL + 'administrator/articles/' + action + '/' + id,
                type: 'POST',
                dataType: 'json',
                data: payload
            })
            .done(function (res) {
                if (res.success) {
                    toastr.success(res.message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 400);
                } else {
                    toastr.error(res.message || 'Gagal mengubah status artikel.');
                    $btn.prop('disabled', false).removeClass('disabled');
                }
            })
            .fail(function (xhr, status, error) {
                var msg = 'Gagal mengubah status artikel.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
                $btn.prop('disabled', false).removeClass('disabled');
            });
        });

        $('#apply').click(function () {
            var bulk = $('#bulk').val();

            if (bulk === '') {
                toastr.warning('<?= cclang('please_choose_bulk_action_first'); ?>');
                return false;
            }

            if (bulk === 'delete') {
                var serialized = $('#form_articles').serialize();

                swal({
                    title: "<?= cclang('are_you_sure'); ?>",
                    text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
                    cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        document.location.href = BASE_URL + 'administrator/articles/delete?' + serialized;
                    }
                });
            }

            return false;
        });

        var checkAll = $('#check_all');
        var checkboxes = $('input.check');

        checkAll.on('ifChecked ifUnchecked', function (event) {
            if (event.type === 'ifChecked') {
                checkboxes.iCheck('check');
            } else {
                checkboxes.iCheck('uncheck');
            }
        });

        checkboxes.on('ifChanged', function () {
            if (checkboxes.filter(':checked').length === checkboxes.length) {
                checkAll.prop('checked', 'checked');
            } else {
                checkAll.removeProp('checked');
            }
            checkAll.iCheck('update');
        });
    });
</script>

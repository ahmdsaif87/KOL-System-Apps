
<section class="content-header">
    <h1>
        Artikel <small>Detail</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/articles'); ?>">Artikel</a></li>
        <li class="active">Detail</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
                <div class="box-body" style="padding: 24px;">
                    <?php if (!empty($article->cover_image)): ?>
                        <img src="<?= base_url('uploads/articles/' . $article->cover_image); ?>" alt="<?= _ent($article->title); ?>" style="width: 100%; height: auto; border-radius: 8px; margin-bottom: 20px;">
                    <?php endif; ?>

                    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-bottom: 12px;">
                        <?php if (!empty($article->category)): ?>
                            <span class="label label-primary"><?= _ent($article->category); ?></span>
                        <?php endif; ?>
                        <?php
                        $status_class = 'label-default';
                        if ($article->status === 'PUBLISHED') {
                            $status_class = 'label-success';
                        } elseif ($article->status === 'ARCHIVED') {
                            $status_class = 'label-warning';
                        }
                        ?>
                        <span class="label <?= $status_class; ?>"><?= _ent($article->status); ?></span>
                        <?php if (!empty($article->is_featured)): ?>
                            <span class="label label-info">Featured</span>
                        <?php endif; ?>
                    </div>

                    <h1 style="margin-top: 0; font-size: 30px; line-height: 1.25; color: #172b4d;"><?= _ent($article->title); ?></h1>

                    <p style="color: #6b778c; margin-bottom: 18px;">
                        <?= !empty($article->author_name) ? _ent($article->author_name) : 'Admin'; ?>
                        &middot; <?= !empty($article->published_at) ? _ent($article->published_at) : 'Belum diterbitkan'; ?>
                        &middot; <?= (int) $article->views; ?> views
                    </p>

                    <?php if (!empty($article->excerpt)): ?>
                        <blockquote style="font-size: 16px; color: #434655;"><?= _ent($article->excerpt); ?></blockquote>
                    <?php endif; ?>

                    <div class="article-content-preview" style="font-size: 15px; line-height: 1.75; color: #263445;">
                        <?= $article->content; ?>
                    </div>

                    <?php if (!empty($article->tags)): ?>
                        <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #e7edf3;">
                            <?php foreach (array_filter(array_map('trim', explode(',', $article->tags))) as $tag): ?>
                                <span class="label label-default" style="margin-right: 4px;"><?= _ent($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title">Informasi</h3>
                </div>
                <div class="box-body" style="padding: 20px;">
                    <dl class="dl-horizontal">
                        <dt>Slug</dt>
                        <dd><code>/artikel/<?= _ent($article->slug); ?></code></dd>

                        <dt>Status</dt>
                        <dd><?= _ent($article->status); ?></dd>

                        <dt>Penulis</dt>
                        <dd><?= !empty($article->author_name) ? _ent($article->author_name) : 'Admin'; ?></dd>

                        <dt>Dibuat</dt>
                        <dd><?= _ent($article->created_at); ?></dd>

                        <dt>Diperbarui</dt>
                        <dd><?= _ent($article->updated_at); ?></dd>

                        <dt>Terbit</dt>
                        <dd><?= !empty($article->published_at) ? _ent($article->published_at) : '-'; ?></dd>

                        <dt>Views</dt>
                        <dd><?= (int) $article->views; ?></dd>
                    </dl>

                    <div style="margin-top: 16px; display: flex; gap: 6px; flex-wrap: wrap;">
                        <?php is_allowed('articles_update', function () use ($article) { ?>
                            <a href="<?= site_url('administrator/articles/edit/' . $article->id); ?>" class="btn btn-primary btn-flat btn-sm">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                        <?php }); ?>
                        <a href="<?= site_url('administrator/articles'); ?>" class="btn btn-default btn-flat btn-sm">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title">SEO</h3>
                </div>
                <div class="box-body" style="padding: 20px;">
                    <strong>Meta title</strong>
                    <p><?= !empty($article->meta_title) ? _ent($article->meta_title) : _ent($article->title); ?></p>

                    <strong>Meta description</strong>
                    <p><?= !empty($article->meta_description) ? _ent($article->meta_description) : (!empty($article->excerpt) ? _ent($article->excerpt) : '-'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

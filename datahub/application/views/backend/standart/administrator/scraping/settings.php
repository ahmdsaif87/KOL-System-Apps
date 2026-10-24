<section class="content-header">
    <h1>Scraping <small>Settings</small></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Scraping Settings</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            
            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-cogs"></i> Apify Configuration</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form" method="POST" action="<?= site_url('administrator/scraping_settings') ?>">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="save" value="1">
                    
                    <div class="box-body">
                        <div class="callout callout-info">
                            <p><i class="fa fa-info-circle"></i> This system uses Apify to scrape social media profiles (TikTok, IG, YouTube) to gather accurate follower counts and calculate engagement rates.</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="apify_token">Apify API Token</label>
                            <input type="text" class="form-control" id="apify_token" name="apify_token" value="<?= htmlspecialchars($apify_token) ?>" required placeholder="Enter Apify API Token">
                            <p class="help-block">Find this in your Apify Account Settings under Integrations.</p>
                        </div>
                        <div class="form-group">
                            <label for="apify_actor_id">Apify Actor ID</label>
                            <input type="text" class="form-control" id="apify_actor_id" name="apify_actor_id" value="<?= htmlspecialchars($apify_actor_id) ?>" required placeholder="e.g. yourusername/instagram-scraper">
                            <p class="help-block">The unique ID of the actor you want to trigger.</p>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

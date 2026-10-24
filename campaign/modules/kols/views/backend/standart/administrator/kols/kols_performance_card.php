<!-- Content Header -->
<section class="content-header">
   <h1>
      Performance Card <small><?= cclang('detail', ['Kols']); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/kols'); ?>">Kols</a></li>
      <li><a href="<?= site_url('administrator/kols/view/'.$kol_id); ?>"><?= cclang('detail'); ?></a></li>
      <li class="active">Performance Card</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
            <div class="box-body" style="padding: 0; min-height: 800px; background: #f8fafc; position: relative;">
                <iframe id="perfCardIframe" src="<?= $iframe_url; ?>" style="width: 100%; height: 1200px; min-height: 800px; border: none; display: block;" allowfullscreen></iframe>
            </div>
            <div class="box-footer" style="padding: 15px;">
                <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/kols/'); ?>"><i class="fa fa-undo"></i> <?= cclang('go_list_button', ['Kols']); ?></a>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
window.addEventListener('message', function(e) {
    if (e.data && e.data.type === 'setIframeHeight') {
        var frame = document.getElementById('perfCardIframe');
        if (frame && e.data.height) {
            frame.style.height = (parseInt(e.data.height) + 30) + 'px';
        }
    }
});
</script>

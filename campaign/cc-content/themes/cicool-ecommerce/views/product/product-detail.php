<?=get_header();?>
<body id="page-top">
   <?=get_navigation();?>
   <?=get_view_component('search-nav');?>
<div class="container" style="margin-top: 140px;">
</div>
<?=cicool()->eventListen('coshop_product_detail_top');?>
<div class="container">

  <div class="col-md-12">
    <div class="breadcrumb-product">
      <span class="breadcrumb-item"><a href="">Home</a></span>
      <span class="breadcrumb-chevron"><span class="fa fa-chevron-right"></span></span>
      <span class="breadcrumb-item"><a href="">Souvenir</a></span>
      <span class="breadcrumb-chevron"><span class="fa fa-chevron-right"></span></span>
      <span class="breadcrumb-item"><a href="">Jaket</a></span>
      <span class="breadcrumb-chevron"><span class="fa fa-chevron-right"></span></span>
      <span class="breadcrumb-item-current">Jaket lukis murah meriah</span>
    </div>
  </div>
</div>

<div class="container ">

  <div class="col-md-4 ">
    <div class="img-product-detail-wrapper">
      <img src="<?= $product->getImageCover() ?>" alt="" class="img-product-detail">
    </div>
    <div class="img-product-galery-detail-wrapper">
      <?php $i = 0; foreach($product->getImages() as $image): ?>
      <div class="img-product-galery-wrapper <?= $i == 0 ? 'active' : '' ?>" style="
      background: url('<?= $image ?>');background-size: contain; background-repeat: no-repeat; 
      " data-src="<?= $image ?>">
      </div>
      <?php $i++; endforeach ?>
    </div>
  </div>
  <div class="col-md-6">
    <div href="" class="product-detail-title">
      <?= $product->getName() ?>
    </div>
    <div class="rating-product-detail-wrapper">
      <img src="https://cdn.tokopedia.net/img/star-4-new.png" class="product-detail-rating" alt=""><span class="product-detail-rating-count">4 ratings</span>
    </div>
    <div class="price-product-detail-wrapper">
      <span class="price-product-detail">USD <?= $product->getPriceFormat() ?></span>
    </div>
    <div class="qty-product-detail-wrapper">
      <div class="col-md-4 padd-left-0">
          <div class="qty-divider"><b>Quantity</b></div>
          <div class="qty-product-detail-counter">
            <a href="" class="button-qty-min" title=""><i class="fa fa-minus-circle"></i></a> 
            <input type="" name="" class="counter-qty" readonly="" min="2" max="<?= $product->getStock() ?>" value="1">
            <a href="" class="button-qty-plus" title=""><i class="fa fa-plus-circle"></i></a>
          </div>
      </div>
      <div class="col-md-8 padd-left-0">
          <div class="qty-divider"><b>Notes For Merchant</b></div>
          <textarea class="notes-for-merchant" name="" placeholder="Example : white color, large size"></textarea>
          <div class="notes-info"><span class="current-char">0</span>/140 character</div>
      </div>
    </div>
    <div class="row"></div>
    <div class="button-product-detail-wrapper">
        <a href="" class="btn btn-primary btn-flat btn-add-chart-detail btn-lg " title=""><img src="<?= theme_asset() ?>img/icon-cart.png" alt=""> Add To Chart</a>
        <a href="" class="btn btn-primary btn-flat btn-buy-now-detail btn-lg " title="">Buy Now</a>
    </div>
  </div>
  <div class="col-md-2">
    <div class="box-share-product-detail">
    </div>
    
    <div class="box-merchant-product-detail">
        <center>

          <img src="https://ecs7.tokopedia.net/img/cache/100-square/shops-1/2017/11/7/24101125/24101125_df0f707b-c92c-4fa5-86b6-603d53f44e13.png" class="img-merchant-product-detail" alt="">
        </center>
      <div>
        <center>
          
          <a href="" class="merchant-name" title=""><?= $product->merchant->getName() ?></a>
          <div href="" class="merchant-location" title=""><i class="fa fa-map-marker"></i> Melborn</div>
          <div href="" class="merchant-last-online" title=""><i class="fa fa-clock-o"></i> Today</div>
        </center>
      </div>
    </div>

   <!--  <div class="box-promotion">
      <b>Promo</b>
    </div> -->
  </div>

    <div class="col-md-12">
      <div class="product-detail-tab-wrapper">
        <div role="tabpanel ">
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
              <a href="#home" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-list-alt icon-tab"></i> Product Information</a>
            </li>
            <li role="presentation">
              <a href="#review" aria-controls="review" role="tab" data-toggle="tab"><i class="fa fa-star-o icon-tab"></i> Reviews</a>
            </li>
            <!-- <li role="presentation">
              <a href="#discussion" aria-controls="discussion" role="tab" data-toggle="tab"><i class="fa fa-comments-o icon-tab"></i> Product Discussion</a>
            </li> -->
          </ul>
        
          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
              <?= $product->getDescription() ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="review">
             <div class="review-wrapper">
            
                 <div class="review-item-wrapper">
                  <div class="review-item-head">
                    <div class="review-star">
                      <i class="fa fa-star"></i>
                      <i class="fa fa-star"></i>
                      <i class="fa fa-star"></i>
                      <i class="fa fa-star muted"></i>
                      <i class="fa fa-star muted"></i>
                    </div>
                    <div class="review-tile">by <b>Muhamad Ridwan</b> <i class="review-date">19 dec</i></div>
                    <div class="review-content">Great product</div>
                  </div>
                  <div class="review-item-foot">
                    help you ? <a href="" title="" class="btn-help-review"><i class="fa fa-thumbs-o-up"></i></a>
                  </div>
                </div>


              </div> 
            </div>
            <div role="tabpanel" class="tab-pane" id="discussion">...</div>
          </div>
        </div>
      </div>
      
    </div>
    
</div>


<div class="container margin-top-30">
    <div class="col-md-12">
      <div class="product-related-title">Related Product</div>
    </div>
    <?php foreach($this->model_product->get(null, null, 6) as $prod): ?>
    <div class="col-md-2 col-sm-3 col-xs-6">
      <?=get_view_component('product-single-item', ['product' => $prod])?>
    </div>
    <?php endforeach;?>
</div>
<div class="container margin-top-30">
    <div class="col-md-12">
      <div class="product-related-title">Other Product In Store</div>
    </div>
    <?php foreach($this->model_product->get(null, null, 6) as $prod): ?>
    <div class="col-md-2 col-sm-3 col-xs-6">
      <?=get_view_component('product-single-item', ['product' => $prod])?>
    </div>
    <?php endforeach;?>
</div>

<style type="">
  body {
    background: #fff;
  }

  .product-item {
    border: 1px solid #C9C9C9;
  }
</style>
<script src="https://rawgit.com/jackmoore/autosize/master/dist/autosize.min.js"></script>

<script>

  var product = <?= json_encode($product->toArray()) ?>;

  $(function(){

    $('.img-product-galery-wrapper').hover(function(event) {
      $('.img-product-galery-wrapper').removeClass('active')
      $(this).addClass('active')
      var src = $(this).data('src');
      $('.img-product-detail').animate({
        opacity: 0.1,},
        200, function() {
        $('.img-product-detail').attr('src', src);
        $('.img-product-detail').animate({
        opacity: 1},
        200);
      });
    });

    $(document).on('click', 'a.btn-add-chart-detail', function(event){
      event.preventDefault();


      var notes = $('.notes-for-merchant').val();
      var qty = $('.counter-qty').val();

      $.ajax({
        url: window.base_url + 'product/add_cart',
        type: 'GET',
        dataType: 'JSON',
        data: {
          product : product.id,
          qty : qty,
          notes : notes,
        },
      })
      .done(function(res) {
          console.log(res)
        if (res.status) {
          $('.cart-counter').html(res.cart.total_items)
        }
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      

    });
    autosize($('.notes-for-merchant'));
    $('.notes-for-merchant').keydown(function(event) {
      var char = $(this).val();
      var length = char.length;
      if (length > 139) {
        $(this).val(char.substring(0,139));
      }
      var length = char.length;
      $('.current-char').html(length);

    });

    $('.button-qty-plus').click(function(event) {
      event.preventDefault();
      var current = parseInt($('.counter-qty').val());
      var min = $('.counter-qty').attr('min');
      var max = $('.counter-qty').attr('max');
      if (Number.isNaN(current)) {
        $('.counter-qty').val(0);
      } else {
        if (current < max) {
          $('.counter-qty').val(current+=1);
        }
      }

    });
    $('.button-qty-min').click(function(event) {
      event.preventDefault();
      var min = $('.counter-qty').attr('min');
      var max = $('.counter-qty').attr('max');
      var current = parseInt($('.counter-qty').val());
      if (Number.isNaN(current)) {
        $('.counter-qty').val(0);
      } else {
        if (current > min) {
          $('.counter-qty').val(current-=1);
        }
      }

    });

  })
</script>
<?=get_footer();?>
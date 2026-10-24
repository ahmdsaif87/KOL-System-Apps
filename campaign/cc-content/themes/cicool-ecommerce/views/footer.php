
<div class="footer-wrapper">
    <div class="container">
        <div class="col-md-3">
            <span class="footer-title">CUSTOMER SERVICE</span>
            <ul class="footer-link-section">
                <li><a href="" class="footer-link" title="">Support</a></li>
                <li><a href="" class="footer-link" title="">Support</a></li>
                <li><a href="" class="footer-link" title="">Support</a></li>
                <li><a href="" class="footer-link" title="">Support</a></li>
            </ul>
        </div>
        <div class="col-md-3">
            <span class="footer-title">EXPLORE CICOOL</span>
            <ul class="footer-link-section">
                <li><a href="" class="footer-link" title="">Support</a></li>
                <li><a href="" class="footer-link" title="">Support</a></li>
                <li><a href="" class="footer-link" title="">Support</a></li>
                <li><a href="" class="footer-link" title="">Support</a></li>
            </ul>
        </div>
        <div class="col-md-3">
            <span class="footer-title">PAYMENT</span>
            <ul class="footer-link-section">
                <!-- <li><a href="" class="footer-link" title=""><img src="https://stripe.com/img/v3/home/social.png" width="100px" alt=""></a></li>
                <li><a href="" class="footer-link" title=""><img src="https://www.ecommercebytes.com/wp-content/uploads/2017/06/paypal_lg.jpg" width="100px" alt=""></a></li> -->
            </ul>
            
        </div>
        <div class="col-md-3">
            <span class="footer-title">FFOLLOW US</span>
            <ul class="footer-link-section">
                <li><a href="" class="footer-link" title="">Support</a></li>
                <li><a href="" class="footer-link" title="">Support</a></li>
                <li><a href="" class="footer-link" title="">Support</a></li>
                <li><a href="" class="footer-link" title="">Support</a></li>
            </ul>
        </div>
        <div class="clearfix">
            
        </div>
        <div class="row">
            
        </div>
        <div class="footer-second">

            </div>

        <div class="col-md-3">
            <span class="footer-copy-right">Cicool <?= date('Y') ?>. Copy Right</span>
        </div>
        <div class="col-md-9">
            <span class="footer-copy-right pull-right">Language : English</span>
        </div>
    </div>
</div>


<script>
    $(function(){
        $(window).scroll(function(event) {
            if ($(window).scrollTop() > 160) {
                $('.navbar-search').css('marginTop', 0);
                //$('.header-first-top').slideUp('fast');
            } else {
                $('.navbar-search').animate({
                	marginTop: 30}, 10);
                //$('.header-first-top').slideDown('fast');
            }
        });

        $('.search-product').focusin(function(event) {
            $('.search-result-container,.menu-category-wrapper').show();
            $('.menu-category-container').hide();
        });
        $('.search-product').focusout(function(event) {
            setTimeout(function(){
                $('.search-result-container,.menu-category-wrapper').hide();
            }, 250);
        });

        $('.btn-cart, .cart-item-wrapper').hover(function() {
            $('.cart-item-wrapper').show();
        }, function(event) {
            $('.cart-item-wrapper').hide();
        });

        $('.menu-bar').click(function(event) {
            event.preventDefault();
            var toggle = $(this).data('toggle');

            if (toggle == '0' || toggle == undefined) {
                $(this).data('toggle', 1);
                $('.menu-category-container, .menu-category-wrapper').show();
                 setTimeout(function(){
                    $('.menu-category-wrapper').show();
                }, 252);
            } else {
                $(this).data('toggle', 0);
                $('.menu-category-container, .menu-category-wrapper').hide();
            }
        });

        $(document).click(function(event) {
           if (!$(event.target).closest('.menu-bar').length) {
              $('.menu-category-container').hide();
              if (!$(event.target).closest('.search-product').length) {
                $('.menu-category-wrapper').hide();
              }
           } 
        });
    })
</script>

<script type="text/javascript">
   var base_url = '<?= base_url() ?>';
   function inArray(needle, haystack) {
       var length = haystack.length;
       for(var i = 0; i < length; i++) {
           if(haystack[i] == needle) return true;
       }
       return false;
   }
   $(function(){
      var id_int = null;
      function search() {
         $.ajax({
            url: base_url + 'product/search',
            dataType: 'JSON',
            data: {q: $('.search-product').val()},
         })
         .done(function(res) {
            var product_search = ``;
            $.each(res.product, function(index, val) {
               var product_name_ex = val.product_name.split(' ');
               var terms = $('.search-product').val().toLowerCase().split(' ');
               var product_name_ex_string = ``;
               $.each(product_name_ex, function(index, val) {
                  product_name_ex_string += `<span class="search-item-`+val+` `+(inArray(val.toLowerCase(),terms) ? ' search-highlight' : '') +`">`+val+`</span> `;
               });
               product_search += `
                    <li>
                        <a href="`+base_url+`product/ca/all?q=`+$('.search-product').val()+`" title="">
                        `+product_name_ex_string+` <div class="search-desc">in man fashion</div>
                        </a>
                    </li>`;
            });
            var tpl_product = ` <ul class="result-product">
                  `+product_search+`
                </ul>
               `;

         var merchant = ` <div class="search-result-title">
                    MERCHANT
                </div>
                <ul class="result-merchant">
                    <li>
                        <a href="" title="">
                            <center>
                                
                        <img src="https://cf.shopee.co.id/file/f8afcd66cc257066b034c1b9551248c3_tn">  <div class="search-desc">in man fashion</div>
                            </center>
                        </a>
                    </li>
                </ul>`;
          $('.search-result-container').html(tpl_product+' '+merchant);
         })

         .fail(function() {
         })
         .always(function() {
            console.log("complete");
         });
      }
      $('.search-product').keyup(function(event) {
         clearTimeout(id_int);
         id_int = setTimeout(function(){
            if ($('.search-product').val().length > 1) {
               search();
            }
         }, 1000);
         
      });
   })
</script>


    <!-- jQuery -->
   
    <!-- Bootstrap Core JavaScript -->
    <script src="<?= theme_asset(); ?>/vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script src="<?= theme_asset(); ?>/vendor/scrollreveal/scrollreveal.min.js"></script>
    <script src="<?= theme_asset(); ?>/vendor/magnific-popup/jquery.magnific-popup.min.js"></script>

    <!-- Theme JavaScript -->
    <script src="<?= theme_asset(); ?>/js/creative.min.js"></script>

</body>

</html>

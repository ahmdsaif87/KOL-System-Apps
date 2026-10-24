
<nav id="mainNav" class="navbar navbar-default navbar-fixed-top navbar-search" style="margin-top: 30px;">
    <!-- /.container-fluid -->
    <div class="header-second-top">

     <div class="container">
        <div class="col-md-2">
            <img src="<?= theme_asset() ?>img/logo.png" alt="" class="main-logo">
            <a href="" title="" class="bar-menu">
             <!--    <i class="fa fa-bars" style="float: right;color: #fff; font-size: 30px;margin-top: 10px"></i> -->
             <img src="<?= theme_asset() ?>img/menu.svg" style="width:25px;float: right;color: #fff; font-size: 30px;margin-top: 12px" class="menu-bar">

            </a>
                 <div class="menu-category-container" style="display: none;">
                    <div class="shop-category-tile">SHOOP CATEGORY</div>
                    <ul>
                    <?php for($i=1; $i<10; $i++): ?>
                    <li><a href="" title="">Menu Item <img src="<?= theme_asset() ?>img/chevron.svg" class="menu-bar"></a></li>
                    <?php endfor ?>
                </ul>
                </div>
            <div class="menu-category-wrapper" style="display: none;">
                
            </div>

        </div>
        <div class="col-md-9">
            <a class="btn-search">
                <center><i class="fa fa-search"></i></center> </a>
            <input type="" name="" class="search-product" placeholder="Find product, store and brand"> 
            <div class="search-suggest-bottom">
                <a href="" class="suggest-item" title="">samsung a5</a>
                <a href="" class="suggest-item" title="">xiaomi redmi s2</a>
                <a href="" class="suggest-item" title="">t shirt</a>
                
            </div>

            <div class="search-result-container" style="display: none;">
               
            </div>
        </div>
        <div class="col-md-1">
            <a class="btn-cart"><img style="height:30px;" src="<?= theme_asset() ?>img/icon-cart.png" alt=""></a>

            <div class="cart-counter">19</div>
            <div class="cart-item-wrapper" style="display: none;">
                <center>
                    
                <img style="height:60px; margin-top: 50px;" src="<?= theme_asset() ?>img/bag.svg" alt="">
                <div class="cart-empty-label">Cart is Empty</div>
                </center>
            </div>
        </div>
     </div>
     </div>
</nav>

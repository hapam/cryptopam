<div class="mainMenuOuterWrapper">
            <!-- main menu wrapper starts -->
    <ul class="mainMenuWrapper">
        <li>
            <a href="{$base_url}">Trang chủ</a></li>
        {foreach from=$langs item=item key=k name=i}
            {if $k == $cur_lang}
                <li class="currentPage">
                    <a href="javascript:void(0)">{$item}</a></li>
            {else}
                <li>
                    <a href="javascript:void(0)" onclick="shop.lang.change('{$k}')" class="fl">{$item}</a></li>
            {/if}
        {/foreach}
        <li>
            <a href="javascript:shop.changeMode(1)">Phiên bản PC</a></li>
    </ul>
    <!-- main menu wrapper ends -->
    <div class="mainMenuBottomDecoration"></div>
</div>

<form id="shoppingCartWrapper" class="shoppingCartWrapper" action="#" method="post" />
    <fieldset>
    <!-- shopping cart product starts -->
    <div class="shoppingCartProductWrapper">
        <a href="" class="shoppingCartProductImageWrapper">
            <img src="themes/mobile/sogood/style/images/content/shoppingCartProductImage-3.jpg" class="shoppingCartProductImage" alt="" />
        </a>
        <div class="shoppingCartProductInfoWrapper"> <a href="" class="shoppingCartProductTitle">Product Three</a>
            <div class="shoppingCartProductButtonsWrapper">
                <input type="text" id="shoppingCartProductNumber-3" class="shoppingCartProductNumber" name="product-3" value="1" />
                <span class="shoppingCartProductPrice">$110</span>
                <a href="" class="shoppingCartRemoveProductButton"></a>
            </div>
        </div>
    </div>
    <!-- shopping cart product ends -->

    <!-- shopping cart info wrapper starts -->
    <div class="shoppingCartInfoWrapper">
        <span class="shoppingCartProductsNumber">Products: 3</span>
        <span class="shoppingCartProductsTotal">Total: $235</span> 
    </div>
    <!-- shopping cart info wrapper ends -->

    <div class="shoppingCartButtonsWrapper"><a href="" class="shoppingCartEmptyButton">Empty</a>
        <input type="submit" value="Checkout" id="shoppingCartCheckoutButton" class="shoppingCartCheckoutButton" />
    </div>
</fieldset>
</form>

<div class="headerOuterWrapper">
    <div class="headerWrapper">
        <a href="login.html" class="accountButton"></a>
        <a href="" class="shoppingCartButton"></a>
        <a href="" class="mainMenuButton"></a>
    </div>
    <div class="headerDecoration"></div>

    <!-- main logo starts -->
    <a href="" class="mainLogo">
        <img src="themes/mobile/sogood/style/images/common/mainLogo.png" alt="" />
    </a>
    <!-- main logo ends -->
</div>
{if $curPage == 'home' || $curPage == 'category'}
<div class="shopSearchFormWrapper">
    <form action="#" method="post" id="shopSearchForm">
    <fieldset>
        <input type="text" value="" id="shopSearchField" class="shopSearchField fieldWithIcon shopSearchFieldIcon" name="shopSearchField" placeholder="search..." />
    </fieldset>
    </form>
</div>
<!-- shop search form wrapper ends -->

{if $curPage == 'home'}
<!-- slider wrapper starts -->
<div class="sliderOuterWrapper">
    <div class="sliderWrapper">
        <div class="mainSlider" id="mainSlider">
            <a href="">
                <img src="themes/mobile/sogood/style/images/content/slide-1.jpg" alt="" />
            </a>
            <a href="">
                <img src="themes/mobile/sogood/style/images/content/slide-2.jpg" alt="" />
            </a>
            <a href="">
                <img src="themes/mobile/sogood/style/images/content/slide-3.jpg" alt="" />
            </a>
        </div>
    </div>
    <a href="" class="sliderControl previousSlideButton"></a>
    <a href="" class="sliderControl nextSlideButton"></a>
</div>
<!-- slider wrapper ends -->

<div class="textBreakBottom"></div>

<!-- new products start -->
<h4 class="sectionTitle">New Products:<a href="" class="sectionTitleLink">More &raquo;</a></h4>
{/if}

<div class="homeProductsWrapper">
    <div class="homeProductWrapper">
        <img src="themes/mobile/sogood/style/images/content/homeProduct-1.jpg" alt="" />
        <div class="homeProductInfoWrapper"><span class="homeProductTitle">Product #1</span><a href="" class="homePurchaseButton">($5) Buy</a>
        </div>
    </div>
    <div class="homeProductWrapper">
        <img src="themes/mobile/sogood/style/images/content/homeProduct-2.jpg" alt="" />
        <div class="homeProductInfoWrapper"><span class="homeProductTitle">Product #2</span><a href="" class="homePurchaseButton">($2) Buy</a>
        </div>
    </div>
    <div class="homeProductWrapper">
        <img src="themes/mobile/sogood/style/images/content/homeProduct-3.jpg" alt="" />
        <div class="homeProductInfoWrapper"><span class="homeProductTitle">Product #3</span><a href="" class="homePurchaseButton">($7) Buy</a>
        </div>
    </div>
    <div class="homeProductWrapper">
        <img src="themes/mobile/sogood/style/images/content/homeProduct-4.jpg" alt="" />
        <div class="homeProductInfoWrapper"><span class="homeProductTitle">Product #4</span><a href="" class="homePurchaseButton">($4) Buy</a>
        </div>
    </div>
</div>
<!-- new products end -->

<div class="textBreakBottom"></div>

<!-- recently viewed products start -->
<h4 class="sectionTitle">Recently Viewed:<a href="" class="sectionTitleLink">More &raquo;</a></h4>
<div class="homeProductsWrapper">
    <div class="homeProductWrapper">
        <img src="themes/mobile/sogood/style/images/content/homeProduct-1.jpg" alt="" />
        <div class="homeProductInfoWrapper"><span class="homeProductTitle">Product #1</span><a href="" class="homePurchaseButton">($5) Buy</a>
        </div>
    </div>
    <div class="homeProductWrapper">
        <img src="themes/mobile/sogood/style/images/content/homeProduct-2.jpg" alt="" />
        <div class="homeProductInfoWrapper"><span class="homeProductTitle">Product #2</span><a href="" class="homePurchaseButton">($2) Buy</a>
        </div>
    </div>
</div>
<!-- recently viewed products end -->

{if $curPage == 'home'}
<div class="pageBreak"></div>

<!-- accordion wrapper starts -->
<div class="accordionWrapper">
    <!-- accordion item wrapper starts -->
    <div class="accordionItemWrapper"> <a href="" class="accordionButton"><span class="accordionButtonIcon"></span><span class="accordionButtonTitle">How many pages are included?</span></a>
        <div class="accordionContentWrapper">
            <div class="accordionContent">
                <p>This template has 18 unique pages.</p>
            </div>
        </div>
    </div>
    <!-- accordion item wrapper ends -->
    <!-- accordion item wrapper starts -->
    <div class="accordionItemWrapper"> <a href="" class="accordionButton"><span class="accordionButtonIcon"></span><span class="accordionButtonTitle">How many PSD files do I get?</span></a>
        <div class="accordionContentWrapper">
            <div class="accordionContent">
                <p>Yes, a complete PSD is included for the home page, and many other secondary PSD files used to create all the elements for this template.</p>
            </div>
        </div>
    </div>
    <!-- accordion item wrapper ends -->
    <!-- accordion item wrapper starts -->
    <div class="accordionItemWrapper"> <a href="" class="accordionButton"><span class="accordionButtonIcon"></span><span class="accordionButtonTitle">I need help, what can I do?</span></a>
        <div class="accordionContentWrapper">
            <div class="accordionContent">
                <p>A complete guide is included with this template, and all the code is properly commented. All assets are well organized and easy to find.</p>
                <p>You can also contact us if you have any problems or want us to customize it for you.</p>
            </div>
        </div>
    </div>
    <!-- accordion item wrapper ends -->
</div>
<!-- accordion wrapper ends -->
{/if}
{/if}
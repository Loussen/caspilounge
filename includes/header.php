<!-- header for small devices -->
<header class="bwp-sm-header hidden-md hidden-lg">
    <div class="container">
        <div class="bwp-sm-header-container clearfix">
            <!-- mobile logo (text) -->
            <div class="bwp-sm-logo-wrap">
                <a href="<?=SITE_PATH?>" rel="home" class="bwp-sm-logo-text">
                    <span>Fizuli Hüseynov</span>
                </a>
            </div>
            <!-- end mobile logo (text) -->
            <!-- search form -->
            <form id="searchform" role="search" method="get" action="<?=SITE_PATH?>/search">
                <div class="input-group">
                    <input type="text" name="search" id="s" class="bwp-search-field form-control"
                           placeholder="<?=$lang1?> ...">
                    <span class="input-group-btn">
                        <button type="submit" class="btn bwp-search-submit">
                            <i class="fa fa-arrow-right"></i>
                        </button>
                    </span>
                </div>
            </form>
            <!-- end search form -->

            <!-- mobile menu -->
            <div class="bwp-sm-menu-wrap">
                <!-- mobile menu icon -->
                <a href="#" rel="nofollow" id="bwp-sm-menu-icon">
                    <i class="fa fa-bars"></i>
                </a>
                <!-- end mobile menu icon -->
                <!-- dropdown mobile menu -->
                <div id="bwp-dropdown-sm-menu" class="bwp-sm-menu-container">
                    <aside id="tag_cloud-5" class="bwp-widget widget_tag_cloud clearfix" style="margin-bottom: 10px; text-align: center;">
                        <div class="tagcloud">
                            <a href="<?=SITE_PATH."/index.php?lang=1"?>" class="tag-cloud-link tag-link-17 tag-link-position-1 lang <?=($main_lang==1) ? 'active':''?>" style="font-size: 8pt;" aria-label="AZ">AZ</a>
                            <a href="<?=SITE_PATH."/index.php?lang=2"?>" class="tag-cloud-link tag-link-17 tag-link-position-1 lang <?=($main_lang==2) ? 'active':''?>" style="font-size: 8pt;" aria-label="RU">RU</a>
                            <a href="<?=SITE_PATH."/index.php?lang=3"?>" class="tag-cloud-link tag-link-17 tag-link-position-1 lang <?=($main_lang==3) ? 'active':''?>" style="font-size: 8pt;" aria-label="EN">TR</a>
                        </div>
                    </aside>
                    <!-- search form container -->
                    <div id="bwp-dropdown-search" class="bwp-dropdown-search-container bwp-search-hidden" style="display: block; width: 215px; position: relative; padding: 8px 0 15px;">
                        <!-- search form -->
                        <form id="searchform" role="search" method="get" action="<?=SITE_PATH?>/search">
                            <div class="input-group">
                                <input type="text" name="search" id="s" class="bwp-search-field form-control"
                                       placeholder="<?=$lang1?> ...">
                                <span class="input-group-btn">
                        <button type="submit" class="btn bwp-search-submit">
                            <i class="fa fa-arrow-right"></i>
                        </button>
                    </span>
                            </div>
                        </form>
                        <!-- end search form -->
                    </div>
                    <!-- end search form container -->
                    <nav class="menu-demo-main-menu-container">
                        <ul id="menu-demo-main-menu" class="bwp-sm-menu list-unstyled">
                            <?php
                                foreach ($result_menus_arr as $item)
                                {
                                    if(strpos($item['link'],'http')!== false)
                                    {
                                        $target = "_blank";
                                        $link = $item['link'];
                                    }
                                    else
                                    {
                                        $target = "";
                                        $link = SITE_PATH."/".$item['link'];
                                    }
                                    ?>
                                    <li id="menu-item-2037" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2037">
                                        <a target="<?=$target?>" href="<?=$link?>"><?=$item['name']?></a>
                                    </li>
                                    <?php
                                }
                            ?>
                            <li id="menu-item-1647" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-1647">
                                <a href="javascript::void(0);"><?=$lang6?></a>
                                <ul class="sub-menu">
                                    <?php
                                        foreach ($cats_arr as $item)
                                        {
                                            ?>
                                            <li id="menu-item-1657" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1657">
                                                <a href="<?=SITE_PATH."/category/".slugGenerator($item['name'])."-".$item['auto_id']?>"><?=$item['name']?></a>
                                            </li>
                                            <?php
                                        }
                                    ?>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
                <!-- end dropdown mobile menu -->
            </div>
            <!-- end mobile menu -->
        </div>
    </div>
</header>
<!-- end header for small devices -->
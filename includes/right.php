<!-- fixed buttons -->
<div class="bwp-fixed-buttons hidden-sm hidden-xs">
    <!-- "show sidebar" button -->
    <a href="#" rel="nofollow" id="bwp-show-main-sidebar" class="bwp-show-main-sidebar-btn">
        <span></span>
    </a>
    <!-- end "show sidebar" button -->
    <!-- search -->
    <div class="bwp-fixed-search">
        <!-- search button -->
        <a href="#" rel="nofollow" id="bwp-show-dropdown-search" class="bwp-show-dropdown-search-btn">
            <i class="fa fa-search"></i>
        </a>
        <!-- end search button -->
        <!-- search form container -->
        <div id="bwp-dropdown-search" class="bwp-dropdown-search-container bwp-search-hidden">
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
    </div>
    <!-- end search -->
</div>
<!-- end fixed buttons -->

<!-- main sidebar -->
<div class="bwp-main-sidebar hidden-sm hidden-xs" role="complementary">
    <aside id="tag_cloud-5" class="bwp-widget widget_tag_cloud clearfix">
        <div class="tagcloud">
            <a href="<?=SITE_PATH."/index.php?lang=1"?>" class="tag-cloud-link tag-link-17 tag-link-position-1 lang <?=($main_lang==1) ? 'active':''?>" style="font-size: 8pt;" aria-label="AZ">AZ</a>
            <a href="<?=SITE_PATH."/index.php?lang=2"?>" class="tag-cloud-link tag-link-17 tag-link-position-1 lang <?=($main_lang==2) ? 'active':''?>" style="font-size: 8pt;" aria-label="RU">RU</a>
            <a href="<?=SITE_PATH."/index.php?lang=3"?>" class="tag-cloud-link tag-link-17 tag-link-position-1 lang <?=($main_lang==3) ? 'active':''?>" style="font-size: 8pt;" aria-label="EN">TR</a>
        </div>
    </aside>
    <aside id="text-2" class="bwp-widget widget_text clearfix"><h3 class="bwp-widget-title"><?=$lang8?></h3>
        <div class="textwidget">
            <p><?=more_string(html_entity_decode($about),300)?></p>
            <div class="bwp-post-links clearfix">
                <a href="<?=SITE_PATH."/about/"?>" class="bwp-read-more">
                    <?=$lang2?> </a>
            </div>
        </div>
    </aside>
    <aside id="ammi_recent_widget-2" class="bwp-widget widget_bwp_recent_posts clearfix">
        <h3 class="bwp-widget-title"><?=$lang3?></h3>
        <a href="https://play.google.com/store/apps/details?id=com.muradoff.saglam_yasamaq_ucun" target="_blank">
            <img src="<?=SITE_PATH?>/images/playstore.png" style="width: 100%;" />
        </a>
    </aside>
    <aside id="ammi_recent_widget-2" class="bwp-widget widget_bwp_recent_posts clearfix">
        <h3 class="bwp-widget-title"><?=$lang4?></h3>
        <a href="https://fizulihuseynov.com/2017/08/21/saglam-yasamaq-ucun" target="_blank">
            <img src="<?=SITE_PATH?>/images/book.png" style="width: 100%;" />
        </a>
    </aside>
    <aside id="ammi_recent_widget-2" class="bwp-widget widget_bwp_recent_posts clearfix">
        <h3 class="bwp-widget-title"><?=$lang5?></h3>
        <iframe src="https://www.youtube.com/embed/OPF6_-tuEpw"></iframe>
    </aside>
    <aside id="tag_cloud-3" class="bwp-widget widget_tag_cloud clearfix">
        <h3 class="bwp-widget-title"><?=$lang6?></h3>
        <div class="tagcloud">
            <?php
                foreach ($cats_arr as $item)
                {
                    ?>
                    <a href="<?=SITE_PATH."/category/".slugGenerator($item['name'])."-".$item['auto_id']?>" class="tag-cloud-link tag-link-17 tag-link-position-1" style="font-size: 8pt;" aria-label="<?=$item['name']?>"><?=$item['name']?></a>
                    <?php
                }
            ?>
    </aside>
    <aside id="ammi_social_widget-2" class="bwp-widget widget_bwp_social clearfix">
        <h3 class="bwp-widget-title"><?=$lang7?></h3>
        <ul class="list-unstyled clearfix">
            <li>
                <a href="<?=$facebook?>" target="_blank" class="w_bwp_social_i w_bwp_social_facebook">
                    <i class="fa fa-facebook"></i>
                </a>
            </li>
            <li>
                <a href="<?=$youtube?>" target="_blank" class="w_bwp_social_i w_bwp_social_youtube">
                    <i class="fa fa-youtube"></i>
                </a>
            </li>
            <li>
                <a href="<?=$twitter?>" target="_blank" class="w_bwp_social_i w_bwp_social_twitter">
                    <i class="fa fa-twitter"></i>
                </a>
            </li>
            <li>
                <a href="<?=$instagram?>" target="_blank" class="w_bwp_social_i w_bwp_social_instagram">
                    <i class="fa fa-instagram"></i>
                </a>
            </li>
        </ul>
    </aside>
</div>
<!-- end main sidebar -->
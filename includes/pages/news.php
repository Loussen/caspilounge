<!-- column 1 - blog post -->
<div class="col-md-8 col-md-push-4 bwp-single-post-col bwp-sidebar-left">
    <div class="bwp-single-post-container" role="main">
        <!-- article -->
        <article id="bwp-post-317" class="bwp-single-article post-317 post type-post status-publish format-aside has-post-thumbnail hentry category-aside tag-aside tag-featured-image tag-image post_format-post-format-aside">
            <div class="bwp-post-wrap bwp-post-aside-format">
                <!-- header -->
                <header class="bwp-post-header">

                    <!-- format icon -->
                    <span class="bwp-post-format-icon">
                        <i class="fa fa-file-o"></i>
                    </span>
                    <!-- end format icon -->

                    <!-- categories -->
                    <div class="bwp-post-categories">
                        <?php
                            foreach ($categories as $item)
                            {
                                ?>
                                <i class="fa fa-bookmark"></i>
                                <a href="<?=SITE_PATH."/category/".slugGenerator($item['name'])."-".$item['id']?>" rel="category tag"><?=$item['name']?></a>
                                <?php
                            }
                        ?>
                    </div>
                    <!-- end categories -->

                    <!-- title -->
                    <h1 class="bwp-post-title entry-title">
                        <?=$current_news_title?>
                    </h1>
                    <!-- end title -->

                    <!-- metadata -->
                    <ul class="bwp-post-meta list-unstyled">
                        <li>
                            <span class="date updated">
                                <?php
                                    $month = getMonth(date("m",$current_news_created_at),$main_lang);
                                ?>
                                <i class="fa fa-clock-o"></i><a href="<?=SITE_PATH."/date/".date("Y",$current_news_created_at)."/".date("m",$current_news_created_at)?>"><?=$month." ".date("d",$current_news_created_at).", ".date("Y",$current_news_created_at)?></a>
                            </span>
                        </li>
                    </ul>
                    <!-- end metadata -->

                </header>
                <!-- end header -->


                <!-- media - featured image -->
                <figure class="bwp-post-media">
                    <?php
                    if(file_exists("images/news/".$current_news_image_name) && $current_news_image_name!='')
                    {
                        ?>
                        <a href="<?=SITE_PATH?>/images/news/<?=$current_news_image_name?>"
                           class="bwp-post-media-link bwp-popup-image" title="<?=$current_news_title?>">
                            <img width="1314" height="876"
                                 src="<?=SITE_PATH?>/images/news/<?=$current_news_image_name?>"
                                 class="attachment-full size-full wp-post-image" alt="<?=$current_news_title?>"
                                 srcset="<?=SITE_PATH?>/images/news/<?=$current_news_image_name?>"
                                 sizes="(max-width: 1314px) 100vw, 1314px"/>
                            <div class="bwp-post-bg-overlay"></div>
                            <span class="bwp-post-expand-icon">
                                <i class="fa fa-expand"></i>
                            </span>
                        </a>
                        <?php
                    }
                    else
                    {
                        ?>
                        <a href="javascript::void(0);"
                           class="bwp-post-media-link" title="<?=$current_news_title?>">
                            <img width="1314" height="876"
                                 src="<?=SITE_PATH?>/assets/img/no-image.jpg"
                                 class="attachment-full size-full wp-post-image" alt="<?=$current_news_title?>"
                                 sizes="(max-width: 1314px) 100vw, 1314px"/>
                            <div class="bwp-post-bg-overlay"></div>
                            <span class="bwp-post-expand-icon">
                                <i class="fa fa-expand"></i>
                            </span>
                        </a>
                        <?php
                    }
                    ?>
                </figure>
                <!-- end media - featured image -->


                <!-- content container -->
                <div class="bwp-post-content">


                    <!-- full post content -->
                    <div class="bwp-content entry-content clearfix">
                        <p><?=html_entity_decode($current_news_text)?></p>
                    </div>
                    <!-- end full post content -->


                    <!-- links -->
                    <div class="bwp-post-links">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="bwp-post-tags">
                                    Tags:
                                    <?php
                                        $i=1;
                                        foreach ($tags as $item)
                                        {
                                            if($i!=count($tags)) $delimeter = ', ';
                                            else $delimeter = '';
                                            ?>
                                            <a href="<?=SITE_PATH."/tags/".slugGenerator($item['name'])."-".$item['id']?>" rel="tag"><?=$item['name']?></a><?=$delimeter?>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <i class="fa fa-eye"></i> <?=number_format($current_news_view)?>
                            </div>
                        </div>
                    </div>
                    <!-- end links -->

                    <!-- share buttons -->
                    <div class="bwp-post-share-wrap">
                        <div class="bwp-post-share-line"></div>
                        <span><?=$lang15?>:</span>
                        <ul class="bwp-post-share list-unstyled">
                            <!-- facebook -->
                            <li>
                                <a href="http://www.facebook.com/sharer.php?u=<?=SITE_PATH."/news/".slugGenerator($current_news_title)."-".$current_news_id?>"
                                   rel="nofollow" target="_blank"
                                   class="bwp-share-link bwp-facebook-share"
                                   onclick="window.open('http://www.facebook.com/sharer.php?u=<?=SITE_PATH.'/news/'.slugGenerator($current_news_title).'-'.$current_news_id?>', 'Facebook', 'width=600, height=400, left='+(screen.availWidth/2-300)+', top='+(screen.availHeight/2-200)+''); return false;">
                                    <i class="fa fa-facebook"></i>
                                </a>
                            </li>
                            <!-- end facebook -->
                            <!-- twitter -->
                            <li>
                                <a href="https://twitter.com/share?url=<?=SITE_PATH."/news/".slugGenerator($current_news_title)."-".$current_news_id?>"
                                   rel="nofollow" target="_blank"
                                   class="bwp-share-link bwp-twitter-share"
                                   onclick="window.open('https://twitter.com/share?url=<?=SITE_PATH.'/news/'.slugGenerator($current_news_title).'-'.$current_news_id?>', 'Twitter', 'width=600, height=400, left='+(screen.availWidth/2-300)+', top='+(screen.availHeight/2-200)+''); return false;">
                                    <i class="fa fa-twitter"></i>
                                </a>
                            </li>
                            <!-- end twitter -->
                            <!-- google+ -->
                            <li>
                                <a href="https://plus.google.com/share?url=<?=SITE_PATH.'/news/'.slugGenerator($current_news_title).'-'.$current_news_id?>"
                                   rel="nofollow" target="_blank"
                                   class="bwp-share-link bwp-google-plus-share"
                                   onclick="window.open('https://plus.google.com/share?url=<?=SITE_PATH.'/news/'.slugGenerator($current_news_title).'-'.$current_news_id?>', 'Google Plus', 'width=600, height=500, left='+(screen.availWidth/2-300)+', top='+(screen.availHeight/2-250)+''); return false;">
                                    <i class="fa fa-google-plus"></i>
                                </a>
                            </li>
                            <!-- end google+ -->
                        </ul>
                    </div>
                    <!-- end share buttons -->


                </div>
                <!-- end content container -->

            </div>
        </article>
        <!-- end article -->

    </div><!-- /bwp-single-post-container -->
</div><!-- /col1 -->
<!-- end column 1 - blog post -->
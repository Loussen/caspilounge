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

                    <!-- title -->
                    <h1 class="bwp-post-title entry-title">
                        <?=$lang24?>
                    </h1>
                    <!-- end title -->

                </header>
                <!-- end header -->


                <!-- media - featured image -->
                <figure class="bwp-post-media">
                    <?php
                    if(file_exists("images/about/".$image_about) && $image_about!='')
                    {
                        ?>
                        <a href="<?=SITE_PATH?>/images/about/<?=$image_about?>"
                           class="bwp-post-media-link bwp-popup-image" title="<?=$title?>">
                            <img width="1314" height="876"
                                 src="<?=SITE_PATH?>/images/about/<?=$image_about?>"
                                 class="attachment-full size-full wp-post-image" alt="<?=$title?>"
                                 srcset="<?=SITE_PATH?>/images/about/<?=$image_about?>"
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
                           class="bwp-post-media-link" title="<?=$title?>">
                            <img width="1314" height="876"
                                 src="<?=SITE_PATH?>/assets/img/no-image.jpg"
                                 class="attachment-full size-full wp-post-image" alt="<?=$title?>"
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
                        <p><?=html_entity_decode($about)?></p>
                    </div>
                    <!-- end full post content -->

                    <!-- share buttons -->
                    <div class="bwp-post-share-wrap">
                        <div class="bwp-post-share-line"></div>
                        <span><?=$lang15?>:</span>
                        <ul class="bwp-post-share list-unstyled">
                            <!-- facebook -->
                            <li>
                                <a href="http://www.facebook.com/sharer.php?u=<?=SITE_PATH."/about"?>"
                                   rel="nofollow" target="_blank"
                                   class="bwp-share-link bwp-facebook-share"
                                   onclick="window.open('http://www.facebook.com/sharer.php?u=<?=SITE_PATH.'/about'?>', 'Facebook', 'width=600, height=400, left='+(screen.availWidth/2-300)+', top='+(screen.availHeight/2-200)+''); return false;">
                                    <i class="fa fa-facebook"></i>
                                </a>
                            </li>
                            <!-- end facebook -->
                            <!-- twitter -->
                            <li>
                                <a href="https://twitter.com/share?url=<?=SITE_PATH."/about"?>"
                                   rel="nofollow" target="_blank"
                                   class="bwp-share-link bwp-twitter-share"
                                   onclick="window.open('https://twitter.com/share?url=<?=SITE_PATH.'/about'?>', 'Twitter', 'width=600, height=400, left='+(screen.availWidth/2-300)+', top='+(screen.availHeight/2-200)+''); return false;">
                                    <i class="fa fa-twitter"></i>
                                </a>
                            </li>
                            <!-- end twitter -->
                            <!-- google+ -->
                            <li>
                                <a href="https://plus.google.com/share?url=<?=SITE_PATH.'/about'?>"
                                   rel="nofollow" target="_blank"
                                   class="bwp-share-link bwp-google-plus-share"
                                   onclick="window.open('https://plus.google.com/share?url=<?=SITE_PATH.'/about'?>', 'Google Plus', 'width=600, height=500, left='+(screen.availWidth/2-300)+', top='+(screen.availHeight/2-250)+''); return false;">
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
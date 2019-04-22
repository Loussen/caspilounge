<div class="col-md-8 col-md-push-4 bwp-posts-col bwp-sidebar-left">
    <div class="bwp-posts-container" role="main">

        <!-- archive heading - category/tag page -->
        <div class="bwp-archive-heading">
            <h1 class="bwp-archive-title"><?=$lang14?>: <?=$current_tag_name?></h1>
            <div class="bwp-archive-heading-line"></div>
        </div>
        <!-- end archive heading -->

        <?php
            foreach ($result_news_by_tags_arr as $item)
            {
                $stmt_select = mysqli_prepare($db,
                    "SELECT
                            `categories`.`name`,
                            `categories`.`auto_id`
                            FROM `categories`
                            LEFT JOIN `news_cat` ON `categories`.`auto_id`=`news_cat`.`cat_id`
                            WHERE `categories`.`lang_id`=(?) and `categories`.`active`=(?) and `news_cat`.`news_id`=(?)");
                $stmt_select->bind_param('iii', $main_lang,$active_status,$item['auto_id']);
                $stmt_select->execute();
                $stmt_select->bind_result($category_name,$category_id);
                $stmt_select->fetch();
                $stmt_select->close();

                ?>
                <!-- post - aside format -->
                <article class="post-317 post type-post status-publish format-aside has-post-thumbnail hentry category-aside tag-aside tag-featured-image tag-image post_format-post-format-aside">
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
                                <i class="fa fa-bookmark"></i>
                                <a href="<?=SITE_PATH."/category/".slugGenerator($category_name)."-".$category_id?>" rel="category tag"><?=$category_name?></a>
                            </div>
                            <!-- end categories -->

                            <!-- title -->
                            <h2 class="bwp-post-title entry-title">
                                <a href="<?=SITE_PATH."/news/".slugGenerator($item['title'])."-".$item['auto_id']?>"><?=$item['title']?></a>
                            </h2>
                            <!-- end title -->

                            <!-- metadata -->
                            <ul class="bwp-post-meta list-unstyled">
                                <li>
                                        <span class="date updated">
                                            <?php
                                            $month = getMonth(date("m",$item['created_at']),$main_lang);
                                            ?>
                                            <i class="fa fa-clock-o"></i><a href="<?=SITE_PATH."/date/".date("Y",$item['created_at'])."/".date("m",$item['created_at'])?>"><?=$month." ".date("d",$item['created_at']).", ".date("Y",$item['created_at'])?></a>
                                        </span>
                                </li>
                            </ul>
                            <!-- end metadata -->
                        </header>
                        <!-- end header -->

                        <!-- media - featured image -->
                        <figure class="bwp-post-media">
                            <?php
                            if(file_exists("images/news/".$item['image_name']) && $item['image_name']!='')
                            {
                                ?>
                                <a href="<?=SITE_PATH?>/images/news/<?=$item['image_name']?>"
                                   class="bwp-post-media-link bwp-popup-image" title="<?=$item['title']?>">
                                    <img width="1314" height="876"
                                         src="<?=SITE_PATH?>/images/news/<?=$item['image_name']?>"
                                         class="attachment-full size-full wp-post-image" alt="<?=$item['title']?>"
                                         srcset="<?=SITE_PATH?>/images/news/<?=$item['image_name']?>"
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
                                   class="bwp-post-media-link" title="<?=$item['title']?>">
                                    <img width="1314" height="876"
                                         src="<?=SITE_PATH?>/assets/img/no-image.jpg"
                                         class="attachment-full size-full wp-post-image" alt="<?=$item['title']?>"
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

                        <!-- content -->
                        <div class="bwp-post-content">
                            <!-- excerpt (post content) -->
                            <div class="bwp-post-excerpt bwp-content entry-content clearfix">
                                <p>
                                    <?php
                                    if(strpos($item['short_text'],'...<div class="read-more-link">')>0)
                                    {
                                        echo substr($item['short_text'],0,strpos($item['short_text'],'...<div class="read-more-link">'));
                                    }
                                    else
                                    {
                                        echo $item['short_text'];
                                    }
                                    ?>
                                    <a href="<?=SITE_PATH."/news/".slugGenerator($item['title'])."-".$item['auto_id']?>" class="more-link">[&#8230;]</a>
                                </p>
                            </div>
                            <!-- end excerpt (post content) -->

                            <!-- links -->
                            <div class="bwp-post-links clearfix">
                                <a href="<?=SITE_PATH."/news/".slugGenerator($item['title'])."-".$item['auto_id']?>" class="bwp-read-more">
                                    <?=$lang2?> </a>
                            </div>
                            <!-- end links -->

                            <!-- share buttons -->
                            <div class="bwp-post-share-wrap">
                                <div class="bwp-post-share-line"></div>
                                <span><?=$lang15?>:</span>
                                <ul class="bwp-post-share list-unstyled">
                                    <!-- facebook -->
                                    <li>
                                        <a href="http://www.facebook.com/sharer.php?u=<?=SITE_PATH."/news/".slugGenerator($item['title'])."-".$item['auto_id']?>"
                                           rel="nofollow" target="_blank"
                                           class="bwp-share-link bwp-facebook-share"
                                           onclick="window.open('http://www.facebook.com/sharer.php?u=<?=SITE_PATH.'/news/'.slugGenerator($item["title"]).'-'.$item["auto_id"]?>', 'Facebook', 'width=600, height=400, left='+(screen.availWidth/2-300)+', top='+(screen.availHeight/2-200)+''); return false;">
                                            <i class="fa fa-facebook"></i>
                                        </a>
                                    </li>
                                    <!-- end facebook -->
                                    <!-- twitter -->
                                    <li>
                                        <a href="https://twitter.com/share?url=<?=SITE_PATH."/news/".slugGenerator($item['title'])."-".$item['auto_id']?>"
                                           rel="nofollow" target="_blank"
                                           class="bwp-share-link bwp-twitter-share"
                                           onclick="window.open('https://twitter.com/share?url=<?=SITE_PATH.'/news/'.slugGenerator($item["title"]).'-'.$item["auto_id"]?>', 'Twitter', 'width=600, height=400, left='+(screen.availWidth/2-300)+', top='+(screen.availHeight/2-200)+''); return false;">
                                            <i class="fa fa-twitter"></i>
                                        </a>
                                    </li>
                                    <!-- end twitter -->
                                    <!-- google+ -->
                                    <li>
                                        <a href="https://plus.google.com/share?url=<?=SITE_PATH.'/news/'.slugGenerator($item["title"]).'-'.$item["auto_id"]?>"
                                           rel="nofollow" target="_blank"
                                           class="bwp-share-link bwp-google-plus-share"
                                           onclick="window.open('https://plus.google.com/share?url=<?=SITE_PATH.'/news/'.slugGenerator($item["title"]).'-'.$item["auto_id"]?>', 'Google Plus', 'width=600, height=500, left='+(screen.availWidth/2-300)+', top='+(screen.availHeight/2-250)+''); return false;">
                                            <i class="fa fa-google-plus"></i>
                                        </a>
                                    </li>
                                    <!-- end google+ -->
                                </ul>
                            </div>
                            <!-- end share buttons -->
                        </div>
                        <!-- end content -->
                    </div>
                </article>
                <!-- end post -->
                <?php
            }
        ?>

        <?php
        if($count_rows > $limit)
        {
            $show= 5;
            ?>
            <nav class="navigation pagination" role="navigation">
                <h2 class="screen-reader-text">Posts navigation</h2>
                <div class="nav-links">
                    <?php
                    if($page>1)
                    {
                        ?>
                        <a class="first page-numbers" href="<?=SITE_PATH?>/tags/<?=slugGenerator($current_tag_name) . '-' . $current_tag_id.'/1'?>">
                            <i class="fa fa-step-backward"></i>
                        </a>
                        <a class="previous page-numbers" href="<?=SITE_PATH?>/tags/<?=slugGenerator($current_tag_name) . '-' . $current_tag_id.'/'.($page-1)?>">
                            <i class="fa fa-caret-left"></i>
                        </a>
                        <?php
                    }

                    for ($i = $page - $show; $i <= $page + $show; $i++)
                    {
                        if ($i > 0 && $i <= $max_page)
                        {
                            if ($i == $page)
                            {
                                ?>
                                <span aria-current='page' class='page-numbers current'><?= $i ?></span>
                                <?php
                            }
                            else
                            {
                                ?>
                                <a class='page-numbers' href='<?=SITE_PATH?>/tags/<?=slugGenerator($current_tag_name) . '-' . $current_tag_id.'/'.$i?>'><?=$i?></a>
                                <?php
                            }
                        }
                    }
                    if ($page < $max_page)
                    {
                        ?>
                        <a class="next page-numbers" href="<?=SITE_PATH?>/tags/<?=slugGenerator($current_tag_name) . '-' . $current_tag_id.'/'.($page + 1)?>">
                            <i class="fa fa-caret-right"></i>
                        </a>
                        <a class="last page-numbers" href="<?=SITE_PATH?>/tags/<?=slugGenerator($current_tag_name) . '-' . $current_tag_id.'/'.$max_page?>">
                            <i class="fa fa-step-forward"></i>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </nav>
            <?php
        }
        ?>
    </div><!-- /bwp-posts-container -->
</div><!-- /col1 -->
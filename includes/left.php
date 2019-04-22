<!-- column 2 - sidebar -->
<div class="col-md-4 col-md-pull-8 bwp-sidebar-col bwp-sidebar-left">
    <div class="bwp-sidebar-container" role="complementary">


        <!-- sidebar - main navigation container -->
        <div class="bwp-sidebar-nav-container hidden-sm hidden-xs">


            <!-- text logo + tagline -->
            <div class="bwp-sidebar-logo-wrap">
                <a href="<?=SITE_PATH?>" rel="home" class="bwp-logo-text">
                    <img src="<?=SITE_PATH?>/images/me.jpg" style="width: 100%;" />
                </a>

                <span class="bwp-tagline">
                    Fizuli Hüseynov
                </span>

            </div>
            <!-- end text logo + tagline -->


            <!-- menu -->
            <div class="bwp-sidebar-menu-wrap">
                <div class="bwp-sidebar-menu-line"></div>
                <nav class="menu-demo-main-menu-container">
                    <ul id="menu-demo-main-menu-1" class="sf-menu">
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
                                    if(strlen($item['link'])>2)
                                    {
                                        $link = SITE_PATH."/".$item['link'];
                                    }
                                    else
                                    {
                                        $link = SITE_PATH;
                                    }
                                }
                                ?>
                                <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2037">
                                    <a target="<?=$target?>" href="<?=$link?>"><?=$item['name']?></a>
                                </li>
                                <?php
                            }
                        ?>
                        <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-1647">
                            <a href="javascript::void(0);" class="sf-with-ul"><?=$lang6?></a>
                            <ul class="sub-menu" style="display: none; margin-top: 15px;">
                                <?php
                                    foreach ($cats_arr as $item)
                                    {
                                        ?>
                                        <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1657">
                                            <a href="<?=SITE_PATH."/category/".slugGenerator($item['name'])."-".$item['auto_id']?>"><?=$item['name']?></a>
                                        </li>
                                        <?php
                                    }
                                ?>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <div class="bwp-sidebar-menu-line"></div>
            </div>
            <!-- end menu -->


            <!-- social links -->
            <div class="bwp-sidebar-social-links-wrap">
                <span><?=$lang9?>:</span>
                <ul class="bwp-sidebar-social-links list-unstyled clearfix">
                    <li>
                        <a href="<?=$facebook?>" target="_blank" class="bwp-s-facebook-link">
                            <i class="fa fa-facebook"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?=$youtube?>" target="_blank" class="bwp-s-youtube-p-link">
                            <i class="fa fa-youtube"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?=$twitter?>" target="_blank" class="bwp-s-twitter-link">
                            <i class="fa fa-twitter"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?=$instagram?>" target="_blank" class="bwp-s-instagram-link">
                            <i class="fa fa-instagram"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- end social links -->

            <!-- custom text -->
            <div class="bwp-sidebar-custom-text">
                <?=$lang10?>
            </div>
            <!-- end custom text -->

        </div>
        <!-- end main navigation container -->

        <aside id="ammi_random_widget-2" class="bwp-widget widget_bwp_random_posts clearfix"><h3
                class="bwp-widget-title"><?=$lang11?></h3>
            <ul class="list-unstyled">
                <?php
                    if(!$cache->isCached('news_top_'.$main_lang))
                    {
                        // Get games by category
                        $stmt_select = mysqli_prepare($db,
                            "SELECT
                            `title`,
                            `image_name`,
                            `view`,
                            `created_at`,
                            `auto_id`,
                            `view`
                            FROM `news`
                            WHERE `lang_id`=(?) and `active`=(?) and `title`!=''
                            order by `view` desc limit 5");

                        $stmt_select->bind_param('ii', $main_lang,$active_status);
                        $stmt_select->execute();
                        $result = $stmt_select->get_result();
                        $stmt_select->close();

                        $news_top_arr = [];
                        while($row=$result->fetch_assoc())
                        {
                            $news_top_arr[] = $row;
                        }

                        $cache->store('news_top_'.$main_lang,$news_top_arr, 100);
                    }
                    else
                    {
                        $news_top_arr = $cache->retrieve('news_top_'.$main_lang);
                    }

                    foreach ($news_top_arr as $item)
                    {
                        ?>
                        <li>
                            <figure class="widget_bwp_thumb">
                                <a href="<?=SITE_PATH."/news/".slugGenerator($item['title'])."-".$item['auto_id']?>">
                                    <img width="150" height="150"
                                         src="<?=SITE_PATH?>/images/news/<?=$item['image_name']?>"
                                         class="attachment-thumbnail size-thumbnail wp-post-image" alt="<?=$item['title']?>"/>
                                    <div class="widget-bwp-bg-overlay bwp-transition-3"></div>
                                </a>
                            </figure>
                            <div class="widget_bwp_content" style="">
                                <h4 class="entry-title">
                                    <a href="<?=SITE_PATH."/news/".slugGenerator($item['title'])."-".$item['auto_id']?>"><?=$item['title']?></a>
                                </h4>
                                <ul class="widget_bwp_meta list-unstyled clearfix">
                                    <li>
                                        <a href="<?=SITE_PATH."/date/".date("Y",$item['created_at'])."/".date("m",$item['created_at'])?>">
                                            <span class="date updated"><?=$month." ".date("d",$item['created_at']).", ".date("Y",$item['created_at'])?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <span class="date updated"><i class="fa fa-eye"></i> <?=number_format($item['view'],0)?></span>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <?php
                    }
                ?>
            </ul>
        </aside>
        <aside id="tag_cloud-2" class="bwp-widget widget_tag_cloud clearfix">
            <h3 class="bwp-widget-title"><?=$lang12?></h3>
            <div class="tagcloud">
                <?php
                    if(!$cache->isCached('tags_'.$main_lang))
                    {
                        $stmt_select = mysqli_prepare($db,
                            "SELECT
                                    `tags`.`name`,
                                    `tags`.`auto_id`,
                                    COUNT(`news_tags`.`id`) as `numberTags`
                                    FROM `tags`
                                    LEFT JOIN `news_tags` ON `tags`.`auto_id`=`news_tags`.`tag_id`
                                    LEFT JOIN `news` ON `news_tags`.`news_id`=`news`.`auto_id`
                                    WHERE `tags`.`lang_id`=(?) and `tags`.`active`=(?) and `news`.`title`!=''
                                    GROUP BY `news_tags`.`tag_id` ORDER BY `numberTags` DESC
                                    LIMIT 50");
                        $stmt_select->bind_param('ii', $main_lang,$active_status);
                        $stmt_select->execute();
                        $result = $stmt_select->get_result();
                        $stmt_select->close();

                        $tags_arr = [];
                        while($row=$result->fetch_assoc())
                        {
                            $tags_arr[] = $row;
                        }

                        $cache->store('tags_'.$main_lang,$tags_arr, 100);
                    }
                    else
                    {
                        $tags_arr = $cache->retrieve('tags_'.$main_lang);
                    }

                    foreach ($tags_arr as $item)
                    {
                        ?>
                        <a href="<?=SITE_PATH."/tags/".slugGenerator($item['name'])."-".$item['auto_id']?>"
                           class="tag-cloud-link tag-link-117 tag-link-position-1"
                           style="font-size: 8pt;" aria-label="<?=$item['auto_id']?>"><?=$item['name']?><span
                                    class="tag-link-count"> (<?=$item['numberTags']?>)</span></a>
                        <?php
                    }
                ?>
            </div>
        </aside>
        <aside id="tag_cloud-2" class="bwp-widget widget_tag_cloud clearfix">
            <h3 class="bwp-widget-title"><?=$lang13?></h3>
            <ul class="date-list list-unstyled">
                <?php
                if(!$cache->isCached('date_list_'.$main_lang))
                {
                    $stmt_select = mysqli_prepare($db,
                        "SELECT
                                `news`.`created_at`,
                                MONTH(FROM_UNIXTIME(`news`.`created_at`)) as `news_month`, 
                                YEAR(FROM_UNIXTIME(`news`.`created_at`)) as `news_year`, 
                                COUNT(id) as news_count                                
                                FROM `news`
                                WHERE `news`.`lang_id`=(?) and `news`.`active`=(?) and `news`.`title`!=''
                                GROUP BY 
                                          MONTH(FROM_UNIXTIME(`news`.`created_at`)), 
                                          YEAR(FROM_UNIXTIME(`news`.`created_at`))
                                order by `news`.`created_at` desc
                                ");

                    $stmt_select->bind_param('ii', $main_lang,$active_status);
                    $stmt_select->execute();
                    $result_news_by_date = $stmt_select->get_result();
                    $stmt_select->close();

                    $result_news_by_date_arr = [];

                    while($row=$result_news_by_date->fetch_assoc())
                    {
                        $result_news_by_date_arr[] = $row;
                    }

                    $cache->store('date_list_'.$main_lang,$result_news_by_date_arr, 100);
                }
                else
                {
                    $result_news_by_date_arr = $cache->retrieve('date_list_'.$main_lang);
                }

                foreach ($result_news_by_date_arr as $item)
                {
                    $month = getMonth($item['news_month'],$main_lang);
                    $str_date = $month." ".$item['news_year'];
                    $link = $item['news_year']."/".$item['news_month'];

                    ?>
                    <li><a href="<?=SITE_PATH."/date/".$link?>"><?=$str_date?> (<?=$item['news_count']?>)</a></li>
                    <?php
                }
                ?>
            </ul>
        </aside>
    </div><!-- /bwp-sidebar-container -->
</div><!-- /col2 -->
<!-- end column 2 - sidebar -->
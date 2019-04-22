<?php
/**
 * Created by PhpStorm.
 * User: fuad
 * Date: 1/16/17
 * Time: 6:18 PM
 */
?>

<?php

    $starttime_about = microtime(true);
    // Get about
    if(!$cache->isCached('about_'.$main_lang))
    {
        $stmt_select = mysqli_prepare($db,"SELECT `image_name`,`text` FROM `about` WHERE `lang_id`=(?) ORDER BY `id` DESC");
        $stmt_select->bind_param('i', $main_lang);
        $stmt_select->execute();
        $stmt_select->bind_result($image_about,$about);
        $stmt_select->fetch();
        $stmt_select->close();

        $cache->store('about_'.$main_lang, [
            'image_about'   => $image_about,
            'about'         => $about
        ],100);
    }
    else
    {
        $cache_about = $cache->retrieve('about_'.$main_lang);
        $image_about = $cache_about['image_about'];
        $about = $cache_about['about'];
    }
    $endtime_about = microtime(true);
    $duration_about = $endtime_about - $starttime_about;

    $starttime_seo = microtime(true);
    // Get seo
    if(!$cache->isCached('seo_'.$main_lang))
    {
        $stmt_select = mysqli_prepare($db,"SELECT `description_`,`title_`,`keywords_` FROM `seo` WHERE `lang_id`=(?)");
        $stmt_select->bind_param('i', $main_lang);
        $stmt_select->execute();
        $stmt_select->bind_result($site_description,$site_title,$site_keywords);
        $stmt_select->fetch();
        $stmt_select->close();

        $description = $site_description;
        $title = $site_title;
        $image = SITE_PATH.'/images/about/'.$image_about;
        $keywords = $site_keywords;
        $og_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

        $cache->store('seo_'.$main_lang, [
                'description'   => $site_description,
                'title'         => $site_title,
                'image'         => SITE_PATH.'/images/about/'.$image_about,
                'keywords'      => $site_keywords,
                'og_url'        => 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]
        ],100);
    }
    else
    {
        $cache_seo_result = $cache->retrieve('seo_'.$main_lang);

        $description = $cache_seo_result['description'];
        $title = $cache_seo_result['title'];
        $image = $cache_seo_result['image'];
        $keywords = $cache_seo_result['keywords'];
        $og_url = $cache_seo_result['og_url'];
    }
    $endtime_seo = microtime(true);
    $duration_seo = $endtime_seo - $starttime_seo;

    $starttime_menus = microtime(true);
    // Get menus
    if(!$cache->isCached('menus_'.$main_lang))
    {
        $stmt_select = mysqli_prepare($db,"SELECT `name`,`link` FROM `menus` WHERE `lang_id`=(?) and `active`=(?) ORDER BY `order_number` ASC");
        $stmt_select->bind_param('ii', $main_lang,$active_status);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $stmt_select->close();

        $result_menus_arr = [];
        while($row=$result->fetch_assoc())
        {
            $result_menus_arr[] = $row;
        }

        $cache->store('menus_'.$main_lang,$result_menus_arr, 100);
    }
    else
    {
        $result_menus_arr = $cache->retrieve('menus_'.$main_lang);
    }
    $endtime_menus = microtime(true);
    $duration_menus = $endtime_menus - $starttime_menus;

    $starttime_cat = microtime(true);
    // Get categories
    if(!$cache->isCached('categories_'.$main_lang))
    {
//        $stmt_select = mysqli_prepare($db,
//            "SELECT
//                                            `categories`.`name`,
//                                            `categories`.`auto_id`,
//                                            COUNT(`news_cat`.`id`) as `numberCat`
//                                            FROM `categories`
//                                            LEFT JOIN `news_cat` ON `categories`.`auto_id`=`news_cat`.`cat_id`
//                                            LEFT JOIN `news` ON `news_cat`.`news_id`=`news`.`auto_id`
//                                            WHERE `categories`.`lang_id`=(?) and `categories`.`active`=(?) and `news`.`title`!=''
//                                            GROUP BY `news_cat`.`cat_id` ORDER BY `numberCat` DESC");
        $stmt_select = mysqli_prepare($db,
            "SELECT
                    `categories`.`name`,
                    `categories`.`auto_id`
                    FROM `categories`
                    WHERE `categories`.`lang_id`=(?) and `categories`.`active`=(?)
                    ORDER BY `order_number`");
        $stmt_select->bind_param('ii', $main_lang,$active_status);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $stmt_select->close();

        $cats_arr = [];
        while($row=$result->fetch_assoc())
        {
            $cats_arr[] = $row;
        }

        $cache->store('categories_'.$main_lang,$cats_arr, 100);
    }
    else
    {
        $cats_arr = $cache->retrieve('categories_'.$main_lang);
    }
    $endtime_cat = microtime(true);
    $duration_cat = $endtime_cat - $starttime_cat;

    $starttime_contact = microtime(true);
    // Get contacts
    if(!$cache->isCached('contacts_'.$main_lang))
    {
        $stmt_select = mysqli_prepare($db,"SELECT `email`,`facebook`,`twitter`,`youtube`,`instagram`,`phone` FROM `contacts` WHERE `lang_id`=(?)");
        $stmt_select->bind_param('i', $main_lang);
        $stmt_select->execute();
        $stmt_select->bind_result($email,$facebook,$twitter,$youtube,$instagram,$phone);
        $stmt_select->fetch();
        $stmt_select->close();

        $cache->store('contacts_'.$main_lang, [
            'email'         => $email,
            'facebook'      => $facebook,
            'twitter'       => $twitter,
            'youtube'       => $youtube,
            'instagram'     => $instagram,
            'phone'         => $phone
        ],100);
    }
    else
    {
        $cache_contacts_result = $cache->retrieve('contacts_'.$main_lang);

        $email = $cache_contacts_result['email'];
        $facebook = $cache_contacts_result['facebook'];
        $twitter = $cache_contacts_result['twitter'];
        $youtube = $cache_contacts_result['youtube'];
        $instagram = $cache_contacts_result['instagram'];
        $phone = $cache_contacts_result['phone'];
    }
    $endtime_contact = microtime(true);
    $duration_contact = $endtime_contact - $starttime_contact;

    if($do=="news")
    {
        $entry = false;

        // Origin url
        if(isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['slug']) && !empty($_GET['slug']))
        {
            $news_id = intval($_GET['id']);
            $news_slug = mysqli_real_escape_string($db,$_GET['slug']);

            $entry = true;

            // Get news info
            if(!$cache->isCached('news_inner_'.$news_id.$news_slug.$main_lang))
            {
                $stmt_select = mysqli_prepare($db,
                    "SELECT
                    `news`.`auto_id` as `news_id`,
                    `news`.`title` as `news_title`,
                    `news`.`image_name` as `news_image_name`,
                    `news`.`short_text` as `news_short_text`,
                    `news`.`text` as `news_text`,
                    `news`.`created_at` as `news_created_at`,
                    `news`.`view` as `news_view`
                    FROM `news`
                    WHERE `news`.`lang_id`=(?) and `news`.`active`=(?) and `news`.`auto_id`=(?) and `news`.`title`!=''
                    ");
                $stmt_select->bind_param('iii', $main_lang,$active_status,$news_id);
                $stmt_select->execute();
                $stmt_select->bind_result($current_news_id,$current_news_title,$current_news_image_name,$current_news_short_text,$current_news_text,$current_news_created_at,$current_news_view);
                $stmt_select->fetch();
                $stmt_select->close();

                // Get categories
                $stmt_select = mysqli_prepare($db,
                    "SELECT
                            `categories`.`name`,
                            `categories`.`auto_id`
                            FROM `categories`
                            LEFT JOIN `news_cat` ON `categories`.`auto_id`=`news_cat`.`cat_id`
                            WHERE `categories`.`lang_id`=(?) and `categories`.`active`=(?) and `news_cat`.`news_id`=(?)");
                $stmt_select->bind_param('iii', $main_lang,$active_status,$current_news_id);
                $stmt_select->execute();
                $result = $stmt_select->get_result();
                $stmt_select->close();

                $categories = [];
                $i=0;
                while($row=$result->fetch_assoc())
                {
                    $categories[$i]['id'] = $row['auto_id'];
                    $categories[$i]['name'] = $row['name'];

                    $i++;
                }

                // Get tags
                $stmt_select = mysqli_prepare($db,
                    "SELECT
                            `tags`.`name`,
                            `tags`.`auto_id`
                            FROM `tags`
                            LEFT JOIN `news_tags` ON `tags`.`auto_id`=`news_tags`.`tag_id`
                            WHERE `tags`.`lang_id`=(?) and `tags`.`active`=(?) and `news_tags`.`news_id`=(?)");
                $stmt_select->bind_param('iii', $main_lang,$active_status,$current_news_id);
                $stmt_select->execute();
                $result = $stmt_select->get_result();
                $stmt_select->close();

                $tags = [];
                $i=0;
                while($row=$result->fetch_assoc())
                {
                    $tags[$i]['id'] = $row['auto_id'];
                    $tags[$i]['name'] = $row['name'];

                    $i++;
                }

                if(strpos($current_news_short_text,'...<div class="read-more-link">')>0)
                {
                    $current_news_short_text = substr($current_news_short_text,0,strpos($current_news_short_text,'...<div class="read-more-link">'));
                }

                $cache->store('news_inner_'.$news_id.$news_slug.$main_lang,[
                    'current_news_id'           => $current_news_id,
                    'current_news_title'        => $current_news_title,
                    'current_news_image_name'   => $current_news_image_name,
                    'current_news_text'         => $current_news_text,
                    'current_news_short_text'   => $current_news_short_text,
                    'current_news_created_at'   => $current_news_created_at,
                    'current_news_view'         => $current_news_view,
                    'categories'                => $categories,
                    'tags'                      => $tags
                ],100);
            }
            else
            {
                $cache_news_inner_result = $cache->retrieve('news_inner_'.$news_id.$news_slug.$main_lang);

                $current_news_id = $cache_news_inner_result['current_news_id'];
                $current_news_title = $cache_news_inner_result['current_news_title'];
                $current_news_image_name = $cache_news_inner_result['current_news_image_name'];
                $current_news_text = $cache_news_inner_result['current_news_text'];
                $current_news_short_text = $cache_news_inner_result['current_news_short_text'];
                $current_news_created_at = $cache_news_inner_result['current_news_created_at'];
                $current_news_view = $cache_news_inner_result['current_news_view'];
                $categories = $cache_news_inner_result['categories'];
                $tags = $cache_news_inner_result['tags'];

                mysqli_query($db, "UPDATE `news` SET `view`=`view`+1 WHERE `auto_id`='$current_news_id'");
            }

            if($news_id!=$current_news_id || $news_slug!=slugGenerator($current_news_title) || !$current_news_id)
            {
                header("Location: ".SITE_PATH."/404");
                exit('Redirecting...');
            }
        }
        elseif(isset($_GET['year']) && !empty($_GET['year']) && isset($_GET['month']) && !empty($_GET['month']) && isset($_GET['day']) && !empty($_GET['day']) && isset($_GET['slug']) && !empty($_GET['slug'])) // Redirected url from old site
        {
            $year = intval($_GET['year']);
            $month = intval($_GET['month']);
            $day = intval($_GET['day']);
            $news_slug = str_replace("/","",mysqli_real_escape_string($db,$_GET['slug']));

            $stmt_select = mysqli_prepare($db,
                "SELECT
                        `auto_id`,
                        `title`
                        FROM `news`
                        WHERE `lang_id`=(?) and `active`=(?) and MONTH(FROM_UNIXTIME(`news`.`created_at`))=(?) and YEAR(FROM_UNIXTIME(`news`.`created_at`))=(?) and DAY(FROM_UNIXTIME(`news`.`created_at`))=(?) and `slug`=(?) and `news`.`title`!=''
                        ");
            $stmt_select->bind_param('iiiiis', $main_lang,$active_status,$month,$year,$day,$news_slug);
            $stmt_select->execute();
            $stmt_select->bind_result($news_id,$news_title);
            $stmt_select->fetch();
            $stmt_select->close();

            $entry = true;

            if($news_id>0)
            {
                header("Location: ".SITE_PATH."/news/".slugGenerator($news_title)."-".$news_id);
                exit('Redirecting...');
            }
            else
            {
                header("Location: ".SITE_PATH."/404");
                exit('Redirecting...');
            }
        }

        if($entry===false)
        {
            header("Location: ".SITE_PATH."/404");
            exit('Redirecting...');
        }

        $title = $title.' - '.$current_news_title;
        $image = SITE_PATH."/images/news/".$current_news_image_name;
        $description = $current_news_short_text;
        $og_url = SITE_PATH."/news/".slugGenerator($current_news_title)."-".$current_news_id;
    }
    elseif($do=="category")
    {
        $category_id = intval($_GET['id']);
        $category_slug = mysqli_real_escape_string($db,$_GET['slug']);

        // Redirected url from old site
        if(isset($category_slug) && !empty($category_slug) && !isset($_GET['id']))
        {
            $category_slug = str_replace("/","",$category_slug);

            $stmt_select = mysqli_prepare($db,
                "SELECT 
                    `name`,
                    `auto_id`                               
                    FROM `categories`
                    WHERE `lang_id`=(?) and `active`=(?)
                    order by `created_at` desc");
            $stmt_select->bind_param('ii', $main_lang,$active_status);
            $stmt_select->execute();
            $result_news_by_slug = $stmt_select->get_result();
            $stmt_select->close();

            while($row=$result_news_by_slug->fetch_assoc())
            {
                if($category_slug==slugGenerator($row['name'],'-',false,true))
                {
                    header("Location: ".SITE_PATH."/category/".slugGenerator($row['name'])."-".$row['auto_id']);
                    exit('Redirecting...');
                }
            }
        }

        // Get current category
        if(!$cache->isCached('current_category_'.$category_id.$category_slug.$main_lang))
        {
            $stmt_select = mysqli_prepare($db,
                "SELECT
                        `name`,
                        `auto_id`
                        FROM `categories`
                        WHERE `lang_id`=(?) and `active`=(?) and `auto_id`=(?)
                        ");
            $stmt_select->bind_param('iii', $main_lang,$active_status,$category_id);
            $stmt_select->execute();
            $stmt_select->bind_result($current_category_name,$current_category_id);
            $stmt_select->fetch();
            $stmt_select->close();

            $cache->store('current_category_'.$category_id.$category_slug.$main_lang,[
                'current_category_name' =>  $current_category_name,
                'current_category_id'   =>  $current_category_id
            ],100);
        }
        else
        {
            $cache_current_category_result = $cache->retrieve('current_category_'.$category_id.$category_slug.$main_lang);
            $current_category_name = $cache_current_category_result['current_category_name'];
            $current_category_id = $cache_current_category_result['current_category_id'];
        }

        if($category_slug!=slugGenerator($current_category_name) || $current_category_id!=$category_id)
        {
            header("Location: ".SITE_PATH."/404");
            exit('Redirecting...');
        }

        // Paginator
        $limit = 8;

        $stmt_select = mysqli_prepare($db,
            "SELECT 
                    `news`.`id`
                    FROM `news`
                    LEFT JOIN `news_cat` ON `news`.`auto_id`=`news_cat`.`news_id`
                    WHERE `news`.`lang_id`=(?) and `news`.`active`=(?) and `news_cat`.`cat_id`=(?) and `news`.`title`!=''
                    ");
        $stmt_select->bind_param('iii', $main_lang,$active_status,$category_id);
        $stmt_select->execute();
        $stmt_select->store_result();

        $count_rows = $stmt_select->num_rows;
        $max_page=ceil($count_rows/$limit);
        $page=intval($_GET["page"]); if($page<1) $page=1; if($page>$max_page) $page=$max_page;
        if($page<1) $page = 1;
        $start=$page*$limit-$limit;
        $stmt_select->close();

        if(!$cache->isCached('result_news_by_categories_'.$category_id.$category_slug.$page.$main_lang))
        {
            // Get news by category
            $stmt_select = mysqli_prepare($db,
                "SELECT 
                    `news`.`auto_id`,
                    `news`.`title`,
                    `news`.`image_name`,
                    `news`.`short_text`,
                    `news`.`created_at`                                
                    FROM `news`
                    LEFT JOIN `news_cat` ON `news`.`auto_id`=`news_cat`.`news_id`
                    WHERE `news`.`lang_id`=(?) and `news`.`active`=(?) and `news_cat`.`cat_id`=(?) and `news`.`title`!=''
                    order by `news`.`created_at` desc limit $start,$limit");

            $stmt_select->bind_param('iii', $main_lang,$active_status,$category_id);
            $stmt_select->execute();
            $result_news_by_categories = $stmt_select->get_result();
            $stmt_select->close();

            $result_news_by_categories_arr = [];
            while($row=$result_news_by_categories->fetch_assoc())
            {
                $result_news_by_categories_arr[] = $row;
            }

            $cache->store('result_news_by_categories_'.$category_id.$category_slug.$page.$main_lang,$result_news_by_categories_arr, 100);
        }
        else
        {
            $result_news_by_categories_arr = $cache->retrieve('result_news_by_categories_'.$category_id.$category_slug.$page.$main_lang);
        }

        $title = $title.' | '.$current_category_name;
        $description = $current_category_name;
    }
    elseif($do=="tags")
    {
        $tag_id = intval($_GET['id']);
        $tag_slug = mysqli_real_escape_string($db,$_GET['slug']);

        // Redirected url from old site
        if(isset($tag_slug) && !empty($tag_slug) && !isset($_GET['id']))
        {
            $tag_slug = str_replace("/","",$tag_slug);

            $stmt_select = mysqli_prepare($db,
                "SELECT 
                    `name`,
                    `auto_id`                               
                    FROM `tags`
                    WHERE `lang_id`=(?) and `active`=(?)
                    order by `created_at` desc");
            $stmt_select->bind_param('ii', $main_lang,$active_status);
            $stmt_select->execute();
            $result_news_by_slug = $stmt_select->get_result();
            $stmt_select->close();

            while($row=$result_news_by_slug->fetch_assoc())
            {
                if($tag_slug==slugGenerator($row['name'],'-',false,true))
                {
                    header("Location: ".SITE_PATH."/tags/".slugGenerator($row['name'])."-".$row['auto_id']);
                    exit('Redirecting...');
                }
            }
        }

        // Get current tag
        if(!$cache->isCached('current_tag_'.$tag_id.$tag_slug.$main_lang))
        {
            $stmt_select = mysqli_prepare($db,
                "SELECT
                        `name`,
                        `auto_id`
                        FROM `tags`
                        WHERE `lang_id`=(?) and `active`=(?) and `auto_id`=(?)
                        ");
            $stmt_select->bind_param('iii', $main_lang,$active_status,$tag_id);
            $stmt_select->execute();
            $stmt_select->bind_result($current_tag_name,$current_tag_id);
            $stmt_select->fetch();
            $stmt_select->close();

            $cache->store('current_category_'.$tag_id.$tag_slug.$main_lang,[
                'current_tag_name' =>  $current_tag_name,
                'current_tag_id'   =>  $current_tag_id
            ],100);
        }
        else
        {
            $cache_current_tag_result = $cache->retrieve('current_tag_'.$tag_id.$tag_slug.$main_lang);
            $current_tag_name = $cache_current_tag_result['current_tag_name'];
            $current_tag_id = $cache_current_tag_result['current_tag_id'];
        }

        if($tag_slug!=slugGenerator($current_tag_name) || $current_tag_id!=$tag_id)
        {
            header("Location: ".SITE_PATH."/404");
            exit('Redirecting...');
        }

        // Paginator
        $limit = 8;

        $stmt_select = mysqli_prepare($db,
            "SELECT 
                    `news`.`id`
                    FROM `news`
                    LEFT JOIN `news_tags` ON `news`.`auto_id`=`news_tags`.`news_id`
                    WHERE `news`.`lang_id`=(?) and `news`.`active`=(?) and `news_tags`.`tag_id`=(?) and `news`.`title`!=''
                    ");
        $stmt_select->bind_param('iii', $main_lang,$active_status,$tag_id);
        $stmt_select->execute();
        $stmt_select->store_result();

        $count_rows = $stmt_select->num_rows;
        $max_page=ceil($count_rows/$limit);
        $page=intval($_GET["page"]); if($page<1) $page=1; if($page>$max_page) $page=$max_page;
        if($page<1) $page = 1;
        $start=$page*$limit-$limit;
        $stmt_select->close();

        if(!$cache->isCached('result_news_by_tags_'.$tag_id.$tag_slug.$page.$main_lang))
        {
            // Get news by tag
            $stmt_select = mysqli_prepare($db,
                "SELECT 
                    `news`.`auto_id`,
                    `news`.`title`,
                    `news`.`image_name`,
                    `news`.`short_text`,
                    `news`.`created_at`                                
                    FROM `news`
                    LEFT JOIN `news_tags` ON `news`.`auto_id`=`news_tags`.`news_id`
                    WHERE `news`.`lang_id`=(?) and `news`.`active`=(?) and `news_tags`.`tag_id`=(?) and `news`.`title`!=''
                    order by `news`.`created_at` desc limit $start,$limit");

            $stmt_select->bind_param('iii', $main_lang,$active_status,$tag_id);
            $stmt_select->execute();
            $result_news_by_tags = $stmt_select->get_result();
            $stmt_select->close();

            $result_news_by_tags_arr = [];
            while($row=$result_news_by_tags->fetch_assoc())
            {
                $result_news_by_tags_arr[] = $row;
            }

            $cache->store('result_news_by_tags_'.$tag_id.$tag_slug.$page.$main_lang,$result_news_by_tags_arr, 100);
        }
        else
        {
            $result_news_by_tags_arr = $cache->retrieve('result_news_by_tags_'.$tag_id.$tag_slug.$page.$main_lang);
        }

        $title = $title.' | '.$current_tag_name;
        $description = $current_tag_name;
    }
    elseif($do=="search")
    {
        $search = mysqli_real_escape_string($db,$_GET['search']);

        if(strlen($search)>=3)
        {
            $search_param = "%{$search}%";

            // Get searched news
            $limit = 8;

            $stmt_select = mysqli_prepare($db,
                "SELECT 
                    `news`.`id`
                    FROM `news`
                    WHERE `news`.`lang_id`=(?) and `news`.`active`=(?) and (`news`.`title` LIKE (?) or `news`.`text` LIKE (?)) and `news`.`title`!=''
                    ");
            $stmt_select->bind_param('iiss', $main_lang,$active_status,$search_param,$search_param);
            $stmt_select->execute();
            $stmt_select->store_result();

            $count_rows = $stmt_select->num_rows;
            $max_page=ceil($count_rows/$limit);
            $page=intval($_GET["page"]); if($page<1) $page=1; if($page>$max_page) $page=$max_page;
            if($page<1) $page = 1;
            $start=$page*$limit-$limit;
            $stmt_select->close();

            // Get news by search
            $stmt_select = mysqli_prepare($db,
                "SELECT 
                `news`.`auto_id`,
                `news`.`title`,
                `news`.`image_name`,
                `news`.`short_text`,
                `news`.`created_at`                                
                FROM `news`
                WHERE `news`.`lang_id`=(?) and `news`.`active`=(?) and (`news`.`title` LIKE (?) or `news`.`text` LIKE (?)) and `news`.`title`!=''
                order by `news`.`created_at` desc limit $start,$limit");

            $stmt_select->bind_param('iiss', $main_lang,$active_status,$search_param,$search_param);
            $stmt_select->execute();
            $result_news_by_search = $stmt_select->get_result();
            $stmt_select->close();

            $result_news_by_search_arr = [];
            while($row=$result_news_by_search->fetch_assoc())
            {
                $result_news_by_search_arr[] = $row;
            }
        }
        else
        {
            $count_search = 0;
        }

        $title = $title.' | '.$lang1;
    }
    elseif($do=="date")
    {
        $year = intval($_GET['year']);
        $month = intval($_GET['month']);

        // Paginator
        $limit = 8;

        $stmt_select = mysqli_prepare($db,
            "SELECT
                        `id`
                        FROM `news`
                        WHERE `lang_id`=(?) and `active`=(?) and MONTH(FROM_UNIXTIME(`news`.`created_at`))=(?) and YEAR(FROM_UNIXTIME(`news`.`created_at`))=(?) and `news`.`title`!=''
                        order by `created_at` desc");
        $stmt_select->bind_param('iiii', $main_lang,$active_status,$month,$year);
        $stmt_select->execute();
        $stmt_select->store_result();

        $count_rows = $stmt_select->num_rows;
        $max_page=ceil($count_rows/$limit);
        $page=intval($_GET["page"]); if($page<1) $page=1; if($page>$max_page) $page=$max_page;
        if($page<1) $page = 1;
        $start=$page*$limit-$limit;
        $stmt_select->close();

        if(!$cache->isCached('result_news_by_date_'.$year.$month.$page.$main_lang))
        {
            // Get games by category
            $stmt_select = mysqli_prepare($db,
                "SELECT
                        `title`,
                        `image_name`,
                        `short_text`,
                        `view`,
                        `created_at`,
                        `auto_id`
                        FROM `news`
                        WHERE `lang_id`=(?) and `active`=(?) and MONTH(FROM_UNIXTIME(`news`.`created_at`))=(?) and YEAR(FROM_UNIXTIME(`news`.`created_at`))=(?) and `news`.`title`!=''
                        order by `created_at` desc limit $start,$limit");

            $stmt_select->bind_param('iiii', $main_lang,$active_status,$month,$year);
            $stmt_select->execute();
            $result = $stmt_select->get_result();
            $stmt_select->close();

            $news_by_date_arr = [];
            while($row=$result->fetch_assoc())
            {
                $news_by_date_arr[] = $row;
            }

            $cache->store('result_news_by_date_'.$year.$month.$page.$main_lang,$news_by_date_arr, 100);
        }
        else
        {
            $news_by_date_arr = $cache->retrieve('result_news_by_date_'.$year.$month.$page.$main_lang);
        }

        $month_str = getMonth($month,$main_lang);

        $title = $title.' | '.$month_str." ".$year;
    }
    elseif($do=="haqqimda")
    {
        header("Location: ".SITE_PATH."/about");
        exit('Redirecting...');
    }

?>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta property="description" content="<?=$description?>"/>
<meta property="keywords" content="<?=$keywords?>"/>
<meta property="og:type" content="article" />
<meta property="og:image" content="<?=$image?>"/>
<meta property="og:image:width" content="2000" />
<meta property="og:image:height" content="2000" />
<meta property="og:title" content="<?=$title?>"/>
<meta property="og:url" content="<?=$og_url?>"/>
<meta property="og:description" content="<?=$description?>"/>

<link rel='stylesheet' id='wp-block-library-css' href='<?=SITE_PATH?>/assets/css/style.minaead.css?ver=5.0.3' type='text/css' media='all'/>
<link rel='stylesheet' id='bootstrap-css' href='<?=SITE_PATH?>/assets/css/bootstrap/bootstrap.min7433.css?ver=3.3.7' type='text/css' media='all'/>
<link rel='stylesheet' id='bootstrap-theme-css' href='<?=SITE_PATH?>/assets/css/bootstrap/bootstrap-theme.min7433.css?ver=3.3.7' type='text/css' media='all'/>
<link rel='stylesheet' id='ammi-ie10-viewport-bug-workaround-css' href='<?=SITE_PATH?>/assets/css/ie10-viewport-bug-workaround8a54.css?ver=1.0.0' type='text/css' media='all'/>
<link rel='stylesheet' id='font-awesome-css' href='<?=SITE_PATH?>/assets/css/font-awesome.min1849.css?ver=4.7.0' type='text/css' media='all'/>
<link rel='stylesheet' id='owl-carousel-css' href='<?=SITE_PATH?>/assets/css/owl-carousel/owl.carousel3ba1.css?ver=1.3.3' type='text/css' media='all'/>
<link rel='stylesheet' id='owl-theme-css' href='<?=SITE_PATH?>/assets/css/owl-carousel/owl.theme3ba1.css?ver=1.3.3' type='text/css' media='all'/>
<link rel='stylesheet' id='magnific-popup-css' href='<?=SITE_PATH?>/assets/css/magnific-popupf488.css?ver=1.1.0' type='text/css' media='all'/>
<link rel='stylesheet' id='ammi-style-css' href='<?=SITE_PATH?>/assets/css/style20b9.css?ver=1.0.2' type='text/css' media='all'/>
<link rel='stylesheet' id='ammi-style-css' href='<?=SITE_PATH?>/assets/css/back.css' type='text/css' media='all'/>
<!--[if lt IE 9]>
<script type='text/javascript' src='<?=SITE_PATH?>/assets/js/html5shiv.min.js?ver=3.7.3'></script>
<![endif]-->
<!--[if lt IE 9]>
<script type='text/javascript' src='<?=SITE_PATH?>/assets/js/respond.min.js?ver=1.4.2'></script>
<![endif]-->
<script type='text/javascript' src='<?=SITE_PATH?>/assets/js/jquery/jqueryb8ff.js?ver=1.12.4'></script>
<script type='text/javascript' src='<?=SITE_PATH?>/assets/js/jquery/jquery-migrate.min330a.js?ver=1.4.1'></script>

<!--Favicon-->
<link rel="icon" href="<?=SITE_PATH?>/assets/favicon/cropped-favicon-32x32.png" sizes="32x32"/>
<link rel="icon" href="<?=SITE_PATH?>/assets/favicon/cropped-favicon-192x192.png" sizes="192x192"/>
<link rel="apple-touch-icon-precomposed" href="<?=SITE_PATH?>/assets/favicon/cropped-favicon-180x180.png"/>
<meta name="msapplication-TileImage" content="<?=SITE_PATH?>/assets/favicon/cropped-favicon-270x270.png"/>
<!-- Favicon -->

<title>Fizuli Hüseynov | <?=$title?></title>

<!-- Google Analytics -->

<!-- End Google Analytics -->

<script>
    var base_url = '<?=SITE_PATH?>';
</script>



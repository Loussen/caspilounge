<?php
// Paginator
$limit=intval($_GET["limit"]);
if($limit!=15 && $limit!=25 && $limit!=50 && $limit!=100 && $limit!=999999) $limit=15;
$query_count="select id from $do where lang_id='$main_lang' ";
$count_rows=mysqli_num_rows(mysqli_query($db,$query_count));
$max_page=ceil($count_rows/$limit);
$page=intval($_GET["page"]); if($page<1) $page=1; if($page>$max_page) $page=$max_page; if($page<1) $page=1;
$start=$page*$limit-$limit;
//

$add=intval($_GET["add"]);
$edit=intval($_GET["edit"]);
$delete=intval($_GET["delete"]);
$up=intval($_GET["up"]);
$down=intval($_GET["down"]);

if($edit>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where auto_id='$edit' "))==0)
{
    header("Location: index.php?do=$do");
    exit;
}

if($_POST) // Add && edit
{
    extract($_POST);

    $auto_id=mysqli_fetch_assoc(mysqli_query($db,"select auto_id from $do order by auto_id desc"));
    $auto_id=intval($auto_id["auto_id"])+1;
    $active=1;

    $image_tmp = $_FILES["image_file"]["tmp_name"];

    if($edit>0)
    {
        $info_edit=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$edit' "));
        $add_where="and auto_id='$edit' ";
        $auto_id=$edit;
        $active=$info_edit["active"];
    }
    else $add_where="";

    $sql=mysqli_query($db,"select * from diller where aktivlik=1 order by sira");
    while($row=mysqli_fetch_assoc($sql))
    {
        $title="title_".$row["id"]; $title=mysqli_real_escape_string($db,htmlspecialchars($$title));
        $short_text="short_text_".$row["id"]; $short_text=mysqli_real_escape_string($db,htmlspecialchars($$short_text));
        $text="text_".$row["id"]; $text=mysqli_real_escape_string($db,htmlspecialchars($$text));

//        if($title=='' && $row["id"]!=$main_lang) {$c='name'; $$c=get_lang_val($do,$auto_id,$c);}
//        if($short_text=='' && $row["id"]!=$main_lang) {$c='short_text'; $$c=get_lang_val($do,$auto_id,$c);}
//        if($text=='' && $row["id"]!=$main_lang) {$c='text'; $$c=get_lang_val($do,$auto_id,$c);}

        $time = time();

        $slug = slugGenerator($title);

        if(mysqli_num_rows(mysqli_query($db,"select id from $do where lang_id='$row[id]' $add_where"))>0 && $edit>0)
        {
            mysqli_query($db,"update $do set title='$title',short_text='$short_text',text='$text',updated_at='$time' where lang_id='$row[id]' $add_where");
        }
        else
        {
            mysqli_query($db,"insert into $do values (0,'$title','','$short_text','$text','$slug',0,'$active', '$row[id]', '$auto_id', '$time',0) ");
        }
    }

    // News_category
    if($edit>0)
    {
        mysqli_query($db,"delete from `news_cat` where `news_id`='$edit'");
    }
    foreach ($_POST['category_id'] as $cat_id)
    {
        mysqli_query($db,"insert into `news_cat` values (0,'$auto_id','$cat_id')");
    }

    // News_tags
    if($edit>0)
    {
        mysqli_query($db,"delete from `news_tags` where `news_id`='$edit'");
    }
    if(strlen($_POST["hidden-tags"])>=2)
    {
        $explode_tags = explode(",",$_POST['hidden-tags']);

        foreach ($explode_tags as $tag)
        {
            if(mysqli_num_rows(mysqli_query($db,"select `id` from `tags` where `name`='$tag'"))>0)
            {
                $found_tag = mysqli_fetch_assoc(mysqli_query($db,"select `auto_id` from `tags` where `name`='$tag'"));

                $auto_id_tag = $found_tag['auto_id'];
            }
            else
            {
                $last_order_tag=mysqli_fetch_assoc(mysqli_query($db,"select order_number from tags order by order_number desc"));
                $last_order_tag=intval($last_order_tag["order_number"])+1;
                $auto_id_tag=mysqli_fetch_assoc(mysqli_query($db,"select auto_id from tags order by auto_id desc"));
                $auto_id_tag=intval($auto_id_tag["auto_id"])+1;
                $active=1;

                $sql=mysqli_query($db,"select * from diller where aktivlik=1 order by sira");
                while($row=mysqli_fetch_assoc($sql))
                {
                    $time = time();

                    mysqli_query($db,"insert into `tags` values (0,'$tag','$last_order_tag', '$active', '$row[id]', '$auto_id_tag', '$time',0) ");
                }
            }

            mysqli_query($db,"insert into `news_tags` values (0,'$auto_id','$auto_id_tag')");
        }
    }

//    if($edit>0) $this_id=$edit;
//    else
//    {
//        $this_id=mysqli_insert_id($db);
//        $this_id=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where id='$this_id' "));
//        $this_id=$this_id["auto_id"];
//    }

    // Image upload
    if($image_tmp!="")
    {
        $image_type=$_FILES["image_file"]["type"];
        $image_name=strtolower($_FILES["image_file"]["name"]);
        $explode_name = explode(".",$image_name);
        $type=end($explode_name);
        $image_access=false;
        if($type=="jpg" || $type=="bmp"  || $type=="png" || $type=="gif" || $type=="jpeg") $image_access=true;
        if($image_access==true)
        {
            $image_upload_name = substr(sha1(mt_rand()),17,15)."-".pathinfo($_FILES['image_file']['name'], PATHINFO_FILENAME).".".$type;

            if($edit>0)
            {
                $old_image_name = mysqli_fetch_assoc(mysqli_query($db,"SELECT image_name FROM $do WHERE auto_id='$auto_id'"));
                @unlink("../images/news/".$old_image_name['image_name']);
            }

            move_uploaded_file($image_tmp,'../images/news/'.$image_upload_name);

            compress('../images/news/'.$image_upload_name, '../images/news/'.$image_upload_name, 80);

            mysqli_query($db,"update $do set image_name='$image_upload_name' where auto_id='$auto_id'");
        }
    }

    $ok="Data has been successfully saved.";
    $edit=0;
}


if($delete>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where auto_id='$delete' "))>0)
{
    $data = mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$delete' "));
    @unlink('../images/news/'.$data["image_name"]);

//    $gallery = mysqli_query($db,"select image_name from gallery where news_id='$delete' ");
//
//    if(mysqli_num_rows($gallery)>0)
//    {
//        while($row=mysqli_fetch_assoc($gallery))
//        {
//            @unlink('../images/news_gallery/'.$row["image_name"]);
//        }
//    }

    mysqli_query($db,"delete from $do where auto_id='$delete' ");
    mysqli_query($db,"delete from `news_cat` where `news_id`='$delete' ");
    mysqli_query($db,"delete from `news_tags` where `news_id`='$delete' ");
    $ok="Data has been successfully deleted.";
}
elseif($delete_img>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where auto_id='$delete_img' and image_name!='' "))>0)
{
    $info_image_name=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$delete_img' and image_name!='' "));
    mysqli_query($db,"update $do set image_name='' where auto_id='$delete_img' and image_name!='' ");
    $ok="Image has been deleted";
    @unlink('../images/news/'.$info_image_name['image_name']);
}
?>
<script type="text/JavaScript">
    function MM_jumpMenu(targ,selObj,restore){ //v3.0
        eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
        if (restore) selObj.selectedIndex=0;
    }
</script>
<div class="onecolumn">
    <div class="header">
        <span>News</span>
        <div class="switch">
            <table cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <?php
                    $sql=mysqli_query($db,"select * from diller where aktivlik=1 order by sira");
                    if(mysqli_num_rows($sql)>1 and ($add==1 || $edit>0) )
                    {
                        while($row=mysqli_fetch_assoc($sql))
                        {
                            echo '<td><input type="button" id="tab_lang'.$row["id"].'" onclick="tab_select(this.id)" class="left_switch" value="'.$row["ad"].'" style="width:50px"/></td>';
                        }
                    }
                    ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br class="clear"/>
    <div class="content">
        <?php
            if($ok!="") echo '<div class="alert_success"><p><img src="images/icon_accept.png" alt="success" class="mid_align"/>'.$ok.'</p></div>';
            if($error!="") echo '<div class="alert_error"><p><img src="images/icon_error.png" alt="delete" class="mid_align"/>'.$error.'</p></div>';
        ?>

        <!-- Content start-->
        <form action="index.php?do=<?php echo $do; ?>&page=<?php echo $page; if($edit>0) echo '&edit='.$edit; ?>" method="post" id="form_login" name="form_login" enctype="multipart/form-data">
            <a href="index.php?do=<?php echo $do; ?>&add=1" style="margin-right:50px"><img src="images/icon_add.png" alt="" /> <b style="">Create new</b></a>
            <hr class="clear" />
            <?php
                if($add==1 || $edit>0) $hide=""; else $hide="hide";

                $information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$edit' and lang_id='$main_lang' "));
                $sql_categories = mysqli_query($db,"SELECT `auto_id`,`name` FROM `categories` WHERE lang_id='$main_lang' and active=1");

                $cat_ids = [];
                if($edit>0)
                {
                    $sql_news_cat = mysqli_query($db,"SELECT `news_id`,`cat_id` FROM `news_cat` WHERE `news_id`='$edit'");

                    while($row_news_cat=mysqli_fetch_assoc($sql_news_cat))
                    {
                        $cat_ids[] = $row_news_cat['cat_id'];
                    }
                }

                echo '<div class="'.$hide.'">';
                echo 'Choose category : <br />
                <select name="category_id[]" required id="category_id" style="width: 200px; height: 200px;" multiple>';
                while($row_categories=mysqli_fetch_assoc($sql_categories))
                {
                    if(in_array($row_categories['auto_id'],$cat_ids))
                    {
                        echo '<option value="'.$row_categories['auto_id'].'" selected="selected">'.$row_categories['name'].'</option>';
                    }
                    else
                    {
                        echo '<option value="'.$row_categories['auto_id'].'">'.$row_categories['name'].'</option>';
                    }
                }
                echo '</select><br class="clear" /><br />
                </div>';
                $sql=mysqli_query($db,"select * from diller where aktivlik=1 order by sira");
                while($row=mysqli_fetch_assoc($sql))
                {
                    if($row["id"]==$main_lang) $required = "required"; else $required = "";
                    if($add==1 || $edit>0) $hide=""; else $hide="hide";
                    $information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$edit' and lang_id='$row[id]' "));

                    if($lang_count>1) echo '<div id="tab'.$row["id"].'_content" class="tab_content '.$hide.'">';
                    else echo '<div class="'.$hide.'">';

                    echo 'Title : <br />
                      <input type="text" name="title_'.$row["id"].'" value="'.$information["title"].'" style="width:800px" />
                      <br /><br />
                      Short text : <br />
                      <textarea name="short_text_'.$row["id"].'" cols="80" rows="11">'.$information["short_text"].'</textarea>
                      <br /><br />
                      Text : <br />
                        <textarea name="text_'.$row["id"].'" rows="1" cols="1" id="editor'.$row["sira"].'">'.$information["text"].'</textarea>
                    <br /><br />
                    Slug : <br />
                      <input disabled readonly type="text" value="'.$information["slug"].'" style="width:800px" />
                      <br /><br />
                    </div>
                      ';
                }

                $information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$edit' and lang_id='$main_lang'"));
                echo '<div class="'.$hide.'">';
                if($information["image_name"]!="" && $edit>0)
                {
                    $image='<ul class="media_photos" style="margin-left:450px;margin-top:-25px; margin-right: 40px;"><li><a rel="slide" href="../images/news/'.$information["image_name"].'?rand='.rand(0,10000).'" title="">
                    Current image : <img src="../images/news/'.$information ["image_name"].'?rand='.rand(0,10000).'" alt="" width="120" /></a>
                    <br class="clear" />
                  <a href="index.php?do='.$do.'&delete_img='.$information["auto_id"].'" title="Delete" class="delete"><img src="images/icon_delete.png" alt="" /></a></li></ul>';
                    $height_photo_div = "height:120px;";
                }
                else
                {
                    $height_photo_div = "";
                    $image='';
                }

                echo '<div style="padding: 30px 0 50px 15px;border: 1px solid #ddd; margin-top: 5px; '.$height_photo_div.'">
                        <div style="float: left;">Choose image : <input name="image_file" id="image_file" type="file" /></div><div style="float: right;">'.$image.'</div></div><br />';

                if($information["image_name"]!="" && $edit>0) echo '<br /><br />';
                echo 'Tags : <br /><input type="text" style="width: 100%;" name="tags" placeholder="Tags" class="typeahead tm-input tm-input-info"/><br /><br />';
                echo '<input type="submit" id="save" name="button" value=" Save " />
                  <hr class="clear" />
                  <br class="clear" /></div>';
            ?>
        </form>
        <div>

            <div style="float: left;">
                <a href="javascript:void(0);" class="chbx_del"><img src="images/icon_delete.png" alt="" title="" /></a>
                <a href="javascript:void(0);" class="chbx_active" data-val="1"><img src="images/1_lamp.png" alt="" title="" /></a>
                <a href="javascript:void(0);" class="chbx_active" data-val="2"><img src="images/0_lamp.png" alt="" title="" /></a>
                <input type="hidden" value="index.php?do=<?=$do?>&page=<?=$page?>&limit=<?=$limit?>&forId=2" id="current_link" />
            </div>

<!--            <div style="text-align: center; float: left; margin-left: 10%;">-->
<!--                <select name="category_id" onchange="MM_jumpMenu('parent',this,0)">-->
<!--                    <option value="index.php?do=--><?//=$do?><!--&category_id=0" selected disabled hidden>Choose category</option>-->
<!--                    <option --><?//=($_GET['category_id']=="all") ? "selected" : ""?><!-- value="index.php?do=--><?//=$do?><!--&category_id=all">ALL</option>-->
<!--                    --><?php
//                    $sql=mysqli_query($db,"select auto_id,name from categories where lang_id='$main_lang' order by order_number asc");
//                    while($row=mysqli_fetch_assoc($sql))
//                    {
//                        if($row["auto_id"]==intval($_GET['category_id'])) echo '<option value="index.php?do='.$do.'&category_id='.$row["auto_id"].'" selected="selected">'.$row["name"].'</option>';
//                        else echo '<option value="index.php?do='.$do.'&category_id='.$row["auto_id"].'">'.$row["name"].'</option>';
//                    }
//                    ?>
<!--                </select>-->
<!--            </div>-->

            <div style="float: right;">
                <u>Show data's limit:</u>
                <select name="limit" id="limit" onchange="MM_jumpMenu('parent',this,0)" style="margin-bottom: 5px;">
                    <option value="index.php?<?=addFullUrl(array('limit'=>15,'page'=>0))?>" <?php if($limit==15) echo 'selected="selected"'; ?>>15</option>
                    <option value="index.php?<?=addFullUrl(array('limit'=>25,'page'=>0))?>" <?php if($limit==25) echo 'selected="selected"'; ?>>25</option>
                    <option value="index.php?<?=addFullUrl(array('limit'=>50,'page'=>0))?>" <?php if($limit==50) echo 'selected="selected"'; ?>>50</option>
                    <option value="index.php?<?=addFullUrl(array('limit'=>100,'page'=>0))?>" <?php if($limit==100) echo 'selected="selected"'; ?>>100</option>
                    <option value="index.php?<?=addFullUrl(array('limit'=>999999,'page'=>0))?>" <?php if($limit==999999) echo 'selected="selected"'; ?>>ALL</option>
                </select>
            </div>
        </div>

        <br class="clear" />
        <?php
        echo '<table class="data" width="100%" cellpadding="0" cellspacing="0" style="margin: 15px 0;"><thead><tr>
                <th style="width:10%"><input type="checkbox" data-val="0" name="all_check" id="hamisini_sec" value="all_check" /> №</th>
                <th style="width:70%">Title</th>
                <th style="width:30%">Editing</th>
</tr></thead><tbody>';
        $query=str_replace("select id ","select * ",$query_count);
        $query.=" order by auto_id desc limit $start,$limit";
        $sql=mysqli_query($db,"select * from $do where lang_id='$main_lang' ".$add_information_sql." order by created_at desc limit $start,$limit");
        $i = $start+1;
        while($row=mysqli_fetch_assoc($sql))
        {
            echo '<tr>
                    <td><input type="checkbox" id="chbx_'.$row["auto_id"].'" value="'.$row["auto_id"].'" onclick="chbx_(this.id)" /> '.$i.'</td>
					<td>'.more_string($row['title'],200).'</td>
					<td>
						<a href="index.php?do='.$do.'&page='.$page.'&edit='.$row["auto_id"].'"><img src="images/icon_edit.png" alt="" title="Edit" /></a>
						<a href="index.php?do='.$do.'&page='.$page.'&delete='.$row["auto_id"].'" class="delete"><img src="images/icon_delete.png" alt="" title="Sil" /></a>';

            if($row["active"]==1) $title='Active'; else $title='Deactive';
            echo '<img src="images/'.$row["active"].'_lamp.png" title="'.$title.'" border="0" align="absmiddle" style="cursor:pointer" id="info_'.$row["auto_id"].'" onclick="aktivlik(\''.$do.'\',this.id,this.title)"  />';
            echo '</td>
				</tr>';
            $i++;
        }
        echo '</tbody></table>';
        ?>
        <div class="ps_"><?=page_nav()?></div>
        <?php
            // Paginator
            echo '<div class="pagination">';
            $show=3;
            if($page>$show+1) echo '<a href="index.php?do='.$do.'&page=1">First page</a>';
            if($page>1) echo '<a href="index.php?do='.$do.'&page='.($page-1).'">Previous page</a>';
            for($i=$page-$show;$i<=$page+$show;$i++)
            {
                if($i==$page) $class='class="active"'; else $class='';;
                if($i>0 && $i<=$max_page) echo '<a href="index.php?do='.$do.'&page='.$i.'" '.$class.'>'.$i.'</a>';
            }
            if($page<$max_page) echo '<a href="index.php?do='.$do.'&page='.($page+1).'">Next page</a>';
            if($page<$max_page-$show && $max_page>1) echo '<a href="index.php?do='.$do.'&page='.$max_page.'"> Last page </a>';
            echo '</div>';
            // Paginator
        ?>
        <br class="clear" />
        <!-- Content end-->
    </div>
</div>

<script type='text/javascript'>
    <?php
        if($edit>0)
        {
            $get_news_tags = mysqli_query($db,"select `tag_id` from `news_tags` where `news_id`='$edit'");

            $tags_arr = [];
            while($row_news_tags=mysqli_fetch_assoc($get_news_tags))
            {
                $get_tags = mysqli_query($db,"select `auto_id`,`name` from `tags` where `auto_id`='$row_news_tags[tag_id]'");

                while($row_tags=mysqli_fetch_assoc($get_tags))
                {
                    $tags_arr[$row_tags['auto_id']] = $row_tags['name'];
                }
            }

            $tags_arr = json_encode($tags_arr);
            echo "var tags_arr = ". $tags_arr . ";\n";
        }
        else
            echo "var tags_arr = []";
    ?>
</script>

<script type="text/javascript">
    $(document).ready(function() {
        var tagApi = $(".tm-input").tagsManager({
            prefilled: tags_arr
        });


        jQuery(".typeahead").typeahead({
            name: 'tags',
            displayKey: 'name',
            source: function (query, process) {
                return $.get('ajax.php', { query: query }, function (data) {
                    data = $.parseJSON(data);
                    return process(data);
                });
            },
            afterSelect :function (item){
                tagApi.tagsManager("pushTag", item);
            }
        });

        jQuery(".tm-input.tm-input-typeahead").typeahead(null, {
            name: 'countries',
            displayKey: 'name',
            source: countries.ttAdapter()
        }).on('typeahead:selected', function (e, d) {
            tagApi.tagsManager("pushTag", d.name);
        });
    });
</script>
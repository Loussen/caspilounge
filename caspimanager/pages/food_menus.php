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
    $last_order=mysqli_fetch_assoc(mysqli_query($db,"select order_number from $do order by order_number desc"));
    $last_order=intval($last_order["order_number"])+1;
    $auto_id=mysqli_fetch_assoc(mysqli_query($db,"select auto_id from $do order by auto_id desc"));
    $auto_id=intval($auto_id["auto_id"])+1;
    $active=1;

    $image_tmp = $_FILES["image_file"]["tmp_name"];

    if($edit>0)
    {
        $info_edit=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$edit' "));
        $add_where="and auto_id='$edit' ";
        $auto_id=$edit;
        $last_order=$info_edit["order_number"];
        $active=$info_edit["active"];
    }
    else $add_where="";

    $sql=mysqli_query($db,"select * from diller where aktivlik=1 order by sira");
    while($row=mysqli_fetch_assoc($sql))
    {

        $time = time();

        if(mysqli_num_rows(mysqli_query($db,"select id from $do where lang_id='$row[id]' $add_where"))>0 && $edit>0)
        {
            mysqli_query($db,"update $do set link='$link',updated_at='$time' where lang_id='$row[id]' $add_where");
        }
        else
        {
            mysqli_query($db,"insert into $do values (0,'','$link','$last_order','$active', '$row[id]', '$auto_id', '$time',0) ");
        }
    }

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
                @unlink("../images/food_menus/".$old_image_name['image_name']);
            }

            move_uploaded_file($image_tmp,'../images/food_menus/'.$image_upload_name);

//            compress('../images/food_menus/'.$image_upload_name, '../images/food_menus/'.$image_upload_name, 100);

            mysqli_query($db,"update $do set image_name='$image_upload_name' where auto_id='$auto_id'");
        }
    }

    $ok="Data has been successfully saved.";
    $edit=0;
}


if($delete>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where auto_id='$delete' "))>0)
{
    $data = mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$delete' "));
    @unlink('../images/food_menus/'.$data["image_name"]);

    mysqli_query($db,"delete from $do where auto_id='$delete' ");
    $ok="Data has been successfully deleted.";
    $new_order=1;
    $sql=mysqli_query($db,"select * from $do where lang_id='$main_lang' order by order_number");
    $order_update='';
    while($row=mysqli_fetch_assoc($sql))
    {
        $order_update.=" when auto_id='$row[auto_id]' then '$new_order' ";
        $new_order++;
    }
    $query_update="update $do set order_number=case".$order_update."else order_number end;";
    if($order_update!='') mysqli_query($db,$query_update);
}
elseif($delete_img>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where auto_id='$delete_img' and image_name!='' "))>0)
{
    $info_image_name=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$delete_img' and image_name!='' "));
    mysqli_query($db,"update $do set image_name='' where auto_id='$delete_img' and image_name!='' ");
    $ok="Image has been deleted";
    @unlink('../images/food_menus/'.$info_image_name['image_name']);
}
elseif($up>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where auto_id='$up' "))>0)
{
    $data = mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$up' "));
    $current_order=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$up'"));
    $current_order=$current_order["order_number"];
    if($current_order>1)
    {
        $previous_order=$current_order-1;
        mysqli_query($db,"update $do set order_number='-1' where order_number='$previous_order'");
        mysqli_query($db,"update $do set order_number='$previous_order' where order_number='$current_order'");
        mysqli_query($db,"update $do set order_number='$current_order' where order_number='-1'");
    }
}
elseif($down>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where auto_id='$down' "))>0)
{
    $data = mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$down' "));
    $current_order=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$down'"));
    $current_order=$current_order["order_number"];
    $last_order=mysqli_fetch_assoc(mysqli_query($db,"select order_number from $do order by order_number desc"));
    $last_order=$last_order["order_number"];
    if($current_order<$last_order)
    {
        $next_order=$current_order+1;
        mysqli_query($db,"update $do set order_number='-1' where order_number='$next_order'");
        mysqli_query($db,"update $do set order_number='$next_order' where order_number='$current_order'");
        mysqli_query($db,"update $do set order_number='$current_order' where order_number='-1'");
    }
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
        <span>Menus images</span>
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

                echo '<div class="'.$hide.'">';
                echo 'Link : <br />
                    <input type="text" name="link" value="'.$information["link"].'" style="width:800px" /><br /><br />
                    </div>';

                $information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where auto_id='$edit' and lang_id='$main_lang'"));
                echo '<div class="'.$hide.'">';
                if($information["image_name"]!="" && $edit>0)
                {
                    $image='<ul class="media_photos" style="margin-left:450px;margin-top:-25px; margin-right: 40px;"><li><a rel="slide" href="../images/food_menus/'.$information["image_name"].'?rand='.rand(0,10000).'" title="">
                    Current image : <img src="../images/food_menus/'.$information ["image_name"].'?rand='.rand(0,10000).'" alt="" width="120" /></a>
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
                <th style="width:25%">Image</th>
                <th style="width:50%">Link</th>
                <th style="width:30%">Editing</th>
</tr></thead><tbody>';
        $query=str_replace("select id ","select * ",$query_count);
        $query.=" order by auto_id desc limit $start,$limit";
        $sql=mysqli_query($db,"select * from $do where lang_id='$main_lang' order by order_number asc limit $start,$limit");
        $i = $start+1;
        while($row=mysqli_fetch_assoc($sql))
        {
            echo '<tr>
                    <td><input type="checkbox" id="chbx_'.$row["auto_id"].'" value="'.$row["auto_id"].'" onclick="chbx_(this.id)" /> '.$i.'</td>
					<td><ul class="media_photos" style="padding:0;margin-top:0px;margin-bottom:-40px"><li><a rel="slide" href="../images/food_menus/'.$row["image_name"].'" title=""><img src="../images/food_menus/'.$row["image_name"].'?rand='.rand(0,10000).'" width="100" height="60"></a></li></ul></td>
					<td><a href="'.$row['link'].'" target="_blank">'.$row['link'].'</a></td>
					<td>
						<a href="index.php?do='.$do.'&page='.$page.'&edit='.$row["auto_id"].'"><img src="images/icon_edit.png" alt="" title="Edit" /></a>
						<a href="index.php?do='.$do.'&page='.$page.'&delete='.$row["auto_id"].'" class="delete"><img src="images/icon_delete.png" alt="" title="Sil" /></a>
						<a href="index.php?do='.$do.'&page='.$page.'&up='.$row["auto_id"].'"><img src="images/up.png" alt="" title="Up" /></a>
						<a href="index.php?do='.$do.'&page='.$page.'&down='.$row["auto_id"].'"><img src="images/down.png" alt="" title="Down" /></a>
						';

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
<?php
// Get params for filters
$food_id = intval($_GET['food_id']);
$quantity_from = intval($_GET['quantity_from']);
$quantity_to = intval($_GET['quantity_to']);
$total_from = $_GET['total_from'];
$total_to = $_GET['total_to'];
$datetimes = $_GET['datetimes'];

// Paginator
$limit=intval($_GET["limit"]);
if($limit!=15 && $limit!=25 && $limit!=50 && $limit!=100 && $limit!=999999) $limit=15;
$query_count="select id from $do where 1=1";

// Filters
$add_information_sql = "";
if($food_id>0)
{
    $query_count.=" and food_id='$food_id' ";
    $add_information_sql .= " and food_id='$food_id' ";
}
if($quantity_from>0)
{
    $query_count.=" and quantity>='$quantity_from' ";
    $add_information_sql .= " and quantity>='$quantity_from' ";
}
else
{
    $quantity_from = '';
}
if($quantity_to>0)
{
    $query_count.=" and quantity<='$quantity_to' ";
    $add_information_sql .= " and quantity<='$quantity_to' ";
}
else
{
    $quantity_to = '';
}
if($total_from>0)
{
    $query_count.=" and total>='$total_from' ";
    $add_information_sql .= " and total>='$total_from' ";
}
if($total_to>0)
{
    $query_count.=" and total<='$total_to' ";
    $add_information_sql .= " and total<='$total_to' ";
}
if(strlen($datetimes)>0)
{
    $datetime = explode("-",$datetimes);

    $fromDate = strtotime($datetime[0]);
    $toDate = strtotime($datetime[1]);

    $query_count.=" and created_at>='$fromDate' and created_at<='$toDate' ";
    $add_information_sql .= " and created_at>='$fromDate' and created_at<='$toDate' ";
}

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

if($edit>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where id='$edit' "))==0)
{
    header("Location: index.php?do=$do");
    exit;
}

if($_POST) // Add && edit
{
    extract($_POST);

    if($edit>0)
    {
        $info_edit=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where id='$edit' "));
        $add_where="and id='$edit' ";
    }
    else $add_where="";

    $time = time();

    if(mysqli_num_rows(mysqli_query($db,"select id from $do where 1=1 $add_where"))>0 && $edit>0)
    {
        $food_price=mysqli_fetch_assoc(mysqli_query($db,"select price from foods where auto_id='$info_edit[food_id]' "));

        mysqli_query($db,"update $do set quantity='$quantity',special_req='$special_req',updated_at='$time',total='$total' where 1=1 $add_where");
    }
    else
    {
        mysqli_query($db,"insert into $do values (0,'$firstname','$lastname','$phone','$email','$city','$street', '$apartment', '$floor','$no', '$time',0) ");
    }

    $ok="Data has been successfully saved.";
    $edit=0;
}

if($delete>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where id='$delete' "))>0)
{
    mysqli_query($db,"delete from $do where id='$delete' ");
    $ok="Data has been successfully deleted.";
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
        <span>Cart</span>
    </div>
    <br class="clear"/>
    <div class="content">
        <?php
            if($ok!="") echo '<div class="alert_success"><p><img src="images/icon_accept.png" alt="success" class="mid_align"/>'.$ok.'</p></div>';
            if($error!="") echo '<div class="alert_error"><p><img src="images/icon_error.png" alt="delete" class="mid_align"/>'.$error.'</p></div>';
        ?>

        <!-- Content start-->
        <form action="index.php?do=<?php echo $do; ?>&page=<?php echo $page; if($edit>0) echo '&edit='.$edit; ?>" method="post" id="form_login" name="form_login" enctype="multipart/form-data">
            <hr class="clear" />
            <?php
                if($add==1 || $edit>0) $hide=""; else $hide="hide";

                $information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where id='$edit'"));

                $food_price=mysqli_fetch_assoc(mysqli_query($db,"select price from foods where auto_id='$information[food_id]' "));

                echo '<div class="'.$hide.'">';
                echo 'Quantity : <br />
                          <input type="text" name="quantity" value="'.$information["quantity"].'" style="width:800px" /> <br /><br />
                          Food price: '.$food_price['price'].'<br /><br />
                          Total : <br />
                          <input type="text" name="total" value="'.$information["total"].'" style="width:800px" /> <br /><br />
                      Special request : <br />
                      <textarea name="special_req" cols="50" rows="5">'.$information["special_req"].'</textarea>
                     <br /><br />';
                echo '<input type="submit" id="save" name="button" value=" Save " />
                  <hr class="clear" />
                  <br class="clear" /></div>';
            ?>
        </form>
        <div>

            <div style="float: left;">
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

            <div>
                <form method="get">
                    <input type="hidden" name="do" value="<?=$do?>">
                    <select name="food_id">
                        <option value="" selected>Select food</option>
                        <?php
                            $get_foods = mysqli_query($db,"SELECT auto_id,title FROM foods WHERE active=1 and lang_id='$main_lang'");

                            while($row_foods=mysqli_fetch_assoc($get_foods))
                            {
                                $selected = ($row_foods['auto_id']==$food_id) ? 'selected' : '';
                                ?>
                                <option <?=$selected?> value="<?=$row_foods['auto_id']?>"><?=$row_foods['title']?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <input type="text" name="quantity_from" placeholder="quantity from" value="<?=$quantity_from?>"> -
                    <input type="text" name="quantity_to" placeholder="quantity to" value="<?=$quantity_to?>">
                    <input type="text" name="total_from" placeholder="total from" value="<?=$total_from?>"> -
                    <input type="text" name="total_to" placeholder="total to" value="<?=$total_to?>">
                    <input type="text" name="datetimes" autocomplete="off" placeholder="date range" style="width: 250px;" value="<?=$datetimes?>" />

                    <script>
                        $(function() {
                            $('input[name="datetimes"]').daterangepicker({
                                timePicker: true,
                                // startDate: moment().startOf('hour'),
                                // endDate: moment().startOf('hour').add(32, 'hour'),
                                locale: {
                                    format: 'M/DD/YYYY hh:mm A'
                                },
                                autoUpdateInput: false
                            });

                            $('input[name="datetimes"]').on('apply.daterangepicker', function(ev, picker) {
                                $(this).val(picker.startDate.format('M/DD/YYYY hh:mm A') + ' - ' + picker.endDate.format('M/DD/YYYY hh:mm A'));
                            });

                            $('input[name="datetimes"]').on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                            });
                        });
                    </script>
                    <button class="alert_success" type="submit">Search</button>
                </form>
            </div>
        </div>

        <br class="clear" />
        <?php
        echo '<table class="data" width="100%" cellpadding="0" cellspacing="0" style="margin: 15px 0;"><thead><tr>
                <th style="width:10%"><input type="checkbox" data-val="0" name="all_check" id="hamisini_sec" value="all_check" /> №</th>
                <th style="width:15%">Food</th>
                <th style="width:15%">Price</th>
                <th style="width:10%">Quantity</th>
                <th style="width:25%">Special request</th>
                <th style="width:10%">Total</th>
                <th style="width:20%">Date (Y-m-d)</th>
                <th style="width:10%">Editing</th>
</tr></thead><tbody>';
        $query=str_replace("select id ","select * ",$query_count);
        $query.=" order by auto_id desc limit $start,$limit";
        $sql=mysqli_query($db,"select * from $do where 1=1 ".$add_information_sql." order by created_at desc limit $start,$limit");
        $i = $start+1;
        while($row=mysqli_fetch_assoc($sql))
        {
            $food = mysqli_fetch_assoc(mysqli_query($db,"select `title`,`price` from foods where auto_id='$row[food_id]'"));
            echo '<tr>
                    <td><input type="checkbox" id="chbx_'.$row["id"].'" value="'.$row["id"].'" onclick="chbx_(this.id)" /> '.$i.'</td>
					<td>'.$food['title'].'</td>
					<td>'.$food['price'].'</td>
					<td>'.$row["quantity"].'</td>
					<td>'.$row["special_req"].'</td>
					<td>'.$row['total'].'</td>
					<td>'.date("Y-m-d H:i",$row["created_at"]).'</td>
					<td>
						<a href="index.php?do='.$do.'&page='.$page.'&edit='.$row["id"].'"><img src="images/icon_edit.png" alt="" title="Edit" /></a>
						<a href="index.php?do='.$do.'&page='.$page.'&delete='.$row["id"].'" class="delete"><img src="images/icon_delete.png" alt="" title="Sil" /></a>
					';
            echo '</td>
				</tr>';

            $i++;
        }
        echo '</tbody></table>';
        ?>
        <div class="ps_"><?=page_nav()?></div>
        <?php
            $get_param = '';
            $get_param .= ($food_id>0) ? '&food_id='.$food_id : '';
            $get_param .= (strlen($total_from)>0) ? '&total_from='.$total_from : '';
            $get_param .= (strlen($total_to)>0) ? '&total_to='.$total_to : '';
            $get_param .= ($quantity_from>0) ? '&quantity_from='.$quantity_from : '';
            $get_param .= ($quantity_to>0) ? '&quantity_to='.$quantity_to : '';
            $get_param .= (strlen($datetimes)>0) ? '&datetimes='.$datetimes : '';

            // Paginator
            echo '<div class="pagination">';
            $show=3;
            if($page>$show+1) echo '<a href="index.php?do='.$do.'&page=1'.$get_param.'">First page</a>';
            if($page>1) echo '<a href="index.php?do='.$do.'&page='.($page-1).$get_param.'">Previous page</a>';
            for($i=$page-$show;$i<=$page+$show;$i++)
            {
                if($i==$page) $class='class="active"'; else $class='';;
                if($i>0 && $i<=$max_page) echo '<a href="index.php?do='.$do.'&page='.$i.$get_param.'" '.$class.'>'.$i.'</a>';
            }
            if($page<$max_page) echo '<a href="index.php?do='.$do.'&page='.($page+1).$get_param.'">Next page</a>';
            if($page<$max_page-$show && $max_page>1) echo '<a href="index.php?do='.$do.'&page='.$max_page.$get_param.'"> Last page </a>';
            echo '</div>';
            // Paginator
        ?>
        <br class="clear" />
        <!-- Content end-->
    </div>
</div>

<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
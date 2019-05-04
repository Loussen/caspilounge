<?php
// Get params for filters
$email = $_GET['email'];
$firstname = $_GET['firstname'];
$lastname = $_GET['lastname'];
$phone = $_GET['phone'];
$datetimes = $_GET['datetimes'];

// Paginator
$limit=intval($_GET["limit"]);
if($limit!=15 && $limit!=25 && $limit!=50 && $limit!=100 && $limit!=999999) $limit=15;
$query_count="select id from $do where 1=1";

// Filters
$add_information_sql = "";
if(strlen($email)>0)
{
    $query_count.=" and email LIKE '%$email%' ";
    $add_information_sql .= " and email LIKE '%$email%' ";
}
if(strlen($firstname)>0)
{
    $query_count.=" and firstname LIKE '%$firstname%' ";
    $add_information_sql .= " and firstname LIKE '%$firstname%' ";
}
if(strlen($lastname)>0)
{
    $query_count.=" and lastname LIKE '%$lastname%' ";
    $add_information_sql .= " and lastname LIKE '%$lastname%' ";
}
if(strlen($phone)>0)
{
    $query_count.=" and phone LIKE '%$phone%' ";
    $add_information_sql .= " and phone LIKE '%$phone%' ";
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
        mysqli_query($db,"update $do set firstname='$firstname',lastname='$lastname',phone='$phone',email='$email',city='$city',street='$street',apartment='$apartment',floor='$floor',no='$no',updated_at='$time' where 1=1 $add_where");
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
        <span>Customers</span>
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
                echo '<div class="'.$hide.'">';
                echo 'Firstname : <br />
                      <input type="text" name="firstname" value="'.$information["firstname"].'" style="width:800px" /> <br /><br />
                      Lastname : <br />
                      <input type="text" name="lastname" value="'.$information["lastname"].'" style="width:800px" /><br /><br />
                      Phone : <br />
                      <input type="text" name="phone" value="'.$information["phone"].'" style="width:800px" /><br /><br />
                      Email : <br />
                      <input type="text" name="email" value="'.$information["email"].'" style="width:800px" /><br /><br />
                      City : <br />
                      <input type="text" name="city" value="'.$information["city"].'" style="width:800px" /><br /><br />
                      Street : <br />
                      <input type="text" name="street" value="'.$information["street"].'" style="width:800px" /><br /><br />
                      Apartment : <br />
                      <input type="text" name="apartment" value="'.$information["apartment"].'" style="width:800px" /><br /><br />
                      Floor : <br />
                      <input type="text" name="floor" value="'.$information["floor"].'" style="width:800px" /><br /><br />
                      NO : <br />
                      <input type="text" name="no" value="'.$information["no"].'" style="width:800px" />
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
                    <input type="text" name="email" placeholder="email" value="<?=$email?>">
                    <input type="text" name="firstname" placeholder="firstname" value="<?=$firstname?>">
                    <input type="text" name="lastname" placeholder="lastname" value="<?=$lastname?>">
                    <input type="text" name="phone" placeholder="phone" value="<?=$phone?>">
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
                <th style="width:15%">Name, Surname</th>
                <th style="width:10%">Phone</th>
                <th style="width:10%">Email</th>
                <th style="width:25%">Address</th>
                <th style="width:20%">Date (Y-m-d)</th>
                <th style="width:10%">Editing</th>
</tr></thead><tbody>';
        $query=str_replace("select id ","select * ",$query_count);
        $query.=" order by auto_id desc limit $start,$limit";
        $sql=mysqli_query($db,"select * from $do where 1=1 ".$add_information_sql." order by created_at desc limit $start,$limit");
        $i = $start+1;
        while($row=mysqli_fetch_assoc($sql))
        {
            echo '<tr>
                    <td><input type="checkbox" id="chbx_'.$row["id"].'" value="'.$row["id"].'" onclick="chbx_(this.id)" /> '.$i.'</td>
					<td>'.$row["firstname"].' '.$row['lastname'].'</td>
					<td>'.$row["phone"].'</td>
					<td>'.$row["email"].'</td>
					<td>'.$row["city"].', '.$row['street'].', '.$row['apartment'].', '.$row['floor'].', '.$row['no'].'</td>
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
            $get_param .= (strlen($email)>0) ? '&email='.$email : '';
            $get_param .= (strlen($firstname)>0) ? '&firstname='.$firstname : '';
            $get_param .= (strlen($lastname)>0) ? '&lastname='.$lastname : '';
            $get_param .= (strlen($phone)>0) ? '&phone='.$phone : '';
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
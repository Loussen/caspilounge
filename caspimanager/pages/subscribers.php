<?php
$datetimes = $_GET['datetimes'];

// Paginator
$limit=intval($_GET["limit"]);
if($limit!=15 && $limit!=25 && $limit!=50 && $limit!=100 && $limit!=999999) $limit=15;
$query_count="select id from $do where 1=1 ";

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

if($edit>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where id='$edit' "))==0)
{
    header("Location: index.php?do=$do");
    exit;
}

if($_POST) // Add && edit
{
    extract($_POST);
    $active=1;

    if($edit>0)
    {
        $info_edit=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where id='$edit' "));
        $add_where="and id='$edit' ";
        $active=$info_edit["active"];
    }

    $time = time();

    if(mysqli_num_rows(mysqli_query($db,"select id from $do where 1=1 $add_where"))>0 && $edit>0)
    {
        mysqli_query($db,"update $do set email='$email',updated_at='$time' where 1=1 $add_where");
    }
    else
    {
        mysqli_query($db,"insert into $do values (0,'$email', '$active', '$time',0) ");
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
<div class="onecolumn">
    <div class="header">
        <span>Subscribers</span>
    </div>
    <br class="clear"/>
    <div class="content">
        <?php
        if($ok!="") echo '<div class="alert_success"><p><img src="images/icon_accept.png" alt="success" class="mid_align"/>'.$ok.'</p></div>';
        if($error!="") echo '<div class="alert_error"><p><img src="images/icon_error.png" alt="delete" class="mid_align"/>'.$error.'</p></div>';
        ?>

        <!-- Content start-->
        <form action="index.php?do=<?php echo $do; ?>&page=<?php echo $page; ?><?php if($edit>0) echo '&edit='.$edit; ?>" method="post" id="form_login" name="form_login" enctype="multipart/form-data">
            <a href="index.php?do=<?php echo $do; ?>&add=1" style="margin-right:50px"><img src="images/icon_add.png" alt="" /> <b style="">Create new</b></a>
            <hr class="clear" />
            <?php
            if($add==1 || $edit>0) $hide=""; else $hide="hide";

            $information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where id='$edit' "));
            $sql=mysqli_query($db,"select * from diller where aktivlik=1 order by sira");

            if($add==1 || $edit>0) $hide=""; else $hide="hide";
            $information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where id='$edit' "));

            echo '<div class="'.$hide.'">';

            echo 'Email : <br />
                  <input type="email" required name="email" value="'.$information["email"].'" style="width:250px" />
                  <br /><br />
                  ';
            echo '<input type="submit" name="button" value=" Save " />
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

            <div style="float: left; margin-left: 50px;">
                <form method="get">
                    <input type="hidden" name="do" value="<?=$do?>">
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

                    <a class="btn btn-default" style="margin-left: 20px;" href="emails.php?datetimes=<?=$datetimes?>" onClick="window.open(this.href,'Emails','resizable,height=300,width=500'); return false;" >Copy emails</a>

                </form>
            </div>
        </div>

        <br class="clear" />
        <?php
        echo '<table class="data" width="100%" cellpadding="0" cellspacing="0" style="margin: 15px 0;"><thead><tr>
                <th style="width:10%"><input type="checkbox" data-val="0" name="all_check" id="hamisini_sec" value="all_check" /> №</th>
                <th style="width:40%">Email</th>
                <th style="width:40%">Date</th>
                <th style="width:30%">Editing</th>
</tr></thead><tbody>';
        $query=str_replace("select id ","select * ",$query_count);
        $query.=" order by id desc limit $start,$limit";
        $sql=mysqli_query($db,"select * from $do where 1=1 ".$add_information_sql." order by id desc limit $start,$limit");
        $i = $start+1;
        while($row=mysqli_fetch_assoc($sql))
        {
            $subs_date = ($row['created_at']>0) ? date('Y-m-d H:i', $row['created_at']) : '-';
            echo '<tr>
                    <td><input type="checkbox" id="chbx_'.$row["id"].'" value="'.$row["id"].'" onclick="chbx_(this.id)" /> '.$i.'</td>
					<td>'.stripslashes($row["email"]).'</td>
					<td>'.$subs_date.'</td>
					<td>
						<a href="index.php?do='.$do.'&page='.$page.'&edit='.$row["id"].'"><img src="images/icon_edit.png" alt="" title="Edit" /></a>
						<a href="index.php?do='.$do.'&page='.$page.'&delete='.$row["id"].'" class="delete"><img src="images/icon_delete.png" alt="" title="Sil" /></a>';
            if($row["active"]==1) $title='Active'; else $title='Deactive';
            echo '<img src="images/'.$row["active"].'_lamp.png" title="'.$title.'" border="0" align="absmiddle" style="cursor:pointer" id="info_'.$row["id"].'" onclick="aktivlik(\''.$do.'\',this.id,this.title)"  />';
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

<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
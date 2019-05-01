<style>
    table.info_table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    table.info_table td, table.info_table th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;
    }

    table.info_table tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
<?php
// Paginator
$limit=intval($_GET["limit"]);
if($limit!=15 && $limit!=25 && $limit!=50 && $limit!=100 && $limit!=999999) $limit=15;
$query_count="select id from $do ";
$count_rows=mysqli_num_rows(mysqli_query($db,$query_count));
$max_page=ceil($count_rows/$limit);
$page=intval($_GET["page"]); if($page<1) $page=1; if($page>$max_page) $page=$max_page; if($page<1) $page=1;
$start=$page*$limit-$limit;
//

$add=intval($_GET["add"]);
$edit=intval($_GET["edit"]);
$view=intval($_GET["view"]);
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
    $active=1;

    $image_tmp = $_FILES["image_file"]["tmp_name"];

    if($edit>0)
    {
        $info_edit=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where id='$edit' "));
        $add_where="and id='$edit' ";
        $id=$edit;
        $last_order=$info_edit["order_number"];
        $active=$info_edit["active"];
    }
    else $add_where="";

    $sql=mysqli_query($db,"select * from diller where aktivlik=1 order by sira");
    while($row=mysqli_fetch_assoc($sql))
    {
        $title="title_".$row["id"]; $title=mysqli_real_escape_string($db,htmlspecialchars($$title));
        $short_text="short_text_".$row["id"]; $short_text=mysqli_real_escape_string($db,htmlspecialchars($$short_text));
        $text="text_".$row["id"]; $text=mysqli_real_escape_string($db,htmlspecialchars($$text));

        $time = time();

        if(mysqli_num_rows(mysqli_query($db,"select id from $do where lang_id='$row[id]' $add_where"))>0 && $edit>0)
        {
            mysqli_query($db,"update $do set title='$title',short_text='$short_text',text='$text',updated_at='$time' where lang_id='$row[id]' $add_where");
        }
        else
        {
            mysqli_query($db,"insert into $do values (0,'$title','','$short_text','$text','$last_order','$active', '$row[id]', '$auto_id', '$time',0) ");
        }
    }

    $ok="Data has been successfully saved.";
    $edit=0;
}


if($delete>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where id='$delete' "))>0)
{
    $data = mysqli_fetch_assoc(mysqli_query($db,"select * from $do where id='$delete' "));
    @unlink('../images/pevents/'.$data["image_name"]);

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
        <span>Orders</span>
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
                if($view>0) $hide=""; else $hide="hide";

                $information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where id='$view'"));

                // Get customer
                $customer_sql = mysqli_query($db,"select * from customers where id='$information[customer_id]'");

                if(mysqli_num_rows($customer_sql)>0)
                {
                    $customers = mysqli_fetch_assoc($customer_sql);
                    $customer_found = true;
                }
                else
                {
                    $customer_found = false;
                }

                echo '<div class="'.$hide.'">';
                echo '<div style="border: 1px solid #ddd; padding: 5px;"><h2 align="center">Customer info</h2>
                    <table class="info_table">
                        <tr>
                            <th>Name, Surname</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>City</th>
                            <th>Street</th>
                            <th>Apartment</th>
                            <th>Floor</th>
                            <th>NO</th>
                        </tr>
                        <tr>
                            <td>'.$customers['firstname'].' '.$customers['lastname'].'</td>
                            <td>'.$customers['phone'].'</td>
                            <td>'.$customers['email'].'</td>
                            <td>'.$customers['city'].'</td>
                            <td>'.$customers['street'].'</td>
                            <td>'.$customers['apartment'].'</td>
                            <td>'.$customers['floor'].'</td>
                            <td>'.$customers['no'].'</td>
                        </tr>
                    </table></div><br />';

            $pay_type = ($information['pay_type']==1) ? 'Cash' : 'Card';
            $payment_date = ($information['payment_date']>0) ? date('Y-m-d H:i', $information['payment_date']) : '-';
            $paid = ($information['paid']==1) ? 'Paid' : 'Unpaid';
            $created_at = ($information['created_at']>0) ? date('Y-m-d H:i', $information['created_at']) : '-';

            if($row['status']==0)
            {
                $status = 'Deactive';
            }
            elseif($row['status']==1)
            {
                $status = 'Active';
            }
            elseif($row['status']==2)
            {
                $status = 'Shipping';
            }
            elseif($row['status']==3)
            {
                $status = 'Delivered';
            }

            echo '<div style="border: 1px solid #ddd; padding: 5px;"><h2 align="center">Order info</h2>
                    <table class="info_table">
                        <tr>
                            <th>Pay type</th>
                            <th>Payment date</th>
                            <th>Status</th>
                            <th>Pay status</th>
                            <th>Special request</th>
                            <th>Order date</th>
                        </tr>
                        <tr>
                            <td>'.$pay_type.'</td>
                            <td>'.$payment_date.'</td>
                            <td>'.$status.'</td>
                            <td>'.$paid.'</td>
                            <td>'.$information['special_req'].'</td>
                            <td>'.$created_at.'</td>
                        </tr>
                    </table></div><br />';

            $active_status = 1;

            $stmt_select = mysqli_prepare($db,"SELECT 
                                                `cart`.`id` as `cart_id`,
                                                `foods`.`auto_id` as `food_id`,
                                                `cart`.`quantity` as `quantity`,
                                                `cart`.`special_req` as `special_req`,
                                                `cart`.`total` as `total`,
                                                `foods`.`title` as `title`,
                                                `foods`.`text` as `text`,
                                                `foods`.`price` as `price`
                                                FROM `cart`
                                                INNER JOIN `foods` on `foods`.`auto_id`=`cart`.`food_id`
                                                 WHERE `cart`.`order_id`=(?) and `foods`.`active`=(?) and `foods`.`lang_id`=(?)
                                                 ORDER BY `cart`.`id` DESC 
                                                 ");
            $stmt_select->bind_param('iii', $information['id'],$active_status,$main_lang);
            $stmt_select->execute();
            $result = $stmt_select->get_result();
            $stmt_select->close();

            if($result->num_rows>0)
            {
                echo '<div style="border: 1px solid #ddd; padding: 5px;"><h2 align="center">Orders (count: '.$result->num_rows.')</h2>
                    <table class="info_table">
                        <tr>
                            <th>Cart ID</th>
                            <th>Food ID</th>
                            <th>Quantity</th>
                            <th>Special request</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>';

                $total = 0;
                while($row=$result->fetch_assoc())
                {
                    echo '<tr>
                            <td>'.$row['cart_id'].'</td>
                            <td>'.$row['food_id'].'</td>
                            <td>'.$row['quantity'].'</td>
                            <td>'.$row['special_req'].'</td>
                            <td>'.$row['title'].'</td>
                            <td>'.$row['price'].' USD</td>
                            <td>'.$row['total'].' USD</td>
                        </tr>';

                    $total+=$row['total'];
                }
                echo '<tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="background-color: green; color: #fff;">'.$total.' USD</td>
                        </tr>';

                echo '</table></div><br />';
                exit;
            }
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
        echo '<table class="data" width="100%" cellpadding="0" cellspacing="0" style="margin: 15px 0; text-align: center;"><thead><tr>
                <th style="width:5%"><input type="checkbox" data-val="0" name="all_check" id="hamisini_sec" value="all_check" /> №</th>
                <th style="width:7%">Pay type</th>
                <th style="width:20%">Payment date (Y-m-d)</th>
                <th style="width:7%">Paid</th>
                <th style="width:20%">Total order</th>
                <th style="width:20%">Order date (Y-m-d)</th>
                <th style="width:10%">Status</th>
                <th style="width:30%">Editing</th>
</tr></thead><tbody>';
        $query=str_replace("select id ","select * ",$query_count);
        $query.=" order by auto_id desc limit $start,$limit";
        $sql=mysqli_query($db,"select * from $do order by id desc limit $start,$limit");
        $i = $start+1;

        while($row=mysqli_fetch_assoc($sql))
        {
            $pay_type = ($row['pay_type']==1) ? 'Cash' : 'Card';
            $payment_date = ($row['payment_date']>0) ? date('Y-m-d H:i', $row['payment_date']) : '-';
            $paid = ($row['paid']==1) ? 'Paid' : 'Unpaid';
            $created_at = ($row['created_at']>0) ? date('Y-m-d H:i', $row['created_at']) : '-';
            if($row['status']==0)
            {
                $status = 'Deactive';
            }
            elseif($row['status']==1)
            {
                $status = 'Active';
            }
            elseif($row['status']==2)
            {
                $status = 'Shipping';
            }
            elseif($row['status']==3)
            {
                $status = 'Delivered';
            }

            echo '<tr>
                    <td><input type="checkbox" id="chbx_'.$row["auto_id"].'" value="'.$row["auto_id"].'" onclick="chbx_(this.id)" /> '.$i.'</td>
					<td>'.$pay_type.'</td>
					<td>'.$payment_date.'</td>
					<td>'.$paid.'</td>
					<td>'.$row["special_req"].'</td>
					<td>'.$created_at.'</td>
					<td>'.$status.'</td>
					<td>
                        <a target="_blank" href="index.php?do='.$do.'&page='.$page.'&view='.$row["id"].'"><img src="images/icon_eye.png" alt="" title="View" /></a>
						<a href="index.php?do='.$do.'&page='.$page.'&edit='.$row["id"].'"><img src="images/icon_edit.png" alt="" title="Edit" /></a>
						<a href="index.php?do='.$do.'&page='.$page.'&delete='.$row["id"].'" class="delete"><img src="images/icon_delete.png" alt="" title="Sil" /></a>';
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

<style>
    table.data tr th, table.data tr th
    {
        text-align: center;
    }
</style>
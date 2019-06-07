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

    table.data tbody tr td
    {
        color: #fff;
    }
</style>
<?php
// Paginator
$limit=intval($_GET["limit"]);
if($limit!=15 && $limit!=25 && $limit!=50 && $limit!=100 && $limit!=999999) $limit=15;
$query_count="select sum(cart.total) as total, orders.id from $do left join cart on cart.order_id=orders.id where 1=1";

// Get params for filters
$add_information_sql = $add_information_sql_cart = "";
if(isset($_GET['pay_type_get']) && !empty($_GET['pay_type_get']))
{
    $pay_type_get = intval($_GET['pay_type_get']);

    $query_count.=" and orders.pay_type='$pay_type_get' ";
    $add_information_sql .= " and orders.pay_type='$pay_type_get' ";
}
if(isset($_GET['paid_type']) && !empty($_GET['paid_type']) && intval($_GET['paid_type'])>0)
{
    $paid_type = intval($_GET['paid_type']);

    $paid_type_sql = $paid_type;
    if($paid_type==1000)
    {
        $paid_type_sql = 0;
    }

    $query_count.=" and orders.paid='$paid_type_sql' ";
    $add_information_sql .= " and orders.paid='$paid_type_sql' ";
}
if(isset($_GET['total_from']) && !empty($_GET['total_from']))
{
    $total_from = $_GET['total_from'];

    $query_count.=" and cart.total>='$total_from' ";
    $add_information_sql .= " and cart.total>='$total_from' ";
}
if(isset($_GET['total_to']) && !empty($_GET['total_to']))
{
    $total_to = $_GET['total_to'];

    $query_count.=" and cart.total<='$total_to' ";
    $add_information_sql .= " and cart.total<='$total_to' ";
}
if(isset($_GET['status_type']) && !empty($_GET['status_type']) && intval($_GET['status_type'])>0)
{
    $status_type = intval($_GET['status_type']);

    $status_sql = $status_type;
    if($status_type==1000)
    {
        $status_sql = 0;
    }

    $query_count.=" and orders.status='$status_sql' ";
    $add_information_sql .= " and orders.status='$status_sql' ";
}
if(isset($_GET['datetimes']) && !empty($_GET['datetimes']))
{
    $datetimes = $_GET['datetimes'];

    $datetime = explode("-",$datetimes);

    $fromDate = strtotime($datetime[0]);
    $toDate = strtotime($datetime[1]);

    $query_count.=" and orders.payment_date>='$fromDate' and orders.payment_date<='$toDate' ";
    $add_information_sql .= " and orders.payment_date>='$fromDate' and orders.payment_date<='$toDate' ";
}
if(isset($_GET['datetimes2']) && !empty($_GET['datetimes2']))
{
    $datetimes2 = $_GET['datetimes2'];

    $datetime = explode("-",$datetimes2);

    $fromDate = strtotime($datetime[0]);
    $toDate = strtotime($datetime[1]);

    $query_count.=" and orders.created_at>='$fromDate' and orders.created_at<='$toDate' ";
    $add_information_sql .= " and orders.created_at>='$fromDate' and orders.created_at<='$toDate' ";
}
if(isset($_GET['customer_id']) && !empty($_GET['customer_id']))
{
    $customer_id = intval($_GET['customer_id']);

    $query_count.=" and orders.customer_id='$customer_id' ";
    $add_information_sql .= " and orders.customer_id='$customer_id' ";
}

if(isset($_GET['order_id']) && !empty($_GET['order_id']))
{
    $order_id = $_GET['order_id'];

    $query_count.=" and orders.id='$order_id' ";
    $add_information_sql .= " and orders.id='$order_id' ";
}

// New orders
if(isset($_GET['new_order']) && !empty($_GET['new_order']) && intval($_GET['new_order'])==1)
{
    $date_new_order = strtotime(date("Y-m-d H:i",strtotime('-10 minutes')));
    $query_count.=" and  status=1 and read_admin=0 ";
    $add_information_sql .= " and  status=1 and read_admin=0 ";
}

$query_count.=' group by cart.order_id';

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

if($edit>0)
{
    mysqli_query($db,"update $do set read_admin=1 where id='$edit'");
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

        if(mysqli_num_rows(mysqli_query($db,"select id from $do where 1=1 $add_where"))>0 && $edit>0)
        {
            $payment_date = mktime($hour,$minute,00,$month,$day,$year);
            mysqli_query($db,"update $do set payment_date='$payment_date',status='$status',paid='$paid',special_req='$special_req',updated_at='$time' where 1=1 $add_where");
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

            <span style="padding: 5px 25px; width: 10%; background-color: #659BF5; margin-right: 10px; color:#fff; float: right
;">Deactive</span>
            <span style="padding: 5px 25px; width: 10%; background-color: #008000; margin-right: 10px; color:#fff; float: right
;">Active</span>
            <span style="padding: 5px 25px; width: 10%; background-color: #F1AE55; margin-right: 10px; color:#fff; float: right
;">Shipping</span>
            <span style="padding: 5px 25px; width: 10%; background-color: #5AC57D; margin-right: 10px; color:#fff; float: right
;">Delivered</span>
            <span style="float: right; margin-right: 10px; margin-top: 5px;">Color's info : </span>
            <button type="button" onclick="window.location.href='index.php?do=<?=$do?>&new_order=1'" class="shake-horizontal new_order">New order (<span class="count"><?=$count_rows?></span>)</button>
            <hr class="clear" />

            <?php
                if($add==1 || $edit>0) $hide=""; else $hide="hide";
                if($view>0)
                {
                    $hide_view="";

                    mysqli_query($db,"update $do set read_admin=1 where id='$view'");
                }
                else $hide_view="hide";

                $information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where id='$edit'"));

                if($information['payment_date']>0)
                {
                    $year = date("Y",$information['payment_date']);
                    $month = date("m",$information['payment_date']);
                    $day = date("d",$information['payment_date']);
                    $hour = date("H",$information['payment_date']);
                    $minute = date("i",$information['payment_date']);
                }
                else
                {
                    $year = $month = $day = $hour = $minute = 0;
                }

                echo '<div class="'.$hide.'">';
                echo 'Payment date (Y-m-d H:i) : <br />';
                ?>
                <select name="year">
                    <?php
                        for ($i=2019;$i<=2030;$i++)
                        {
                            $selected = ($year==$i) ? 'selected' : '';
                            ?>
                            <option <?=$selected?> value="<?=$i?>"><?=$i?></option>
                            <?php
                        }
                    ?>
                </select> -
                <select name="month">
                    <?php
                    for ($i=1;$i<=12;$i++)
                    {
                        $delimeter = ($i<10) ? '0' : '';
                        $selected = ($month==$i) ? 'selected' : '';
                        ?>
                        <option <?=$selected?> value="<?=$i?>"><?=$delimeter.$i?></option>
                        <?php
                    }
                    ?>
                </select> -
                <select name="day">
                    <?php
                    for ($i=1;$i<=31;$i++)
                    {
                        $delimeter = ($i<10) ? '0' : '';
                        $selected = ($day==$i) ? 'selected' : '';
                        ?>
                        <option <?=$selected?> value="<?=$i?>"><?=$delimeter.$i?></option>
                        <?php
                    }
                    ?>
                </select>&nbsp;&nbsp;
                <select name="hour">
                    <?php
                    for ($i=0;$i<=24;$i++)
                    {
                        $delimeter = ($i<10) ? '0' : '';
                        $selected = ($hour==$i) ? 'selected' : '';
                        ?>
                        <option <?=$selected?> value="<?=$i?>"><?=$delimeter.$i?></option>
                        <?php
                    }
                    ?>
                </select>
                 :
                <select name="minute">
                    <?php
                    for ($i=0;$i<=24;$i++)
                    {
                        $delimeter = ($i<10) ? '0' : '';
                        $selected = ($minute==$i) ? 'selected' : '';
                        ?>
                        <option <?=$selected?> value="<?=$i?>"><?=$delimeter.$i?></option>
                        <?php
                    }
                    ?>
                </select>
                <br /><br />
                Status <br />
                <select name="status">
                    <?php
                    $status_arr = [1000=>'Deactive',1=>'Active',2=>'Shipping',3=>'Delivered'];
                    foreach ($status_arr as $key=>$value)
                    {
                        $selected = ($key==$information['status']) ? 'selected' : '';
                        ?>
                        <option <?=$selected?> value="<?=$key?>"><?=$value?></option>
                        <?php
                    }
                    ?>
                </select><br /><br />
                Paid <br />
                <select name="paid">
                    <?php
                    $paid_arr = [0=>'No',1=>'Yes'];
                    foreach ($paid_arr as $key=>$value)
                    {
                        $selected = ($key==$information['paid']) ? 'selected' : '';
                        ?>
                        <option <?=$selected?> value="<?=$key?>"><?=$value?></option>
                        <?php
                    }
                    ?>
                </select><br /><br />
                Special request : <br />
                <textarea name="special_req" cols="50" rows="5"><?=$information["special_req"]?></textarea><br /><br />
                <?php
                         echo '<br /><br />';
                echo '<input type="submit" id="save" name="button" value=" Save " />
                      <hr class="clear" />
                      <br class="clear" /></div>';

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

                echo '<div class="'.$hide_view.'">';
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
                            <th>Editing</th>
                        </tr>
                        <tr>
                            <td>'.$pay_type.'</td>
                            <td>'.$payment_date.'</td>
                            <td>'.$status.'</td>
                            <td>'.$paid.'</td>
                            <td>'.$information['special_req'].'</td>
                            <td>'.$created_at.'</td>
                            <td><a target="_blank" href="index.php?do='.$do.'&page='.$page.'&edit='.$information["id"].'"><img src="images/icon_edit.png" alt="" title="Edit" /></a>
						<a target="_blank" href="index.php?do='.$do.'&page='.$page.'&delete='.$information["id"].'" class="delete"><img src="images/icon_delete.png" alt="" title="Sil" /></a></td>
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
                            <td><a target="_blank" href="index.php?do=cart&edit='.$row['cart_id'].'">'.$row['cart_id'].'</a></td>
                            <td><a target="_blank" href="index.php?do=foods&edit='.$row['food_id'].'">'.$row['food_id'].'</a></td>
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

<!--            <div style="float: left;">-->
<!--                <a href="javascript:void(0);" class="chbx_del"><img src="images/icon_delete.png" alt="" title="" /></a>-->
<!--                <a href="javascript:void(0);" class="chbx_active" data-val="1"><img src="images/1_lamp.png" alt="" title="" /></a>-->
<!--                <a href="javascript:void(0);" class="chbx_active" data-val="2"><img src="images/0_lamp.png" alt="" title="" /></a>-->
<!--                <input type="hidden" value="index.php?do=--><?//=$do?><!--&page=--><?//=$page?><!--&limit=--><?//=$limit?><!--&forId=2" id="current_link" />-->
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

            <div>
                <form method="get">
                    <input type="hidden" name="do" value="<?=$do?>">
                    <input type="text" style="width: 100px;" name="order_id" placeholder="order number" value="<?=$order_id?>">
                    <select name="pay_type_get">
                        <option value="" selected>Select pay type</option>
                        <?php
                        $pay_type_arr = [1=>'Cash',2=>'Card'];

                        foreach($pay_type_arr as $key=>$value)
                        {
                            $selected = ($key==$pay_type_get) ? 'selected' : '';
                            ?>
                            <option <?=$selected?> value="<?=$key?>"><?=$value?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <input type="text" name="datetimes" autocomplete="off" placeholder="payment date range" style="width: 250px;" value="<?=$datetimes?>" />
                    <select name="paid_type">
                        <option value selected>Select paid type</option>
                        <?php
                        $paid_type_arr = [1000=>'Unpaid',1=>'Paid'];

                        foreach($paid_type_arr as $key=>$value)
                        {
                            $selected = ($key===$paid_type) ? 'selected' : '';
                            ?>
                            <option <?=$selected?> value="<?=$key?>"><?=$value?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <input type="text" style="width: 100px;" name="total_from" placeholder="total from" value="<?=$total_from?>"> -
                    <input type="text" style="width: 100px;" name="total_to" placeholder="total to" value="<?=$total_to?>">
                    <input type="text" name="datetimes2" autocomplete="off" placeholder="order date range" style="width: 250px;" value="<?=$datetimes2?>" />
                    <select name="status_type">
                        <option value="" selected>Select status</option>
                        <?php
                        $status_type_arr = [1000=>'Deactive',1=>'Active',2=>'Shipping',3=>'Delivered'];

                        foreach($status_type_arr as $key=>$value)
                        {
                            $selected = ($key===$status_type) ? 'selected' : '';
                            ?>
                            <option <?=$selected?> value="<?=$key?>"><?=$value?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <select name="customer_id" id="customers">
                        <option value="" selected>Select customer</option>
                        <?php
                            $sql_customers = mysqli_query($db, "SELECT customers.id,firstname,lastname,email,phone FROM customers inner join orders on orders.customer_id=customers.id order by customers.created_at desc");

                            while ($row_customer=mysqli_fetch_assoc($sql_customers))
                            {
                                $selected = ($row_customer['id']==$customer_id) ? 'selected' : '';
                                ?>
                                <option <?=$selected?> value="<?=$row_customer['id']?>"><?=$row_customer['firstname']." ".$row_customer['lastname'].", ".$row_customer['email'].", ".$row_customer['phone']?></option>
                                <?php
                            }
                        ?>
                    </select>

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

                            $('input[name="datetimes2"]').daterangepicker({
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

                            $('input[name="datetimes2"]').on('apply.daterangepicker', function(ev, picker) {
                                $(this).val(picker.startDate.format('M/DD/YYYY hh:mm A') + ' - ' + picker.endDate.format('M/DD/YYYY hh:mm A'));
                            });

                            $('input[name="datetimes2"]').on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                            });
                        });
                    </script>
                    <button class="alert_success" type="submit">Search</button>
                </form>
            </div>

<!--            <div style="margin-top: 5px;">-->
<!--                <span style="padding: 5px 25px; width: 10%; background-color: #FF4500; margin-right: 10px; color:#fff;">Deactive</span>-->
<!--                <span style="padding: 5px 25px; width: 10%; background-color: #008000; margin-right: 10px; color:#fff;">Active</span>-->
<!--                <span style="padding: 5px 25px; width: 10%; background-color: #0000FF; margin-right: 10px; color:#fff;">Shipping</span>-->
<!--                <span style="padding: 5px 25px; width: 10%; background-color: #999999; margin-right: 10px; color:#fff;">Delivered</span>-->
<!--            </div>-->
        </div>

        <br class="clear" />
        <?php
        echo '<table class="data" width="100%" cellpadding="0" cellspacing="0" style="margin: 15px 0; text-align: center;"><thead><tr>
                <th style="width:5%"><input type="checkbox" data-val="0" name="all_check" id="hamisini_sec" value="all_check" />ID</th>
                <th style="width:5%">Pay type</th>
                <th style="width:20%">Payment date (Y-m-d)</th>
                <th style="width:7%">Paid</th>
                <th style="width:12%">Total order</th>
                <th style="width:13%">Order date (Y-m-d)</th>
                <th style="width:15%">Transaction</th>
                <th style="width:10%">Status</th>
                <th style="width:30%">Editing</th>
</tr></thead><tbody>';
        $query=str_replace("select orders.id ","select * ",$query_count);
        $query.=" order by orders.id desc limit $start,$limit";
        $sql=mysqli_query($db,"select sum(cart.total) as total, orders.created_at as order_date,orders.id as id, orders.* from $do
                                      left join cart on cart.order_id=orders.id
                                      where 1=1 ".$add_information_sql." 
                                      group by cart.order_id
                                      order by orders.created_at desc,orders.payment_date desc 
                                      limit $start,$limit");

//        echo "select sum(cart.total) as total, orders.created_at as order_date,orders.id as id, orders.* from $do
//                                      left join cart on cart.order_id=orders.id
//                                      where 1=1 ".$add_information_sql."
//                                      group by cart.order_id
//                                      order by orders.payment_date desc,orders.created_at desc
//                                      limit $start,$limit";

        $i = $start+1;

        $total_order = 0;
        while($row=mysqli_fetch_assoc($sql))
        {
            $pay_type = ($row['pay_type']==1) ? 'Cash' : 'Card';
            $payment_date = ($row['payment_date']>0) ? date('Y-m-d H:i', $row['payment_date']) : '-';
            $paid = ($row['paid']==1) ? 'Paid' : 'Unpaid';
            $created_at = ($row['order_date']>0) ? date('Y-m-d H:i', $row['order_date']) : '-';
            if($row['status']==0)
            {
                $status = 'Deactive';
                $backcolor = '#659BF5';
            }
            elseif($row['status']==1)
            {
                $status = 'Active';
                $backcolor = '#008000';
            }
            elseif($row['status']==2)
            {
                $status = 'Shipping';
                $backcolor = '#F1AE55';
            }
            elseif($row['status']==3)
            {
                $status = 'Delivered';
                $backcolor = '#5AC57D';
            }

            $sql_transactions=mysqli_fetch_assoc(mysqli_query($db,"select response from transactions where order_id='$row[id]'"));

            if(strlen($sql_transactions['response'])>0)
            {
                $click_trans = '<a href="transactions.php?order_id='.$row['id'].'" onClick="window.open(this.href,\'Transaction\',\'resizable,height=400,width=800\'); return false;" >Click</a>';
            }
            else
            {
                $click_trans = " - ";
            }

            if($row['customer_id']>0)
            {
                $click_mail = '<a href="sendmail.php?user_id='.$row['customer_id'].'" onClick="window.open(this.href,\'Send Mail\',\'resizable,height=400,width=800\'); return false;" ><img src="images/mail.png" alt="" title="Mail" /></a>';
            }
            else
            {
                $click_mail = '<img src="images/mail_disable.png" alt="" title="Mail" style="cursor:no-drop;" />';
            }

//            $sql_cart=mysqli_fetch_assoc(mysqli_query($db,"select sum(`total`) as total from cart where order_id='$row[id]'"));

            echo '<tr style="background-color: '.$backcolor.'">
                    <td><input type="checkbox" id="chbx_'.$row["auto_id"].'" value="'.$row["auto_id"].'" onclick="chbx_(this.id)" /> '.$row['id'].'</td>
					<td>'.$pay_type.'</td>
					<td>'.$payment_date.'</td>
					<td>'.$paid.'</td>
					<td>'.$row['total'].'</td>
					<td>'.$created_at.'</td>
					<td>'.$click_trans.'</td>
					<td>'.$status.'</td>
					<td>
					    '.$click_mail.'
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
            $get_param = '';
            $get_param .= ($pay_type_get>0) ? '&pay_type_get='.$pay_type_get : '';
            $get_param .= ($paid_type>0) ? '&paid_type='.$paid_type : '';
            $get_param .= (strlen($total_from)>0) ? '&total_from='.$total_from : '';
            $get_param .= (strlen($total_to)>0) ? '&total_to='.$total_to : '';
            $get_param .= ($status_type>0) ? '&status_type='.$status_type : '';
            $get_param .= (strlen($datetimes)>0) ? '&datetimes='.$datetimes : '';
            $get_param .= (strlen($datetimes2)>0) ? '&datetimes2='.$datetimes2 : '';
            $get_param .= (strlen($order_id)>0) ? '&total_to='.$order_id : '';

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

<style>
    table.data tr th, table.data tr th
    {
        text-align: center;
    }

    .shake-horizontal {
        -webkit-animation: shake-horizontal 2s cubic-bezier(0.455, 0.030, 0.515, 0.955) infinite both;
        animation: shake-horizontal 2s cubic-bezier(0.455, 0.030, 0.515, 0.955) infinite both;

        background: red;
        border: 0;
        padding: 5px 30px;
        color: #fff;
        font-weight: bold;
        letter-spacing: 2px;
        border-radius: 10px;
    }
    @-webkit-keyframes shake-horizontal {
        0%,
        100% {
            -webkit-transform: translateX(0);
            transform: translateX(0);
        }
        10%,
        30%,
        50%,
        70% {
            -webkit-transform: translateX(-10px);
            transform: translateX(-10px);
        }
        20%,
        40%,
        60% {
            -webkit-transform: translateX(10px);
            transform: translateX(10px);
        }
        80% {
            -webkit-transform: translateX(8px);
            transform: translateX(8px);
        }
        90% {
            -webkit-transform: translateX(-8px);
            transform: translateX(-8px);
        }
    }
    @keyframes shake-horizontal {
        0%,
        100% {
            -webkit-transform: translateX(0);
            transform: translateX(0);
        }
        10%,
        30%,
        50%,
        70% {
            -webkit-transform: translateX(-10px);
            transform: translateX(-10px);
        }
        20%,
        40%,
        60% {
            -webkit-transform: translateX(10px);
            transform: translateX(10px);
        }
        80% {
            -webkit-transform: translateX(8px);
            transform: translateX(8px);
        }
        90% {
            -webkit-transform: translateX(-8px);
            transform: translateX(-8px);
        }
    }
</style>

<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<script type="text/javascript" src="js/selectsize.js"></script>
<link rel="stylesheet" type="text/css" href="css/selectsize.css" />

<script type="text/javascript">
    $('#customers').selectize({
        create: true,
        sortField: 'text'
    });

    $(document).ready(function(){
        setInterval(function () {
            $.post('new_order.php', {},function( data ) {
                if(data.code==1)
                {
                    $('div.new-order-alert').show();
                    $('button.new_order').show();
                    $('button.new_order span.count').html(data.count);
                }
                else
                {
                    $('div.new-order-alert').hide();
                    $('button.new_order').hide();
                }
            },"json");
        }, 10000);

        $.post('new_order.php', {},function( data ) {
            if(data.code==1)
            {
                $('div.new-order-alert').show();
                $('button.new_order').show();
                $('button.new_order span.count').html(data.count);
            }
            else
            {
                $('div.new-order-alert').hide();
                $('button.new_order').hide();
            }
        },"json");
    });
</script>
<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 2/13/2019
 * Time: 12:27 PM
 */

include "pages/includes/config.php";

$add_information_sql = '';

if(isset($_GET['datetimes']) && !empty($_GET['datetimes']) && strlen($_GET['datetimes'])>0)
{
    $datetimes = safe($_GET['datetimes']);
    $datetime = explode("-",$datetimes);

    $fromDate = strtotime($datetime[0]);
    $toDate = strtotime($datetime[1]);

    $add_information_sql .= " and created_at>='$fromDate' and created_at<='$toDate' ";
}

$sql = mysqli_query($db,"select `email` from `subscribers` where active=1 ".$add_information_sql." order by created_at desc");

if(mysqli_num_rows($sql)>0)
{
    while($row=mysqli_fetch_assoc($sql))
    {
        echo $row['email'].'<br />';
    }
}
else
{
    echo "Not found";
}
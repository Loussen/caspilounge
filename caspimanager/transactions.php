<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 2/13/2019
 * Time: 12:27 PM
 */

include "pages/includes/config.php";
include "pretty_json.php";

if(isset($_GET['order_id']) && !empty($_GET['order_id']) && intval($_GET['order_id'])>0)
{
    $order_id = intval($_GET['order_id']);

    $row = mysqli_fetch_assoc(mysqli_query($db,"select `response` from `transactions` where order_id='$order_id'"));

    if(strlen($row['response'])>0)
    {
        echo _format_json($row['response'], true);
    }
    else
    {
        echo "Not found";
    }
}
else
{
    echo "Not found";
}
<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 6/7/2019
 * Time: 1:43 PM
 */

include "pages/includes/config.php";

$response = json_encode(["code"=>0, "content" => "Error system"]);

if($_POST || 1==1)
{
    $date_new_order = strtotime(date("Y-m-d H:i",strtotime('-10 minutes')));

    $count = mysqli_num_rows(mysqli_query($db,"SELECT id FROM orders WHERE status=1 and read_admin=0"));

    if($count>0)
    {
        $response = json_encode(["code"=>1, "count" => $count]);
    }
    else
    {
        $response = json_encode(["code"=>0, "content" => "Not found"]);
    }
}

echo $response;
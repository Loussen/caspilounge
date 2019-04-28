<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 4/23/2019
 * Time: 9:44 AM
 */

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

if (isset($_SERVER['HTTP_ORIGIN']))
{
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

include "../caspimanager/pages/includes/config.php";

$response = json_encode(array("status"=>false, "type"=>"delete_cart", "err" => "Error system"));

$phpInput = file_get_contents("php://input");

if($phpInput)
{
    if(isset($_SERVER['HTTP_SECRET']) && !empty($_SERVER['HTTP_SECRET']))
    {
        $data = json_decode($phpInput, true);

        if(!intval($data['food_id'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"food_id", "err" => "Food is required"));
        }
        else
        {
            $token = safe($_SERVER['HTTP_SECRET']);
            $food_id = intval($data['food_id']);

            $status_order = 0;

            $stmt_select = mysqli_prepare($db,"SELECT `id` FROM `orders` WHERE `token`=(?) and `status`=(?) LIMIT 1");
            $stmt_select->bind_param('si', $token,$status_order);
            $stmt_select->execute();
            $stmt_select->bind_result($order_id);
            $stmt_select->store_result();
            $stmt_select->fetch();

            if($stmt_select->num_rows==1 && $order_id>0)
            {
                $stmt_select = mysqli_prepare($db,"SELECT `id` FROM `cart` WHERE `order_id`=(?) and `food_id`=(?) LIMIT 1");
                $stmt_select->bind_param('ii', $order_id,$food_id);
                $stmt_select->execute();
                $stmt_select->bind_result($cart_id);
                $stmt_select->store_result();
                $stmt_select->fetch();

                if($stmt_select->num_rows==1 && $cart_id>0)
                {
                    $stmt_delete = mysqli_prepare($db, "DELETE FROM `cart` WHERE `id`=(?)");
                    $stmt_delete->bind_param('i', $cart_id);
                    $delete = $stmt_delete->execute();

                    if($delete==1)
                    {
                        $response = json_encode(array("status"=>true, "type"=>"delete_cart", "message" => "Success"));
                    }
                    else
                    {
                        $response = json_encode(array("status"=>false, "type"=>"delete_cart", "err" => "Error cart delete"));
                    }
                }
                else
                {
                    $response = json_encode(array("status"=>false, "type"=>"delete_cart", "err" => "Not found cart"));
                }
            }
            else
            {
                $response = json_encode(array("status"=>false, "type"=>"delete_cart", "err" => "Not found order"));
            }
        }
    }
    else
    {
        $response = json_encode(array("status"=>false, "err_param"=>"token", "err" => "Token is required"));
    }
}
else
{
    $response = json_encode(array("status"=>false, "type"=>"delete_cart", "err" => "Not data"));
}

echo $response;




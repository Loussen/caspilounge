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

$response = json_encode(array("status"=>false, "type"=>"add_cart", "err" => "Error system"));

$phpInput = file_get_contents("php://input");

if($phpInput)
{
    $data = json_decode($phpInput, true);

    if(!intval($data['food_id'])>0)
    {
        $response = json_encode(array("status"=>false, "err_param"=>"food_id", "err" => "Food is required"));
    }
    elseif(!intval($data['quantity'])>0)
    {
        $response = json_encode(array("status"=>false, "err_param"=>"quantity", "err" => "Quantity is required"));
    }
    elseif(!strlen($data['token'])>0)
    {
        $response = json_encode(array("status"=>false, "err_param"=>"token", "err" => "Token is required"));
    }
    else
    {
        $token = safe($data['token']);
        $food_id = intval($data['food_id']);
        $quantity = intval($data['quantity']);
        $special_req = safe($data['special_req']);

        $stmt_select = mysqli_prepare($db,"SELECT `id` FROM `orders` WHERE `token`=(?) LIMIT 1");
        $stmt_select->bind_param('s', $token);
        $stmt_select->execute();
        $stmt_select->bind_result($order_id);
        $stmt_select->store_result();
        $stmt_select->fetch();

        if($stmt_select->num_rows!=1 || !$order_id>0)
        {
            // default params
            $customer_id = $payment_date = $status = $paid = $updated_at = 0;
            $pay_type = 1;
            $created_at = time();

            $special_req = safe($data['special_req']);

            $stmt_insert = mysqli_prepare($db, "INSERT INTO `orders` (`customer_id`,`pay_type`,`payment_date`,`status`,`paid`,`special_req`,`token`,`created_at`,`updated_at`) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt_insert->bind_param('iiiiissii', $customer_id,$pay_type,$payment_date,$status,$paid,$special_req,$token,$created_at,$updated_at);
            $insert = $stmt_insert->execute();

            if($insert==1)
            {
                $order_id = $stmt_insert->insert_id;
            }
            else
            {
                $response = json_encode(array("status"=>false, "type"=>"add_cart", "err" => "Error orders insert"));
                echo $response;
                exit;
            }
        }

        $stmt_insert = mysqli_prepare($db, "INSERT INTO `orders` (`order_id`,`food_id`,`quantity`,`special_req`,`created_at`,`updated_at`) VALUES (?,?,?,?,?,?)");
        $stmt_insert->bind_param('iiisii', $order_id,$food_id,$quantity,$special_req,$created_at,$updated_at);
        $insert = $stmt_insert->execute();
    }
}
else
{
    $response = json_encode(array("status"=>false, "type"=>"catering", "err" => "Not data"));
}

echo $response;




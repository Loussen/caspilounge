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

$main_lang = mysqli_real_escape_string($db,$_GET['main_lang']);

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
        elseif(!intval($data['quantity'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"quantity", "err" => "Quantity is required"));
        }
        else
        {
            $token = safe($_SERVER['HTTP_SECRET']);
            $food_id = intval($data['food_id']);
            $quantity = intval($data['quantity']);
            $special_req = safe($data['special_req']);

            $created_at = time();
            $updated_at = $status_order = 0;

            $stmt_select = mysqli_prepare($db,"SELECT `id` FROM `orders` WHERE `token`=(?) and `status`=(?) LIMIT 1");
            $stmt_select->bind_param('si', $token,$status_order);
            $stmt_select->execute();
            $stmt_select->bind_result($order_id);
            $stmt_select->store_result();
            $stmt_select->fetch();

            if($stmt_select->num_rows<1 || !$order_id>0)
            {
                // default params
                $customer_id = $payment_date = $status = $paid = $read_admin = 0;
                $pay_type = 1;
                $special_req_order = '';

                $stmt_insert = mysqli_prepare($db, "INSERT INTO `orders` (`customer_id`,`pay_type`,`payment_date`,`status`,`paid`,`special_req`,`read_admin`,`token`,`created_at`,`updated_at`) VALUES (?,?,?,?,?,?,?,?,?,?)");
                $stmt_insert->bind_param('iiiiisisii', $customer_id,$pay_type,$payment_date,$status,$paid,$special_req_order,$read_admin,$token,$created_at,$updated_at);
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

            $special_req_cart = safe($data['special_req']);

            $active_status = 1;

            $stmt_select = mysqli_prepare($db,
                "SELECT
                    `cart`.`id` as `cart_id`,
                    `cart`.`quantity` as `quantity`,
                    `foods`.`price` as `price`
                    FROM `cart`
                    INNER JOIN `foods` on `foods`.`auto_id`=`cart`.`food_id`
                    WHERE `cart`.`order_id`=(?) and `cart`.`food_id`=(?) and `foods`.`active`=(?) LIMIT 1");
            $stmt_select->bind_param('iii', $order_id,$food_id,$active_status);
            $stmt_select->execute();
            $stmt_select->bind_result($cart_id,$quantity_row,$price);
            $stmt_select->store_result();
            $stmt_select->fetch();

            if($stmt_select->num_rows==1 && $cart_id>0)
            {
                $quantity_new = $quantity_row+$quantity;
                $updated_at = time();
                $total = $quantity_new*$price;
                $stmt_update = mysqli_prepare($db, "UPDATE `cart` SET `quantity`=(?),`special_req`=(?),`total`=(?),`updated_at`=(?) WHERE `order_id`=(?) and `food_id`=(?)");
                $stmt_update->bind_param('isdiii', $quantity_new,$special_req,$total,$updated_at,$order_id,$food_id);
                $update = $stmt_update->execute();

                if($update==1)
                {
                    $response = json_encode(array("status"=>true, "type"=>"update_cart", "message" => "Success"));
                }
                else
                {
                    $response = json_encode(array("status"=>false, "type"=>"update_cart", "err" => "Error cart update"));
                }
            }
            else
            {
                $stmt_select = mysqli_prepare($db,
                    "SELECT
                    `foods`.`price` as `price`
                    FROM `foods`
                    WHERE `foods`.`active`=(?) and `foods`.`auto_id`=(?) LIMIT 1");
                $stmt_select->bind_param('ii', $active_status,$food_id);
                $stmt_select->execute();
                $stmt_select->bind_result($price);
                $stmt_select->store_result();
                $stmt_select->fetch();

                $total = $quantity*$price;
                $stmt_insert = mysqli_prepare($db, "INSERT INTO `cart` (`order_id`,`food_id`,`quantity`,`special_req`,`total`,`created_at`,`updated_at`) VALUES (?,?,?,?,?,?,?)");
                $stmt_insert->bind_param('iiisdii', $order_id,$food_id,$quantity,$special_req_cart,$total,$created_at,$updated_at);
                $insert = $stmt_insert->execute();

                if($insert==1)
                {
                    $response = json_encode(array("status"=>true, "type"=>"add_cart", "message" => "Success", "order_id" => $order_id));
                }
                else
                {
                    $response = json_encode(array("status"=>false, "type"=>"add_cart", "err" => "Error cart insert"));
                }
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
    $response = json_encode(array("status"=>false, "type"=>"add_cart", "err" => "Not data"));
}

echo $response;




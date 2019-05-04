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

$response = json_encode(array("status"=>false, "type"=>"confirm_order", "err" => "Error system"));

$phpInput = file_get_contents("php://input");

if($phpInput)
{
    if(isset($_SERVER['HTTP_SECRET']) && !empty($_SERVER['HTTP_SECRET']))
    {
        $data = json_decode($phpInput, true);

        if(!strlen($data['city'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"city", "err" => "City is required"));
        }
        elseif(!strlen($data['no'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"no", "err" => "No is required"));
        }
        elseif(!strlen($data['street'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"street", "err" => "Street is required"));
        }
        elseif(!strlen($data['firstname'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"firstname", "err" => "Firstname is required"));
        }
        elseif(!strlen($data['phone'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"phone", "err" => "Phone is required"));
        }
        elseif(!strlen($data['email'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"email", "err" => "Email is required"));
        }
        elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        {
            $response = json_encode(array("status"=>false, "err_param"=>"email", "err" => "Invalid email"));
        }
        elseif(!intval($data['pay_type'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"pay_type", "err" => "Pay type is required"));
        }
        else
        {
            $token = safe($_SERVER['HTTP_SECRET']);
            $city = safe($data['city']);
            $no = safe($data['no']);
            $floor = safe($data['floor']);
            $street = safe($data['street']);
            $apt = safe($data['apt']);
            $firstname = safe($data['firstname']);
            $lastname = safe($data['lastname']);
            $phone = safe($data['phone']);
            $email = safe($data['email']);
            $pay_type = intval($data['pay_type']); // 1=>Cash, 2=>card
            $special_req = safe($data['special_req']);

            $created_at = time();
            $updated_at = $status_order = 0;

            $stmt_insert = mysqli_prepare($db, "INSERT INTO `customers` (`firstname`,`lastname`,`phone`,`email`,`city`,`street`,`apartment`,`floor`,`no`,`created_at`,`updated_at`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt_insert->bind_param('sssssssssii', $firstname,$lastname,$phone,$email,$city,$street,$apt,$floor,$no,$created_at,$updated_at);
            $insert = $stmt_insert->execute();

            if($insert==1)
            {
                $customer_id = $stmt_insert->insert_id;

                $stmt_select = mysqli_prepare($db,"SELECT `id` FROM `orders` WHERE `token`=(?) and `status`=(?) LIMIT 1");
                $stmt_select->bind_param('si', $token,$status_order);
                $stmt_select->execute();
                $stmt_select->bind_result($order_id);
                $stmt_select->store_result();
                $stmt_select->fetch();

                if($stmt_select->num_rows==1 && $order_id>0)
                {
                    $updated_at = time();

                    if($pay_type==1)
                    {
                        $status_order_new = 1;
                    }
                    else
                    {
                        $status_order_new = 0;
                    }

                    $paid = 0;

                    $stmt_update = mysqli_prepare($db, "UPDATE `orders` SET `customer_id`=(?),`pay_type`=(?),`status`=(?),`paid`=(?),`special_req`=(?) WHERE `token`=(?) and `status`=(?)");
                    $stmt_update->bind_param('iiiissi', $customer_id,$pay_type,$status_order_new,$paid,$special_req,$token,$status_order);
                    $update = $stmt_update->execute();

                    if($update==1)
                    {
                        $response = json_encode(array("status"=>true, "type"=>"confirm_order", "message" => "Success", "payment_type" => $status_order_new));
                    }
                    else
                    {
                        $response = json_encode(array("status"=>false, "type"=>"confirm_order", "err" => "Error order update"));
                    }
                }
                else
                {
                    $response = json_encode(array("status"=>false, "type"=>"confirm_order", "err" => "Not found order"));
                }
            }
            else
            {
                $response = json_encode(array("status"=>false, "type"=>"confirm_order", "err" => "Error customer insert"));
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
    $response = json_encode(array("status"=>false, "type"=>"confirm_order", "err" => "Not data"));
}

echo $response;




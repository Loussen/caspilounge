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

$response = json_encode(array("status"=>false, "type"=>"get_cart", "err" => "Error system"));

$main_lang = mysqli_real_escape_string($db,$_GET['main_lang']);
$active_status = 1;

if(isset($_SERVER['HTTP_SECRET']) && !empty($_SERVER['HTTP_SECRET']))
{
    $token = safe($_SERVER['HTTP_SECRET']);

    $status_order = 0;

    $stmt_select = mysqli_prepare($db,"SELECT `id` FROM `orders` WHERE `token`=(?) and `status`=(?) LIMIT 1");
    $stmt_select->bind_param('si', $token,$status_order);
    $stmt_select->execute();
    $stmt_select->bind_result($order_id);
    $stmt_select->store_result();
    $stmt_select->fetch();

    if($stmt_select->num_rows==1 && $order_id>0)
    {
        $stmt_select = mysqli_prepare($db,"SELECT 
                                                `cart`.`id` as `cart_id`,
                                                `foods`.`auto_id` as `food_id`,
                                                `cart`.`quantity` as `quantity`,
                                                `cart`.`special_req` as `special_req`,
                                                `foods`.`title` as `title`,
                                                `foods`.`text` as `text`,
                                                `foods`.`price` as `price`,
                                                `cart`.`total` as `total`
                                                FROM `cart`
                                                INNER JOIN `foods` on `foods`.`auto_id`=`cart`.`food_id`
                                                 WHERE `cart`.`order_id`=(?) and `foods`.`active`=(?) and `foods`.`lang_id`=(?)
                                                 ORDER BY `cart`.`id` DESC 
                                                 ");
        $stmt_select->bind_param('iii', $order_id,$active_status,$main_lang);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $stmt_select->close();

        if($result->num_rows>0)
        {
            $data = [];
            $i = 0;
            while($row=$result->fetch_assoc())
            {
                $data[] = $row;

                $data[$i]['food_id'] = $row['food_id'];
                $data[$i]['title'] = $row['title'];
                $data[$i]['text'] = html_entity_decode($row['text']);
                $data[$i]['price'] = $row['price'];

                $i++;
            }

            $response = json_encode(array("status"=>true, "type"=>"get_cart", "data" => $data));
        }
        else
        {
            $response = json_encode(array("status"=>false, "type"=>"get_cart", "err" => "Not found cart"));
        }
    }
    else
    {
        $response = json_encode(array("status"=>false, "type"=>"get_cart", "err" => "Not found order"));
    }
}
else
{
    $response = json_encode(array("status"=>false, "err_param"=>"token", "err" => "Token is required"));
}

echo $response;




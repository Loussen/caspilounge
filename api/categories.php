<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 4/23/2019
 * Time: 9:44 AM
 */

header("Content-Type: application/json; charset=UTF-8");
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

$response = json_encode(array("status"=>false, "type"=>"categories", "err" => "Error system"));

$main_lang = mysqli_real_escape_string($db,$_GET['main_lang']);
$active_status = 1;

$stmt_select = mysqli_prepare($db,
    "SELECT
                    `auto_id` as `category_id`,
                    `name`
                    FROM `categories`
                    WHERE `lang_id`=(?) and `active`=(?)
                    ORDER BY `order_number`");
$stmt_select->bind_param('ii', $main_lang,$active_status);
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

        $stmt_select = mysqli_prepare($db,
            "SELECT
                    `auto_id` as `food_id`,
                    `title`,
                    `text`,
                    `price`
                    FROM `foods`
                    WHERE `cat_id`=(?) and `active`=(?) and `lang_id`=(?)
                    ORDER BY `order_number`");
        $stmt_select->bind_param('iii', $row['category_id'],$active_status,$main_lang);
        $stmt_select->execute();
        $result_menus = $stmt_select->get_result();
        $stmt_select->close();

        $j=0;
        while($row_menus=$result_menus->fetch_assoc())
        {
            $data[$i]['menus'][$j]['food_id'] = $row_menus['food_id'];
            $data[$i]['menus'][$j]['title'] = $row_menus['title'];
            $data[$i]['menus'][$j]['text'] = html_entity_decode($row_menus['text']);
            $data[$i]['menus'][$j]['price'] = $row_menus['price'];

            $j++;
        }

        $i++;
    }

    $response = json_encode(array("status"=>true, "type"=>"categories", "data" => $data));
}
else
{
    $response = json_encode(array("status"=>false, "type"=>"categories", "data" => "Not found"));
}

echo $response;




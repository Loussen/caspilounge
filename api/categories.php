<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 4/23/2019
 * Time: 9:44 AM
 */

header("Content-Type: application/json; charset=UTF-8");

include "../caspimanager/pages/includes/config.php";

$response = json_encode(array("status"=>false, "type"=>"categories", "err" => "Error system"));

$main_lang = mysqli_real_escape_string($db,$_GET['main_lang']);
$active_status = 1;

$stmt_select = mysqli_prepare($db,
    "SELECT
                    `auto_id` as `id`,
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
                    `auto_id` as `id`,
                    `title`,
                    `text`,
                    `price`
                    FROM `foods`
                    WHERE `cat_id`=(?)
                    ORDER BY `id`");
        $stmt_select->bind_param('i', $row['id']);
        $stmt_select->execute();
        $result_menus = $stmt_select->get_result();
        $stmt_select->close();

        $j=0;
        while($row_menus=$result_menus->fetch_assoc())
        {
            $data[$i]['menus'][$j]['id'] = $row_menus['id'];
            $data[$i]['menus'][$j]['title'] = $row_menus['title'];
            $data[$i]['menus'][$j]['text'] = $row_menus['text'];
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




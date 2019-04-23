<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 4/23/2019
 * Time: 9:44 AM
 */

header("Content-Type: application/json; charset=UTF-8");

include "../caspimanager/pages/includes/config.php";

$response = json_encode(array("status"=>false, "type"=>"menus_slider", "err" => "Error system"));

$main_lang = mysqli_real_escape_string($db,$_GET['main_lang']);
$active_status = 1;

$stmt_select = mysqli_prepare($db,
    "SELECT
                    `image_name`,
                    `link`
                    FROM `food_menus`
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

        $data[$i]['image_name'] = SITE_PATH."/images/food_menus/".$row['image_name'];

        $i++;
    }

    $response = json_encode(array("status"=>true, "type"=>"menus_slider", "data" => $data));
}
else
{
    $response = json_encode(array("status"=>false, "type"=>"menus_slider", "data" => "Not found"));
}

echo $response;



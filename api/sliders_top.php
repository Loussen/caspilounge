<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 4/23/2019
 * Time: 9:44 AM
 */

header("Content-Type: application/json; charset=UTF-8");

include "../caspimanager/pages/includes/config.php";

$response = json_encode(array("status"=>false, "type"=>"sliders_top", "err" => "Error system"));

$main_lang = mysqli_real_escape_string($db,$_GET['main_lang']);
$active_status = 1;

$stmt_select = mysqli_prepare($db,
    "SELECT
                    `image_name` as `imagePath`,
                    `title`,
                    `button_text` as `buttonText`,
                    `button_link` as `buttonLink`
                    FROM `sliders`
                    WHERE `lang_id`=(?) and `active`=(?)
                    ORDER BY `order_number`");
$stmt_select->bind_param('ii', $main_lang,$active_status);
$stmt_select->execute();
$result = $stmt_select->get_result();
$stmt_select->close();

if($result->num_rows>0)
{
    $data = [];
    while($row=$result->fetch_assoc())
    {
        $data[] = $row;
    }

    $response = json_encode(array("status"=>true, "type"=>"sliders_top", "data" => $data));
}
else
{
    $response = json_encode(array("status"=>false, "type"=>"sliders_top", "data" => "Not found"));
}

echo $response;




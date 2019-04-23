<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 4/23/2019
 * Time: 9:44 AM
 */

header("Content-Type: application/json; charset=UTF-8");

include "../caspimanager/pages/includes/config.php";

$response = json_encode(array("status"=>false, "type"=>"menus", "err" => "Error system"));

$main_lang = mysqli_real_escape_string($db,$_GET['main_lang']);
$active_status = 1;


if(isset($_GET['category_id']) && !empty($_GET['category_id']))
{
    $category_id = intval($_GET['category_id']);

    $stmt_select = mysqli_prepare($db,
        "SELECT
                `foods`.`auto_id` as `id`,
                `foods`.`title`,
                `foods`.`text`,
                `foods`.`price`,
                `categories`.`name` as 'categoryName',
                `categories`.`auto_id` as 'categoryId'
                FROM `foods`
                LEFT JOIN `categories` on `categories`.`auto_id`=`foods`.`cat_id`
                WHERE `foods`.`lang_id`=(?) and `foods`.`active`=(?) and `foods`.`cat_id`=(?)
                ORDER BY `foods`.`order_number`");
    $stmt_select->bind_param('iii', $main_lang,$active_status,$category_id);
}
else
{
    $stmt_select = mysqli_prepare($db,
        "SELECT
                `foods`.`auto_id` as `id`,
                `foods`.`title`,
                `foods`.`text`,
                `foods`.`price`,
                `categories`.`name` as 'categoryName',
                `categories`.`auto_id` as 'categoryId'
                FROM `foods`
                LEFT JOIN `categories` on `categories`.`auto_id`=`foods`.`cat_id`
                WHERE `foods`.`lang_id`=(?) and `foods`.`active`=(?)
                ORDER BY `foods`.`order_number`");
    $stmt_select->bind_param('ii', $main_lang,$active_status);
}


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

        $i++;
    }

    $response = json_encode(array("status"=>true, "type"=>"menus", "data" => $data));
}
else
{
    $response = json_encode(array("status"=>false, "type"=>"menus", "data" => "Not found"));
}

echo $response;




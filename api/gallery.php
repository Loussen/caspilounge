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

$response = json_encode(array("status"=>false, "type"=>"gallery", "err" => "Error system"));

$main_lang = mysqli_real_escape_string($db,$_GET['main_lang']);
$active_status = 1;

if(isset($_GET['albom_id']) && !empty($_GET['albom_id']))
{
    $albom_id = intval($_GET['albom_id']);

    $stmt_select = mysqli_prepare($db,
        "SELECT
                `gallery`.`id`,
                `gallery`.`image_name`,
                `alboms`.`title` as `albomTitle`
                FROM `gallery`
                LEFT JOIN `alboms` on `alboms`.`auto_id`=`gallery`.`albom_id`
                WHERE `gallery`.`albom_id`=(?)
                ORDER BY `gallery`.`id`");
    $stmt_select->bind_param('i', $albom_id);
}
else
{
    $stmt_select = mysqli_prepare($db,
        "SELECT
                `gallery`.`id`,
                `gallery`.`image_name`,
                `alboms`.`title` as `albomTitle`
                FROM `gallery`
                LEFT JOIN `alboms` on `alboms`.`auto_id`=`gallery`.`albom_id`
                ORDER BY `gallery`.`id`");
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

        $data[$i]['image_name'] = SITE_PATH."/images/gallery/".$row['image_name'];

        $i++;
    }

    $response = json_encode(array("status"=>true, "type"=>"gallery", "data" => $data));
}
else
{
    $response = json_encode(array("status"=>false, "type"=>"gallery", "data" => "Not found"));
}

echo $response;




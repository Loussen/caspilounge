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

$response = json_encode(array("status"=>false, "type"=>"alboms", "err" => "Error system"));

$main_lang = mysqli_real_escape_string($db,$_GET['main_lang']);
$active_status = 1;

$stmt_select = mysqli_prepare($db,
    "SELECT
                    `auto_id` as `id`,
                    `image_name` as `imagePath`,
                    `title`,
                    `created_at` as `date`
                    FROM `alboms`
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

        $data[$i]['imagePath'] = SITE_PATH."/images/alboms/".$row['imagePath'];
        $data[$i]['date'] = date("d/m/Y", $row['date']);

        $stmt_select = mysqli_prepare($db,
            "SELECT
                    `id`,
                    `image_name` as `imagePath`
                    FROM `gallery`
                    WHERE `albom_id`=(?)
                    ORDER BY `id`");
        $stmt_select->bind_param('i', $row['id']);
        $stmt_select->execute();
        $result_gallery = $stmt_select->get_result();
        $stmt_select->close();

        $j=0;
        while($row_gallery=$result_gallery->fetch_assoc())
        {
            $data[$i]['images'][$j]['id'] = $row_gallery['id'];
            $data[$i]['images'][$j]['imagePath'] = SITE_PATH."/images/gallery/".$row_gallery['imagePath'];

            $j++;
        }

        $i++;
    }

    $response = json_encode(array("status"=>true, "type"=>"alboms", "data" => $data));
}
else
{
    $response = json_encode(array("status"=>false, "type"=>"alboms", "data" => "Not found"));
}

echo $response;




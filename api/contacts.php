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

$response = json_encode(array("status"=>false, "type"=>"contacts", "err" => "Error system"));

$main_lang = mysqli_real_escape_string($db,$_GET['main_lang']);

$stmt_select = mysqli_prepare($db,"SELECT 
                                          `facebook`,`youtube`,`twitter`,`instagram`,`google_map`,`address`,`phone`,`text`,`footer` 
                                          FROM `contacts` 
                                          WHERE `lang_id`=(?) ORDER BY `id` DESC");
$stmt_select->bind_param('i', $main_lang);
$stmt_select->execute();
$stmt_select->bind_result($facebook,$youtube,$twitter,$instagram,$google_map,$address,$phone,$text,$footer);
$stmt_select->fetch();
$stmt_select->close();

$data['facebook'] = $facebook;
$data['youtube'] = $youtube;
$data['twitter'] = $twitter;
$data['instagram'] = $instagram;
$data['google_map'] = $google_map;
$data['address'] = $address;
$data['phone'] = $phone;
$data['text'] = $text;
$data['footer'] = $footer;


$response = json_encode(array("status"=>true, "type"=>"contacts", "data" => $data));


echo $response;




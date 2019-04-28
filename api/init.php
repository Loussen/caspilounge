<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 4/24/2019
 * Time: 10:49 AM
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


if(isset($_SERVER['HTTP_SECRET']) && !empty($_SERVER['HTTP_SECRET']) && $_SERVER['HTTP_SECRET']!='null')
{
    $response = json_encode(array("status"=>true, "type"=>"token", "token" => $_SERVER['HTTP_SECRET']));
}
else
{
    $token = md5(uniqid(rand(), true));

    $response = json_encode(array("status"=>true, "type"=>"new_token", "token" => $token));
}

echo $response;
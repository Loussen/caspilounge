<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 4/23/2019
 * Time: 9:44 AM
 */

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "../caspimanager/pages/includes/config.php";

$response = json_encode(array("status"=>false, "type"=>"catering", "err" => "Error system"));

$data = json_decode(file_get_contents("php://input"), true);

var_dump($data);

echo $response;




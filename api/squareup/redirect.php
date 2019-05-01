<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 4/29/2019
 * Time: 12:28 PM
 */
include "../../caspimanager/pages/includes/config.php";

$image_name = $albom_id = $created_at = $updated_at = 1;

$stmt_insert = mysqli_prepare($db, "INSERT INTO `gallery` (`image_name`,`albom_id`,`created_at`,`updated_at`) VALUES (?,?,?,?)");
$stmt_insert->bind_param('iiii', $image_name,$albom_id,$created_at,$updated_at);
$stmt_insert->execute();
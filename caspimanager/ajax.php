<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 2/13/2019
 * Time: 12:27 PM
 */

include "pages/includes/config.php";

$sql = mysqli_query($db,"select `auto_id`,`name` from `tags` where lang_id='$main_lang' order by order_number desc");

$json = [];
while($row = mysqli_fetch_assoc($sql))
{
    $json[] = $row['name'];
}

echo json_encode($json);
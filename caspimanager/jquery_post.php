<?php
include "pages/includes/config.php";
$info_id=intval($_POST["info_id"]);
$table=safe($_POST["table"]);
$new_activlik=intval($_POST["new_activlik"]);

if( ($table=="bannerler" || $table=="diller" || $table=="yazarlar") and ($info_id>0 && $new_activlik>=0) or $table=="serhler") mysqli_query($db,"update $table set active='$new_activlik' where id='$info_id' ");
elseif($info_id>0 && $new_activlik>=0) mysqli_query($db,"update $table set active='$new_activlik' where auto_id='$info_id' ");
?>
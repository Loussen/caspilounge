<?php
if(isset($_GET["lang"])) $lang=intval($_GET["lang"]);
if(mysqli_num_rows(mysqli_query($db,"select id from diller where id='$lang' "))>0){
	$_SESSION["admin_lang"]=$lang;
}
$lang=intval($_SESSION["admin_lang"]);
if(is_file($lang.'/lang.php')) include $lang.'/lang.php';
elseif(is_file('langs/admin/'.$lang.'/lang.php')) include_once 'langs/admin/'.$lang.'/lang.php';
else{
	$lang=$main_lang;
	$_SESSION["admin_lang"]=$lang;
	if(is_file($lang.'/lang.php')) include $lang.'/lang.php';
	elseif(is_file('langs/admin/'.$lang.'/lang.php')) include_once 'langs/admin/'.$lang.'/lang.php';
}
$main_lang=$lang;

$referer=safe($_SERVER['HTTP_REFERER']); if($referer=="") $referer="index.php";
if(intval(strpos($referer,"lang="))>0) $referer=substr($referer,0,strlen($referer)-7);
if(isset($_GET["lang"]) && $do!="diller") header("Location: $referer");
?>
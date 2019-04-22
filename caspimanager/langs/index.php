<?php
// URL with lang
if(isset($_GET["lang_name"])){
	$lang_name=addslashes($_GET["lang_name"]);
	if($lang_name=='ru') $lang=2;
	elseif($lang_name=='en') $lang=3;
	else { $lang_name=''; $lang=1; }
}
elseif(isset($_GET["lang"])) $lang=intval($_GET["lang"]);
if($lang==2) $lang_name='ru';
elseif($lang==3) $lang_name='en';
else $lang_name='';

if($lang_name!='') $site.='/'.$lang_name;

if(isset($_GET["lang"])) $lang=intval($_GET["lang"]);
if(mysqli_num_rows(mysqli_query($db,"select id from diller where id='$lang' "))>0){
	$_SESSION["lang"]=$lang;
}
$lang=intval($_SESSION["lang"]);
if(is_file($lang.'/lang.php')) include $lang.'/lang.php';
elseif(is_file('caspimanager/langs/'.$lang.'/lang.php')) include 'caspimanager/langs/'.$lang.'/lang.php';
else{
	$lang=$main_lang;
	$_SESSION["lang"]=$lang;
	if(is_file($lang.'/lang.php')) include $lang.'/lang.php';
	elseif(is_file('caspimanager/langs/'.$lang.'/lang.php')) include 'caspimanager/langs/'.$lang.'/lang.php';
}
$main_lang=$lang;

$referer=safe($_SERVER['HTTP_REFERER']); if($referer=="") $referer="index.php";
if(intval(strpos($referer,"lang="))>0) $referer=substr($referer,0,strlen($referer)-7);
if(isset($_GET["lang"]) && $do!="diller") header("Location: $referer");
?>
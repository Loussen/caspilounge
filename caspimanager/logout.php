<?php
ob_start(); session_start();
$_SESSION["login"]="";
$_SESSION["pass"]="";
header("Location: login.php");
?>
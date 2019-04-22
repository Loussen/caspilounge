<?php
// Data of user
$check_login=safe($_SESSION["login"]);
$check_pass=safe($_SESSION["pass"]);
$user=mysqli_fetch_assoc(mysqli_query($db,"select * from admin_pass where login='$check_login' and pass='$check_pass' "));
if(intval($user["id"])==0)
{
    $admin_user = false;
}
else
{
    $admin_user = true;
}
?>
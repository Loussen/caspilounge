<?php
/**
 * Created by PhpStorm.
 * User: fuad
 * Date: 1/18/17
 * Time: 1:17 PM
 */
?>
<?php
    $check_id=intval($_SESSION["logged_user_id"]);
    $check_ps=safe($_SESSION["logged_user_pass"]);
    $sql=mysqli_query($db,"select * from `users` where `id`='$check_id' and `pass`='$check_ps'");
    if(mysqli_num_rows($sql)>0){
        $info_profil=mysqli_fetch_assoc($sql);
    }
    else{
        $check_id=intval($_COOKIE["logged_user_id"]);
        $check_ps=safe($_COOKIE["logged_user_pass"]);
        $sql=mysqli_query($db,"select * from users where id='$check_id' and pass='$check_ps' ");
        if(mysqli_num_rows($sql)>0){
            $_SESSION["logged_user_id"]=$check_id;
            $_SESSION["logged_user_pass"]=$check_ps;
            $info_profil=mysqli_fetch_assoc($sql);
        }
    }
?>

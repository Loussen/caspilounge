<?php
$login = 'fizuli';
// fizuli!)(
$pass = '2cf2d171fe609c60aff6b23876db87fc';

//if(( @md5(md5($_SERVER['PHP_AUTH_PW'])) != $pass || @$_SERVER['PHP_AUTH_USER'] != $login)|| !@$_SERVER['PHP_AUTH_USER'])
//{
//    header('WWW-Authenticate: Basic realm="Access denied"');
//    header('HTTP/1.0 401 Unauthorized');
//    echo 'Auth failed';
//    exit;
//}

include "pages/includes/config.php";
if($_POST){
	if(check_csrf_(safe($_POST["csrf_"]))){
		$login=safe($_POST["login"]);
		$pass=md5(md5(safe($_POST["pass"])));
		$user=mysqli_fetch_assoc(mysqli_query($db,"select login,pass from admin_pass where login='$login' and pass='$pass' "));
		if($login==$user["login"] && $pass==$user["pass"]){
			$_SESSION["login"]=$login;
			$_SESSION["pass"]=$pass;
			header("Location: index.php"); exit();
		}
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head><?php include "pages/layouts/head.php"; ?></head>
<body class="login">
	<!-- Begin login window -->
	<div id="login_wrapper">
		<br class="clear"/>
		<div id="login_top_window">
			<img src="images/blue/top_login_window.png" alt="top window"/>
		</div>
		<div id="login_body_window">
			<div class="inner">
                <img src="<?=SITE_PATH?>/images/logo.png" alt="logo" style="width: 50%; padding: 5px; background-color: #0A173B;"/>
				<form action="login.php" method="post" id="form_login" name="form_login">
					<p><input type="text" id="username" name="login" style="width:285px" title="Username"/></p>
					<p><input type="password" id="password" name="pass" style="width:285px" title="******"/></p>
					<p><input type="hidden" name="csrf_" value="<?=set_csrf_()?>" /></p>
					<p><input type="submit" id="submit" name="submit" value=" Log in " class="Login" style="margin-right:5px"/></p>
				</form>
			</div>
		</div>
		<div id="login_footer_window">
			<img src="images/blue/footer_login_window.png" alt="footer window"/>
		</div>
		<div id="login_reflect">
			<img src="images/blue/reflect.png" alt="window reflect"/>
		</div>
	</div>
</body>
</html>
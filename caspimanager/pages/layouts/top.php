<?php
//$new_messages=mysqli_num_rows(mysqli_query($db,"select id from mektublar where oxunmasi='0' "));
?>
<div id="header">
	<div id="logo">
<!--		<img src="../assets/img/logo.svg" style="width: 75%; margin-top: 5px;" />-->
	</div>
	<div id="search">
		<p style="margin-top:-4px;margin-left:-5px;color:#000000">
			<?php
			$sql=mysqli_query($db,"select * from diller order by sira");
			while($row=mysqli_fetch_assoc($sql)){
				echo '&nbsp;&nbsp;<a href="index.php?lang='.$row["id"].'" style="color:darkgreen">'.$row["tam_adi"].'</a>';
			}
			?>
			<a href="logout.php" style="color:#000000"><img align="middle" src="images/logout.png" alt="" style="margin-left:10px;margin-bottom:-15px" /> <span style="vertical-align: -webkit-baseline-middle;">Logout</span></a>
		</p>
	</div>
	<div id="account_info">
<!--		<img src="images/icon_online.png" alt="Online" class="mid_align"/>-->
<!--		<a href="javascript:void(0);">--><?//=ucfirst($user["login"])?><!--</a>-->
<!--		--><?php //if(isset($all_online)) { ?>
<!--			| Qeydiyyat sayı: --><?php //echo mysqli_num_rows(mysqli_query($db,"select id from users")); ?>
<!--			| Online: --><?php //echo $all_online; ?>
<!--			| İstifadəçi sayı: --><?php //echo $user_online; ?>
<!--			| Qonaq sayı: --><?php //echo $guest_online; ?>
<!--		--><?php //} ?>
	</div>
</div>
<div class="clear"></div>

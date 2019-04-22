<?php
$edit=intval($_GET["edit"]);
$add=intval($_GET["add"]);
$delete=intval($_GET["delete"]);

if($_POST)
{
	$login=addslashes($_POST["login"]);
	$pass=md5(md5(safe($_POST["pass"])));
	if($login!="")
	{
		if($add==1)
		{
			mysqli_query($db,"insert into admin_pass values (0,'$login','$pass') ");
			$ok="Məlumatlar yadda saxlanıldı.";
			$add=0;
		}
		else
		{
			mysqli_query($db,"update admin_pass set login='$login' where id='$edit' ");
			if( $pass!=md5(md5("")) ){
				mysqli_query($db,"update $do set pass='$pass' where id='$edit' ");
				$_SESSION["login"]=$login;
				$_SESSION["pass"]=$pass;
			}
			$ok="Məlumatlar yadda saxlanıldı.";
			$edit=0;
		}
	}
}
elseif($delete>0 && mysqli_num_rows(mysqli_query($db,"select id from admin_pass where id='$delete' "))>0 && $delete!=1)
{
	mysqli_query($db,"delete from admin_pass where id='$delete' ");
	$ok="İstifadəçi silindi.";
}
?>
<div class="onecolumn">
	<div class="header">
		<span>Admin users</span>
	</div>
	<br class="clear"/>
	<div class="content">
	<?php
	if($ok!="") echo '<div class="alert_success"><p><img src="images/icon_accept.png" alt="success" class="mid_align"/>'.$ok.'</p></div>';
	if($error!="") echo '<div class="alert_error"><p><img src="images/icon_error.png" alt="delete" class="mid_align"/>'.$error.'</p></div>';
	?>
	<!-- Content start-->
	<a href="index.php?do=<?php echo $do; ?>&add=1" style="margin-right:50px"><img style="vertical-align: text-top;" src="images/icon_add.png" alt="" /> <b>Yeni əlavə et</b></a><br /><br />
		<form action="index.php?do=admin_pass<?php if($edit>0) echo '&edit='.$edit; ?><?php if($add>0) echo '&add='.$add; ?>" method="post" id="form_login" name="form_login" enctype="multipart/form-data">
		<div style="display:<?php if($edit>0 || $add==1) echo "block"; else echo "none"; ?>">	
		<?php $information=mysqli_fetch_assoc(mysqli_query($db,"select * from admin_pass where id='$edit' ")); ?>
			<p>
				<label>Username:<sup>*</sup></label><br />
				<input name="login" type="text" size="25" value="<?php echo $information["login"]; ?>" />
			</p><br />
			<p>
				<label>Password:<sup>*</sup></label><br />
				<input name="pass" type="text" size="25" value="" />
			</p><br />
				<input type="submit" name="button" value=" Save " />
				<hr class="clear"/>
		</div>
		</form>
		<?php
echo '<table class="data" width="100%" cellpadding="0" cellspacing="0"><thead><tr>
<th style="width:50%">Username</th>
<th style="width:25%">Düzəlişlər</th></tr></thead><tbody>';
$sql=mysqli_query($db,"select * from admin_pass order by id desc");
		while($row=mysqli_fetch_assoc($sql))
		{
			echo '<tr>
					<td>'.$row["login"].'</td>
					<td>
						<a href="index.php?do=admin_pass&edit='.$row["id"].'"><img src="images/icon_edit.png" alt="" title="Edit" /></a>
						<a href="index.php?do=admin_pass&delete='.$row["id"].'" class="delete"><img src="images/icon_delete.png" alt="" title="Delete" /></a>';
					echo '</td>
				</tr>';
		}
echo '</tbody></table>';
		?>
	<br class="clear"/>
	<!-- Content end-->
	</div>
</div>
<?php
$albom=intval($_GET["albom"]); if($albom==0) {$albom=mysqli_fetch_assoc(mysqli_query($db,"select * from alboms order by order_number desc")); $albom=$albom["auto_id"];}
$edit=intval($_GET["edit"]);
$delete=intval($_GET["delete"]);
$add=intval($_GET["add"]);

if($edit>0 && mysqli_num_rows(mysqli_query($db,"select id from alboms where auto_id='$edit' "))==0) header("Location: index.php?do=$do");

if($_POST["add_gallery"])
{
    $image_tmps = $_FILES["image_file"]["tmp_name"];
	$image_types=$_FILES["image_file"]["type"];
	$image_names=$_FILES["image_file"]["name"];
	$count=0;

	$time = time();

	foreach($image_tmps as $image_tmp)
	{
		$type=explode(".",$image_names[$count]);
		$type=end($type);
        $type=strtolower($type);
		$image_type=$image_types[$count];
		$image_name=$image_names[$count];
		$image_access=false;

		if( ($image_type=="image/pjpeg" || $image_type=="image/jpeg" || $image_type=="image/bmp"  || $image_type=="image/x-png" || $image_type=="image/gif" || $image_type=="image/png")and($type=="jpg" || $type=="bmp"  || $type=="png" || $type=="gif" || $type=="jpeg") ) $image_access=true;
		if($image_access==true)
		{
            $image_upload_name = substr(sha1(mt_rand()),17,15)."-".pathinfo($image_name, PATHINFO_FILENAME).".".$type;

			mysqli_query($db,"insert into $do values (0,'$image_upload_name','$albom','$time',0) ");

			$last_id=mysqli_insert_id($db);
			move_uploaded_file($image_tmp,'../images/gallery/'.$image_upload_name);
            compress('../images/gallery/'.$image_upload_name, '../images/gallery/'.$image_upload_name, 80);

            $ok="Data has been successfully saved.";
		}
		$count++;
	}
}
elseif($delete>0 && mysqli_num_rows(mysqli_query($db,"select id from $do where id='$delete' "))>0)
{
	$image_name=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where id='$delete'"));
    $image_name=$image_name["image_name"];
	mysqli_query($db,"delete from $do where id='$delete' ");
	@unlink('../images/gallery/'.$image_name);
    $ok="Data has been successfully deleted.";
}
?>
<script type="text/JavaScript">
	<!--
	function MM_jumpMenu(targ,selObj,restore){ //v3.0
		eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
		if (restore) selObj.selectedIndex=0;
	}
	//-->
</script>
<div class="onecolumn">
	<div class="header">
		<span>Gallery</span>

	</div>
	<br class="clear"/>
	<div class="content">
		<?php
		if($ok!="") echo '<div class="alert_success"><p><img src="images/icon_accept.png" alt="success" class="mid_align"/>'.$ok.'</p></div>';
		if($error!="") echo '<div class="alert_error"><p><img src="images/icon_error.png" alt="delete" class="mid_align"/>'.$error.'</p></div>';
		?>
		<!-- Content start-->
		<form action="index.php?do=<?php echo $do; ?><?php if($albom>0) echo '&albom='.$albom; if($edit>0) echo '&edit='.$edit; if($add==1) echo '&add='.$add;?>" method="post" id="form_login" name="form_login" enctype="multipart/form-data">
			<hr class="clear" />

			Alboms:&nbsp;&nbsp;&nbsp;
			<select name="sorgu" onchange="MM_jumpMenu('parent',this,0)">
				<?php
				$sql=mysqli_query($db,"select * from alboms where lang_id='$main_lang' order by order_number desc");
				while($row=mysqli_fetch_assoc($sql))
				{
					if($row["auto_id"]==$albom) echo '<option value="index.php?do='.$do.'&albom='.$row["auto_id"].'" selected="selected">'.$row["title"].'</option>';
					else echo '<option value="index.php?do='.$do.'&albom='.$row["auto_id"].'">'.$row["title"].'</option>';
				}
				?>
			</select>
			<br class="clear" /><br />
			<u>Images:</u><br class="clear" />
			<ul class="media_photos">
				<?php
				$sql=mysqli_query($db,"select * from $do where albom_id='$albom' order by id");
				while($row=mysqli_fetch_assoc($sql))
				{
					echo '<li style="margin-bottom:20px">
				  <a rel="slide" href="../images/gallery/'.$row["image_name"].'" title="">
				  	<img src="../images/gallery/'.$row["image_name"].'" alt="" width="75" height="75" />
				  </a>
				  <br class="clear" />
				  '.$row["title"].' <a href="index.php?do='.$do.'&delete='.$row["id"].'&albom='.$albom.'" title="Sil" class="delete"><img src="images/icon_delete.png" alt="" /></a>
			</li>';
				}
				?>
			</ul>
			<br class="clear" />
			<div style="display:<?php if($add==0 && $edit_albom==0) echo "block"; else echo "none"; ?>">
				<b>Create new:</b><br />
				<input name="image_file[]" type="file" multiple /><br /> <input type="submit" name="add_gallery" value=" Save " />
			</div>
		</form>
		<!-- Content end-->
	</div>
</div>
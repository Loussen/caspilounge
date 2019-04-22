<?php
if($_POST) // Add && edit
{
	extract($_POST);
	$sql=mysqli_query($db,"select * from diller order by sira");
	while($row=mysqli_fetch_assoc($sql))
	{
		$description_="description__".$row["id"]; $description_=mysqli_real_escape_string($db,htmlspecialchars($$description_));
		$keywords_="keywords__".$row["id"]; $keywords_=mysqli_real_escape_string($db,htmlspecialchars($$keywords_));
		$title_="title__".$row["id"]; $title_=mysqli_real_escape_string($db,htmlspecialchars($$title_));
		//if($text=='' && $row["id"]!=$main_lang) {$c='text'; $$c=get_lang_val($do,$auto_id,$c);}
		
		if(mysqli_num_rows(mysqli_query($db,"select id from $do where lang_id='$row[id]' "))>0) mysqli_query($db,"update $do set description_='$description_',keywords_='$keywords_',title_='$title_' where lang_id='$row[id]' ");
		else mysqli_query($db,"insert into $do values (0,'$description_','$keywords_','$title_','$row[id]') ");
	}
	$ok=$lang1;
	$edit=0;
}
$information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do"));
?>
<!-- Begin one column tab content window -->
<div class="onecolumn">
	<div class="header">
		<span>Seo optimization</span>
		<div class="switch">
			<table cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
				<?php
				$sql=mysqli_query($db,"select * from diller order by sira");
				if(mysqli_num_rows($sql)>1)
				{
					while($row=mysqli_fetch_assoc($sql))
					{
						echo '<td><input type="button" id="tab_lang'.$row["id"].'" onclick="tab_select(this.id)" class="left_switch" value="'.$row["ad"].'" style="width:50px"/></td>';
					}
				}
				?>
				</tr>
			</tbody>
			</table>
		</div>
	</div>
	<br class="clear"/>
	<div class="content">
		<?php
		if($ok!="") echo '<div class="alert_success"><p><img src="images/icon_accept.png" alt="success" class="mid_align"/>'.$ok.'</p></div>';
		if($error!="") echo '<div class="alert_error"><p><img src="images/icon_error.png" alt="delete" class="mid_align"/>'.$error.'</p></div>';
		?>
		<form action="index.php?do=<?php echo $do; ?>" method="post" id="form_login" name="form_login">
		<?php
		$sql=mysqli_query($db,"select * from diller order by sira");
		$ssira=1;
		while($row=mysqli_fetch_assoc($sql)){
			$information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where lang_id='$row[id]' "));
			if($lang_count>1) echo '<div id="tab'.$row["id"].'_content" class="tab_content '.$hide.'">'; else echo '<div class="'.$hide.'">';
			echo '
			Description:<br /><textarea name="description__'.$row["id"].'" cols="70" rows="6">'.stripslashes($information["description_"]).'</textarea><br /><br />
			Keywords:<br /><input name="keywords__'.$row["id"].'" type="text" value="'.stripslashes($information["keywords_"]).'" style="width:600px" /><br /><br />
			Title:<br /><input name="title__'.$row["id"].'" type="text" value="'.stripslashes($information["title_"]).'" style="width:600px" /><br />

			<br class="clear" />
			<p><input type="submit" id="submit" name="submit" value=" Save " class="Login" style="margin-right:5px"/></p>
			</div>';
			$ssira++;
		}
		?>
		</form>
	</div>
</div>
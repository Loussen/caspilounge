<?php
if($_POST) // Add && edit
{
	extract($_POST);
	$sql=mysqli_query($db,"select * from diller order by sira");
	$auto_id=1;
	while($row=mysqli_fetch_assoc($sql))
	{
		$text="text_".$row["id"];		$text=htmlspecialchars($$text);	
		$footer="footer_".$row["id"];	$footer=htmlspecialchars($$footer);
		if($text=='' && $row["id"]!=$main_lang) {$c='text'; $$c=get_lang_val($do,$auto_id,$c);}
		if($footer=='' && $row["id"]!=$main_lang) {$c='footer'; $$c=get_lang_val($do,$auto_id,$c);}
		$address="address_".$row["id"]; $address=mysqli_real_escape_string($db,htmlspecialchars($$address));
		if($address=='' && $row["id"]!=$main_lang) {$c='address'; $$c=get_lang_val($do,$auto_id,$c);}
        $worksday="worksday_".$row["id"]; $worksday=mysqli_real_escape_string($db,htmlspecialchars($$worksday));
        if($worksday=='' && $row["id"]!=$main_lang) {$c='worksday'; $$c=get_lang_val($do,$auto_id,$c);}

		if(intval(++$sss)==1)
		{
		$email=htmlspecialchars($email);
		$facebook=htmlspecialchars($facebook);
		$twitter=htmlspecialchars($twitter);
		$vkontakte=htmlspecialchars($vkontakte);
		$linkedin=htmlspecialchars($linkedin);
		$digg=htmlspecialchars($digg);
		$flickr=htmlspecialchars($flickr);
		$dribbble=htmlspecialchars($dribbble);
		$vimeo=htmlspecialchars($vimeo);
		$myspace=htmlspecialchars($myspace);
		$google=htmlspecialchars($google);
		$youtube=htmlspecialchars($youtube);
		$instagram=htmlspecialchars($instagram);
		$phone=htmlspecialchars($phone);
		$mobile=htmlspecialchars($mobile);
		$skype=htmlspecialchars($skype);
		$fax=htmlspecialchars($fax);
		$google_map=htmlspecialchars($google_map);

		}
		
		if(mysqli_num_rows(mysqli_query($db,"select id from $do where lang_id='$row[id]' and auto_id='$auto_id' "))>0) mysqli_query($db,"update $do set text='$text', email='$email',facebook='$facebook',twitter='$twitter', vkontakte='$vkontakte',linkedin='$linkedin',digg='$digg',flickr='$flickr', dribbble='$dribbble', vimeo='$vimeo',myspace='$myspace',google='$google',  youtube='$youtube', instagram='$instagram', phone='$phone', mobile='$mobile', skype='$skype', fax='$fax',google_map='$google_map',address='$address',footer='$footer',worksday='$worksday' where lang_id='$row[id]' ");
		else mysqli_query($db,"insert into $do values (0,'$text','$email','$facebook','$twitter', '$vkontakte', '$linkedin', '$digg', '$flickr', '$dribbble', '$vimeo', '$myspace', '$google', '$youtube', '$instagram', '$phone', '$mobile', '$skype', '$fax', '$google_map','$address', '$footer','$worksday', '$row[id]', '$auto_id') ");
	}
	$ok="Məlumatlar uğurla yadda saxlanıldı.";
	$edit=0;
}
$information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do"));
?>
<div class="onecolumn">
	<div class="header">
		<span>Contacts</span>
<!--		<div class="switch">-->
<!--			<table cellpadding="0" cellspacing="0">-->
<!--			<tbody>-->
<!--				<tr>-->
<!--				--><?php
//				$sql=mysqli_query($db,"select * from diller order by sira");
//				if(mysqli_num_rows($sql)>1)
//				{
//					while($row=mysqli_fetch_assoc($sql))
//					{
//						echo '<td><input type="button" id="tab_lang'.$row["id"].'" onclick="tab_select(this.id)" class="left_switch" value="'.$row["ad"].'" style="width:50px"/></td>';
//					}
//				}
//				?>
<!--				</tr>-->
<!--			</tbody>-->
<!--			</table>-->
<!--		</div>-->
	</div>
	<br class="clear"/>
	<div class="content">
	<?php
	if($ok!="") echo '<div class="alert_success"><p><img src="images/icon_accept.png" alt="success" class="mid_align"/>'.$ok.'</p></div>';
	if($error!="") echo '<div class="alert_error"><p><img src="images/icon_error.png" alt="delete" class="mid_align"/>'.$error.'</p></div>';
	?>
	<!-- Content start-->
		<form action="index.php?do=<?php echo $do; ?>" method="post" id="form_login" name="form_login">
			<?php
//			$sql=mysqli_query($db,"select * from diller order by sira");
//			while($row=mysqli_fetch_assoc($sql))
//			{
//				$information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where lang_id='$row[id]' "));
//				if($lang_count>1) echo '<div id="tab'.$row["id"].'_content" class="tab_content '.$hide.'">'; else echo '<div class="'.$hide.'">';
//				echo 'Text:
//				<p><textarea name="text_'.$row["id"].'" id="editor'.$df++.'">'.stripslashes($information["text"]).'</textarea></p>
//
//				Footer:
//				<textarea name="footer_'.$row["id"].'" rows="1" cols="1" id="editor'.$df++.'">'.stripslashes($information["footer"]).'</textarea>
//
//				<br class="clear" />
//				</div>';
//			}
			$information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where lang_id='$main_lang' "));
			?>
			<div>
				Email:
				<p><input type="text" name="email" value="<?php echo stripslashes($information["email"]); ?>" style="width:300px" /></p>
				<br />
				Facebook:
				<p><input type="text" name="facebook" value="<?php echo stripslashes($information["facebook"]); ?>" style="width:300px" /></p>
				<br />
				Twitter:
				<p><input type="text" name="twitter" value="<?php echo stripslashes($information["twitter"]); ?>" style="width:300px" /></p>
				<br />
<!--				Vkontakte:-->
<!--				<p><input type="text" name="vkontakte" value="--><?php //echo stripslashes($information["vkontakte"]); ?><!--" style="width:300px" /></p>-->
<!--				-->
<!--				LinkedIn:-->
<!--				<p><input type="text" name="linkedin" value="--><?php //echo stripslashes($information["linkedin"]); ?><!--" style="width:300px" /></p>-->
<!--				-->
<!--				Digg:-->
<!--				<p><input type="text" name="digg" value="--><?php //echo stripslashes($information["digg"]); ?><!--" style="width:300px" /></p>-->
<!--				-->
<!--				Flickr:-->
<!--				<p><input type="text" name="flickr" value="--><?php //echo stripslashes($information["flickr"]); ?><!--" style="width:300px" /></p>-->
<!--				-->
<!--				Dribbble:-->
<!--				<p><input type="text" name="dribbble" value="--><?php //echo stripslashes($information["dribbble"]); ?><!--" style="width:300px" /></p>-->
<!--				-->
<!--				Vimeo:-->
<!--				<p><input type="text" name="vimeo" value="--><?php //echo stripslashes($information["vimeo"]); ?><!--" style="width:300px" /></p>-->
<!--				-->
<!--				Myspace:-->
<!--				<p><input type="text" name="myspace" value="--><?php //echo stripslashes($information["myspace"]); ?><!--" style="width:300px" /></p>-->
<!--				-->
<!--				Google+:-->
<!--				<p><input type="text" name="google" value="--><?php //echo stripslashes($information["google"]); ?><!--" style="width:300px" /></p>-->
				
				Youtube:
				<p><input type="text" name="youtube" value="<?php echo stripslashes($information["youtube"]); ?>" style="width:300px" /></p>
				<br />
				Instagram:
				<p><input type="text" name="instagram" value="<?php echo stripslashes($information["instagram"]); ?>" style="width:300px" /></p>
                <br />
				Telefon:
				<p><input type="text" name="phone" value="<?php echo stripslashes($information["phone"]); ?>" style="width:300px" /></p>
				<br />
				
<!--				Skype:-->
<!--				<p><input type="text" name="skype" value="--><?php //echo stripslashes($information["skype"]); ?><!--" style="width:300px" /></p>-->

<!--				Telefon 2:-->
<!--				<p><input type="text" name="mobile" value="--><?php //echo stripslashes($information["mobile"]); ?><!--" style="width:300px" /></p>-->

<!--				Telefon 3:-->
<!--				<p><input type="text" name="phone" value="--><?php //echo stripslashes($information["phone"]); ?><!--" style="width:300px" /></p>-->
				
<!--				Google Map: (Iframe):-->
<!--				<p><textarea cols="10" rows="15" name="google_map" style="width:300px">--><?//=stripslashes($information["google_map"]); ?><!--</textarea></p>-->
<!--				<br />-->

				<?php
//					$sql=mysqli_query($db,"select * from diller order by sira");
//
//				while($row=mysqli_fetch_assoc($sql)){
//					$information=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where lang_id='$row[id]' "));
//					if($lang_count>1) echo '<div id="tab'.$row["id"].'_content" class="tab_content '.$hide.'">'; else echo '<div class="'.$hide.'">';
//					echo '
//
//
//			</div>';
//				}
				?>

				
				<br class="clear" />
				<p><input type="submit" id="submit" name="submit" value=" Save " class="Login" style="margin-right:5px"/></p>
			</div>
		</form>
	<!-- Content end-->
	</div>
</div>
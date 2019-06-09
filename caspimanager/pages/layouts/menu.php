<a href="javascript:;" id="show_menu">&raquo;</a>
<div id="left_menu">
	<a href="javascript:;" id="hide_menu">&laquo;</a>
	<ul id="main_menu">
        <?php
            if($user['login']!='admin')
            {
                ?>
                <li><a href="<?=SITE_PATH.'/caspimanager'?>"><img src="images/home.png" alt="" width="25"/>Home</a></li>
                <li><a href="index.php?do=orders"><img src="images/pevents.png" alt="" width="25"/>Orders</a></li>
                <li><a href="index.php?do=cart"><img src="images/cart.png" alt="" width="25"/>Cart</a></li>
                <li><a href="index.php?do=customers"><img src="images/customers.png" alt="" width="25"/>Customers</a></li>
                <li><a href="index.php?do=categories"><img src="images/categories.png" alt="" width="25"/>Menu categories</a></li>
                <li><a href="index.php?do=foods"><img src="images/menus.png" alt="" width="25"/>Menus</a></li>
                <?php
            }
            else
            {
                ?>
                <li><a href="<?=SITE_PATH.'/caspimanager'?>"><img src="images/home.png" alt="" width="25"/>Home</a></li>
                <li><a href="index.php?do=sliders"><img src="images/sliders.png" alt="" width="25"/>Sliders top</a></li>
                <li><a href="index.php?do=orders"><img src="images/pevents.png" alt="" width="25"/>Orders</a></li>
                <li><a href="index.php?do=cart"><img src="images/cart.png" alt="" width="25"/>Cart</a></li>
                <li><a href="index.php?do=customers"><img src="images/customers.png" alt="" width="25"/>Customers</a></li>
                <li><a href="index.php?do=pevents"><img src="images/pevents.png" alt="" width="25"/>Private events</a></li>
                <li><a href="index.php?do=sliders2nd"><img src="images/sliders.png" alt="" width="25"/>Sliders middle</a></li>
                <li><a href="index.php?do=sliders3rd"><img src="images/sliders.png" alt="" width="25"/>Sliders bottom</a></li>
                <li><a href="index.php?do=food_menus"><img src="images/sliders.png" alt="" width="25"/>Menus images</a></li>
                <li><a href="index.php?do=alboms"><img src="images/alboms.png" alt="" width="25"/>Alboms</a></li>
                <li><a href="index.php?do=gallery"><img src="images/gallery.png" alt="" width="25"/>Gallery</a></li>
                <li><a href="index.php?do=categories"><img src="images/categories.png" alt="" width="25"/>Menu categories</a></li>
                <li><a href="index.php?do=foods"><img src="images/menus.png" alt="" width="25"/>Menus</a></li>
                <li><a href="index.php?do=subscribers"><img src="images/subscribers.png" alt="" width="25"/>Subscribers</a></li>
<!--                <li><a href="index.php?do=messages"><img src="images/mail.png" alt="" width="25"/>Messages</a></li>-->
                <li><a href="index.php?do=contacts"><img src="images/contacts.png" alt="" width="25"/>Contacts</a></li>
                <li><a href="index.php?do=seo"><img src="images/seoopt.png" alt="" width="25"/>Seo Opt.</a></li>

                <li><a href="javascript::void(0);">&nbsp;&nbsp;</a></li>

                <li><a href="index.php?do=diller"><img src="images/lang.png" alt="" width="25"/>Languages</a></li>
                <li><a href="index.php?do=admin_pass"><img src="images/admin.png" alt="" width="25"/>Administration</a></li>
                <?php
            }
        ?>

		<li><a href="logout.php"><img src="images/logout.png" alt="" width="25"/>Logout</a></li>
	</ul>
	<br class="clear"/>	
</div>
<?php
/*

*/
?>
<?php
//    $starttime = microtime(true);
    include "caspimanager/pages/includes/config.php";
    $do=safe($_GET["do"]);
    if(!is_file("includes/pages/".$do.".php")) $do='index';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "includes/head.php"; ?>
</head>
<body class="home blog">
<input type="hidden" name="csrf_" value="<?=set_csrf_()?>" />

<!-- global container -->
<div id="bwp-global-container" class="bwp-sidebar-close">
    <?php include "includes/right.php"; ?>
    <div class="bwp-main-content">
        <?php include "includes/header.php"; ?>
        <div class="container">
            <div class="bwp-main-content-container">
                <div class="row">
                    <?php include "includes/pages/".$do.".php"; ?>
                    <?php include "includes/left.php"; ?>
                </div>
            </div>
        </div>
        <?php include "includes/footer.php"; ?>
    </div>
</div>

<script type='text/javascript' src='<?=SITE_PATH?>/assets/js/bootstrap.min7433.js?ver=3.3.7'></script>
<script type='text/javascript' src='<?=SITE_PATH?>/assets/js/superfish.min97e9.js?ver=1.7.9'></script>
<script type='text/javascript' src='<?=SITE_PATH?>/assets/js/owl-carousel/owl.carousel.min3ba1.js?ver=1.3.3'></script>
<script type='text/javascript' src='<?=SITE_PATH?>/assets/js/jquery.magnific-popup.minf488.js?ver=1.1.0'></script>
<script type='text/javascript' src='<?=SITE_PATH?>/assets/js/ie10-viewport-bug-workaround8a54.js?ver=1.0.0'></script>
<script type='text/javascript'>
    /* <![CDATA[ */
    var ammiData = {"toTopButton": "true"};
    /* ]]> */
</script>
<script type='text/javascript' src='<?=SITE_PATH?>/assets/js/ammi-theme8a54.js?ver=1.0.0'></script>
<script type='text/javascript' src='<?=SITE_PATH?>/assets/js/wp-embed.minaead.js?ver=5.0.3'></script>

</body>
</html>

<?php
//    $endtime = microtime(true);
//    echo $endtime-$starttime;
?>
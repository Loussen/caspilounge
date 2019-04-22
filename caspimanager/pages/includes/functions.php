<?php
function strtoupper_($soz){
    $neyi=array('q','ü','e','r','t','y','u','i','o','p','ö','ğ','a','s','d','f','g','h','j','k','l','ı','ə','z','x','c','v','b','n','m','ç','ş','w');
    $neye=array('Q','Ü','E','R','T','Y','U','İ','O','P','Ö','Ğ','A','S','D','F','G','H','J','K','L','I','Ə','Z','X','C','V','B','N','M','Ç','Ş','W');
    $soz=str_replace($neyi,$neye,$soz);
    return $soz;
}

function strtolower_($soz,$ucfirst=true){
    $neyi=array('Q','Ü','E','R','T','Y','U','İ','O','P','Ö','Ğ','A','S','D','F','G','H','J','K','L','I','Ə','Z','X','C','V','B','N','M','Ç','Ş','W');
    $neye=array('q','ü','e','r','t','y','u','i','o','p','ö','ğ','a','s','d','f','g','h','j','k','l','ı','ə','z','x','c','v','b','n','m','ç','ş','w');
    $soz=str_replace($neyi,$neye,$soz);
    if($ucfirst==true) $soz=mb_ucfirst($soz,"utf-8");
    return $soz;
}

function mb_ucfirst($string, $encoding)
{
    $strlen = mb_strlen($string, $encoding);
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, $strlen - 1, $encoding);
    return mb_strtoupper($firstChar, $encoding) . $then;
}

function stlower($word,$lower=false){
	$word=str_replace(["İ","Ş","Ü","Ö","Ə","Ç"],["i","ş","ü","ö","ə","ç"],$word);
	if($lower==true) $word=strtolower($word);
	
	if(mb_substr($word,0,1,"utf-8")=='"') $start=1; else $start=0;
	
	if(mb_substr($word,$start,1,"utf-8")=="i") $word="İ".mb_substr($word,1,500,"utf-8");
	elseif(trim(mb_substr($word,$start,1,"utf-8"))=="ş" ) $word="Ş".mb_substr($word,($start+1),500,"utf-8");
	elseif(trim(mb_substr($word,$start,1,"utf-8"))=="ü" ) $word="Ü".mb_substr($word,($start+1),500,"utf-8");
	elseif(trim(mb_substr($word,$start,1,"utf-8"))=="ö" ) $word="Ö".mb_substr($word,($start+1),500,"utf-8");
	elseif(trim(mb_substr($word,$start,1,"utf-8"))=="ə" ) $word="Ə".mb_substr($word,($start+1),500,"utf-8");
	elseif(trim(mb_substr($word,$start,1,"utf-8"))=="ç" ) $word="Ç".mb_substr($word,($start+1),500,"utf-8");
	
	elseif(trim(mb_substr($word,$start,2,"utf-8"))=="ş") $word="Ş".mb_substr($word,($start+2),500,"utf-8");
	elseif(trim(mb_substr($word,$start,2,"utf-8"))=="ü") $word="Ü".mb_substr($word,($start+2),500,"utf-8");
	elseif(trim(mb_substr($word,$start,2,"utf-8"))=="ö") $word="Ö".mb_substr($word,($start+2),500,"utf-8");
	elseif(trim(mb_substr($word,$start,2,"utf-8"))=="ə") $word="Ə".mb_substr($word,($start+2),500,"utf-8");
	elseif(trim(mb_substr($word,$start,2,"utf-8"))=="ç") $word="Ç".mb_substr($word,($start+2),500,"utf-8");
	elseif($start==0) $word=ucfirst($word);
	else{
		$letter=mb_substr($word,$start,1,"utf-8"); $letter=strtoupper($letter);
		$word=mb_substr($word,0,1,"utf-8").$letter.mb_substr($word,2,500,"utf-8");
	}
	return $word;
}

function changeLang($newLang=''){
	$uri = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	if($newLang!=''){
		if($newLang=='ru'){
			$uri=str_replace(array(SITE_PATH.'/az',SITE_PATH.'/en',SITE_PATH.'/ru/en',SITE_PATH.'/en/ru'),SITE_PATH.'/',$uri);
		}
		elseif($newLang=='en'){
			$uri=str_replace(array(SITE_PATH.'/az',SITE_PATH.'/ru',SITE_PATH.'/ru/en',SITE_PATH.'/en/ru'),SITE_PATH.'/',$uri);
		}
		$uri.=$newLang;
	}
	else {$newLang='az'; $uri=str_replace(array(SITE_PATH.'/ru',SITE_PATH.'/en'),SITE_PATH.'/'.$newLang,$uri);}
	return $uri;
}

function addFullUrl($adds=array()){
	$uri = parse_url($_SERVER['QUERY_STRING'], PHP_URL_PATH);
	foreach($adds as $variable=>$value){
		if(strpos($uri,$variable."=")>0) $uri=remove_qs_key($uri,$variable);
		$uri.='&'.$variable.'='.$value;
	}
	return $uri;
}
function remove_qs_key($url, $key) {
	$url = preg_replace('/(?:&|(\?))' . $key . '=[^&]*(?(1)&|)?/i', "$1", $url);
	return $url;
}

function page_nav($count_rows=0){
	global $query_count, $start, $limit, $db;
	if($count_rows==0) $count_rows=mysqli_num_rows(mysqli_query($db,$query_count));
	if(!isset($start)) $start=0;
	if(!isset($limit)) $limit=999999;
	if($limit>$count_rows) $limit=$count_rows;
	if($limit+$start>$count_rows){
		$limit=$count_rows-$start;
		if($limit<1) $limit=1;
	}
	if($count_rows==0) {$start=0; $limit=0;}
	return 'Total number of data : <b>'.$count_rows.'</b>, Displayed : <b>'.($start+1).'-'.($start+$limit).'</b>';
}

function fileNameGenerator($name){
	$name=html_entity_decode($name); $name=strip_tags($name);
	$from=array('ü','ö','ğ','ı','ə','ç','ş','Ü','Ö','Ğ','I','Ə','Ç','Ş','İ',' ','.',',','~');
	$to=array('u','o','g','i','e','c','s','U','O','G','I','E','C','S','I','-','','','');
	$name=str_replace($from,$to,$name);
	$name = preg_replace("/[^a-zA-Z0-9-]+/", "", $name);
	$name=strtolower($name);
	return $name;
}

function youtube_embed($url){
    /*
    * type1: http://www.youtube.com/watch?v=H1ImndT0fC8
    * type2: http://www.youtube.com/watch?v=4nrxbHyJp9k&feature=related
    * type3: http://youtu.be/H1ImndT0fC8
    */
    $vid_id = "";
    $flag = false;
    if(isset($url) && !empty($url)){
        /*case1 and 2*/
        $parts = explode("?", $url);
        if(isset($parts) && !empty($parts) && is_array($parts) && count($parts)>1){
            $params = explode("&", $parts[1]);
            if(isset($params) && !empty($params) && is_array($params)){
                foreach($params as $param){
                    $kv = explode("=", $param);
                    if(isset($kv) && !empty($kv) && is_array($kv) && count($kv)>1){
                        if($kv[0]=='v'){
                            $vid_id = $kv[1];
                            $flag = true;
                            break;
                        }
                    }
                }
            }
        }
        
        /*case 3*/
        if(!$flag){
            $needle = "youtu.be/";
            $pos = null;
            $pos = strpos($url, $needle);
            if ($pos !== false) {
                $start = $pos + strlen($needle);
                $vid_id = substr($url, $start, 11);
                $flag = true;
            }
        }
    }
    return $vid_id;
}

function safe($value,$strip=true){
    global $db;
	$value=trim($value);
	$value=htmlentities( $value, ENT_QUOTES, 'utf-8' );
	if($strip==true) $value=strip_tags($value);
	$value=mysqli_real_escape_string($db,$value);
	$from=array('\\', "\0", "\n", "\r", "'", '"', "\x1a", "\x00","\x0B"); $to=array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z',"\\0","");
	$value=str_replace($from, $to, $value);
	return $value;
}

function get_fcontent( $url,  $javascript_loop = 0, $timeout = 5 ){
    $url = str_replace( "&amp;", "&", urldecode(trim($url)) );
    $cookie = tempnam ("/tmp", "CURLCOOKIE");
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_ENCODING, "" );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
    $content = curl_exec( $ch );
    $response = curl_getinfo( $ch );
    curl_close ( $ch );
    if ($response['http_code'] == 301 || $response['http_code'] == 302) {
        ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
        if ( $headers = get_headers($response['url']) ) {
            foreach( $headers as $value ) {
                if ( substr( strtolower($value), 0, 9 ) == "location:" )
                    return get_url( trim( substr( $value, 9, strlen($value) ) ) );
            }
        }
    }
    if (    ( preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value) ) && $javascript_loop < 5) {
        return get_url( $value[1], $javascript_loop+1 );
    } else {
        return array( $content, $response );
    }
}

function set_csrf_(){
	$_SESSION["csrf_"]=md5(rand(1,99999));
	return $_SESSION["csrf_"];
}
function update_csrf_(){
	$_SESSION["csrf_"]=md5(rand(1,99999));
}
function get_csrf_(){
	return $_SESSION["csrf_"];
}
function check_csrf_($csrf_){
	if($csrf_==safe($_SESSION["csrf_"])) return true;
	else return false;
}
function get_lang_val($do,$auto_id,$column)
{
	global $main_lang, $db;
	if($auto_id=='') $addQ=''; else $addQ="and auto_id='$auto_id'";
	
	$get_val=mysqli_fetch_assoc(mysqli_query($db,"select * from $do where lang_id='$main_lang' $addQ "));
	return $get_val[$column];
}

function slugGenerator($slug,$space='-',$onlyEnglish=true,$onlySpecialChar=false){;
	$slug=str_replace('&amp;','-',$slug); $slug=str_replace('&','-',$slug);
	$slug=html_entity_decode($slug); $slug=strip_tags($slug);
	$lettersNumbersSpacesHyphens = '/[^\-\s\pN\pL]+/u';
	$spacesDuplicateHypens = '/[\-\s]+/';
	$slug = preg_replace($lettersNumbersSpacesHyphens, '', $slug);
	$slug = preg_replace($spacesDuplicateHypens, $space, $slug);
	$slug = trim($slug, '-');
	if(strlen($slug)>190) $slug=mb_substr($slug,0,190,"UTF-8");
	$slug=mb_strtolower($slug, 'UTF-8');
	if($onlyEnglish==true){
		$from=array('ü','ö','ğ','ı','ə','ç','ş'); $to=array('u','o','g','i','e','c','s');
		$slug=str_replace($from,$to,$slug);
	}
	if($onlySpecialChar==true)
    {
        $from=array('ü','ö','ğ','ı','ç','ş'); $to=array('u','o','g','i','c','s');
        $slug=str_replace($from,$to,$slug);
    }
	$slug=str_replace(' ','',$slug);
	return $slug;
}

function getMonth($month, $lang)
{
    if($lang=='ru')
    {
        switch ($month) {
            case '01':
                $month = 'Январь';
                break;
            case '02':
                $month = 'Февраль';
                break;
            case '03':
                $month = 'Март';
                break;
            case '04':
                $month = 'Апрель';
                break;
            case '05':
                $month = 'Май';
                break;
            case '06':
                $month = 'Июнь';
                break;
            case '07':
                $month = 'Июль';
                break;
            case '08':
                $month = 'Август';
                break;
            case '09':
                $month = 'Сентябрь';
                break;
            case '10':
                $month = 'Октябрь';
                break;
            case '11':
                $month = 'Ноябрь';
                break;
            case '12':
                $month = 'Декабрь';
                break;
        }
    }
    elseif($lang=='en')
    {
        switch ($month) {
            case '01':
                $month = 'January';
                break;
            case '02':
                $month = 'February';
                break;
            case '03':
                $month = 'March';
                break;
            case '04':
                $month = 'April';
                break;
            case '05':
                $month = 'May';
                break;
            case '06':
                $month = 'June';
                break;
            case '07':
                $month = 'July';
                break;
            case '08':
                $month = 'August';
                break;
            case '09':
                $month = 'September';
                break;
            case '10':
                $month = 'October';
                break;
            case '11':
                $month = 'November';
                break;
            case '12':
                $month = 'December';
                break;
        }
    }
    elseif($lang=='tr')
    {
        switch ($month) {
            case '01':
                $month = 'Ocak';
                break;
            case '02':
                $month = 'Şubat';
                break;
            case '03':
                $month = 'Mart';
                break;
            case '04':
                $month = 'Nisan';
                break;
            case '05':
                $month = 'Mayıs';
                break;
            case '06':
                $month = 'Haziran';
                break;
            case '07':
                $month = 'Temmuz';
                break;
            case '08':
                $month = 'Ağustos';
                break;
            case '09':
                $month = 'Eylül';
                break;
            case '10':
                $month = 'Ekim';
                break;
            case '11':
                $month = 'Kasım';
                break;
            case '12':
                $month = 'Aralık';
                break;
        }
    }
    else
    {
        switch ($month) {
            case '01':
                $month = 'Yanvar';
                break;
            case '02':
                $month = 'Fevral';
                break;
            case '03':
                $month = 'Mart';
                break;
            case '04':
                $month = 'Aprel';
                break;
            case '05':
                $month = 'May';
                break;
            case '06':
                $month = 'Iyun';
                break;
            case '07':
                $month = 'Iyul';
                break;
            case '08':
                $month = 'Avqust';
                break;
            case '09':
                $month = 'Sentyabr';
                break;
            case '10':
                $month = 'Oktyabr';
                break;
            case '11':
                $month = 'Noyabr';
                break;
            case '12':
                $month = 'Dekabr';
                break;
        }
    }

    return $month;
}

function sendMail($to,$from,$subject,$html='',$text='')
{
    $url = 'https://api.sendgrid.com/';
    $user = 'Dhm.az';
    $pass = '159357dhm!)(';

    $params = array(
        'api_user' => $user,
        'api_key' => $pass,
        'to' => $to,
        'subject' => $subject,
        'html' => $html,
        'text' => $text,
        'from' => $from
    );


    $request = $url . 'api/mail.send.json';

// Generate curl request
    $session = curl_init($request);
// Tell curl to use HTTP POST
    curl_setopt($session, CURLOPT_POST, true);
// Tell curl that this is the body of the POST
    curl_setopt($session, CURLOPT_POSTFIELDS, $params);
// Tell curl not to return headers, but do return the response
    curl_setopt($session, CURLOPT_HEADER, false);
// Tell PHP not to use SSLv3 (instead opting for TLS)
    curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

// obtain response
    $response = curl_exec($session);
    curl_close($session);

    if(strpos($response,"success")>0)
        return true;
    else
        return false;
}

function checkMaxSizeImage($uploaded_image,$maxWidth=1000,$maxHeight=800){
    list($width, $height, $type, $attr) = getimagesize($uploaded_image);
    $tip=explode(".",$uploaded_image);  $tip=end($tip); $tip=strtolower($tip);
    $image = new SimpleImage();
    if($width>$maxWidth){
        if($tip=='png') resizePng($uploaded_image,$uploaded_image,$maxWidth,0);
        else{
            $image->load($uploaded_image);
            $image->resizeToWidth($maxWidth);
            $image->save($uploaded_image);
        }
        list($width, $height, $type, $attr) = getimagesize($uploaded_image);
    }
    if($height>$maxHeight){
        if($tip=='png') resizePng($uploaded_image,$uploaded_image,0,$maxHeight);
        else{
            $image->load($uploaded_image);
            $image->resizeToHeight($maxHeight);
            $image->save($uploaded_image);
        }
    }
}
function makeThumb($uploaded_image, $output, $thumbWidth=0, $thumbHeight=0, $crop=true, $smallToBig=true){
    list($width, $height, $type, $attr) = getimagesize($uploaded_image);
    $tip=explode(".",$uploaded_image);  $tip=end($tip); $tip=strtolower($tip);
    if( ($smallToBig==false && $width>$thumbWidth && $height>$thumbHeight) or $smallToBig==true){
        $image = new SimpleImage();
        if($thumbWidth==0 || $thumbHeight==0){
            $image->load($uploaded_image);
            if($thumbWidth==0) $image->resizeToHeight($thumbHeight); else $image->resizeToWidth($thumbWidth);
            $image->save($output);
        }
        else{
            if($width>$height){
                if($crop==true){
                    if($tip=='png') resizePng($uploaded_image,$output,0,$thumbHeight);
                    else{
                        $image->load($uploaded_image);
                        $image->resizeToHeight($thumbHeight);
                        $image->save($output);
                    }
                    $uploaded_image=$output;
                    list($width, $height, $type, $attr) = getimagesize($uploaded_image);
                    if($thumbWidth>0 && $thumbHeight>0 && ($width!=$thumbWidth || $height!=$thumbHeight) ){
                        create_thumbnail($uploaded_image,$output,$thumbWidth,$thumbHeight);
                    }
                }
                else{
                    if($tip=='png') resizePng($uploaded_image,$output,$thumbWidth,0);
                    else{
                        $image->load($uploaded_image);
                        $image->resizeToWidth($thumbWidth);
                        $image->save($output);
                    }
                }
            }
            else{
                if($crop==true){
                    if($tip=='png') resizePng($uploaded_image,$output,$thumbWidth,0);
                    else{
                        $image->load($uploaded_image);
                        $image->resizeToWidth($thumbWidth);
                        $image->save($output);
                    }
                    $uploaded_image=$output;
                    list($width, $height, $type, $attr) = getimagesize($uploaded_image);
                    if($thumbWidth>0 && $thumbHeight>0 && ($width!=$thumbWidth || $height!=$thumbHeight) ){
                        create_thumbnail($uploaded_image,$output,$thumbWidth,$thumbHeight);
                    }
                }
                else{
                    if($tip=='png') resizePng($uploaded_image,$output,0,$thumbHeight);
                    else{
                        $image->load($uploaded_image);
                        $image->resizeToHeight($thumbHeight);
                        $image->save($output);
                    }
                }
            }
        }
    }
}

function resizePng($uploaded_sekil, $output, $width=0, $height=0) {
    list( $uploadWidth, $uploadHeight, $uploadType ) = getimagesize( $uploaded_sekil );

    if($width>0 && $height==0) $height=$uploadHeight/($uploadWidth/$width);
    elseif($width==0 && $height>0) $width=$uploadWidth/($uploadHeight/$height);

//    if($width>0 && $uploadWidth>$width && $height>0 && $uploadHeight>$height){
        $srcImage = imagecreatefrompng( $uploaded_sekil );
        $targetImage = imagecreatetruecolor( $width, $height );
        imagealphablending( $targetImage, false );
        imagesavealpha( $targetImage, true );
        imagecopyresampled( $targetImage, $srcImage, 0, 0, 0, 0, $width, $height, $uploadWidth, $uploadHeight );
        imagepng(  $targetImage, $output, 9 );
//    }
}

function more($str,$limit) {

    $strArr=explode(' ',$str);

    $i=0;
    $resultStr='';

    if (count($strArr) > $limit)

        foreach ($strArr as $val)
        {
            $resultStr.=$val.' ';

            $i++;

            if ($i==$limit)
            {
                $resultStr.='...';
                break;
            }

        }

    else  $resultStr=implode(' ',$strArr);

    return $resultStr;


}

function more_string($str,$limit) {

    $strlen = strlen($str);

    $resultStr='';

    if ($strlen > $limit)
    {
        $substr = mb_substr($str,0,$limit,'UTF-8');

        $resultStr.=$substr.' ...';
    }

    else  $resultStr=$str;

    return $resultStr;


}

function realUrl()
{
    return $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function rmdir_recursive($dir)
{
    foreach(scandir($dir) as $file)
    {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
        else @unlink("$dir/$file");
    }
    rmdir($dir);
}

function rrmdir($dir)
{
    if (is_dir($dir))
    {
        $objects = scandir($dir);
        foreach ($objects as $object)
        {
            if ($object != "." && $object != "..")
            {
                if (filetype($dir."/".$object) == "dir")
                    rrmdir($dir."/".$object);
                else unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

function compress($source, $destination, $quality)
{

    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source);

    imagejpeg($image, $destination, $quality);

    return $destination;
}


?>
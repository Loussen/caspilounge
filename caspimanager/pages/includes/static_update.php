<?php
$valyuta_active=0;
$namaz_active=0;
$hava_active=0;

//Valyuta update
if(mysqli_num_rows(mysqli_query($db,"select id from valyuta where tarix='$this_day' "))==0 && $valyuta_active==1){
	$all_valyuta = simplexml_load_file("http://cbar.az/currencies/" .date("d.m.Y").".xml");
	foreach($all_valyuta->ValType as $a){
		foreach($a->Valute as $b){
			if(strtoupper($b["Code"])=='USD') $usd=$b->Value;
			if(strtoupper($b["Code"])=='EUR') $eur=$b->Value;
			if(strtoupper($b["Code"])=='RUB') $rub=$b->Value;
		}
	}
	if($usd>0) mysqli_query($db,"update valyuta set usd='$usd', eur='$eur', rub='$rub', tarix='$this_day' ");
//
}
elseif(mysqli_num_rows(mysqli_query($db,"select id from namaz_vaxti where tarix='$this_day' "))==0 && $namaz_active==1)	//Namaz update
{
$all_date=file_get_contents('http://www.islamicfinder.org/prayer_service.php?country=azerbaijan&city=baku&state=&zipcode=&latitude=40.3953&longitude=49.8822&timezone=4&HanfiShafi=1&pmethod=1&fajrTwilight1=10&fajrTwilight2=10&ishaTwilight=&ishaInterval=30&dhuhrInterval=1&maghribInterval=1&dayLight=1&page_background=FFFFFF&table_background=FFFFFF&table_lines=FFFFFF&text_color=000000&link_color=000000&prayerFajr=Facr&prayerSunrise=Subh&prayerDhuhr=Zohr&prayerAsr=Asr&prayerMaghrib=Magrib&prayerIsha=Isha&lang=');
$all_date=strstr($all_date,'Facr'); $all_date=strstr($all_date,'000000'); $subh=trim(substr($all_date,8)); $end=strpos($all_date,"<"); $subh=substr($subh,0,$end);
$all_date=strstr($all_date,'Subh'); $all_date=strstr($all_date,'000000'); $gun=trim(substr($all_date,8)); $end=strpos($all_date,"<"); $gun=substr($gun,0,$end);
$all_date=strstr($all_date,'Zohr'); $all_date=strstr($all_date,'000000'); $zohr=trim(substr($all_date,8)); $end=strpos($all_date,"<"); $zohr=substr($zohr,0,$end);
$all_date=strstr($all_date,'Asr'); $all_date=strstr($all_date,'000000'); $esr=trim(substr($all_date,8)); $end=strpos($all_date,"<"); $esr=substr($esr,0,$end);
$all_date=strstr($all_date,'Magrib'); $all_date=strstr($all_date,'000000'); $megrib=trim(substr($all_date,8)); $end=strpos($all_date,"<"); $megrib=substr($megrib,0,$end);
$all_date=strstr($all_date,'Isha'); $all_date=strstr($all_date,'000000'); $isha=trim(substr($all_date,8)); $end=strpos($all_date,"<"); $isha=substr($isha,0,$end);
$neyi=array('</font></','</font><'); $neye=array('','');
$subh=str_replace($neyi,$neye,$subh);
$gun=str_replace($neyi,$neye,$gun);
$zohr=str_replace($neyi,$neye,$zohr);
$esr=str_replace($neyi,$neye,$esr);
$megrib=str_replace($neyi,$neye,$megrib);
$isha=str_replace($neyi,$neye,$isha);
mysqli_query($db,"update namaz_vaxti set subh='$subh', gun='$gun', zohr='$zohr', esr='$esr', megrib='$megrib', isha='$isha', tarix='$this_day' ");
//
}
//elseif(mysqli_num_rows(mysqli_query($db,"select id from hava where tarix='$this_day' "))==0 && $hava_active==1)	//Hava update
//{
//$query_update="update hava set ";
//$regions=array(
//	'baki'=>'27103',
//	'sumqayit'=>'26921',
//	'gence'=>'28166',
//	'lenkeran'=>'29090',
//	'quba'=>'30500',
//	'seki'=>'27610',
//	'sirvan'=>'30724',
//	'mingecevir'=>'29699',
//	'naxcivan'=>'30012',
//	'susa'=>'30815',
//
//);
//foreach($regions as $key=>$val){
//	$datas=get_fcontent("http://www.accuweather.com/az/az/baku/".$val."/daily-weather-forecast/".$val);
//	$all_hava=$datas[0]; $this_hava=strstr($all_hava,'<span class="temp">');
//	$gunduz=intval(substr($this_hava,19,5)); $this_hava=substr($this_hava,19); $this_hava=strstr($this_hava,'<span class="temp">');
//	$gece=intval(substr($this_hava,19,5)); $this_hava=$gunduz."```".$gece;
//	$query_update.="$key='$this_hava', ";
//}
//// Baki
//
//$query_update.=" tarix='$this_day' ";
//mysqli_query($db,$query_update);
//}
?>
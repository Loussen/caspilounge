<?php
$neyi=array(
'[b]','[/b]',
'[u]','[/u]',
'[i]','[/i]',
'[red]','[/red]',
'[blue]','[/blue]',
'[green]','[/green]',
'[yellow]','[/yellow]',
'[purple]','[/purple]',
'[silver]','[/silver]',
'[lime]','[/lime]',
'[black]','[/black]',
'[pink]','[/pink]'
);
$neye=array(
'<b>','</b>',
'<u>','</u>',
'<i>','</i>',
'<font style="color:red">','</font>',
'<font style="color:blue">','</font>',
'<font style="color:green">','</font>',
'<font style="color:yellow">','</font>',
'<font style="color:purple">','</font>',
'<font style="color:silver">','</font>',
'<font style="color:lightgreen">','</font>',
'<font style="color:black">','</font>',
'<font style="color:pink">','</font>'
);
for($i=0;$i<=count($neyi)-1;$i++){
	$j=$i+1;
	if(is_numeric(strpos($mektub,$neyi[$i])) && is_numeric(strpos($mektub,$neyi[$j])) && $i%2==0 ){
		$mektub=str_replace($neyi[$i],$neye[$i],$mektub);
		$mektub=str_replace($neyi[$j],$neye[$j],$mektub);
	}
}
$mektub=str_replace("
","<br/>",$mektub);
?>
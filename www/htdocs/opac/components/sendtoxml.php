<?php
$mostrar_menu="N";
include("../../central/config_opac.php");

//foreach ($_REQUEST as $key=>$value) echo "$key=$value<br>";die;


$desde=1;
$count="";

include $Web_Dir.'functions.php';

//if (isset($_REQUEST["sendto"]) and trim($_REQUEST["sendto"])!="")
	//$_REQUEST["cookie"]=$_REQUEST["sendto"] ;
$list=explode("|",$_REQUEST["cookie"]);
$seleccion=array();
$primeravez="S";



$filename="abcdOpac.xml";
header('Content-Type: text/xml; charset=".$meta_encoding."');
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=$filename");
$ix=0;
$contador=0;
$control_entrada=0;
foreach ($list as $value){
	$value=trim($value);
	if ($value!="")	{
		$x=explode('_=',$value);
		$seleccion[$x[1]][]=$x[2];
	}
}

$xml_head="Y";
$lista_mfn="";

if ($xml_head=="Y"){
	echo "<?xml version=\"1.0\"?> \n";
	$xml_head="N";
}

foreach ($seleccion as $base=>$value){
	$lists_mfn="";
	foreach ($value as $mfn){
		if ($lista_mfn=="")
			$lista_mfn="'$mfn'";
		else
			$lista_mfn.="/,'$mfn'";
	}
	if (file_exists($db_path.$base."/pfts/marcxml.pft")){
		$Formato='@'.$db_path.$base."/pfts/marcxml.pft";
		$encabezado='<marc:collection xmlns:marc="http://www.loc.gov/MARC21/slim" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd">';
		$pie='</marc:collection>'."\n";
	}else{
		if (file_exists($db_path.$base."/pfts/dcxml.pft")){
			$Formato='@'.$db_path.$base."/pfts/dcxml.pft";
			$encabezado="<collection>\n";
			$pie="</collection>\n";
		}else{

	}
}
$query = "&base=".$base."&cipar=$db_path"."par/$base".".par&Mfn=$lista_mfn&Formato=$Formato&lang=".$_REQUEST["lang"];
//echo $query;die;
$resultado=wxisLlamar($base,$query,$xWxis."opac/imprime_sel.xis");
	//echo '<marc:collection xmlns:marc="http://www.loc.gov/MARC21/slim" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd">'."\n";
	//echo "<!DOCTYPE dublinCore PUBLIC '-//OCLC//DTD Dublin core v.1//EN'> \n";

echo $encabezado;
foreach($resultado as $value)  {
	$value=trim($value);
	if (substr($value,0,8)=="[TOTAL:]") continue;
	$value=mb_convert_encoding($value,'UTF-8', 'ISO-8859-1');
	echo str_replace('&','&amp;',$value);
}
echo $pie;
}

?>



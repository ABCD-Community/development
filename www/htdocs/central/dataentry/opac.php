<?php
include("../config.php");
function wxisLlamar($base,$query,$IsisScript){
global $db_path,$Wxis,$xWxis;

	include("../common/wxis_llamar.php");
	return $contenido;
}

function LeerRegistro($Expresion,$base){
	global $db_path,$xWxis;
	$salida="";
	if(!isset($_REQUEST["from"])) $_REQUEST["from"]="";
	if(!isset($_REQUEST["count"])) $_REQUEST["count"]="";
	if (trim($Expresion)!=""){
		$query = "&base=$base&cipar=$db_path"."par/$base.par&Opcion=buscar&Expresion=".$Expresion."&count=".$_REQUEST["count"]."&Mfn=".$_REQUEST["from"];
		if (isset($_REQUEST["formato"]))
			$query.="&Formato=".$_REQUEST["formato"];
		else
			$query.="&Pft=ALL";
		$resultado=wxisLlamar($base,$query,$xWxis."buscar.xis");
		foreach ($resultado as $value) {
			$value=trim($value);
			if (substr($value,0,7)=='[TOTAL:'){
				continue;
			}else{
		       $salida.=$value;
			}
		}
	}
	return $salida;
}
if (isset($_REQUEST["base"])){
	$fp=file($db_path."opac.dat");
			echo LeerRegistro($_REQUEST["expresion"],$base[0]);
}

?>

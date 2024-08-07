<?php
/**
 * @program:   ABCD - ABCD-Central - https://abcd-community.org/
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      captura_claves.php
 * @desc:      Get a keyword from an authority list
 * @author:    Guilda Ascencio
 * @since:     20091203
 * @version:   1.0
 *
 * == BEGIN LICENSE ==
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Lesser General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * == END LICENSE ==
*/
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../common/get_post.php");
include ("../config.php");
$lang=$_SESSION["lang"];
include("../lang/admin.php");
// ==================================================================================================
// INICIO DEL PROGRAMA
// ==================================================================================================



//foreach ($arrHttp as $var => $value )	echo "$var = $value<br>";
//die;
if (!isset($arrHttp["subc"])) $arrHttp["subc"]="";
if (!isset($arrHttp["pref"])) $arrHttp["pref"]=$arrHttp["prefijo"];
if (!isset($arrHttp["tagfst"])) $arrHttp["tagfst"]="";
if (!isset($arrHttp["delimitador"])) $arrHttp["delimitador"]="";
if (!isset($arrHttp["separa"])) $arrHttp["separa"]="";
if (!isset($arrHttp["postings"])) $arrHttp["postings"]="ALL";
if (!isset($arrHttp["Tag"]))$arrHttp["Tag"]="";
if (!isset($arrHttp["Repetible"]))$arrHttp["Repetible"]="";
if ($arrHttp["Formato"]=="ifp"){
	$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["base"].".par&Opcion=diccionario"."&tagfst=".substr($arrHttp["tagfst"],3)."&prefijo=".strtoupper($arrHttp["prefijo"])."&pref=".strtoupper($arrHttp["pref"]);
}else{
	$arrHttp["Formato"]=stripslashes($arrHttp["Formato"]);
	if (substr($arrHttp["Formato"],0,1)=="@"){
		$Formato=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/".substr($arrHttp["Formato"],1);
		if (!file_exists($Formato)) $Formato=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/".substr($arrHttp["Formato"],1);
		$Formato="@".$Formato;
	}else{
		$Formato=$arrHttp["Formato"];
	}
	$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["base"].".par&Opcion=autoridades"."&tagfst=".substr($arrHttp["tagfst"],3)."&prefijo=".strtoupper($arrHttp["prefijo"])."&pref=".strtoupper($arrHttp["pref"])."&postings=".$arrHttp["postings"]."&formato_e=".$Formato;
}
//echo $Formato;die;
$IsisScript=$xWxis."ifp.xis";
include("../common/wxis_llamar.php");
$contenido = array_unique ($contenido);
include("../common/header_display.php");
echo "		<Title>Lista de t�rminos</title>
		<script languaje=Javascript>\n";
echo "subC=\"".$arrHttp["subc"]."\"\n";
echo "Repetible=\""; if (!isset($arrHttp["repetible"])) echo "0"; else echo $arrHttp["repetible"]; echo "\"\n";
echo "Tag=\"".$arrHttp["Tag"]."\"\n";
echo "Delimitador=\"".$arrHttp["delimitador"]."\"\n";
echo "Prefijo=\"".$arrHttp["prefijo"]."\"\n";
echo "Separa=\"".$arrHttp["separa"]."\"\n";
if (isset($arrHttp["indice"]))
	echo "Indice=\"".$arrHttp["indice"]."\"\n";
else
	echo "Indice='N'\n";
?>

	function ObtenerTerminos(){
		Seleccion=""
		icuenta=0
		term=document.Lista.autoridades.options[document.Lista.autoridades.selectedIndex].value
		if (Repetible==0){
        	window.opener.Ctrl_activo.value=term
        }else{
        	window.opener.Ctrl_activo.value=window.opener.Ctrl_activo.value+term+"\n"
        }
        //window.opener.Ctrl_activo.focus()
        if (Repetible==0)self.close()
	}

	function Continuar(){
		i=document.Lista.autoridades.length-1
		a=document.Lista.autoridades[i].text
		AbrirIndice(a)
	}

	function IrA(ixj){
		a=document.Lista.ira.value
    	AbrirIndice(a)
	}

<?php
echo "function AbrirIndice(Termino){
    	db='".$arrHttp["base"]."'
    	cipar='".$arrHttp["cipar"]."'
    	Separa='".$arrHttp["separa"]."'
		Tag='".$arrHttp["Tag"]."'
		Prefijo='".$arrHttp["pref"]."'+Termino
		Pref='".$arrHttp["pref"]."'
		Subc='". $arrHttp["subc"]."'
		lang='".$arrHttp["lang"]."'
		Postings='".$arrHttp["postings"]."'
		repetible='".$arrHttp["repetible"]."'
		Formato='".urlencode($arrHttp["Formato"])."'
		url='capturaclaves.php?Opcion=autoridades&base='+db+'&cipar='+cipar+'&Tag='+Tag+'&pref='+Pref+'&prefijo='+Prefijo+'&separa='+Separa+'&subc='+Subc+'&postings='+Postings+'&lang='+lang+'&repetible='+repetible+'&Formato='+Formato
		self.location.href=url
	}

</script>\n";

if (isset($arrHttp["width"])){
	$width=$arrHttp["width"];
}else{
	$width="600px";
}
?>
	</head>
	<body LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" >

<p class="p-3"><?php echo $msgstr["ayudaterminos"]?></p>

	<form method=post name=Lista onSubmit="javascript:return false">

	<table cellpadding=0 cellspacing=0 border=0 width=90%>
	<tr>
		<td width="5%" align="center" >
			<?php for ($i=65;$i<91;$i++ ){?>
				<a class="bt-sm" href="javascript:AbrirIndice('<?php echo chr($i)?>')"><?php echo chr($i)?></a><br>
			<?php } ?>
		</td>

		<td width="95%" valign="top">
			<Select name="autoridades" multiple size="15" style="width:<?php echo $width;?>;" onclick="javascript:ObtenerTerminos()">
<?php

	foreach ($contenido as $linea){
		if (trim($linea)!=""){
	       	if ($arrHttp["Formato"]=="ifp"){
	       		if (substr($linea,0,strtoupper(strlen($arrHttp["pref"])))!=strtoupper($arrHttp["pref"])){
	       			break;
	       		}else{
	       			$p=explode("|",$linea);
	       			echo "<option value=\"";
	       			$l=strlen($arrHttp["pref"]);
	       			$ter=substr($p[0],$l);
					echo $ter;
					echo "\">";
			        echo $ter;
	       		}
	       	}else{
				$f=explode('$$$',$linea);
				if (!isset($f[1])) $f[1]=$f[0];
				if (trim($f[1])!=""){
					echo "<option value=\"";
					echo trim($f[1]);
					echo "\">";
			        if (substr($f[0],0,1)=="^") $f[0]=substr($f[0],2);
			        echo $f[0];
			    }
	       }
	    }
	}

?>
	</select></td>

	</table>

		<table cellpadding="0" cellspacing="0" border="0" width="100%" >
			<tr>
			<td colspan=2><!--full width required to avoid wrap in all languages-->
				<a href="javascript:AbrirIndice(' ')" class="bt bt-gray" title='<?php echo $msgstr["src_top"]?>'>
					<i class="fas fa-chevron-circle-up" title="<?php echo $msgstr["continuar"]?>"></i> 
				</a>
				<a href="javascript:Continuar()" class="bt bt-gray" title='<?php echo $msgstr["masterms"]?>'>
					<i class="fas fa-chevron-circle-down"></i>
				</a>
				&nbsp;
				<?php echo $msgstr["avanzara"]?>
				<input type=text name=ira size=5 value="" onKeyPress="codes(event)" title="<?php echo $msgstr["src_advance"];?>">
				<a href=Javascript:IrA() class="bt bt-gray" title='<?php echo $msgstr["src_enter"]?>'>
					<i class="fas fa-angle-right"></i></a>
			</td>
			</tr>
		</table>

	</form>
</body>
</html>

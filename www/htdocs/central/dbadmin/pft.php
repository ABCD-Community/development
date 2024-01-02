<?php
/* Modifications
2021-03-03 fho4abcd Replaced helper code fragment by included file
2021-06-14 fho4abcd remove password+lineends
2021-08-30 rogercg	Small adjustments+ point to community
2021-09-06 rogercg	New look - p1
2022-01-20 fho4abcd new look buttons+Remove some nested tables+cleanup html+repair bugs
2022-01-25 fho4abcd more new look buttons, shift Generate output to the bottom, improve generate output layout
2022-01-26 fho4abcd Open preview in larger window and after all checks passed.
2022-01-29 fho4abcd Improve setting of encabezado+create language folder if it does not exist
20220227 fho4abcd Always show backbutton. Other back if institutional info not shown
20220918 fho4abcd Explode base before config.php (to get correct value for $actparfolder)
20221102 fho4abcd Cancel button acts now the same as the back button
20240102 fho4abcd Cleanup,better messages,correct handle output type, improved Edit, remove Expresion parameter, add Print option
*/
/*
**Parameters:
	&base	database indicator (<basename>[|<user>]) (required)
	&Opcion	The function that this module is supposed to serve
			- "new"		Create an initial pft during creation of a new database. No report run
			- "edit"	Edit the pft given by parameter "fgen"+ run report
			- "update"	Legacy. Equal to default
			- default	Run report. Edit/create new can be selected in the code
	&fgen	Indication of the pft to be used. Accepted string values
			- <pftname>[|<syntax acronym>[|<separator acronym>]]
				<pftname>: Filename of the pft. No fullpath, no extension
				<syntax acronym>: The intended output format (PL/CD/...)
				<separator acronym>:	The separator in column formats
				default	Empty and omitted values result in default Opcion
	&encabezado	Processed as usual
	&retorno	Indicator of the caller (==backtoscript)
	&Modulo		Used for backtoscript by called scripts
	&seleccionades	Preselected record numbers (used?format?)
*/
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
//echo "<br>";
//foreach ($_SESSION as $var=>$value) echo "$var=$value<br>";
$basearr=explode('|',$arrHttp["base"]);
$base=$basearr[0];
$arrHttp["base"]=$base;//required by includes
$cipar=$base.".par";
$Opcion="";
if (isset($arrHttp["Opcion"])) $Opcion=$arrHttp["Opcion"];
$description="";
if (isset($arrHttp["descripcion"])) $description=$arrHttp["descripcion"];
$pft_name="";
$tipoacro="";
$separacro="VBAR";// was hard coded in previous versions
if (isset($arrHttp["fgen"]) && $arrHttp["fgen"]!=""){
	$fgenar=explode('|',$arrHttp["fgen"]);
	if ($fgenar[0]!="") 							$pft_name=Trim($fgenar[0]);
	if (isset($fgenar[1])&& Trim($fgenar[1]!=""))	$description=Trim($fgenar[1]);
	if (isset($fgenar[2])&& Trim($fgenar[2]!=""))	$tipoacro=Trim($fgenar[2]);
	if (isset($fgenar[3])&& Trim($fgenar[3]!=""))	$separacro=Trim($fgenar[3]);
}
if( $tipoacro=="") $tipoacro="T";// most probable default: for default pft file to view records
$pft_content="";
$pft_h_content="";

include ("../config.php");
if (isset($_SESSION["UNICODE"])) {
	IF ($_SESSION["UNICODE"]==1)
		$meta_encoding="UTF-8";
	else
		$meta_encoding="ISO-8859-1";
}
include ("../lang/dbadmin.php");
include ("../lang/admin.php");
include ("inc_pft_files.php");

// ==================================================================================================
// INICIO DEL PROGRAMA
// ==================================================================================================
//
$Expresion="";
if (isset($arrHttp["encabezado"])) $encabezado="s";
else $encabezado="";

include("../common/header.php");
?>
<body>
<script language="JavaScript" type="text/javascript" src="../dataentry/js/lr_trim.js"></script>
<script language="Javascript" src=../dataentry/js/selectbox.js></script>
<style type=text/css>
div#useexformat{
	display: block;
	margin: 0px 20px 0px 20px;
	padding: 15px 0 15px 15px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
div#createformat{
	<?php if ($Opcion!="new" && $Opcion!="edit") echo "display: none;\n"?>
	margin: 0px 20px 0px 20px;
	padding: 15px 0 15px 15px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
div#pftedit{
	display: none;
	margin: 0px 20px 0px 20px;
	padding: 15px 0 15px 15px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
div#generateoutput{
	display: none;
	margin: 0px 20px 0px 20px;
	padding: 15px 0 15px 15px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
div#saveformat{
	<?php if ($Opcion!="new") echo "display: none;\n"?>
	margin: 0px 20px 0px 20px;
	padding: 15px 0 15px 15px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
</style>
<script language=javascript>

TipoFormato=""

function Buscar(){
	base='<?php echo $base?>'
	cipar='<?php echo $cipar?>'
	Url="../dataentry/buscar.php?Opcion=formab&prologo=prologoact&Target=s&Tabla=imprimir&base="+base+"&cipar="+cipar
  	msgwin=window.open(Url,"Buscar","menu=no, resizable,scrollbars,width=1000,height=500")
	msgwin.focus()
}
//IF THE TYPE OF OUTPUT IS NOT IN COLUMN, HEADINGS ARE NOT ALLOWED
function CheckType(){
	var trimtext=document.forma1.headings.value.trim();
	document.forma1.headings.value=trimtext
	if ((document.forma1.tipof[0].checked ||
		 document.forma1.tipof[1].checked ||
		 document.forma1.tipof[2].checked) && (trimtext)){
		alert("<?php echo $msgstr["r_noheading"]?>")
		document.forma1.headings.focus();
		return false;
	}
	return true;
}

function ClearFormat(){
	document.forma1.headings.value=""
	document.forma1.pft.value=""
	moveAllOptions(document.forms[0]['list21'],document.forms[0]['list11'],false)
	for (i=0;i<document.forma1.tipof.length;i++){
		document.forma1.tipof[i].checked=false
	}
	for (i=0;i<document.forma1.separ.length;i++){
		document.forma1.tipof[i].checked=false
	}
	document.forma1.separ[0].checked=true // The separator default in history
}

function ClearRange(){
	document.forma1.Mfn.value=''
	document.forma1.to.value=''
}

function CopiarExpresion(){
	Expr=document.forma1.Expr.options[document.forma1.Expr.selectedIndex].value
	document.forma1.Expresion.value=Expr
}

function CopySortKey(){
	Sort=document.forma1.sort.options[document.forma1.sort.selectedIndex].value
	document.forma1.sortkey.value=Sort
}

function CreateSortKey(){
	msgwin=window.open("","sortkey","resizable,scrollbars, width=700,height=600")
	document.sortkey.submit()
	msgwin.focus()
}

function DeleteExpression(){
	document.forma1.Expresion.value=''
}

function DeleteFormat(){
	if (document.forma1.fgen.selectedIndex==0 || document.forma1.fgen.selectedIndex==-1){
		alert("<?php echo $msgstr["selpftdel"]?>")
		return
	}
	ix=document.forma1.fgen.selectedIndex
	if (confirm("delete "+document.forma1.fgen.options[ix].text+"?")){
		file=document.forma1.fgen.options[ix].value +'|'
		f=file.split('|')
    	document.frmdelete.pft.value=f[0]
    	document.frmdelete.submit()
    }
}

function DeleteSelected(){
	document.forma1.seleccionados.value=''
}
function DeleteSrchExpr(){
	if (document.forma1.Expr.selectedIndex==0 || document.forma1.Expr.selectedIndex==-1){
		alert("<?php echo $msgstr["pftselsrchexprdel"]?>")
		return
	}
	ix=document.forma1.Expr.selectedIndex
	srchdesc=document.forma1.Expr.options[ix].text;
	if (confirm("<?php echo $msgstr["pftdelsrchexpr"]?>"+": "+srchdesc+"?")){
		document.forma1.Expresion.value=''
		var winl = (screen.width-300)/2;
		var wint = (screen.height-200)/2;
		var select = document.getElementById('Expr')
		select.removeChild(select.options[ix])
		msgwin=window.open("","removesearch","menu=no,status=yes,width=300, height=200,left="+winl+",top="+wint)
		msgwin.focus()
		document.removesearch.Descripcion.value=srchdesc;
		document.removesearch.submit()
    }
}

function GenerarFormato(){
    if (document.forma1.list21.options.length==0){
    	alert("<?php echo $msgstr["selfieldsfmt"]?>")
    	return
    }
    <?php if ($Opcion!="new")
		echo "document.forma1.fgen.selectedIndex=-1
		";
	?>
	formato=""	// Format content initially empty
	head=""    	// Column heading initally empty
	separstr=""	// Initial column separator
   	for (i=0;i<document.forma1.separ.length;i++){
   		if (document.forma1.separ[i].checked){
			separstr = document.forma1.separ[i].value;
		}
   	}
	Tipo=""		// Initial content template empty
   	for (i=0;i<document.forma1.tipof.length;i++){
   		if (document.forma1.tipof[i].checked)
   			Tipo=document.forma1.tipof[i].value
   	}
    switch (Tipo){
    	case "T":             //TABLE
    		formato="'<table border=0 width=90%>'\n"
    		for (i=0;i<document.forma1.list21.options.length;i++){
			    campo=document.forma1.list21.options[i].value
			    t=campo.split('|')
				xTag=t[1]
				xTipoE=t[0]
				if (xTag=="" || xTipoE=="L"){
					campo="'<tr><td colspan=2 valign=top><font face=arial size=2><b>"+t[2]+"</b></font></td>'/\n"
		    	}else {
		    		if(Trim(t[5])!=""){
						tag=SubFields(xTag,t[5],t[6])
					}else{
						tag="v"+xTag+"+|<br>|"
					}
			    	campo="if p(v"+xTag+ ") then '<tr><td width=20% valign=top><font face=arial size=2><b>"+t[2]+"</b></font></td><td valign=top><font face=arial size=2>'"+tag+",'</td>' fi/\n"
				}
				formato+=campo
			}
			formato+="'</table><p>'";
    		break
    	case "PL":
    	case "P":
	    	for (i=0;i<document.forma1.list21.options.length;i++){
		    	campo=document.forma1.list21.options[i].value
		    	t=campo.split('|')
				xTag=t[1]
				xTipoE=t[0]
				if (xTag=="" || xTipoE=="L"){
					campo="'<br><b>"+t[2]+"</b></td>'/\n"
		    	}else {
		    		if (Tipo=='PL')
		    			label_f=  "<font face=arial size=2><b>"+t[2]+"</b></font>: "
		 			else
		 				label_f=""
					if(Trim(t[5])!=""){
						tag=SubFields(xTag,t[5],t[6])
					}else{
						tag="v"+xTag+"+|; |"
					}

		    		campo="if p(v"+xTag+ ") then '<br>"+label_f+"<font face=arial size=2>'"+tag+"'</font>'"+", fi/\n"
				}
				formato+=campo
			}
			formato+="'<P>'/\n"
    		break
    	case "CT":
    		formato+="'<tr>',"
    		for (i=0;i<document.forma1.list21.options.length;i++){
			    campo=document.forma1.list21.options[i].value
			    t=campo.split('|')
				xTag=t[1]
				xTipoE=t[0]
		  		if (xTag!=""){
		  			res=""
					if(Trim(t[5])!=""){
						tag=SubFields(xTag,t[5],t[6])
					}else{
						tag="v"+xTag+"+|; |"
					}
			    	campo="'<td valign=top><font face=arial size=2>'"+tag+" if a(v"+xTag+") then '&nbsp;' fi,'</font></td>'/\n"
			    	formato+=campo
			    	head+=t[2]+"\n"
				}
			}
    		break;
    	case "CD":
    		for (i=0;i<document.forma1.list21.options.length;i++){
			    campo=document.forma1.list21.options[i].value
			    t=campo.split('|')
				xTag=t[1]
				xTipoE=t[0]
		  		if (xTag!=""){
					if(Trim(t[5])!=""){
						tag=SubFields(xTag,t[5],t[6])
					}else{
						tag="v"+xTag
					}
			    	campo=tag+",'"+separstr+"',\n"
			    	formato+=campo
			    	head+=t[2]+"\n"
				}
			}
			formato+="/"
    		break;
		default:
			alert("<?php echo $msgstr["pftseltemplate"]?>");
    }
	document.forma1.pft.value=formato
	document.forma1.headings.value=head
}

function SubFields(Tag,delim,ed){
	xtag="(if p(v"+Tag+") then "
	for (ic=0;ic<delim.length;ic++){
		if(delim.substr(ic,1)=="-")
			delimiter="*"
 		else
 			delimiter=delim.substr(ic,1)
 		edicion=ed.substr(ic,1)
 		if (ic==0)
 			edicion=""
 		else
 			if (edicion!="") edicion=" "+edicion
			if (edicion!="")
				xtag+=',|'+edicion+'|v'+Tag+'^'+delimiter+','
	        else
			    xtag+="| |v"+Tag+'^'+delimiter+","
	}
	xtag+=" if iocc<>nocc(v"+Tag+") then '<br>' fi"
	xtag+=" fi/)"
	return xtag
}

function SaveFormat(){
	document.forma1.fgen.selectedindex=-1
	if (Trim(document.forma1.pft.value)==""){
	  	alert("<?php echo $msgstr["pftselgen"]?>")
	  	return
	}
	if (Trim(document.forma1.nombre.value)==""){
		alert("<?php echo $msgstr["r_fnomb"]?>")
		return
	}
	if (Trim(document.forma1.descripcion.value)==""){
		alert("<?php echo $msgstr["pftnodescri"]?>")
		return
	}
	fn=document.forma1.nombre.value
	bool=  /^[a-z][\w]+$/i.test(fn)
 	if (!bool){
      	alert("<?php echo $msgstr["errfilename"]?>");
      	return
   	}
	if( CheckType()==false) return;
   	tipoacro=""
   	for (i=0;i<document.forma1.tipof.length;i++){
   		if (document.forma1.tipof[i].checked)
   			tipoacro=document.forma1.tipof[i].value
   	}
	separacro=""
   	for (i=0;i<document.forma1.separ.length;i++){
   		if (document.forma1.separ[i].checked)
   			separacro=document.forma1.separ[i].id
   	}
	document.guardar.pft.value=document.forma1.pft.value
	document.guardar.headings.value=document.forma1.headings.value
	document.guardar.tipoacro.value=tipoacro
	document.guardar.separacro.value=separacro
	document.guardar.nombre.value=document.forma1.nombre.value
	document.guardar.descripcion.value=document.forma1.descripcion.value
	document.guardar.base.value=document.forma1.base.value
	document.guardar.submit()
}

function SaveSearch(){
	document.savesearch.Expresion.value=Trim(document.forma1.Expresion.value)
	if (document.savesearch.Expresion.value==""){
		alert("<?php echo $msgstr["faltaexpr"]?>")
		return
	}
	Descripcion=document.forma1.Descripcion.value
	if (Trim(Descripcion)==""){
		alert("<?php echo $msgstr["errsave"]?>")
		return
	}
	document.savesearch.Descripcion.value=Descripcion
	var winl = (screen.width-300)/2;
	var wint = (screen.height-200)/2;
	msgwin=window.open("","savesearch","menu=no,status=yes,width=300, height=200,left="+winl+",top="+wint)
	msgwin.focus()
	document.savesearch.submit()
}

function SelectExistingFormat(){
	// called when another existing format is selected
	document.forma1.headings.value=""
	document.forma1.pft.value=""
	document.forma1.nombre.value=""
	document.forma1.descripcion.value=""
	for (i=0;i<document.forma1.tipof.length;i++){
		document.forma1.tipof[i].checked=false
	}
	for (i=0;i<document.forma1.separ.length;i++){
		document.forma1.separ[i].checked=false
	}
	ix=document.forma1.fgen.selectedIndex
	if(ix==0) return // the first option is blank
	format=document.forma1.fgen.options[ix].value
	formatar=format.split('|') // format is <pftname>|<description>|<syntax acronym>|<separator acronym>
	separset=false
	tipoacro=""
	if (0 in formatar) document.forma1.nombre.value=formatar[0]
	if (1 in formatar) document.forma1.descripcion.value=formatar[1]
	if (2 in formatar){
		for (i=0;i<document.forma1.tipof.length;i++){
			if (document.forma1.tipof[i].value===formatar[2]){
				document.forma1.tipof[i].checked=true
				tipoacro=document.forma1.tipof[i].value
			}
		}
	}
	if (3 in formatar) {
		for (i=0;i<document.forma1.separ.length;i++){
			if (document.forma1.separ[i].id===formatar[3]){
				document.forma1.separ[i].checked=true
				separset=true
			}
		}
	}
	if (!separset && tipoacro=='CT') document.forma1.separ[0].checked=true;// historical situation
	if (!separset && tipoacro=='CD') document.forma1.separ[0].checked=true;// historical situation
}

function SendForEdit(){
	if (document.forma1.fgen.selectedIndex==0 || document.forma1.fgen.selectedIndex==-1){
		alert("<?php echo $msgstr["r_self"]?>")
		return
	}
	document.forma1.headings.value=""
	document.forma1.pft.value=""
	document.forma1.action="pft.php"
	document.forma1.Opcion.value="edit"
	document.forma1.target="_self"
	document.forma1.submit()
}

function SubmitForm(vp){
	document.forma1.vp.value=vp;
	document.forma1.target="_self";
	document.forma1.headings.value=document.forma1.headings.value.trimEnd()
	de=Trim(document.forma1.Mfn.value)
  	a=Trim(document.forma1.to.value)
  	if (de!="" || a!="") {
	  	document.forma1.Opcion.value="rango"
  		Se=""
		var strValidChars = "0123456789";
		blnResult=true
		//  test strString consists of valid characters listed above
   		for (i = 0; i < de.length; i++){
    		strChar = de.charAt(i);
    		if (strValidChars.indexOf(strChar) == -1){
    			alert("<?php echo $msgstr["especificarvaln"]?>")
	    		return
    		}
    	}
    	for (i = 0; i < a.length; i++){
    		strChar = a.charAt(i);
    		if (strValidChars.indexOf(strChar) == -1){
    			alert("<?php echo $msgstr["especificarvaln"]?>")
	    		return
    		}
    	}
    	de=Number(de)
    	a=Number(a)
    	if (de<=0 || a<=0 || de>a ||a>top.maxmfn){
	    	alert("<?php echo $msgstr["numfr"]?>")
	    	return
		}
	}
	if (Trim(document.forma1.pft.value)=="" && document.forma1.fgen.selectedIndex<1 && Trim(document.forma1.pft.value)==""  ){
	  	alert("<?php echo $msgstr["r_selgen"]?>")
	  	return
	}
	if (Trim(document.forma1.Expresion.value)=="" && Trim(document.forma1.Mfn.value)=="" && Trim(document.forma1.seleccionados.value)==""){
		alert("<?php echo $msgstr["r_selreg"]?>")
		return
	}
	if (vp=="P") {
		msgwin=window.open("","VistaPrevia","width=600,top=0,left=0,resizable, status, scrollbars")
		document.forma1.target="VistaPrevia"
		msgwin.focus()
    }
	if (vp=="PRINT") {
		msgwin=window.open("","VistaPrevia","width=800,top=0,left=0,resizable, status, scrollbars")
		document.forma1.target="VistaPrevia"
		msgwin.focus()
    }
  	document.forma1.submit()
}

function toggleLayer( whichLayer ){
	var elem, vis;
	if( document.getElementById ) // this is the way the standards work
		elem = document.getElementById( whichLayer );
	else if( document.all ) // this is the way old msie versions work
		elem = document.all[whichLayer];
	else if( document.layers ) // this is the way nn4 works
		elem = document.layers[whichLayer];
	vis = elem.style;
	// if the style.display value is blank we try to figure it out here
	if( vis.display == '' && elem.offsetWidth != undefined && elem.offsetHeight != undefined )
		vis.display = ( elem.offsetWidth != 0 && elem.offsetHeight != 0 ) ? 'block':'none';
	vis.display = ( vis.display == '' || vis.display == 'block' ) ? 'none':'block';
}

function ValidarFormato(){
	if (Trim(document.forma1.pft.value)==""){
		alert("<?php echo $msgstr["genformat"]?>")
		return
	}
	document.forma1.action="crearbd_new_create.php"
	document.forma1.submit()
}
</script>
<?php
if ($encabezado!=""){
	include("../common/institutional_info.php");
}
?>
<div class="sectionInfo">
	<div class="breadcrumb">
    <?php echo $msgstr["pft"]." &rarr; ".$msgstr["database"].": ".$base?>
	</div>
	<div class="actions">
<?php
if ($Opcion=="new"){
	//This option comes from the creation of a new database. After creation of the fst a pft must be created
	$ayuda="pft_create.html";
    $backtoscript="fst.php?Opcion=new";
    $backtocancelscript="../dbadmin/menu_creardb.php";
    include "../common/inc_back.php";
    include "../common/inc_cancel.php";
}else{
	$ayuda="pft.html";
	if (isset($arrHttp["encabezado"])){
		if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_MODIFYDB"]) or isset($_SESSION["permiso"][$base."_CENTRAL_MODIFYDB"]) or isset($_SESSION["permiso"][$base."_CENTRAL_ALL"])){
			if (isset($arrHttp["retorno"])) $backtoscript=$arrHttp["retorno"];
			else                            $backtoscript="menu_modificardb.php";
            include "../common/inc_back.php";
            include("../common/inc_home.php");

		}else{
            include("../common/inc_home.php");
		}
	} else {
        $backtoscript="../dataentry/inicio_main.php";
        include "../common/inc_back.php";
    }// end if encabezado
    $backtocancelscript=$backtoscript;
}
?>
    </div>
    <div class="spacer">&#160;</div>
</div>
<?php
include "../common/inc_div-helper.php";
if ($Opcion!="new"){
	// Set additional variables if not creation of a new database
	$pftdir="pfts/".$_SESSION["lang"];
	// Read the list of existing pft Files
	$pftdirfull=$db_path.$base.$pftdir;
	if (!file_exists($pftdirfull)) mkdir($pftdirfull,0770,true);
	if (!file_exists($pftdirfull)){
		echo "<br><b style='color:red'>".$msgstr["fatal"].". ".$msgstr["folderne"].": ".$pftdirfull."</b>";
		die;
	}
	$handle = opendir($pftdirfull);
	while (false !== ($file = readdir($handle))) {
	   if ($file != "." && $file != "..") {
			if(is_file($pftdirfull."/".$file))
			   if (substr($file,strlen($file)-4,4)==".pft") $pft[]=$file;
	   }
	}
	closedir($handle);
	// For the actual pft for edit purposes
	if ( $Opcion=="edit") {
	}
	// Read the fdt file.
	$fdtarchivo=$db_path.$base."/def/".$_SESSION["lang"]."/".$base.".fdt";
	if (file_exists($fdtarchivo)){
		$fpTm=file($fdtarchivo);
	}else{
		// Try the fallback language
		$fdtarchivo=$db_path.$base."/def/".$lang_db."/".$base.".fdt";
		if (file_exists($fdtarchivo)){
			$fpTm=file($fdtarchivo);
		}else{
			echo "<br><b style='color:red'>".$msgstr["fatal"].". ".$msgstr["misfile"].": ".$fdtarchivo."</b>";
			die;
		}
	}
}else{
	$pftdir="";
	$fpTm=explode("\n",$_SESSION["FDT"]);
}
// The content of the FDT is required for creation and update of pft's
foreach ($fpTm as $linea){
	if (trim($linea)!="") {
		$t=explode('|',$linea);
		if ($t[0]!="S")
		$Fdt[]=rtrim($linea);
	}
}
if ( $Opcion!="new" ) {
    include ("../common/inc_get-dbinfo.php");// sets $arrHttp["MAXMFN"]
} else {
    $arrHttp["MAXMFN"]="";
}
?>
<div class="middle form">
<div class="formContent">
<form name=forma1 method=post action=../dataentry/imprimir_g.php >
<input type=hidden name=base value=<?php echo $base?>>
<input type=hidden name=cipar value=<?php echo $cipar?>>
<input type=hidden name=Dir value='<?php echo $pftdir?>'>
<input type=hidden name=Modulo value='<?php if (isset($arrHttp["Modulo"])) echo $arrHttp["Modulo"]?>'>
<input type=hidden name=Opcion>
<input type=hidden name=vp>
<?php
if ($encabezado!="") echo "<input type=hidden name=encabezado value=s>\n";

if ($Opcion!="new"){
    //==== USE AN EXISTING FORMAT ==================================
    ?>
	<table style="width:100%;">
        <tr>
        <td style="background-color:var(--abcd-gray-100);" >
    		<A HREF="javascript:toggleLayer('useexformat')"> <u><strong><?php echo $msgstr["useexformat"]?></strong></u></a>
    		<div id=useexformat>
				<span title="<?php echo $msgstr["pftexisting"]?>"><?php echo $msgstr["r_formatos"]?></span>: 
                <select name=fgen onchange='javascript:SelectExistingFormat()' title="<?php echo $msgstr["pftexisting"]?>">
                <option value=''/>
                <?php
                unset($fp);
                $archivo=$db_path.$base."/pfts/".$_SESSION["lang"]."/formatos.dat";
                if (!file_exists($archivo)) $archivo=$db_path.$base."/pfts/".$lang_db."/formatos.dat";
                if (file_exists($archivo)) $fp = file($archivo);
                if (isset($fp)){
                    foreach ($fp as $value){
                        $value=trim($value);
                        if ($value!=""){
							// The syntax of the list of pfts (formatos.dat):
							// [filename] | [description] | [syntax acronym(tipof)] | [separator acronym]
                            $pp=explode('|',$value);
                            if (!isset($pp[2])) $pp[2]="";
                            if (!isset($pp[3])) $pp[3]="";
                            if (isset($_SESSION["permiso"]["CENTRAL_ALL"])
                                or isset($_SESSION["permiso"][$base."_CENTRAL_ALL"])
                                or isset($_SESSION["permiso"][$base."_pft_ALL"])
                                or isset($_SESSION["permiso"][$base."_pft_".$pp[0]])){
									$selected="";
									if ( $pp[0]==$pft_name) $selected="selected";
                                echo "<option ".$selected." ";
								echo "value=\"".$pp[0]."|".$pp[1]."|".$pp[2]."|".$pp[3]."\">".$pp[1]." (".$pp[0].")</option>\n";
                            }
                        }
                    }

                }
                ?>
                </select>
                <?php
                if (isset($_SESSION["permiso"]["CENTRAL_ALL"])
                    or isset($_SESSION["permiso"][$base."_CENTRAL_ALL"])
                    or isset($_SESSION["permiso"][$base."_CENTRAL_EDPFT"])
                    or isset($_SESSION["permiso"]["_CENTRAL_EDPFT"])) {
                    ?> &nbsp;
                    <button class="bt-green" type="button"
                        title="<?php echo $msgstr["edit"]?>:&nbsp;<?php echo $base."/".$pftdir."/..."?>"
                        onclick='javascript:SendForEdit("")'>
                        <i class="far fa-edit"></i> <?php echo $msgstr["edit"]?></button>
                    <button class="bt-red" type="button"
                        title="<?php echo $msgstr["delete"]?>:&nbsp;<?php echo $base."/".$pftdir."/..."?>"
                        onclick='javascript:DeleteFormat()'>
                        <i class="fas fa-trash"></i> <?php echo $msgstr["delete"]?></button>
                    <?php
                }
                ?>
            </div>
        </td></tr>
    </table>
<?php
}else{
    echo "<div id=useexformat></div>";
}
if (isset($_SESSION["permiso"]["CENTRAL_ALL"])
    or isset($_SESSION["permiso"][$base."_CENTRAL_ALL"])
    or isset($_SESSION["permiso"]["CENTRAL_EDPFT"])
    or isset($_SESSION["permiso"][$base."_CENTRAL_EDPFT"])
    or isset($_SESSION["permiso"]["CENTRAL_MODIFYDB"])
    or isset($_SESSION["permiso"][$base."_CENTRAL_MODIFYDB"])){
    // ==== CREATE A FORMAT==== also for "new" items === also for edit items ========
	if ($Opcion=="edit"){
		//read the content of the pft and pft_h
		if (ReadPFT  ($pft_name, $pft_content)!=0) die;
		if (ReadPFT_H($pft_name, $pft_h_content)!=0) die;
	}
	?>
    <!-- CREATE A FORMAT -->
    <table style="width:100%;">
        <tr>
        <td style="background-color:var(--abcd-gray-200);">
            <a HREF="javascript:toggleLayer('createformat')"><u><strong><?php if($Opcion=="edit"){echo $msgstr["editcreate"];} else{echo $msgstr["r_creaf"];}?></strong></u></a>
            <div id=createformat>
                <?php echo $msgstr["r_incluirc"]?>&nbsp; &nbsp;
				<a href=fdt_leer.php?base=<?php echo $arrHttp["base"]?> target=_blank><?php echo $msgstr["show"]." ".$msgstr["fdt"]?></a>
           &nbsp; &nbsp; &nbsp;
            <table>
                <tr>
                <td>
                    <select name=list11 style="width:250px" multiple size=10 onDblClick="moveSelectedOptions(this.form['list11'],this.form['list21'],false)">
                    <?php
                        $t=array();
                        foreach ($Fdt as $linea){
                            $t=explode('|',$linea);
                            echo "<option value='".$linea."'>".$t[2]." (".$t[1].")\n";
                        }
                    ?>
					</select>
				</td>
				<td>
					<a class="button_browse show" href="#" onClick="moveSelectedOptions(document.forms[0]['list11'],document.forms[0]['list21'],false);return false;">
                        <i class="fas fa-angle-right"></i>
                        </a>
                        <br><br>
                    <a class="button_browse show" href="#" onClick="moveAllOptions(document.forms[0]['list11'],document.forms[0]['list21'],false); return false;">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                    <br><br>
                    <a class="button_browse show" href="#" onClick="moveAllOptions(document.forms[0]['list21'],document.forms[0]['list11'],false); return false;"><i class="fas fa-angle-double-left"></i>
                    </a>
                    <br><br>
                    <a class="button_browse show" href="#" onClick="moveSelectedOptions(document.forms[0]['list21'],document.forms[0]['list11'],false); return false;">
                        <i class="fas fa-angle-left"></i>
                    </a>
				</td>
				<td width=250>
					<select NAME="list21" MULTIPLE SIZE=10 style="width:250px" onDblClick="moveSelectedOptions(this.form['list21'],this.form['list11'],false)">
					</select>
				</td>
				<td>
                    <button class="button_browse show" type="button" value="<?php echo $msgstr["up"]?>" title="<?php echo $msgstr['up']?>" onClick="moveOptionUp(this.form['list21'])"><i class="fas fa-caret-up"></i></button>
                    <br><br>
                    <button class="button_browse show" type="button" value="<?php echo $msgstr["down"]?>" title="<?php echo $msgstr["down"]?>" onClick="moveOptionDown(this.form['list21'])"><i class="fas fa-caret-down"></i></button>
				</td>
                </tr>
			</table>
			<table>
                <tr>
				<td><?php echo $msgstr["pfttemplate"]?></td>
				<td >
                <input type=radio name=tipof value=T  <?php if ($tipoacro=="T")  echo "checked"?>> <?php echo $msgstr["r_tabla"]?>&nbsp;
                <input type=radio name=tipof value=P  <?php if ($tipoacro=="P")  echo "checked"?>> <?php echo $msgstr["r_parrafo"]?>&nbsp;
                <input type=radio name=tipof value=PL <?php if ($tipoacro=="PL") echo "checked"?>> <?php echo $msgstr["r_parrafowith"]?>&nbsp;
                <input type=radio name=tipof value=CT <?php if ($tipoacro=="CT") echo "checked"?>> <?php echo $msgstr["r_colstab"]?>&nbsp;
                <input type=radio name=tipof value=CD <?php if ($tipoacro=="CD") echo "checked"?>> <?php echo $msgstr["r_colsdelim"]?>
				</td>
				</tr><tr>
				<td><?php echo $msgstr["pftseparator"]."<br>".$msgstr["r_colsdelim"]?></td>
				<td>
                <input type=radio name=separ value="|" id=VBAR  <?php if ($separacro=="VBAR") echo "checked"?>><b> | &nbsp; &nbsp;</b>
                <input type=radio name=separ value="," id=COMMA <?php if ($separacro=="COMMA")echo "checked"?>><b> , &nbsp; &nbsp;</b>
                <input type=radio name=separ value=";" id=SEMI  <?php if ($separacro=="SEMI") echo "checked"?>><b> ;</b>
				</td></tr>
				<tr><td></td>
				<td>
					<button class="bt-blue" type="button"
							title="<?php echo $msgstr["pftgenformatplus"]?>" onclick='javascript:GenerarFormato()'>
                            <i class="far fa-plus-square"></i> <?php echo $msgstr["pftgenformat"]?></button>
					&nbsp; &nbsp; &nbsp; &nbsp;
					<button class="bt-gray" type="button"
                            title="<?php echo $msgstr["pftresetcreate"]?>" onclick='javascript:ClearFormat()'>
                            <i class="fa fa-times"></i> <?php echo $msgstr["borrar"]?></button>
                </td></tr>
            </table>
			<table>
                <tr>
                <td>
                    <?php echo $msgstr["pftcontent"]?>
					<a href="https://abcd-community.org/cisis-formatting-language/" target="_blank">
						<i class="far fa-life-ring"></i> <?php echo $msgstr["cisis"]?>
					</a><br>
                    <textarea name=pft cols=80 rows=10 style="font-family:courier new;"><?php echo $pft_content?></textarea>
                </td>
                <td>
                    <?php echo $msgstr["pftheadings"]?><br>
                    <textarea name=headings cols=30 rows=10 style="font-family:courier new;" onchange=CheckType()><?php echo $pft_h_content?></textarea>
                </td>
                </tr>
            </table>
		</div>
		</td>
        </tr>
    </table>
<?php
}else{
	echo "<div id=createformat></div>";
}
// =========== Save format =========== Not for new databases
if ($Opcion!="new" and
    (   isset($_SESSION["permiso"]["CENTRAL_ALL"])
        or isset($_SESSION["permiso"]["CENTRAL_EDPFT"])
        or isset($_SESSION["permiso"][$base."_CENTRAL_ALL"])
        or isset($_SESSION["permiso"][$base."_CENTRAL_EDPFT"]))){
    ?>
    <table style="width:100%" >
        <tr>
        <td  style="background-color:#e6ffe6;">
            <a HREF="javascript:toggleLayer('saveformat')"><u><strong><?php echo $msgstr["r_guardar"]?></strong></u></a>
            <div id=saveformat>
                <span title="<?php echo $msgstr["pftnameenter"]?>"><?php echo $msgstr["name"]?></span>
                <input type=text name=nombre size=20 maxlength=30 value="<?php echo $pft_name?>" title="<?php echo $msgstr["pftnameenter"]?>"> &nbsp; &nbsp;

                <?php echo $msgstr["description"]?>
                <input type=text size=50 maxlength=50 name=descripcion value="<?php echo $description?>"> &nbsp; 

                <button class="bt-green" type="button"
                    title="<?php echo $msgstr["saveinfolder"]?>:&nbsp;<?php echo $base."/". $pftdir?>/"
                    onclick="javascript:SaveFormat()">
                    <i class="far fa-save"></i> </button>
            </div>
        </td>
        </tr>
    </table>
    <?php
}
 //=============GENERATE OUTPUT============= 
if ($Opcion!="new"){?>
    <!-- GENERATE OUTPUT -->
    <table style="width:100%">
	<tr>
    <td style="background-color:PowderBlue;">
        <a HREF="javascript:toggleLayer('generateoutput')"><u><strong><?php echo $msgstr["generateoutput"]?></strong></u></a>
        <div id=generateoutput>
            <table>
                <tr> <!-- row 1 record selection by MFN range -->
                <td><?php echo $msgstr["r_recsel"]?><br>
                    <b><?php echo $msgstr["r_mfnr"]?></b>
                </td>
                <td>
                    <?php echo $msgstr["r_desde"]?>: <input type=text name=Mfn size=10>&nbsp; &nbsp; &nbsp; &nbsp;
                    <?php echo $msgstr["r_hasta"]?>: <input type=text name=to size=10>
                    &nbsp;<?php echo $msgstr["maxmfn"]?>:&nbsp;<?php echo $arrHttp["MAXMFN"] ?>
                    &nbsp; &nbsp; 
                    <button class="bt-gray" type="button"
                        title="<?php echo $msgstr["borrar"]?>" onclick='javascript:ClearRange()'>
                        <i class="fa fa-times"></i> <?php echo $msgstr["borrar"]?></button>
                </td>
                </tr>
                <tr><td colspan=2><hr class="color-gray-100"></td></tr>
                <?php // check if there are preselected records
                if (isset($arrHttp["seleccionados"])){
                ?>
                <tr>  <!-- row 2 selected record numbers -->
                <td><?php echo $msgstr["r_recsel"]?><br>
                    <b><?php echo $msgstr["selected_records"]?></b>
                    <?php
                    $sel=str_replace("__",",",trim($arrHttp["seleccionados"]));
                    $sel=str_replace("_","",$sel);
                    ?>
                </td>
                <td>
                    <input type=text name=seleccionados size=100 value=<?php echo $sel?>>
                &nbsp;
                    <button class="bt-red" type="button"
                        title="<?php echo $msgstr["borrar"]?>" onclick='javascript:DeleteSelected()'>
                        <i class="fa fa-times"></i> <?php echo $msgstr["borrar"]?></button>
                </td>
                </tr>
                <tr><td></td><td><hr class="color-gray-100"><br></td></tr>
                <?php 
				} else {
					// define dummy entry to avoid undefined references
					?><input type=hidden name=seleccionados><?php 
				}
				?>
                <tr> <!-- row 3 record selection by Search -->
                <td><?php echo $msgstr["r_recsel"]?><br>
                    <b><?php echo $msgstr["r_busqueda"]?></b>
                </td>
                <td>
                <table>
                    <tr><td colspan=2>
                        <?php // proces a possible search expression table
                        unset($fp);
                        if (file_exists($db_path.$base."/pfts/".$_SESSION["lang"]."/search_expr.tab"))
                            $fp = file($db_path.$base."/pfts/".$_SESSION["lang"]."/search_expr.tab");
                        else
                            if (file_exists($db_path.$base."/pfts/".$lang_db."/search_expr.tab"))
                                $fp = file($db_path.$base."/pfts/".$lang_db."/search_expr.tab");
                        if (isset($fp)){
                            ?>
                            <?php echo $msgstr["copysearch"]?> :&nbsp;
                            <select name=Expr id=Expr onChange=CopiarExpresion()>
                                <option value=''>
                                <?php
                                foreach ($fp as $value){
									// format= <description>|<search_expression>
									// may contain empty lines
                                    $value=trim($value);
                                    if ($value!=""){
                                        $pp=explode('|',$value);
                                        ?>
                                        <option value='<?php echo $pp[1]?>'><?php echo $pp[0]?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select> &nbsp; &nbsp;
                            <?php
                        }
                        ?>
                        <button class="bt-green" type="button"
                            title="<?php echo $msgstr["pftcreatesrcexpr"]?>"
                            onclick='javascript:Buscar()'>
                            <i class="far fa-plus-square"></i> <?php echo $msgstr["pftcreatesrcexpr"]?></button>
						<?php
						if (isset($fp)){
							// show delete button only if there is an expression table
						?>
						<button class="bt-red" type="button"
							title="<?php echo $msgstr["pftdelsrchexpr"]?>"
							onclick='javascript:DeleteSrchExpr()'>
							<i class="fas fa-trash"></i> <?php echo $msgstr["pftdelsrchexpr"]?></button>
						<?php
						}
						?>
                       </td>
                    </tr>
                    <tr><td>
                        <textarea rows=2 cols=100 name=Expresion><?php if ($Expresion!="") echo $Expresion?></textarea>
                    </td>
                    <td> &nbsp;
                        <button class="bt-gray" type="button"
                            title="<?php echo $msgstr["borrar"]?>" onclick='javascript:DeleteExpression()'>
                            <i class="fa fa-times"></i> <?php echo $msgstr["borrar"]?></button>
                    </td>
                    </tr>
                    <?php
                    if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or
                        isset($_SESSION["permiso"]["CENTRAL_SAVEXPR"])  or
                        isset($_SESSION["permiso"][$base."_CENTRAL_ALL"]) or
                        isset($_SESSION["permiso"][$base."_CENTRAL_SAVEXPR"])){
                        ?>
                        <tr><td>
                            <button class="bt-green" type="button"
                                title="<?php echo $msgstr["savesearch"]?>"
                                onclick="javascript:SaveSearch()">
                                <i class="far fa-save"></i> <?php echo $msgstr["savesearch"]?></button>
                            <?php echo $msgstr["r_desc"].": " ?>
                            <input type=text name=Descripcion size=40>
                        </td>
                        <?php
                    }
                    ?>
                </table>
                </td>
                </tr>
                <tr><td colspan=2><hr class="color-gray-100"></td></tr>
                <tr>  <!-- row 4 sort key -->
                <td><b><?php echo $msgstr["sortkey"]?></b></td>
                <td><?php echo $msgstr["sortkeycopy"]?> :&nbsp;
                    <select name=sort  onChange=CopySortKey()>
                        <option value=''>
                        <?php
                        unset($fp);
                        if (file_exists($db_path.$base."/pfts/".$_SESSION["lang"]."/sort.tab"))
                            $fp = file($db_path.$base."/pfts/".$_SESSION["lang"]."/sort.tab");
                        else
                            if (file_exists($db_path.$base."/pfts/".$lang_db."/sort.tab"))
                                $fp = file($db_path.$base."/pfts/".$lang_db."/sort.tab");
                        if (isset($fp)){
                            foreach ($fp as $value){
                                if (trim($value)!=""){
                                    $pp=explode('|',$value);
                                    ?>
                                    <option value="<?php echo trim($pp[1])?>"><?php echo $pp[0]?></option>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </select> &nbsp; &nbsp;
                    <?php 
                    if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or
                        isset($_SESSION["permiso"]["CENTRAL_EDSORT"]) or
                        isset($_SESSION["permiso"][$base."_CENTRAL_ALL"]) or
                        isset($_SESSION["permiso"][$base."_CENTRAL_EDSORT"])){
                        ?>
                        <button class="bt-green" type="button"
                            title="<?php echo $msgstr["sortkeycreeddel"]?>"
                            onclick='javascript:CreateSortKey()'>
                            <i class="far fa-plus-square"></i> <?php echo $msgstr["sortkeycreeddel"]?></button>
                    <?php  }
                    ?>
                    <br>
                    <input type=text name=sortkey size=70>
                </td>
                </tr>
                <tr><td colspan=2><hr class="color-gray-100"><br></td></tr>
                <tr>  <!-- row 5 send to -->
                <td style="border-bottom:none">
                    <strong><?php echo $msgstr["sendto"]?></strong>:
                </td>
                <td>
                    <button class="bt-blue" type="button"
                        title="<?php echo $msgstr["vistap"]?>"
                        onclick="javascript:SubmitForm('P')">
                        <i class="far fa-eye"></i> <?php echo $msgstr["vistap"]?></button> &nbsp;
                    <button class="bt-blue" type="button"
                        title="<?php echo $msgstr["word"]?>"
                        onclick="javascript:SubmitForm('WP')">
                        <i class="far fa-file-word"></i> <?php echo $msgstr["word"]?></button> &nbsp;
                    <button class="bt-blue" type="button"
                        title="<?php echo $msgstr["wsproc"]?>"
                        onclick="javascript:SubmitForm('TB')">
                        <i class="far fa-file-excel"></i> <?php echo $msgstr["wsproc"]?></button> &nbsp;
                    <button class="bt-blue" type="button"
                        title="<?php echo $msgstr["pftplaintxt"]?>"
                        onclick="javascript:SubmitForm('TXT')">
                        <i class="far fa-file-alt"></i> <?php echo $msgstr["pftplaintxt"]?></button> &nbsp;
                    <button class="bt-blue" type="button"
                        title="<?php echo $msgstr["Print"]?>"
                        onclick="javascript:SubmitForm('PRINT')">
                        <i class="far fa-file-alt"></i> <?php echo $msgstr["Print"]?></button>
                </td>
            </table>
        </div>
    </td></tr>
    </table>
	<br>
	<?php
	include "../common/inc_back.php";
/*  End of the tables with collapsing content. */
}else{
	/* show a save button in case of database creation*/
    ?>
    <button class="bt-green" type="button"
        title="<?php echo $msgstr["createdb"]?>"
        onclick="javascript:ValidarFormato()">
        <i class="far fa-save"></i> <?php echo $msgstr["createdb"]?> </button>
    <?php
}
?>
</form>
<form name=guardar method=post action=pft_update.php>
    <input type=hidden name=base value=<?php echo $base?>>
    <input type=hidden name=pft>
    <input type=hidden name=nombre>
    <input type=hidden name=descripcion>
    <input type=hidden name=tipoacro>
    <input type=hidden name=separacro>
    <input type=hidden name=headings>
    <input type=hidden name=pftname>
    <?php if ($encabezado=="s") echo "<input type=hidden name=encabezado value=s>"; ?>
    <input type=hidden name=Modulo value='<?php if (isset($arrHttp["Modulo"])) echo $arrHttp["Modulo"]?>'>
</form>
<form name=frmdelete action=pft_delete.php method=post>
    <input type=hidden name=base value=<?php echo $base?>>
    <input type=hidden name=pft>
    <?php if ($encabezado=="s") echo "<input type=hidden name=encabezado value=s>"; ?>
</form>
<form name=savesearch action=../dataentry/busqueda_guardar.php method=post target=savesearch>
	<input type=hidden name=base value=<?php echo $base?>>
	<input type=hidden name=Expresion value="">
	<input type=hidden name=Descripcion value="">
</form>
<form name=removesearch action=../dataentry/busqueda_eleminar.php method=post target=removesearch>
	<input type=hidden name=base value=<?php echo $base?>>
	<input type=hidden name=Descripcion value="">
</form>
<form name=sortkey method=post action=sortkey_edit.php target=sortkey>
	<input type=hidden name=base value=<?php echo $base?>>
	<input type=hidden name=encabezado value=s>
</form>
</div>
</div>
<?php
// Initially shown layers are set here as the layers (implemented by <div>) must exist
// See also divs for initial show layers
/*Example: if ($Opcion=="edit") {?><script>toggleLayer('createformat')</script><?php };*/

include("../common/footer.php");

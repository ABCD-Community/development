<?php
//header('Content-Type: text/html; charset=iso-8859-1');

ini_set('error_reporting', E_ALL);
$cisis_versions_allowed="16-60;ffi;bigisis";

// Main server configuration
$server_url="http://localhost:9090";    // URL with port (if not 80)
$postMethod=1;                          // if set to '1' (or true) ABCD will use POST-method; if set to '0' the GET-method will be used. Use with caution
$dirtree=1;                             // SE THIS PARAMETER TO SHOW THE ICON THAT ALLOWS THE BASES FOLDER EXPLORATION
$MD5=0;                                 // USE THIS PARAMETER TO ENABLE/DISABLE THE MD5 PASSWORD ENCRIPTYON (0=OFF 1=ON)
$EmpWeb=0;                              // use EmpWeb or not
$use_ldap=0;                            // use LDAP or not

// Set operation system depending variables
if (stripos($_SERVER["SERVER_SOFTWARE"],"Win") > 0) {  //Windows path variables
	$ABCD_path="/ABCD/";                    // base path to ABCD-installation
 	$db_path="/ABCD/www/bases/";                     // path where the databases are to be located
 	$exe_ext=".exe";                        // extension for executables
}else{                                   // Linux path variables
 	$ABCD_path="/opt/ABCD/";
 	$db_path="/var/opt/ABCD/bases/";
 	$exe_ext="";
}
$ABCD_scripts_path=$ABCD_path. "www/htdocs/";  //PATH

//IF THERE ARE MULTIPLE BASES FOLDERS THE FOLDETR SELECTED IS SET
if ( isset($_SESSION["db_path"]) and  $_SESSION["db_path"]!="")
 	$db_path=$_SESSION["db_path"];

if (isset($_REQUEST["db_path"])) {    //PARA PERMITIR MANEJAR VARIAS CARPETAS BASES DESDE EL OPAC
	$_REQUEST["db_path"]=urldecode($_REQUEST["db_path"]);
	$db_path=$_REQUEST["db_path"];
	if (isset($_SESSION)) $_SESSION["db_path"]=$db_path; //CUANDO VIENE DEL OPAC NO SE TRABAJA CON SESIONES
}

// Other local settings to be configured
$open_new_window="N";                   // Open the Central module in a new window for avoiding the use of the browse buttons
$context_menu="Y";                      // allow opening right-click menu
$config_date_format="DD/MM/YY";         // USED FOR ALL THE DATE FUNCTIONS. DD=DAYS, MM=MONTH, AA=YEAR. USE / AS SEPARATOR
$app_path="central";                    // Folder with the administration module
$inventory_numeric ="N";                // This variable erases the left zeroes in the inventory number
$max_inventory_length=1;                // Add Zeroes to the left for reaching the max length of the inventory number
$max_cn_length=1;                       // Add Zeroes to the left for reaching the max length of the control number
$log="Y";                               // switch on logging of the actions, a subfolder 'log' needs to exist in database-directory
$lang="en";                             // default language
$lang_db="en";                          // Default langue for the databases definition
$change_password="Y";                   //allow change password
$ext_allowed=array("jpg","gif","png","pdf","doc","docx","xls","xlsx","odt");    //extensions allowed for uploading files (used in dataentry/)

// *** NO CHANGES NEEDED BELOW HERE
// Construction of executable path and URL
                             // initialisation of $cisis_ver as empty = default standard CISIS-version
$wxis_exec="wxis".$exe_ext;                // name and extension of wxis executable
$mx_exec="mx".$exe_ext;                    // name and extension of mx executable
$msg_path=$db_path;                        // path where the message-files are stored, typciall the database-directory
$img_path=$db_path;                        // legacy path to the folder where the uploaded images are to be stored (the database name will be added to this path)
$cgibin_path=$ABCD_path."www/cgi-bin/";    // path to the basic directory for CISIS-utilities
$xWxis=$ABCD_path."www/htdocs/$app_path/dataentry/wxis/";    // path to the wxis scripts .xis for Central
//para leer los par�metros reales de configuracion
/*if (file_exists("config_vars.php"))
	include("config_vars.php");
else
	if (file_exists("../config_vars.php"))
		include("../config_vars.php");
	else
		if (file_exists("central/config_vars.php"))
			include("central/config_vars.php");
*/
//$unicode="";
if (substr($db_path, strlen($db_path)-1,1) <> "/") $db_path.="/";
$unicode="";
$institution_name="";
$cisis_ver="";
if (!file_exists($db_path."abcd.def")){
	echo "Missing abcd.def in the database folder $db_path of ABCD"; die;
 }
if (isset($_SESSION["MULTIPLE_DB_FORMATS"])) unset($_SESSION["MULTIPLE_DB_FORMATS"]);
$def = parse_ini_file($db_path."abcd.def",true);      // read variables from abcd.def
$institution_name=$def["LEGEND2"];        // Institution name defined by abcd.def 'LEGEND2'

if (isset($def["UNICODE"]) and $def["UNICODE"]==1)
	$meta_encoding="UTF-8";
else
	$meta_encoding="ISO-8859-1";



if (file_exists(realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR."config_extended.php")){
 	include (realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR."config_extended.php");  //Include config_extended.php that reads extra configuration parameters
}
//en el config_extended.php se pueden cambiar los valores de $def["UNICODE"],$def[["CISIS_VERSION"]] 0 $def["UNICODE"]
//se determina la versi�n del cisis; Si el par�metro no existe se asuma 16-60
//echo "<pre>";echo print_r($def);echo "</pre>";

 if (isset($def["UNICODE"]) and $def["UNICODE"]==1){
	$unicode="utf8";
	$charset="UTF-8";
}else{
	$unicode="ansi";
	$def["UNICODE"]=0;
	$charset="ISO-8859-1";
}
$_SESSION["meta_encoding"]=$meta_encoding;
header('Content-Type: text/html; charset=$charset');

//SE CAMBIA EL LENGUAJE POR DEFECTO POR EL QUE SE ESTABLEZCA EN abcd.def
if (isset($def["DEFAULT_LANG"])) $lang=$def["DEFAULT_LANG"];

$cisis_ver="";
if (isset($def["CISIS_VERSION"]) and $def["CISIS_VERSION"]!="16-60" and $def["CISIS_VERSION"]!="" ){
	$cisis_ver=$def["CISIS_VERSION"];
}

$cisis_path=$cgibin_path.$unicode;
if ($cisis_ver!="")
	$cisis_path.="/".$cisis_ver."/";   // path to directory with correct CISIS-executables
else
	$cisis_path.="/";

$mx_path=$cisis_path.$mx_exec;                      // path to mx-executable
if ($postMethod == '1'){
	$wxisUrl=$server_url."/cgi-bin/";
	if ($unicode!="") $wxisUrl.="$unicode/";
	if ($cisis_ver!="") $wxisUrl.=$cisis_ver."/";
	$wxisUrl.=$wxis_exec;  // POST method used
	$Wxis="";
}else{
	$wxisUrl="";
 	$Wxis=$cgibin_path;
 	if ($unicode!="") $Wxis.="$unicode/";
 	if ($cisis_ver!="") $Wxis.=$cisis_ver."/";
 	$Wxis.=$wxis_exec;   //GET method is used
}



$FCKConfigurationsPath="/".$app_path."/dataentry/fckconfig.js";  // path to CKeditor configuration
$FCKEditorPath="/site/bvs-mod/FCKeditor/";                       // path to CKEditor
if ($use_ldap) {                                                 //LDAP parameters if used
	$ldap_host = "ldap://zflexldap.com";
	$ldap_dn = "cn=ro_admin,ou=sysadmins,dc=zflexsoftware,dc=com";
	$ldap_search_context = "ou=guests,dc=zflexsoftware,dc=com";
	$ldap_port = "389";
	$ldap_pass = "zflexpass";
}
if ($EmpWeb) {                                                   //EmpWeb parameters if used
	$empwebservicequerylocation = "http://localhost:8086/ewengine/services/EmpwebQueryService";
	$empwebservicetranslocation = "http://localhost:8086/ewengine/services/EmpwebTransactionService";
	$empwebserviceobjectsdb = "objetos";
	$empwebserviceusersdb = "*";
}
$adm_login="";                                                // emergency username for administrator
$adm_password="";                                            // emergency password for administrator


?>
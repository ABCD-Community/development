<?php
session_start();
include("../common/get_post.php");
include ("../config.php");
$lang=$arrHttp["lang"];
$_SESSION["lang"]=$lang;
include("../lang/dbadmin.php");;
if (!isset($arrHttp["archivo"])) die;
$fp=file("../../dbpath.dat");
if (!$fp)    {
	die;
$db_path="";
foreach ($fp as $value){
		break;
if ($db_path==""){
	die;
$d=explode("|",$db_path);
$db_path=$d[0];
$archivo=$db_path.$arrHttp["archivo"];
?>
<html>
<body>
<font face=verdana>
<?
if (!file_exists($archivo)){

	$fp=file($archivo);
	echo "<h5>".$arrHttp["archivo"]."</h5>
	<xmp>";

	foreach ($fp as $value) echo $value;
	echo "</xmp>";
}
?>
</body>
</html>
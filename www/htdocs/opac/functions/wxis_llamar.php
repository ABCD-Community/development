<?php
/*
20220128 fho4abcd Removed MULTIPLE_DB_FORMATS
*/
/*
function WxisLLamar() {
    global $ABCD_scripts_path;  //para asegurar que la variable est� cuando se llama desde una funci�n
    include($ABCD_scripts_path."central/common/wxis_llamar.php");
}*/

function wxisLlamar($base, $query, $IsisScript) {
	global $db_path, $Wxis, $xWxis, $ABCD_scripts_path;
	include($ABCD_scripts_path."central/common/wxis_llamar.php");
	return $contenido;
}

?>
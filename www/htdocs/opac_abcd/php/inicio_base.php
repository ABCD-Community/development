<?php
include("config_opac.php");
include("leer_bases.php");
include("tope.php");
//echo "<div style=\"margin:0 auto;height:100%\">";
//foreach ($_REQUEST as $var=>$value) echo "$var=>$value<br>";
if (isset($_REQUEST["home"])){
	if (substr($_REQUEST["home"],0,6)=="[LINK]"){
	if (substr($_REQUEST["home"],0,6)=="[TEXT]"){
			foreach ($fp as $value){
    }
}
//echo "</div>";

include("footer.php");
?>

<script>
function setIframeHeight(iframe) {
	if (iframe) {
		var iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow;
		if (iframeWin.document.body) {
			iframe.height = iframeWin.document.documentElement.scrollHeight || iframeWin.document.body.scrollHeight;
		}
	}
};

window.onload = function () {
	setIframeHeight(document.getElementById('idf'));
};
</script>
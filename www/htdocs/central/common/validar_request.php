<?php
foreach ($_REQUEST as $var=>$value){
		$value=str_replace(' ','',$value);
		if (stripos($value,"<script>")!==false  ){
			unset($_REQUEST[$var]);
			die;
		}
	}
	if (stripos($value,">")!==false and $var!="ValorCapturado" and $var!="pftedit") die;
	/*if ($var=="base"){
		$encontrado="N";
		foreach ($fp as $x){
		if ($encontrado=="N") die;
}
?>
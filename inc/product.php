<?php
//DO NOT EDIT
define('__ROOT__', dirname(dirname(__FILE__))); 

$x = '';
$def_app_img = 'img/logo_400.png';
$product = 'JBs Perfumers Vault Pro';
if(file_exists('.DOCKER') == TRUE){
	$x = "DOCKER";
}
$ver = file_get_contents(__ROOT__.'/VERSION.md').$x;
$pvOnlineAPI = 'https://online.jbparfum.com/api.php';


?>

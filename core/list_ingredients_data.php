<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require(__ROOT__.'/inc/sec.php');

$provider = $_POST['provider'];

if(empty($provider) || $provider == 'local'){
	require (__ROOT__.'/modules/suppliers/local.php');
}else{
	require (__ROOT__.'/modules/suppliers/'.$provider.'.php');
}
?>
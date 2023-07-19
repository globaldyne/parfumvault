<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');

$provider = $_POST['provider'];

if(empty($provider) || $provider == 'local'){
	require_once (__ROOT__.'/modules/suppliers/local.php');
}else{
	require_once (__ROOT__.'/modules/suppliers/'.$provider.'.php');
}
?>
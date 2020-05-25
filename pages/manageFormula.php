<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');

$formula = mysqli_real_escape_string($conn, $_GET['formula']);

$q = mysqli_query($conn, "SELECT quantity,ingredient FROM formulas WHERE name = '$formula'");
while($cur =  mysqli_fetch_array($q)){
	if($_GET['do'] == 'multiply'){
		$nq = $cur['quantity']*2;
	}elseif($_GET['do'] == 'divide'){
		$nq = $cur['quantity']/2;
	}
	mysqli_query($conn,"UPDATE formulas SET quantity = '$nq' WHERE name = '$formula' AND quantity = '$cur[quantity]' AND ingredient = '$cur[ingredient]'");
}
header("Location: /?do=Formula&name=$formula");
?>
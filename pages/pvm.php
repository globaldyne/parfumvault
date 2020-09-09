<?php
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');
require_once('../inc/product.php');


if($_GET['setup'] == '1'){
	$ip = $_SERVER['SERVER_ADDR'];
	if(file_get_contents("http://".$settings['pv_maker_host']."/?setup=1&ip=$ip")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Command sent!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Failed to contact PVM Device!</div>';
	}
	return;
}
if($_GET['setup'] == 'rfd'){
	if(file_get_contents("http://".$settings['pv_maker_host']."/?setup=rfd")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Command sent!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Failed to contact PVM Device!</div>';
	}
	return;
}

if($_GET['queue'] == 'add'){
	$dpg = trim($_GET['dpg']);
	$ethanol = trim($_GET['ethanol']);
	$water = trim($_GET['water']);
	$compound = trim($_GET['compound']);
	
	$pvm_q_q.= mysqli_query($conn,"INSERT INTO pv_maker_queue (material,quantity_to_add,pending,active) VALUES ('DPG','$dpg','1','0')");
	$pvm_q_q.= mysqli_query($conn,"INSERT INTO pv_maker_queue (material,quantity_to_add,pending,active) VALUES ('ALC','$ethanol','1','0')");
	$pvm_q_q.= mysqli_query($conn,"INSERT INTO pv_maker_queue (material,quantity_to_add,pending,active) VALUES ('WAT','$water','1','0')");
	$pvm_q_q.= mysqli_query($conn,"INSERT INTO pv_maker_queue (material,quantity_to_add,pending,active) VALUES ('COM','$compound','1','0')");

	if($pvm_q_q){
		
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Added to the queue</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error'.mysqli_error($conn).'</div>';
	}
	return;
		
}

?>
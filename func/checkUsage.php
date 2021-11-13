<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function checkUsage($limit,$conc,$defCatClass){
	if($limit != null){
		if($limit < $conc){
			return 'class="alert-danger"';//VALUE IS TO HIGH AGAINST IFRA
		}else{
			return 'class="alert-success"'; //VALUE IS OK
		}
	}else
	if($defCatClass != null){
		if($defCatClass < $conc){
			return 'class="alert-info"'; //VALUE IS TO HIGH AGAINST LOCAL DB
		}else{
			return 'class="alert-success"'; //VALUE IS OK
		}
	}else{
		return 'class="alert-warning"';
	}
	return;
}
?>

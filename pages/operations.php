<?php
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');

if($_GET['do'] == 'db_update'){

	$n_ver = trim(file_get_contents('../db/schema.ver'));
	$c_ver = $pv_meta['schema_ver'];
	
	
}

?>
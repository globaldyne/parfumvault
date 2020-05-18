<?php

$tmp_path = 'tmp/';
$allowed_ext = 'pdf,doc,docx';
$max_filesize = '4194304';
$total_records_per_page = 20;

error_reporting(E_ALL ^ E_NOTICE); 
$product = 'JBs Parfum Vault';
$ver = file_get_contents('./VERSION.md');

$top_n = '25';
$heart_n = '50';
$base_n = '25';
?>

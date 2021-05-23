<?php 
if (!defined('pvault_panel')){ die('Not Found');}


function priceScrape($url,$size,$start_tag,$end_tag,$extras){
	
	$html = file_get_contents($url);
	$start = stripos($html, htmlspecialchars_decode($start_tag));
	$end = stripos($html, htmlspecialchars_decode($end_tag), $offset = $start);
	$length = $end - $start;
	$result = substr($html, $start, $length);
	
	return preg_replace("/[^0-9.,]/", "", $result)*$size+$extras;
}

?>
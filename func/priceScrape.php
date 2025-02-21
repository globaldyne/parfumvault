<?php 
if (!defined('pvault_panel')){ die('Not Found');}


function priceScrape($url, $size, $start_tag, $end_tag, $extras, $price_per_size) {
	if ($price_per_size == '0') {
		$size = 1;
	}

	$html = @file_get_contents($url);
	if ($html === FALSE) {
		error_log("Error: Unable to retrieve content from the URL: $url");
		return false;
	}

	$start = stripos($html, htmlspecialchars_decode($start_tag));
	if ($start === FALSE) {
		error_log("Error: Start tag not found in the content from URL: $url");
		return false;
	}

	$end = stripos($html, htmlspecialchars_decode($end_tag), $offset = $start);
	if ($end === FALSE) {
		error_log("Error: End tag not found in the content from URL: $url");
		return false;
	}

	$length = $end - $start;
	$result = substr($html, $start, $length);
	if ($result === FALSE) {
		error_log("Error: Unable to extract the content between the tags from URL: $url");
		return false;
	}

	$price = preg_replace("/[^0-9.,]/", "", $result);
	if ($price === NULL) {
		error_log("Error: Unable to parse the price from the content from URL: $url");
		return false;
	}

	$fetched_price = (double)$price * $size + (double)$extras;
	if ($fetched_price == 0) {
		error_log("Error: Fetched price is 0 from URL: $url");
		return false;
	}

	return $fetched_price;
}

?>
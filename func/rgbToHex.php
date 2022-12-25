<?php
if (!defined('pvault_panel')){ die('Not Found');}

function rgb_to_hex(string $rgba) : string {
	if(strpos($rgba, '#') === 0){
		return $rgba;
	}
	
	preg_match('/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i',$rgba,$by_color);
	return sprintf('#%02x%02x%02x', $by_color[1], $by_color[2], $by_color[3]);
}

?>
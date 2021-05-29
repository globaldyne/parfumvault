<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function labelMap($label_printer_size){
	
	switch($label_printer_size) {
		case "12":
			$dim = "106,106";
			break;	
		
		case "29":
			$dim = "306,306";
			break;
			
		case "38":
			$dim = "413,413";
			break;	
			
		case "50":
			$dim = "554,554";
			break;
			
		case "54":
			$dim = "590,590";
			break;
			
		case "62":
			$dim = "720,820";
			break;
			
		case "62red":
			$dim = "720,820";
			break;
			
		case "102":
			$dim = "1164,1164";
			break;
			
		case "17x54":
			$dim = "566,165";
			break;
			
		case "17x87":
			$dim = "956,165";
			break;
			
		case "23x23":
			$dim = "202,202";
			break;
			
		case "29x42":
			$dim = "425,306";
			break;
			
		case "29x90":
			$dim = "991,306";
			break;	

		case "39x90":
			$dim = "991,413";
			break;
			
		case "39x48":
			$dim = "495,425";
			break;
			
		case "52x29":
			$dim = "271,578";
			break;
			
		case "62x29":
			$dim = "271,578";
			break;
			
		case "62x100":
			$dim = "1109,696";
			break;
			
		case "102x51":
			$dim = "526,1164";
			break;
			
		case "102x152":
			$dim = "1660,1164";
			break;
			
		case "d12":
			$dim = "94,94";
			break;
			
		case "d24":
			$dim = "236,236";
			break;
			
		case "d58":
			$dim = "618,618";
			break;
			
		case "pt24":
			$dim = "128,128";
			break;

			
		default:
			$dim = "720,260";
	}
	
	return $dim;
}

?>
<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');

if($_GET['formula']){
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
	
}elseif($_GET['action'] == 'printLabel' && $_GET['name']){
	$lbl = imagecreatetruecolor(720, 260);

	$white = imagecolorallocate($lbl, 255, 255, 255);
	$black = imagecolorallocate($lbl, 0, 0, 0);	
	
	imagefilledrectangle($lbl, 0, 0, 720, 260, $white);
	
	$text = trim($_GET['name']);
	$font = '../fonts/Arial.ttf';
	
	imagettftext($lbl, $settings['label_printer_font_size'], 0, 0, 150, $black, $font, $text);
	$lblF = imagerotate($lbl, 90 ,0);
	
	$save = "../tmp/labels/".base64_encode($text.'png');
	if(imagepng($lblF, $save)){
		imagedestroy($lblF);
		shell_exec('/usr/bin/brother_ql -m '.$settings['label_printer_model'].' -p tcp://'.$settings['label_printer_addr'].' print -l '.$settings['label_printer_size'].' '. $save);
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Print sent!</div>';
	}
}

?>
<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');

if($_GET['formula'] && $_GET['do']){
	$formula = mysqli_real_escape_string($conn, $_GET['formula']);
	
	$q = mysqli_query($conn, "SELECT quantity,ingredient FROM formulas WHERE name = '$formula'");
	while($cur =  mysqli_fetch_array($q)){
		if($_GET['do'] == 'multiply'){
			$nq = $cur['quantity']*2;
		}elseif($_GET['do'] == 'divide'){
			$nq = $cur['quantity']/2;
		}
		
		if(empty($nq)){
			print 'error';
			return;
		}
		
		mysqli_query($conn,"UPDATE formulas SET quantity = '$nq' WHERE name = '$formula' AND quantity = '$cur[quantity]' AND ingredient = '$cur[ingredient]'");
	}
	header("Location: /?do=Formula&name=$formula");

//DELETING

}elseif($_GET['action'] == 'deleteIng' && $_GET['ingID'] && $_GET['ing']){
	$id = mysqli_real_escape_string($conn, $_GET['ingID']);
	$ing = mysqli_real_escape_string($conn, $_GET['ing']);
	$fname = mysqli_real_escape_string($conn, $_GET['fname']);
	if(mysqli_query($conn, "DELETE FROM formulas WHERE id = '$id' AND name = '$fname'")){
		
		echo  '<div class="alert alert-success alert-dismissible">
				<a href="?do=Formula&name='.$fname.'" class="close" data-dismiss="alert" aria-label="close">x</a>
				'.$ing.' removed from the formula!
				</div>';
	}else{
		echo  '<div class="alert alert-danger alert-dismissible">
				<a href="?do=Formula&name='.$fname.'" class="close" data-dismiss="alert" aria-label="close">x</a>
				'.$ing.' cannot be removed from the formula!
				</div>';
	}
//ADDING
}elseif($_GET['action'] == 'addIng' && $_GET['fname']){// && $_GET['quantity'] && $_GET['ingredient']){
	$fname = mysqli_real_escape_string($conn, $_GET['fname']);
	$ingredient = mysqli_real_escape_string($conn, $_GET['ingredient']);
	$quantity = mysqli_real_escape_string($conn, $_GET['quantity']);
	$concentration = mysqli_real_escape_string($conn, $_GET['concentration']);
	
	if (empty($quantity) || empty($concentration)){
		echo '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error: </strong>Missing fields</div>';
	}else
		
	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '$ingredient' AND name = '$fname'"))){
		echo '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error: </strong>'.$ingredient.' already exists in formula!
		'.mysqli_error($conn).'
		</div>';
	}else{

		if(mysqli_query($conn,"INSERT INTO formulas(fid,name,ingredient,ingredient_id,concentration,quantity) VALUES('".base64_encode($fname)."','$fname','$ingredient','$ingredient_id','$concentration','$quantity')")){
			echo '<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
					'.$ingredient.' added to formula!
					</div>';
		}else{
			echo '<div class="alert alert-danger alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
					Error adding '.$ingredient.'!
					</div>';
		}
	}
	
//CLONE
}elseif($_GET['action'] == 'clone' && $_GET['formula']){
	$fname = mysqli_real_escape_string($conn, $_GET['formula']);
	$fid = base64_encode($fname);
	$newName = $fname.' - (Copy)';
	$newFid = base64_encode($newName);
		if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE fid = '$newFid'"))){
			echo '<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  				<strong>Error: </strong>'.$newName.' already exists, please remove or rename it first!</div>';
		}else{
			$sql.=mysqli_query($conn, "INSERT INTO formulasMetaData (fid, name, notes, profile, image, sex) SELECT '$newFid', '$newName', notes, profile, image, sex FROM formulasMetaData WHERE fid = '$fid'");
			$sql.=mysqli_query($conn, "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, quantity) SELECT '$newFid', '$newName', ingredient, ingredient_id, concentration, quantity FROM formulas WHERE fid = '$fid'");
		}
	if($sql){
		echo '<div class="alert alert-success alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
			'.$fname.' cloned as '.$newName.'!
			</div>';
	}
	
	
//PRINTING
}elseif($_GET['action'] == 'printLabel' && $_GET['name']){
	
	if($settings['label_printer_size'] == '62' || $settings['label_printer_size'] == '62 --red'){
		$name = mysqli_real_escape_string($conn, $_GET['name']);
		$q = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE name = '$name'"));
		$info = "Production: ".date("d/m/Y")."\nProfile: ".$q['profile']."\nSex: ".$q['sex']."\nDescription:\n\n".wordwrap($q['notes'],30);
		$w = '720';
		$h = '860';
	}else{
		$w = '720';
		$h = '260';
	}
		
	$lbl = imagecreatetruecolor($w, $h);

	$white = imagecolorallocate($lbl, 255, 255, 255);
	$black = imagecolorallocate($lbl, 0, 0, 0);	
	
	imagefilledrectangle($lbl, 0, 0, $w, $h, $white);
	
	$text = trim($_GET['name']);
	$font = '../fonts/Arial.ttf';

	imagettftext($lbl, $settings['label_printer_font_size'], 0, 0, 150, $black, $font, $text);
	$lblF = imagerotate($lbl, 90 ,0);
	
	if($settings['label_printer_size'] == '62' || $settings['label_printer_size'] == '62 --red'){
		imagettftext($lblF, 25, 0, 200, 300, $black, $font, $info);
	}
	$save = "../tmp/labels/".base64_encode($text.'png');

	if(imagepng($lblF, $save)){
		imagedestroy($lblF);
		shell_exec('/usr/bin/brother_ql -m '.$settings['label_printer_model'].' -p tcp://'.$settings['label_printer_addr'].' print -l '.$settings['label_printer_size'].' '. $save);
		//echo '<img src="'.$save.'"/>';
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Print sent!</div>';
	}
}

?>
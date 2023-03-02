<?php
define('__ROOT__', dirname(dirname(__FILE__))); 
define('FPDF_FONTPATH',__ROOT__.'/fonts');

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

require_once(__ROOT__.'/libs/fpdf.php');
require_once(__ROOT__.'/libs/Html2Pdf.php');

define('__PVLOGO__', __ROOT__.'/img/logo.png');
define('__BRANDLOGO__', __ROOT__.'/'.$settings['brandLogo']);

if ($_POST['action'] == 'generateSDS' && $_POST['kind'] == 'ingredient'){
	
	$ingName = mysqli_real_escape_string($conn, $_POST['name']);
	$ingID = $_POST['id'];
	$tmplID = $_POST['tmpl'];
	
	$tmpl = mysqli_fetch_array(mysqli_query($conn,"SELECT name,content FROM templates WHERE id = '$tmplID'"));
	$ingData = mysqli_fetch_array(mysqli_query($conn,"SELECT cas,INCI,reach,einecs,chemical_name,formula,flash_point,appearance FROM ingredients WHERE id = '$ingID'"));

	$search  = array(
					 '%LOGO%',
					 '%BRAND_NAME%',
					 '%BRAND_ADDRESS%',
					 '%BRAND_EMAIL%',
					 '%BRAND_PHONE%',
					 '%INGREDIENT_NAME%',
					 '%CAS%',
					 '%IUPAC%',
					 '%REACH%',
					 '%EINECS%',
					 '%CHEMICAL_NAME%',
					 '%FORMULA%',
					 '%FLASH_POINT%',
					 '%APPEARANCE%',
					 
					 '%CURRENT_DATE%'
					 );

	$replace = array(
					 __PVLOGO__,
					 $settings['brandName'],
					 $settings['brandAddress'], 
					 $settings['brandEmail'], 
					 $settings['brandPhone'], 
					 $ingName,
					 $ingData['cas'],
					 $ingData['INCI'],
					 $ingData['reach'],
					 $ingData['einecs'],
					 $ingData['chemical_name'],
					 $ingData['formula'],
					 $ingData['flash_point'],
					 $ingData['appearance'],

					 date('d/M/Y')
					 );
	
	
	$contents =  str_replace( $search, $replace, $tmpl['content'] );

//	str_replace( $search, $replace, preg_replace('#(%IFRA_MATERIALS_LIST%)#ms', $x, $tmpl['content']) );

class PDF extends PDF_HTML{
	function Header() {
		$this->Image(__BRANDLOGO__,10,6,30,0,'png');
		$this->SetFont('Arial','B',15);
		$this->Cell(80);
		$this->Cell(40,10,'GHS Safety Data Sheet',0,0,'C');
		$this->Ln(20);
	}

	function Footer(){
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->MultiCell(190,10,$pdf->WriteHTML($contents));

$content = mysqli_real_escape_string($conn,$pdf->Output("S"));
//DIRTY WAY TO CLEANUP //TODO
mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$ingID' AND type = '0' AND notes = 'PV Generated'");

if(mysqli_query($conn, "INSERT INTO documents(ownerID,type,name,docData,notes) values('$ingID','0','$ingName','$content','PV Generated')")){
	$response["success"] = '<a href="/pages/viewDoc.php?id='.mysqli_insert_id($conn).'&type=internal" target="_blank">View file</a>';
}else{
	$response["error"] = "Unable to generate PDF";
}


echo json_encode($response);
	return;	
}

?>
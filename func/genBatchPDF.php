<?php
if (!defined('pvault_panel')){ die('Not Found');}
function genBatchPDF($fid, $batchID, $bottle, $new_conc, $mg, $ver, $conn){
	class PDF extends FPDF {
		function Header() {
			global $fid;
			global $batchID;
			$this->Image('img/logo.png',10,-1,30);
			//TITLE
			$this->SetFont('Arial','B',13);
			$this->Cell(60);
			$this->Cell(130,10,base64_decode($fid),0,0,'C');
			$this->Cell(60);
			//SUB
			$this->SetY(12);
			$this->SetFont('Arial','I',8);
			$this->Cell(250,5,$batchID,0,0,'C');
			$this->Ln(20);
		}
				
		function Footer() {
			global $ver;
			$this->SetY(-15);
			$this->SetFont('Arial','I',8);
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb} - Perfumers Vault Pro '.$ver,0,0,'C');
		}
	}		

	$display_heading = array('name'=> 'Product', 'ingredient'=> 'Ingredient','concentration'=> 'Concentration',);	
	$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE fid = '$fid' ORDER BY ingredient ASC");

	$header = array('Ingredient', 'CAS#', 'Purity %', 'Quantity', 'Concentration %');
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid'"));
	
	$fq = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid'");
	while($ing = mysqli_fetch_array($fq)){
		$getAllergen = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '".$ing['ingredient']."' AND allergen = '1'"));
		$allergen[] = $getAllergen['name'];
	}
	
	$coverText = "Profile: ".$meta['profile']." \nSex: ".$meta['sex']." \nCreated: ".$meta['created']." \n".$meta['notes'];
	$allergenFinal = implode(", ",array_filter($allergen));
	if(empty($allergenFinal)){
		$allergenFinal = 'None found';
	}
	$finalText = "Allergens and/or ingredients to be declared in the box: \n".$allergenFinal;
	
	$pdf = new PDF( 'L', 'mm', 'A4');

	$pdf->SetAutoPageBreak(true , 30);
	$pdf->SetMargins(20, 1, 20);
	
	//Cover page
	$pdf->AddPage();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','B',10);
	$pdf->MultiCell(250,10,$coverText);
	
	//Formula table
	$pdf->AddPage();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','B',10);
	
	foreach($header as $heading) {
		$pdf->Cell(55,12,$heading,1,0,'C');
	}

	while ($formula = mysqli_fetch_array($formula_q)) {
		$pdf->Ln();
		$pdf->SetFont('Arial','',9);
					  
		$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT IFRA,price,ml,profile,profile,cas FROM ingredients WHERE name = '".$formula['ingredient']."'"));
		$new_quantity = $formula['quantity']/$mg*$new_conc;
		$conc = $new_quantity/$bottle * 100;
		$conc_p = number_format($formula['concentration'] / 100 * $conc, 3);
		
		$pdf->Cell(55,8,$formula['ingredient'],1,0,'C');
		$pdf->Cell(55,8,$ing_q['cas'],1,0,'C');
		$pdf->Cell(55,8,$formula['concentration'],1,0,'C');
		
		$pdf->Cell(55,8,number_format($new_quantity, 3),1,0,'C');
		$pdf->Cell(55,8,$conc_p,1,0,'C');
	}
                 
	//ADD Final details
	$pdf->AddPage();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','B',10);
	$pdf->MultiCell(250,10,$finalText);
	
	$pdf->Output('F','batches/'.$batchID);
	//$pdf->Output('I');
}

?>

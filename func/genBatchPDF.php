<?php
if (!defined('pvault_panel')){ die('Not Found');}
function genBatchPDF($fid, $batchID, $conn){
	class PDF extends FPDF {
		function Header() {
			global $fid;
			global $batchID;
			$this->Image('./img/logo.png',10,-1,30);
			$this->SetFont('Arial','B',13);
			$this->Cell(60);
			$this->Cell(60,10,base64_decode($fid),0,0,'C');
			$this->Cell(60);
			$this->SetY(12);
			$this->SetFont('Arial','I',8);
			$this->Cell(180,20,$batchID,0,0,'C');
			$this->Ln(20);
		}
				
		function Footer() {
			$this->SetY(-15);
			$this->SetFont('Arial','I',8);
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		}
	}		

	$display_heading = array('name'=> 'Product', 'ingredient'=> 'Ingredient','concentration'=> 'Concentration',);
	$result = mysqli_query($conn, "SELECT ingredient, concentration, quantity FROM formulas WHERE fid = '$fid'");
	
	$header = array('Ingredient', 'CAS#', 'Purity %', 'Quantity', 'Concentration %');

	$pdf = new PDF( 'L', 'mm', 'A4');

	$pdf->SetAutoPageBreak(true , 30);
	$pdf->SetMargins(20, 1, 20);

	$pdf->AddPage();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','B',10);
	
	foreach($header as $heading) {
		$pdf->Cell(55,12,$heading,1,0,'C');
	}
		
	foreach($result as $row) {
		$pdf->Ln();
		$pdf->SetFont('Arial','',9);

		foreach($row as $column)
		//$pdf->MultiCell( 200, 40, $reportSubtitle, 1);

			$pdf->Cell(55,8,$column,1,0,'C');
	}
	//$pdf->Output('F','batches/'.$batchID);
	$pdf->Output('I');

}

?>
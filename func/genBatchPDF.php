<?php
if (!defined('pvault_panel')){ die('Not Found');}

function genBatchPDF($fid, $batchID, $conn){
	class PDF extends FPDF {
		function Header() {
			global $fid;
			$this->Image('./img/logo.png',10,-1,30);
			$this->SetFont('Arial','B',13);
			$this->Cell(80);
			$this->Cell(80,10,base64_decode($fid),0,0,'C');
			$this->Cell(90,10,base64_decode($fid),0,0,'C');
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
	$header = array('Ingredient', 'Purity', 'Quantity');

	$pdf = new PDF();

	$pdf->AddPage();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','B',10);
	
	foreach($header as $heading) {
		$pdf->Cell(80,12,$heading,1);
	}
		
	foreach($result as $row) {
		$pdf->Ln();
		foreach($row as $column)
			$pdf->Cell(80,10,$column,1);
	}
	//$pdf->Output('F','batches/'.$batchID);
	$pdf->Output('F','batches/1.pdf');

}

?>
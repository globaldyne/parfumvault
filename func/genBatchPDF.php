<?php
if (!defined('pvault_panel')){ die('Not Found');}
function genBatchPDF($fid, $batchID, $bottle, $new_conc, $mg, $ver, $uploads_path, $defCatClass, $qStep, $conn){
	class PDF extends FPDF {
		function Header() {
			global $fid;
			global $batchID;
			global $prodName;
			$this->Image('img/logo.png',10,-1,30);
			//TITLE
			$this->SetFont('Arial','B',13);
			$this->Cell(60);
			$this->Cell(130,10,$prodName,0,0,'C');
			$this->Cell(60);
			//SUB
			$this->SetY(12);
			$this->SetFont('Arial','I',8);
			$this->Cell(250,5,$batchID,0,0,'C');
			$this->Ln(20);
			//Barcode
			$this->SetY(12);
			$this->SetFont('Arial','I',8);
			$this->Code39(80,20,$batchID,1,10);
			$this->Ln(30);

		}
				
		function Footer() {
			global $ver;
			$this->SetY(-15);
			$this->SetFont('Arial','I',8);
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb} - Perfumers Vault Pro '.$ver,0,0,'C');
		}

		function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5){

			$wide = $baseline;
			$narrow = $baseline / 3 ; 
			$gap = $narrow;
		
			$barChar['0'] = 'nnnwwnwnn';
			$barChar['1'] = 'wnnwnnnnw';
			$barChar['2'] = 'nnwwnnnnw';
			$barChar['3'] = 'wnwwnnnnn';
			$barChar['4'] = 'nnnwwnnnw';
			$barChar['5'] = 'wnnwwnnnn';
			$barChar['6'] = 'nnwwwnnnn';
			$barChar['7'] = 'nnnwnnwnw';
			$barChar['8'] = 'wnnwnnwnn';
			$barChar['9'] = 'nnwwnnwnn';
			$barChar['A'] = 'wnnnnwnnw';
			$barChar['B'] = 'nnwnnwnnw';
			$barChar['C'] = 'wnwnnwnnn';
			$barChar['D'] = 'nnnnwwnnw';
			$barChar['E'] = 'wnnnwwnnn';
			$barChar['F'] = 'nnwnwwnnn';
			$barChar['G'] = 'nnnnnwwnw';
			$barChar['H'] = 'wnnnnwwnn';
			$barChar['I'] = 'nnwnnwwnn';
			$barChar['J'] = 'nnnnwwwnn';
			$barChar['K'] = 'wnnnnnnww';
			$barChar['L'] = 'nnwnnnnww';
			$barChar['M'] = 'wnwnnnnwn';
			$barChar['N'] = 'nnnnwnnww';
			$barChar['O'] = 'wnnnwnnwn'; 
			$barChar['P'] = 'nnwnwnnwn';
			$barChar['Q'] = 'nnnnnnwww';
			$barChar['R'] = 'wnnnnnwwn';
			$barChar['S'] = 'nnwnnnwwn';
			$barChar['T'] = 'nnnnwnwwn';
			$barChar['U'] = 'wwnnnnnnw';
			$barChar['V'] = 'nwwnnnnnw';
			$barChar['W'] = 'wwwnnnnnn';
			$barChar['X'] = 'nwnnwnnnw';
			$barChar['Y'] = 'wwnnwnnnn';
			$barChar['Z'] = 'nwwnwnnnn';
			$barChar['-'] = 'nwnnnnwnw';
			$barChar['.'] = 'wwnnnnwnn';
			$barChar[' '] = 'nwwnnnwnn';
			$barChar['*'] = 'nwnnwnwnn';
			$barChar['$'] = 'nwnwnwnnn';
			$barChar['/'] = 'nwnwnnnwn';
			$barChar['+'] = 'nwnnnwnwn';
			$barChar['%'] = 'nnnwnwnwn';
		
		//	$this->SetFont('Arial','',10);
		//	$this->Text($xpos, $ypos + $height + 4, $code);
		//	$this->SetFillColor(0);
		
			$code = '*'.strtoupper($code).'*';
			for($i=0; $i<strlen($code); $i++){
				$char = $code[$i];
				if(!isset($barChar[$char])){
					$this->Error('Invalid character in barcode: '.$char);
				}
				$seq = $barChar[$char];
				for($bar=0; $bar<9; $bar++){
					if($seq[$bar] == 'n'){
						$lineWidth = $narrow;
					}else{
						$lineWidth = $wide;
					}
					if($bar % 2 == 0){
						$this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
					}
					$xpos += $lineWidth;
				}
				$xpos += $gap;
			}
		}

}		

	$display_heading = array('name'=> 'Product', 'ingredient'=> 'Ingredient','concentration'=> 'Concentration',);	
	$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE fid = '$fid' ORDER BY ingredient ASC");

	$header = array('Ingredient', 'CAS#', 'Purity %', 'Dilutant', 'Quantity', 'Concentration %');
	$hd_blends = array('Ingredient', 'Contains','CAS#', 'Concentration %');

	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid'"));
	
	$fq = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid'");
	while($ing = mysqli_fetch_array($fq)){
		$getIngAlergen = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '".$ing['ingredient']."' AND allergen = '1'"));
		$qAll = mysqli_query($conn, "SELECT name FROM allergens WHERE ing = '".$ing['ingredient']."'");
		
		while($getAllergen = mysqli_fetch_array($qAll)){
			$allergen[] = $getAllergen['name'];
		}
		$allergen[] = $getIngAlergen['name'];
		$allergen[] = $getAllergen['name'];
	}
	
	$coverText = "Profile: ".$meta['profile']." \nSex: ".$meta['sex']." \nCreated: ".$meta['created']." \n".$meta['notes'];
	$allergenFinal = implode(", ",array_unique(array_filter($allergen)));
	if(empty($allergenFinal)){
		$allergenFinal = 'None found';
	}
	
	$pdf = new PDF( 'L', 'mm', 'A4');

	$pdf->SetAutoPageBreak(true , 15);
	$pdf->SetMargins(10, 1, 5);
	
	//Cover page
	$pdf->AddPage();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','B',10);
	$pdf->MultiCell(250,10,$coverText);
	
	//Barcode
	//$pdf->Code39(80,40,$batchID,1,10);

	//Formula table
	$pdf->AddPage();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','B',10);
	
	foreach($header as $heading) {
		$pdf->Cell(45,12,$heading,1,0,'C');
	}

	while ($formula = mysqli_fetch_array($formula_q)) {
		$pdf->Ln();
		$pdf->SetFont('Arial','',9);
					  
		$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT $defCatClass,profile,cas FROM ingredients WHERE name = '".$formula['ingredient']."'"));
		$new_quantity = $formula['quantity']/$mg*$new_conc;
		$conc = $new_quantity/$bottle * 100;
		$conc_p = number_format($formula['concentration'] / 100 * $conc, $qStep);
		
		$pdf->Cell(45,8,$formula['ingredient'],1,0,'C');
		$pdf->Cell(45,8,$ing_q['cas'],1,0,'C');
		$pdf->Cell(45,8,$formula['concentration'],1,0,'C');
		if($formula['dilutant']){
			$pdf->Cell(45,8,$formula['dilutant'],1,0,'C');
		}else{
			$pdf->Cell(45,8,'None',1,0,'C');			
		}
		$pdf->Cell(45,8,number_format($new_quantity, $qStep),1,0,'C');
		$pdf->Cell(45,8,$conc_p,1,0,'C');
	}
               
		
	//ADD Final details
	$pdf->AddPage();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','BU',10);
	$pdf->MultiCell(250,10,"Allergens and/or ingredients to be declared in the box: \n");

	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(280,10,$allergenFinal);
	
	
	
		//Blends or mixed bases
	$pdf->AddPage();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','BU',10);
	$pdf->MultiCell(250,10,"Also contains \n");

	foreach($hd_blends as $heading) {
		$pdf->Cell(68,12,$heading,1,0,'C');
	}

	$qAllIng = mysqli_query($conn, "SELECT ingredient,quantity,concentration FROM formulas WHERE fid = '$fid'");
	while ($res_all_ing = mysqli_fetch_array($qAllIng)) {
		
		$bldQ = mysqli_query($conn, "SELECT ing,name,cas,percentage FROM allergens WHERE ing = '".$res_all_ing['ingredient']."'");
		while($bld = mysqli_fetch_array($bldQ)){
			$pdf->Ln();
			$pdf->SetFont('Arial','',8);

			$pdf->Cell(68,8,$bld['ing'],1,0,'C');

			$pdf->Cell(68,8,$bld['name'],1,0,'C');
			$pdf->Cell(68,8,$bld['cas'],1,0,'C');
			$pdf->Cell(68,8,$bld['percentage'],1,0,'C');
		}
	}	
	
	$pdf->Output('F',$uploads_path.'batches/'.$batchID);
	//$pdf->Output('I');
}

?>

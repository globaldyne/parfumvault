<?php
define('__ROOT__', dirname(dirname(__FILE__))); 
define('FPDF_FONTPATH',__ROOT__.'/fonts');

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/libs/fpdf.php');

if (!file_exists($tmp_path)) {
	mkdir($tmp_path, 0740, true);
}

$branding = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM branding WHERE owner_id = '$userID'"));
if($branding['brandLogo']){
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	$imageData = base64_decode(explode(',', $branding['brandLogo'])[1]);
	$imageType = $finfo->buffer($imageData);
	$extension = '';

	switch ($imageType) {
		case 'image/jpeg':
			$extension = 'jpg';
			break;
		case 'image/png':
			$extension = 'png';
			break;
		case 'image/gif':
			$extension = 'gif';
			break;
		default:
			$tempImagePath =  __ROOT__.'/img/logo.png';
			break;
	}

	if ($extension) {
		$tempImagePath = $tmp_path.'/temp_logo_' . bin2hex(random_bytes(8)) . '.' . $extension;
		file_put_contents($tempImagePath, $imageData);
	}
} else {
	$tempImagePath =  __ROOT__.'/img/logo.png';
}

define('__PVLOGO__', $tempImagePath ?:  __ROOT__.'/img/logo.png');

if ($_REQUEST['action'] == 'generateDOC' && $_REQUEST['kind'] == 'ingredient'){
	
	$ingName = mysqli_real_escape_string($conn, $_REQUEST['name']);
	$ingID = $_REQUEST['id'];
	define('__INGNAME__',$ingName);

	$ingredient_compounds_count = 0;
	
    // Fetch ingredient details
    $query = "SELECT * FROM ingredients WHERE id = ? AND owner_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $ingID, $userID);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
	
	$g = [
		'name' => (string)$res['name'],
		'INCI' => (string)$res['INCI'] ?: '-',
		'FEMA' => (string)$res['FEMA'] ?: '-',
		'einecs' => (string)$res['einecs'] ?: '-',
		'reach' => (string)$res['reach'] ?: '-',
		'notes' => (string)$res['notes'] ?: '-',
		'physical_state' => $res['physical_state'] === 1 ? 'Liquid' : ($res['physical_state'] === 2 ? 'Solid' : 'Unknown')
	];
	
	define('__INGCAS__', $res['cas'] ?: '-');
	
	$t = [
		'tenacity' => (string)$res['tenacity'] ?: '-',
		'chemical_name' => (string)$res['chemical_name'] ?: '-',
		'formula' => (string)$res['formula'] ?: '-',
		'flash_point' => (string)$res['flash_point'] ?: '-',
		'flavor_use' => (int)$res['flavor_use'],
		'soluble' => (string)$res['soluble'] ?: '-',
		'logp' => (string)$res['logp'] ?: '-',
		'appearance' => (string)$res['appearance'] ?: '-',
		'molecularWeight' => (string)$res['molecularWeight'] ?: '-'
	];
	
	$i = [
		'cat1' => (double)$res['cat1'],
		'cat2' => (double)$res['cat2'],
		'cat3' => (double)$res['cat3'],
		'cat4' => (double)$res['cat4'],
		'cat5A' => (double)$res['cat5A'],
		'cat5B' => (double)$res['cat5B'],
		'cat5C' => (double)$res['cat5C'],
		'cat6' => (double)$res['cat6'],
		'cat7A' => (double)$res['cat7A'],
		'cat7B' => (double)$res['cat7B'],
		'cat8' => (double)$res['cat8'],
		'cat9' => (double)$res['cat9'],
		'cat10A' => (double)$res['cat10A'],
		'cat10B' => (double)$res['cat10B'],
		'cat11A' => (double)$res['cat11A'],
		'cat11B' => (double)$res['cat11B'],
		'cat12' => (double)$res['cat12']
	];
	
	$ing[] = $g;
	$ifra[] = $i;
	$tech[] = $t;
	
    // Fetch ingredient compounds
    $query = "SELECT * FROM ingredient_compounds WHERE ing = ? AND owner_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $g['name'], $userID);
    $stmt->execute();
    $result = $stmt->get_result();

	
	$cmp = [];
	while ($res = $result->fetch_assoc()) {
		$c = [
			'ing' => (string)$res['ing'],
			'name' => (string)$res['name'],
			'CAS' => (string)$res['cas'] ?: '-',
			'EINECS' => (string)$res['ec'] ?: '-',
			'Concentration (Average)' => $res['min_percentage'] + $res['max_percentage'] / 2,
			'GHS' => (string)$res['GHS'] ?: '-'
		];
		$cmp[] = $c;
		$ingredient_compounds_count++;
	}
	
    // Fetch ingredient synonyms
    $query = "SELECT synonym, source FROM synonyms WHERE ing = ? AND owner_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $g['name'], $userID);
    $stmt->execute();
    $result = $stmt->get_result();
	
	$syn = [];
	while ($res = $result->fetch_assoc()) {
		$c = [
			'synonym' => (string)$res['synonym'],
			'source' => (string)$res['source'],
		];
		$syn[] = $c;
	}
	


    // Fetch GHS information
    $query = "SELECT id, ingID, GHS FROM ingSafetyInfo WHERE ingID = ? AND owner_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $ingID, $userID);
    $stmt->execute();
    $result = $stmt->get_result();
	
	$ghs = [];
	while ($res = $result->fetch_assoc()) {
		$stmt_pictograms = $conn->prepare("SELECT name FROM pictograms WHERE code = ?");
		$stmt_pictograms->bind_param("s", $res['GHS']);
		$stmt_pictograms->execute();
		$ex = $stmt_pictograms->get_result()->fetch_assoc();
	
		$s = [
			'GHS' => (int)$res['GHS'],
			'name' => (string)$ex['name']
		];
		$ghs[] = $s;
	}
	
	// Prepare data for PDF
	$vd = [
		'product' => $product,
		'version' => $ver,
		'ingredients' => $ingredient,
		'timestamp' => date('d/m/Y H:i:s')
	];
	
	$result = [
		'General' => $ing,
		'GHS' => $ghs,
		'IFRA' => $ifra,
		'Technical Data' => $tech,
		'Compositions' => $cmp,
		'Synonyms' => $syn,
		'contact' => [
			"Name" => $branding['brandName'],
			"Address" => $branding['brandAddress'],
			"Email" => $branding['brandEmail'],
			"Phone" => $branding['brandPhone']
		]
	];
	
	$json_data = json_encode($result);
	$data = json_decode($json_data, true);
	
	// PDF generation
	
	class PDF extends FPDF {
		private $logoPath;
	
		function __construct($logoPath) {
			parent::__construct();
			$this->logoPath = $logoPath;
		}
	
		function Header() {
			$this->Image($this->logoPath, 10, 6, 30);
			$this->Ln(20);
	
			$this->SetFont('Arial', 'B', 14);
			$this->Cell(0, 8, __INGNAME__, 0, 1, 'C');
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(0, 5, "CAS: " . __INGCAS__, 0, 1, 'C');
	
			$this->Ln(5);
		}
	
		function Footer() {
			$this->SetY(-20);
			$this->Cell(0, 10, 'Page ' . $this->PageNo() . ' of {nb}', 0, 1, 'C');
	
			$footerText = 'This document is generated by calculation based on the ingredients present in the formula. The information contained herein is, to the best of our knowledge, true and accurate at the date of issue. It is provided to the customer for information and internal use only. It is not a confirmation of compliance and it is customer responsibility to perform his own evaluation on the product, including with respect to end-use application.';
			$this->SetFont('Arial', '', 5);
			$this->MultiCell(0, 3, $footerText, 0, 'C');
		}
	}
	
	$pdf = new PDF(__PVLOGO__);
	
	$pdf->AliasNbPages();
	$pdf->AddPage();
	
	function addSection($pdf, $title, $content) {
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetFillColor(3, 189, 123);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->Cell(0, 10, $title, 0, 1, 'L', true);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('Arial', '', 8);
	
		if ($title == 'contact' && is_array($content) && !empty($content)) {
			foreach ($content as $value) {
				$pdf->Cell(0, 10, $value, 0, 1);
			}
			$pdf->Ln();
		} else if ($title == 'Compositions' && is_array($content) && !empty($content)) {
			$pdf->SetFillColor(211, 211, 211); // Light gray
			$headers = array_keys($content[0]);
			$headers = array_diff($headers, ['ing']);
			
			foreach ($headers as $header) {
				$pdf->Cell(38, 8, ucfirst($header), 1, 0, 'C', true);
			}
			$pdf->Ln();
	
			$pdf->SetFont('Arial', '', 8);
			foreach ($content as $row) {
				foreach ($headers as $header) {
					$cellText = $header == 'Concentration' ? $row[$header] . '%' : $row[$header];
					$pdf->Cell(38, 10, $cellText, 1);
				}
				$pdf->Ln();
			}
		
		} else if ($title == 'Synonyms' && is_array($content) && !empty($content)) {
			$pdf->SetFillColor(211, 211, 211); // Light gray
			$headers = array_keys($content[0]);
			
			foreach ($headers as $header) {
				$pdf->Cell(95, 10, ucfirst($header), 1, 0, 'C', true);
			}
			$pdf->Ln();
	
			$pdf->SetFont('Arial', '', 7);
			foreach ($content as $row) {
				foreach ($headers as $header) {
					$cellText =  $row[$header];
					$pdf->Cell(95, 5, $cellText, 1);
				}
				$pdf->Ln();
			}
			
		} else if ($title == 'General' && is_array($content) && !empty($content)) {
			foreach ($content as $ingredient) {
				foreach ($ingredient as $key => $value) {
					if (!in_array($key, ['name'])) {
						$pdf->Cell(0, 10, ucfirst($key) . ': ' . $value, 0, 1);
					}
				}
				$pdf->Ln();
			}
		} else if ($title == 'Technical Data' && is_array($content) && !empty($content)) {
			foreach ($content as $ingredient) {
				foreach ($ingredient as $key => $value) {
					if (!in_array($key, ['name'])) {
						$pdf->Cell(0, 10, ucfirst($key) . ': ' . $value, 0, 1);
					}
				}
				$pdf->Ln();
			}
		} else if ($title == 'GHS' && is_array($content) && !empty($content)) {
			foreach ($content as $ingredient) {
				foreach ($ingredient as $key => $value) {
					if (!in_array($key, ['name'])) {
						$pdf->Cell(0, 10, ucfirst($key) . ': ' . $value, 0, 1);
						$imageX = $pdf->GetX();
						$imageY = $pdf->GetY();
						foreach (explode(' ', $value) as $image) {
							$pdf->Image(__ROOT__ . '/img/Pictograms/GHS0' . $value . '.png', $imageX, $imageY, 10);
							$imageX += 12;
	
						}
						$pdf->Ln(5);
					}
				}
				$pdf->Ln();
			}
		} else if ($title == 'IFRA' && is_array($content) && !empty($content)) {
			$pdf->SetFillColor(211, 211, 211); // Light gray
			$pdf->Cell(95, 10, 'Category', 1, 0, 'C', true);
			$pdf->Cell(95, 10, 'Percentage', 1, 1, 'C', true);
			
			foreach ($content[0] as $key => $value) {
				$pdf->Cell(95, 10, ucfirst($key), 1, 0, 'C');
				$pdf->Cell(95, 10, $value, 1, 1, 'C');
			}
		} else {
			foreach ($content as $key => $value) {
				if (is_array($value)) {
					$pdf->Cell(0, 10, "$key:", 0, 1);
					foreach ($value as $subKey => $subValue) {
						if (!in_array($subKey, ['id', 'ing'])) {
							$pdf->Cell(0, 10, "    $subKey: $subValue", 0, 1);
						}
					}
				} else {
					if (!in_array($key, ['id', 'ing'])) {
						$pdf->Cell(0, 10, "$key: $value", 0, 1);
					}
				}
			}
		}
		$pdf->Ln(10);
	}
	// Move the contact section to the beginning
	if (isset($data['contact'])) {
		addSection($pdf, 'Contact', $data['contact']);
		unset($data['contact']);
	}
	
	foreach ($data as $section => $content) {
		addSection($pdf, ucfirst($section), $content);
	}
	
	$content = mysqli_real_escape_string($conn, $pdf->Output("S"));


	//DIRTY WAY TO CLEANUP //TODO
	$content = mysqli_real_escape_string($conn, $pdf->Output("S"));
    $deleteQuery = "DELETE FROM documents WHERE ownerID = ? AND owner_id = ? AND type = 0 AND isSDS = 0 AND notes = 'PV Generated'";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("is", $ingID, $userID);
    $stmt->execute();
	
	if(mysqli_query($conn, "INSERT INTO documents(ownerID,type,name,docData,notes, owner_id) values('$ingID', 0, '$ingName', '$content', 'PV Generated', '$userID')")){
		$response["success"] = '<i class="fa-solid fa-file-pdf mr-2"></i><a href="/pages/viewDoc.php?id='.mysqli_insert_id($conn).'&type=internal" target="_blank">Download file</a>';
	}else{
		$response["error"] = "Unable to generate PDF";
	}

	echo json_encode($response);
	return;	
}

?>
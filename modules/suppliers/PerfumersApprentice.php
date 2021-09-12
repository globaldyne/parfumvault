<?php

/*

Material search for PA
Version: 1.0

*/

header('Content-Type: application/json; charset=UTF-8'); 

if(empty($_GET['q'])){
	echo json_encode(array('ERR' => 'Please define query.'));
	return;
}

$q = $_GET['q'];

$url = 'https://shop.perfumersapprentice.com';
$uri = 'ajaxCompunixtSearch.aspx?searchQuery='.$q;
//$url = 'https://vault.jbparfum.com';
//$uri = 'tests/dummy.html';

function myDOM($url,$uri){
	$dom = new DOMDocument();
	libxml_use_internal_errors(true);
	$dom->loadHTML(file_get_contents($url.'/'.$uri));
	$dom->preserveWhiteSpace = false;
	$xpath = new DOMXpath($dom);
	return $xpath;
}

$i = 0;
foreach(myDOM($url,$uri)->query('//li') as $element){
	$links=$element->getElementsByTagName('a');
	foreach($links as $a) {
		foreach(myDOM($url,$a->getAttribute('href'))->query('//div[@class="product-description"]|//a[@class="btn-doc btn-doc-default"] ') as $element2){
			
			$s = explode('CAS #',trim($element2->nodeValue));

		
		
		$CAS = preg_replace('/\s+/', '',explode('Safety Data Sheet', $s['0']['1']));
		$USAGE = preg_replace('/[^0-9.]/', '', explode('Use Level:',explode('%', $s['0']['0'])['0'])['1']);
		$ODOR = explode('.',explode('Odor Description:',explode('.Use level:',$s['0']['0'])['0'])['1']);
		}
		//print_r($ODOR);
		if($a->getAttribute('href')){
			$x[] = array(
				'<a href="'.$url.$a->getAttribute('href').'" target="_blank">'.preg_replace('/[^\00-\255]+/u', '', $a->nodeValue).'</a>',
				$CAS['0'],
				$ODOR['0'],
				'N/A',
				'N/A',
				$USAGE,
				'Perfumers Apprentice',
				'<a href="'.$url.$element2->getAttribute('href').'"target="_blank" class="fa fa-file-alt"></a>',
				'<a href="javascript:importING(\''.base64_encode($a->nodeValue).'\')">Import</a>'
			);
		}
	}

	$i++;
}

if(empty($x)){
	$x = [];
}
	
$json_data = array(
	"draw"            => intval( $i ),
	"recordsTotal"    => intval( $i ),
	"recordsFiltered" => intval( $i ),
	"data"            => $x
);
  
echo json_encode($json_data);

?>
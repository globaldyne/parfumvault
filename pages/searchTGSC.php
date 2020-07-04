<?php
//$url = 'http://www.thegoodscentscompany.com/search3.php?qName=476332-65-7';

$url = "http://www.thegoodscentscompany.com/search2.php?qName=".$_GET['name'];

$doc = new DOMDocument();
libxml_use_internal_errors(true);
$doc->loadHTMLFile($url);
$xpath = new DOMXpath($doc);
$elements = $xpath->query('//*/tr');
  foreach ($elements as $element) {
	$x[] = $element->nodeValue; 
  }
$l =  str_replace('Flavor', '',explode('Odor : ',$x[2]));
$k = explode(': ',$l['1']);
if ($k[0]){
	echo '<input name="odor" id="odor" type="text" class="form-control" value="'.$k['0'].'"/>';

}else{
	echo '<input name="odor" id="odor" type="text" class="form-control" placeholder="Not Found..."/>';
}
?>
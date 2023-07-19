<?php 
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/getIFRAMeta.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/validateFormula.php');
require_once(__ROOT__.'/func/calcPerc.php');

$bottle = $_GET['bottle'];
$type = $_GET['conc'];
$defCatClass = $settings['defCatClass'];

if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM IFRALibrary"))){
	echo 'You need to <a href="/?do=IFRA">import</a> the IFRA xls first.';
	return;
}

if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM templates"))){
	echo 'You need to <a href="/?do=settings">add</a> an IFRA template first.';
	return;
}

$defCatClass = mysqli_real_escape_string($conn, $_GET['defCatClass']);

if(empty($defCatClass)){
	$defCatClass = $settings['defCatClass'];
}
	
$fid = mysqli_real_escape_string($conn, $_GET['fid']);


$cid = mysqli_real_escape_string($conn, $_POST['customer']);
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid'"));
$customers = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM customers WHERE id = '$cid'"));

$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid'"));

$new_conc = $bottle/100*$type;

if(validateFormula($fid, $bottle, $new_conc, $mg['total_mg'], $defCatClass, $settings['qStep'], $conn) == TRUE){
	echo 'Error: Your formula contains materials, exceeding and/or missing IFRA standards. Please alter your formula and try again.';
	return;
}


if ( empty($settings['brandLogo']) ){ 
	$logo = "/img/logo.png";
}else{
	$logo = "/".$settings['brandLogo'];
}
if ( empty($settings['brandName']) || empty($settings['brandAddress']) || empty($settings['brandEmail']) || empty($settings['brandPhone']) ){
	echo 'Missing brand info, please update your brand details in <a href="/?do=settings">settings</a> page first!';
	return;
}
if ( empty($customers['name']) || empty($customers['address']) || empty($customers['email']) ){
	echo 'Missing customers info, please update your customers details in <a href="/?do=settings">settings</a> page first!';
	return;
}

$tmpl = mysqli_fetch_array(mysqli_query($conn,"SELECT name,content FROM templates WHERE id = '".$_POST['template']."'"));

$search  = array('%LOGO%','%BRAND_NAME%','%BRAND_ADDRESS%','%BRAND_EMAIL%','%BRAND_PHONE%','%CUSTOMER_NAME%','%CUSTOMER_ADDRESS%','%CUSTOMER_EMAIL%','%CUSTOMER_WEB%','%PRODUCT_NAME%','%PRODUCT_SIZE%','%PRODUCT_CONCENTATION%','%IFRA_AMMENDMENT%','%IFRA_AMMENDMENT_DATE%','%PRODUCT_CAT_CLASS%','%PRODUCT_TYPE%','%CURRENT_DATE%');

$replace = array($logo, $settings['brandName'], $settings['brandAddress'], $settings['brandEmail'], $settings['brandPhone'], $customers['name'],$customers['address'],$customers['email'],$customers['web'],$meta['product_name'],$bottle,$type,getIFRAMeta('MAX(amendment)',$conn),getIFRAMeta('MAX(last_pub)',$conn),strtoupper($defCatClass),$type,date('d/M/Y'));


$formula_q = mysqli_query($conn, "SELECT ingredient,quantity,concentration FROM formulas WHERE fid = '$fid'");
while ($formula = mysqli_fetch_array($formula_q)){
	$form[] = $formula;
}
		
foreach ($form as $formula){
	$cas = mysqli_fetch_array(mysqli_query($conn, "SELECT cas,$defCatClass FROM ingredients WHERE name = '".$formula['ingredient']."'"));
	if ($cas['cas']){
		$q2 = mysqli_query($conn, "SELECT DISTINCT name,$defCatClass,risk,type,cas FROM IFRALibrary WHERE name LIKE '".$formula['ingredient']."' OR cas = '".$cas['cas']."' GROUP BY name");
				
		while($ifra = mysqli_fetch_array($q2)){
			$new_quantity = $formula['quantity']/$mg['total_mg']*$new_conc;
			$conc = $new_quantity/$bottle * 100;						
			$conc_p = number_format($formula['concentration'] / 100 * $conc, $settings['qStep']);
					
			if($settings['multi_dim_perc'] == '1'){
				$conc_p   += multi_dim_perc($conn, $form, $cas['cas'], $settings['qStep'])[$cas['cas']];
			}
			
			$x .='<tr>
				<td align="center">'.$ifra['name'].'</td>
				<td align="center">'.$ifra['cas'].'</td>
				<td align="center">'.$ifra[$defCatClass].'</td>
				<td align="center">'.$conc_p.'</td>
				<td align="center">'.$ifra['risk'].'</td> 
			</tr>';
			
          }
	}
	if($qCMP = mysqli_query($conn, "SELECT allergens.ing, allergens.name, allergens.cas, allergens.percentage, IFRALibrary.risk, IFRALibrary.$defCatClass  FROM allergens, IFRALibrary WHERE allergens.ing = '".$formula['ingredient']."' AND toDeclare = '1' AND IFRALibrary.name = allergens.name GROUP BY name ")){
		while($cmp = mysqli_fetch_array($qCMP)){
			$x .='<tr>
					<td align="center">'.$cmp['name'].'</td>
					<td align="center">'.$cmp['cas'].'</td>
					<td align="center">'.$cmp[$defCatClass].'</td>
					<td align="center">'.$cmp['percentage']/100*$formula['quantity']/$mg['total_mg']*$new_conc/100*$bottle.'</td>
					<td align="center">'.$cmp['risk'].'</td> 
				</tr>';
		}
	
	} 
}
echo  str_replace( $search, $replace, preg_replace('#(%IFRA_MATERIALS_LIST%)#ms', $x, $tmpl['content']) );

?>

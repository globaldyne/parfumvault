<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
define('FPDF_FONTPATH',__ROOT__.'/fonts');
define('__BRANDLOGO__', __ROOT__.'/img/logo.png');

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/getIFRAMeta.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/validateFormula.php');
require_once(__ROOT__.'/func/calcPerc.php');


require_once(__ROOT__.'/libs/fpdf.php');
require_once(__ROOT__.'/libs/Html2Pdf.php');

$bottle = $_POST['bottle'];
$type = $_POST['conc'];
$defCatClass = $settings['defCatClass'];
$defPercentage = $settings['defPercentage'];

if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM IFRALibrary"))){
	echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mr-2"></i>You need to <a href="/?do=IFRA" target="_blank">import</a> the IFRA xls first</div>';
	return;
}

if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM templates"))){
	echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mr-2"></i>You need to <a href="/?do=settings" target="_blank">add</a> an IFRA template first</div>';
	return;
}

$defCatClass = mysqli_real_escape_string($conn, $_POST['defCatClass']);

if(empty($defCatClass)){
	$defCatClass = $settings['defCatClass'];
}
	
$fid = mysqli_real_escape_string($conn, $_POST['fid']);


$cid = mysqli_real_escape_string($conn, $_POST['customer']);
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid'"));
$customers = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM customers WHERE id = '$cid'"));

$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid'"));

$new_conc = $bottle/100*$type;

if(validateFormula($fid, $bottle, $new_conc, $mg['total_mg'], $defCatClass, $settings['qStep'], $conn) == TRUE){
	echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mr-2"></i>Your formula contains materials, exceeding and/or missing IFRA standards. Please alter your formula and try again</div>';
	return;
}


if ( empty($settings['brandLogo']) ){ 
	$logo = "/img/logo.png";
}else{
	$logo = $settings['brandLogo'];
}
if ( empty($settings['brandName']) || empty($settings['brandAddress']) || empty($settings['brandEmail']) || empty($settings['brandPhone']) ){
	echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mr-2"></i>Missing brand info, please update your brand details in <a href="/?do=settings">settings</a> page first</div>';
	return;
}
if ( empty($customers['name']) || empty($customers['address']) || empty($customers['email']) ){
	echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mr-2"></i>Missing customers info, please update your customers details in <a href="/?do=customers">customers</a> page first</div>';
	return;
}

$tmpl = mysqli_fetch_array(mysqli_query($conn,"SELECT name,content FROM templates WHERE id = '".$_POST['template']."'"));

$search  = array('%LOGO%','%BRAND_NAME%','%BRAND_ADDRESS%','%BRAND_EMAIL%','%BRAND_PHONE%','%CUSTOMER_NAME%','%CUSTOMER_ADDRESS%','%CUSTOMER_EMAIL%','%CUSTOMER_WEB%','%PRODUCT_NAME%','%PRODUCT_SIZE%','%PRODUCT_CONCENTRATION%','%IFRA_AMENDMENT%','%IFRA_AMENDMENT_DATE%','%PRODUCT_CAT_CLASS%','%PRODUCT_TYPE%','%CURRENT_DATE%');

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
				$conc_p   += multi_dim_perc($conn, $form, $cas['cas'], $settings['qStep'], $settings['defPercentage'])[$cas['cas']];
			}
			
			$x .='<tr>
				<td align="center">'.$ifra['name'].'</td>
				<td align="center">'.$ifra['cas'].'</td>
				<td align="center">'.$ifra[$defCatClass].'</td>
				<td align="center">'.number_format($conc_p, 4).'</td>
				<td align="center">'.$ifra['risk'].'</td> 
			</tr>';
			
          }
	}

	if($qCMP = mysqli_query($conn, "SELECT ingredient_compounds.ing, ingredient_compounds.name, ingredient_compounds.cas, ingredient_compounds.$defPercentage, IFRALibrary.risk, IFRALibrary.$defCatClass  FROM ingredient_compounds, IFRALibrary WHERE ingredient_compounds.ing = '".$formula['ingredient']."' AND toDeclare = '1' AND IFRALibrary.name = ingredient_compounds.name GROUP BY name ")){
		while($cmp = mysqli_fetch_array($qCMP)){
			$x .='<tr>
					<td align="center">'.$cmp['name'].'</td>
					<td align="center">'.$cmp['cas'].'</td>
					<td align="center">'.$cmp[$defCatClass].'</td>
					<td align="center">'.number_format($cmp['percentage']/100*$formula['quantity']/$mg['total_mg']*$new_conc/100*$bottle, 4).'</td>
					<td align="center">'.$cmp['risk'].'</td> 
				</tr>';
		}
	
	} 
}
$contents =  str_replace( $search, $replace, preg_replace('#(%IFRA_MATERIALS_LIST%)#ms', $x, $tmpl['content']) );
echo $contents;

?>

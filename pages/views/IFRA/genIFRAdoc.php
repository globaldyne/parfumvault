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
$branding = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM branding WHERE owner_id = '$userID'"));

if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM IFRALibrary WHERE owner_id = '$userID' "))){
	echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>You need to <a href="/?do=IFRA" target="_blank">import</a> the IFRA xls first</div>';
	return;
}

if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM templates WHERE owner_id = '$userID'"))){
	echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>You need to <a href="/?do=settings" target="_blank">add</a> an IFRA template first</div>';
	return;
}

$defCatClass = mysqli_real_escape_string($conn, $_POST['defCatClass']);

if(empty($defCatClass)){
	$defCatClass = $settings['defCatClass'];
}
	
$fid = mysqli_real_escape_string($conn, $_POST['fid']);


$cid = mysqli_real_escape_string($conn, $_POST['customer']);
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'"));
$customers = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM customers WHERE id = '$cid' AND owner_id = '$userID'"));

$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'"));

$new_conc = $bottle/100*$type;

if(validateFormula($fid, $bottle, $new_conc, $mg['total_mg'], $defCatClass, $settings['qStep']) !== 0){
	echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Your formula contains materials not compatible with IFRA standards</div>';
	return;
}


if ( empty($branding['brandLogo']) ){ 
	$logo = "/img/logo.png";
}else{
	$logo = $branding['brandLogo'];
}
if ( empty($branding['brandName']) || empty($branding['brandAddress']) || empty($branding['brandEmail']) || empty($branding['brandPhone']) ){
	echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Missing brand info, please update your brand details in <a href="/?do=settings">branding</a> page first</div>';
	return;
}
if ( empty($customers['name']) || empty($customers['address']) || empty($customers['email']) ){
	echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Missing customers info, please update your customers details in <a href="/?do=customers">customers</a> page first</div>';
	return;
}

$template_id = mysqli_real_escape_string($conn, $_POST['template']);
$template_query = "SELECT name, content FROM templates WHERE id = '$template_id' AND owner_id = '$userID'";
$template_result = mysqli_query($conn, $template_query);
$tmpl = mysqli_fetch_array($template_result);

$search = array(
    '%LOGO%', '%BRAND_NAME%', '%BRAND_ADDRESS%', '%BRAND_EMAIL%', '%BRAND_PHONE%', 
    '%CUSTOMER_NAME%', '%CUSTOMER_ADDRESS%', '%CUSTOMER_EMAIL%', '%CUSTOMER_WEB%', 
    '%PRODUCT_NAME%', '%PRODUCT_SIZE%', '%PRODUCT_CONCENTRATION%', 
    '%IFRA_AMENDMENT%', '%IFRA_AMENDMENT_DATE%', '%CURRENT_DATE%'
);

$replace = array(
    $logo, 
    $branding['brandName'], $branding['brandAddress'], $branding['brandEmail'], 
    $branding['brandPhone'], $customers['name'], $customers['address'], 
    $customers['email'], $customers['web'], $meta['product_name'], $bottle, 
    $type, getIFRAMeta('MAX(amendment)', $conn), 
    getIFRAMeta('MAX(last_pub)', $conn), date('d/M/Y')
);

$categories = [];
$cats_q = mysqli_query($conn, "SELECT name, description FROM IFRACategories");
while ($category = mysqli_fetch_array($cats_q)) {
    $categories[] = $category;
}



$formulas = [];
$formula_q = mysqli_query($conn, "SELECT ingredient, quantity, concentration,exclude_from_calculation FROM formulas WHERE fid = '" . mysqli_real_escape_string($conn, $fid) . "' AND owner_id = '$userID'");
while ($formula = mysqli_fetch_array($formula_q)) {
    $formulas[] = $formula;
}

$x = '';

foreach ($formulas as $formula) {
	if ( $formulas['exclude_from_calculation'] != 1 ){
		$mg['total_mg'] += $formulas['quantity'];
	}
    $ingredient = mysqli_real_escape_string($conn, $formula['ingredient']);
    $cas = mysqli_fetch_array(mysqli_query($conn, "SELECT cas, $defCatClass FROM ingredients WHERE name = '$ingredient' AND owner_id = '$userID'"));

    if ($cas['cas']) {
        $q2 = mysqli_query($conn, "SELECT DISTINCT name, $defCatClass, risk, type, cas FROM IFRALibrary WHERE (name LIKE '$ingredient' OR cas = '" . $cas['cas'] . "') AND owner_id = '$userID' GROUP BY name");

        while ($ifra = mysqli_fetch_array($q2)) {
            $new_quantity = $formula['quantity'] / $mg['total_mg'] * $new_conc;
            $conc = $new_quantity / $bottle * 100;
            $conc_p = number_format($formula['concentration'] / 100 * $conc, $settings['qStep']);

            if ($settings['multi_dim_perc'] == '1') {
                $multi_dim = multi_dim_perc($conn, $formulas, $cas['cas'], $settings['qStep'], $settings['defPercentage']);
                $conc_p += $multi_dim[$cas['cas']];
            }

            $x .= '<tr>
                <td align="center">' . htmlspecialchars($ifra['name']) . '</td>
                <td align="center">' . htmlspecialchars($ifra['cas']) . '</td>
                <td align="center">' . htmlspecialchars($ifra[$defCatClass]) . '</td>
                <td align="center">' . number_format($conc_p, $settings['qStep']) . '</td>
                <td align="center">' . htmlspecialchars($ifra['risk']) . '</td>
            </tr>';
        }
    }

    $qCMP = mysqli_query($conn, "SELECT ingredient_compounds.ing, ingredient_compounds.name, ingredient_compounds.cas, ingredient_compounds.$defPercentage, IFRALibrary.risk, IFRALibrary.$defCatClass 
        FROM ingredient_compounds, IFRALibrary 
        WHERE ingredient_compounds.ing = '$ingredient' 
        AND toDeclare = '1' 
        AND IFRALibrary.name = ingredient_compounds.name 
        AND ingredient_compounds.owner_id = '$userID'
        AND IFRALibrary.owner_id = '$userID'
        GROUP BY name");

    while ($cmp = mysqli_fetch_array($qCMP)) {
        $x .= '<tr>
            <td align="center">' . htmlspecialchars($cmp['name']) . '</td>
            <td align="center">' . htmlspecialchars($cmp['cas']) . '</td>
            <td align="center">' . htmlspecialchars($cmp[$defCatClass]) . '</td>
            <td align="center">' . number_format($cmp['percentage'] / 100 * $formula['quantity'] / $mg['total_mg'] * $new_conc / 100 * $bottle, $settings['qStep']) . '</td>
            <td align="center">' . htmlspecialchars($cmp['risk']) . '</td>
        </tr>';
    }
}

$f = '';
foreach ($categories as $cat) {
	$lastValAccepted = null;
	$catname = 'cat'.$cat['name'];
    for ($c = 1; $c <= 100; $c++) {
		
        $result = validateFormula($fid, 100, $c, $mg['total_mg'], $catname, $settings['qStep'], 1);

        if ($result === 0) {
            $lastValAccepted = $c;
        } else {
            break;
        }
    }

    if ($lastValAccepted !== null) {
        $m[$catname] = $lastValAccepted;
    } else {
        $m[$catname] = '-';
    }
    $f .= '<tr>
        <td align="center">Cat' . htmlspecialchars($cat['name']) . '</td>
        <td align="center">' . htmlspecialchars($cat['description']) . '</td>
        <td align="center">'.$m[$catname].'</td>
    </tr>';
}

$contents = str_replace($search, $replace, preg_replace('#(%IFRA_MATERIALS_LIST%)#ms', $x, preg_replace('#(%IFRA_CAT_LIST%)#ms', $f, $tmpl['content'])));

echo $contents;

?>

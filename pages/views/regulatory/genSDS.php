<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

if ($_POST['do'] = 'genSDS') {
  $name = $_POST['name'];
  $notes = "SDS wizard generated";
  $product_use = $_POST['product_use'];
  $country = $_POST['country'];
  $language = $_POST['language'];
  $product_type = $_POST['product_type'];
  $sttype = $_POST['state_type'];
  $supplier_id = $_POST['supplier_id'];
  $po = $_POST['po'];
  $address = $_POST['address'];
  $telephone = $_POST['telephone'];
  $email = $_POST['email'];
  $url = $_POST['url'];
  $sdsCountry = $_POST['sdsCountry'];
  $prodUse = $_POST['prodUse'];
  $sdsLang = $_POST['sdsLang'];
  $productType = $_POST['productType'];
  $productState = $_POST['productState'];


$htmlContent = '
<!doctype html>
<html lang="en">
<head>


    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" type="text/css">
    <link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="/css/regulatory.css" rel="stylesheet">

</head>

<body
<div class="container">
   <div class="col-md-12">
      <div class="sds">
         <div class="sds-company text-inverse f-w-600">
            '.$name.'
         </div>           
         	<div class="sds-date">
               <small>Language: '.$sdsLang.'</small>
               <div class="date text-inverse m-t-5">August 3,2024</div>
               <div class="sds-detail">
                  According to Regulation (EC) No. 1907/2006 (amended by Regulation (EU)
No. 2020/878) 
               </div>
            </div>
         <div class="sds-header">
           <div class="sds-to">
              <h4>1. Identification of the substance/mixture and of the company/undertaking</h4>
            </div>

         </div>
         <div class="sds-content">
            <div class="table-responsive">
               <table class="table table-sds">
                  <tbody>
                     <tr>
                        <td colspan="4">
                        <span class="text-inverse">1.1 Product identifier</span><small></small>                        $50.0050$2,500.00</td>
                     </tr>
                     <tr>
                        <td colspan="4">'.$name.'</td>
                     </tr>
                     <tr>
                        <td>
                           <span class="text-inverse">Redesign Service</span><br>
                           <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed id sagittis arcu.</small>
                        </td>
                        <td width="10%" class="text-center">$50.00</td>
                        <td width="10%" class="text-center">50</td>
                        <td width="20%" class="text-right">$2,500.00</td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div class="sds-price">
               <div class="sds-price-left">
                  <div class="sds-price-row">
                     <div class="sub-price">
                        <small>SUBTOTAL</small>
                        <span class="text-inverse">$4,500.00</span>
                     </div>
                     <div class="sub-price">
                        <i class="fa fa-plus text-muted"></i>
                     </div>
                     <div class="sub-price">
                        <small>PAYPAL FEE (5.4%)</small>
                        <span class="text-inverse">$108.00</span>
                     </div>
                  </div>
               </div>
               <div class="sds-price-right">
                  <small>TOTAL</small> <span class="f-w-600">$4508.00</span>
               </div>
            </div>
         </div>
         <div class="sds-note">
            * Make all cheques payable to [Your Company Name]<br>
            * Payment is due within 30 days<br>
            * If you have any questions concerning this sds, contact  [Name, Phone Number, Email]
         </div>
         <div class="sds-footer">
            <p class="text-center m-b-5 f-w-600">
               THANK YOU FOR YOUR BUSINESS
            </p>
            <p class="text-center">
               <span class="m-r-10"><i class="fa fa-fw fa-lg fa-globe"></i> matiasgallipoli.com</span>
               <span class="m-r-10"><i class="fa fa-fw fa-lg fa-phone-volume"></i> T:016-18192302</span>
               <span class="m-r-10"><i class="fa fa-fw fa-lg fa-envelope"></i> rtiemps@gmail.com</span>
            </p>
         </div>
      </div>
   </div>
</div>


</body>
</html>
';
//$pdfBlob = mysqli_real_escape_string($conn, $docData);

// Insert into sds_data table
$insert_sds_data = mysqli_query($conn, "INSERT INTO sds_data (product_name,product_use,country,language,product_type,state_type,supplier_id,docID) VALUES ('$name','$product_use','$country','$language','$product_type','$state_type','$supplier_id','0')");

if ($insert_sds_data) {
    $ownerID = mysqli_insert_id($conn);

    // Prepare and bind the documents table insert statement
    $stmt = $conn->prepare("INSERT INTO documents (docData, isSDS, name, type, notes, ownerID) VALUES (?, '1', ?, '0', ?, ?)");
    $stmt->bind_param("bssi", $htmlContent, $name, $notes, $ownerID);

    // Send HTML content as BLOB
    $stmt->send_long_data(0, $htmlContent);

    // Execute the query
    $sds = $stmt->execute();

    if ($sds) {
        $result['success'] = $ownerID;
    } else {
        $result['error'] = "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    $result['error'] = "Error: " . mysqli_error($conn);
}

$conn->close();

echo json_encode($result);
  return;
}



?>
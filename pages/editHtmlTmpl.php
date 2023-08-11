<?php 
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$tmpl = mysqli_fetch_array(mysqli_query($conn,"SELECT name,content FROM templates WHERE id = '".$_GET['id']."'"));

?>
<div class="card-body">
  <div id="tmpl-inf"></div>
      <div class="row">
            <div class="col-xs-8">
                HTML Content:
                <textarea class="form-control" name="tmpl-editor" id="tmpl-editor" rows="30"><?=$tmpl['content']?></textarea>
            </div>
            <div class="col-xs-3">
                <div class="special-vars">
                    <strong>Special vars</strong>
                    <ul id="addvars">
                        <li><a onclick="insertAtCaret('tmpl-editor','%LOGO%');" href="#">Add your Logo (IFRA, SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%BRAND_NAME%');" href="#">Add your brand name (IFRA, SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%BRAND_LOGO%');" href="#">Add your brand logo (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%BRAND_ADDRESS%');" href="#">Add your brand’s address (IFRA, SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%BRAND_EMAIL%');" href="#">Add your email (IFRA, SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%BRAND_PHONE%');" href="#">Add your phone number (IFRA, SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CUSTOMER_NAME%');" href="#">Add customer's name (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CUSTOMER_ADDRESS%');" href="#">Add customer’s address (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CUSTOMER_EMAIL%');" href="#">Add custtomes email (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CUSTOMER_WEB%');" href="#">Add customer’s website (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%PRODUCT_NAME%');" href="#">Add finished product size in ml (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%PRODUCT_CONCENTRATION%');" href="#">Add product concentration (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%IFRA_AMENDMENT%');" href="#">Add the IFRA amendment number you use (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%IFRA_AMENDMENT_DATE%');" href="#">Add the IFRA amendment release date (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%PRODUCT_CAT_CLASS%');" href="#">Add the category class of your product (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%PRODUCT_TYPE%');" href="#">Add the product type (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%PRODUCT_CAT_CLASS%');" href="#">Add the category class of your product (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CURRENT_DATE%');" href="#">Add the current date (IFRA, SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%IFRA_MATERIALS_LIST%');" href="#">Add materials in formula found under the IFRA scope (IFRA)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%INGREDIENT_NAME%');" href="#">Add the ingredient name (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CAS%');" href="#">Add ingredient’s CAS number (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%IUPAC%');" href="#">Add ingredient’s IUPAC data (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%REACH%');" href="#">Add ingredient’s REACH number (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%EINECS%');" href="#">Add ingredient’s EINECS - EC (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SUPPLIER_NAME%');" href="#">Add ingredient’s supplier name (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SUPPLIER_ADDRESS%');" href="#">Add ingredient’s supplier address (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SUPPLIER_PO%');" href="#">Add ingredient’s supplier postal code (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SUPPLIER_COUNTRY%');" href="#">Add ingredient’s supplier country (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SUPPLIER_PHONE%');" href="#">Add ingredient’s supplier phone number (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SUPPLIER_URL%');" href="#">Add ingredient’s supplier website (SDS)</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SUPPLIER_EMAIL%');" href="#">Add ingredient’s supplier email (SDS)</a></li>
                    </ul>
                </div>
            </div>
        </div>
      <div class="dropdown-divider"></div>
        <div class="alert alert-info">Please refer <a href="https://www.perfumersvault.com/knowledge-base/html-templates/" target="_blank">here</a> for special variables syntax</div>
         <div class="modal-footer">
          <input type="submit" name="button" class="btn btn-primary" id="tmpl-save" value="Save changes">
      </div>
 </div>
</div>


<script>


$('#tmpl-save').click(function() {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			tmpl: 'update',
			name: 'content',
			pk: <?=$_GET['id']?> ,
			value: $("#tmpl-editor").val(),
			},
		dataType: 'json',   			
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success">' + data.success + '</div>';
			}else{
				var msg ='<div class="alert alert-danger">' + data.error + '</div>';
			}
			$('#tmpl-inf').html(msg);
		}
	});
});
</script>
<script src="/js/helpers.js"></script>

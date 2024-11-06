<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$tmpl = mysqli_fetch_array(mysqli_query($conn,"SELECT name,content FROM templates WHERE id = '".$_GET['id']."'"));

?>
<div class="card-body">

      <div class="row">
            <div class="col-sm-8">
            	<div id="tmpl-inf"></div>
                HTML Content:
                <textarea class="form-control mt-2 mb-2" name="tmpl-editor" id="tmpl-editor" rows="40"><?=$tmpl['content']?></textarea>
            </div>
            <div class="col-sm-3">
                <div class="special-vars">
                    <strong>IFRA Special vars</strong>
                    <ul id="ifraaddvars">
                        <li><a onclick="insertAtCaret('tmpl-editor','%BRAND_ADDRESS%');" href="#">Add your brand’s address</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%BRAND_EMAIL%');" href="#">Add your email</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%BRAND_PHONE%');" href="#">Add your phone number</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CUSTOMER_NAME%');" href="#">Add customer's name</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CUSTOMER_ADDRESS%');" href="#">Add customer’s address</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CUSTOMER_EMAIL%');" href="#">Add custtomes email</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CUSTOMER_WEB%');" href="#">Add customer’s website </a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%PRODUCT_NAME%');" href="#">Add finished product size in ml</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%PRODUCT_CONCENTRATION%');" href="#">Add product concentration</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%IFRA_AMENDMENT%');" href="#">Add the IFRA amendment number you use</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%IFRA_AMENDMENT_DATE%');" href="#">Add the IFRA amendment release date</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%IFRA_CAT_LIST%');" href="#">Add IFRA categories and limits</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%IFRA_MATERIALS_LIST%');" href="#">Add materials in formula found under the IFRA scope</a></li>

                    </ul>
                </div>
                <div class="special-vars">
                    <strong>SDS Special vars</strong>
                    <ul id="sdsaddvars">
                        <li><a onclick="insertAtCaret('tmpl-editor','%SDS_LANGUAGE%');" href="#">Add SDS Language</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SDS_PRODUCT_NAME%');" href="#">Add product name</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%GHS_LABEL_LIST%');" href="#">Add GHS data</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CMP_MATERIALS_LIST%');" href="#">Add compositions</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SDS_SUPPLIER_NAME%');" href="#">Add supplier name</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SDS_SUPPLIER_ADDRESS%');" href="#">Add supplier address</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SDS_SUPPLIER_PO%');" href="#">Add ingredient’s supplier postal code</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SDS_SUPPLIER_COUNTRY%');" href="#">Add ingredient’s supplier country</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SDS_SUPPLIER_PHONE%');" href="#">Add ingredient’s supplier phone number</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%SDS_SUPPLIER_URL%');" href="#">Add ingredient’s supplier website</a></li>
                    	<li><a onclick="insertAtCaret('tmpl-editor','%SDS_SUPPLIER_EMAIL%');" href="#">Add ingredient’s supplier email</a></li>
                        
                    	<li><a onclick="insertAtCaret('tmpl-editor','%SDS_DISCLAIMER%');" href="#">Add disclaimer info</a></li>

                        <li><a onclick="insertAtCaret('tmpl-editor','%CAS%');" href="#">Add ingredient’s CAS number</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%IUPAC%');" href="#">Add ingredient’s IUPAC data</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%REACH%');" href="#">Add ingredient’s REACH number</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%EINECS%');" href="#">Add ingredient’s EINECS - EC</a></li>

                	</ul>
                </div>
                <div class="special-vars">
                    <strong>Common Special vars</strong>
                    <ul id="commonaddvars">
                    	<li><a onclick='insertAtCaret("tmpl-editor","<img src=\"%LOGO%\" class=\"img-thumbnail float-start\" >");' href="#">Add your Logo</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%CURRENT_DATE%');" href="#">Add the current date</a></li>
                        <li><a onclick="insertAtCaret('tmpl-editor','%BRAND_NAME%');" href="#">Add your brand name</a></li>

                    </ul>
                </div>
            </div>
        </div>
      <div class="dropdown-divider"></div>
        <div class="alert alert-info"><i class="fa-solid fa-circle-exclamation mr-2"></i>Please refer <a href="https://www.perfumersvault.com/knowledge-base/html-templates/" target="_blank" class="link-primary">here</a> for additional special variables syntax</div>
         <div class="modal-footer">
          <input type="submit" name="button" class="btn btn-primary" id="tmpl-save" value="Save changes">
      </div>
 </div>
</div>


<script>


$('#tmpl-save').click(function() {
	$.ajax({ 
		url: '/core/core.php', 
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

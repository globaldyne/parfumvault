<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$res_ingSupplier = mysqli_query($conn, "SELECT * FROM ingSuppliers ORDER BY name ASC");

?>
  
  <link href="/css/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" type="text/css">
  <link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="/css/sb-admin-2.css" rel="stylesheet" />
  <link href="/css/bootstrap-select.min.css" rel="stylesheet">
  <link href="/css/bootstrap-editable.css" rel="stylesheet">
  <link href="/css/datatables.min.css" rel="stylesheet">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/jquery-ui.css" rel="stylesheet">
  <link href="/css/magnific-popup.css" rel="stylesheet">
  <link href="/css/vault.css" rel="stylesheet">
  <script src="/js/jquery/jquery.min.js"></script>
  <script src="/js/tableHTMLExport.js"></script>
  <script src="/js/jspdf.min.js"></script>
  <script src="/js/jspdf.plugin.autotable.js"></script>
  <script src="/js/datatables.min.js"></script> 
  <script src="/js/magnific-popup.js"></script>
  <script src="/js/jquery-ui.js"></script>
  <script src="/js/bootstrap.bundle.min.js"></script>
  <script src="/js/bootstrap-select.js"></script>
  <script src="/js/bootstrap-editable.js"></script>
  <script src="/js/bootbox.min.js"></script>
  <script src="/js/sb-admin-2.js"></script>
  <script src="/js/validate-session.js"></script>
  
  
<script src="/js/regulatory.js"></script>
<div class="container">

<ul class="nav nav-tabs" id="SDSTabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" data-bs-toggle="tab" href="#supplierPanel" role="tab">Supplier</a>
  <li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#productPanel" role="tab">Product</a>
  <li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#compoPanel" role="tab">Composition</a>
  <li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#reviewPanel" role="tab">Review SDS</a>
  <li>
</ul>

<div class="tab-content mt-2">
  <div class="tab-pane fade show active" id="supplierPanel" role="tabpanel">
    <h4>1. Supplier contact details</h4>
    <div class="col-sm">

        <div class="form-row mb-2">
    			<label for="supplier_name">Name</label>
                <select name="supplier_name" id="supplier_name" class="form-control selectpicker" data-live-search="true">
                <?php 
					while ($row_ingSupplier = mysqli_fetch_array($res_ingSupplier)){ 
						$supLogo = mysqli_fetch_array(mysqli_query($conn,"SELECT docData FROM documents WHERE ownerID = '".$row_ingSupplier['ownerID']."' AND type = '3'"));
	
				?>
                    <option value="<?=$row_ingSupplier['id']?>" data-city="<?=$row_ingSupplier['city'];?>" data-email="<?=$row_ingSupplier['email'];?>" data-url="<?=$row_ingSupplier['url'];?>" data-telephone="<?=$row_ingSupplier['telephone'];?>" data-country="<?=$row_ingSupplier['country'];?>" data-po="<?=$row_ingSupplier['po'];?>" data-address="<?=$row_ingSupplier['address'];?>"><?=$row_ingSupplier['name'];?></option>
                <?php	}	?>
                </select>
	    </div>
           
           <div class="row">
           	 	<div class="col-sm">
                	<div class="form-row mb-2">
                    	<label for="address">Address</label>
                    	<input class="form-control" name="address" type="text" id="address" >
                	</div>
             	</div>
        </div>
             

           
             <div class="row">
                 <div class="col-sm-6">
                    <div class="form-row mb-2">
                        <label for="po">Postal Code</label>
                        <input name="po" type="text" class="form-control" id="po">
                    </div>
                </div>
                                <div class="col-sm">
                    <div class="form-row mb-2">
                        <label for="country">Country</label>
                        <input name="country" type="text" class="form-control" id="country">
                    </div>
                </div>
           </div>
           

                        <div class="row">
                 <div class="col-sm-6">
                    <div class="form-row mb-2">
                        <label for="telephone">Phone</label>
                        <input name="telephone" type="text" class="form-control" id="telephone">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-row mb-2">
                        <label for="email">Email</label>
                        <input name="email" type="text" class="form-control" id="email">
                    </div>
                </div>
           </div>
           
                      <div class="row">
           	 	<div class="col-sm">
                	<div class="form-row mb-2">
                    	<label for="url">Website</label>
                    	<input class="form-control" name="url" type="text" id="url" >
                	</div>
             	</div>
             </div>
                   <div class="col-md-4">
                   
        <div class="text-center">
          <div id="supplier_pic" class="mb-3"></div>
          <input type="file" id="brandLogo" name="brandLogo" class="form-control" />
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
			<div class="text-right mt-3">
        		<input type="button" class="btn btn-primary" value="Upload" id="brandLogo_upload" />
        	</div>
        </div>
      </div>
      
    </div>
    <hr />
    <button class="btn btn-secondary" id="supplierContinue">Continue</button>
  </div>
  
  <div class="tab-pane fade" id="productPanel" role="tabpanel">
    <h4>2. Product information</h4>
    
   <div class="row">
           	 	<div class="col-sm">
                	<div class="form-row mb-2">
                    	<label for="prodName">Prodcut name</label>
                    	<input class="form-control" name="prodName" type="text" id="prodName" >
                	</div>
             	</div>
      </div>
             
                <div class="row">
           	 	<div class="col-sm">
                	<div class="form-row mb-2">
                    	<label for="prodUse">Prodcut use</label>
                    	<input class="form-control" name="prodUse" type="text" id="prodUse" >
                	</div>
             	</div>
             </div>
                             <div class="row">
           	 	<div class="col-sm">

                        <div class="form-row mb-2">
    			<label for="sdsCountry">Country</label>
                <select name="sdsCountry" id="sdsCountry" class="form-control selectpicker" data-live-search="true">
                
                    <option value="">xxx</option>
                </select>
  		   </div>
           </div>
                      	 	<div class="col-sm">

                                   <div class="form-row mb-2">
    			<label for="sdsLang">Language</label>
                <select name="sdsLang" id="sdsLang" class="form-control selectpicker" data-live-search="true">
               
                    <option value="">xxxx</option>
                </select>
  		   </div>
           </div>
           </div>
    <div class="form-check">
  <input class="form-check-input" type="radio" name="sdsType" id="sdsSub">
  <label class="form-check-label" for="sdsSub">
    Product is a substance
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="radio" name="sdsType" id="sdsMixture" checked>
  <label class="form-check-label" for="sdsMixture">
    Product is a mixture
  </label>
</div>
<hr />

    <button class="btn btn-secondary" id="productContinue">Continue</button>
  </div>
  
  <div class="tab-pane fade" id="compoPanel" role="tabpanel">
    <h4>3. Product composition</h4>
    <div id="accordion" class="mb-3" role="tablist" aria-multiselectable="true">
      <div class="card">
        <div class="card-header" role="tab" id="headingOne">
          <h5 class="mb-0">
            <a data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              Entire Venue
            </a>
          </h5>
        </div>

        <div id="collapseOne" class="collapse" role="tabpanel" aria-labelledby="headingOne">
          <div class="card-block">
            <div class="form-group">
              <label for="venueSelect">Select a Venue</label>
              <select class="form-control" id="venueSelect">
                <option selected disabled>Choose a venue</option>
                <option>Venue 1</option>
                <option>Venue 2</option>
                <option>Venue 3</option>
                <option>Venue 4</option>
                <option>Venue 5</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header" role="tab" id="headingTwo">
          <h5 class="mb-0">
            <a class="collapsed" data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
              Specific Kiosks
            </a>
          </h5>
        </div>
        <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
          <div class="card-block">
            <div class="form-group">
              <label for="kioskSelectVenue">First, choose a venue.</label>
              <select class="form-control" id="kioskSelectVenue">
                <option selected disabled>Choose a venue</option>
                <option>Venue 1</option>
                <option>Venue 2</option>
                <option>Venue 3</option>
                <option>Venue 4</option>
                <option>Venue 5</option>
              </select>
            </div>
            <div class="form-group">
              <label for="exampleSelect2">Then select kiosks (you can select multiple)</label>
              <select multiple class="form-control" id="exampleSelect2">
                <option>Kiosk 1</option>
                <option>Kiosk 2</option>
                <option>Kiosk 3</option>
                <option>Kiosk 4</option>
                <option>Kiosk 5</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header" role="tab" id="headingThree">
          <h5 class="mb-0">
            <a class="collapsed" data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
              Specific Screens
            </a>
          </h5>
        </div>
        <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">
          <div class="card-block">
            <div class="form-group">
              <label for="kioskSelectVenue">First, choose a venue.</label>
              <select class="form-control" id="kioskSelectVenue">
                <option selected disabled>Choose a venue</option>
                <option>Venue 1</option>
                <option>Venue 2</option>
                <option>Venue 3</option>
                <option>Venue 4</option>
                <option>Venue 5</option>
              </select>
            </div>
            <div class="form-group">
              <label for="exampleSelect2">Then select screens (you can select multiple)</label>
              <select multiple class="form-control" id="exampleSelect2">
                <option>Kiosk 1 - Screen 1</option>
                <option>Kiosk 1 - Screen 2</option>
                <option>Kiosk 2 - Screen 1</option>
                <option>Kiosk 2 - Screen 2</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
    <button class="btn btn-secondary" id="compoContinue">Continue</button>
  </div>
  <div class="tab-pane fade" id="reviewPanel" role="tabpanel">
    <h4>Review</h4>
    <button class="btn btn-primary btn-block" id="commitSDS">Save SDS data</button>
</div>
</div>
<div class="progress mt-5">
  <div class="progress-bar" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">Step 1 of 4</div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button type="button" class="btn btn-primary">Save for later</button>
</div>

</div>
<script>
$(document).ready(function() {
	$("#supplier_name").change(function () {
    	$("#address").focus().val($(this).children(':selected').data('address')); 
		$("#country").focus().val($(this).children(':selected').data('country'));
		$("#city").focus().val($(this).children(':selected').data('city')); 
		$("#po").focus().val($(this).children(':selected').data('po')); 
		$("#telephone").focus().val($(this).children(':selected').data('telephone')); 
		$("#email").focus().val($(this).children(':selected').data('email')); 
		$("#url").focus().val($(this).children(':selected').data('url')); 

	});
    
});
    </script>
<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$res_ingSupplier = mysqli_query($conn, "SELECT * FROM ingSuppliers ORDER BY name ASC");
$res_SDStmpl = mysqli_query($conn, "SELECT * FROM templates ORDER BY name ASC");

?>
<!doctype html>
<html lang="en" data-bs-theme="<?=$settings['bs_theme']?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
  	<link href="/css/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" type="text/css">
  	<link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet">
  	<link href="/css/sb-admin-2.css" rel="stylesheet" />
  	<link href="/css/bootstrap-select.min.css" rel="stylesheet">
  	<link href="/css/datatables.min.css" rel="stylesheet">
  	<link href="/css/bootstrap.min.css" rel="stylesheet">
  	<link href="/css/jquery-ui.css" rel="stylesheet">
  	<link href="/css/vault.css" rel="stylesheet">
	<script src="/js/jquery/jquery.min.js"></script>
    <script src="/js/datatables.min.js"></script> 
    <script src="/js/jquery-ui.js"></script>
    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="/js/bootstrap-select.js"></script>
    <script src="/js/bootbox.min.js"></script>
    <script src="/js/sb-admin-2.js"></script>
    <script src="/js/validate-session.js"></script>
  	<script src="/js/bootstrap-editable.js"></script>

	<link href="/css/bootstrap-editable.css" rel="stylesheet">
    <link href="/css/select2.css" rel="stylesheet">
    <script src="/js/select2.js"></script> 
    <script src="/js/regulatory.js"></script> 
    <script src="/js/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="/js/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

</head>

<body>
<div class="container mt-5">
    <ul class="nav nav-tabs" id="SDSTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#supplierPanel" role="tab"><i class="fa fa-shopping-cart mx-2"></i>Supplier</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#productPanel" id="product_tab" role="tab"><i class="fa-brands fa-product-hunt mx-2"></i>Product</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="#tech_composition" id="cmps_tab" class="nav-link" aria-selected="false" role="tab"><i class="fa fa-th-list mx-2"></i>Composition</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="#safety_info" id="safety_tab" class="nav-link" aria-selected="false" role="tab"><i class="fa fa-biohazard mx-2"></i>Safety</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="gen_tab" href="#reviewPanel" role="tab"><i class="fa-solid fa-file-shield mx-2"></i>Choose template</a>
        </li>
    </ul>

  <div class="tab-content mt-2">
    <div class="tab-pane fade show active" id="supplierPanel" role="tabpanel">
      <h4>1. Supplier contact details</h4>
      <div class="col-sm">
        <div class="mb-2">
          <select name="supplier_name" id="supplier_name" class="form-control selectpicker" data-live-search="true">
          	<option selected disabled>Choose a supplier</option>
            <?php while ($row_ingSupplier = mysqli_fetch_array($res_ingSupplier)){ ?>
              <option value="<?=$row_ingSupplier['id']?>" data-sname="<?=$row_ingSupplier['name'];?>" data-city="<?=$row_ingSupplier['city'];?>" data-email="<?=$row_ingSupplier['email'];?>" data-url="<?=$row_ingSupplier['url'];?>" data-telephone="<?=$row_ingSupplier['telephone'];?>" data-country="<?=$row_ingSupplier['country'];?>" data-po="<?=$row_ingSupplier['po'];?>" data-address="<?=$row_ingSupplier['address'];?>"><?=$row_ingSupplier['name'];?></option>
            <?php } ?>
          </select>
        </div>

        <div class="row">
          <div class="col-sm">
            <div class="mb-2">
              <label for="address">Address</label>
              <input class="form-control" name="address" type="text" id="address">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-6">
            <div class="mb-2">
              <label for="po">Postal Code</label>
              <input name="po" type="text" class="form-control" id="po">
            </div>
          </div>
          <div class="col-sm">
            <div class="mb-2">
              <label for="country">Country</label>
              <input name="country" type="text" class="form-control" id="country">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-6">
            <div class="mb-2">
              <label for="telephone">Phone</label>
              <input name="telephone" type="text" class="form-control" id="telephone">
            </div>
          </div>
          <div class="col-sm-6">
            <div class="mb-2">
              <label for="email">Email</label>
              <input name="email" type="text" class="form-control" id="email">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm">
            <div class="mb-2">
              <label for="url">Website</label>
              <input class="form-control" name="url" type="text" id="url">
            </div>
          </div>
        </div>
      
      </div>
      <hr>
      <button class="btn btn-primary" id="supplierContinue">Continue</button>
    </div>

    <div class="tab-pane fade" id="productPanel" role="tabpanel">
      <h4>2. Product information</h4>

      <div class="row">
        <div class="col-sm">
          <div class="mb-2">
            <label for="prodName">Product name</label>
            <select name="prodName" id="prodName" class="prodName pv-form-control"></select>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm">
          <div class="mb-2">
            <label for="prodUse">Product use</label>
            <input name="prodUse" type="text" class="form-control" id="prodUse" placeholder="eg: Fragrance">
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm">
          <div class="mb-2">
            <label for="sdsCountry">Country</label>
            <input name="sdsCountry" type="text" class="form-control" id="sdsCountry" placeholder="eg: UK">
          </div>
        </div>
        <div class="col-sm">
          <div class="mb-2">
            <label for="sdsLang">Language</label>
            <input name="sdsLang" type="text" class="form-control" id="sdsLang" placeholder="eg: EN">
          </div>
        </div>
      </div>

      <div class="form-check">
        <input class="form-check-input" type="radio" name="productType" id="Substance" checked>
        <label class="form-check-label" for="Substance">Product is a substance</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="productType" id="Mixture">
        <label class="form-check-label" for="Mixture">Product is a mixture</label>
      </div>
      <hr>
      <button class="btn btn-primary" id="productContinue">Continue</button>
    </div>

   	<div class="tab-pane fade" id="tech_composition">
      <h4>3. Product composition</h4>
      <div class="alert alert-warning"><strong><i class="fa-solid fa-triangle-exclamation mx-2"></i>Updating data bellow will also update main material's data</strong></div>

        <div id="fetch_composition">
            <div class="row justify-content-md-center">
                <div class="loader"></div>
            </div>
        </div>
         <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="productState" id="Liquid" value="Liquid" checked>
      <label class="form-check-label" for="Liquid">Liquid <i class="fa-solid fa-droplet"></i></label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="productState" id="Solid" value="Solid">
      <label class="form-check-label" for="Solid">Solid <i class="fa-solid fa-hill-rockslide"></i></label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="productState" id="GAS" value="Gas">
      <label class="form-check-label" for="GAS">GAS <i class="fa-solid fa-wind"></i></label>
    </div>

      <hr>
      <button class="btn btn-primary mb-2" id="compoContinue">Continue</button>
    </div>
    

      

    <div class="tab-pane fade" id="safety_info">
        <div class="alert alert-warning"><strong><i class="fa-solid fa-triangle-exclamation mx-2"></i>Updating data bellow will also update main material's data</strong></div>
        <div id="fetch_safety">
            <div class="row justify-content-md-center">
                <div class="loader"></div>
             </div>
        </div>
        <hr>
        <button class="btn btn-primary  mb-2" id="ghsContinue">Continue</button>
    </div>
    
    <div class="tab-pane fade" id="reviewPanel" role="tabpanel">
      <h4>Generate SDS</h4>
      <div class="alert alert-warning"><strong><i class="fa-solid fa-triangle-exclamation mx-2"></i>To create or update an SDS template, go to Settings -> <a href="https://vault.jbparfum.com/?do=settings#templates" target="_blank">HTML Templates</a></strong></div>
      <div class="col-sm">
        <div class="mb-4 mt-4">
          <select name="sds_tmpl" id="sds_tmpl" class="form-control selectpicker" data-live-search="true">
          	<option selected disabled>Choose an SDS template</option>
            <?php while ($row_SDStmpl = mysqli_fetch_array($res_SDStmpl)){ ?>
              <option value="<?=$row_SDStmpl['id']?>"><?=$row_SDStmpl['name'];?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="mt-3" id="sdsResult"></div>
      <button class="btn btn-primary btn-block" id="downloadSDS">Generate SDS</button>
    </div>
  </div>

  </div>


</div>

<script>
var prodName;
var ingID;

$(document).ready(function() {
	
	$('[data-bs-toggle="tooltip"]').tooltip();
	$('.selectpicker').selectpicker();

	$('#safety_tab, #SDSTabs a[href="#safety_info"]').on('click shown.bs.tab', function (e) {
    	fetch_safety();
	});
	
	$('#cmps_tab, #SDSTabs a[href="#tech_composition"]').on('click shown.bs.tab', function (e) {
		fetch_cmps();
	});
	
	$("#supplier_name").change(function () {
    	$("#address").focus().val($(this).children(':selected').data('address')); 
		$("#country").focus().val($(this).children(':selected').data('country'));
		$("#city").focus().val($(this).children(':selected').data('city')); 
		$("#po").focus().val($(this).children(':selected').data('po')); 
		$("#telephone").focus().val($(this).children(':selected').data('telephone')); 
		$("#email").focus().val($(this).children(':selected').data('email')); 
		$("#url").focus().val($(this).children(':selected').data('url')); 
	});

	

    // Initialize select2
    $("#prodName").select2({
        width: '100%',
        placeholder: 'Search for material..',
        allowClear: true,
        dropdownAutoWidth: true,
        containerCssClass: "prodName",
		//dropdownParent: $('#createSDS'),
        minimumInputLength: 2,
        ajax: {
            url: '/core/list_ingredients_simple.php',
            dataType: 'json',
            type: 'POST',
            delay: 100,
            quietMillis: 250,
            data: function (params) {
                return {
                    search: { term: params.term },
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data.data, function(obj) {
                        return {
                            id: obj.id,
                            text: obj.name || 'No material found...',
							physical_state: obj.physical_state
                        }
                    })
                };
            },
            cache: false,
        }
    }).on('select2:selecting', function (e) {
        prodName = e.params.args.data.text;
        ingID = e.params.args.data.id;
		physical_state = e.params.args.data.physical_state;
		
		if (physical_state == 1) {
            $('#inlineLiquid').prop('checked', true);
        } else if (physical_state == 2) {
            $('#inlineSolid').prop('checked', true);
        } else if (physical_state == 3) {
            $('#inlineGAS').prop('checked', true);
        }
		
    });

	$('#downloadSDS').click(function (e) {
		e.preventDefault();
		$.ajax({ 
			url: '/pages/views/regulatory/genSDS.php', 
			type: 'POST',
			dataType: 'json',
			data: {
				genSDS: true,
				tmplID: $('#sds_tmpl').val(),
				name: prodName,
				ingID: ingID,
				supplier_name: $('#supplier_name').children(':selected').data('sname'),
				supplier_id: $('#supplier_name').val(),
				po: $('#po').val(),
				country: $('#country').val(),
				address: $('#address').val(),
				telephone: $('#telephone').val(),
				email: $('#email').val(),
				url: $('#url').val(),
				sdsCountry: $('#sdsCountry').val(),
				prodUse: $('#prodUse').val(),
				sdsLang: $('#sdsLang').val(),
				productType: $('input[name="productType"]:checked').attr('id'),
				productState : $('input[name="productState"]:checked').attr('id')
			},
			success: function (data) {
				if(data.success){
					$('#sdsResult').html('<div class="alert alert-success"><i class="fa-solid fa-file-pdf mx-2"></i>' + data.success + '</div>');
				}else if(data.error){
					$('#sdsResult').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error + '</div>');
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error("Failed to fetch data: ", textStatus, errorThrown);
				$('#sdsResult').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mr-2"></i>Failed to fetch data</div>');
			}
	  });
	  
	});
       
	
});
</script>


<!-- TOAST -->
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 11">
  <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
    <div class="toast-header">
      <strong class="me-auto" id="toast-title">...</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>


</body>
</html>
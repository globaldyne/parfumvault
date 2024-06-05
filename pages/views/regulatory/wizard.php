<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$res_ingSupplier = mysqli_query($conn, "SELECT * FROM ingSuppliers ORDER BY name ASC");

?>
  

  
  

  
<link href="/css/select2.css" rel="stylesheet">
<script src="/js/select2.js"></script> 
<script src="/js/regulatory.js"></script> 
<script src="/js/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="/js/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

    <style>
        #jsonOut {
            white-space: pre; /* Preserve whitespace formatting */
            font-family: monospace; /* Use a monospace font */
            background-color: #f5f5f5; /* Light grey background */
            padding: 10px; /* Some padding */
            border: 1px solid #ddd; /* Light border */
            border-radius: 5px; /* Rounded corners */
        }
    </style>

<div class="container">
  <ul class="nav nav-tabs" id="SDSTabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" data-bs-toggle="tab" href="#supplierPanel" role="tab"><i class="fa fa-shopping-cart mx-2"></i>Supplier</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#productPanel" role="tab"><i class="fa-brands fa-product-hunt mx-2"></i>Product</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#compoPanel" role="tab"><i class="fa fa-th-list mx-2"></i>Composition</a>
    </li>
    
    <li class="nav-item" role="presentation">
    	<a href="#safety_info" id="safety_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-biohazard mx-2"></i>Safety</a>
    </li>
   
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#reviewPanel" role="tab"><i class="fa-solid fa-file-shield mx-2"></i>Review SDS</a>
    </li>
  </ul>

  <div class="tab-content mt-2">
    <div class="tab-pane fade show active" id="supplierPanel" role="tabpanel">
      <h4>1. Supplier contact details</h4>
      <div class="col-sm">
        <div class="mb-2">
          <label for="supplier_name">Name</label>
          <select name="supplier_name" id="supplier_name" class="form-control selectpicker" data-live-search="true">
          	<option selected disabled>Choose a supplier</option>
            <?php while ($row_ingSupplier = mysqli_fetch_array($res_ingSupplier)){ 
              $supLogo = mysqli_fetch_array(mysqli_query($conn,"SELECT docData FROM documents WHERE ownerID = '".$row_ingSupplier['ownerID']."' AND type = '3'")); ?>
              <option value="<?=$row_ingSupplier['id']?>" data-city="<?=$row_ingSupplier['city'];?>" data-email="<?=$row_ingSupplier['email'];?>" data-url="<?=$row_ingSupplier['url'];?>" data-telephone="<?=$row_ingSupplier['telephone'];?>" data-country="<?=$row_ingSupplier['country'];?>" data-po="<?=$row_ingSupplier['po'];?>" data-address="<?=$row_ingSupplier['address'];?>"><?=$row_ingSupplier['name'];?></option>
            <?php } ?>
          </select>
        </div>

        <div class="row">
          <div class="col-sm">
            <div class="mb-2">
              <label for="address">Address</label>
              <input class="form-control" name="address" type="text" id="address" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-6">
            <div class="mb-2">
              <label for="po">Postal Code</label>
              <input name="po" type="text" class="form-control" id="po" required>
            </div>
          </div>
          <div class="col-sm">
            <div class="mb-2">
              <label for="country">Country</label>
              <input name="country" type="text" class="form-control" id="country" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-6">
            <div class="mb-2">
              <label for="telephone">Phone</label>
              <input name="telephone" type="text" class="form-control" id="telephone" required>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="mb-2">
              <label for="email">Email</label>
              <input name="email" type="text" class="form-control" id="email" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm">
            <div class="mb-2">
              <label for="url">Website</label>
              <input class="form-control" name="url" type="text" id="url" required>
            </div>
          </div>
        </div>

      
      </div>
      <hr>
      <button class="btn btn-secondary" id="supplierContinue">Continue</button>
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
            <input class="form-control" name="prodUse" type="text" id="prodUse" required>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm">
          <div class="mb-2">
            <label for="sdsCountry">Country</label>
            <input name="sdsCountry" type="text" class="form-control" id="country" required>
          </div>
        </div>
        <div class="col-sm">
          <div class="mb-2">
            <label for="sdsLang">Language</label>
            <input name="sdsLang" type="text" class="form-control" id="country" required>
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
      <button class="btn btn-secondary" id="productContinue">Continue</button>
    </div>

    <div class="tab-pane fade" id="compoPanel" role="tabpanel">
      <h4>3. Product composition</h4>
      <div class="alert alert-warning"><strong><i class="fa-solid fa-triangle-exclamation mx-2"></i>Updating data bellow will also update main material's data!</strong></div>

      
       	<div class="text-right">
  		<div class="btn-group">
   			<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
    		<div class="dropdown-menu dropdown-menu-right">
        		<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addComposition"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
        		<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addCSV"><i class="fa-solid fa-file-import mx-2"></i>Upload CSV</a></li>
    		</div>
  		</div>                    
	</div>
      <table id="tdCompositions" class="table table-striped table-bordered" style="width:100%">
      	<thead>
          <tr>
              <th>Name</th>
              <th>CAS</th>
              <th>EINECS</th>
              <th>Concentration</th>
              <th></th>
          </tr>
       </thead>
    </table>

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

      <hr />
      <button class="btn btn-secondary" id="compoContinue">Continue</button>
    </div>

    <div class="tab-pane fade" id="safety_info">
        <div class="alert alert-warning"><strong><i class="fa-solid fa-triangle-exclamation mx-2"></i>Updating data bellow will also update main material's data!</strong></div>
        <div id="fetch_safety">
            <div class="row justify-content-md-center">
                <div class="loader"></div>
             </div>
        </div>
        <hr />
        
         <button class="btn btn-secondary" id="ghsContinue">Continue</button>
    </div>
    
    <div class="tab-pane fade" id="reviewPanel" role="tabpanel">
      <h4>Review</h4>
      <div id="jsonOut"></div>
      <button class="btn btn-primary btn-block" id="commitSDS">Save SDS data</button>
    </div>
  </div>

  <div class="progress mt-5">
    <div class="progress-bar" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">Step 1 of 5</div>
  </div>


  <div class="modal-footer mt-4">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-primary">Save for later</button>
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
		dropdownParent: $('#createSDS'),
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

    // Initialize DataTable variable
    var tdCompositions;

    $('#productContinue').click(function (e) {
        // Check if DataTable instance already exists
        if ($.fn.DataTable.isDataTable('#tdCompositions')) {
            // Destroy existing DataTable instance
            $('#tdCompositions').DataTable().destroy();
        }

        // Initialize DataTable
        tdCompositions = $('#tdCompositions').DataTable({
            columnDefs: [
                { className: 'text-center', targets: '_all' },
                { orderable: false, targets: [4] },
            ],
            dom: 'lfrtip',
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
                emptyTable: 'No compositions added yet.',
                search: 'Search:'
            },
            ajax: {    
                url: '/core/list_ing_compos_data.php?id=' + btoa(prodName)
            },
            columns: [
                { data : 'name', title: 'Name', render: cmpName },
                { data : 'cas', title: 'CAS', render: cmpCAS},
                { data : 'ec', title: 'EINECS', render: cmpEC},
                { data : 'percentage', title: 'Percentage', render: cmpPerc},
                { data : null, title: '', render: cmpActions},           
            ],
            order: [[ 1, 'asc' ]],
            lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
            pageLength: 20,
            displayLength: 20,        
        });

        function cmpName(data, type, row){
            return '<i class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</i>';    
        };

        function cmpCAS(data, type, row){
            return '<i class="cas pv_point_gen" data-name="cas" data-type="text" data-pk="'+row.id+'">'+row.cas+'</i>';    
        };

        function cmpEC(data, type, row){
            return '<i class="ec pv_point_gen" data-name="ec" data-type="text" data-pk="'+row.id+'">'+row.ec+'</i>';    
        };

        function cmpPerc(data, type, row){
            return '<i class="percentage pv_point_gen" data-name="percentage" data-type="text" data-pk="'+row.id+'">'+row.percentage+'</i>';    
        };

        function cmpActions(data, type, row){
            return '<a href="#" id="cmpDel" class="fas fa-trash link-danger" data-id="'+row.id+'" data-name="'+row.name+'"></a>';
        };
    	
	});

	$('#tdCompositions').editable({
		container: 'body',
		selector: 'i.name',
		type: 'POST',
		url: "/pages/update_data.php?composition=update",
		title: 'Name'
	});
	
	$('#tdCompositions').editable({
	   container: 'body',
	   selector: 'i.cas',
	   type: 'POST',
	   url: "/pages/update_data.php?composition=update",
	   title: 'CAS'
	});
	
	$('#tdCompositions').editable({
	   container: 'body',
	   selector: 'i.ec',
	   type: 'POST',
	   url: "/pages/update_data.php?composition=update",
	   title: 'EINECS',
	});
	
	$('#tdCompositions').editable({
		container: 'body',
		selector: 'i.percentage',
		type: 'POST',
		url: "/pages/update_data.php?composition=update",
		title: 'Percentage'
	});
	
	$('#tdCompositions').editable({
		container: 'body',
		selector: 'i.GHS',
		type: 'POST',
		url: "/pages/update_data.php?composition=update",
		title: 'GHS'
	});
	


	$('#tdCompositions').on('click', '[id*=cmpDel]', function () {
		var cmp = {};
		cmp.ID = $(this).attr('data-id');
		cmp.Name = $(this).attr('data-name');
	
		bootbox.dialog({
		   title: "Confirm removal",
		   message : 'Remove <strong>'+ cmp.Name +'</strong> from the list?',
		   buttons :{
			   main: {
				   label : "Remove",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({ 
						url: '/pages/update_data.php', 
						type: 'POST',
						data: {
							composition: 'delete',
							allgID: cmp.ID,
						},
						dataType: 'json',
						success: function (data) {
							reload_cmp_data();
						}
					  });
	
					 return true;
				   }
			   },
			   cancel: {
				   label : "Cancel",
				   className : "btn-secondary",
				   callback : function() {
					   return true;
				   }
			   }   
		   },onEscape: function () {return true;}
	   });
	});
	
	
	$('#addComposition').on('click', '[id*=cmpAdd]', function () {
		$.ajax({ 
			url: '/pages/update_data.php', 
			type: 'POST',
			data: {
				composition: 'add',
				allgName: $("#allgName").val(),
				allgPerc: $("#allgPerc").val(),
				allgCAS: $("#allgCAS").val(),
				allgEC: $("#allgEC").val(),	
				GHS: $("#GHS").val(),	
				addToIng: $("#addToIng").is(':checked'),
				addToDeclare: $("#addToDeclare").is(':checked'),
				ing: btoa(prodName)
			},
			dataType: 'json',
			success: function (data) {
				if (data.success) {
					var msg = '<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check mr-2"></i><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
					$("#allgName").val('');
					$("#allgCAS").val('');
					$("#allgEC").val('');
					$("#allgPerc").val('');
					$("#GHS").val('');
					reload_cmp_data();
				}else{
					var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				}
			
				$('#inf').html(msg);
	
			}
		  });
	});
	
	$('#addCSV').on('click', '[id*=cmpCSV]', function () {
		$("#CSVImportMsg").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
		$("#cmpCSV").prop("disabled", true);
			
		var fd = new FormData();
		var files = $('#CSVFile')[0].files;
	
		if(files.length > 0 ){
		fd.append('CSVFile',files[0]);
		$.ajax({
		   url: '/pages/upload.php?type=cmpCSVImport&ingID=' + btoa(prodName),
		   type: 'POST',
		   data: fd,
		   contentType: false,
		   processData: false,
				 cache: false,
		   success: function(response){
			 if(response != 0){
				$("#CSVImportMsg").html(response);
				$("#cmpCSV").prop("disabled", false);
				reload_cmp_data();
			  }else{
				$("#CSVImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
				$("#cmpCSV").prop("disabled", false);
			  }
			},
		 });
		}else{
			$("#CSVImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
			$("#cmpCSV").prop("disabled", false);
		}
	});
	
	function reload_cmp_data() {
		$('#tdCompositions').DataTable().ajax.reload(null, true);
	};
	
});
</script>
<!-- ADD COMPOSITION-->
<div class="modal fade" id="addComposition" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addComposition" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addComposition">Add composition</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="inf"></div>
        <div class="mb-3">
	        <label for="allgName" class="form-label">Name</label>
    	    <input class="form-control" name="allgName" type="text" id="allgName" />
        </div>
        <div class="mb-3">
        	<label for="allgCAS" class="form-label">CAS</label>
        	<input class="form-control" name="allgCAS" type="text" id="allgCAS" />
        </div>
        <div class="mb-3">
        	<label for="allgEC" class="form-label">EINECS</label>
        	<input class="form-control" name="allgEC" type="text" id="allgEC" />
        </div>
        <div class="mb-3">
	        <label for="allgPerc" class="form-label">Percentage</label>
    	    <input class="form-control" name="allgPerc" type="text" id="allgPerc" />
        </div>
        <div class="mb-3">
	        <label for="GHS" class="form-label">GHS Classification</label>
    	    <input class="form-control" name="GHS" type="text" id="GHS" />
        </div>        
        <hr class="dropdown-divider" />
        <div class="form-check">
            <input class="form-check-input" name="addToDeclare" type="checkbox" id="addToDeclare" value="1" />
            <label class="form-check-label" for="addToDeclare">To declare in warnings</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" name="addToIng" type="checkbox" id="addToIng" value="1" />
            <label class="form-check-label" for="addToIng">Add to ingredients</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="cmpAdd" value="Add">
      </div>
    </div>
  </div>
</div>
</div>

<!--ADD FROM CSV MODAL-->
<div class="modal fade" id="addCSV" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addCSV" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import CSV</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      
      <div id="CSVImportMsg"></div>
     	<div class="mb-3">
  			<label for="formFile" class="form-label">Choose file</label>
            <input type="file" name="CSVFile" id="CSVFile" class="form-control" />
        </div>
        <hr class="dropdown-divider" />        
        <p>CSV format: <strong>ingredient,CAS,EINECS,percentage,GHS</strong></p>
        <p>Example: <em><strong>Citral,5392-40-5,226-394-6,0.15,Skin Irrit. 2-Eye Irrit</strong></em></p>
        <p>Duplicates will be ignored.</p>
            
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="cmpCSV" class="btn btn-primary" id="cmpCSV" value="Import">
      </div>
    </div>
  </div>
</div>
</div>

<!-- TOAST -->
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 11">
  <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
    <div class="toast-header">
      <strong class="me-auto" id="toast-title">...</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$ingID = mysqli_real_escape_string($conn, $_GET["id"]);

$q = mysqli_query($conn, "SELECT * FROM suppliers WHERE ingID = '$ingID' ORDER BY preferred");
$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name,physical_state FROM ingredients WHERE id ='$ingID'"));
$res_ingSupplier = mysqli_query($conn, "SELECT id,name,min_ml,min_gr FROM ingSuppliers ORDER BY name ASC");

if($ing['physical_state'] == 1){
	$mUnit = 'ml';
}elseif($ing['physical_state'] == 2){
	$mUnit = 'grams';
}
?>
<?php if($_GET['standAlone'] == 1){ ?>
	<html lang="en" data-bs-theme="<?=$settings['bs_theme']?>">

	<link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    
	<script src="/js/jquery/jquery.min.js"></script>
	<script src="/js/bootstrap.bundle.min.js"></script>
	<script src="/js/bootstrap-select.js"></script>
	<script src="/js/bootstrap-editable.js"></script>
	<script src="/js/datatables.min.js"></script>
	<script src="/js/bootbox.min.js"></script>

    <link href="/css/datatables.min.css" rel="stylesheet"/>
	<link href="/css/sb-admin-2.css" rel="stylesheet">
	<link href="/css/bootstrap-select.min.css" rel="stylesheet">
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/vault.css" rel="stylesheet">
	<link href="/css/bootstrap-editable.css" rel="stylesheet">
	<link href="/css/mgmIngredient.css" rel="stylesheet">
    
    <style>
		body { margin: 10; }
	</style>
    </html>
<?php } ?>

<h3>Suppliers</h3>
<hr>
<div id="supMsg"></div>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
        <div class="dropdown-menu">
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addSupplier"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
        </div>
    </div>                    
  </div>


</div>
<table id="tdIngSup" class="table table-striped" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>eShop</th>
          <th>Price</th>
          <th>Size</th>
          <th>Measurement Unit</th>
          <th>Manufacturer</th>
          <th>Batch</th>
          <th>Purchased</th>
          <th>In Stock</th>
          <th>Internal SKU</th>
          <th>Supplier SKU</th>
          <th>Storage location</th>
          <th>Status</th>
          <th>Last updated</th>
          <th>Created</th>
          <th data-priority="1"></th>
      </tr>
   </thead>
</table>
<script>
$(document).ready(function() {
	$("#supplier_name").change(function () {
    	vol = $(this).children(':selected').data('vol');
    	$("#supplier_size").focus().val(vol);    
	});
	$('.selectpicker').selectpicker();
	
	$('[data-bs-toggle="tooltip"]').tooltip();
	var tdIngSup = $('#tdIngSup').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [15] },
			{ responsivePriority: 1, targets: 0 }
		],
		dom: 'lfrtip',
		processing: true,
		responsive: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: 'No suppliers added yet.',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
		ajax: {	url: '/core/list_ing_suppliers_data.php?id=<?=$ingID?>' },
		columns: [
			{ data : 'supplierName', title: 'Name', render: sName },
			{ data : 'supplierLink', title: 'eShop', render: sLink},
			{ data : 'price', title: 'Price(<?=$settings['currency']?>)', render: sPrice},
			{ data : 'size', title: 'Size', render: sSize},
			{ data : 'mUnit', title: 'Measurement Unit', render: mUnit},
			{ data : 'manufacturer', title: 'Manufacturer', render: sManufacturer},
			{ data : 'batch', title: 'Batch', render: sBatch},
			{ data : 'purchased', title: 'Purchased', render: sPurchased},
			{ data : 'stock', title: 'In Stock', render: sStock},
			{ data : 'internal_sku', title: 'Internal SKU', render: internal_sku},
			{ data : 'supplier_sku', title: 'Supplier SKU', render: supplier_sku},
			{ data : 'storage_location', title: 'Storage location', render: storage_location},
			{ data : 'status', title: 'Status', render: status},
			{ data : 'updated', title: 'Last update'},
			{ data : 'created', title: 'Created'},
			{ data : null, title: '', render: sActions},		   
		],
		order: [[ 1, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
		pageLength: 20,
		displayLength: 20,
		
		stateSave: true,
		stateDuration : -1,
		stateLoadCallback: function (settings, callback) {
			$.ajax( {
				url: '/core/update_user_settings.php?set=listIngSuppliers&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listIngSuppliers&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},
	});
	
	
	$('#addSupplier').on('click', '[id*=sAdd]', function () {
	  $.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			ingSupplier: 'add',
			supplier_id: $("#supplier_name").val(),
			supplier_link: $("#supplier_link").val(),
			supplier_size: $("#supplier_size").val(),	
			supplier_price: $("#supplier_price").val(),				
			supplier_manufacturer: $("#supplier_manufacturer").val(),
			supplier_batch: $("#supplier_batch").val(),
			purchased: $("#purchased").val(),
			stock: $("#stock").val(),
			mUnit: $("#mUnit").val(),
			status: $("#status").val(),
			supplier_sku: $("#supplier_sku").val(),
			internal_sku: $("#internal_sku").val(),
			storage_location: $("#storage_location").val(),

			ingID: '<?=$ingID;?>'
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check mx-2"></i><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				$("#supplier_batch").val('');
				$("#supplier_link").val('');
				$("#supplier_size").val('');
				$("#supplier_price").val('');
				$("#supplier_manufacturer").val('');
				$("#mUnit").val('');
				$("#supplier_sku").val(''),
			    $("#internal_sku").val(''),
				$("#storage_location").val(''),
				reload_sup_data();
			}else{
				var msg ='<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-triangle-exclamation mx-2"></i><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#supplier_inf').html(msg);
		}
	  });
	});
	
	
});//END DOC
 Object.getPrototypeOf($('#purchased')).size = function() { return this.length; }; // Workaround for https://github.com/Eonasdan/bootstrap-datetimepicker/issues/1714
function sName(data, type, row){
	if(row.preferred == 1){
		data = '<i class="ingSupplierID pv_point_gen" data-name="ingSupplierID" data-type="select" data-pk="'+row.id+'"><i class="fas fa-star pv_point_gen pv_point_gen_color mx-2"></i>'+row.supplierName+'</i>';  
	}else{
		data = '<i class="ingSupplierID pv_point_gen" data-name="ingSupplierID" data-type="select" data-pk="'+row.id+'">'+row.supplierName+'</i>';  
	}
	
	return data;
};

function sLink(data, type, row){
	return '<i class="supplierLink pv_point_gen" data-name="supplierLink" data-type="textarea" data-pk="'+row.id+'">'+row.supplierLink+'</i>';    
};

function sPrice(data, type, row){
	return '<i id="'+row.ingSupplierID+'" class="price pv_point_gen" data-name="price" data-type="text" data-pk="'+row.id+'">'+row.price+'</i>';    
};

function sSize(data, type, row){
	return '<i class="size pv_point_gen" data-name="size" data-type="text" data-pk="'+row.id+'">'+row.size+'</i>';    
};

function mUnit(data, type, row){
	return '<i class="mUnit pv_point_gen" data-name="mUnit" data-type="select" data-pk="'+row.id+'">'+row.mUnit+'</i>';
};

function sManufacturer(data, type, row){
	return '<i class="manufacturer pv_point_gen" data-name="manufacturer" data-type="text" data-pk="'+row.id+'">'+row.manufacturer+'</i>';    
};

function sBatch(data, type, row){
	return '<i class="batch pv_point_gen" data-name="batch" data-type="text" data-pk="'+row.id+'">'+row.batch+'</i>';    
};

function sPurchased(data, type, row){
	return '<i class="purchased pv_point_gen" data-name="purchased" data-type="date" data-pk="'+row.id+'">'+row.purchased+'</i>';    
};

function sStock(data, type, row){
	return '<i class="stock pv_point_gen" data-name="stock" data-type="text" data-pk="'+row.id+'">'+row.stock+'</i>';    
};

function status(data, type, row){
	if(row.status == 0){
		var data = '<span class="pv-label badge bg-default">Unkwnown</span>';
	}
	if(row.status == 1){
		var data = '<span class="pv-label badge bg-success">Available</span>';
	}
	if(row.status == 2){
		var data = '<span class="pv-label badge bg-warning">Limited Availability</span>';
	}
	if(row.status == 3){
		var data = '<span class="pv-label badge bg-danger">Discontinued</span>';
	}
	
	return '<i class="status pv_point_gen" data-name="status" data-type="select" data-pk="'+row.id+'">'+data+'</i>';
};

function internal_sku(data, type, row){
	return '<i class="internal_sku pv_point_gen" data-name="internal_sku" data-type="text" data-pk="'+row.id+'">'+row.internal_sku+'</i>';    
};

function supplier_sku(data, type, row){
	return '<i class="supplier_sku pv_point_gen" data-name="supplier_sku" data-type="text" data-pk="'+row.id+'">'+row.supplier_sku+'</i>';    
};

function storage_location(data, type, row){
	return '<i class="storage_location pv_point_gen" data-name="storage_location" data-type="text" data-pk="'+row.id+'">'+row.storage_location+'</i>';    
};

function sActions(data, type, row){
	data = '<div class="dropdown">' +
			'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu">';
	
	if(row.preferred == 0){
		data += '<li><a class="dropdown-item" href="#" id="prefSID" data-status="1" data-id="'+row.ingSupplierID+'"><i class="far fa-star pv_point_gen mx-2"></i>Set as preferred</a></li>';
	}
	
	data += '<li><a class="dropdown-item" href="#" id="getPrice" data-name="'+row.supplierName+'" data-id="'+encodeURIComponent(row.ingSupplierID)+'" data-link="'+row.supplierLink+'" data-size="'+row.size+'" data-toggle="tooltip" data-placement="top" title="Get the latest price from the supplier."><i class="fas fa-sync pv_point_gen_color mx-2"></i>Update price</a></li>';
	data += '<li><a class="dropdown-item" href="'+row.supplierLink+'" target="_blank"><i class="fas fa-store mx-2"></i>Go to supplier</a></li>';
	data += '<div class="dropdown-divider"></div>';
	data += '<li><a href="#" id="sDel" class="dropdown-item link-danger" data-id="'+row.id+'" data-name="'+row.supplierName+'"><i class="fas fa-trash link-danger mx-2"></i>Delete supplier</a></li>'; 
	data += '</ul></div>';
	return data;
	
}

$('#tdIngSup').editable({
	pvnoresp: false,
	highlight: false,
	title: "Supplier's Name",
	container: 'body',
	selector: 'i.ingSupplierID',
	type: 'POST',
	emptytext: "",
	emptyclass: "",
  	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID?>",
    source: [
			 <?php
				$res_ing = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY name ASC");
				while ($r_ing = mysqli_fetch_array($res_ing)){
					echo '{value: "'.htmlspecialchars($r_ing['id']).'", text: "'.htmlspecialchars($r_ing['name']).'"},';
			}
			?>
    ],
    success: function (data) {
		reload_sup_data();
	}
});


$('#tdIngSup').editable({
	pvnoresp: false,
	highlight: false,
	title: "Availability status",
	container: 'body',
	selector: 'i.status',
	type: 'POST',
	emptytext: "",
	emptyclass: "",
  	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID?>",
    source: [
			 {value: '1', text: 'Available'},
             {value: '2', text: 'Limited availability'},
			 {value: '3', text: 'Discontinued / Cannot sourced'},
    ],
    success: function (data) {
		reload_sup_data();
	}
});

$('#tdIngSup').editable({
	container: 'body',
	selector: 'i.supplierLink',
	type: 'POST',
	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Store link',
	validate: function(value){
		if($.trim(value) == ''){
			return 'This field is required';
		}
  	}
});
  
$('#tdIngSup').editable({
	container: 'body',
	selector: 'i.price',
	type: 'POST',
	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Price',
	validate: function(value){
		if($.trim(value) == ''){
			return 'This field is required';
		}
		if($.isNumeric(value) == '' ){
			return 'Numbers only!';
		}
  	}
});
	
$('#tdIngSup').editable({
  	container: 'body',
  	selector: 'i.size',
  	type: 'POST',
	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Size',
	success: function (data) {
		reload_sup_data();
	}
});
	
$('#tdIngSup').editable({
	pvnoresp: false,
	highlight: false,
	emptytext: "",
	emptyclass: "",
  	container: 'body',
  	selector: 'i.mUnit',
  	type: 'POST',
	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Measurement Unit',
	source: [
			 {value: 'ml', text: 'Milliliter'},
			 {value: 'g', text: 'Grams'},
			 {value: 'L', text: 'Liter'},
			 {value: 'fl. oz.', text: 'Fluid ounce (fl. oz.)'}
	],
	success: function (data) {
		reload_sup_data();
	}
});

$('#tdIngSup').editable({
	container: 'body',
	selector: 'i.manufacturer',
	type: 'POST',
	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Manufacturer',
	success: function (data) {
		reload_sup_data();
	}
});

$('#tdIngSup').editable({
	container: 'body',
	selector: 'i.batch',
	type: 'POST',
	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Batch',
	success: function (data) {
		reload_sup_data();
	}
});

$('#tdIngSup').editable({
	container: 'body',
	selector: 'i.purchased',
	type: 'POST',
	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Purchase date',
	type: 'date',
	success: function (data) {
		reload_sup_data();
	}
});
  
$('#tdIngSup').editable({
	container: 'body',
	selector: 'i.stock',
	type: 'POST',
	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'In Stock',
	success: function (data) {
		reload_sup_data();
	}
});

$('#tdIngSup').editable({
  	container: 'body',
  	selector: 'i.internal_sku',
  	type: 'POST',
	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Internal SKU',
	success: function (data) {
		reload_sup_data();
	}
});

$('#tdIngSup').editable({
  	container: 'body',
  	selector: 'i.supplier_sku',
  	type: 'POST',
	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Supplier SKU',
	success: function (data) {
		reload_sup_data();
	}
});

$('#tdIngSup').editable({
  	container: 'body',
  	selector: 'i.storage_location',
  	type: 'POST',
	url: "/pages/update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Storage location',
	success: function (data) {
		reload_sup_data();
	}
});


$('#tdIngSup').on('click', '[id*=prefSID]', function () {
	var s = {};
	s.ID = $(this).attr('data-id');
   	s.Status = $(this).attr('data-status');

	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'GET',
		data: {
			ingSupplier: 'preferred',
			sID: s.ID,
			status: s.Status,
			ingID: '<?=$ingID?>'
			},
		dataType: 'html',
		success: function (data) {
			reload_sup_data();
		}
	  });

});

$('#tdIngSup').on('click', '[id*=getPrice]', function () {
	var s = {};
	s.ID = $(this).attr('data-id');
   	s.Name = $(this).attr('data-name');
	s.Link = $(this).attr('data-link');
   	s.Size = $(this).attr('data-size');

	$('#supMsg').html('<div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Please wait, trying to fetch supplier data...</strong></div>');
		$('#' + s.ID).html('<img src="/img/loading.gif"/>');
		$.ajax({ 
			url: '/pages/update_data.php', 
			type: 'POST',
			data: {
				ingSupplier: 'getPrice',
				sLink: s.Link,
				size: s.Size,
				ingSupplierID: s.ID,
				ingID: '<?=$ingID?>'
			},
			dataType: 'json',
			success: function (data) {
				if (data.success) {
		 	 		var msg = '<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check mx-2"></i><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				}else{
					var msg = '<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-triangle-exclamation mx-2"></i><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				}
				$('#supMsg').html(msg);
				reload_sup_data();
			}
		  });
	
});
	
$('#tdIngSup').on('click', '[id*=sDel]', function () {
	var ing = {};
	ing.ID = $(this).attr('data-id');
	ing.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm supplier removal",
       message : 'Remove <strong>'+ ing.Name +'</strong> from the list?',
       buttons :{
           main: {
               label : "Remove",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: '/pages/update_data.php', 
					type: 'GET',
					data: {
						ingSupplier: 'delete',
						sID: ing.ID,
						ingID: '<?=$ingID?>'
					},
					dataType: 'html',
					success: function (data) {
						reload_sup_data();
					},
					error: function (xhr, status, error) {
						$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
						$('.toast-header').removeClass().addClass('toast-header alert-danger');
						$('.toast').toast('show');
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



function reload_sup_data() {
    $('#tdIngSup').DataTable().ajax.reload(null, true);
};
</script>

<!-- ADD SUPPLIER-->
<div class="modal fade" id="addSupplier" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addSupplier" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add supplier for <?php echo $ing['name']; ?></h5>
      </div>
      <div class="modal-body">
      <div id="supplier_inf"></div>
		<div class="col-sm">
        	<div class="form-row mb-2">
    			<label for="supplier_name">Name</label>
                <select name="supplier_name" id="supplier_name" class="form-control selectpicker" data-live-search="true">
                <?php while ($row_ingSupplier = mysqli_fetch_array($res_ingSupplier)){ ?>
                    <option value="<?=$row_ingSupplier['id']?>" data-vol="<?php if($ing['physical_state'] == '1'){ echo $row_ingSupplier['min_ml']; }elseif($ing['physical_state'] == '2'){ echo $row_ingSupplier['min_gr'];} ?>" ><?=$row_ingSupplier['name'];?></option>
                <?php	}	?>
                </select>
  			</div>
            
        	<div class="form-row mb-2">
    			<label for="supplier_link">URL*</label>
                <input class="form-control" name="supplier_link" type="text" id="supplier_link" />
            </div>
            
            <div class="row">
    			<div class="col-md-6">
                <label for="supplier_price">Price*</label>
                <div class="input-group mb-2">
                    <span class="input-group-text"><?php echo $settings['currency']; ?></span>
                    <input class="form-control" name="supplier_price" type="text" id="supplier_price" />
                </div>
            </div>
            <div class="col-md-6">
                <label for="supplier_size">Size*</label>
                <div class="input-group mb-2">
                    <span class="input-group-text"><?php if($ing['physical_state'] == '1'){ echo 'ml'; }elseif($ing['physical_state'] == '2'){ echo 'grams'; }else{ echo $settings['mUnit']; }?></span>
                    <input class="form-control" name="supplier_size" type="text" id="supplier_size" value="10" />
                </div>
                </div>
            </div>
            
           <div class="row">
			<div class="col-md-6">
                <div class="form-row mb-2">
                    <label for="supplier_manufacturer">Manufacturer</label>
                    <input class="form-control" name="supplier_manufacturer" type="text" id="supplier_manufacturer" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-row mb-2">
                    <label for="supplier_batch">Batch</label>
                    <input class="form-control" name="supplier_batch" type="text" id="supplier_batch" />
                </div>
            </div>
           </div>
			<div class="form-row mb-2">
    			<label for="purchased">Purchased</label>
	            <input class="form-control" name="purchased" type="date" id="purchased" />
            </div>

          	<div class="form-row mb-2">
    			<label for="status">Availability status</label>
                <select name="status" id="status" class="form-control">
                  <option value="1">Available</option>
                  <option value="2">Limited availability</option>
                  <option value="3">Discontinued / Cannot sourced</option>
                </select>
            </div>    
            <div class="form-row mb-2">
    			<label for="mUnit">Measurement Unit</label>
                <select name="mUnit" id="mUnit" class="form-control">
                  <option value="ml">Milliliter</option>
                  <option value="g">Grams</option>
                  <option value="L">Liter</option>
                  <option value="fl. oz.">Fluid ounce (fl. oz.)</option>
                </select>
            </div>
          </div>
          
          <div class="row">
              <div class="col-sm-6">
                <div class="form-row mb-2">
                    <label for="supplier_sku">Supplier SKU</label>
                    <input class="form-control" name="supplier_sku" type="text" id="supplier_sku" />
                </div>
               </div>
              <div class="col-sm-6">

			  	<div class="form-row mb-2">
    				<label for="internal_sku">Internal SKU</label>
                	<input class="form-control" name="internal_sku" type="text" id="internal_sku" />
            	</div> 
            </div>
            <div class="col-sm-6">
                <div class="form-row mb-2">
                    <label for="storage_location">Storage location</label>
                    <input class="form-control" name="storage_location" type="text" id="storage_location" />
                </div>
             </div>
            <div class="col-sm-6">
				<div class="form-row mb-2">
    				<label for="stock">In stock</label>
	            	<input name="stock" type="text" class="form-control" id="stock" value="0" />
    	    	</div>
          	</div>
          </div>
     	</div>
          <div class="dropdown-divider"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <input type="submit" name="button" class="btn btn-primary" id="sAdd" value="Add">
          </div>
        </div>
	</div>
</div>

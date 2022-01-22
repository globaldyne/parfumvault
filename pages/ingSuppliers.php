<?php

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$ingID = mysqli_real_escape_string($conn, $_GET["id"]);

$q = mysqli_query($conn, "SELECT * FROM suppliers WHERE ingID = '$ingID' ORDER BY preferred");
$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT physical_state FROM ingredients WHERE id ='$ingID'"));

if($_GET['s'] == '1'){

?>
<link href="../css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

<link href="../css/datatables.min.css" rel="stylesheet">
    <script src="../js/jquery/jquery.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/bootbox.min.js"></script>

<script src="../js/datatables.min.js"></script>
  <link href="../css/bootstrap-editable.css" rel="stylesheet">
  <script src="../js/bootstrap-editable.js"></script>

  <link href="../css/bootstrap.min.css" rel="stylesheet">

<?php } ?>

<h3>Suppliers</h3>
<hr>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addSupplier">Add new</a>
        </div>
    </div>                    
  </div>


</div>
<table id="tdIngSup" class="table table-striped table-bordered nowrap" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>eShop</th>
          <th>Price</th>
          <th>Size</th>
          <th>Manufacturer</th>
          <th>Batch</th>
          <th>Purchased</th>
          <th>In Stock</th>
          <th>Actions</th>
      </tr>
   </thead>
</table>
<script type="text/javascript" language="javascript" >
$(document).ready(function() {
	$('[data-toggle="tooltip"]').tooltip();
	var tdIngSup = $('#tdIngSup').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
	],
	dom: 'lfrtip',
	processing: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
		emptyTable: 'No suppliers added yet.',
		search: 'Search:'
		},
	ajax: {	url: '/core/list_ing_suppliers_data.php?id=<?=$ingID?>' },
	columns: [
			  { data : 'supplierName', title: 'Name', render: sName },
			  { data : 'supplierLink', title: 'eShop', render: sLink},
			  { data : 'price', title: 'Price', render: sPrice},
			  { data : 'size', title: 'Size', render: sSize},
			  { data : 'manufacturer', title: 'Manufacturer', render: sManufacturer},
			  { data : 'batch', title: 'Batch', render: sBatch},
			  { data : 'purchased', title: 'Purchased', render: sPurchased},
			  { data : 'stock', title: 'In Stock', render: sStock},
			  { data : null, title: 'Actions', render: sActions},		   
			 ],
	order: [[ 1, 'asc' ]],
	lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
	pageLength: 20,
	displayLength: 20,		
	});
});
 Object.getPrototypeOf($('#purchased')).size = function() { return this.length; }; // Workaround for https://github.com/Eonasdan/bootstrap-datetimepicker/issues/1714
function sName(data, type, row){
	return '<a href="#" class="ingSupplierID pv_point_gen" data-name="ingSupplierID" data-type="select" data-pk="'+row.id+'">'+row.supplierName+'</a>';    
}
function sLink(data, type, row){
	return '<a href="#" class="supplierLink pv_point_gen" data-name="supplierLink" data-type="textarea" data-pk="'+row.id+'">'+row.supplierLink+'</a>';    
}

function sPrice(data, type, row){
	return '<a href="#" id="'+row.ingSupplierID+'" class="price pv_point_gen" data-name="price" data-type="text" data-pk="'+row.id+'">'+row.price+'</a>';    
}

function sSize(data, type, row){
	return '<a href="#" class="size pv_point_gen" data-name="size" data-type="text" data-pk="'+row.id+'">'+row.size+'</a>';    
}

function sManufacturer(data, type, row){
	return '<a href="#" class="manufacturer pv_point_gen" data-name="manufacturer" data-type="text" data-pk="'+row.id+'">'+row.manufacturer+'</a>';    
}

function sBatch(data, type, row){
	return '<a href="#" class="batch pv_point_gen" data-name="batch" data-type="text" data-pk="'+row.id+'">'+row.batch+'</a>';    
}

function sPurchased(data, type, row){
	return '<a href="#" class="purchased pv_point_gen" data-name="purchased" data-type="date" data-pk="'+row.id+'">'+row.purchased+'</a>';    
}

function sStock(data, type, row){
	return '<a href="#" class="stock pv_point_gen" data-name="stock" data-type="text" data-pk="'+row.id+'">'+row.stock+'</a>';    
}

function sActions(data, type, row){
	if(row.preferred == 1){
		var pref = '<a href="#" class="fas fa-star"></a>&nbsp;';
	}else{
		var pref = '<a href="#" id="prefSID" data-status="1" data-id="'+row.ingSupplierID+'" class="far fa-star" data-toggle="tooltip" data-placement="top" title="Set as preferred supplier."></a>&nbsp;';
	}
	return pref + '<a href="#" id="getPrice" data-name="'+row.supplierName+'" data-id="'+row.ingSupplierID+'" data-link="'+row.supplierLink+'" data-size="'+row.size+'" data-toggle="tooltip" data-placement="top" title="Get the latest price from the supplier." class="fas fa-sync"></a>&nbsp;<a href="'+row.supplierLink+'" target="_blank" class="fas fa-store" data-toggle="tooltip" data-placement="top" title="Open supplier\'s web page."></a>&nbsp;<a href="#" id="sDel" class="fas fa-trash" data-id="'+row.id+'" data-name="'+row.supplierName+'"></a>';    
}

$('#tdIngSup').editable({
	pvnoresp: false,
	highlight: false,
	container: 'body',
	selector: 'a.ingSupplierID',
	type: 'POST',
	emptytext: "",
	emptyclass: "",
  	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID?>",
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
	  container: 'body',
	  selector: 'a.supplierLink',
	  type: 'POST',
	  url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	  title: 'Store link',
});
  
$('#tdIngSup').editable({
	  container: 'body',
	  selector: 'a.price',
	  type: 'POST',
	  url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	  title: 'Price',
});
	
$('#tdIngSup').editable({
  	container: 'body',
  	selector: 'a.size',
  	type: 'POST',
	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Size',
	success: function (data) {
			reload_sup_data();
	}
});
 
$('#tdIngSup').editable({
	container: 'body',
	selector: 'a.manufacturer',
	type: 'POST',
	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Manufacturer',
	success: function (data) {
			reload_sup_data();
	}
});

$('#tdIngSup').editable({
	container: 'body',
	selector: 'a.batch',
	type: 'POST',
	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Batch',
	success: function (data) {
			reload_sup_data();
	}
});

$('#tdIngSup').editable({
	container: 'body',
	selector: 'a.purchased',
	type: 'POST',
	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Purchase date',
	type: 'date',
	success: function (data) {
			reload_sup_data();
	}
});
  
$('#tdIngSup').editable({
	container: 'body',
	selector: 'a.stock',
	type: 'POST',
	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'In Stock',
	success: function (data) {
			reload_sup_data();
	}
});

$('#tdIngSup').on('click', '[id*=prefSID]', function () {
	var s = {};
	s.ID = $(this).attr('data-id');
   	s.Status = $(this).attr('data-status');
console.log(s);
	$.ajax({ 
		url: 'update_data.php', 
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

	$('#ingMsg').html('<div class="alert alert-info alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Please wait...</strong></div>');
		$('#' + s.ID).html('<img src="/img/loading.gif"/>');
		$.ajax({ 
			url: 'update_data.php', 
			type: 'POST',
			data: {
				ingSupplier: 'getPrice',
				sLink: s.Link,
				size: s.Size,
				ingSupplierID: s.ID,
				ingID: '<?=$ingID?>'
				},
			dataType: 'html',
			success: function (data) {
				$('#ingMsg').html(data);
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
               className : "btn-primary",
               callback: function (){
	    			
				$.ajax({ 
					url: 'update_data.php', 
					type: 'GET',
					data: {
						ingSupplier: 'delete',
						sID: ing.ID,
						ingID: '<?=$ingID?>'
						},
					dataType: 'html',
					success: function (data) {
						reload_sup_data();
					}
				  });
				
                 return true;
               }
           },
           cancel: {
               label : "Cancel",
               className : "btn-default",
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

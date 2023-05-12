<?php

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
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
	<link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    
	<script src="/js/jquery/jquery.min.js"></script>
	<script src="/js/bootstrap.min.js"></script>
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
    
<?php } ?>

<h3>Suppliers</h3>
<hr>
<div id="supMsg"></div>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i> Actions</button>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addSupplier">Add new</a>
        </div>
    </div>                    
  </div>


</div>
<table id="tdIngSup" class="table table-striped table-bordered" style="width:100%">
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
          <th>Last updated</th>
          <th>Actions</th>
      </tr>
   </thead>
</table>
<script type="text/javascript" language="javascript" >
$(document).ready(function() {
	$("#supplier_name").change(function () {
    	vol = $(this).children(':selected').data('vol');
    	$("#supplier_size").focus().val(vol);    
	});
	$('.selectpicker').selectpicker();
	
	$('[data-toggle="tooltip"]').tooltip();
	var tdIngSup = $('#tdIngSup').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
		{ orderable: false, targets: [10] },

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
			  { data : 'price', title: 'Price(<?=$settings['currency']?>)', render: sPrice},
			  { data : 'size', title: 'Size', render: sSize},
			  { data : 'mUnit', title: 'Measurement Unit', render: mUnit},
			  { data : 'manufacturer', title: 'Manufacturer', render: sManufacturer},
			  { data : 'batch', title: 'Batch', render: sBatch},
			  { data : 'purchased', title: 'Purchased', render: sPurchased},
			  { data : 'stock', title: 'In Stock', render: sStock},
			  { data : 'updated', title: 'Last update', render: sUpdated},

			  { data : null, title: 'Actions', render: sActions},		   
			 ],
	order: [[ 1, 'asc' ]],
	lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
	pageLength: 20,
	displayLength: 20,		
	});
	
	
	$('#addSupplier').on('click', '[id*=sAdd]', function () {
	  $.ajax({ 
		url: 'update_data.php', 
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

			ingID: '<?=$ingID;?>'
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				$("#supplier_batch").val('');
				$("#supplier_link").val('');
				$("#supplier_size").val('');
				$("#supplier_price").val('');
				$("#supplier_manufacturer").val('');
				$("#mUnit").val('');
				reload_sup_data();
			}else{
				var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#supplier_inf').html(msg);
		}
	  });
	});
	
	
});//END DOC
 Object.getPrototypeOf($('#purchased')).size = function() { return this.length; }; // Workaround for https://github.com/Eonasdan/bootstrap-datetimepicker/issues/1714
function sName(data, type, row){
	return '<i class="ingSupplierID pv_point_gen" data-name="ingSupplierID" data-type="select" data-pk="'+row.id+'">'+row.supplierName+'</i>';    
}

function sLink(data, type, row){
	return '<i class="supplierLink pv_point_gen" data-name="supplierLink" data-type="textarea" data-pk="'+row.id+'">'+row.supplierLink+'</i>';    
}

function sPrice(data, type, row){
	return '<i id="'+row.ingSupplierID+'" class="price pv_point_gen" data-name="price" data-type="text" data-pk="'+row.id+'">'+row.price+'</i>';    
}

function sSize(data, type, row){
	return '<i class="size pv_point_gen" data-name="size" data-type="text" data-pk="'+row.id+'">'+row.size+'</i>';    
}

function mUnit(data, type, row){
	return '<i class="mUnit pv_point_gen" data-name="mUnit" data-type="select" data-pk="'+row.id+'">'+row.mUnit+'</i>';
}

function sManufacturer(data, type, row){
	return '<i class="manufacturer pv_point_gen" data-name="manufacturer" data-type="text" data-pk="'+row.id+'">'+row.manufacturer+'</i>';    
}

function sBatch(data, type, row){
	return '<i class="batch pv_point_gen" data-name="batch" data-type="text" data-pk="'+row.id+'">'+row.batch+'</i>';    
}

function sPurchased(data, type, row){
	return '<i class="purchased pv_point_gen" data-name="purchased" data-type="date" data-pk="'+row.id+'">'+row.purchased+'</i>';    
}

function sStock(data, type, row){
	return '<i class="stock pv_point_gen" data-name="stock" data-type="text" data-pk="'+row.id+'">'+row.stock+'</i>';    
}

function sUpdated(data, type, row){
	return row.updated;    
}

function sActions(data, type, row){
	if(row.preferred == 1){
		var pref = '<i class="fas fa-star pv_point_gen pv_point_gen_color"></i>&nbsp;';
	}else{
		var pref = '<i id="prefSID" data-status="1" data-id="'+row.ingSupplierID+'" class="far fa-star pv_point_gen" data-toggle="tooltip" data-placement="top" title="Set as preferred supplier."></i>&nbsp;';
	}
	return pref + '<i id="getPrice" data-name="'+row.supplierName+'" data-id="'+encodeURIComponent(row.ingSupplierID)+'" data-link="'+row.supplierLink+'" data-size="'+row.size+'" data-toggle="tooltip" data-placement="top" title="Get the latest price from the supplier." class="fas fa-sync pv_point_gen_color"></i>&nbsp;<a href="'+row.supplierLink+'" target="_blank" class="fas fa-store" data-toggle="tooltip" data-placement="top" title="Open supplier\'s web page."></a>&nbsp;<a href="#" id="sDel" class="fas fa-trash alert-danger" data-id="'+row.id+'" data-name="'+row.supplierName+'"></a>';    
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
	  selector: 'i.supplierLink',
	  type: 'POST',
	  url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	  title: 'Store link',
});
  
$('#tdIngSup').editable({
	  container: 'body',
	  selector: 'i.price',
	  type: 'POST',
	  url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	  title: 'Price',
});
	
$('#tdIngSup').editable({
  	container: 'body',
  	selector: 'i.size',
  	type: 'POST',
	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
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
	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
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
	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Manufacturer',
	success: function (data) {
			reload_sup_data();
	}
});

$('#tdIngSup').editable({
	container: 'body',
	selector: 'i.batch',
	type: 'POST',
	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Batch',
	success: function (data) {
			reload_sup_data();
	}
});

$('#tdIngSup').editable({
	container: 'body',
	selector: 'i.purchased',
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
	selector: 'i.stock',
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

	$('#supMsg').html('<div class="alert alert-info"><strong>Please wait, trying to fetch supplier data...</strong></div>');
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
			dataType: 'json',
			success: function (data) {
				if (data.success) {
		 	 		var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				}else{
					var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
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

<!-- ADD SUPPLIER-->
<div class="modal fade" id="addSupplier" tabindex="-1" role="dialog" aria-labelledby="addSupplier" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSupplier">Add supplier for <?php echo $ing['name']; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="supplier_inf"></div>
          <p>
            Name: 
            <select name="supplier_name" id="supplier_name" class="form-control selectpicker" data-live-search="true">
            <?php while ($row_ingSupplier = mysqli_fetch_array($res_ingSupplier)){ ?>
				<option value="<?=$row_ingSupplier['id']?>" data-vol="<?php if($ing['physical_state'] == '1'){ echo $row_ingSupplier['min_ml']; }elseif($ing['physical_state'] == '2'){ echo $row_ingSupplier['min_gr'];} ?>" ><?=$row_ingSupplier['name'];?></option>
			<?php	}	?>
            </select>
            </p>
            <p>
            URL*: 
            <input class="form-control" name="supplier_link" type="text" id="supplier_link" />
            </p>
            <p>            
            Price (<?php echo $settings['currency']; ?>):
            <input class="form-control" name="supplier_price" type="text" id="supplier_price" />
            </p>
            <p>
            Size (<?php if($ing['physical_state'] == '1'){ echo 'ml'; }elseif($ing['physical_state'] == '2'){ echo 'grams'; }else{ echo $settings['mUnit']; }?>)*:
            <input class="form-control" name="supplier_size" type="text" id="supplier_size" value="10" />
            </p>
            <p>
            Manufacturer:
            <input class="form-control" name="supplier_manufacturer" type="text" id="supplier_manufacturer" />
            </p>
            <p>
            Batch:
            <input class="form-control" name="supplier_batch" type="text" id="supplier_batch" />
            </p>
			<p>
            Purchased:
            <input class="form-control" name="purchased" type="date" id="purchased" />
            </p>
			<p>
            In stock:
            <input name="stock" type="text" class="form-control" id="stock" value="0" />
            </p>
            Measurement Unit:
            <select name="mUnit" id="mUnit" class="form-control">
			  <option value="ml">Milliliter</option>
			  <option value="g">Grams</option>
			  <option value="L">Liter</option>
			  <option value="fl. oz.">Fluid ounce (fl. oz.)</option>
            </select>
            </p>
            <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="sAdd" value="Add">
      </div>
    </div>
  </div>
</div>
</div>

<?php 
if (!defined('pvault_panel')){ die('Not Found');}

?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
     <div class="container-fluid">
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=suppliers">Suppliers</a></h2>
            </div>
             <div class="card-body">
              <div class="table-responsive">
            <div id="supmsg"></div>
            <table class="table table-striped table-bordered">
                <tr class="noBorder noexport">
                 <div class="text-right">
                  <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i> Actions</button>
                      <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-toggle="modal" data-backdrop="static" data-target="#addSupplier">Add new</a>
                        <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                      </div>
                    </div>        
                 </div>
            </tr>
            </table>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdIngSupData" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Platform</th>
                      <th>Price start tag</th>
                      <th>Price end tag</th>
                      <th>Additional costs</th>
                      <th>Price per</th>
                      <th>Min ml</th>
                      <th>Min grams</th>
                      <th>Description</th>
                      <th class="noexport">Actions</th>
                    </tr>
                  </thead>
                </table>
            <script type="text/javascript" language="javascript" >
$(document).ready(function() {
	
	$('[data-toggle="tooltip"]').tooltip();
	var tdIngSupData = $('#tdIngSupData').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [2,3,9]}
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: 'No suppliers added yet.',
			search: 'Search:'
			},
		ajax: {	url: '/core/list_suppliers_data.php' },
		columns: [
				  { data : 'name', title: 'Name', render: name },
				  { data : 'platform', title: 'Platform', render: platform},
				  { data : 'price_tag_start', title: 'Price start tag', render: price_tag_start},
				  { data : 'price_tag_end', title: 'Price end tag', render: price_tag_end},
				  { data : 'add_costs', title: 'Additional costs', render: add_costs},
				  { data : 'price_per_size', title: 'Price per size', render: price_per_size},
				  { data : 'min_ml', title: 'Min ml', render: min_ml},
				  { data : 'min_gr', title: 'Min grams', render: min_gr},
				  { data : 'description', title: 'Description', render: description},
	
				  { data : null, title: 'Actions', render: actions},		   
				 ],
		order: [[ 1, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
		pageLength: 20,
		displayLength: 20,
		
	});


function name(data, type, row){
	return '<i class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</i>';    
}

function platform(data, type, row){
	return '<i class="platform pv_point_gen" data-name="platform" data-type="select" data-pk="'+row.id+'">'+row.platform+'</i>';
}

function price_tag_start(data, type, row){
	return '<i class="price_tag_start pv_point_gen" data-name="price_tag_start" data-type="textarea" data-pk="'+row.id+'">'+atob(row.price_tag_start)+'</i>';    
}
function price_tag_end(data, type, row){
	return '<i class="price_tag_end pv_point_gen" data-name="price_tag_end" data-type="textarea" data-pk="'+row.id+'">'+atob(row.price_tag_end)+'</i>';    
}
function add_costs(data, type, row){
	return '<i class="add_costs pv_point_gen" data-name="add_costs" data-type="text" data-pk="'+row.id+'">'+row.add_costs+'</i>';    
}
function price_per_size(data, type, row){
	return '<i class="price_per_size pv_point_gen" data-name="price_per_size" data-type="select" data-pk="'+row.id+'">'+row.price_per_size+'</i>';    
}
function min_ml(data, type, row){
	return '<i class="min_ml pv_point_gen" data-name="min_ml" data-type="text" data-pk="'+row.id+'">'+row.min_ml+'</i>';    
}
function min_gr(data, type, row){
	return '<i class="min_gr pv_point_gen" data-name="min_gr" data-type="text" data-pk="'+row.id+'">'+row.min_gr+'</i>';
}
function description(data, type, row){
	return '<i class="notes pv_point_gen" data-name="notes" data-type="textarea" data-pk="'+row.id+'">'+row.notes+'</i>';    
}
function actions(data, type, row){
	return '<i class="pv_point_gen fas fa-edit mr2" rel="tip" title="Edit additional info" data-toggle="modal" id="edit_supplier" data-id="' + row.id + '" data-name="' + row.name + '" data-address="'+row.address+'" data-po="'+row.po+'" data-country="'+row.country+'" data-telephone="'+row.telephone+'" data-url="'+row.url+'" data-email="'+row.email+'"></i><i class="pv_point_gen fas fa-trash" style="color: #c9302c;" id="dDel" data-id="'+row.id+'" data-name="'+row.name+'"></a>';
}

$('#tdIngSupData').editable({
  container: 'body',
  selector: 'i.name',
  url: "/pages/update_data.php?settings=sup",
  title: 'Supplier',
  type: "POST",
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
});

$('#tdIngSupData').editable({
	container: 'body',
	selector: 'i.platform',
	type: 'POST',
  	url: "/pages/update_data.php?settings=sup",
    source: [
			 {value: "woocomerce", text: "Woocomerce"},
			 {value: "shopify", text: "Shopify"},
			 {value: "other", text: "Custom/Other"},
          ],
});

$('#tdIngSupData').editable({
	container: 'body',
	selector: 'i.price_per_size',
	type: 'POST',
  	url: "/pages/update_data.php?settings=sup",
    source: [
			 {value: "0", text: "Product"},
			 {value: "1", text: "Volume"},
          ],
});

$('#tdIngSupData').editable({
  container: 'body',
  selector: 'i.min_ml',
  url: "/pages/update_data.php?settings=sup",
  title: 'Minimum ml',
  type: "POST",
  validate: function(value){
	if($.trim(value) == ''){
		return 'This field cannot be empty, set 0 for none';
	}
   	if($.isNumeric(value) == '' ){
		return 'Numbers only!';
	}
  }
});

$('#tdIngSupData').editable({
  container: 'body',
  selector: 'i.min_gr',
  url: "/pages/update_data.php?settings=sup",
  title: 'Minimum grams',
  type: "POST",
  validate: function(value){
	if($.trim(value) == ''){
		return 'This field cannot be empty, set 0 for none';
	}
	if($.isNumeric(value) == '' ){
		return 'Numbers only!';
	}
  }
});

$('#tdIngSupData').editable({
  container: 'body',
  selector: 'i.price_tag_start',
  url: "/pages/update_data.php?settings=sup",
  title: 'Price tag start',
  type: "POST"
});

$('#tdIngSupData').editable({
  container: 'body',
  selector: 'i.price_tag_end',
  url: "/pages/update_data.php?settings=sup",
  title: 'Price tag end',
  type: "POST"
});

$('#tdIngSupData').editable({
  container: 'body',
  selector: 'i.add_costs',
  url: "/pages/update_data.php?settings=sup",
  title: 'Additional Costs',
  type: "POST",
  validate: function(value){
	  if($.trim(value) == ''){
		return 'This field cannot be empty, set 0 for none';
	  }
	  if($.isNumeric(value) == '' ){
		return 'Numbers only!';
	  }
  }
});

$('#tdIngSupData').editable({
  container: 'body',
  selector: 'i.notes',
  url: "/pages/update_data.php?settings=sup",
  title: 'Description',
  type: "POST",
});

	
$('#tdIngSupData').on('click', '[id*=dDel]', function () {
	var d = {};
	d.ID = $(this).attr('data-id');
    d.Name = $(this).attr('data-name');

	bootbox.dialog({
       title: "Confirm deletion",
       message : 'Delete supplier <strong>'+ d.Name +'</strong> ?',
       buttons :{
           main: {
               label : "Delete",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: '/pages/update_data.php', 
					type: 'GET',
					data: {
						supp: 'delete',
						ID: d.ID,
						},
					dataType: 'json',
					success: function (data) {
						if(data.success){
							msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
							reload_data();
						}else if(data.error){
							msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
						}
						$('#supmsg').html(msg);
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


$('#btnAddSupplier').on('click', function () {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			supp: 'add',
			name: $("#name").val(),
			address: $("#address").val(),
			po: $("#po").val(),
			country: $("#country").val(),
			telephone: $("#telephone").val(),
			url: $("#url").val(),
			email: $("#email").val(),
			platform: $("#platform").val(),
			price_tag_start: $("#price_tag_start").val(),
			price_tag_end: $("#price_tag_end").val(),
			add_costs: $("#add_costs").val(),
			description: $("#description").val(),
			min_ml: $("#min_ml").val(),
			min_gr: $("#min_gr").val()
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#inf').html(data);
				$("#name").val('');
				$("#description").val('');
				$("#platform").val('');
				$("#price_tag_start").val('');
				$("#price_tag_end").val('');
				$("#add_costs").val('');
				$("#min_ml").val('');
				$("#min_gr").val('');
				$("#address").val('');
				$("#po").val('');
				$("#country").val('');
				$("#telephone").val('');
				$("#url").val('');
				$("#email").val('');
				
				msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				reload_data();
			}else if(data.error){
				msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
				$('#inf').html(msg);
			}
  });
});

$('#btnEditSupplier').on('click', function () {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			supp: 'edit',
			id: $("#id").val(),
			name: $("#name").val(),
			address: $("#address").val(),
			po: $("#po").val(),
			country: $("#country").val(),
			telephone: $("#telephone").val(),
			url: $("#url").val(),
			email: $("#email").val(),
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){			
				msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				reload_data();
			}else if(data.error){
				msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
				$('#editSup').html(msg);
			}
  });
});

//Export
$('#csv').on('click',function(){
  $("#tdIngSupData").tableHTMLExport({
	type:'csv',
	filename:'suppliers.csv',
	separator: ',',
  	newline: '\r\n',
  	trimContent: true,
  	quoteFields: true,
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',	
	htmlContent: false,
  	// debug
  	consoleLog: false   
	});
});

function reload_data() {
    $('#tdIngSupData').DataTable().ajax.reload(null, true);
};

	$('#tdIngSupData').on('click', '[id*=edit_supplier]', function () {
        
        $(".modal-body div span").text("");
        $(".modal-title").text($(this).data().name);
        $("#address").val($(this).data().address);
		$("#po").val($(this).data().po);
		$("#country").val($(this).data().country);
        $("#telephone").val($(this).data().telephone);
        $("#url").val($(this).data().url);
        $("#email").val($(this).data().email);
        $("#id").val($(this).data().id);
        $("#name").val($(this).data().name);

        $("#details").modal("show");
	});
});
</script>

<!-- Edit additional info -->
<div class="modal fade" id="details" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
        </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <h4 class="modal-title">Supplier Details</h4>
    </div>
    <div class="modal-body">
        <div id="editSup"></div>
        <div class="container-fluid">
            <div class="row d-inline">
               <input type="hidden" name="id" id="id" />
               <input type="hidden" name="name" id="name" />
                Address:
                    <input class="form-control" name="address" type="text" id="address" />
                Postal Code:
                    <input class="form-control" name="po" type="text" id="po" />
                Country:
                    <input class="form-control" name="country" type="text" id="country" />
                Telephone:
                    <input class="form-control" name="telephone" type="text" id="telephone" />
                Website:
                    <input class="form-control" name="url" type="text" id="url" />
                Email:
                    <input class="form-control" name="email" type="text" id="email" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      <input type="submit" name="button" class="btn btn-primary" id="btnEditSupplier" value="Update">
    </div>
  </div>  
</div>
</div>


<!-- ADD NEW-->
<div class="modal fade" id="addSupplier" tabindex="-1" role="dialog" aria-labelledby="addSupplier" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSupplier">Add supplier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
          <div class="modal-body">
          	<div id="inf"></div>
            <div class="container-fluid">
                <div class="col-md-6">
                    <div class="row d-inline">
                        Name:
                            <input class="form-control" name="name" type="text" id="name" />
                        Address:
                            <input class="form-control" name="address" type="text" id="address" />
                        Postal Code:
                            <input class="form-control" name="po" type="text" id="po" />
                        Country:
                            <input class="form-control" name="country" type="text" id="country" />
                        Telephone:
                            <input class="form-control" name="telephone" type="text" id="telephone" />
                        Website:
                            <input class="form-control" name="website" type="text" id="website" />
                        Email:
                            <input class="form-control" name="email" type="text" id="email" />
                        Platform:
                          <select class="form-control" name="select" id="platform">
                            <option value="woocomerce">Woocomerce</option>
                            <option value="shopify">Shopify</option>
                            <option value="Other">Other/Custom</option>
                          </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row d-inline">
                        Price start tag:
                          <input class="form-control" type="text" name="price_tag_start" id="price_tag_start" />
                        Price end tag:
                          <input class="form-control" type="text" name="price_tag_end" id="price_tag_end" />
                        Additional costs:
                          <input class="form-control" type="text" name="add_costs" id="add_costs" />
                        Minimum ml quantity:
                          <input class="form-control" type="text" name="min_ml" id="min_ml" />
                        Minimum grams quantity:
                          <input class="form-control" type="text" name="min_gr" id="min_gr" />
                        Price to be calucalted per:
                          <select class="form-control" name="select" id="price_per_size">
                            <option value="0">Product</option>
                            <option value="1">Volume</option>
                          </select>
                        Description: 
                        <input class="form-control" name="description" type="text" id="description" />   
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="submit" name="button" class="btn btn-primary" id="btnAddSupplier" value="Add">
            </div>
        
    		</div>
  		</div>
	</div>
</div>

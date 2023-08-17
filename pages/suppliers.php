<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
     <div class="container-fluid">
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="/?do=suppliers">Suppliers</a></h2>
            </div>
             <div class="card-body">
              <div class="table-responsive">
            <div id="supmsg"></div>
            <table class="table table-striped table-bordered">
                <tr class="noBorder noexport">
                 <div class="text-right">
                  <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                      <div class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#addSupplier"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
                        <li><a class="dropdown-item" id="csv" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
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
                      <th>Materials</th>
                      <th>Platform</th>
                      <th>Price start tag</th>
                      <th>Price end tag</th>
                      <th>Additional costs</th>
                      <th>Price per</th>
                      <th>Min ml</th>
                      <th>Min grams</th>
                      <th>Description</th>
                      <th></th>
                    </tr>
                  </thead>
                </table>
<script type="text/javascript" language="javascript" >
$(document).ready(function() {
	
	$('[data-toggle="tooltip"]').tooltip();
	var tdIngSupData = $('#tdIngSupData').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [10]}
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
			emptyTable: 'No suppliers added yet.',
			search: 'Search:'
			},
		ajax: {	url: '/core/list_suppliers_data.php' },
		columns: [
				  { data : 'name', title: 'Name', render: name },
				  { data : 'materials', title: 'Materials' },
				  { data : 'platform', title: 'Platform', render: platform},
				  { data : 'price_tag_start', title: 'Price start tag', render: price_tag_start},
				  { data : 'price_tag_end', title: 'Price end tag', render: price_tag_end},
				  { data : 'add_costs', title: 'Additional costs', render: add_costs},
				  { data : 'price_per_size', title: 'Price per size', render: price_per_size},
				  { data : 'min_ml', title: 'Min ml', render: min_ml},
				  { data : 'min_gr', title: 'Min grams', render: min_gr},
				  { data : 'description', title: 'Description', render: description},
	
				  { data : null, title: '', render: actions},		   
				 ],
		order: [[ 1, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
		pageLength: 20,
		displayLength: 20,
		stateSave: true,
		stateDuration: -1,
		stateLoadCallback: function (settings, callback) {
			$.ajax( {
				url: '/core/update_user_settings.php?set=listSuppliers&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listSuppliers&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},
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
	data = '<div class="dropdown">' +
			'<button type="button" class="btn btn-primary btn-floating dropdown-toggle hidden-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu dropdown-menu-right">';
	data += '<li><i class="pv_point_gen dropdown-item text-dark" data-toggle="modal" id="edit_supplier" data-id="' + row.id + '" data-name="' + row.name + '" data-address="'+row.address+'" data-po="'+row.po+'" data-country="'+row.country+'" data-telephone="'+row.telephone+'" data-url="'+row.url+'" data-email="'+row.email+'"><i class="fas fa-edit mx-2"></i>Edit</i></li>';
	data += '<div class="dropdown-divider"></div>';
	data += '<li><a class="dropdown-item pv_point_gen text-danger" id="dDel" data-name="'+ row.name +'" data-id='+ row.id +'><i class="fas fa-trash mx-2"></i>Delete</a></li>';
	data += '</ul></div>';
	
	return data;
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
			name: $("#add_supplier #name").val(),
			address: $("#add_supplier #address").val(),
			po: $("#add_supplier #po").val(),
			country: $("#add_supplier #country").val(),
			telephone: $("#add_supplier #telephone").val(),
			url: $("#add_supplier #url").val(),
			email: $("#add_supplier #email").val(),
			platform: $("#add_supplier #platform").val(),
			price_tag_start: $("#add_supplier #price_tag_start").val(),
			price_tag_end: $("#add_supplier #price_tag_end").val(),
			add_costs: $("#add_supplier #add_costs").val(),
			description: $("#add_supplier #description").val(),
			min_ml: $("#add_supplier #min_ml").val(),
			min_gr: $("#add_supplier #min_gr").val()
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#inf').html(data);
				$("#add_supplier #name").val('');
				$("#add_supplier #description").val('');
				$("#add_supplier #platform").val('');
				$("#add_supplier #price_tag_start").val('');
				$("#add_supplier #price_tag_end").val('');
				$("#add_supplier #add_costs").val('');
				$("#add_supplier #min_ml").val('');
				$("#add_supplier #min_gr").val('');
				$("#add_supplier #address").val('');
				$("#add_supplier #po").val('');
				$("#add_supplier #country").val('');
				$("#add_supplier #telephone").val('');
				$("#add_supplier #url").val('');
				$("#add_supplier #email").val('');
				
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
<div class="modal fade" id="details" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="details" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
	    <h5 class="modal-title">Supplier Details</h5>
    </div>
    <div class="modal-body" id="edit_supplier">
        <div id="editSup"></div>
        <div class="container-fluid">
            <div class="col-sm-12">
               <input type="hidden" name="id" id="id" />
               <input type="hidden" name="name" id="name" />
               
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="address">Address</label>
                        <input class="form-control" name="address" type="text" id="address" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="address">Postal Code</label>
                        <input class="form-control" name="po" type="text" id="po" />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="country">Country</label>
                        <input class="form-control" name="country" type="text" id="country" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="telephone">Telephone</label>
                        <input class="form-control" name="telephone" type="text" id="telephone" />
                    </div>
                </div>                             
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="url">Website</label>
                        <input class="form-control" name="url" type="text" id="url" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <input class="form-control" name="email" type="text" id="email" />
                    </div>
                </div>
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
<div class="modal fade" id="addSupplier" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addSupplier" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSupplier">Add supplier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
          <div class="modal-body" id="add_supplier">
          	<div id="inf"></div>
            <div class="container-fluid">
                <div class="col-sm-12">
                    <div class="form-row">
    					<div class="form-group col-md-6">
                            <label for="name">Name</label>
                            <input class="form-control" name="name" type="text" id="name" />
                    	</div>
                        <div class="form-group col-md-6">
                            <label for="address">Address</label>
                                <input class="form-control" name="address" type="text" id="address" />
                        </div>
                    </div>
                    <div class="form-row">
    					<div class="form-group col-md-6">
                            <label for="po">Postal Code</label>
                            <input class="form-control" name="po" type="text" id="po" />
                    	</div>
                        <div class="form-group col-md-6">
                            <label for="country">Country</label>
                                <input class="form-control" name="country" type="text" id="country" />
                        </div>
                    </div>
                    <div class="form-row">
    					<div class="form-group col-md-6">
                            <label for="telephone">Telephone</label>
                            <input class="form-control" name="po" type="text" id="telephone" />
                    	</div>
                        <div class="form-group col-md-6">
                            <label for="website">Website</label>
                                <input class="form-control" name="website" type="text" id="website" />
                        </div>
                    </div>

                    <div class="form-row">
    					<div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input class="form-control" name="po" type="text" id="email" />
                    	</div>
                        <div class="form-group col-md-6">
                            <label for="platform">Platform</label>
                            <select class="form-control" name="select" id="platform">
                            	<option value="woocomerce">Woocomerce</option>
                            	<option value="shopify">Shopify</option>
                            	<option value="Other">Other/Custom</option>
                          	</select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-row">
    					<div class="form-group col-md-6">
                            <label for="price_tag_start">Price start tag</label>
                            <input class="form-control" name="price_tag_start" type="text" id="price_tag_start" />
                    	</div>
                        <div class="form-group col-md-6">
                            <label for="price_tag_end">Price end tag</label>
                            <input class="form-control" name="price_tag_end" type="text" id="price_tag_end" />
                        </div>
                    </div>
                    <div class="form-row">
    					<div class="form-group col-md-6">
                            <label for="add_costs">Additional costs</label>
                            <input class="form-control" name="add_costs" type="text" id="add_costs" />
                    	</div>
                        <div class="form-group col-md-6">
                            <label for="min_ml">Minimum ml quantity</label>
                            <input class="form-control" name="min_ml" type="text" id="min_ml" />
                        </div>
                    </div>
                    <div class="form-row">
    					<div class="form-group col-md-6">
                            <label for="min_gr">Minimum grams quantity</label>
                            <input class="form-control" name="min_gr" type="text" id="min_gr" />
                    	</div>
                        <div class="form-group col-md-6">
                            <label for="price_per_size">Price to be calucalted per</label>
                            <select class="form-control" name="select" id="price_per_size">
                            	<option value="0">Product</option>
                            	<option value="1">Volume</option>
                         	</select>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                     	<label for="description">Description</label>
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

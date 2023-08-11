<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:reload_data()">Customers</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
              <div id="innermsg"></div>
                <table class="table table-striped table-bordered">
                 <tr class="noBorder noexport">
                     <div class="text-right">
                      <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addCustomer"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
                            <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
                          </div>
                        </div>
                     </div>
                 </tr>
                </table>
                <table class="table table-bordered" id="tdDataCustomers" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Address</th>
                      <th>Email</th>
                      <th>Web Site</th>
                      <th></th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
 
<!-- ADD NEW-->
<div class="modal fade" id="addCustomer" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addCustomer" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add customer</h5>
      </div>
      <div class="modal-body">
      <div id="customer_inf"></div>
            Name: 
            <input class="form-control" name="name" type="text" id="name" />
            <p>
            Address: 
              <input class="form-control" name="address" type="text" id="addrss" />  
            <p>
            Email: 
              <input class="form-control" name="email" type="text" id="email" />           
            <p>
            Web Site: 
              <input class="form-control" name="web" type="text" id="web" /> 
              
              <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        	  <input type="submit" name="button" class="btn btn-primary" id="customer_add" value="Add">
      </div>
    </div>
  </div>
</div>
</div>

<!--EDIT CUSTOMER MODAL-->            
<div class="modal fade" id="editCustomer" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editCustomerLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editCustomerLabel">Edit customer</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {

	var tdDataCustomers = $('#tdDataCustomers').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [4] },
	],
	dom: 'lrftip',
	buttons: [{
				extend: 'csvHtml5',
				title: "Customers",
				exportOptions: {
     				columns: [0, 1, 2, 3]
  				},
			  }],
	processing: true,
	serverSide: true,
	searching: true,
	mark: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: 'Please Wait...',
		zeroRecords: 'Nothing found',
		search: 'Quick Search:',
		searchPlaceholder: 'Name..',
		},
	ajax: {	
		url: '/core/list_customer_data.php',
		type: 'POST',
		dataType: 'json',
		data: function(d) {
				if (d.order.length>0){
					d.order_by = d.columns[d.order[0].column].data
					d.order_as = d.order[0].dir
				}
			},
		},
	   columns: [
            { data : 'name', title: 'Name' },
			{ data : 'address', title: 'Address' },
			{ data : 'email', title: 'Email' },
			{ data : 'web', title: 'Web Site' },
			{ data : null, title: '', render: actions },

			],
	order: [[ 0, 'asc' ]],
	lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
	pageLength: 20,
	displayLength: 20,
	drawCallback: function( settings ) {
		extrasShow();
    	}
	});
	
}); //END DOC

function actions(data, type, row){
		data = '<div class="dropdown">' +
        '<button type="button" class="btn btn-primary btn-floating dropdown-toggle hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
            '<ul class="dropdown-menu dropdown-menu-right">';
		data += '<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editCustomer" rel="tip" title="Edit '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-edit mx-2"></i>Edit</a></li>';
		data += '<li><a href="'+ row.web +'" class="dropdown-item" target="_blank" rel="tip" title="Open '+ row.name +' page"><i class="fas fa-shopping-cart mx-2"></i>Go to customer</a></li>';
		data += '<div class="dropdown-divider"></div>';
		data += '<li><a class="dropdown-item" href="#" id="cDel" style="color: #c9302c;" rel="tip" title="Delete '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
		data += '</ul></div>';
	return data;
}

function reload_data() {
    $('#tdDataCustomers').DataTable().ajax.reload(null, true);
}

$('#tdDataCustomers').on('click', '[id*=cDel]', function () {
	var c = {};
	c.ID = $(this).attr('data-id');
	c.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm deletion",
       message : 'Permanently delete <strong>'+ c.Name +'</strong> and its data?',
       buttons :{
           main: {
               label : "Delete",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({
					url: '/pages/update_data.php', 
					type: 'POST',
					data: {
						action: "delete",
						type: "customer",
						customer_id: c.ID,
						},
					dataType: 'json',
					success: function (data) {
						if(data.success){
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
							reload_data();
						}else if(data.error){
							var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
						}
						$('#innermsg').html(msg);
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
  

$('#customer_add').on('click', function () {
	$.ajax({
        url: '/pages/update_data.php',
        type: 'POST',
		dataType: 'json',
		data: {
			customer: 'add',
			name: $("#name").val(),
			address: $("#address").val(),
			email: $("#email").val(),
			web: $("#website").val(),
		},
        success: function(response){
			if(response.success){
               $("#customer_inf").html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.success+'</div>');
				reload_data();
            }else{
                $("#customer_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.error+'</div>');
            }
          },
       });
	
});

function extrasShow() {
	$('[rel=tip]').tooltip({
        "html": true,
        "delay": {"show": 100, "hide": 0},
    });
};


$('#exportCSV').click(() => {
    $('#tdDataCustomers').DataTable().button(0).trigger();
});

$("#editCustomer").on("show.bs.modal", function(e) {
	const id = e.relatedTarget.dataset.id;
	const customer = e.relatedTarget.dataset.name;

	$.get("/pages/editCustomer.php?id=" + id)
		.then(data => {
		$("#editCustomerLabel", this).html(customer);
		$(".modal-body", this).html(data);
	});
});

</script>

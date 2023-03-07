<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>   
<div class="container-fluid">
  <div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:reload_data()">Cart</a></h2>
    </div>
    <div class="card-body">
      <div class="table-responsive">
      <div id="innermsg"></div>
        <table class="table table-bordered" id="tdDataCart" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Ingredient</th>
              <th>Purity (%)</th>
              <th>Quantity (ml)</th>
              <th>Actions</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<script>
$(document).ready(function() {

	var tdDataCart = $('#tdDataCart').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [3] },
	],
	dom: 'lrftip',
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
		url: '/core/cart_data.php',
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
            { data : 'name', title: 'Ingredient', render: name },
			{ data : 'purity', title: 'Purity (%)' },
			{ data : 'quantity', title: 'Quantity (ml)' },
			{ data : null, title: 'Actions', render: actions },

			],
	order: [[ 0, 'asc' ]],
	lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
	pageLength: 20,
	displayLength: 20,
	});
	
}); //END DOC

function reload_data() {
    $('#tdDataCart').DataTable().ajax.reload(null, true);
}

function name(data, type, row){
	if(row.supplier){
		data ='<div class="btn-group"><a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+row.name+'</a><div class="dropdown-menu dropdown-menu-right">';
		for (var key in row.supplier) {
			if (row.supplier.hasOwnProperty(key)) {
				data+='<a class="dropdown-item popup-link" href="'+row.supplier[key].link+'" target="_blank">'+row.supplier[key].name+'</a>';
			}
		}                
		data+='</div></div>';
	}else{
		data = 'N/A';
	}
	return data;
}

function actions(data, type, row){
	return '<i rel="tip" title="Remove '+ row.name +'" class="pv_point_gen fas fa-trash" style="color: #c9302c;" id="cart_remove" data-name="'+ row.name +'" data-id='+ row.id +'></i>';    
}

function reload_cart_data() {
    $('#tdDataCart').DataTable().ajax.reload(null, true);
}


$('#tdDataCart').on('click', '[id*=cart_remove]', function () {
	var ing = {};
	ing.ID = $(this).attr('data-id');
	ing.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm removal",
       message : 'Remove <strong>'+ ing.Name +'</strong> from shopping cart?',
       buttons :{
           main: {
               label : "Remove",
               className : "btn-danger",
               callback: function (){
			   $.ajax({
					url: '/pages/manageFormula.php', 
					type: 'POST',
					data: {
						action: "removeFromCart",
						materialId: ing.ID,
						materialName: ing.Name
						},
					dataType: 'json',
					success: function (data) {
						if(data.success) {
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
								reload_cart_data();
							} else {
								var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				
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
</script>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>   
<div class="container-fluid">
  <div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle">Cart</a></h2>
    </div>
    <div class="card-body">
      <div class="table-responsive">
      <div id="innermsg"></div>
      <div class="mt-4 mr-4 text-right">
      	<div class="btn-group" id="menu">
        	<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
            <div class="dropdown-menu dropdown-menu-left">
               <li><a href="#" class="dropdown-item" id="export_csv"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
           </div>
       	</div>
       </div>
        <table class="table table-striped" id="tdDataCart" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Ingredient</th>
              <th>Purity (%)</th>
              <th>Quantity (ml)</th>
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
		buttons: [{
        	extend: "csv",
			filename: "Cart ingredients",
            exportOptions: {
            	columns: [0, 1, 2],
				stripHtml: true,
				orthogonal: 'export'
           	}
		}],
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
			{ data : 'quantity', title: 'Quantity (<?=$settings['mUnit']?>)' },
			{ data : null, title: '', render: actions },

		],
		order: [[ 0, 'asc' ]],
		lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
		pageLength: 20,
		displayLength: 20
	});
	
	$('#mainTitle').click(function() {
	 	reload_cart_data();
  	});


	function reload_data() {
		$('#tdDataCart').DataTable().ajax.reload(null, true);
	}
	
	function name(data, type, row){
		if(row.supplier){
			data ='<div class="btn-group"><a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+row.name+'</a><div class="dropdown-menu dropdown-menu-right">';
			for (var key in row.supplier) {
				if (row.supplier.hasOwnProperty(key)) {
					data+='<a class="dropdown-item" href="'+row.supplier[key].link+'" target="_blank">'+row.supplier[key].name+'</a>';
				}
			}                
			data+='</div></div>';
		}else{
			data = 'N/A';
		}
		if (type === 'export') {
              data = row.name;
        }
		return data;
	}
	
	function actions(data, type, row){
		return '<i rel="tip" title="Remove '+ row.name +'" class="pv_point_gen fas fa-trash link-danger" id="cart_remove" data-name="'+ row.name +'" data-id='+ row.id +'></i>';    
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
								$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
								$('.toast-header').removeClass().addClass('toast-header alert-success');
								reload_cart_data();
							}else if(data.error){
								$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
								$('.toast-header').removeClass().addClass('toast-header alert-danger');
							}
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

	$('#export_csv').click(() => {
		$('#tdDataCart').DataTable().button(0).trigger();
	});

}); //END DOC
</script>
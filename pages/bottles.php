<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
<?php
$sup = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY name ASC");
while ($suppliers = mysqli_fetch_array($sup)){
	    $supplier[] = $suppliers;
}
?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:reload_data()">Bottles</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
              <div id="innermsg"></div>
               <table class="table table-striped table-bordered">
                 <tr class="noBorder noexport">
                     <div class="text-right">
                      <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                          <div class="dropdown-menu dropdown-menu-right">
            				<li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#addBottle"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
                            <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
                          </div>
                        </div>        
                     </div>
                 </tr>
                </table>
                <table class="table table-bordered" id="tdDataBottles" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Size (ml)</th>
                      <th>Price</th>
                      <th>Supplier</th>
                      <th>Pieces</th>
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

<!-- ADD BOTTLE MODAL-->
<div class="modal fade" id="addBottle" tabindex="-1" role="dialog" aria-labelledby="addBottle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Bottle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="bottle_inf"></div>
        <p>
        Name: 
          <input class="form-control" name="name" type="text" id="name" />
        </p>
        <p>            
        Size (ml):
          <input class="form-control" name="size" type="text" id="size" />
        </p>
        <p>
        Price:
          <input class="form-control" name="price" type="text" id="price" />
        </p>
 		<p>
        Height:
          <input class="form-control" name="height" type="text" id="height" />
        </p>
        <p>
        Width:
          <input class="form-control" name="width" type="text" id="width" />
        </p>
        <p>
        Diameter:
          <input class="form-control" name="diameter" type="text" id="diameter" />
        </p>
        <p>Stock (pieces): 
          <input class="form-control" name="pieces" type="text" id="pieces" />
        </p>
        <p>
        Notes:
          <input class="form-control" name="notes" type="text" id="notes" />
        </p>
        <p>
        Supplier:
          <select name="supplier" id="supplier" class="form-control">
            <option value="" selected></option>
            <?php
            foreach($supplier as $sup) {
                echo '<option value="'.$sup['name'].'">'.$sup['name'].'</option>';
            }
            ?>
          </select>
        </p>
        <p>
        Supplier URL:
          <input class="form-control" name="supplier_link" type="text" id="supplier_link" />
        </p>
        <p>
        Image:
        <input type="file" name="pic" id="pic" class="form-control" />
    	</p>            
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="bottle_add" value="Add">
      </div>
    </div>
  </div>
</div>

<!--EDIT BOTTLE MODAL-->            
<div class="modal fade" id="editBottle" tabindex="-1" role="dialog" aria-labelledby="editBottleLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editBottleLabel">Edit bottle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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

	var tdDataBottles = $('#tdDataBottles').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [5] },
	],
	dom: 'lrftip',
	buttons: [{
				extend: 'csvHtml5',
				title: "Bottle Inventory",
				exportOptions: {
     				columns: [0, 1, 2, 3, 4]
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
		url: '/core/list_bottle_data.php',
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
            { data : 'name', title: 'Name', render: name },
			{ data : 'ml', title: 'Size (ml)' },
			{ data : 'price', title: 'Price (<?php echo $settings['currency'];?>)' },
			{ data : 'supplier', title: 'Supplier' },
			{ data : 'pieces', title: 'Pieces in stock' },
			{ data : null, title: '', render: actions }
			],
	order: [[ 0, 'asc' ]],
	lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
	pageLength: 20,
	displayLength: 20,
	drawCallback: function( settings ) {
		extrasShow();
    	}
	});
	
	var detailRows = [];
 
    $('#tdDataBottles tbody').on( 'click', 'tr td:first-child', function () {
        var tr = $(this).parents('tr');
        var row = tdDataBottles.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
 
        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();
            detailRows.splice( idx, 1 );
        } else {
            tr.addClass( 'details' );
            row.child( format( row.data() ) ).show();
            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
        }
    });
 
    tdDataBottles.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' td:first-child + td').trigger( 'click' );
        });
    });
}); //END DOC


function format ( d ) {
    details = '<img src="'+d.photo+'" class="img_ifra"/><br><hr/>'+
	'<strong>Height:</strong><br><span class="details">'+d.height+
	'mm</span><br><strong>Width:</strong><br><span class="details">'+d.width+
	'mm</span><br><strong>Diameter:</strong><br><span class="details">'+d.diameter+
	'mm</span><br><strong>Notes:</strong><br><span class="details">'+d.notes;

	return details;
}

function name(data, type, row){
	return '<i class="pv_point_gen pv_gen_li">'+row.name+'</i>';
}

function actions(data, type, row){	
		data = '<div class="dropdown">' +
        '<button type="button" class="btn btn-primary btn-floating dropdown-toggle hidden-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
            '<ul class="dropdown-menu dropdown-menu-right">';
		data += '<li><a href="#" class="dropdown-item" data-toggle="modal" data-backdrop="static" data-target="#editBottle" rel="tip" title="Edit '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-edit mx-2"></i>Edit</a></li>';
		data += '<li><a href="'+ row.supplier_link +'" class="dropdown-item" target="_blank" rel="tip" title="Open '+ row.supplier +' page"><i class="fas fa-shopping-cart mx-2"></i>Go to supplier</a></li>';
		data += '<div class="dropdown-divider"></div>';
		data += '<li><a class="dropdown-item" href="#" id="btlDel" style="color: #c9302c;" rel="tip" title="Delete '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
		data += '</ul></div>';
	return data;
}

function reload_data() {
    $('#tdDataBottles').DataTable().ajax.reload(null, true);
}

$('#tdDataBottles').on('click', '[id*=btlDel]', function () {
	var btl = {};
	btl.ID = $(this).attr('data-id');
	btl.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm deletion",
       message : 'Permanently delete <strong>'+ btl.Name +'</strong> and its data?',
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
						type: "bottle",
						btlId: btl.ID,
						},
					dataType: 'json',
					success: function (data) {
						if(data.success){
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
							reload_data();
						}else if(data.error){
							var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
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
  

$('#bottle_add').on('click', function () {

	$("#bottle_inf").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	$("#bottle_add").prop("disabled", true);
    $("#bottle_add").prop('value', 'Please wait...');
		
	var fd = new FormData();
    var files = $('#pic')[0].files;
    var name = $('#name').val();
    var size = $('#size').val();
    var price = $('#price').val();
    var supplier = $('#supplier').val();
    var supplier_link = $('#supplier_link').val();

    var height = $('#height').val();
    var width = $('#width').val();
    var diameter = $('#diameter').val();
    var notes = $('#notes').val();
    var pieces = $('#pieces').val();

    if(files.length > 0 ){
		fd.append('pic_file',files[0]);

			$.ajax({
              url: '/pages/upload.php?type=bottle&name=' + btoa(name) + '&size=' + size + '&price=' + price + '&supplier=' + btoa(supplier) + '&supplier_link=' + btoa(supplier_link)+ '&height=' + height + '&width=' + width + '&diameter=' + diameter + '&notes=' + btoa(notes) + '&pieces=' + pieces,
              type: 'POST',
              data: fd,
              contentType: false,
              processData: false,
			  		cache: false,
			  dataType: 'json',
              success: function(response){
                 if(response.success){
                    $("#bottle_inf").html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+response.success+'</div>');
					$("#bottle_add").prop("disabled", false);
        			$("#bottle_add").prop("value", "Add");
					reload_data();
                 }else{
                    $("#bottle_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+response.error+'</div>');
					$("#bottle_add").prop("disabled", false);
        			$("#bottle_add").prop("value", 'Add');
                 }
              },
           });
        }else{
			$("#bottle_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a image to upload!</div>');
			$("#bottle_add").prop("disabled", false);
   			$("#bottle_add").prop("value", "Add");
        }
		
});

function extrasShow() {
	$('[rel=tip]').tooltip({
        "html": true,
        "delay": {"show": 100, "hide": 0},
     });
};


$('#exportCSV').click(() => {
    $('#tdDataBottles').DataTable().button(0).trigger();
});

$("#editBottle").on("show.bs.modal", function(e) {
	const id = e.relatedTarget.dataset.id;
	const bottle = e.relatedTarget.dataset.name;

	$.get("/pages/editBottle.php?id=" + id)
		.then(data => {
		$("#editBottleLabel", this).html(bottle);
		$(".modal-body", this).html(data);
	});
});
</script>


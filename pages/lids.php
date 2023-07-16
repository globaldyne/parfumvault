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
              <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:reload_data()">Lids</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
              <div id="innermsg"></div>
               <table class="table table-striped table-bordered">
                 <tr class="noBorder noexport">
                     <div class="text-right">
                      <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#addLid"><i class="fa-solid fa-plus mr2"></i>Add new</a></li>
                            <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mr2"></i>Export to CSV</a></li>
                          </div>
                        </div>        
                     </div>
                 </tr>
                </table>
                <table class="table table-bordered" id="tdDataLids" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Style</th>
                      <th>Colour</th>
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
    
<!-- ADD LID MODAL-->
<div class="modal fade" id="addLid" tabindex="-1" role="dialog" aria-labelledby="addLid" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Lid</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="lid_inf"></div>
        <p>
        Style: 
          <input class="form-control" name="style" type="text" id="style" />
        </p>
        <p>            
        Colour:
          <input class="form-control" name="colour" type="text" id="colour" />
        </p>
        <p>
        Price:
          <input class="form-control" name="price" type="text" id="price" />
        </p>
        <p>
        Pieces in stock:
          <input class="form-control" name="pieces" type="text" id="pieces" />
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
        <input type="submit" name="button" class="btn btn-primary" id="lid_add" value="Add">
      </div>
    </div>
  </div>
</div>


<!--EDIT LID MODAL-->            
<div class="modal fade" id="editLid" tabindex="-1" role="dialog" aria-labelledby="editLidLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editLidLabel">Edit lid</h5>
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

	var tdDataLids = $('#tdDataLids').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [5] },
	],
	dom: 'lrftip',
	buttons: [{
				extend: 'csvHtml5',
				title: "Lid Inventory",
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
		url: '/core/list_lid_data.php',
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
            { data : 'style', title: 'Style', render: style },
			{ data : 'colour', title: 'Colour' },
			{ data : 'price', title: 'Price (<?php echo $settings['currency'];?>)' },
			{ data : 'supplier', title: 'Supplier' },
			{ data : 'pieces', title: 'Pieces in stock' },
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
	
	var detailRows = [];
 
    $('#tdDataLids tbody').on( 'click', 'tr td:first-child', function () {
        var tr = $(this).parents('tr');
        var row = tdDataLids.row( tr );
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
 
    tdDataLids.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' td:first-child + td').trigger( 'click' );
        });
    });
	
}); //END DOC


function format ( d ) {
    details = '<img src="'+d.photo+'" class="img_ifra"/>';
	return details;
}

function style(data, type, row){
	return '<i class="pv_point_gen pv_gen_li">'+row.style+'</i>';
}

function actions(data, type, row){	
		data = '<div class="dropdown">' +
        '<button type="button" class="btn btn-primary btn-floating dropdown-toggle hidden-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
            '<ul class="dropdown-menu dropdown-menu-right">';
		data += '<li><a href="#" class="dropdown-item" data-toggle="modal" data-backdrop="static" data-target="#editLid" rel="tip" title="Edit '+ row.style +'" data-id='+ row.id +' data-name="'+ row.style +'"><i class="fas fa-edit mr2"></i>Edit</a></li>';
		data += '<li><a href="'+ row.supplier_link +'" target="_blank" rel="tip" title="Open '+ row.style +' page"><i class="fas fa-shopping-cart mr2"></i>Go to supplier</a></li>';
		data += '<div class="dropdown-divider"></div>';
		data += '<li><a class="dropdown-item" href="#" id="ldlDel" style="color: #c9302c;" rel="tip" title="Delete '+ row.name +'" data-id='+ row.id +' data-name="'+ row.style +'"><i class="fas fa-trash mr2"></i>Delete</a></li>';
		data += '</ul></div>';
	return data;
}

function reload_data() {
    $('#tdDataLids').DataTable().ajax.reload(null, true);
}

$('#tdDataLids').on('click', '[id*=ldlDel]', function () {
	var ldl = {};
	ldl.ID = $(this).attr('data-id');
	ldl.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm deletion",
       message : 'Permanently delete <strong>'+ ldl.Name +'</strong> and its data?',
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
						type: "lid",
						lidId: ldl.ID,
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
  

$('#lid_add').on('click', function () {

	$("#lid_inf").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	$("#lid_add").prop("disabled", true);
    $("#lid_add").prop('value', 'Please wait...');
		
	var fd = new FormData();
    var files = $('#pic')[0].files;
    var style = $('#style').val();
    var price = $('#price').val();
    var supplier = $('#supplier').val();
    var supplier_link = $('#supplier_link').val();
    var pieces = $('#pieces').val();
    var colour = $('#colour').val();

    if(files.length > 0 ){
		fd.append('pic_file',files[0]);

			$.ajax({
              url: '/pages/upload.php?type=lid&style=' + btoa(style) + '&price=' + price + '&supplier=' + btoa(supplier) + '&supplier_link=' + btoa(supplier_link) + '&pieces=' + pieces + '&colour=' + colour,
              type: 'POST',
              data: fd,
              contentType: false,
              processData: false,
			  		cache: false,
			  dataType: 'json',
              success: function(response){
                 if(response.success){
                    $("#lid_inf").html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+response.success+'</div>');
					$("#lid_add").prop("disabled", false);
        			$("#lid_add").prop("value", "Add");
					reload_data();
                 }else{
                    $("#lid_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+response.error+'</div>');
					$("#lid_add").prop("disabled", false);
        			$("#lid_add").prop("value", 'Add');
                 }
              },
           });
        }else{
			$("#lid_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a image to upload!</div>');
			$("#lid_add").prop("disabled", false);
   			$("#lid_add").prop("value", "Add");
        }
		
});

function extrasShow() {
	$('[rel=tip]').tooltip({
        "html": true,
        "delay": {"show": 100, "hide": 0},
     });
};


$('#exportCSV').click(() => {
    $('#tdDataLids').DataTable().button(0).trigger();
});

$("#editLid").on("show.bs.modal", function(e) {
	const id = e.relatedTarget.dataset.id;
	const lid = e.relatedTarget.dataset.style;

	$.get("/pages/editLid.php?id=" + id)
		.then(data => {
		$("#editLidLabel", this).html(lid);
		$(".modal-body", this).html(data);
	});
});
</script>

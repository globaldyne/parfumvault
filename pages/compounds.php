<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php');

$q = mysqli_query($conn, "SELECT id,name FROM documents WHERE type = '5' AND isBatch = '1'");
while($res = mysqli_fetch_array($q)){
    $data[] = $res;
}


?>
<div class="container-fluid">
  <div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:reload_data()">Compounds</a></h2>
    </div>
    <div class="card-body">
      <div class="table-responsive">
      <div id="innermsg"></div>
       <table class="table table-striped table-bordered">
         <tr class="noBorder">
             <div class="form-inline mb-3">
                <input type="text" class="form-control mr-2" id="btlSize" placeholder="Enter bottle size, eg: 100">
                <button type="button" class="btn btn-primary" id="submitBottleAmount">Submit</button>
             </div>
             <div class="text-right">
              <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addCompound"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
                    <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
                  </div>
                </div>        
             </div>
         </tr>
        </table>

        <table class="table table-bordered" id="tdDataCompounds" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Description</th>
              <th>Batch</th>
              <th>Size(<?php echo $settings['mUnit']; ?>)</th>
              <th>Label</th>
              <th>Location</th>
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
    
<!-- ADD NEW MODAL-->
<div class="modal fade" id="addCompound" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addCompound" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add compound</h5>
      </div>
      <div class="modal-body">
      <div id="compound_inf"></div>
          <div class="row">
              <div class="mb-3">
                <label for="cmp_name" class="form-label">Compound name</label>
                <input name="cmp_name" type="cmp_name" class="form-control" id="cmp_name">
              </div>
              <div class="mb-3">
                <label for="cmp_batch" class="form-label">Batch</label>
                <select name="cmp_batch" id="cmp_batch" class="form-control selectpicker" data-live-search="true" >
            		<option value="" selected></option>
            		<?php
						foreach($data as $b) {
							echo '<option value="'.$b['id'].'">'.$b['name'].'</option>';
						}
					?>
				</select>
              </div>
              <div class="mb-3">
                <label for="cmp_size" class="form-label">Bottle size (<?php echo $settings['mUnit']; ?>)</label>
                <input name="cmp_size" type="text" class="form-control" id="cmp_size">
              </div>
              <div class="mb-3">
                <label for="cmp_location" class="form-label">Location</label>
                <input name="cmp_location" type="text" class="form-control" id="cmp_location">
              </div>
              <div class="mb-3">
                <label for="cmp_desc" class="form-label">Short Description</label>
                <input name="cmp_desc" type="text" class="form-control" id="cmp_desc">
              </div>
             <div class="col-sm">
               <label for="cmp_label_info" class="form-label">Label info</label>
               <textarea class="form-control" name="cmp_label_info" id="cmp_label_info" rows="5"></textarea>
             </div>
        </div>
      <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="compound_add" value="Add compound">
      </div>
    </div>
  </div>
</div>


<!--EDIT MODAL-->            
<div class="modal fade" id="editCompound" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editCompound" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editCompoundLabel">Edit compound</h5>
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
 $('#submitBottleAmount').click(function() {
         reload_data();
  });
	
	var tdDataCompounds = $('#tdDataCompounds').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [6] },
	],
	dom: 'lrftip',
	buttons: [{
				extend: 'csvHtml5',
				title: "Compounds Inventory",
				exportOptions: {
     				columns: [0, 1, 2, 3, 4, 5]
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
		url: '/core/list_inventory_compounds_data.php',
		type: 'POST',
		dataType: 'json',
				data: function(d) {
				var btlSize = $('#btlSize').val();

				if (d.order.length>0){
					d.order_by = d.columns[d.order[0].column].data
					d.order_as = d.order[0].dir
				}
				d.btlSize = btlSize;
			},
		},
	   columns: [
            { data : 'name', title: 'Name', render: name },
            { data : 'description', title: 'Description' },
			{ data : 'batch_id', title: 'Batch', render: docData },
			{ data : 'size', title: 'Size (<?php echo $settings['mUnit'];?>)' },
			{ data : 'label_info', title: 'Label' },
			{ data : 'location', title: 'Location' },
			{ data : null, title: '', render: actions },

			],
	order: [[ 0, 'asc' ]],
	lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
	pageLength: 20,
	displayLength: 20,
	stateSave: true,
	stateDuration: -1,
	stateLoadCallback: function (settings, callback) {
       	$.ajax( {
           	url: '/core/update_user_settings.php?set=listInvComp&action=load',
           	dataType: 'json',
           	success: function (json) {
               	callback( json );
           	}
       	});
    },
    stateSaveCallback: function (settings, data) {
	   $.ajax({
		 url: "/core/update_user_settings.php?set=listInvComp&action=save",
		 data: data,
		 dataType: "json",
		 type: "POST"
	  });
	},
	drawCallback: function( settings ) {
		extrasShow();
    },

	});
	
	
	tdDataCompounds.on('requestChild.dt', function (e, row) {
		row.child(format(row.data())).show();
	});
	 
	tdDataCompounds.on('click', '#compound_name', function (e) {
		let tr = e.target.closest('tr');
		let row = tdDataCompounds.row(tr); 
		if (row.child.isShown()) {
			row.child.hide();
		} else {
			row.child(format(row.data())).show();
		}
	});
	
}); //END DOC

function format(d) {
    var details = '<strong>Bottle breakdown size: ' + d.btlSize + '<?php echo $settings['mUnit'];?></strong><br><hr/>';
    $.each(d.breakDown, function(i, breakdownItem) {
        details += '<span class="details"><strong>' + breakdownItem.name + '(' + breakdownItem.concentration + '%)</strong> ' + breakdownItem.bottles_total + ' Bottles</span><br>';
    });
    return details;
}


function name(data, type, row){
	return '<i class="pv_point_gen pv_gen_li" id="compound_name">'+row.name+'</i>';
}

function docData(data, type, row){
	return '<a href="/pages/viewDoc.php?id='+row.id+'" target="_blank" class="fa fa-file-alt"></a>';    
};

function actions(data, type, row){	
		data = '<div class="dropdown">' +
        '<button type="button" class="btn btn-primary btn-floating dropdown-toggle hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
            '<ul class="dropdown-menu dropdown-menu-right">';
		data += '<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editCompound" rel="tip" title="Edit '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-edit mx-2"></i>Edit</a></li>';
		data += '<div class="dropdown-divider"></div>';
		data += '<li><a class="dropdown-item" href="#" id="cmpDel" style="color: #c9302c;" rel="tip" title="Delete '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
		data += '</ul></div>';
	return data;
};

function reload_data() {
    $('#tdDataCompounds').DataTable().ajax.reload(null, true);
};

$('#tdDataCompounds').on('click', '[id*=cmpDel]', function () {
	var cmp = {};
	cmp.ID = $(this).attr('data-id');
	cmp.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm deletion",
       message : 'Permanently delete <strong>'+ cmp.Name +'</strong> and its data?',
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
						type: "invCmp",
						compoundId: cmp.ID,
						},
					dataType: 'json',
					success: function (data) {
						if(data.success){
							$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
							$('.toast-header').removeClass().addClass('toast-header alert-success');
							reload_data();
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
  

$('#compound_add').on('click', function () {

	$("#compound_add").prop("disabled", true);
    $("#compound_add").prop('value', 'Please wait...');

	$.ajax({
	  url: '/pages/update_data.php', 
	  type: 'POST',
	  data: {
			action: "add",
			type: "invCmp",
			cmp_name: $('#cmp_name').val(),
			cmp_batch: $('#cmp_batch').val(),
			cmp_size: $('#cmp_size').val(),
			cmp_location: $('#cmp_location').val(),
			cmp_desc: $('#cmp_desc').val(),
			cmp_label_info: $('#cmp_label_info').val()
	 },
	  dataType: 'json',
	  success: function(response){
		 if(response.success){
			$("#compound_inf").html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.success+'</div>');
			$("#compound_add").prop("disabled", false);
			$("#compound_add").prop("value", "Add");
			reload_data();
		 }else{
			$("#compound_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.error+'</div>');
			$("#compound_add").prop("disabled", false);
			$("#compound_add").prop("value", 'Add');
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
    $('#tdDataCompounds').DataTable().button(0).trigger();
});

$("#editCompound").on("show.bs.modal", function(e) {
	const id = e.relatedTarget.dataset.id;
	const compound = e.relatedTarget.dataset.name;

	$.get("/pages/views/inventory/editCompound.php?id=" + id)
		.then(data => {
		$("#editCompoundLabel", this).html(compound);
		$(".modal-body", this).html(data);
	});
});


</script>

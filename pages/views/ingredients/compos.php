<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$ingID = mysqli_real_escape_string($conn, $_GET["id"]);
$ingName = mysqli_real_escape_string($conn, $_GET["name"]);

?>

<h3>Composition</h3>
<hr>
<div class="card-body">
 	<div class="text-right">
  		<div class="btn-group">
   			<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
    		<div class="dropdown-menu dropdown-menu-right">
        		<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addComposition"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
        		<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addCSV"><i class="fa-solid fa-file-import mx-2"></i>Upload CSV</a></li>
    		</div>
  		</div>                    
	</div>
</div>

<table id="tdCompositions" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>CAS</th>
          <th>EINECS</th>
          <th>Percentage</th>
          <th>GHS Classification</th>
          <th>Declare</th>
          <th></th>
      </tr>
   </thead>
</table>


<script type="text/javascript" language="javascript" >
$(document).ready(function() {
	
$('[data-bs-toggle="tooltip"]').tooltip();
var tdCompositions = $('#tdCompositions').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
		{ orderable: false, targets: [4,6] },
	],
	dom: 'lfrtip',
	processing: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
		emptyTable: 'No compositions added yet.',
		search: 'Search:'
		},
	ajax: {	url: '/core/list_ing_compos_data.php?id=<?=$ingName?>' },
	columns: [
			  { data : 'name', title: 'Name', render: cmpName },
			  { data : 'cas', title: 'CAS', render: cmpCAS},
			  { data : 'ec', title: 'EINECS', render: cmpEC},
			  { data : 'percentage', title: 'Percentage', render: cmpPerc},
			  { data : 'GHS', title: 'GHS Classification', render: cmpGHS},
			  { data : 'toDeclare', title: 'Declare', render: cmpDeclare},
			  { data : null, title: '', render: cmpActions},		   
			 ],
	order: [[ 1, 'asc' ]],
	lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
	pageLength: 20,
	displayLength: 20,		
	});
});

function cmpName(data, type, row){
	return '<i class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</i>';    
}

function cmpCAS(data, type, row){
	return '<i class="cas pv_point_gen" data-name="cas" data-type="text" data-pk="'+row.id+'">'+row.cas+'</i>';    
}

function cmpEC(data, type, row){
	return '<i class="ec pv_point_gen" data-name="ec" data-type="text" data-pk="'+row.id+'">'+row.ec+'</i>';    
}

function cmpPerc(data, type, row){
	return '<i class="percentage pv_point_gen" data-name="percentage" data-type="text" data-pk="'+row.id+'">'+row.percentage+'</i>';    
}

function cmpGHS(data, type, row){
	return '<i class="GHS pv_point_gen" data-name="GHS" data-type="text" data-pk="'+row.id+'">'+row.GHS+'</i>';    
}

function cmpDeclare(data, type, row){
	if(row.toDeclare == 0){
		var declare = 'No';
	}else if(row.toDeclare == 1){
		var declare = 'Yes';
	}
	return '<i class="toDeclare pv_point_gen" data-name="toDeclare" data-type="select" data-pk="'+row.id+'">'+declare+'</i>';    
}

function cmpActions(data, type, row){
	return '<a href="#" id="cmpDel" class="fas fa-trash link-danger" data-id="'+row.id+'" data-name="'+row.name+'"></a>';
}

$('#tdCompositions').editable({
	container: 'body',
    selector: 'i.name',
    type: 'POST',
    url: "update_data.php?composition=update&ing=<?=$ingName;?>",
    title: 'Name'
});

$('#tdCompositions').editable({
   container: 'body',
   selector: 'i.cas',
   type: 'POST',
   url: "update_data.php?composition=update&ing=<?=$ingName;?>",
   title: 'CAS'
});

$('#tdCompositions').editable({
   container: 'body',
   selector: 'i.ec',
   type: 'POST',
   url: "update_data.php?composition=update&ing=<?=$ingName;?>",
   title: 'EINECS',
});

$('#tdCompositions').editable({
    container: 'body',
    selector: 'i.percentage',
    type: 'POST',
    url: "update_data.php?composition=update&ing=<?=$ingName;?>",
    title: 'Percentage'
});

$('#tdCompositions').editable({
    container: 'body',
    selector: 'i.GHS',
    type: 'POST',
    url: "update_data.php?composition=update&ing=<?=$ingName;?>",
    title: 'GHS'
});

$('#tdCompositions').editable({
	pvnoresp: false,
	highlight: false,
	emptytext: "",
	emptyclass: "",
  	container: 'body',
  	selector: 'i.toDeclare',
  	type: 'POST',
	url: "update_data.php?composition=update&ing=<?=$ingName;?>",
	title: 'To be declared',
	source: [
			 {value: '0', text: 'No'},
			 {value: '1', text: 'Yes'},
			 ],
	success: function (data) {
			reload_cmp_data();
	}
});

$('#tdCompositions').on('click', '[id*=cmpDel]', function () {
	var cmp = {};
	cmp.ID = $(this).attr('data-id');
    cmp.Name = $(this).attr('data-name');

	bootbox.dialog({
       title: "Confirm removal",
       message : 'Remove <strong>'+ cmp.Name +'</strong> from the list?',
       buttons :{
           main: {
               label : "Remove",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: 'update_data.php', 
					type: 'POST',
					data: {
						composition: 'delete',
						allgID: cmp.ID,
						ing: '<?=$ingName?>'
						},
					dataType: 'json',
					success: function (data) {
						reload_cmp_data();
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


$('#addComposition').on('click', '[id*=cmpAdd]', function () {
	$.ajax({ 
		url: 'update_data.php', 
		type: 'POST',
		data: {
			composition: 'add',
			allgName: $("#allgName").val(),
			allgPerc: $("#allgPerc").val(),
			allgCAS: $("#allgCAS").val(),
			allgEC: $("#allgEC").val(),	
			GHS: $("#GHS").val(),	
			addToIng: $("#addToIng").is(':checked'),
			addToDeclare: $("#addToDeclare").is(':checked'),
			ing: '<?=$ingName?>'
			},
		dataType: 'json',
		success: function (data) {
			if (data.success) {
	 	 		var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				$("#allgName").val('');
				$("#allgCAS").val('');
				$("#allgEC").val('');
				$("#allgPerc").val('');
				$("#GHS").val('');
				reload_cmp_data();
			}else{
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
		
			$('#inf').html(msg);

		}
	  });
});

$('#addCSV').on('click', '[id*=cmpCSV]', function () {
    $("#CSVImportMsg").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	$("#cmpCSV").prop("disabled", true);
		
	var fd = new FormData();
    var files = $('#CSVFile')[0].files;

	if(files.length > 0 ){
	fd.append('CSVFile',files[0]);
	$.ajax({
	   url: 'upload.php?type=cmpCSVImport&ingID=<?=$ingName?>',
	   type: 'POST',
	   data: fd,
	   contentType: false,
	   processData: false,
			 cache: false,
	   success: function(response){
		 if(response != 0){
			$("#CSVImportMsg").html(response);
			$("#cmpCSV").prop("disabled", false);
			reload_cmp_data();
		  }else{
			$("#CSVImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
			$("#cmpCSV").prop("disabled", false);
		  }
		},
	 });
	}else{
		$("#CSVImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
		$("#cmpCSV").prop("disabled", false);
	}
});

function reload_cmp_data() {
    $('#tdCompositions').DataTable().ajax.reload(null, true);
};
</script>
<!-- ADD COMPOSITION-->
<div class="modal fade" id="addComposition" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addComposition" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addComposition">Add composition</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="inf"></div>
        <div class="mb-3">
	        <label for="allgName" class="form-label">Name</label>
    	    <input class="form-control" name="allgName" type="text" id="allgName" />
        </div>
        <div class="mb-3">
        	<label for="allgCAS" class="form-label">CAS</label>
        	<input class="form-control" name="allgCAS" type="text" id="allgCAS" />
        </div>
        <div class="mb-3">
        	<label for="allgEC" class="form-label">EINECS</label>
        	<input class="form-control" name="allgEC" type="text" id="allgEC" />
        </div>
        <div class="mb-3">
	        <label for="allgPerc" class="form-label">Percentage</label>
    	    <input class="form-control" name="allgPerc" type="text" id="allgPerc" />
        </div>
        <div class="mb-3">
	        <label for="GHS" class="form-label">GHS Classification</label>
    	    <input class="form-control" name="GHS" type="text" id="GHS" />
        </div>        
        <hr class="dropdown-divider" />
        <div class="form-check">
            <input class="form-check-input" name="addToDeclare" type="checkbox" id="addToDeclare" value="1" />
            <label class="form-check-label" for="addToDeclare">To declare in warnings</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" name="addToIng" type="checkbox" id="addToIng" value="1" />
            <label class="form-check-label" for="addToIng">Add to ingredients</label>
        </div>
    </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="cmpAdd" value="Add">
      </div>
    </div>
  </div>
</div>
</div>

<!--ADD FROM CSV MODAL-->
<div class="modal fade" id="addCSV" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addCSV" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import CSV</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      
      <div id="CSVImportMsg"></div>
     	<div class="mb-3">
  			<label for="formFile" class="form-label">Choose file</label>
            <input type="file" name="CSVFile" id="CSVFile" class="form-control" />
        </div>
        <hr class="dropdown-divider" />        
        <p>CSV format: <strong>ingredient,CAS,EINECS,percentage,GHS</strong></p>
        <p>Example: <em><strong>Citral,5392-40-5,226-394-6,0.15,Skin Irrit. 2-Eye Irrit</strong></em></p>
        <p>Duplicates will be ignored.</p>
            
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="cmpCSV" class="btn btn-primary" id="cmpCSV" value="Import">
      </div>
    </div>
  </div>
</div>
</div>
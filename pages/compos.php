<?php

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$ingID = mysqli_real_escape_string($conn, $_GET["id"]);
$ingName = mysqli_real_escape_string($conn, $_GET["name"]);

?>

<h3>Composition</h3>
<hr>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addComposition">Add new</a>
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
          <th>Actions</th>
      </tr>
   </thead>
</table>
<script type="text/javascript" language="javascript" >
$(document).ready(function() {
	
	$('[data-toggle="tooltip"]').tooltip();
	var tdCompositions = $('#tdCompositions').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
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
			  { data : 'name', title: 'Document', render: cmpName },
			  { data : 'cas', title: 'CAS', render: cmpCAS},
			  { data : 'ec', title: 'EINECS', render: cmpEC},
			  { data : 'percentage', title: 'Percentage', render: cmpPerc},
			  { data : null, title: 'Actions', render: cmpActions},		   
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

function cmpActions(data, type, row){
	return '<a href="#" id="cmpDel" class="fas fa-trash" data-id="'+row.id+'" data-name="'+row.name+'"></a>';    
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
               className : "btn-primary",
               callback: function (){
	    			
				$.ajax({ 
					url: 'update_data.php', 
					type: 'GET',
					data: {
						composition: 'delete',
						allgID: cmp.ID,
						ing: '<?=$ingName?>'
						},
					dataType: 'html',
					success: function (data) {
						reload_cmp_data();
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


$('#addComposition').on('click', '[id*=cmpAdd]', function () {
	$.ajax({ 
		url: 'update_data.php', 
		type: 'GET',
		data: {
			composition: 'add',
			allgName: $("#allgName").val(),
			allgPerc: $("#allgPerc").val(),
			allgCAS: $("#allgCAS").val(),
			allgEC: $("#allgEC").val(),	
			addToIng: $("#addToIng").is(':checked'),				
			ing: '<?=$ingName?>'
			},
		dataType: 'html',
		success: function (data) {
			$('#inf').html(data);
			$("#allgName").val('');
			$("#allgCAS").val('');
			$("#allgEC").val('');
			$("#allgPerc").val('');
			reload_cmp_data();
		}
	  });
});

function reload_cmp_data() {
    $('#tdCompositions').DataTable().ajax.reload(null, true);
};
</script>
<!-- ADD COMPOSITION-->
<div class="modal fade" id="addComposition" tabindex="-1" role="dialog" aria-labelledby="addComposition" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addComposition">Add composition</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="inf"></div>
            Name: 
            <input class="form-control" name="allgName" type="text" id="allgName" />
            <p>
            CAS: 
            <input class="form-control" name="allgCAS" type="text" id="allgCAS" />
            <p>
            EINECS: 
            <input class="form-control" name="allgEC" type="text" id="allgEC" />
            <p>            
            Percentage %:
            <input class="form-control" name="allgPerc" type="text" id="allgPerc" />
            </p>
            <div class="dropdown-divider"></div>
      <label>
         <input name="addToIng" type="checkbox" id="addToIng" value="1" />
        Add to ingredients
      </label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="cmpAdd" value="Add">
      </div>
    </div>
  </div>
</div>
</div>
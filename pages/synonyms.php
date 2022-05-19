<?php

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$CAS = mysqli_real_escape_string($conn, $_GET["cas"]);
$ingName = mysqli_real_escape_string($conn, $_GET["name"]);

?>

<h3>Synonyms</h3>
<hr>
<div class="card-body">
 	<div class="text-right">
  		<div class="btn-group">
   			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
    		<div class="dropdown-menu dropdown-menu-right">
        		<a class="dropdown-item" href="#" data-toggle="modal" data-target="#addSynonym">Add new</a>
                <?php if(preg_match('/(Mixture|Blend)/i', $CAS) === 0){	?>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#pubchem_import">Import from PubChem</a>

                <?php } ?>
    		</div>
  		</div>                    
	</div>
</div>

<table id="tdSynonyms" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Synonym</th>
          <th>Source</th>
          <th>Actions</th>
      </tr>
   </thead>
</table>


<script type="text/javascript" language="javascript" >
$(document).ready(function() {

$('[data-toggle="tooltip"]').tooltip();
var tdSynonyms = $('#tdSynonyms').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
	],
	dom: 'lfrtip',
	processing: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
		emptyTable: 'No synonyms added yet.',
		search: 'Search:'
		},
	ajax: {	url: '/core/list_ing_synonyms_data.php?id=<?=$ingName?>' },
	columns: [
			  { data : 'synonym', title: 'Synonym', render: synName },
			  { data : 'source', title: 'Source', render: synSource},
			  { data : null, title: 'Actions', render: synActions},		   
			 ],
	order: [[ 1, 'asc' ]],
	lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
	pageLength: 20,
	displayLength: 20,		
	});
});

function synName(data, type, row){
	return '<i class="synonym pv_point_gen" data-name="synonym" data-type="text" data-pk="'+row.id+'">'+row.synonym+'</i>';    
}

function synSource(data, type, row){
	return '<i class="source pv_point_gen" data-name="source" data-type="text" data-pk="'+row.id+'">'+row.source+'</i>';    
}


function synActions(data, type, row){
	return '<a href="#" id="synDel" class="fas fa-trash" data-id="'+row.id+'" data-name="'+row.synonym+'"></a>';    
}

$('#tdSynonyms').editable({
	container: 'body',
    selector: 'i.synonym',
    type: 'POST',
    url: "update_data.php?synonym=update&ing=<?=$ingName;?>",
    title: 'Synonym'
});

$('#tdSynonyms').editable({
   container: 'body',
   selector: 'i.source',
   type: 'POST',
   url: "update_data.php?synonym=update&ing=<?=$ingName;?>",
   title: 'Source'
});


$('#tdSynonyms').on('click', '[id*=synDel]', function () {
	var syn = {};
	syn.ID = $(this).attr('data-id');
    syn.Name = $(this).attr('data-name');

	bootbox.dialog({
       title: "Confirm removal",
       message : 'Remove <strong>'+ syn.Name +'</strong> from the list?',
       buttons :{
           main: {
               label : "Remove",
               className : "btn-primary",
               callback: function (){
	    			
				$.ajax({ 
					url: 'update_data.php', 
					type: 'GET',
					data: {
						synonym: 'delete',
						id: syn.ID,
						},
					dataType: 'html',
					success: function (data) {
						reload_syn_data();
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


$('#import').on('click', '[id*=importPubChem]', function () {
	$('#importPubChem').attr('disabled', true);
	$('#pvImportMsg').html('<div class="alert alert-info">Please wait...</div>');			
	$.ajax({ 
		url: 'update_data.php', 
		type: 'GET',
		data: {
			synonym: 'import',
			method: 'pubchem',
			ing: '<?=$ingName?>',
			cas: '<?=$CAS?>',
			},
		dataType: 'html',
		success: function (data) {
			$('#pvImportMsg').html(data);
			$('#importPubChem').attr('disabled', false);
			reload_syn_data();
			reload_overview();

		}
	});		
             
});

$('#addSynonym').on('click', '[id*=synAdd]', function () {
	$.ajax({ 
		url: 'update_data.php', 
		type: 'GET',
		data: {
			synonym: 'add',
			sName: $("#synonym").val(),
			source: $("#source").val(),
			ing: '<?=$ingName?>'
			},
		dataType: 'html',
		success: function (data) {
			$('#infSyn').html(data);
			$("#synonym").val('');
			$("#source").val('');
			reload_syn_data();
		}
	  });
});


function reload_syn_data() {
    $('#tdSynonyms').DataTable().ajax.reload(null, true);
};
</script>
<!-- ADD SYNONYM -->
<div class="modal fade" id="addSynonym" tabindex="-1" role="dialog" aria-labelledby="addSynonym" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSynonym">Add synonym</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="infSyn"></div>
            Name: 
            <input class="form-control" name="synonym" type="text" id="synonym" />
            <p>
            Source: 
            <input class="form-control" name="source" type="text" id="source" />
            </p>
            <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="synAdd" value="Add">
      </div>
    </div>
  </div>
</div>
</div>

<!-- PUBCHEM SYNONYM IMPORT -->
<div class="modal fade" id="pubchem_import" tabindex="-1" role="dialog" aria-labelledby="pubchem_import" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import synonyms from PubChem</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       	<div id="pvImportMsg"></div>
	      <strong>WARNING:</strong><br />
    	  you are about to import data from PubChem.com, if your local database contains already the same data, new data will not be imported. <p></p>
		
      	<div class="dropdown-divider"></div>
	  	For more info regarding PubChem Rest API please refer to its documentation <a href="https://pubchemdocs.ncbi.nlm.nih.gov/about" target="_blank">here.</a> 
      </div>
      <div class="modal-footer" id="import">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="importPubChem" value="Import">
      </div>
     </form>
    </div>
  </div>
</div>
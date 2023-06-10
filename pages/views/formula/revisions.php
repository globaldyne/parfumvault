<h3>Revisions</h3>
<hr>
<div id="msg_rev"></div>
<table id="tdRevisions" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Revision ID</th>
          <th>Revision taken</th>
          <th>Actions</th>
      </tr>
   </thead>
</table>

<script type="text/javascript" language="javascript" >
$(document).ready(function() {

	$('[data-toggle="tooltip"]').tooltip();
	var tdRevisions = $('#tdRevisions').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
	],
	dom: 'lrtip',
	processing: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
		emptyTable: 'No attachments found.',
		search: 'Search:'
		},
	ajax: {	
		url: '/core/list_revisions_data.php',
		type: 'GET',
		data: {
				fid: '<?=$_GET['fid']?>',
			},
		},
	columns: [
			  { data : 'revision', title: 'Revision ID' },
			  { data : 'revisionDate', title: 'Revision taken' },
			  { data : null, title: 'Actions', render: actions },		   
			],
	
	drawCallback: function ( settings ) {
			extrasShow();
	},
	order: [[ 0, 'asc' ]],
	lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
	pageLength: 20,
	displayLength: 20
	});
});



function actions(data, type, row){
	if(row.isCurrent == true){
		return '<strong>Current revision</strong>';
	}
	
	data = '<a href="/?do=compareFormulas&compare=2&revision='+row.revision+'&formula_a='+row.formulaID+'&formula_b='+row.fid+'" target="_blank" class="fas fa-greater-than-equal mr2" title="Compare with the current revision" rel="tipsy"></a>';
		
	data += '<a href="#" id="restore" class="fas fa-history mr2" data-id="'+row.fid+'" data-revision="'+row.revision+'"></a>';
	data += '<a href="#" id="dDel" class="fas fa-trash mr2" data-id="'+row.fid+'" data-revision="'+row.revision+'"></a>';
	
	return data;
}


//RESTORE REVISION
$('#tdRevisions').on('click', '[id*=restore]', function () {
	var r = {};
	r.ID = $(this).attr('data-id');
    r.Revision = $(this).attr('data-revision');

	bootbox.dialog({
       title: "Confirm revision restore",
       message : 'Restore revision <strong>'+ r.Revision +'</strong>?<p>Please note, this will overwrite the current formula.</p>',
       buttons :{
           main: {
               label : "Restore",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: '/pages/manageFormula.php', 
					type: 'GET',
					data: {
						restore: "rev",
						fid: r.ID,
						revision: r.Revision
						},
					dataType: 'json',
					success: function (data) {
						if ( data.success ) {
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
							reload_formula_data();
							reload_rev_data();
						} else {
							var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>' + data.error + '</strong></div>';
						}
						$('#msg_rev').html(msg);
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

//DELETE REVISION
$('#tdRevisions').on('click', '[id*=dDel]', function () {
	var r = {};
	r.ID = $(this).attr('data-id');
    r.Revision = $(this).attr('data-revision');

	bootbox.dialog({
       title: "Confirm revision delete",
       message : 'Delete revision <strong>'+ r.Revision +'</strong>?',
       buttons :{
           main: {
               label : "Remove",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: '/pages/manageFormula.php', 
					type: 'GET',
					data: {
						delete: "rev",
						fid: r.ID,
						revision: r.Revision
						},
					dataType: 'json',
					success: function (data) {
						if ( data.success ) {
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
							reload_rev_data();
						} else {
							var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>' + data.error + '</strong></div>';
						}
						$('#msg_rev').html(msg);
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



function reload_rev_data() {
    $('#tdRevisions').DataTable().ajax.reload(null, true);
};
</script>


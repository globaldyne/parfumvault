<h3>Revisions</h3>
<div id="msg_rev_info"><div class="alert alert-info">A revision will be automatically created each time you lock the formula if any changes in formulation. Alternatively you can manually create one from the revisions menu.</div>
<hr>
<div class="card-body">
    <div class="text-right">
      <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
            <div class="dropdown-menu dropdown-menu-right">
                <li><a class="dropdown-item" href="#" id="genRev"><i class="fa-solid fa-plus mr2"></i>Create revision</a><li>
            </div>
      </div>                    
    </div>
</div>

<div id="msg_rev"></div>

<table id="tdRevisions" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Revision ID</th>
          <th>Revision taken</th>
          <th>Method</th>
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
		{ orderable: false, targets: [3] }
	],
	dom: 'lrtip',
	processing: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
		emptyTable: 'No revisions found.',
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
			  { data : 'revisionMethod', title: 'Method' },
			  { data : null, title: 'Actions', render: actions },
			],
	
	drawCallback: function ( settings ) {
			extrasShow();
	},
	order: [[ 0, 'asc' ]],
	lengthMenu: [[5, 50, 100, -1], [5, 50, 100, "All"]],
	pageLength: 5,
	displayLength: 5
	});
});



function actions(data, type, row){
	if(row.isCurrent == true){
		return '<strong>Current revision</strong>';
	}
	
	data = '<a href="#" id="cmpRev" data-id="'+row.fid+'" data-revision="'+row.revision+'" class="fas fa-greater-than-equal mr2" title="Compare with the current revision" rel="tip"></a>';
		
	data += '<a href="#" id="restore" class="fas fa-history mr2" data-id="'+row.fid+'" data-revision="'+row.revision+'"></a>';
	data += '<a href="#" id="dDel" class="fas fa-trash mr2" data-id="'+row.fid+'" data-revision="'+row.revision+'"></a>';
	
	return data;
}

//COMPARE REVISION
$('#tdRevisions').on('click', '[id*=cmpRev]', function () {
  $.ajax({ 
	url: '/pages/cmp_formulas_data.php', 
	type: 'POST',
	data: {
		id_a: '<?=$_GET['id']?>',
		fid: '<?=$_GET['fid']?>',
		revID: $(this).attr('data-revision'),
		},
	dataType: 'html',
	success: function (data) {
		$('#cmp_res').html(data);
	}
  });

});

//CREATE REVISION
$('#genRev').click(function() {
  $.ajax({ 
	url: '/pages/update_data.php', 
	type: 'GET',
	data: {
		fid: '<?=$_GET['fid']?>',
		createRev: 'man',
		},
	dataType: 'json',
	success: function (data) {
		if ( data.success ) {
			var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
			reload_rev_data();
			//reload_formula_data();
		} else {
			var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>' + data.error + '</strong></div>';
		}
		$('#msg_rev').html(msg);
	}
  });

});


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
	$('#cmp_res').html('');
    $('#tdRevisions').DataTable().ajax.reload(null, true);
};
</script>
<hr>
<div id="cmp_res"></div>


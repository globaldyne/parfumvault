<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
<div class="container-fluid">
  <div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:reload_data()">Marketplace</a></h2>
    </div>
    <div class="card-body">
      <div class="table-responsive">
      <div id="data_area">
       		<table id="all-table-market" class="table table-bordered" width="100%" cellspacing="0">
            	<thead>
                	<tr>
                    	<th>Formula Name</th>
                        <th>Status</th>
                    	<th>Author</th>
                    	<th>License</th>
                    	<th>Published</th>
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
</div>

<!--Contact author modal-->
<div class="modal fade" id="contact-formula-author" tabindex="-1" role="dialog" aria-labelledby="contact-formula-author" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Contact <div id="fname" class="d-inline"></div> formula's author</h5>
      </div>
      <div class="modal-body">
      <div id="cntMsg"></div>
      	<div class="modal-body-main">
            <input type="hidden" name="fid" id="fid" />
            <input type="hidden" name="fname" id="fname" />
            <div class="alert alert-warning">You can contact the author of the formula using the form bellow if you have quaries or recommendations regarding this formula.<p><strong>Please note, your full name and your email address will be shared with the author(s) of the formula in order so they can get in touch to discuss further. Also, a copy of your message will be shared with the admins to ensure there is no service abuse.</strong></p></div>
            <div class="form-group">
                <label class="form-label">Full name:</label>
                <input name="contactName" id="contactName" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Email:</label>
                <input name="contactEmail" id="contactEmail" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Comments:</label>
                <textarea name="contactReason" id="contactReason" rows="3" class="form-control"></textarea>
            </div>
            <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
             <input type="submit" name="button" class="btn btn-primary" id="confirm-contact-author" value="Send message">
           </div>
      	</div>
    </div>
  </div>
 </div>
</div>

<!--Report formula modal-->
<div class="modal fade" id="report-market-formula" tabindex="-1" role="dialog" aria-labelledby="report-market-formula" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Report <div id="fname" class="d-inline"></div> formula</h5>
      </div>
      <div class="modal-body">
      <div id="reportMsg"></div>
      <div class="modal-body-main">
            <input type="hidden" name="fid" id="fid" />
            <input type="hidden" name="fname" id="fname" />
            <div class="alert alert-warning">If you believe that <strong><div id="fname" class="d-inline"></div></strong> formula violates our <a href="https://www.jbparfum.com/community_rules" target="_blank">community rules</a>, please use this form to report it explaining in detail what's wrong.<p>Once we receive the report and review it, will take actions, if any required and let the author know.</p><p><strong>Please don't use this form if you have queries or suggestions regarding the formula, use the <i>Contact Author</i> option instead.</strong></p></div>
            <div class="form-group">
                <label class="form-label">Full name:</label>
                <input name="reporterName" id="reporterName" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Email:</label>
                <input name="reporterEmail" id="reporterEmail" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Please provide the reason you reporting <div id="fname" class="d-inline"></div> formula in detail:</label>
                <textarea name="reportReason" id="reportReason" rows="3" class="form-control"></textarea>
            </div>
            <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
             <input type="submit" name="button" class="btn btn-primary" id="confirm-formula-report" value="Report fomula">
           </div>
        </div>
     </div>
  </div>
 </div>
</div>
<?php
if($qPVfids = mysqli_query($conn,"SELECT fid FROM formulasMetaData WHERE src = '1'")){
	while ($rPVFIDS = mysqli_fetch_array($qPVfids)){
		$result_fids[] = $rPVFIDS['fid'];	
	}
	$json_fids = json_encode($result_fids);
}
?>
<script type="text/javascript" language="javascript" >

const arrayFIDS = <?php echo $json_fids; ?> || '0';


$.fn.dataTable.ext.errMode = 'none';
function extrasShow() {
	$('[rel=tip]').tooltip({
		"html": true,
		"delay": {"show": 100, "hide": 0},
	});
	$('.popup-link').magnificPopup({
		type: 'iframe',
		closeOnContentClick: false,
		closeOnBgClick: false,
		showCloseBtn: true,
	});
};

var tableMarket = $("#all-table-market").DataTable({
   	ajax: {
	   url: '<?=$pvOnlineAPI?>',
	   type: 'POST',
	   dataType: 'json',
	   timeout: 5000,
	   data: function(d) {
		   d.do = 'MarketPlace'
		   d.action = 'list_all'
			if (d.order.length>0){
				d.order_by = d.columns[d.order[0].column].data
				d.order_as = d.order[0].dir
			}
		 },
	    },
	   columns: [
	    	{ data : 'name', title: 'Formula Name', render: name},
			{ data : null, title: 'Status', render: status},
	    	{ data : 'author', title: 'Author'},
	   		{ data : 'cost', title: 'License', render: cost},
	   		{ data : 'created_at', title: 'Published'},
	   		{ data : null, title: 'Actions', render: actions},				   
	  	],
	   processing: true,
	   serverSide: true,
	   searching: true,
	   language: {
		  loadingRecords: '&nbsp;',
		  processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Blending...</span>',
		  emptyTable: '<div class="alert alert-warning"><strong>No formulas found in Marketplace, please come back later.</strong></div>',
		  searchPlaceholder: 'Formula name..',
		  search: "Search for formula:"
        },
       order: [0,'asc'],
   	   columnDefs: [
			{ orderable: false, targets: [4] },
			{ className: 'text-center', targets: '_all' },				  
		],
	destroy: true,
	bFilter: true,
	paging:  true,
	info:   true,
	lengthMenu: [[20, 40, 60, 100], [20, 40, 60, 100]],
	drawCallback: function( settings ) {
		extrasShow();
	},
	createdRow: function(row, data, dataIndex){
	// initRating(row);
	},
	
		
				

}).on('error.dt', function ( e, settings, techNote, message ) {
	var m = message.split(' - ');
	$('#data_area').html('<div class="alert alert-danger"><strong>' + m[1] + '</strong></div>');
});

var detailRows = [];

$('#all-table-market tbody').on( 'click', '[id*=open-details]', function () {
	var tr = $(this).parents('tr');
	var row = tableMarket.row( tr );
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

tableMarket.on( 'draw', function () {
	$.each( detailRows, function ( i, id ) {
		$('#'+id+' td:first-child + td').trigger( 'click' );
	} );
} );


function format ( d ) {		

    details = '<strong>Description:</strong><br><span class="formula_details">'+d.notes +
		'</span><br><strong>Published:</strong><br><span class="formula_details">'+d.created_at +
		'</span><br><strong>Updated:</strong><br><span class="formula_details">'+d.updated_at+'</span>' +
		'</span><br><strong>Downloads:</strong><br><span class="formula_details">'+d.downloads+'</span>' +
		'<br><strong>Labels:</strong><br>';

	for (var key in d.labels) {
		if (d.labels.hasOwnProperty(key)) {
			details+='<span class="formula_details mr2 label pv-label label-md label-default">'+d.labels[key].name+'</span>';
		}
	}        
	
	return details;
}

function cost(data, type, row){
	if(row.cost == 0){
		data = '<span class="label pv-label label-md label-success">FREE!</span>';
	}else{
		data = '<span class="label pv-label label-md label-info">' + row.currency + row.cost + '</span>';
	}
	return data;
}

function name(data, type, row){
	
	data = '<i class="pv_point_gen pv_gen_li" id="open-details"> ' + data + '</i>';

  	return data;
}

function status(data, type, row, meta){
	
	if ( arrayFIDS.includes(row.fid)) {	
		data = '<span class="label pv-label label-md label-success"><strong>Downloaded</strong></span>';
	}else{
		data = '<span class="label pv-label label-md label-warning"><strong>NEW!!!</strong></span>';
	}
	
	return data;
}

function actions(data, type, row, meta){
		data = '<div class="dropdown">' +
        '<button type="button" class="btn btn-primary btn-floating dropdown-toggle hidden-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
            '<ul class="dropdown-menu dropdown-menu-right">';
		
		data += '<li><i class="pv_point_gen pv_gen_li dropdown-item" id="import-market-formula" data-id="'+row.id+'" data-name="'+row.name+'" rel="tip" title="Import '+ row.name +' to my database" ><i class="fas fa-download mr2"></i>Import Formula</i></li>';
		
		data += '<li><i class="pv_point_gen pv_gen_li dropdown-item open-contact-dialog" data-toggle="modal"  data-target="#contact-formula-author" data-backdrop="static" data-id="'+row.id+'" data-name="'+row.name+'" rel="tip" title="Contact '+ row.author +' regarding the formula"><i class="fas fa-id-card mr2"></i>Contact the author</i></li>';
		
		data += '<div class="dropdown-divider"></div>';
		
		data += '<li><i class="pv_point_gen pv_gen_li dropdown-item open-report-dialog" data-toggle="modal"  data-target="#report-market-formula" data-backdrop="static" style="color: #c9302c;" rel="tip" title="Report '+ row.name +' to admins" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-bug mr2"></i>Report formula</i></li>';
		
		data += '</ul></div>';
	
    return data;
}

function reload_data() {
    $('#all-table-market').DataTable().ajax.reload(null, true);
};

//Import Formula
$('#all-table-market').on('click', '[id*=import-market-formula]', function () {
	$("#impMsg").html('<div class="alert alert-info"><img src="/img/loading.gif" class="mr2"/>Please wait...</div>');
	
	var frm = {};
	frm.ID = $(this).attr('data-id');
	frm.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm import",
       message : '<div id="impMsg">Import <strong>'+ frm.Name +'</strong>\'s data from Marketplace? <hr/><div class="alert alert-warning"><strong>Please note: data maybe incorrect and/or incomplete, you should validate them after import.</strong></div></div>',
       buttons :{
           main: {
               label : "Import formula",
               className : "btn-warning",
               callback: function (){
	    			
					$.ajax({
						url: '/pages/pvonline.php', 
						type: 'POST',
						data: {
							action: "import",
							source: "pvMarket",
							kind: "formula",
							fid: frm.ID,
							},
						dataType: 'json',
						success: function (data) {
							if(data.success) {
							    location.reload(true);
								bootbox.hideAll();
								return true;
							} else {								
								$('#impMsg').html('<div class="alert alert-danger">' + data.error + '</div>');
								return false;
							}
							
						}
					});
				
                 return false;
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


//Contact author
$("#all-table-market").on("click", ".open-contact-dialog", function () {
	$('#cntMsg').html('');
	$('#contact-formula-author .modal-body-main').show();
	$("#contactName, #contactEmail, #contactReason").val('');
	
	var fname = $(this).data('name');
	var fid = $(this).data('id');
	
	$("#contact-formula-author #fname").val( fname );
	$("#contact-formula-author #fid").val( fid );
	$("#contact-formula-author #fname").html( fname );

});

$('#contact-formula-author').on('click', '[id*=confirm-contact-author]', function () {
	$("#cntMsg").html('<div class="alert alert-info"><img src="/img/loading.gif" class="mr2"/>Please wait...</div>');
	$.ajax({ 
		url: '/pages/pvonline.php', 
		type: 'POST',
		data: {
			action: "contactAuthor",
			src: 'pvMarket',
			contactName: $('#contactName').val(),
			contactEmail: $('#contactEmail').val(),
			contactReason: $('#contactReason').val(),			
			fname: $('#fname').val(),
			fid: $('#fid').val()
			},
		dataType: 'json',
		success: function (data) {
			if (data.success) {
				var msg = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="modal" aria-label="close">x</a>' + data.success + '</div>';
				$('#contact-formula-author .modal-body-main').hide();
			}else{
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#cntMsg').html(msg);
		}
	  });
});

//Report formula
$("#all-table-market").on("click", ".open-report-dialog", function () {
	$('#reportMsg').html('');
	$('#report-market-formula .modal-body-main').show();
	$("#reporterName, #reporterEmail, #reportReason").val('');
	
	var fname = $(this).data('name');
	var fid = $(this).data('id');
	
	$("#report-market-formula #fname").val( fname );
	$("#report-market-formula #fid").val( fid );
	$("#report-market-formula #fname").html( fname );

});

$('#report-market-formula').on('click', '[id*=confirm-formula-report]', function () {
	$("#reportMsg").html('<div class="alert alert-info"><img src="/img/loading.gif" class="mr2"/>Please wait...</div>');
	$.ajax({ 
		url: '/pages/pvonline.php', 
		type: 'POST',
		data: {
			action: 'report',
			src: 'pvMarket',
			reporterName: $('#reporterName').val(),
			reporterEmail: $('#reporterEmail').val(),
			reportReason: $('#reportReason').val(),
			fname: $('#report-market-formula #fname').val(),
			fid: $('#report-market-formula #fid').val()
			},
		dataType: 'json',
		success: function (data) {
			if (data.success) {
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="modal" aria-label="close">x</a>' + data.success + '</div>';
				$('#report-market-formula .modal-body-main').hide();
			}else{
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#reportMsg').html(msg);
		}
	  });
});

</script>


<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>

<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="/?do=IFRA">IFRA Library</a></h2>
            </div>
            <div class="card-body">
               
                  <div class="text-right">
                    <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
                      <div class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="#" data-backdrop="static" data-toggle="modal" data-target="#ifra_import"><i class="fa-solid fa-file-excel mr2"></i>Import IFRA xls</a>
                        <?php if($settings['pubChem'] == '1'){?>
                        <li><a class="dropdown-item" href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#pubChem_import"><i class="fa-solid fa-file-import mr2"></i>Import images</a></li>
                        <?php } ?>

                        <li><a class="dropdown-item" id="csv" href="#"><i class="fa-solid fa-file-export mr2"></i>Export to CSV</a></li>
                      </div>
                    </div>
                  </div>
                <div class="dropdown-divider"></div>
                <div class="table-responsive">
                <table id="tdDataIFRA" class="table table-striped table-bordered">
                  <thead>
                      <tr>
                      	<th>Structure</th>
                        <th>Name</th>
                        <th>CAS #</th>
                        <th>Amendment</th>
                        <th>Last publication</th>
                        <th>IFRA Type</th>
                        <th>Cat1%</th>
                        <th>Cat2%</th>
                        <th>Cat3%</th>
                        <th>Cat4%</th>
                        <th>Cat5A%</th>
                        <th>Cat5B%</th>
                        <th>Cat5C%</th>
                        <th>Cat5D%</th>
                        <th>Cat6%</th>
                        <th>Cat7A%</th>
                        <th>Cat7B%</th>
                        <th>Cat8%</th>
                        <th>Cat9%</th>
                        <th>Cat10A%</th>
                        <th>Cat11A%</th>
                        <th>Cat11B%</th>
                        <th>Cat12%</th>
                      </tr>
                   </thead>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<!--IFRA IMPORT-->
<div class="modal fade" id="ifra_import" tabindex="-1" role="dialog" aria-labelledby="ifra_import" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-ifra" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ifra_import">Import IFRA xls file</h5>
      </div>
      <div class="modal-body">
       <div id="IFRAImportMsg"></div>
    		<div class="row">
        		<div class="col-lg mx-auto">
                    <div class="form-group">
                        <div class="col-sm-10">
                            <label class="control-label">IFRA xls File</label>
                            <input type="file" id="ifraXLS" name="ifraXLS"  class="form-control" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-10">
                           <input name="overwrite" type="checkbox" id="overwrite"  /> 
                           <label class="control-label" for="overwrite">Overwite current data</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-10">
                           <input name="updateCAS" type="checkbox" id="updateCAS" checked="checked" /> 
                           <label class="control-label" for="updateCAS">Modify original file</label> <i class="fa-solid fa-circle-info pv_point_gen" rel="tip" data-title="This is required if you are importing the original IFRA file. Currently only supported the 49th amendment xls format."></i>
                        </div>
                    </div>

            	</div>
            </div>
            
            <div id="overwrite-msg">
                <div class="dropdown-divider"></div>
            	<div class="col-sm col-sm-10 text-xs-center alert alert-warning">
                	<p class="alert-link"><strong>IMPORTANT:</strong></p>
                	<p class="alert-link">This operation will wipe out any data already in your IFRA Library, so please make sure the file you uploading is in the right format and have taken a <a href="/pages/operations.php?do=backupDB">backup</a> before.</p>
                </div>
           </div>
           
       </div>
       <div class="dropdown-divider"></div>
       <div class="col-sm col-sm-10 text-xs-center">
       		<strong>The IFRA xls can be downloaded from its official <a href="https://ifrafragrance.org/safe-use/standards-guidance" target="_blank">web site</a></strong>
       </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnIFRAC">Cancel</button>
        <input type="submit" name="btnImportIFRA" class="btn btn-primary" id="btnImportIFRA" value="Import">
      </div>
    </div>
  </div>
</div>

<!--PUBCHEM IMPORT-->
<div class="modal fade" id="pubChem_import" tabindex="-1" role="dialog" aria-labelledby="pubChem_import" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-pubChem" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import images from PubChem</h5>
      </div>
      <div class="modal-body">
       <div id="pbmportMsg"></div>
       <p class="alert-link"><strong>Confirm import</strong></p>
       <p class="alert-link">Are you sure you want to import data from pubChem? This operation will overwrite any existing image data in your IFRA database.</p>
       <p>By using this service, you agree with <a href="https://pubchemdocs.ncbi.nlm.nih.gov/about" target="_blank">PubChem's</a> terms</p>
       </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="ImportpbC">Cancel</button>
        <input type="submit" name="btnImport" class="btn btn-primary" id="Importpb" value="Import">
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	
	var tdDataIFRA = $('#tdDataIFRA').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
	],
	dom: 'lrftip',
	processing: true,
	serverSide: true,
	searching: true,
	mark: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<div class="spinner-grow mr2"></div>Please Wait...',
		zeroRecords: 'Nothing found',
		search: 'Quick Search:',
		searchPlaceholder: 'Name, CAS, synonyms..',
		},
	ajax: {	
		url: '/core/list_IFRA_data.php',
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
			{ data : 'image', title: 'Structure', render: image },
            { data : 'name', title: 'Name', render: name },
			{ data : 'cas', title: 'CAS' },
			{ data : 'amendment', title: 'Amendment' },
			{ data : 'last_pub', title: 'Last publication' },
			{ data : 'type', title: 'IFRA Type' },
			{ data : 'cat1', title: 'Cat1%' },
			{ data : 'cat2', title: 'Cat2%' },
			{ data : 'cat3', title: 'Cat3%' },
			{ data : 'cat4', title: 'Cat4%' },
			{ data : 'cat5A', title: 'Cat5A%' },
			{ data : 'cat5B', title: 'Cat5B%' },
			{ data : 'cat5C', title: 'Cat5C%' },
			{ data : 'cat5D', title: 'Cat5D%' },
			{ data : 'cat6', title: 'Cat6%' },
			{ data : 'cat7A', title: 'Cat7A%' },
			{ data : 'cat7B', title: 'Cat7B%' },
			{ data : 'cat8', title: 'Cat8%' },
			{ data : 'cat9', title: 'Cat9%' },
			{ data : 'cat10A', title: 'Cat10A%' },
			{ data : 'cat11A', title: 'Cat11A%' },
			{ data : 'cat11B', title: 'Cat11B%' },
			{ data : 'cat12', title: 'Cat12%' },
			],
	order: [[ 1, 'asc' ]],
	lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
	pageLength: 20,
	displayLength: 20,
	});
	
	var detailRows = [];
 
    $('#tdDataIFRA tbody').on( 'click', 'tr td:first-child + td', function () {
        var tr = $(this).parents('tr');
        var row = tdDataIFRA.row( tr );
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
 
    tdDataIFRA.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' td:first-child + td').trigger( 'click' );
        } );
    } );
});

function format ( d ) {
    details = '<strong>Synonyms:</strong><br><span class="ifra_details">'+d.synonyms+
	'</span><br><strong>CAS Comment:</strong><br><span class="ifra_details">'+d.cas_comment+
	'</span><br><strong>Risk:</strong><br><span class="ifra_details">'+d.risk+
	'</span><br><strong>Specified Notes:</strong><br><span class="ifra_details">'+d.specified_notes+
	'</span><br><strong>Flavor Use:</strong><br><span class="ifra_details">'+d.flavor_use;

	return details;
}

function name(data, type, row){
	return '<i class="pv_point_gen pv_gen_li">'+row.name+'</i>';
}

function image(data, type, row){
	return '<img src="data:image/png;base64, '+row.image+'" class="img_ifra noexport"/>';
}

function reload_ifra_data() {
    $('#tdDataIFRA').DataTable().ajax.reload(null, true);
}

$('#csv').on('click',function(){
	$("#tdDataIFRA").tableHTMLExport({
		type:'csv',
		filename:'ifra.csv',
		separator: ',',
		newline: '\r\n',
		trimContent: true,
		quoteFields: true,
		
		ignoreColumns: '.noexport',
		ignoreRows: '.noexport',
		
		htmlContent: false,  
		consoleLog: false   
	}); 
});

$('#btnImportIFRA').click(function() {	
	$("#IFRAImportMsg").html('<div class="alert alert-info"><img src="/img/loading.gif" class="mr2"/>Please wait, file upload in progress....</div>');
	$("#btnImportIFRA").prop("disabled", true);
	$("#btnIFRAC").prop("disabled", true);

	
	var fd = new FormData();
    var files = $('#ifraXLS')[0].files;
    var modify = $('#updateCAS').prop("checked");
    var overwrite = $('#overwrite').prop("checked");

       if(files.length > 0 ){
        fd.append('ifraXLS',files[0]);
        $.ajax({
           url: '/pages/upload.php?type=IFRA&updateCAS=' + modify + '&overwrite='+ overwrite,
           type: 'POST',
           data: fd,
           contentType: false,
           processData: false,
		         cache: false,
		   dataType: 'json',
           success: function(response){
             if(response.success){
				 $("#IFRAImportMsg").html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+response.success+'</div>');
				// $("#btnImportIFRA").hide();
				 $("#btnIFRAC").html('Close');
				 $("#btnImportIFRA").prop("disabled", false);
				 $("#btnIFRAC").prop("disabled", false);
				 reload_ifra_data();
              }else{
                $("#IFRAImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+response.error+'</div>');
				$("#btnImportIFRA").prop("disabled", false);
				$("#btnIFRAC").prop("disabled", false);
              }
            },
         });
  }else{
	$("#IFRAImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
	$("#btnImportIFRA").prop("disabled", false);
  }	
});

$('#Importpb').click(function() {	
	$("#pbmportMsg").html('<div class="alert alert-info"><img src="/img/loading.gif" class="mr2"/>Please wait, this may take a few minutes, depending your IFRA library size and your internet connection...</div>');
	$("#Importpb").prop("disabled", true);
	$("#ImportpbC").hide();

	$.ajax({
		url: '/pages/update_data.php', 
		type: 'GET',
		data: {
			IFRA_PB: "import",
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){				
				$('#pbmportMsg').html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>');
				$("#Importpb").hide();
				$("#ImportpbC").show();
				$('#ImportpbC').html('Close');
				$("#ImportpC").show();
				reload_ifra_data();
			}else{
				$('#pbmportMsg').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>');
				$("#Importpb").show();
				$("#ImportpbC").show();
				$("#Importpb").prop("disabled", false);
				$("#ImportpC").show();
			}
		}
	});
});


$("#overwrite-msg").hide();

$("#overwrite").click(function() {
    if($(this).is(":checked")) {
        $("#overwrite-msg").show();
    } else {
        $("#overwrite-msg").hide();
    }
});

</script>

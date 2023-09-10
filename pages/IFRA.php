<?php
require_once(__ROOT__.'/func/php-settings.php');
?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="/?do=IFRA">IFRA Library</a></h2>
            </div>
            <div class="card-body">
               <div id="iframsg"></div>
                  <div class="text-right">
                    <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                      <div class="dropdown-menu dropdown-menu-right">
                        <li class="dropdown-header">Import</li> 
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#ifra_import"><i class="fa-solid fa-file-excel mx-2"></i>Import IFRA xls</a>
                                	  
                         <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#import_ifra_json"><i class="fa-solid fa-file-import mx-2"></i>Import from JSON</a></li>

                        <?php if($settings['pubChem'] == '1'){?>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#pubChem_import"><i class="fa-solid fa-file-import mx-2"></i>Import images</a></li>
                        <?php } ?>
                        <li class="dropdown-header">Export</li> 
           				<li><a class="dropdown-item" href="/pages/operations.php?action=exportIFRA"><i class="fa-solid fa-file-code mx-2"></i>Export as JSON</a></li>
                        <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export as CSV</a></li>
                      </div>
                    </div>
                  </div>
                <div class="dropdown-divider"></div>
                <div class="table-responsive">
                <div>
                Toggle column: <a class="toggle-vis pv_point_gen_color" data-column="0">Structure</a> - <a class="toggle-vis pv_point_gen_color" data-column="4">Last publication</a>
    			</div>
                <table id="tdDataIFRA" class="stripe row-border order-column" style="width:100%">
                  <thead>
                      <tr>
                      	<th>Structure</th>
                        <th>Name</th>
                        <th>CAS #</th>
                        <th>Amendment</th>
                        <th>Last publication</th>
                        <th>IFRA Type</th>
                        <th>Implementation deadline for existing creations</th>
                        <th>Implementation deadline for new creations</th>
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
         				<th data-priority="1"></th>
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
<div class="modal fade" id="ifra_import" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ifra_import" aria-hidden="true">
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
                          <label class="control-label" for="IFRAver">IFRA amendment</label> <i class="fa-solid fa-circle-info pv_point_gen" data-bs-toggle="tooltip" title="IFRA file format has been slightly changed after amendment 49, to maintain backwards compatibility, we added the option to select which version you importing."></i>
                          <select id="IFRAver" class="form-control">
                              <option value="0" disabled>Please select amendment format</option>
                              <option value="49">Amendment 49 or older format</option>
                              <option value="51" selected>Amendment 51 format</option>
                          </select>
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
                           <label class="control-label" for="updateCAS">Modify original file</label> <i class="fa-solid fa-circle-info pv_point_gen" data-bs-toggle="tooltip" title="This is required if you are importing the original IFRA file."></i>
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnIFRAC">Cancel</button>
        <input type="submit" name="btnImportIFRA" class="btn btn-primary" id="btnImportIFRA" value="Import">
      </div>
    </div>
  </div>
</div>

<!--PUBCHEM IMPORT-->
<div class="modal fade" id="pubChem_import" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="pubChem_import" aria-hidden="true">
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="ImportpbC">Cancel</button>
        <input type="submit" name="btnImport" class="btn btn-primary" id="Importpb" value="Import">
      </div>
    </div>
  </div>
</div>

<!--IMPORT JSON MODAL-->
<div class="modal fade" id="import_ifra_json" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="import_ifra_json" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import IFRA from a JSON file</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="JSRestMsg"></div>
      <div class="progress">  
         <div id="uploadProgressBar" class="progress-bar" role="progressbar" aria-valuemin="0"></div>
      </div>
      <div id="backupArea">
      
          <div class="form-group">
              <label class="col-md-3 control-label">JSON file:</label>
              <div class="col-md-8">
                 <input type="file" name="backupFile" id="backupFile" class="form-control" />
              </div>
          </div>
          
          <div class="col-sm dropdown-divider"></div>
          <div class="col-md-12 alert alert-warning">
             <p><strong>IMPORTANT:</strong></p>
              <ul>
                <li><div id="raw" data-size="<?=getMaximumFileUploadSizeRaw()?>">Maximum file size: <strong><?=getMaximumFileUploadSize()?></strong></div></li>
                <li>Your current IFRA Library will be <strong>removed</strong> during the import. Please make sure you have taken a backup before imporing a JSON file.</li>
              </ul>
    			<p>&nbsp;</p>
            </div>
          </div>
      
      </div>
	  <div class="modal-footer">
        <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK" value="Cancel">
        <input type="submit" name="btnRestore" class="btn btn-primary" id="btnRestoreIFRA" value="Import">
      </div>
   
  </div>
  
</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	
   $('[data-bs-toggle="tooltip"]').tooltip();

	var tdDataIFRA = $('#tdDataIFRA').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [25]},
		{ responsivePriority: 1, targets: 0 }
	],
	dom: 'lrftip',
	buttons: [{
			extend: 'csvHtml5',
			title: "IFRALibrary"
	}],
	processing: true,
	serverSide: true,
	searching: true,
	mark: true,
	responsive: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<div class="spinner-grow mx-2"></div>Please Wait...',
		zeroRecords: 'Nothing found',
		search: 'Quick Search:',
		searchPlaceholder: 'Name, CAS, synonyms...',
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
			{ data : 'cas', title: 'CAS', render: CAS },
			{ data : 'amendment', title: 'Amendment' },
			{ data : 'last_pub', title: 'Last publication' },
			{ data : 'type', title: 'IFRA Type' },
			{ data : 'deadline_existing', title: 'Implementation deadline for existing creations' },
			{ data : 'deadline_new', title: 'Implementation deadline for new creations' },
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
			{ data : null, title: '', render: actions },

			],
	order: [[ 1, 'asc' ]],
	lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
	pageLength: 20,
	displayLength: 20,
	
	stateSave: true,
	stateDuration : -1,
	stateLoadCallback: function (settings, callback) {
       	$.ajax( {
           	url: '/core/update_user_settings.php?set=listIFRA&action=load',
           	dataType: 'json',
           	success: function (json) {
               	callback( json );
           	}
       	});
    },
    stateSaveCallback: function (settings, data) {
	   $.ajax({
		 url: "/core/update_user_settings.php?set=listIFRA&action=save",
		 data: data,
		 dataType: "json",
		 type: "POST"
	  });
	},
	
	});

	tdDataIFRA.on('requestChild.dt', function (e, row) {
		row.child(format(row.data())).show();
	});
	 
	tdDataIFRA.on('click', '#ifra_name', function (e) {
		let tr = e.target.closest('tr');
		let row = tdDataIFRA.row(tr); 
		if (row.child.isShown()) {
			row.child.hide();
		} else {
			row.child(format(row.data())).show();
		}
	});
	
	document.querySelectorAll('a.toggle-vis').forEach((el) => {
		el.addEventListener('click', function (e) {
			e.preventDefault();
	 
			let columnIdx = e.target.getAttribute('data-column');
			let column = tdDataIFRA.column(columnIdx);
	 
			// Toggle the visibility
			column.visible(!column.visible());
		});
	});
	
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
	return '<i class="pv_point_gen pv_gen_li" id="ifra_name">'+row.name+'</i>';
}

function CAS(data, type, row){
	data = '<a href="#" data-name="cas" class="cas" data-type="text" data-pk="' + row.id + '">' + data + '</a>';
	return data;
}

function image(data, type, row){
	return '<img src="data:image/png;base64, '+row.image+'" class="img_ifra"/>';
}

function reload_ifra_data() {
    $('#tdDataIFRA').DataTable().ajax.reload(null, true);
}

function actions(data, type, row){
	data = '<a class="pv_point_gen text-danger" id="dDel" data-name="'+ row.name +'" data-id='+ row.id +'><i class="fas fa-trash mx-2"></i></a>';
	
	return data;
}


$('#exportCSV').click(() => {
    $('#tdDataIFRA').DataTable().button(0).trigger();
});

$('#btnImportIFRA').click(function() {	
	$("#IFRAImportMsg").html('<div class="alert alert-info"><img src="/img/loading.gif" class="mx-2"/>Please wait, file upload in progress....</div>');
	$("#btnImportIFRA").prop("disabled", true);
	$("#btnIFRAC").prop("disabled", true);

	
	var fd = new FormData();
    var files = $('#ifraXLS')[0].files;
    var modify = $('#updateCAS').prop("checked");
    var overwrite = $('#overwrite').prop("checked");
    var IFRAver = $('#IFRAver').val();

       if(files.length > 0 ){
        fd.append('ifraXLS',files[0]);
        $.ajax({
           url: '/pages/upload.php?type=IFRA&updateCAS=' + modify + '&overwrite='+ overwrite + '&IFRAVer=' + IFRAver,
           type: 'POST',
           data: fd,
           contentType: false,
           processData: false,
		         cache: false,
		   dataType: 'json',
           success: function(response){
             if(response.success){
				 $("#IFRAImportMsg").html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.success+'</div>');
				// $("#btnImportIFRA").hide();
				 $("#btnIFRAC").html('Close');
				 $("#btnImportIFRA").prop("disabled", false);
				 $("#btnIFRAC").prop("disabled", false);
				 reload_ifra_data();
              }else{
                $("#IFRAImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.error+'</div>');
				$("#btnImportIFRA").prop("disabled", false);
				$("#btnIFRAC").prop("disabled", false);
              }
            },
         });
  }else{
	$("#IFRAImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
	$("#btnImportIFRA").prop("disabled", false);
  }	
});

$('#Importpb').click(function() {	
	$("#pbmportMsg").html('<div class="alert alert-info"><img src="/img/loading.gif" class="mx-2"/>Please wait, this may take a few minutes, depending your IFRA library size and your internet connection...</div>');
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
				$('#pbmportMsg').html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>');
				$("#Importpb").hide();
				$("#ImportpbC").show();
				$('#ImportpbC').html('Close');
				$("#ImportpC").show();
				reload_ifra_data();
			}else{
				$('#pbmportMsg').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>');
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

$('#tdDataIFRA').on('click', '[id*=dDel]', function () {
	var d = {};
	d.ID = $(this).attr('data-id');
    d.Name = $(this).attr('data-name');

	bootbox.dialog({
       title: "Confirm deletion",
       message : 'Delete IFRA entry <strong>'+ d.Name +'</strong> ?',
       buttons :{
           main: {
               label : "Delete",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: '/pages/update_data.php', 
					type: 'POST',
					data: {
						IFRA: 'delete',
						ID: d.ID,
						type: 'IFRA'
						},
					dataType: 'json',
					success: function (data) {
						if(data.success){
							msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
							reload_ifra_data();
						}else if(data.error){
							msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
						}
						$('#iframsg').html(msg);
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

$('#tdDataIFRA').editable({
  container: 'body',
  selector: 'a.cas',
  url: "/pages/update_data.php?IFRA=edit&type=CAS",
  title: 'CAS#',
  ajaxOptions: { 
  	dataType: 'json'
  },
  success: function(response, newValue) {
	if(response.error){
		return response.error; 
	}else{ 
		reload_ifra_data();
	}
  },
  validate: function(value){
   if($.trim(value) == ''){
	return 'This field is required';
   }
  }
});
</script>
<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>
<script src="/js/import.IFRA.js"></script>

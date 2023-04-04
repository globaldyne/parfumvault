<?php 

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

?>

<table width="100%" border="0" class="table table-sm">
              <div id="catMsg"></div>
              <tr>
                <td width="4%"><p>Category:</p></td>
                <td width="12%"><input type="text" name="category" id="category" class="form-control"/></td>
                <td width="1%">&nbsp;</td>
                <td width="6%">Description:</td>
                <td width="13%"><input type="text" name="cat_notes" id="cat_notes" class="form-control"/></td>
                <td width="2%">&nbsp;</td>
                <td width="22%"><input type="submit" name="add-category" id="add-category" value="Add" class="btn btn-info" /></td>
                <td width="40%">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="8">
                <div class="card-body">
              <div>
				<table id="tdDataCat" class="table table-striped table-bordered nowrap viewFormula" style="width:100%">
                  <thead>
                    <tr>
                      <th>Image</th>
                      <th>Colour Key</th>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
                </td>
              </tr>
            </table>
<script type="text/javascript" language="javascript" >
$(document).ready(function() {
		var tdDataCat = $('#tdDataCat').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
        ],
		dom: 'lfrtip',
		processing: true,
        language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: "No groups yet.",
			search: "Search:"
			},
    	ajax: {	url: '/core/list_ingCat_data.php' },
		 columns: [
				   { data : 'image', title: 'Image', render: ciImage },
    			   { data : 'colorKey', title: 'Colour Key', render: ciKey},
				   { data : 'name', title: 'Name', render: ciName},
				   { data : 'notes', title: 'Description', render: ciNotes},
   				   { data : null, title: 'Actions', render: ciActions},		   
				  ],
        order: [[ 2, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
        pageLength: 20,
		displayLength: 20,
			drawCallback: function( settings ) {
			extrasShow();
    	},
	});
});

function ciImage(data, type, row){
	if(row.image){
		var cimg = '<img src="' + row.image + '" class="img_ing">';
	}else{
		var cimg = '<img src="img/molecule.png" class="img_ing">';
	}
	return '<a href="pages/editCat.php?id='+row.id+'" class="popup-link">' + cimg + '</a>';    
}
function ciKey(data, type, row){
	return '<a href="#" class="colorKey" style="background-color: rgb('+row.colorKey+')" id="colorKey" data-name="colorKey" data-type="select" data-pk="'+row.id+'" data-title="Choose Colour Key for '+row.name+'"></a>';    
}

function ciName(data, type, row){
	return '<a class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</a>';    
}

function ciNotes(data, type, row){
	return '<a class="notes pv_point_gen" data-name="notes" data-type="textarea" data-pk="'+row.id+'">'+row.notes+'</a>';    
}

function ciActions(data, type, row){
	return '<i id="catDel" class="pv_point_gen fas fa-trash" style="color: #c9302c;" data-id="'+row.id+'" data-name="'+row.name+'"></i>';    
}

$('#add-category').click(function() {
$.ajax({ 
	url: 'pages/update_settings.php', 
		type: 'POST',
		data: {
			manage: 'category',
			
			category: $("#category").val(),
			cat_notes: $("#cat_notes").val(),
			
			},
		dataType: 'html',
		success: function (data) {
			$('#catMsg').html(data);
			reload_cat_data();
		}
	});
});



$('#tdDataCat').editable({
  container: 'body',
  selector: 'a.name',
  url: "pages/update_data.php?settings=cat",
  title: 'Category',
  type: "POST",
  dataType: 'json',
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
});
 
$('#tdDataCat').editable({
  container: 'body',
  selector: 'a.notes',
  url: "pages/update_data.php?settings=cat",
  title: 'Description',
  type: "POST",
  dataType: 'json',
});

//Change colorKey
$('#tdDataCat').editable({
	pvnoresp: false,
	highlight: false,
	selector: 'a.colorKey',
	type: "POST",
	emptytext: "",
	emptyclass: "",
  	url: "pages/update_data.php?settings=cat",
    source: [
			 <?php
				$getCK = mysqli_query($conn, "SELECT name,rgb FROM colorKey ORDER BY name ASC");
				while ($r = mysqli_fetch_array($getCK)){
				echo '{value: "'.$r['rgb'].'", text: "'.$r['name'].'", ck: "color: rgb('.$r['rgb'].')"},';
			}
			?>
          ],
	dataType: 'html',
	success: function () {
		reload_cat_data();
	}
});

	
$('#tdDataCat').on('click', '[id*=catDel]', function () {
	var cat = {};
	cat.ID = $(this).attr('data-id');
	cat.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm category deletion",
       message : 'Delete <strong>'+ $(this).attr('data-name') +'</strong> category?',
       buttons :{
           main: {
               label : "Delete",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: 'pages/update_settings.php', 
					type: 'POST',
					data: {
						action: "delete",
						catId: cat.ID,
						},
					dataType: 'html',
					success: function (data) {
						$('#catMsg').html(data);
						reload_cat_data();
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

function reload_cat_data() {
    $('#tdDataCat').DataTable().ajax.reload(null, true);
};

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

</script>

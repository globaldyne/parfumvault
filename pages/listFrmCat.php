<?php 

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

?>

<table width="100%" border="0" class="table table-striped table-sm">
              <div id="fcatMsg"></div>
              <tr>
                <td width="4%"><p>Category:</p></td>
                <td width="12%"><input type="text" name="fcatName" id="fcatName" class="form-control"/></td>
                <td width="1%">&nbsp;</td>
                <td width="6%">Type:</td>
                <td width="13%"><select name="cat_type" id="cat_type" class="form-control">
                  <option value="profile">Profile</option>
                  <option value="sex">Sex</option>
                </select></td>
                <td width="2%">&nbsp;</td>
                <td width="22%"><input type="submit" name="add-fcat" id="add-fcat" value="Add" class="btn btn-info" /></td>
                <td width="40%">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="8">
                <div class="card-body">
              <div>
				<table id="frmDataCat" class="table table-striped table-bordered nowrap viewFormula" style="width:100%">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Type</th>
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
		var frmDataCat = $('#frmDataCat').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
        ],
		dom: 'lfrtip',
		processing: true,
        language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: 'No groups yet.',
			search: 'Search:'
			},
    	ajax: {	url: 'core/list_frmCat_data.php' },
		columns: [
				  { data : 'name', title: 'Name', render: cName },
    			  { data : 'type', title: 'Type', render: cType},
   				  { data : null, title: 'Actions', render: cActions},		   
				 ],
        order: [[ 1, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
        pageLength: 20,
		displayLength: 20,		
	});
});



function cName(data, type, row){
	return '<a class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</a>';    
}

function cType(data, type, row){
	return '<a class="type pv_point_gen" data-name="type" data-type="select" data-pk="'+row.id+'">'+row.type+'</a>';    
}

function cActions(data, type, row){
	return '<a href="#" id="catDel" class="fas fa-trash" data-id="'+row.id+'" data-name="'+row.name+'"></a>';    
}

$('#add-fcat').click(function() {
$.ajax({ 
	url: 'pages/update_settings.php', 
		type: 'POST',
		data: {
			manage: 'add_frmcategory',
			
			category: $("#fcatName").val(),
			cat_type: $("#cat_type").val(),
			
			},
		dataType: 'html',
		success: function (data) {
			$('#fcatMsg').html(data);
			reload_fcat_data();
		}
	});
});



$('#frmDataCat').editable({
  container: 'body',
  selector: 'a.name',
  url: "pages/update_data.php?settings=fcat",
  title: 'Category name',
  type: "POST",
  dataType: 'json',
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
});
 

//Change type
$('#frmDataCat').editable({
	pvnoresp: false,
	highlight: false,
	title: 'Category type',
	selector: 'a.type',
	type: "POST",
	emptytext: "",
	emptyclass: "",
  	url: "pages/update_data.php?settings=fcat",
    source: [
			 <?php
				$getCK = mysqli_query($conn, "SELECT type FROM formulaCategories GROUP BY type");
				while ($r = mysqli_fetch_array($getCK)){
			 ?>
				{value: "<?=$r['type']?>", text: "<?=$r['type']?>"},
			<?php } ?>
          ],
	dataType: 'html',
	success: function () {
		reload_fcat_data();
	}
});

	
$('#frmDataCat').on('click', '[id*=catDel]', function () {
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
						action: "del_frmcategory",
						catId: cat.ID,
						},
					dataType: 'html',
					success: function (data) {
						$('#fcatMsg').html(data);
						reload_fcat_data();
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

function reload_fcat_data() {
    $('#frmDataCat').DataTable().ajax.reload(null, true);
};


</script>

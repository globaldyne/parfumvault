<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$q = mysqli_query($conn, "SELECT * FROM ingSuppliers ORDER BY name ASC");

?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=suppliers">Suppliers</a></h2>
            </div>
            <div id="errMsg"></div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder noexport">
                      <th colspan="3">
                  <div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addSupplier">Add new</a>
                            <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>Description</th>
                      <th class="noexport">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="supplier_data">
                    <?php while ($supplier = mysqli_fetch_array($q)) {?>
                    <tr>
                      <td data-name="name" class="name" data-type="text" align="center" data-pk="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></td>
					  <td data-name="notes" class="notes" data-type="text" align="center" data-pk="<?php echo $supplier['id']; ?>"><?php echo $supplier['notes']; ?></td>
					  <td class="noexport" align="center"><a href="javascript:deleteSupplier('<?php echo $supplier['id']; ?>')" onclick="return confirm('Delete <?php echo $supplier['name']; ?>?')" class="fas fa-trash"></a></td>
					  </tr>
				  <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
 
<!-- ADD NEW-->
<div class="modal fade" id="addSupplier" tabindex="-1" role="dialog" aria-labelledby="addSupplier" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSupplier">Add supplier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="inf"></div>
          <form action="javascript:addSupplier()" method="get" name="form1" target="_self" id="form1">
            Name: 
            <input class="form-control" name="name" type="text" id="name" />
            <p>
            Description: 
            <input class="form-control" name="description" type="text" id="description" />            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="button" value="Add">
      </div>
     </form>
    </div>
  </div>
</div>
</div>
<script type="text/javascript" language="javascript" >

$('#supplier_data').editable({
  container: 'body',
  selector: 'td.name',
  url: "pages/update_data.php?settings=sup",
  title: 'Supplier',
  type: "POST",
  dataType: 'json',
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
});
 
$('#supplier_data').editable({
  container: 'body',
  selector: 'td.notes',
  url: "pages/update_data.php?settings=sup",
  title: 'Description',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
});

function deleteSupplier(ID) {	  
$.ajax({ 
    url: 'pages/update_data.php', 
	type: 'GET',
    data: {
		supp: 'delete',
		ID: ID,
		},
	dataType: 'html',
    success: function (data) {
	  	$('#errMsg').html(data);
		location.reload();
    }
  });
};

function addSupplier() {	  
$.ajax({ 
    url: 'pages/update_data.php', 
	type: 'GET',
    data: {
		supp: 'add',
		name: $("#name").val(),
		description: $("#description").val()
		},
	dataType: 'html',
    success: function (data) {
	  	$('#inf').html(data);
     	$("#name").val('');
     	$("#description").val('');
		//location.reload();
    }
  });
};
//Export
$('#csv').on('click',function(){
  $("#tdData").tableHTMLExport({
	type:'csv',
	filename:'suppliers.csv',
	separator: ',',
  	newline: '\r\n',
  	trimContent: true,
  	quoteFields: true,
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',	
	htmlContent: false,
  	// debug
  	consoleLog: false   
	});
})

</script>
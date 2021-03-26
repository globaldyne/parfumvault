<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$q = mysqli_query($conn, "SELECT * FROM customers ORDER BY name ASC");

?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=customers">Customers</a></h2>
            </div>
            <div id="errMsg"></div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder noexport">
                      <th colspan="5">
                  <div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addCustomer">Add new</a>
                            <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>Address</th>
                      <th>Email</th>
                      <th>Web Site</th>
                      <th class="noexport">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="customer_data">
                    <?php while ($customer = mysqli_fetch_array($q)) {?>
                    <tr>
                      <td data-name="name" class="name" data-type="text" align="center" data-pk="<?php echo $customer['id']; ?>"><?php echo $customer['name']; ?></td>
					  <td align="center" class="address" data-name="address" data-type="text" data-pk="<?php echo $customer['id']; ?>"><?php echo $customer['address']; ?></td>
					  <td align="center" class="email" data-name="email" data-type="text" data-pk="<?php echo $customer['id']; ?>"><?php echo $customer['email']; ?></td>
					  <td align="center" class="web" data-name="web" data-type="text" data-pk="<?php echo $customer['id']; ?>"><?php echo $customer['web']; ?></td>
					  <td class="noexport" align="center"><a href="javascript:deleteCustomer('<?php echo $customer['id']; ?>')" onclick="return confirm('Delete <?php echo $customer['name']; ?>?')" class="fas fa-trash"></a></td>
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
<div class="modal fade" id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="addCustomer" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCustomer">Add customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="inf"></div>
          <form action="javascript:addCustomer()" method="get" name="form1" target="_self" id="form1">
            Name: 
            <input class="form-control" name="name" type="text" id="name" />
            <p>
            Address: 
              <input class="form-control" name="address" type="text" id="addrss" />  
            <p>
            Email: 
              <input class="form-control" name="email" type="text" id="email" />           
            <p>
            Web Site: 
              <input class="form-control" name="web" type="text" id="web" /> 
              
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

$('#customer_data').editable({
  container: 'body',
  selector: 'td.name',
  url: "pages/update_data.php?customer=update",
  title: 'Customer',
  type: "POST",
  dataType: 'json',
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
});
 
$('#customer_data').editable({
  container: 'body',
  selector: 'td.address',
  url: "pages/update_data.php?customer=update",
  title: 'Address',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
});

$('#customer_data').editable({
  container: 'body',
  selector: 'td.email',
  url: "pages/update_data.php?customer=update",
  title: 'Email Address',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
});

$('#customer_data').editable({
  container: 'body',
  selector: 'td.web',
  url: "pages/update_data.php?customer=update",
  title: 'Web address',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
});

function deleteCustomer(ID) {	  
	$.ajax({ 
		url: 'pages/update_data.php', 
		type: 'GET',
		data: {
			customer: 'delete',
			customer_id: ID,
			},
		dataType: 'html',
		success: function (data) {
			$('#errMsg').html(data);
			location.reload();
		}
	  });
};

function addCustomer() {	  
	$.ajax({ 
		url: 'pages/update_data.php', 
		type: 'POST',
		data: {
			customer: 'add',
			name: $("#name").val(),
			address: $("#address").val(),
			web: $("#web").val(),
			email: $("#email").val()
			},
		dataType: 'html',
		success: function (data) {
			$('#inf').html(data);
			$("#name").val('');
			$("#address").val('');
			$("#web").val('');
			$("#email").val('');
		}
	  });
};
//Export
$('#csv').on('click',function(){
  $("#tdData").tableHTMLExport({
	type:'csv',
	filename:'customers.csv',
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
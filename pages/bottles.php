<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$id = mysqli_real_escape_string($conn, $_GET['id']);

if($_GET['action'] == 'delete' && $_GET['id']){
	$bottle = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM bottles WHERE id = '$id'"));
	
	if(mysqli_query($conn, "DELETE FROM bottles WHERE id = '$id'")){
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="/?do=bottles" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Bottle <strong>'.$bottle['name'].'</strong> removed!
		</div>';
	}
	
}
$q = mysqli_query($conn, "SELECT * FROM bottles ORDER BY name ASC");
$sup = mysqli_query($conn, "SELECT * FROM ingSuppliers ORDER BY name ASC");

?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="/?do=bottles">Bottles</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder noexport">
                      <th colspan="12">
                  <div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="/?do=addBottle">Add new</a>
                            <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>Size (ml)</th>
                      <th>Price <?php echo $settings['currency'];?></th>
                      <th>Supplier</th>
                      <th colspan="3">Dimensions HxWxD (mm)</th>
                      <th>Photo</th>
                      <th>Shop</th>
                      <th>Notes</th>
                      <th class="noexport">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="bottle_data">
                    <?php					
				  while ($bottle = mysqli_fetch_array($q)) {
					  echo'
                    <tr>
                      <td data-name="name" class="name" data-type="text" align="center" data-pk="'.$bottle['id'].'">'.$bottle['name'].'</td>
					  <td data-name="ml" class="ml" data-type="text" align="center" data-pk="'.$bottle['id'].'">'.$bottle['ml'].'</td>
					  <td data-name="price" class="price" data-type="text" align="center" data-pk="'.$bottle['id'].'">'.$bottle['price'].'</td>
                      <td data-name="supplier" class="supplier" data-type="select" align="center" data-pk="'.$bottle['id'].'">'.$bottle['supplier'].'</td>';
					  if(empty($bottle['height'])){ $bottle['height'] = 'N/A';} if(empty($bottle['width'])){ $bottle['width']='N/A';} if(empty($bottle['diameter'])){ $bottle['diameter']='N/A';}?>
                      <?php echo'<td data-name="height" class="height" data-type="text" align="center" data-pk="'.$bottle['id'].'">'.$bottle['height'].'</td>
                  				 <td data-name="width" class="width" data-type="text" align="center" data-pk="'.$bottle['id'].'">'.$bottle['width'].'</td>
                  				 <td data-name="diameter" class="diameter" data-type="text" align="center" data-pk="'.$bottle['id'].'">'.$bottle['diameter'].'</td>
					  <td align="center">';
					  if(empty($bottle['photo'])){ echo 'N/A'; }else{
                      echo '<a href="'.$bottle['photo'].'" class="popup-link fas fa-image"></a>';}
					  echo '<td align="center"><a href="'.$bottle['supplier_link'].'" target="_blank" class="fas fa-external-link-alt"></a></td>';
					  echo '<td data-name="notes" class="notes" data-type="textarea" align="center" data-pk="'.$bottle['id'].'">'.$bottle['notes'].'
					  <td class="noexport" align="center"><a href="/?do=bottles&action=delete&id='.$bottle['id'].'" onclick="return confirm(\'Delete '.$bottle['name'].'?\');" class="fas fa-trash"></a></td>
					  </tr>';
				  }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
<script type="text/javascript" language="javascript" >

 
  $('#bottle_data').editable({
  container: 'body',
  selector: 'td.name',
  url: "/pages/update_data.php?bottle=1",
  title: 'Name',
  type: "POST",
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; else location.reload();
    },
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
 });

  $('#bottle_data').editable({
  container: 'body',
  selector: 'td.ml',
  url: "/pages/update_data.php?bottle=1",
  title: 'ml',
  type: "POST",
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; else location.reload();
    },
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
   if($.isNumeric(value) == '' ){
    return 'Numbers only!';
   }
  }
 });

  $('#bottle_data').editable({
  container: 'body',
  selector: 'td.height',
  url: "/pages/update_data.php?bottle=1",
  title: 'Height (mm)',
  type: "POST",
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; else location.reload();
    },
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
   if($.isNumeric(value) == '' ){
    return 'Numbers only!';
   }
  }
 });
  
   $('#bottle_data').editable({
  container: 'body',
  selector: 'td.width',
  url: "/pages/update_data.php?bottle=1",
  title: 'Width (mm)',
  type: "POST",
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; else location.reload();
    },
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
   if($.isNumeric(value) == '' ){
    return 'Numbers only!';
   }
  }
 });
   
   $('#bottle_data').editable({
  container: 'body',
  selector: 'td.diameter',
  url: "/pages/update_data.php?bottle=1",
  title: 'Diameter (mm)',
  type: "POST",
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; else location.reload();
    },
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
   if($.isNumeric(value) == '' ){
    return 'Numbers only!';
   }
  }
 });  
 
  $('#bottle_data').editable({
  container: 'body',
  selector: 'td.price',
  url: "/pages/update_data.php?bottle=1",
  title: 'Price',
  type: "POST",
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; else location.reload();
    },
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
 });
 
  $('#bottle_data').editable({
  container: 'body',
  selector: 'td.notes',
  url: "/pages/update_data.php?bottle=1",
  title: 'Notes',
  type: "POST",
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; else location.reload();
    },
 });

  $('#bottle_data').editable({
	container: 'body',
  	selector: 'td.supplier',
  	title: 'Supplier',
  	url: "/pages/update_data.php?bottle=1",
    source: [<?php while($supplier = mysqli_fetch_array($sup)){?>
             {value: '<?php echo $supplier ['name'];?>', text: '<?php echo $supplier ['name'];?>'},
            <?php } ?>
          ]
    });
  
//Export
$('#csv').on('click',function(){
  $("#tdData").tableHTMLExport({
	type:'csv',
	filename:'bottles.csv',
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

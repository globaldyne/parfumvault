<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$id = mysqli_real_escape_string($conn, $_GET['id']);

if($_GET['action'] == 'delete' && $_GET['id']){
	$bottle = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM bottles WHERE id = '$id'"));
	
	if(mysqli_query($conn, "DELETE FROM bottles WHERE id = '$id'")){
		$msg = '<div class="alert alert-success alert-dismissible"><a href="?do=bottles" class="close" data-dismiss="alert" aria-label="close">x</a>Bottle <strong>'.$bottle['name'].'</strong> removed!</div>';
	}
	
}
$q = mysqli_query($conn, "SELECT * FROM bottles ORDER BY name ASC");
$sup = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY name ASC");
while ($suppliers = mysqli_fetch_array($sup)){
	    $supplier[] = $suppliers;
}
?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=bottles">Bottles</a></h2>
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
            				<a class="dropdown-item" href="#" data-toggle="modal" data-target="#addBottle">Add new</a>
                            <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>Size (ml)</th>
                      <th>Price (<?php echo $settings['currency'];?>)</th>
                      <th>Supplier</th>
                      <th colspan="3">Dimensions HxWxD (mm)</th>
                      <th>Photo</th>
                      <th>Shop</th>
                      <th>Notes</th>
                      <th class="noexport">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="bottle_data">
                    <?php while ($bottle = mysqli_fetch_array($q)) { ?>
                    <tr>
                      <td data-name="name" class="name" data-type="text" align="center" data-pk="<?=$bottle['id']?>"><?=$bottle['name']?></td>
					  <td data-name="ml" class="ml" data-type="text" align="center" data-pk="<?=$bottle['id']?>"><?=$bottle['ml']?></td>
					  <td data-name="price" class="price" data-type="text" align="center" data-pk="<?=$bottle['id']?>"><?=$bottle['price']?></td>
                      <td data-name="supplier" class="supplier" data-type="select" align="center" data-pk="<?=$bottle['id']?>"><?=$bottle['supplier']?></td>
                      <td data-name="height" class="height" data-type="text" align="center" data-pk="<?=$bottle['id']?>"><?=$bottle['height']?:'N/A';?></td>
                  	  <td data-name="width" class="width" data-type="text" align="center" data-pk="<?=$bottle['id']?>"><?=$bottle['width']?:'N/A';?></td>
                  	  <td data-name="diameter" class="diameter" data-type="text" align="center" data-pk="<?=$bottle['id']?>"><?=$bottle['diameter']?:'N/A';?></td>
					  <td align="center">
					  <?php if(empty($bottle['photo'])){ echo 'N/A'; }else{ ?>
                      	<a href="<?=$bottle['photo']?>" class="popup-link fas fa-image"></a>
					  <?php } ?>
					  <td align="center"><a href="<?=$bottle['supplier_link']?>" target="_blank" class="fas fa-external-link-alt"></a></td>
					  <td data-name="notes" class="notes" data-type="textarea" align="center" data-pk="<?=$bottle['id']?>"><?=$bottle['notes']?></td>
					  <td class="noexport" align="center"><a href="pages/editBottle.php?id=<?=$bottle['id']?>" class="fas fa-edit popup-link"><a> <a href="javascript:btlDel('<?=$bottle['id']?>')" onclick="return confirm('Delete <?=$bottle['name']?>?');" class="fas fa-trash"></a></td>
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

<!-- ADD BOTTLE MODAL-->
<div class="modal fade" id="addBottle" tabindex="-1" role="dialog" aria-labelledby="addBottle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Bottle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="bottle_inf"></div>
        <p>
        Name: 
          <input class="form-control" name="name" type="text" id="name" />
        </p>
        <p>            
        Size (ml):
          <input class="form-control" name="size" type="text" id="size" />
        </p>
        <p>
        Price:
          <input class="form-control" name="price" type="text" id="price" />
        </p>
 		<p>
        Height:
          <input class="form-control" name="height" type="text" id="height" />
        </p>
        <p>
        Width:
          <input class="form-control" name="width" type="text" id="width" />
        </p>
        <p>
        Diameter:
          <input class="form-control" name="diameter" type="text" id="diameter" />
        </p>
        <p>
        Notes:
          <input class="form-control" name="notes" type="text" id="notes" />
        </p>
        <p>
        Supplier:
          <select name="supplier" id="supplier" class="form-control">
            <option value="" selected></option>
            <?php
            foreach($supplier as $sup) {
                echo '<option value="'.$sup['name'].'">'.$sup['name'].'</option>';
            }
            ?>
          </select>
        </p>
        <p>
        Supplier URL:
          <input class="form-control" name="supplier_link" type="text" id="supplier_link" />
        </p>
        <p>
        Image:
        <input type="file" name="pic" id="pic" class="form-control" />
    	</p>            
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="bottle_add" value="Add">
      </div>
    </div>
  </div>
</div>


<script type="text/javascript"> 
function btlDel(btlId){
	$.ajax({ 
		url: 'pages/update_settings.php', 
		type: 'POST',
		data: {
			action: 'delete',
			btlId: btlId,
		},
		dataType: 'html',
		success: function (data) {
			 location.reload();
		}
	});
};

$('#bottle_data').editable({
  container: 'body',
  selector: 'td.name',
  url: "pages/update_data.php?bottle=1",
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
  url: "pages/update_data.php?bottle=1",
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
  url: "pages/update_data.php?bottle=1",
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
  url: "pages/update_data.php?bottle=1",
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
  url: "pages/update_data.php?bottle=1",
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
  url: "pages/update_data.php?bottle=1",
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
	url: "pages/update_data.php?bottle=1",
	title: 'Notes',
	type: "POST",
	dataType: 'json',
	  success: function(response, newValue) {
		if(response.status == 'error') return response.msg; else location.reload();
	}
});

$('#bottle_data').editable({
	container: 'body',
	selector: 'td.supplier',
	title: 'Supplier',
	url: "pages/update_data.php?bottle=1",
    source: [<?php foreach($supplier as $sup){?>
             {value: '<?php echo $sup['name'];?>', text: '<?php echo $sup['name'];?>'},
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
});

$('#addBottle').on('click', '[id*=bottle_add]', function () {

	$("#bottle_inf").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	$("#bottle_add").prop("disabled", true);
    $("#bottle_add").prop('value', 'Please wait...');
		
	var fd = new FormData();
    var files = $('#pic')[0].files;
    var name = $('#name').val();
    var size = $('#size').val();
    var price = $('#price').val();
    var supplier = $('#supplier').val();
    var supplier_link = $('#supplier_link').val();

    var height = $('#height').val();
    var width = $('#width').val();
    var diameter = $('#diameter').val();
    var notes = $('#notes').val();

    if(files.length > 0 ){
		fd.append('pic_file',files[0]);

			$.ajax({
              url: 'pages/upload.php?type=bottle&name=' + btoa(name) + '&size=' + size + '&price=' + price + '&supplier=' + btoa(supplier) + '&supplier_link=' + btoa(supplier_link)+ '&height=' + height + '&width=' + width + '&diameter=' + diameter + '&notes=' + btoa(notes),
              type: 'POST',
              data: fd,
              contentType: false,
              processData: false,
			  		cache: false,
              success: function(response){
                 if(response != 0){
                    $("#bottle_inf").html(response);
					$("#bottle_add").prop("disabled", false);
        			$("#bottle_add").prop("value", "Add");
					//reload_data();
                 }else{
                    $("#bottle_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
					$("#bottle_add").prop("disabled", false);
        			$("#bottle_add").prop("value", 'Add');
                 }
              },
           });
        }else{
			$("#bottle_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a image to upload!</div>');
			$("#bottle_add").prop("disabled", false);
   			$("#bottle_add").prop("value", "Add");
        }
		
});

</script>

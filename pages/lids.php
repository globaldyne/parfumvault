<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$q = mysqli_query($conn, "SELECT * FROM lids ORDER BY style ASC");
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
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=lids">Bottle Lids</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder noexport">
                      <th colspan="8">
                  <div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
            				<a class="dropdown-item" href="#" data-toggle="modal" data-target="#addLid">Add new</a>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Style</th>
                      <th>Colour</th>
                      <th>Price (<?php echo $settings['currency'];?>)</th>
                      <th>Supplier</th>
                      <th>Photo</th>
                      <th>Supplier Link</th>
                      <th class="noexport">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="lid_data">
                    <?php  while ($lid = mysqli_fetch_array($q)){ ?>
                    <tr>
                      <td data-name="style" class="style" data-type="text" align="center" data-pk="<?php echo $lid['id'];?>"><?php echo $lid['style'];?></td>
					  <td data-name="colour" class="colour" data-type="text" align="center" data-pk="<?php echo $lid['id'];?>"><?php echo $lid['colour'];?></td>
					  <td data-name="price" class="price" data-type="text" align="center" data-pk="<?php echo $lid['id'];?>"><?php echo $lid['price'];?></td>
                      <td data-name="supplier" class="supplier" data-type="select" align="center" data-pk="<?php echo $lid['id'];?>"><?php echo $lid['supplier'];?></td>
                      <td align="center"><?php if(empty($lid['photo'])){ echo 'N/A'; }else{?><a href="<?php echo $lid['photo'];?>" class="popup-link fas fa-image"></a><?php } ?></td>
                      <td align="center"><a href="<?php echo $lid['supplier_link'];?>" target="_blank" class="fas fa-external-link-alt"></a></td>
					  <td class="noexport" align="center"><a href="pages/editLid.php?id=<?php echo $lid['id'];?>" class="fas fa-edit popup-link"></a> <a href="javascript:lidDel('<?php echo $lid['id'];?>')" onclick="return confirm('Delete <?php echo $lid['style'];?>?')" class="fas fa-trash"></a></td>
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
<!-- ADD LID MODAL-->
<div class="modal fade" id="addLid" tabindex="-1" role="dialog" aria-labelledby="addLid" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Lid</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="lid_inf"></div>
        <p>
        Style: 
          <input class="form-control" name="style" type="text" id="style" />
        </p>
        <p>            
        Colour:
          <input class="form-control" name="color" type="text" id="color" />
        </p>
        <p>
        Price:
          <input class="form-control" name="price" type="text" id="price" />
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
        <input type="submit" name="button" class="btn btn-primary" id="lid_add" value="Add">
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
function lidDel(lidId){
	$.ajax({ 
		url: 'pages/update_settings.php', 
		type: 'POST',
		data: {
			action: 'delete',
			lidId: lidId,
		},
		dataType: 'html',
		success: function (data) {
			 location.reload();
		}
	});
};

$('#lid_data').editable({
  container: 'body',
  selector: 'td.style',
  url: "pages/update_data.php?lid=1",
  title: 'Style',
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

$('#lid_data').editable({
  container: 'body',
  selector: 'td.colour',
  url: "pages/update_data.php?lid=1",
  title: 'Colour',
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

$('#lid_data').editable({
  container: 'body',
  selector: 'td.price',
  url: "pages/update_data.php?lid=1",
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
   if($.isNumeric(value) == '' ){
    return 'Numbers only!';
   }
  }
});
  
 
$('#lid_data').editable({
	container: 'body',
  	selector: 'td.supplier',
  	title: 'Supplier',
  	url: "pages/update_data.php?lid=1",
    source: [<?php foreach($supplier as $sup){?>
             {value: '<?php echo $sup['name'];?>', text: '<?php echo $sup['name'];?>'},
            <?php } ?>
          ]
});

$('#addLid').on('click', '[id*=lid_add]', function () {

	$("#lid_inf").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	$("#lid_add").prop("disabled", true);
    $("#lid_add").prop('value', 'Please wait...');
		
	var fd = new FormData();
    var files = $('#pic')[0].files;
    var style = $('#style').val();
    var color = $('#color').val();
    var price = $('#price').val();
    var supplier = $('#supplier').val();
    var supplier_link = $('#supplier_link').val();

    if(files.length > 0 ){
		fd.append('pic_file',files[0]);

			$.ajax({
              url: 'pages/upload.php?type=lid&style=' + btoa(style) + '&color=' + btoa(color) + '&price=' + btoa(price) + '&supplier=' + btoa(supplier) + '&supplier_link=' + btoa(supplier_link),
              type: 'POST',
              data: fd,
              contentType: false,
              processData: false,
			  		cache: false,
              success: function(response){
                 if(response != 0){
                    $("#lid_inf").html(response);
					$("#lid_add").prop("disabled", false);
        			$("#lid_add").prop("value", "Add");
					//reload_data();
                 }else{
                    $("#lid_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
					$("#lid_add").prop("disabled", false);
        			$("#lid_add").prop("value", 'Add');
                 }
              },
           });
        }else{
			$("#lid_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a image to upload!</div>');
			$("#lid_add").prop("disabled", false);
   			$("#lid_add").prop("value", "Add");
        }
		
});

</script>

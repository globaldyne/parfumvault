<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$q = mysqli_query($conn, "SELECT * FROM lids ORDER BY style ASC");
$sup = mysqli_query($conn, "SELECT * FROM ingSuppliers ORDER BY name ASC");

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
                            <a class="dropdown-item" href="?do=addLid">Add new</a>
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
    
<script type="text/javascript" language="javascript" >
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
    source: [<?php while($supplier = mysqli_fetch_array($sup)){?>
             {value: '<?php echo $supplier ['name'];?>', text: '<?php echo $supplier ['name'];?>'},
            <?php } ?>
          ]
});

</script>

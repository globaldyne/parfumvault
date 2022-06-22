<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require(__ROOT__.'/inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');


$lid = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM lids WHERE id = '".$_GET['id']."'")); 
$doc = mysqli_fetch_array(mysqli_query($conn,"SELECT docData AS photo FROM documents WHERE ownerID = '".$lid['id']."' AND type = '5'"));
$sup = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY name ASC");
while ($suppliers = mysqli_fetch_array($sup)){
	    $supplier[] = $suppliers;
}
?>
<script src="/js/jquery/jquery.min.js"></script>

<link href="/css/sb-admin-2.css" rel="stylesheet">
<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/vault.css" rel="stylesheet">

<style>

.container {
  max-width: 100%;
  width: 100%;

}
</style>
<div class="container">
    <h1 class="text-primary"><?=$lid['style']?></h1>
      <hr>
       <div class="text-center">
          <div id="lid_pic"><div class="loader"></div></div>
       </div>
       <div id="lid_inf"></div>
        <p>
        Style: 
          <input name="style" type="text" class="form-control" id="style" value="<?=$lid['style']?>" />
        </p>
        <p>            
        Colour:
          <input class="form-control" name="colour" type="text" id="colour" value="<?=$lid['colour']?>"/>
        </p>
        <p>
        Price:
          <input class="form-control" name="price" type="text" id="price" value="<?=$lid['price']?>"/>
        </p>
        <p>
        Pieces in stock:
          <input class="form-control" name="pieces" type="text" id="pieces" value="<?=$lid['pieces']?>"/>
        </p>        
        <p>
        Supplier:
          <select name="supplier" id="supplier" class="form-control">
            <option value="" selected></option>
            <?php foreach($supplier as $sup) { ?>
				<option value="<?php echo $sup['name'];?>" <?php echo ($lid['supplier']==$sup['name'])?"selected=\"selected\"":""; ?>><?php echo $sup['name'];?></option>
		    <?php } ?>
          </select>
        </p>
        <p>
        Supplier URL:
          <input class="form-control" name="supplier_link" type="text" id="supplier_link" value="<?=$lid['supplier_link']?>"/>
        </p>
        <p>
        Image:
        <input type="file" name="lid_pic_file" id="lid_pic_file" class="form-control" />
    	</p>            
        <div class="dropdown-divider"></div>
</div>
      <div class="modal-footer">
        <input type="submit" name="button" class="btn btn-primary" id="save" value="Save">
      </div>
    </div>  
<hr>
<script>
$('#lid_pic').html('<img class="img-profile-avatar" src="<?=$doc['photo']?: '/img/logo_def.png'; ?>">');

$('#save').click(function() {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			update_lid_data: 1,
			lid_id:  "<?=$lid['id']?>",
			style: $("#style").val(),
			price: $("#price").val(),
			colour: $("#colour").val(),
			pieces: $("#pieces").val(),
			supplier: $("#supplier").val(),
			supplier_link: $("#supplier_link").val(),
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success">'+data.success+'</div>';
			}else if( data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
			}
			$('#lid_inf').html(msg);
		}
	  });

	var fd = new FormData();
    var files = $('#lid_pic_file')[0].files;

    if(files.length > 0 ){
		fd.append('lid_pic',files[0]);
	}
	$.ajax({ 
		url: '/pages/update_data.php?update_lid_pic=1&lid_id=<?=$lid['id']?>', 
		type: 'POST',
		data: fd,
		contentType: false,
      	processData: false,
		cache: false,
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#lid_pic').html('<img class="img-profile-avatar" src="'+data.success.lid_pic+'">');
			}else if( data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
			}
			$('#lid_inf').html(msg);
		}
	  });
});
</script>
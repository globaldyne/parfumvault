<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');


$bottle = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM bottles WHERE id = '".$_GET['id']."'")); 
$doc = mysqli_fetch_array(mysqli_query($conn,"SELECT docData AS photo FROM documents WHERE ownerID = '".$bottle['id']."' AND type = '4'"));
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
    <h1 class="text-primary"><?=$bottle['name']?></h1>
      <hr>
       <div class="text-center">
          <div id="bottle_pic"><div class="loader"></div></div>
       </div>
       <div id="bottle_inf"></div>
        <p>
        Name: 
          <input name="name" type="text" class="form-control" id="name" value="<?=$bottle['name']?>" />
        </p>
        <p>            
        Size (ml):
          <input class="form-control" name="size" type="text" id="size" value="<?=$bottle['ml']?>"/>
        </p>
        <p>
        Price:
          <input class="form-control" name="price" type="text" id="price" value="<?=$bottle['price']?>"/>
        </p>
 		<p>
        Height:
          <input class="form-control" name="height" type="text" id="height" value="<?=$bottle['height']?>"/>
        </p>
        <p>
        Width:
          <input class="form-control" name="width" type="text" id="width" value="<?=$bottle['width']?>"/>
        </p>
        <p>
        Diameter:
          <input class="form-control" name="diameter" type="text" id="diameter" value="<?=$bottle['diameter']?>"/>
        </p>
        <p>
        Stock (pieces):
          <input class="form-control" name="pieces" type="text" id="pieces" value="<?=$bottle['pieces']?>"/>
        </p>        
        <p>
        Notes:
          <input class="form-control" name="notes" type="text" id="notes" value="<?=$bottle['notes']?>"/>
        </p>
        <p>
        Supplier:
          <select name="supplier" id="supplier" class="form-control">
            <option value="" selected></option>
            <?php foreach($supplier as $sup) { ?>
				<option value="<?php echo $sup['name'];?>" <?php echo ($bottle['supplier']==$sup['name'])?"selected=\"selected\"":""; ?>><?php echo $sup['name'];?></option>
		    <?php } ?>
          </select>
        </p>
        <p>
        Supplier URL:
          <input class="form-control" name="supplier_link" type="text" id="supplier_link" value="<?=$bottle['supplier_link']?>"/>
        </p>
        <p>
        Image:
        <input type="file" name="bottle_pic_file" id="bottle_pic_file" class="form-control" />
    	</p>            
        <div class="dropdown-divider"></div>
</div>
      <div class="modal-footer">
        <input type="submit" name="button" class="btn btn-primary" id="save" value="Save">
      </div>
    </div>  
<hr>
<script>
$('#bottle_pic').html('<img class="img-profile-avatar" src="<?=$doc['photo']?: '/img/logo_def.png'; ?>">');

$('#save').click(function() {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			update_bottle_data: 1,
			bottle_id:  "<?=$bottle['id']?>",
			name: $("#name").val(),			
			size: $("#size").val(),
			price: $("#price").val(),
			height: $("#height").val(),
			width: $("#width").val(),
			diameter: $("#diameter").val(),
			pieces: $("#pieces").val(),
			notes: $("#notes").val(),
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
			$('#bottle_inf').html(msg);
		}
	  });

	var fd = new FormData();
    var files = $('#bottle_pic_file')[0].files;

    if(files.length > 0 ){
		fd.append('bottle_pic',files[0]);
	}
	$.ajax({ 
		url: '/pages/update_data.php?update_bottle_pic=1&bottle_id=<?=$bottle['id']?>', 
		type: 'POST',
		data: fd,
		contentType: false,
      	processData: false,
		cache: false,
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#bottle_pic').html('<img class="img-profile-avatar" src="'+data.success.bottle_pic+'">');
			}else if( data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
			}
			$('#bottle_inf').html(msg);
		}
	  });
});
</script>
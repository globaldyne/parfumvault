<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

if(!$_GET['id']){
	echo 'Formula not found';
	return;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$info = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE id = '$id' AND owner_id = '$userID' "));

if(empty($info['id'])){
	echo 'Formula not found';
	return;
}
$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");
while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}
$getFCats = mysqli_query($conn, "SELECT name,cname,type FROM formulaCategories WHERE owner_id = '$userID'");
while($fcats = mysqli_fetch_array($getFCats)){
	$fcat[] =$fcats;
}
$cust = mysqli_query($conn, "SELECT id,name FROM customers WHERE owner_id = '$userID' ORDER BY id ASC");
while($customers = mysqli_fetch_array($cust)){
    $customer[] = $customers;
}

$fTypes_q = mysqli_query($conn, "SELECT id,name,description,concentration FROM perfumeTypes WHERE owner_id = '$userID' ORDER BY concentration DESC");
while($fTypes_res = mysqli_fetch_array($fTypes_q)){
    $fTypes[] = $fTypes_res;
}

// Generate array with tags data 
$tagsData = array(); 
$tagsQ = mysqli_query($conn,"SELECT tag_name FROM formulasTags WHERE formula_id = '$id' AND owner_id = '$userID'");
while($qTags = mysqli_fetch_array($tagsQ)){
	
	$tags = $qTags['tag_name'];
	array_push($tagsData, $tags); 
}

?>
<style>

.editableform .form-control {
  width: 500px !important;
}
</style>

<script src="/js/bootstrap-tagsinput.js"></script> 
<link href="/css/bootstrap-tagsinput.css" rel="stylesheet" />

<div id="set_msg"></div>
<div class="card-body" id="formula_metadata">

<div class="col-sm">
   <div class="form-row">
     <div class="form-group col">
        <label class="control-label col-auto" for="formula_name">Formula name</label>
          <a href="#" data-name="name" class="name" id="formula_name" data-pk="<?php echo $info['id'];?>"><?php echo $info['name']?:'Unnamed';?></a>
      </div>
     <div class="form-group col">
        <label class="control-label col-auto" for="product_name">Product name</label>
        <a href="#" data-name="product_name" class="product_name" id="product_name" data-pk="<?php echo $info['id'];?>"><?php echo $info['product_name'] ?: 'Not set';?></a>
     </div>
    </div>

    <div class="form-row">
     <div class="form-group col-md-6">
        <label class="control-label col-auto" for="isProtected">Protected</label>
        <input name="isProtected" type="checkbox" id="isProtected" value="1" <?php if($info['isProtected'] == '1'){; ?> checked="checked"  <?php } ?>/>
        <i class="fa-solid fa-circle-info" rel="tip" title="When enabled, formula is protected against deletion. By enabling this, a formula revision will be automatically created."></i>
     </div>
    </div>
  
   <div class="form-row">
     <div class="form-group col-md-6">
        <div class="form-floating mb-3">
            <select class="form-select" id="customer" name="customer">
                <option value="0">Internal use</option>
                <?php foreach ($customer as $c) { ?>
                    <option value="<?=$c['id'];?>" <?php echo ($info['customer_id'] == $c['id']) ? "selected=\"selected\"" : ""; ?>><?php echo $c['name']; ?></option>
                <?php } ?>
            </select>
            <label for="customer">Customer</label>
        </div>
    </div>
    <div class="form-group col-md-6">
        <div class="form-floating mb-3">
            <select class="form-select" id="defView" name="defView">
                <option value="1" <?php if ($info['defView'] == "1") echo 'selected="selected"'; ?>>Ingredient Properties</option>
                <option value="2" <?php if ($info['defView'] == "2") echo 'selected="selected"'; ?>>Ingredient Notes</option>
                <option value="3" <?php if ($info['defView'] == "3") echo 'selected="selected"'; ?>>None</option>
            </select>
            <label for="defView">Default view</label>
        </div>
    </div>
 </div>  
  
   <div class="form-row">
     <div class="form-group col-md-6">
        <div class="form-floating mb-3">
            <select class="form-select" id="profile" name="profile">
                <?php foreach ($fcat as $cat) { if ($cat['type'] == 'profile') { ?>
                    <option value="<?=$cat['cname'];?>" <?php echo ($info['profile'] == $cat['cname']) ? "selected=\"selected\"" : ""; ?>><?php echo $cat['name']; ?></option>
                <?php } } ?>
            </select>
            <label for="profile">Category</label>
        </div>   
    </div>  
  
    <div class="form-group col-md-6">
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="tagsinput" placeholder="Start typing to add a label..." data-role="tagsinput" value="<?= implode(",", $tagsData) ?>">
        </div>
    </div>
    
 </div>

 <div class="form-row">
   <div class="form-group col-md-6">
        <div class="form-floating mb-3">
            <select class="form-select" id="catClass" name="catClass">
                <option></option>
                <?php foreach ($cats as $IFRACategories) { ?>
                    <option value="cat<?php echo $IFRACategories['name']; ?>" <?php echo ($info['catClass'] == 'cat' . $IFRACategories['name']) ? "selected=\"selected\"" : ""; ?>>
                        <?php echo 'Cat' . $IFRACategories['name'] . ' - ' . $IFRACategories['description']; ?>
                    </option>
                <?php } ?>
            </select>
            <label for="catClass">Purpose</label>
        </div>
  </div>
   
  <div class="form-group col-md-6">
        <div class="form-floating mb-3">
            <select class="form-select" id="finalType" name="finalType">
                <option value="100">Concentrated (100%)</option>
                <?php foreach ($fTypes as $fType) { ?>
                    <option value="<?php echo $fType['concentration']; ?>" <?php echo ($info['finalType'] == $fType['concentration']) ? "selected=\"selected\"" : ""; ?>>
                        <?php echo $fType['name'] . ' (' . $fType['concentration']; ?>%)
                    </option>
                <?php } ?>
            </select>
            <label for="finalType">Final type</label>
        </div>
   </div>
 </div>

 <div class="form-row">
   <div class="form-group col-md-6">
        <div class="form-floating mb-3">
            <select class="form-select" id="status" name="status">
                <option value="0" <?php if ($info['status'] == "0") echo 'selected'; ?>>Scheduled</option>
                <option value="1" <?php if ($info['status'] == "1") echo 'selected'; ?>>Under Development</option>
                <option value="2" <?php if ($info['status'] == "2") echo 'selected'; ?>>Under Evaluation</option>
                <option value="3" <?php if ($info['status'] == "3") echo 'selected'; ?>>In Production</option>
                <option value="4" <?php if ($info['status'] == "4") echo 'selected'; ?>>To be reformulated</option>
                <option value="5" <?php if ($info['status'] == "5") echo 'selected'; ?>>Failure</option>
            </select>
            <label for="status">Status</label>
        </div>
    </div>

 <div class="form-group col-md-6">
    <div class="form-floating mb-3">
        <select class="form-select" id="gender" name="gender">
            <?php foreach ($fcat as $cat) { if ($cat['type'] == 'gender') { ?>
                <option value="<?=$cat['cname'];?>" <?php echo ($info['gender'] == $cat['cname']) ? "selected=\"selected\"" : ""; ?>><?php echo $cat['name']; ?></option>
            <?php } } ?>
        </select>
        <label for="gender">Gender</label>
    </div>
    </div>
 </div>
 
 <div class="form-row">
    <div class="form-group col">
        <label class="control-label col-auto" for="doc_file">Picture</label>
        <input type="file" name="doc_file" id="doc_file" />
        <input type="submit" name="button" class="btn btn-primary mt-4" id="pic_upload" value="Upload">
        <div id="upload_resp"></div>
    </div>
    
 </div>
 
</div>
<div class="col-sm-6">
     <div class="form-row">
        <div class="form-group col-auto">
        <label class="control-label col-auto" for="notes">Notes</label>
        <a href="#" data-name="notes" class="notes" data-type="textarea" id="notes" data-pk="<?php echo $info['id'];?>"><?php echo $info['notes']?: 'None';?></a>
        </div>
     </div>
</div>

</div>
 


<script>
$(document).ready(function(){

	$('[rel=tip]').tooltip({placement: 'right'});
	
	$('#formula_metadata').editable({
		container: 'body',
	  	selector: 'a.name',
	  	url: "/core/core.php?action=rename&fid=<?=$info['fid']?>",
	  	title: 'Name',
	  	mode: 'inline',
	  	ajaxOptions: { 
			dataType: 'json'
	  	},
	  	validate: function(value){
			if($.trim(value) == ''){
				return 'This field is required';
			}
	  	},
	  	success: function(response) {	
			if(response.success){
				$("#getFormMetaLabel").html(response.msg);
				$("#formula_name").html(response.msg);
				$('#set_msg').html('');
			}else{
				$('#set_msg').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-circle-exclamation mx-2"></i>' + response.error + '</strong></div>');
			}
		},
		error: function (xhr, status, error) {
			$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
			}
		});
  
	$('#formula_metadata').editable({
	 	container: 'body',
	  	selector: 'a.notes',
	  	emptytext: 'None',
	  	url: "/core/core.php?formulaMeta=<?=$info['fid']?>",
	  	title: 'Notes',
	  	mode: 'inline',
		ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		success: function (data) {
			if ( data.success ) {
				$('#formula_desc').html( $('#notes').text() );
			} else if ( data.error ) {
				$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		},
	});
  
	$('#formula_metadata').editable({
	 	container: 'body',
	  	selector: 'a.product_name',
	  	url: "/core/core.php?formulaMeta=<?=$info['fid']?>",
	  	title: 'Product Name',
	  	mode: 'inline',
	  	emptytext: 'None',
	  	ajaxOptions: { 
			dataType: 'json'
	  	},
		success: function(response) {	
			if(response.error){
				$('#set_msg').html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-circle-check mx-2"></i>' + response.msg + '</strong></div>');
			}       
		},
		error: function (xhr, status, error) {
			$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
		}
		
	});


	$("#isProtected").change(function() {
	  	$.ajax({ 
			url: '/core/core.php', 
			type: 'GET',
			data: {
				protect: '<?=$info['fid']?>',
				isProtected: $("#isProtected").is(':checked'),
			},
			dataType: 'json',
			success: function (data) {
				if (data.success === 'Formula locked') {
	          		$('#lock_status').html('<a class="fas fa-lock text-body-emphasis" href="javascript:setProtected(\'false\')"></a>');
        		} else {
          			$('#lock_status').html('<a class="fas fa-unlock text-body-emphasis" href="javascript:setProtected(\'true\')"></a>');
        		}
			},
			error: function (xhr, status, error) {
				$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
			}
		  });
	});
  
	$("#defView").change(function() {
	 	$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				formulaSettings: true,
				fid: '<?=$info['fid']?>',
				set: 'defView',
				val: $("#defView").find(":selected").val(),
			},
			dataType: 'json',
			success: function (response) {
				if(response.success){
					msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-circle-check mx-2"></i>' + response.success + '</strong></div>';
				}else{
					msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + response.error + '</strong></div>';
				}
				$('#set_msg').html(msg);
			},
			error: function (xhr, status, error) {
				$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
			}
	 	});
	});

	$("#profile").change(function() {
		$.ajax({ 
			url: "/core/core.php",
			type: 'POST',
			data: {
				formulaSettings: true,
				fid: '<?=$info['fid']?>',
				set: 'profile',
				val: $("#profile").find(":selected").val(),
			},
			dataType: 'json',
			success: function (response) {
				if(response.success){
					msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-circle-check mx-2"></i>' + response.success + '</strong></div>';
				}else{
					msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>' + response.error + '</strong></div>';
				}
				$('#set_msg').html(msg);
			},
			error: function (xhr, status, error) {
				$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
			}
	  }); 
	});


	$("#gender").change(function() {	
		$.ajax({ 
			url: "/core/core.php",
			type: 'POST',
			data: {
				formulaSettings: true,
				fid: '<?=$info['fid']?>',
				set: 'gender',
				val: $("#gender").find(":selected").val(),
			},
			dataType: 'json',
			success: function (response) {
				if(response.success){
					msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-circle-check mx-2"></i>' + response.success + '</strong></div>';
				}else{
					msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>' + response.error + '</strong></div>';
				}
				$('#set_msg').html(msg);
			},
			error: function (xhr, status, error) {
				$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
			}
	  }); 
	});


	$("#catClass").change(function() {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				formulaSettings: true,
				fid: '<?=$info['fid']?>',
				set: 'catClass',
				val: $("#catClass").find(":selected").val(),
			},
		 dataType: 'json',
			success: function (response) {
				if(response.success){
					msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-circle-check mx-2"></i>' + response.success + '</strong></div>';
				}else{
					msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>' + response.error + '</strong></div>';
				}
				$('#set_msg').html(msg);
			},
			error: function (xhr, status, error) {
				$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
			}
	  });
	});

	$("#finalType").change(function() {
	 	$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				formulaSettings: true,
				fid: '<?=$info['fid']?>',
				set: 'finalType',
				val: $("#finalType").find(":selected").val(),
			},
			dataType: 'json',
			success: function (response) {
				if(response.success){
					msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-circle-check mx-2"></i>' + response.success + '</strong></div>';
				}else{
					msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>' + response.error + '</strong></div>';
				}
				$('#set_msg').html(msg);
			},
			error: function (xhr, status, error) {
				$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
			}
	  });
	});

	$("#status").change(function() {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				formulaSettings: true,
				fid: '<?=$info['fid']?>',
				set: 'status',
				val: $("#status").find(":selected").val(),
			},
			dataType: 'json',
			success: function (response) {
				if(response.success){
					msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-circle-check mx-2"></i>' + response.success + '</strong></div>';
				}else{
					msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-circle-exclamation mx-2"></i>' + response.error + '</strong></div>';
				}
					$('#set_msg').html(msg);
				},
				error: function (xhr, status, error) {
					$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
				}
	  	});
	});


	$("#customer").change(function() {
	 	$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				formulaSettings: true,
				fid: '<?=$info['fid']?>',
				set: 'customer_id',
				val: $("#customer").find(":selected").val(),
			},
			dataType: 'json',
			success: function (response) {
				if(response.success){
					msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-circle-check mx-2"></i>' + response.success + '</strong></div>';
				}else{
					msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong><i class="fa-solid fa-circle-exclamation mx-2"></i>' + response.error + '</strong></div>';
				}
					$('#set_msg').html(msg);
				},
				error: function (xhr, status, error) {
					$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
				}
			});
	});


	
	$("#pic_upload").click(function(){
		$("#upload_resp").html('<div class="dropdown-divider"><div class="alert alert-info">Please wait, file upload in progress...</div>');
		$("#pic_upload").prop("disabled", true);
		$("#pic_upload").prop('value', 'Please wait...');
			
		var fd = new FormData();
		var files = $('#doc_file')[0].files;
		var doc_name = '<?=$info['name']?>';
	
		if(files.length > 0 ){
			fd.append('doc_file',files[0]);
	
				$.ajax({
				  url: '/pages/upload.php?type=2&doc_name=' + btoa(doc_name) + '&id=<?=$id?>',
				  type: 'POST',
				  data: fd,
				  contentType: false,
				  processData: false,
				  cache: false,
				  dataType: 'json',
				  success: function(data){
					 if(data.success){
						$("#upload_resp").html('<div class="alert alert-success mt-3"><i class="fa-solid fa-circle-check mx-2"></i>'+ data.success.msg +'</div>');
						$("#pic_upload").prop("disabled", false);
						$("#pic_upload").prop('value', 'Upload');
						$("#img-formula").html('<img class="img-perfume" src="'+data.success.file+'">');
					 }else{
						$("#upload_resp").html('<div class="alert alert-danger mt-3"><i class="fa-solid fa-triangle-exclamation mx-2"></i>'+ data.error +'</div>');
						$("#pic_upload").prop("disabled", false);
						$("#pic_upload").prop('value', 'Upload');
					 }
				  },error: function (xhr, status, error) {
					$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
				}
			   });
			}else{
				$("#upload_resp").html('<div class="alert alert-danger mt-3"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Please select a file to upload</div>');
				$("#pic_upload").prop("disabled", false);
				$("#pic_upload").prop('value', 'Upload');
			}
	});	
	
	
	$('#tagsinput').on('beforeItemAdd', function(event) {
	   var tag = event.item;   
	   $.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				action: "tagadd",
				fid: '<?=$info['id']?>',
				tag: tag
			},
			dataType: 'json',
			success: function (data) {
				if(data.error){
					$('#tagsinput').tagsinput('remove', tag, {preventPost: true});
					$('#set_msg').html(data.error);
				}
			},
			error: function (xhr, status, error) {
				$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
			}
		});
	});
	$('.selectpicker').selectpicker('refresh');
	
	//$('#tagsinput').tagsinput();
	$('#tagsinput').val('<?= implode(",", $tagsData) ?>');
	$('#tagsinput').tagsinput('refresh');

	$('#tagsinput').on('beforeItemRemove', function(event) {
	var tag = event.item;

	$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				action: "tagremove",
				fid: '<?= $info['id'] ?>',
				tag: tag
			},
			dataType: 'json',
			success: function (data) {
				if (data.error) {
					$('#tagsinput').tagsinput('add', tag, { preventPost: true });
					$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i> ' + data.error + '</div>');
				}
			},
			error: function (xhr, status, error) {
				let errorMessage = '<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred.';
				if (xhr.responseText) {
					errorMessage += 'Server response: ' + xhr.responseText;
				} else {
					errorMessage += error;
				}
				errorMessage += '</div>';
				$('#set_msg').html(errorMessage);
			}
		});
	});

}); //END DOC

</script>


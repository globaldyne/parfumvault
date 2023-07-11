<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

if(!$_GET['id']){
	echo 'Formula not found';
	return;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$info = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE id = '$id'"));

if(empty($info['id'])){
	echo 'Formula not found';
	return;
}
$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");
while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}
$getFCats = mysqli_query($conn, "SELECT name,cname,type FROM formulaCategories");
while($fcats = mysqli_fetch_array($getFCats)){
	$fcat[] =$fcats;
}
$cust = mysqli_query($conn, "SELECT id,name FROM customers ORDER BY id ASC");
while($customers = mysqli_fetch_array($cust)){
    $customer[] = $customers;
}

$fTypes_q = mysqli_query($conn, "SELECT id,name,description,concentration FROM perfumeTypes ORDER BY id ASC");
while($fTypes_res = mysqli_fetch_array($fTypes_q)){
    $fTypes[] = $fTypes_res;
}

// Generate array with tags data 
$tagsData = array(); 
$tagsQ = mysqli_query($conn,"SELECT tag_name FROM formulasTags WHERE formula_id = '$id'");
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
<div id="msg_settungs_info"><div class="alert alert-info">Some of the changes require the page to be reloaded to appear properly. Please remember to refresh your browser if your changes not automatically appear.</div>

<div class="form-horizontal col-m card pt-3 pl-5 pr-5" id="formula_metadata">
 <div id="set_msg" class="col-sm"></div>
 <div class="form-group">
    <label class="control-label col-auto" for="formula_name">Formula Name:</label>
    <div class="col-auto">
      <a href="#" data-name="name" class="name" id="formula_name" data-pk="<?php echo $info['id'];?>"><?php echo $info['name']?:'Unnamed';?></a>
    </div>
  </div>

 <div class="form-group">
    <label class="control-label col-auto" for="product_name">Product Name:</label>
    <div class="col-auto">
     	<a href="#" data-name="product_name" class="product_name" id="product_name" data-pk="<?php echo $info['id'];?>"><?php echo $info['product_name'] ?: $info['name'];?></a>
    </div>
  </div>

 <div class="form-group">
    <label class="control-label col-auto" for="isProtected">Protected:</label>
    <div class="col-auto">
     	<input name="isProtected" type="checkbox" id="isProtected" value="1" <?php if($info['isProtected'] == '1'){; ?> checked="checked"  <?php } ?>/>
        <i class="fa-solid fa-circle-info" rel="tip" title="When enabled, formula is protected against deletion. By enabling this, a formula revision will be automatically created."></i>
    </div>
  </div>
  
   <div class="form-group">
    <label class="control-label col-auto" for="customer">Customer:</label>
    <div class="col-auto">
     	<select name="customer" id="customer" class="form-control ellipsis">
      <option value="0">Internal use</option>
      <?php foreach ($customer as $c) {?>
      <option value="<?=$c['id'];?>" <?php echo ($info['customer_id']==$c['id'])?"selected=\"selected\"":""; ?>><?php echo $c['name'];?></option>
      <?php }	?>
    </select>
    </div>
  </div>
  
 <div class="form-group">
    <label class="control-label col-auto" for="defView">Default view:</label>
    <div class="col-auto">
     	<select name="defView" id="defView" class="form-control">
			  <option value="1" <?php if($info['defView']=="1") echo 'selected="selected"'; ?> >Ingredient Properties</option>
			  <option value="2" <?php if($info['defView']=="2") echo 'selected="selected"'; ?> >Ingredient Notes</option>
          </select>
    </div>
 </div>  
  
 <div class="form-group">
    <label class="control-label col-auto" for="profile">Profile:</label>
    <div class="col-auto">
		<a href="#" id="profile" data-type="select" data-pk="<?php echo $info['id'];?>" data-title="Select profile"></a>
    </div>
 </div>  
  
 <div class="form-group">
    <label class="control-label col-auto" for="tagsinput">Tags:</label>
    <div class="col-auto">
		<input type="text" class="form-control col-xs-3 control-label" id="tagsinput" data-role="tagsinput" />
    </div>
 </div>

 <div class="form-group">
    <label class="control-label col-auto" for="catClass">Purpose:</label>
    <div class="col-auto">
		<select name="catClass" id="catClass" class="form-control ellipsis">
            <option></option>
            <?php foreach ($cats as $IFRACategories) {?>
            <option value="cat<?php echo $IFRACategories['name'];?>" <?php echo ($info['catClass']=='cat'.$IFRACategories['name'])?"selected=\"selected\"":""; ?>><?php echo 'Cat'.$IFRACategories['name'].' - '.$IFRACategories['description'];?></option>
            <?php }	?>
        </select>
    </div>
 </div>
  
 <div class="form-group">
    <label class="control-label col-auto" for="finalType">Final type:</label>
    <div class="col-auto">
		<select name="finalType" id="finalType" class="form-control ellipsis">  
            <option value="100">Concentrated (100%)</option>
	 		<?php foreach ($fTypes as $fType) {?>
			<option value="<?php echo $fType['concentration'];?>" <?php echo ($info['finalType']==$fType['concentration'])?"selected=\"selected\"":""; ?>><?php echo $fType['name'].' ('.$fType['concentration'];?>%)</option>
			<?php }	?>	
    	</select>
    </div>
 </div>

 <div class="form-group">
    <label class="control-label col-auto" for="finalType">Status:</label>
    <div class="col-auto">
        <select name="status" id="status" class="form-control ellipsis">  
            <option value="0" <?php if($info['status'] == "0"){ echo 'selected';}?>>Scheduled</option>
            <option value="1" <?php if($info['status'] == "1"){ echo 'selected';}?>>Under Developent</option>
            <option value="2" <?php if($info['status'] == "2"){ echo 'selected';}?>>Under Evaluation</option>
            <option value="3" <?php if($info['status'] == "3"){ echo 'selected';}?>>In Production</option>
            <option value="4" <?php if($info['status'] == "4"){ echo 'selected';}?>>To be reformulated</option>
            <option value="5" <?php if($info['status'] == "5"){ echo 'selected';}?>>Failure</option>
        </select>
    </div>
 </div>

 <div class="form-group">
    <label class="control-label col-auto" for="gender">Gender:</label>
    <div class="col-auto">
		<a href="#" id="gender" data-type="select" data-pk="<?php echo $info['id'];?>" data-title="Select gender"></a>
    </div>
 </div>
 
 <div class="form-group">
    <label class="control-label col-auto" for="doc_file">Picture:</label>
    <div class="col-auto">
		<input type="file" name="doc_file" id="doc_file" />
      	<input type="submit" name="button" class="btn btn-primary" id="pic_upload" value="Upload">
    </div>
    <div id="upload_resp"></div>
 </div>
 
 <div class="form-group">
    <label class="control-label col-auto" for="notes">Notes:</label>
    <div class="col-auto">
		<a href="#" data-name="notes" class="notes" data-type="textarea" id="notes" data-pk="<?php echo $info['id'];?>"><?php echo $info['notes']?: 'None';?></a>
    </div>
 </div>
 
  
</div>


<script type="text/javascript" language="javascript" >
$(document).ready(function(){

$('[rel=tip]').tooltip({placement: 'right'});


$('#formula_metadata').editable({
  container: 'body',
  selector: 'a.name',
  url: "/pages/update_data.php?action=rename&fid=<?=$info['fid']?>",
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
		msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>' + response.success + '</strong></div>';
		$("#getFormMetaLabel").html(response.msg);
	}else{
		msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>' + response.error + '</strong></div>';
	}
	  	$('#set_msg').html(msg);        
    },

});
  
$('#formula_metadata').editable({
  container: 'body',
  selector: 'a.notes',
  emptytext: 'None',
  url: "/pages/update_data.php?formulaMeta=<?=$info['fid']?>",
  title: 'Notes',
  mode: 'inline'


});
  
$('#formula_metadata').editable({
  container: 'body',
  selector: 'a.product_name',
  url: "/pages/update_data.php?formulaMeta=<?=$info['fid']?>",
  title: 'Product Name',
  mode: 'inline',
  emptytext: 'None',
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; 
    },

});

$('#profile').editable({
	value: "<?php echo $info['profile'];?>",
	title: 'Profile',
	mode: 'inline',
	url: "/pages/update_data.php?formulaMeta=<?=$info['fid']?>",
	source: [
		<?php foreach ($fcat as $cat) { if($cat['type'] == 'profile'){?>		
		 {value: '<?=$cat['cname']?>', text: '<?=$cat['name']?>'},
		<?php } }?>
		]
});

$('#gender').editable({
	value: "<?php echo $info['sex'];?>",
	emptytext: 'Please select',
	mode: 'inline',
	url: "/pages/update_data.php?formulaMeta=<?=$info['fid']?>",
	source: [
		 <?php foreach ($fcat as $cat) { if($cat['type'] == 'sex'){?>		
		 	{value: '<?=$cat['cname']?>', text: '<?=$cat['name']?>'},
		<?php } }?>
	   ]
});
});



$("#isProtected").change(function() {
  $.ajax({ 
		url: '/pages/update_data.php', 
		type: 'GET',
		data: {
			protect: '<?=$info['fid']?>',
			isProtected: $("#isProtected").is(':checked'),
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>' + data.success + '</strong></div>';
			}else{
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>' + data.error + '</strong></div>';
			}
			$('#set_msg').html(msg);
		}
	  });
});
  
$("#defView").change(function() {
 $.ajax({ 
	url: '/pages/update_data.php', 
	type: 'GET',
	data: {
		formula: '<?=$info['fid']?>',
		defView: $("#defView").find(":selected").val(),
		},
	dataType: 'html',
	success: function (data) {
		$('#set_msg').html(data);
	}
  });
});

$("#catClass").change(function() {
 $.ajax({ 
	url: '/pages/update_data.php', 
	type: 'GET',
	data: {
		formula: '<?=$info['fid']?>',
		catClass: $("#catClass").find(":selected").val(),
		},
	dataType: 'html',
	success: function (data) {
		$('#set_msg').html(data);
	}
  });
});

$("#finalType").change(function() {
 $.ajax({ 
	url: '/pages/update_data.php', 
	type: 'GET',
	data: {
		formula: '<?=$info['id']?>',
		finalType: $("#finalType").find(":selected").val(),
		},
	dataType: 'html',
	success: function (data) {
		$('#set_msg').html(data);
	}
  });
});

$("#status").change(function() {
 $.ajax({ 
	url: '/pages/update_data.php', 
	type: 'GET',
	data: {
		formula: '<?=$info['id']?>',
		updateStatus: 1,
		formulaStatus: $("#status").find(":selected").val(),
		},
	dataType: 'html',
	success: function (data) {
		$('#set_msg').html(data);
	}
  });
});


$("#customer").change(function() {
 $.ajax({ 
	url: '/pages/update_data.php', 
	type: 'GET',
	data: {
		formula: '<?=$info['fid']?>',
		customer_id: $("#customer").find(":selected").val(),
		customer_set: 1
		},
	dataType: 'html',
	success: function (data) {
		$('#set_msg').html(data);
	}
  });
});

$("#pic_upload").click(function(){
	$("#upload_resp").html('<div class="dropdown-divider"><div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
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
              success: function(response){
                 if(response != 0){
                    $("#upload_resp").html(response);
					$("#pic_upload").prop("disabled", false);
        			$("#pic_upload").prop('value', 'Upload');
                 }else{
                    $("#upload_resp").html('<div class="dropdown-divider"></div><div class="alert alert-danger"><strong>Error:</strong> File upload failed!</div>');
					$("#pic_upload").prop("disabled", false);
        			$("#pic_upload").prop('value', 'Upload');
                 }
              },
           });
        }else{
			$("#upload_resp").html('<div class="dropdown-divider"></div><div class="alert alert-danger"><strong>Error:</strong> Please select a file to upload!</div>');
			$("#pic_upload").prop("disabled", false);
   			$("#pic_upload").prop('value', 'Upload');
        }
});	


$('#tagsinput').on('beforeItemAdd', function(event) {
   var tag = event.item;   
   $.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		data: {
			do: "tagadd",
			fid: '<?=$info['id']?>',
			tag: tag
			},
		dataType: 'json',
		success: function (data) {
		  	if(data.error){
				$('#tagsinput').tagsinput('remove', tag, {preventPost: true});
				$('#set_msg').html(data.error);
			}
		}
	});
});


$('#tagsinput').val('<?=implode(",",$tagsData)?>');
$('#tagsinput').tagsinput('refresh');


$('#tagsinput').on('beforeItemRemove', function(event) {
   var tag = event.item;   
   $.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		data: {
			do: "tagremove",
			fid: '<?=$info['id']?>',
			tag: tag
			},
		dataType: 'json',
		success: function (data) {
		  	if(data.error){
				$('#tagsinput').tagsinput('add', tag, {preventPost: true});
				$('#set_msg').html(data.error);
			}
		}
	});
});

</script>


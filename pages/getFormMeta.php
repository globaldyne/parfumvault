<?php
require('../inc/sec.php');

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
$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");
while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}
if(empty($info['name'])){
	echo 'Formula not found';
	return;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <script type='text/javascript'>
	if ((navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i))){
			if(screen.height>=1080)
				document.write('<meta name="viewport" content="width=device-width, initial-scale=2.0, minimum-scale=1.0, maximum-scale=3.0, target-densityDpi=device-dpi, user-scalable=yes">');
			else	
				document.write('<meta name="viewport" content="width=device-width, initial-scale=0.5, minimum-scale=0.5, maximum-scale=3.0, target-densityDpi=device-dpi, user-scalable=yes">');
	}
  </script>
  <meta name="description" content="<?php echo $product.' - '.$ver;?>">
  <meta name="author" content="JBPARFUM">
  <title><?php echo $info['name'];?></title>
  <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
  <link href="/css/sb-admin-2.css" rel="stylesheet">
  <link href="/css/bootstrap-select.min.css" rel="stylesheet">
  <link href="/css/bootstrap-editable.css" rel="stylesheet">
  <link href="/css/vault.css" rel="stylesheet">
  <script src="/js/jquery/jquery.min.js"></script>
  <script src="/js/jquery-ui.js"></script>
      
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet">

  <script src="/js/bootstrap.min.js"></script>
  <script src="/js/bootstrap-editable.js"></script>  
  <script src="/js/bootstrap-select.js"></script>

</head>



<style>
.form-inline .form-control {
    display: inline-block;
    width: 500px;
    vertical-align: middle;
}

</style>
<body>

<table class="table table-bordered" id="formula_metadata" cellspacing="0">
  <tr>
    <td colspan="2"><h1 class="mgmIngHeader mgmIngHeader-with-separator"><?=$info['name']?></h1><span class="mgmIngHeaderCAS"><?=$info['product_name']?></span></td>
  </tr>
  <tr>
    <td colspan="2"><div id="msg"><?php echo $msg; ?></div></td>
  </tr>
  <tr>
    <td width="20%">Formula Name:</td>
    <td data-name="name" class="name" data-type="text" align="left" data-pk="<?php echo $info['id'];?>" width="80%"><?php echo $info['name'];?></td>
  </tr>
  <tr>
    <td>Product Name:</td>
    <td data-name="product_name" class="product_name" data-type="text" align="left" data-pk="<?php echo $info['id'];?>"><?php echo $info['product_name'];?></td>
  </tr>
  <tr>
    <td><a href="#" rel="tip" title="When enabled, formula is protected against deletion. By enabling this, a formula revision will be automatically created.">Protected:</a></td>
    <td><input name="isProtected" type="checkbox" id="isProtected" value="1" <?php if($info['isProtected'] == '1'){; ?> checked="checked"  <?php } ?>/></td>
  </tr>
  <tr>
    <td>View:</td>
    <td><select name="defView" id="defView" class="form-control">
			  <option value="1" <?php if($info['defView']=="1") echo 'selected="selected"'; ?> >Ingredient Properties</option>
			  <option value="2" <?php if($info['defView']=="2") echo 'selected="selected"'; ?> >Ingredient Notes</option>
          </select></td>
  </tr>
  <tr>
    <td>Profile:</td>
    <td><a href="#" id="profile" data-type="select" data-pk="<?php echo $info['id'];?>" data-title="Select profile"></a></td>
  </tr>
  <tr>
    <td>Purpose:</td>
    <td>
    <select name="catClass" id="catClass" class="form-control ellipsis">
	<option></option>
	<?php foreach ($cats as $IFRACategories) {?>
	<option value="cat<?php echo $IFRACategories['name'];?>" <?php echo ($info['catClass']=='cat'.$IFRACategories['name'])?"selected=\"selected\"":""; ?>><?php echo 'Cat'.$IFRACategories['name'].' - '.$IFRACategories['description'];?></option>
	<?php }	?>
    </select></td>
  </tr>
  <tr>
    <td>Final type:</td>
    <td>
    <select name="finalType" id="finalType" class="form-control ellipsis">  
            <option value="100">Concentrated (100%)</option>
	 		<option value="<?=$settings['Parfum']?>" <?php if($settings['Parfum'] == $info['finalType']){ echo 'selected';}?>>Parfum (<?=$settings['Parfum']?>%)</option>
			<option value="<?=$settings['EDP']?>" <?php if($settings['EDP'] == $info['finalType']){ echo 'selected';}?>>EDP (<?=$settings['EDP']?>%)</option>
	 		<option value="<?=$settings['EDT']?>" <?php if($settings['EDT'] == $info['finalType']){ echo 'selected';}?>>EDT (<?=$settings['EDT']?>%)</option>
	 		<option value="<?=$settings['EDC']?>" <?php if($settings['EDC'] == $info['finalType']){ echo 'selected';}?>>EDC (<?=$settings['EDC']?>%)</option>		
    </select>
    </td>
  </tr>
  <tr>
    <td>Sex:</td>
    <td><a href="#" id="sex" data-type="select" data-pk="<?php echo $info['id'];?>" data-title="Select sex"></a></td>
  </tr>
  <tr>
    <td>Picture:</td>
    <td>
      <input type="file" name="doc_file" id="doc_file" />
      <input type="submit" name="button" class="btn btn-primary" id="pic_upload" value="Upload">
    </td>
  </tr>
  <tr>
    <td>Notes:</td>
    <td data-name="notes" class="notes" data-type="textarea" align="left" data-pk="<?php echo $info['id'];?>"><?php echo $info['notes'];?></td>
  </tr>
</table>
<div id="list_revisions"></div>

<script type="text/javascript" language="javascript" >
$(document).ready(function(){
$('[rel=tip]').tooltip({placement: 'auto'});

 list_revisions();

 $('#formula_metadata').editable({
  container: 'body',
  selector: 'td.name',
  url: "update_data.php?rename=<?=$info['fid']?>",
  title: 'Name',
  type: "POST",
  mode: 'inline',
  dataType: 'json',
      success: function(response) {				
	  	$('#msg').html(response);        
    },

 });
  
  $('#formula_metadata').editable({
  container: 'body',
  selector: 'td.notes',
  url: "update_data.php?formulaMeta=<?=$info['fid']?>",
  title: 'Notes',
  type: "POST",
  mode: 'inline',
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; 
    },

 });
  
$('#formula_metadata').editable({
  container: 'body',
  selector: 'td.product_name',
  url: "update_data.php?formulaMeta=<?=$info['fid']?>",
  title: 'Product Name',
  type: "POST",
  mode: 'inline',
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; 
    },

 });
 
  $('#profile').editable({
	value: "<?php echo $info['profile'];?>",
  	title: 'Profile',
  	url: "update_data.php?formulaMeta=<?=$info['fid']?>",
    source: [
             {value: 'oriental', text: 'Oriental'},
             {value: 'woody', text: 'Woody'},
             {value: 'floral', text: 'Floral'},
             {value: 'fresh', text: 'Fresh'},
             {value: 'other', text: 'Other'},
          ]
    });
  
    $('#sex').editable({
	value: "<?php echo $info['sex'];?>",
  	url: "update_data.php?formulaMeta=<?=$info['fid']?>",
    source: [
             {value: 'unisex', text: 'Unisex'},
             {value: 'men', text: 'Men'},
             {value: 'women', text: 'Women'},
          ]
    });
  });



  $("#isProtected").change(function() {
	  $.ajax({ 
			url: 'update_data.php', 
			type: 'GET',
			data: {
				protect: '<?=$info['fid']?>',
				isProtected: $("#isProtected").is(':checked'),
				},
			dataType: 'html',
			success: function (data) {
				$('#msg').html(data);
				list_revisions();
			}
		  });
  });
  
$("#defView").change(function() {
 $.ajax({ 
	url: 'update_data.php', 
	type: 'GET',
	data: {
		formula: '<?=$info['fid']?>',
		defView: $("#defView").find(":selected").val(),
		},
	dataType: 'html',
	success: function (data) {
		$('#msg').html(data);
	}
  });
});

$("#catClass").change(function() {
 $.ajax({ 
	url: 'update_data.php', 
	type: 'GET',
	data: {
		formula: '<?=$info['fid']?>',
		catClass: $("#catClass").find(":selected").val(),
		},
	dataType: 'html',
	success: function (data) {
		$('#msg').html(data);
	}
  });
});

$("#finalType").change(function() {
 $.ajax({ 
	url: 'update_data.php', 
	type: 'GET',
	data: {
		formula: '<?=$info['fid']?>',
		finalType: $("#finalType").find(":selected").val(),
		},
	dataType: 'html',
	success: function (data) {
		$('#msg').html(data);
	}
  });
});

$("#pic_upload").click(function(){
	$("#msg").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	$("#pic_upload").prop("disabled", true);
    $("#pic_upload").prop('value', 'Please wait...');
		
	var fd = new FormData();
    var files = $('#doc_file')[0].files;
    var doc_name = '<?=$info['name']?>';

    if(files.length > 0 ){
		fd.append('doc_file',files[0]);

			$.ajax({
              url: 'upload.php?type=2&doc_name=' + btoa(doc_name) + '&id=<?=$id?>',
              type: 'POST',
              data: fd,
              contentType: false,
              processData: false,
			  		cache: false,
              success: function(response){
                 if(response != 0){
                    $("#msg").html(response);
					$("#pic_upload").prop("disabled", false);
        			$("#pic_upload").prop('value', 'Upload');
                 }else{
                    $("#msg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
					$("#pic_upload").prop("disabled", false);
        			$("#pic_upload").prop('value', 'Upload');
                 }
              },
           });
        }else{
			$("#msg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
			$("#pic_upload").prop("disabled", false);
   			$("#pic_upload").prop('value', 'Upload');
        }
});	

function restoreRevision(revision) {
	$('#msg').html('<div class="alert alert-info">Please wait...</div>');
	$.ajax({ 
		url: 'manageFormula.php', 
		type: 'get',
		data: {
			restore: "rev",
			fid: '<?=$info['fid']?>',
			revision: revision
			},
		dataType: 'html',
		success: function (data) {
		  	$('#msg').html(data);
			list_revisions();
		}
	  });
};

function deleteRevision(revision) {
	$.ajax({ 
		url: 'manageFormula.php', 
		type: 'get',
		data: {
			delete: "rev",
			fid: '<?=$info['fid']?>',
			revision: revision
			},
		dataType: 'html',
		success: function (data) {
		  	$('#msg').html(data);
			list_revisions();
		}
	  });	
};

function list_revisions(){
  $('#list_revisions').html('<img class="loader loader-center" src="/img/Testtube.gif"/>');
	$.ajax({
		url: 'listRevisions.php',
		type: 'GET',
		data: {
			"fid": '<?=$info['fid']?>',
			},
		dataType: 'html',
			success: function (data) {
				$('#list_revisions').html(data);
			}
	});
};
</script>
</body>
</html>

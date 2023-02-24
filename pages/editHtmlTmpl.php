<?php 
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$tmpl = mysqli_fetch_array(mysqli_query($conn,"SELECT name,content FROM templates WHERE id = '".$_GET['id']."'"));

?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    <title><?=$tmpl['name']?></title>
    <link href="/css/sb-admin-2.css" rel="stylesheet">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/vault.css" rel="stylesheet">
    <style>
    textarea {
      width: 1024px;
      height: 800px;
    }
    </style>
    <script src="/js/jquery/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="card-body">
      <div id="tmpl_inf"></div>
      <table width="100%" border="0">
      <tr>
        <th colspan="2" align="right" valign="top" scope="col"><?=$tmpl['name']?></th>
      <tr>
      <th scope="col"><textarea id="editor"><?=$tmpl['content']?></textarea></th>
       </tr>
        </table>
        </p>
        <div class="alert alert-info">Please refer <a href="https://www.jbparfum.com/knowledge-base/html-templates/" target="_blank">here</a> for special variables syntax</div>
        <p>
          <input type="submit" name="button" class="btn btn-primary" id="save" value="Save changes">
        </p>
      <div class="dropdown-divider"></div>
</div>
    </div>
    </div>

</body>
</html>
<script>
$('#save').click(function() {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			tmpl: 'update',
			name: 'content',
			pk: <?=$_GET['id']?> ,
			value: $("#editor").val(),
			},
		dataType: 'json',   			
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success">' + data.success + '</div>';
			}else{
				var msg ='<div class="alert alert-danger">' + data.error + '</div>';
			}
			$('#tmpl_inf').html(msg);
		}
	});
});
</script>
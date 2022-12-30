<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

if(!$_GET['id']){
	echo 'Formula not found';
	return;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$info = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE id = '$id'"));
$his =  mysqli_query($conn, "SELECT * FROM formula_history WHERE fid = '$id' ORDER BY date_time DESC");

while($his_res = mysqli_fetch_array($his)){
    $history[] = $his_res;
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
  <link href="/css/vault.css" rel="stylesheet">
  
  <script src="/js/jquery/jquery.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>
  <script src="/js/jquery-ui.js"></script>
  <script src="/js/datatables.min.js"></script>
  <script src="/js/dataTables.responsive.min.js"></script>
  <link href="/css/datatables.min.css" rel="stylesheet">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet">

</head>



<body>
<?php if(empty($history)){ ?>
<table width="50%" border="0" align="center">
  <tr>
    <td><?php echo '<div class="alert alert-info"><strong>No changes logged yet...</strong></div>';?></td>
  </tr>
</table>
<?php 
	} 
?>

<h3>Historical changes for <?=$info['name']?></h3>
<hr>
<div class="card-body">
  <div>
    <table class="table table-bordered" id="tdHistory" width="100%" cellspacing="0">
      <thead>
        <th scope="col">Changes</th>
        <th scope="col">Date</th>
        <th scope="col">User</th>
      </thead>
      <tbody id="history">
        <?php foreach($history as $history){?>
        <tr>
            <td><?=$history['change_made']?></td>
            <td><?=$history['date_time']?></td>
            <td><?=$history['user']?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){

 $('#tdHistory').DataTable({
    "paging":   true,
	"info":   true,
	"lengthMenu": [[15, 35, 60, -1], [15, 35, 60, "All"]],	
	order: [[ 1, 'desc' ]]
 });

});
</script>
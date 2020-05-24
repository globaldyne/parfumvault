<link href="../css/sb-admin-2.css" rel="stylesheet">
<?php
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');
require_once('../func/searchIFRA.php');

if($_GET['id']){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$info = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredients WHERE id = '$id'"));
?>

<table class="table table-bordered" id="dataTable" cellspacing="0">
  <tr>
    <td colspan="2"><h1 class="badge-primary"><?php echo $info['name'];?></h1></td>
  </tr>
  <tr>
    <td width="20%">CAS#:</td>
    <td width="80%"><?php echo $info['cas'];?></td>
  </tr>
  <tr>
    <td>Chemical Name:</td>
    <td><?php echo $info['chemical_name'];?></td>
  </tr>
  <tr>
    <td>Type:</td>
    <td><?php echo $info['type'];?></td>
  </tr>
  <tr>
    <td>Strength:</td>
    <td><?php echo $info['strength'];?></td>
  </tr>
  <tr>
    <td>IFRA Cat4 Limit %:</td>
    <td>
	<?php
		if($limit = searchIFRA($info['cas'],$info['name'],$dbhost,$dbuser,$dbpass,$dbname)){
			echo $limit.' (Value retrieved from your IFRA Library)';
		}else{
			echo $info['IFRA'];
		}
	?>
    </td>
  </tr>
  <tr>
    <td>Price:</td>
    <td><?php echo utf8_encode($settings['currency']).$info['price'];?></td>
  </tr>
  <tr>
    <td>Tenacity:</td>
    <td><?php echo $info['tenacity'];?></td>
  </tr>
  <tr>
    <td>Flash Point:</td>
    <td><?php echo $info['flash_point'];?></td>
  </tr>
  <tr>
    <td>Appearance:</td>
    <td><?php echo $info['appearance'];?></td>
  </tr>
  <tr>
    <td>Odor:</td>
    <td><?php echo $info['odor'];?></td>
  </tr>
  <tr>
    <td>Notes:</td>
    <td><?php echo $info['notes'];?></td>
  </tr>
</table>

<?php
}else{
	
	echo 'nothing here yet';
}
?>
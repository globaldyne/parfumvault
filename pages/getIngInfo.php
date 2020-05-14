<link href="../css/sb-admin-2.css" rel="stylesheet">

<?php
require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');

if($_GET['id']){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$info = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredients WHERE id = '$id'"));
?>

<table class="table table-bordered" id="dataTable" cellspacing="0">
  <tr>
    <td colspan="2"><h1><?php echo $info['name'];?></h1></td>
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
    <td>IFRA Limit:</td>
    <td><?php echo $info['IFRA'];?>%</td>
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
    <td>Notes:</td>
    <td><?php echo $info['notes'];?></td>
  </tr>
</table>

<?php
}else{
	
	echo 'nothing here yet';
}
?>
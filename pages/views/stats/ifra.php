<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/getIFRAtypes.php');

if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM IFRALibrary"))){
	echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="index.php?do=IFRA">import</a> the IFRA xls first.</div>';
	return;
}
?>
<script>
var chart = AmCharts.makeChart("chartIFRA",{
  "type"    : "pie",
  "titleField"  : "type",
  "valueField"  : "value",
  "dataProvider"  : [
<?php
$ifratypes = mysqli_query($conn, "SELECT DISTINCT type FROM IFRALibrary");
while($types =  mysqli_fetch_array($ifratypes)){
?>    {
      "type": "<?php echo $types['type'];?>",
      "value": "<?php getIFRAtypes($types['type'],$conn);?>"
    },
<?php } ?>
  ],
});
</script>
<div id="chartIFRA"></div>

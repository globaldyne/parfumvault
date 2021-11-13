<?php 

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

if(!$_GET['fid']){
	echo '<div class="alert alert-danger"><strong>Error: </strong>no formula provided.</div>';
	return;
}

if(!mysqli_num_rows(mysqli_query($conn,"SELECT revisionDate FROM formulasRevisions WHERE fid = '".$_GET['fid']."'"))){
	echo '<div class="alert alert-info"><strong>No revisions available yet.</strong></div>';
	return;
}
$current_rev = mysqli_fetch_array(mysqli_query($conn, "SELECT id,revision FROM formulasMetaData WHERE fid = '".$_GET['fid']."'"));
$rev_q = mysqli_query($conn,"SELECT fid,revision,revisionDate FROM formulasRevisions WHERE fid = '".$_GET['fid']."' GROUP BY revision");


?>
<div class="listRevisions">
<table class="table table-bordered" width="100%" cellspacing="0">
	<thead>
     <tr class="noBorder">
      <th colspan="3">
       <div class="col-sm-6 text-left">
      <tr>
    <th width="33%" scope="col" align="center">Revision ID</th>
    <th width="33%" scope="col" align="center">Revision taken</th>
    <th width="33%" scope="col" align="center">Actions</th>
  </tr>
  
  <?php while ($rev = mysqli_fetch_array($rev_q)){ ?>
  <tr>
    <td align="center"><?=$rev['revision']?></td>
    <td align="center"><?=$rev['revisionDate']?></td>
    <td align="center"><?php if($rev['revision'] == $current_rev['revision']){ ?><strong>Current revision</strong><?php }else{ ?><a href="/?do=compareFormulas&compare=2&revision=<?=$rev['revision']?>&formula_a=<?=$current_rev['id']?>&formula_b=<?=$rev['fid']?>" target="_blank" class="fas fa-greater-than-equal" title="Compare with the current revision" rel="tipsy"></a>  <a href="javascript:restoreRevision('<?=$rev['revision']?>')" class="fas fa-history" onclick="return confirm('Restore revision taken on <?=$rev['revisionDate']?> ?\nPlease note, this will overwrite the current formula.')"></a> <a href="javascript:deleteRevision('<?=$rev['revision']?>')" class="fas fa-trash" onclick="return confirm('Delete revision taken on <?=$rev['revisionDate']?>?')"></a><?php } ?></td>
  </tr>
  <?php } ?>
</table>
</div>

<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function formulaProfile($conn, $profile, $sex){

	if(empty($profile) && empty($sex)){
		$formulas_n = mysqli_query($conn, "SELECT * FROM formulasMetaData ORDER by name DESC");
	}else{
		$formulas_n = mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE profile = '$profile' OR sex = '$sex' ORDER by name DESC");
	}
	?>
<table width="100%" border="0" cellspacing="0" id="tdData<?=$profile?>" class="table table-striped table-bordered table-sm">
	<thead>
       <tr>
         <th width="20%">Formula Name</th>
         <th width="10%">Details</th>
         <th width="20%">Created</th>
         <th width="20%">Actions</th>
       </tr>
    </thead>
    <tbody>
<?php while ($formula = mysqli_fetch_array($formulas_n)) { ?>
		<tr>
		<td align="center"><?php if($formula['isProtected']){?><a class="fas fa-lock"><?php } ?></a>&nbsp;<a href="?do=Formula&name=<?php echo $formula['name']; ?>"><?php echo $formula['name'];?></a></td>
		<?php $meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE name = '".$formula['name']."'"));?>
		<td align="center"><a href="pages/getFormMeta.php?id=<?php echo $meta['id']; ?>" class="fas fa-comment-dots popup-link"></a></td>
		<td align="center"><?php echo $meta['created'];?></td>
		<td align="center"><a href="javascript:addTODO('<?php echo $formula['fid']; ?>')" class="fas fa-list" rel="tipsy" title="Add <?php echo $formula['name']; ?> to the make list" ></a>  &nbsp; <a href="javascript:cloneMe('<?php echo $formula['name']; ?>')" class="fas fa-copy" rel="tipsy" title="Clone <?php echo $formula['name']; ?>"></a>  &nbsp; <a href="javascript:deleteMe('<?php echo $formula['fid']; ?>')" onclick="return confirm('Delete <?php echo $formula['name']; ?> Formula?')" class="fas fa-trash" rel="tipsy" title="Delete <?php echo $formula['name']; ?>"></a></td>
        </tr>
	<?php } ?>
    </tbody>
</table>
<?php } ?>

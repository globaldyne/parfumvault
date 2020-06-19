<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<?php
function formulaProfile($dbhost,$dbuser,$dbpass,$dbname, $profile, $sex){

	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
	if(empty($profile) && empty($sex)){
		$formulas_n = mysqli_query($conn, "SELECT * FROM formulasMetaData ORDER by name DESC");
	}else{
		$formulas_n = mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE profile = '$profile' OR sex = '$sex' ORDER by name DESC");
	}
	echo '<table width="100%" border="0" cellspacing="0" id="tdData" class="table table-striped table-bordered table-sm">
                  <thead>
                    <tr>
                      <th width="14%">Name</th>
                      <th width="20%">Meta Data</th>
                      <th width="23%">Created</th>
                      <th width="21%">Actions</th>
                    </tr>
                  </thead>
                  <tbody>';	
	while ($formula = mysqli_fetch_array($formulas_n)) {
		echo'<tr><td align="center"><a href="/?do=Formula&name='.$formula['name'].'">'.$formula['name'].'</a></td>';
		$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE name = '".$formula['name']."'"));
		echo '<td align="center"><a href="pages/getFormMeta.php?id='.$meta['id'].'" class="fas fa-comment-dots popup-link"></a></td>';
		echo '<td align="center">'.$meta['created'].'</td>';
		?>
		<td align="center"><a href="javascript:cloneMe('<?php echo $formula['name']; ?>');" class="fas fa-copy" rel="tipsy" title="Clone <?php echo $formula['name']; ?>"</a>  &nbsp; <a href="/?do=listFormulas&action=delete&name=<?php echo $formula['name']; ?>" onclick="return confirm(\'Delete <?php echo $formula['name']; ?> Formula?\');" class="fas fa-trash" rel="tipsy" title="Delete <?php echo $formula['name']; ?>"></a></td></tr>
	<?php
    }
	echo '</tr></tbody></table>';
}
?>
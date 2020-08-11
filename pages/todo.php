<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$fid = mysqli_real_escape_string($conn, $_GET['fid']);

if($_GET['action'] == 'delete' && $_GET['fid']){
	$todo = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM makeFormula WHERE fid = '$fid'"));
	
	if(mysqli_query($conn, "DELETE FROM makeFormula WHERE fid = '$fid'")){
		$msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Formula <strong>'.$todo['name'].'</strong> removed!</div>';
	}
	
}elseif($_GET['action'] == 'add' && $_GET['fid']){
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid'"))){
		
			$msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Formula <strong>'.$todo['name'].'</strong> already exists!</div>';
	}else{							
		mysqli_query($conn, "INSERT INTO makeFormula (fid, name, ingredient, concentration, dilutant, quantity, toAdd) SELECT fid, name, ingredient, concentration, dilutant, quantity, '1' FROM formulas WHERE fid = '$fid'");
	}

}
$todo = mysqli_query($conn, "SELECT * FROM makeFormula GROUP BY name ORDER BY name ASC");


?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
		<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=todo">ToDo Formulas</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                      <th colspan="3">
                      </th>
                    </tr>
                    <tr>
                      <th>Formula Name</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="todo_data">
                    <?php  while ($r = mysqli_fetch_array($todo)) {?>
                    <tr>
                      <td align="center"><a href="pages/makeFormula.php?fid=<?php echo $r['fid']; ?>" target="_blank"><?php echo $r['name']; ?></a></td>
					  <td align="center"><a href="?do=todo&action=delete&fid=<?php echo $r['fid']; ?>" onclick="return confirm('Delete <?php echo $r['name']; ?>?');" class="fas fa-trash"></a></td>
					  </tr>
				  <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
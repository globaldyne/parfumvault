<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$ingID = mysqli_real_escape_string($conn, $_GET['id']);
$ingName = mysqli_real_escape_string($conn, $_GET['name']);

if($_GET['action'] == "delete" && $_GET['id']){
	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '$ingName'"))){
		$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>'.$ingName.'</strong> is in use by at least one formula and cannot be removed!</div>';
		
	}elseif(mysqli_query($conn, "DELETE FROM ingredients WHERE id = '$ingID'")){
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Ingredient <strong>'.$ingName.'</strong> removed from the database!
		</div>';
	}
}
$ingredient_q = mysqli_query($conn, "SELECT * FROM ingredients ORDER BY name ASC");

?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=ingredients">Ingredients</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder noexport">
                      <th colspan="10">
                  		<div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="?do=addIngredient">Add new ingredient</a>
                            <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>CAS #</th>
                      <th>Odor</th>
                      <th>Profile</th>
                      <th>Category</th>
                      <th>IFRA (Cat4 %)</th>
                      <th>Supplier</th>
                      <th class="noexport">SDS</th>
                      <th class="noexport">TGSC</th>
                      <th class="noexport">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php					
				  while ($ingredient = mysqli_fetch_array($ingredient_q)) {
					  echo'
                    <tr>
                      <td align="center"><a href="pages/editIngredient.php?id='.$ingredient['name'].'" class="popup-link">'.$ingredient['name'].'</a>'.checkAllergen($ingredient['name'],$conn).'</td>';
					  if($ingredient['cas']){
						  echo '<td align="center">'.$ingredient['cas'].'</td>';
					  }else{
						  echo '<td align="center">N/A</td>';
					  }
					  echo '
					  <td align="center">'.$ingredient['odor'].'</td>
                      <td align="center">'.$ingredient['profile'].'</td>
					  <td align="center">'.$ingredient['category'].'</td>';
  					  if($limit = searchIFRA($ingredient['cas'],$ingredient['name'],$conn)){
						  echo '<td align="center">'.nl2br(str_replace(' - ', "\n",$limit)).'</td>';
					  }elseif($ingredient['IFRA']){
						  echo '<td align="center">'.$ingredient['IFRA'].'%</td>';
					  }else{
						  echo '<td align="center">N/A</a>';
					  }
					  if ($ingredient['supplier'] && $ingredient['supplier_link']){
						  echo '<td align="center"><a href="'.$ingredient['supplier_link'].'" target="_blanc">'.$ingredient['supplier'].'</a></td>';
					  }elseif ($ingredient['supplier']){
						  echo '<td align="center">'.$ingredient['supplier'].'</a></td>';
					  }else{
						  echo '<td align="center">N/A</td>';
					  }	
					  if ($ingredient['SDS']){
						  echo '<td align="center" class="noexport"><a href="'.$ingredient['SDS'].'" target="_blanc" class="fa fa-save"></a></td>';
					  }else{
						  echo '<td align="center" class="noexport">N/A</td>';
					  }	
					  if ($ingredient['cas']){
						  echo '<td align="center" class="noexport"><a href="http://www.thegoodscentscompany.com/search3.php?qName='.$ingredient['cas'].'" target="_blanc" class="fa fa-external-link-alt"></a></td>';
					  }else{
						  echo '<td align="center" class="noexport"><a href="http://www.thegoodscentscompany.com/search3.php?qName='.$ingredient['name'].'" target="_blanc" class="fa fa-external-link-alt"></a></td>';
					  }
                      echo '<td class="noexport" align="center"><a href="pages/editIngredient.php?id='.$ingredient['name'].'" class="fas fa-edit popup-link"><a> <a href="?do=ingredients&action=delete&id='.$ingredient['id'].'&name='.$ingredient['name'].'" onclick="return confirm(\'Delete '.$ingredient['name'].'?\');" class="fas fa-trash"></a></td>';
					  echo '</tr>';
				  }
                    ?>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
<script type="text/javascript" language="javascript" >

$('#csv').on('click',function(){
  $("#tdData").tableHTMLExport({
	type:'csv',
	filename:'ingredients.csv',
	separator: ',',
  	newline: '\r\n',
  	trimContent: true,
  	quoteFields: true,
	
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	
	htmlContent: false,
  
  	// debug
  	consoleLog: false   
});
 
})

</script>

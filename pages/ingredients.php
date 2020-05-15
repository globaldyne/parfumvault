<?php
$ingID = mysqli_real_escape_string($conn, $_GET['id']);
$ingName = mysqli_real_escape_string($conn, $_GET['name']);

if($_GET['action'] == "delete" && $_GET['id']){
	if(mysqli_query($conn, "DELETE FROM ingredients WHERE id = '$ingID'")){
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Ingredient <strong>'.$ingName.'</strong> removed from the database!
		</div>';
	}
}
if (isset($_GET['page_no']) && $_GET['page_no']!="") {
	$page_no = $_GET['page_no'];
}else{
	$page_no = 1;
}
				
$offset = ($page_no-1) * $total_records_per_page;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;
$adjacents = "2"; 
				
$r_count = mysqli_fetch_array(mysqli_query($conn,"SELECT COUNT(*) As total_ingredients FROM ingredients"));

$total_no_of_pages = ceil($r_count['total_ingredients'] / $total_records_per_page);
$second_last = $total_no_of_pages - 1;
$ingredient_q = mysqli_query($conn, "SELECT * FROM ingredients ORDER BY name ASC LIMIT $offset, $total_records_per_page");
?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="/?do=ingredients">Ingredients</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="ingredients" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder noexport">
                      <th>&nbsp;</th>
                      <th>&nbsp;</th>
                      <th>&nbsp;</th>
                      <th>&nbsp;</th>
                      <th>&nbsp;</th>
                      <th>&nbsp;</th>  
                      <th>&nbsp;</th>  
                      <th>&nbsp;</th>  
                      <th>&nbsp;</th>  
                      <th align="center">
                      <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                      <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="/?do=addIngredient">Add new ingredient</a>
                        <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                      </div>
                    </div>
                    </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>CAS #</th>
                      <th>Type</th>
                      <th>Profile</th>
                      <th>Category</th>
                      <th>IFRA</th>
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
                      <td align="center"><a href="pages/getIngInfo.php?id='.$ingredient['id'].'" class="popup-link">'.$ingredient['name'].'</a></td>';
					  if($ingredient['cas']){
						  echo '<td align="center">'.$ingredient['cas'].'</td>';
					  }else{
						  echo '<td align="center">N/A</td>';
					  }
					  echo '
					  <td align="center">'.$ingredient['type'].'</td>
                      <td align="center">'.$ingredient['profile'].'</td>
					  <td align="center">'.$ingredient['category'].'</td>';
					  if($ingredient['IFRA']){
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
                      echo '<td class="noexport" align="center"><a href="/?do=editIngredient&id='.$ingredient['name'].'" class="fas fa-edit"><a> <a href="/?do=ingredients&action=delete&id='.$ingredient['id'].'&name='.$ingredient['name'].'" onclick="return confirm(\'Delete ingredient\');" class="fas fa-trash"></a></td>';
					  echo '</tr>';
				  }
                    ?>
                    </tr>
                  </tbody>
                </table>
                <div style='padding: 10px 20px 0px; border-top: dotted 1px #CCC;'>
                <strong>Page <?php echo $page_no." of ".$total_no_of_pages; ?></strong>
                </div>
                <ul class="pagination">   
                <li <?php if($page_no <= 1){ echo "class='disabled'"; } ?>>
                <a <?php if($page_no > 1){ echo "href='?do=ingredients&page_no=$previous_page'"; } ?>>Previous</a>
                </li>
<?php 
if ($total_no_of_pages <= 10){  	 
	for ($counter = 1; $counter <= $total_no_of_pages; $counter++){
		if ($counter == $page_no) {
		  echo "<li class='active'><a>$counter</a></li>";	
		}else{
          echo "<li><a href='?do=ingredients&page_no=$counter'>$counter</a></li>";
		}
     }
}elseif($total_no_of_pages > 10){
	if($page_no <= 4){			
	 for ($counter = 1; $counter < 8; $counter++){		 
			if($counter == $page_no){
		   		echo "<li class='active'><a>$counter</a></li>";	
			}else{
           		echo "<li><a href='?page_no=$counter'>$counter</a></li>";
			}
      }
		echo "<li><a>...</a></li>";
		echo "<li><a href='?do=ingredients&page_no=$second_last'>$second_last</a></li>";
		echo "<li><a href='?do=ingredients&page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
		
	}elseif($page_no > 4 && $page_no < $total_no_of_pages - 4){		 
		echo "<li><a href='?do=ingredients&page_no=1'>1</a></li>";
		echo "<li><a href='?do=ingredients&page_no=2'>2</a></li>";
        echo "<li><a>...</a></li>";
        for ($counter = $page_no - $adjacents; $counter <= $page_no + $adjacents; $counter++) {			
           if ($counter == $page_no) {
		   		echo "<li class='active'><a>$counter</a></li>";	
			}else{
           		echo "<li><a href='?do=ingredients&page_no=$counter'>$counter</a></li>";
			}                  
       }
       echo "<li><a>...</a></li>";
	   echo "<li><a href='?do=ingredients&page_no=$second_last'>$second_last</a></li>";
	   echo "<li><a href='?do=ingredients&page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";      
	}else{
        echo "<li><a href='?do=ingredients&page_no=1'>1</a></li>";
		echo "<li><a href='?do=ingredients&page_no=2'>2</a></li>";
        echo "<li><a>...</a></li>";

        for($counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++) {
          if($counter == $page_no){
			  echo "<li class='active'><a>$counter</a></li>";	
			}else{
           	   echo "<li><a href='?do=ingredients&page_no=$counter'>$counter</a></li>";
			}                   
        }
     }
}
?>
    
	<li <?php if($page_no >= $total_no_of_pages){ echo "class='disabled'"; } ?>>
	<a <?php if($page_no < $total_no_of_pages) { echo "href='?do=ingredients&page_no=$next_page'"; } ?>>Next</a>
	</li>
    <?php if($page_no < $total_no_of_pages){
		echo "<li><a href='?do=ingredients&page_no=$total_no_of_pages'>Last &rsaquo;&rsaquo;</a></li>";
		} ?>
</ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
<script type="text/javascript" language="javascript" >

$('#csv').on('click',function(){
  $("#ingredients").tableHTMLExport({
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
  	consoleLog: true   
});
 
})

</script>
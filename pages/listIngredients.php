<?php 

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

require_once(__ROOT__.'/func/formulaProfile.php');

require_once(__ROOT__.'/func/checkAllergen.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/getCatByID.php');

$ingredient_q = mysqli_query($conn, "SELECT * FROM ingredients ORDER BY name ASC");
$defCatClass = $settings['defCatClass'];
?>
                <table class="table table-bordered" id="tdDataIng" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder noexport">
                      <th colspan="11">
                  		<div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item popup-link" href="pages/mgmIngredient.php">Add new ingredient</a>
                            <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
	                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#csv_import">Import from CSV</a>
                            <?php if($pv_online['email'] && $pv_online['password']){?>
                            <div class="dropdown-divider"></div>
	                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#pv_online_import">Import from PV Online</a>
	                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#pv_online_upload">Upload to PV Online</a>
                            <?php } ?>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>INCI</th>
                      <th>CAS#</th>
                      <th>Odor</th>
                      <th>Profile</th>
                      <th>Category</th>
                      <th><?php echo ucfirst($settings['defCatClass']);?>%</th>
                      <th>Supplier</th>
                      <th class="noexport">SDS</th>
                      <th class="noexport">TGSC</th>
                      <th class="noexport">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php while ($ingredient = mysqli_fetch_array($ingredient_q)) { ?>
                    <tr>
                      <td align="center"><a href="pages/mgmIngredient.php?id=<?=base64_encode($ingredient['name'])?>" class="popup-link"><?php echo $ingredient['name'];?></a><?php echo checkAllergen($ingredient['name'],$conn);?></td>
                      <td align="center"><?php echo $ingredient['INCI'];?></td>
					  <?php
                      if($ingredient['cas']){
						  echo '<td align="center">'.$ingredient['cas'].'</td>';
					  }else{
						  echo '<td align="center">N/A</td>';
					  }?>
					  
					  <td align="center"><?=$ingredient['odor']?></td>
                      <td align="center"><?=$ingredient['profile']?></td>
					  <td align="center"><?=getCatByID($ingredient['category'],TRUE,$conn)?></td>
  					  <?php
                      if($limit = searchIFRA($ingredient['cas'],$ingredient['name'],null,$conn,$defCatClass)){
						  $limit = explode(' - ', $limit);
						  echo '<td align="center"><a href="#" rel="tipsy" title="'.$limit['1'].'">'.$limit['0'].'<a></td>';
					  }elseif($ingredient[$defCatClass]){
						  echo '<td align="center">'.$ingredient[$defCatClass].'</td>';
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
					  ?>
                      <td class="noexport" align="center"><a href="pages/mgmIngredient.php?id=<?php echo $ingredient['name'];?>" class="fas fa-edit popup-link"><a> <a href="javascript:delete_ingredient('<?php echo $ingredient['id'];?>')" onclick="return confirm('Delete <?php echo $ingredient['name'];?> ?')" class="fas fa-trash"></a></td>
					  </tr>
				  <?php } ?>
                    </tr>
                  </tbody>
                </table>
<script type="text/javascript" language="javascript" >

$('a[rel=tipsy]').tipsy();
	
$('.popup-link').magnificPopup({
	type: 'iframe',
	closeOnContentClick: false,
	closeOnBgClick: false,
	showCloseBtn: true,
});
	
$('#tdDataIng').DataTable({
    "paging":   true,
	"info":   true,
	"lengthMenu": [[20, 35, 60, -1], [20, 35, 60, "All"]]
});

$('#csv').on('click',function(){
  $("#tdDataIng").tableHTMLExport({
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
});

</script>

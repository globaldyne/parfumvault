<?php 

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/formulaProfile.php');
require_once(__ROOT__.'/func/getIngSupplier.php');
require_once(__ROOT__.'/func/checkAllergen.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/getCatByID.php');
require_once(__ROOT__.'/func/profileImg.php');
require_once(__ROOT__.'/func/getDocument.php');

$ingredient_q = mysqli_query($conn, "SELECT * FROM ingredients ORDER BY name ASC");
$defCatClass = $settings['defCatClass'];
?>
                <table class="table table-bordered" id="tdDataIng" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder noexport">
                      <th colspan="10">
                  		<div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item popup-link" href="pages/mgmIngredient.php">Add new ingredient</a>
                            <a class="dropdown-item" id="csv_export" href="/pages/export.php?format=csv&kind=ingredients">Export to CSV</a>
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
                      <th>CAS#</th>
                      <th>Odor</th>
                      <th>Profile</th>
                      <th>Category</th>
                      <th><?php echo ucfirst($settings['defCatClass']);?>%</th>
                      <th>Supplier(s)</th>
                      <th class="noexport">Document(s)</th>
                      <th class="noexport">TGSC</th>
                      <th class="noexport">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php while ($ingredient = mysqli_fetch_array($ingredient_q)) { ?>
                    <tr>
                      <td align="center"><a href="pages/mgmIngredient.php?id=<?=base64_encode($ingredient['name'])?>" class="popup-link listIngName listIngName-with-separator"><?=$ingredient['name']?></a><?=checkAllergen($ingredient['name'],$conn)?><span class="listIngHeaderSub"><?=$ingredient['INCI']?></span></td>
					  <?php if($ingredient['cas']){ ?>
                      <td align="center"><?=$ingredient['cas']?></td>
                      <?php }else{ ?>
					  <td align="center">N/A</td>
					  <?php } ?>
					  <td align="center"><?=$ingredient['odor']?></td>
                      <td align="center"><a href="#" rel="tipsy" title="<?=$ingredient['profile']?>"><img class="img_ing_prof" src="<?=profileImg($ingredient['profile'])?>" /></a></td>
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
					?>
					<?php if($a = getIngSupplier($ingredient['id'],$conn)){ ?>			
                      <td align="center">
                         <div class="btn-group">
                           <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-store"></i></button>
                             <div class="dropdown-menu dropdown-menu-right">
                         <?php foreach ($a as $b){ ?>
                             <a class="dropdown-item popup-link" href="<?=$b['supplierLink']?>"><?=$b['name']?></a> 
                         <?php }	?>
                             </div>
                         </div>
                        </td>
                    <?php }else{ ?>
                        <td align="center">N/A</a>
                    <?php } ?>
					<?php if ($a = getDocument($ingredient['id'],1,$conn)){ ?>
                      <td align="center">
						<div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-alt"></i></button>
                            <div class="dropdown-menu dropdown-menu-right">
                         <?php foreach ($a as $b){ ?>
                             <a class="dropdown-item popup-link" href="pages/viewDoc.php?id=<?=$b['id']?>"><?=$b['name']?></a> 
                         <?php }	?>
                             </div>
                         </div>
                      </td>
                         <?php }else{ ?>
						  <td align="center" class="noexport">N/A</td>
					  <?php } ?>
					  
					  <?php if ($ingredient['cas']){ ?>
					  			<td align="center" class="noexport"><a href="http://www.thegoodscentscompany.com/search3.php?qName=<?=$ingredient['cas']?>" target="_blank" class="fa fa-external-link-alt"></a></td>
					  <?php }else{ ?>
						  		<td align="center" class="noexport"><a href="http://www.thegoodscentscompany.com/search3.php?qName=<?=$ingredient['name']?>" target="_blank" class="fa fa-external-link-alt"></a></td>
					  <?php }  ?>
                      <td class="noexport" align="center"><a href="pages/mgmIngredient.php?id=<?=base64_encode($ingredient['name'])?>" class="fas fa-edit popup-link"><a> <a href="javascript:delete_ingredient('<?=$ingredient['id']?>')" onclick="return confirm('Delete <?=$ingredient['name']?> ?')" class="fas fa-trash"></a></td>
					  </tr>
				  <?php } ?>
                    </tr>
                  </tbody>
                </table>
<script type="text/javascript" language="javascript" >
$(".alert-dismissible").fadeTo(2000, 500).slideUp(500, function(){
    $(".alert-dismissible").slideUp(500);
});

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

</script>

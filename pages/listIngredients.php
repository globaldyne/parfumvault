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

$defCatClass = $settings['defCatClass'];


if(isset($_GET['ing_limit'])){
	 $_SESSION['ing_limit'] = $_GET['ing_limit'];
}

$pageLimit = isset($_SESSION['ing_limit']) ? $_SESSION['ing_limit'] : 20;
$page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
$paginationStart = ($page - 1) * $pageLimit;
$extra = "ORDER BY name ASC LIMIT $paginationStart, $pageLimit";

$i = mysqli_real_escape_string($conn, $_GET['q']);
if($_GET['adv']){
	if($name = mysqli_real_escape_string($conn, $_GET['name'])){
		$n = $name;
	}else{
		$n = '%';
	}
	
	$name = "name LIKE '%$n%'";
	
	if($cas = mysqli_real_escape_string($conn, $_GET['cas'])){
		$cas = "AND cas LIKE '%$cas%'";
	}
	
	if($odor = mysqli_real_escape_string($conn, $_GET['odor'])){
		$odor = "AND odor LIKE '%$odor%'";
	}
	
	if($profile = mysqli_real_escape_string($conn, $_GET['profile'])){
		$profile = "AND profile = '$profile'";
	}
	
	if($category = mysqli_real_escape_string($conn, $_GET['cat'])){
		$category = "AND category = '$category'";	
	}

	$filter = "WHERE $name $cas $odor $profile $category";
	$extra = "ORDER BY name";
}

if($i){
	$filter = "WHERE name LIKE '%$i%' OR cas LIKE '%$i%' OR odor LIKE '%$i%' OR INCI LIKE '%$i%'";
}

$ingredient_q = mysqli_query($conn, "SELECT id,name,INCI,cas,profile,category,odor,$defCatClass FROM ingredients $filter $extra");

$allIng = mysqli_fetch_array(mysqli_query($conn, "SELECT count(id) AS id FROM ingredients $filter"));
$pages = ceil($allIng['0'] / $pageLimit);

$prev = $page - 1;
$next = $page + 1;

?>
<table class="table table-bordered" id="tdDataIng" width="100%" cellspacing="0">
	<thead>
     <tr class="noBorder noexport">
      <th colspan="9">
       <div class="col-sm-6 text-left">
		<label>Show  
        <select name="ing_limit" id="ing_limit" class="form-control input-sm">
        <?php foreach([20,30,50,100] as $setLimit){ ?>
           <option
           <?php if($pageLimit == $setLimit) echo 'selected'; ?>
            value="<?=$setLimit?>"><?=$setLimit?></option>
         <?php } ?>
         </select>
		entries</label>
        </div>
            <div class="col-sm-6 text-right">
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
                      <td class="noexport" align="center"><a href="pages/mgmIngredient.php?id=<?=base64_encode($ingredient['name'])?>" class="fas fa-edit popup-link"><a> <a href="javascript:delete_ingredient('<?=$ingredient['id']?>')" onclick="return confirm('Delete <?=$ingredient['name']?> ?')" class="fas fa-trash"></a></td>
					  </tr>
				  <?php } ?>
                    </tr>
                  </tbody>
                </table>
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a class="page-link" href="<?php if($page <= 1){ echo '#'; } else { echo "javascript:list_ingredients($prev,$pageLimit)"; } ?>">Previous</a>
                </li>
                <?php for($i = 1; $i <= $pages; $i++ ){ ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a class="page-link" href="<?php echo "javascript:list_ingredients($i,$pageLimit)";?>"> <?=$i?> </a>
                </li>
                <?php } ?>
                <li class="page-item <?php if($page >= $pages) { echo 'disabled'; } ?>">
                    <a class="page-link" href="<?php if($page >= $pages){ echo '#'; } else {echo "javascript:list_ingredients($next,$pageLimit)"; } ?>">Next</a>
                </li>
            </ul>
        </nav>
<script type="text/javascript" language="javascript" >
$(".alert-dismissible").fadeTo(2000, 500).slideUp(500, function(){
    $(".alert-dismissible").slideUp(500);
});

$('a[rel=tipsy]').tipsy();
	
$('#ing_limit').change(function () {
	list_ingredients(<?=$page?>,$("#ing_limit").val());
})

var delay = (function(){
  var timer = 0;
  return function(callback, ms){
  clearTimeout (timer);
  timer = setTimeout(callback, ms);
 };
})();

$("#ing_search").keyup(function(){
  var filter = $(this).val();			
  delay(function(){
		list_ingredients('1', <?=$pageLimit?>, filter);
  }, 1000);
});


$('.popup-link').magnificPopup({
	type: 'iframe',
	closeOnContentClick: false,
	closeOnBgClick: false,
	showCloseBtn: true,
});
	
$('#tdDataIng').DataTable({
    "paging":   false,
	"info":   false,
	"searching": false
});

</script>

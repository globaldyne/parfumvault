<link href="css/ingCardView.css" rel="stylesheet">

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
require_once(__ROOT__.'/func/getIngState.php');

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

$ingredient_q = mysqli_query($conn, "SELECT id,name,INCI,cas,profile,category,odor,$defCatClass,notes,physical_state,type FROM ingredients $filter $extra");

$allIng = mysqli_fetch_array(mysqli_query($conn, "SELECT count(id) AS id FROM ingredients $filter"));
$pages = ceil($allIng['0'] / $pageLimit);

$prev = $page - 1;
$next = $page + 1;

?>
<table class="table table-bordered" id="tdDataCard" width="100%" cellspacing="0">
	<thead>
     <tr class="noBorder">
      <th colspan="1">
       <div class="col-sm-6 text-left">
		<label class="ing-limit">Show  
        <select name="ing_limit" id="ing_limit" class="form-control input-sm mr-2 ml-2">
        <?php foreach([20,30,50,100] as $setLimit){ ?>
           <option
           <?php if($pageLimit == $setLimit) echo 'selected'; ?>
            value="<?=$setLimit?>"><?=$setLimit?></option>
         <?php } ?>
         </select>
		entries</label>
            <div class="ing-view">
                <a href="javascript:setView('1')" class="fas fa-list"></a>
                <a href="javascript:setView('2')" class="fas fa-border-all"></a>
            </div>
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
  </thead>
  </table>
  <?php 
  		while ($ingredient = mysqli_fetch_array($ingredient_q)) { 
			$rgb = getCatByIDRaw($ingredient['category'], 'colorKey', $conn)['0'] ;	
	?>
   <div class="col-md-3">
 	<div class="card text-white card-has-bg click-col" style="background-image:url('<?=getCatByIDRaw($ingredient['category'],'image',$conn)['0']?>');"> 
      <div class="card-img-overlay d-flex flex-column">
        <div class="card-body">
          <small class="card-meta mb-2"><?=$ingredient['cas'] ?: "CAS not available"?></small>
           <div class="meta-link">
	     	<a href="javascript:delete_ingredient('<?=$ingredient['id']?>')" onclick="return confirm('Delete <?=$ingredient['name']?> ?')" class="fas fa-trash"></a>
      </div>
          <h4 class="card-title mt-0 "><a class="text-white popup-link" href="pages/mgmIngredient.php?id=<?=base64_encode($ingredient['name'])?>"><?=$ingredient['name']?></a></h4>
          <small class="mb-2"><?=$ingredient['INCI']?></small>
          <div class="ing-desc"><small class="mb-2"><?=$ingredient['notes']?></small></div>

         </div>
         <div class="card-footer">
          <div class="media">
    	   	<?=getIngType($ingredient['type'], 'mr-3 rounded-circle-family physical_state_bg','style="border: solid 3px rgba('.$rgb.');"')?>
  			<?=getIngState($ingredient['physical_state'],'mr-3 rounded-circle-family physical_state_bg', 'style="border: solid 3px rgba('.$rgb.');"')?>
  			<img class="mr-3 rounded-circle-family family_bg" style="border: solid 3px rgba(<?=$rgb?>);" src="<?=profileImg($ingredient['profile'])?>">
  <div class="media-body">
    <h6 class="my-0 text-white d-block"><?=searchIFRA($ingredient['cas'],$ingredient['name'],null,$conn,$defCatClass)?:ucfirst($defCatClass).': '.$ingredient[$defCatClass].'%'?></h6>
     <div class="sh-bk"><small><?=getCatByIDRaw($ingredient['category'], 'name', $conn)['0']?></small></div>
  </div>
</div>
          </div>
        </div>
      </div>
  </div>
  <?php } ?>
<div class="nav-paging">
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
   </div>

<script type="text/javascript" language="javascript" >

$(".alert-dismissible").fadeTo(2000, 500).slideUp(500, function(){
    $(".alert-dismissible").alert('close');
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

var filter = $("#ing_search").val();
var provider = $('.btn-search').attr('id');

$("#ing_search").keyup(pvSearch);
					   
function pvSearch(){
	var filter = $("#ing_search").val();
	var provider = $('.btn-search').attr('id');
	if(provider == 'local'){
	  delay(function(){
			list_ingredients('1', <?=$pageLimit?>, filter);
	  }, 1000);
  	}else{
	  	delay(function(){
		  list_ingredients();
		}, 1000);
	}
};


$('.popup-link').magnificPopup({
	type: 'iframe',
	closeOnContentClick: false,
	closeOnBgClick: false,
	showCloseBtn: true,
});

if(provider == 'local'){
 var pr = null;
}else{
 var pr = "modules/suppliers/" + provider + ".php?q=" + encodeURIComponent(filter);
}

$('#tdDataIng').DataTable({
	"ajax": pr,
	"processing": true,
    "paging":   false,
	"info":   false,
	"searching": false,
	"language": {
        "zeroRecords" : 'Nothing found, try <a href="#" data-toggle="modal" data-target="#adv_search">advanced</a> search instead?',
		"processing": '<div class="spinner-grow"></div> Please Wait...'
    },
	
});

</script>

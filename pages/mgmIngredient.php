<?php 
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/formatBytes.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/func/sanChar.php');
require_once(__ROOT__.'/func/profileImg.php');

require_once(__ROOT__.'/func/searchIFRA.php');

$ingID = sanChar(mysqli_real_escape_string($conn, base64_decode($_GET["id"])));
if($ingID){
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$ingID'")))){
		if(mysqli_query($conn, "INSERT INTO ingredients (name) VALUES ('$ingID')")){
			$msg='<div class="alert alert-info alert-dismissible"><strong>Info:</strong> ingredient '.$ingID.' added</div>';
		}
	}
}
$StandardIFRACategories = mysqli_query($conn, "SELECT name,description,type FROM IFRACategories WHERE type = '1' ORDER BY id ASC");
while($cats_res = mysqli_fetch_array($StandardIFRACategories)){
    $cats[] = $cats_res;
}

$rows = count($cats);
$counter = 0;
$cols = 3;
$usageStyle = array('even_ing','odd_ing');

$defCatClass = $settings['defCatClass'];

$res_ingTypes = mysqli_query($conn, "SELECT id,name FROM ingTypes ORDER BY name ASC");
$res_ingStrength = mysqli_query($conn, "SELECT id,name FROM ingStrength ORDER BY name ASC");
$res_ingCategory = mysqli_query($conn, "SELECT id,image,name,notes FROM ingCategory ORDER BY name ASC");
$res_ingProfiles = mysqli_query($conn, "SELECT id,name FROM ingProfiles ORDER BY name ASC");
$res_ingSupplier = mysqli_query($conn, "SELECT id,name,min_ml,min_gr FROM ingSuppliers ORDER BY name ASC");

$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredients WHERE name = '$ingID'"));
$ingSafetyInfo = mysqli_query($conn, "SELECT GHS FROM ingSafetyInfo WHERE ingID = '".$ing['id']."'");
while($safety_res = mysqli_fetch_array($ingSafetyInfo)){
    $safety[] = $safety_res;
}
$pictograms = mysqli_query($conn, "SELECT name,code FROM pictograms");
while($pictograms_res = mysqli_fetch_array($pictograms)){
    $pictogram[] = $pictograms_res;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
<title>Manage <?=$ing['name']?></title>
<link href="../css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<script src="../js/jquery/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/bootstrap-select.js"></script>
<script src="../js/bootstrap-editable.js"></script>

<link rel="stylesheet" type="text/css" href="../css/datatables.min.css"/>
<script type="text/javascript" src="../js/datatables.min.js"></script>

<style>
.form-inline .form-control {
    display: inline-block;
    width: 500px;
    vertical-align: middle;
}

</style>
<link href="../css/sb-admin-2.css" rel="stylesheet">
<link href="../css/bootstrap-select.min.css" rel="stylesheet">
<link href="../css/bootstrap.min.css" rel="stylesheet">
<link href="../css/vault.css" rel="stylesheet">
<link href="../css/bootstrap-editable.css" rel="stylesheet">


<style>
.container {
    max-width: 100%;
	width: 1400px;
	height: 1300px;
}
.dropdown-menu > li > a {
    font-weight: 700;
    padding: 10px 20px;
}

.bootstrap-select.btn-group .dropdown-menu li small {
    display: block;
    padding: 6px 0 0 0;
    font-weight: 100;
}
</style>

<script>

$(document).ready(function() {
$('[rel=tipsy]').tooltip({placement: 'auto'});

function unlimited_usage(status,maxulimit){
		$('#usage_type').prop('disabled', status);
		<?php foreach ($cats as $cat) {?>
		$('#cat<?php echo $cat['name'];?>').prop('readonly', status).val(maxulimit);
		<?php } ?>
	}

	<?php if($ing['noUsageLimit']){ ?>
		$('#noUsageLimit').prop('checked', true);
		unlimited_usage(true,'100');
	<?php } ?>
    $('#noUsageLimit').click(function(){
        if($(this).is(':checked')){
			unlimited_usage(true,'100');
        }else{
			unlimited_usage(false,null);
		}
    });

});

function reload_overview() {
	$('#ingOverview').html('<img src="/img/loading.gif"/>');

	$.ajax({ 
		url: 'ingOverview.php', 
		type: 'GET',
		data: {
			id: "<?=$ing['id']?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#ingOverview').html(data);
		}
	});
};
reload_overview();

function search() {	  
	$("#odor").val('Loading...');
	
	if ($('#cas').val()) {
		var	ingName = $('#cas').val();
	}else if($('#name').val()) {
		var	ingName = $('#name').val();
	}else{
		var	ingName = "<?php echo $ing['name'];?>"
	}
	
	$.ajax({ 
		url: 'searchTGSC.php', 
		type: 'get',
		data: {
			name: ingName
			},
		dataType: 'html',
		success: function (data) {
		  $('#TGSC').html(data);
		}
	  });
};



function printLabel() {
	<?php if(empty($settings['label_printer_addr']) || empty($settings['label_printer_model'])){?>
	$("#msg").html('<div class="alert alert-danger alert-dismissible">Please configure printer details in <a href="?do=settings">settings<a> page</div>');
	<?php }else{ ?>
	$("#msg").html('<div class="alert alert-info alert-dismissible">Printing...</div>');

$.ajax({ 
    url: 'manageFormula.php', 
	type: 'get',
    data: {
		action: "printLabel",
		type: "ingredient",
		dilution: $("#dilution").val(),
		cas: $("#cas").val(),
		dilutant: btoa($("#dilutant").val()),
		name: "<?php echo base64_encode($ing['name']); ?>"
		},
	dataType: 'html',
    success: function (data) {
	  $('#msg').html(data);
    }
  });
	<?php } ?>
};


function reload_data() {
	$.ajax({ 
		url: 'whereUsed.php', 
		type: 'get',
		data: {
			id: "<?=base64_encode($ingID)?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_whereUsed').html(data);
		},
	  });
	
	$.ajax({ 
		url: 'compos.php', 
		type: 'get',
		data: {
			id: "<?=base64_encode($ingID)?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_composition').html(data);
		},
	  });

	$.ajax({ 
		url: 'ingSuppliers.php', 
		type: 'get',
		data: {
			id: "<?=$ing['id']?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_suppliers').html(data);
		},
	  });
	
	$.ajax({ 
		url: 'ingDocuments.php', 
		type: 'get',
		data: {
			id: "<?=$ing['id']?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_documents').html(data);
		},
	  });
	
	<?php if(isset($ing['cas']) && $settings['pubChem'] == '1'){ ?>

	$.ajax({ 
		url: 'pubChem.php', 
		type: 'get',
		data: {
			cas: "<?php echo $ing['cas']; ?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#pubChemData').html(data);
		}
	});
	
	<?php } ?>
};

<?php if($ingID){ ?>
$(document).ready(function() {

	reload_data();
});

<?php } ?>
</script>
</head>

<body>
<div class="mgm-ing-theme">
	<div class="container mgm-ing-bk">
        <div class="mgm-column mgm-visible-xl mgm-col-xl-5">
        <h1 class="mgmIngHeader mgmIngHeader-with-separator"><?php if($ingID){ echo $ing['name'];?>
            <div class="btn-group">
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#printLabel">Print Label</a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#cloneIng">Clone ingredient</a>
              </div>
            </div>
            <?php }else {?>
            Add ingredient
            <?php } ?>
        </h1>
        <span class="mgmIngHeaderCAS"><?=$ing['cas']?></span>
        </div>

<div id="ingMsg"><?=$msg?></div>
<div id="ingOverview"><img src="/img/loading.gif"/></div>
<div class="mgmIngHeader-with-separator-full"></div>
<!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li class="active"><a href="#general" role="tab" data-toggle="tab"><icon class="fa fa-table"></icon> General</a></li>
      <?php if($ingID){?>
      <li><a href="#usage_limits" role="tab" data-toggle="tab"><i class="fa fa-bong"></i> Usage &amp; Limits</a></li>
      <li><a href="#supply" role="tab" data-toggle="tab"><i class="fa fa-shopping-cart"></i> Supply</a></li>
      <li><a href="#tech_data" role="tab" data-toggle="tab"><i class="fa fa-cog"></i> Technical Data</a></li>
      <li><a href="#documents" role="tab" data-toggle="tab"><i class="fa fa-file-alt"></i> Documents</a></li>
      <li><a href="#note_impact" role="tab" data-toggle="tab"><i class="fa fa-magic"></i> Note Impact</a></li>
      <li><a href="#tech_composition" role="tab" data-toggle="tab"><i class="fa fa-th-list"></i> Composition</a></li>
      <li><a href="#safety_info" role="tab" data-toggle="tab"><i class="fa fa-biohazard"></i> Safety</a></li>
      <?php if($settings['pubChem'] == '1' && $ing['cas']){?>
      	<li><a href="#pubChem" role="tab" data-toggle="tab"><i class="fa fa-atom"></i> Pub Chem</a></li>
      <?php } ?>  
       <li><a href="#privacy" role="tab" data-toggle="tab"><i class="fa fa-user-secret"></i> Privacy</a></li>   
       <li><a href="#whereUsed" role="tab" data-toggle="tab"><i class="fa fa-random"></i> Where used?</a></li>
   	  <?php } ?>
    </ul>
           	  <div class="tab-content">
			<div class="tab-pane fade active in" id="general">
                              <h3>General</h3>
							   <hr>
                	         <table width="100%" border="0">
                              <tr>
                                <td colspan="6"></td>
                              </tr>
                              <?php if(empty($ingID)){?>
                              <tr>
                                <td>Name:</td>
                                <td colspan="5"><input name="name" type="text" class="form-control" id="name" /></td>
                              </tr>
                              <?php } ?>
                              <tr>
                                <td>INCI:</td>
                                <td colspan="5"><input name="INCI" type="text" class="form-control" id="INCI" value="<?php echo $ing['INCI']; ?>" /></td>
                              </tr>
                              <tr>
                                <td width="20%"><a href="#" rel="tipsy" title="If your material contains multiple CAS, then use Mixture or Blend instead.">CAS #:</a></td>
                                <td colspan="5"><input name="cas" type="text" class="form-control" id="cas" value="<?php echo $ing['cas']; ?>"></td>
                              </tr>
                              <tr>
                                <td height="31">REACH #:</td>
                                <td colspan="5"><input name="reach" type="text" class="form-control" id="reach" value="<?php echo $ing['reach']; ?>" /></td>
                              </tr>
                              <tr>
                                <td height="31">FEMA #:</td>
                                <td colspan="5"><input name="fema" type="text" class="form-control" id="fema" value="<?php echo $ing['FEMA']; ?>" /></td>
                              </tr>
                              <tr>
                                <td height="31"><a href="#" rel="tipsy" title="If enabled, ingredient name will be printed in the box label.">To Declare:</a></td>
                                <td colspan="5"><input name="isAllergen" type="checkbox" id="isAllergen" value="1" <?php if($ing['allergen'] == '1'){; ?> checked="checked"  <?php } ?>/></td>
                              </tr>
                              <tr>
                                <td height="29">Purity %:</td>
                                <td width="50%"><input name="purity" type="text" class="form-control" id="purity" value="<?php echo $ing['purity']?: '100'; ?>" /></td>
                                <td width="1%">&nbsp;</td>
                                <td colspan="3"><select name="solvent" id="solvent" class="form-control selectpicker" data-live-search="true" <?php if($ing['purity'] == 100){ ?>disabled<?php }?> >
                                  <option value="" selected disabled>Solvent</option>
                                  <option value="None">None</option>
								  <?php
								   $res_dil = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
								   while ($r_dil = mysqli_fetch_array($res_dil)){
									    $selected=($ing['solvent'] == $r_dil['name'])? "selected" : "";
										echo '<option '.$selected.' value="'.$r_dil['name'].'">'.$r_dil['name'].'</option>';
    							   }
								  ?>
                                </select>
                                </td>
                               </tr>
                              <tr>
                                <td height="29">Profile:</td>
                                <td colspan="5">
                                <select name="profile" id="profile" class="form-control selectpicker" data-live-search="true">
                                <option value="" selected></option>
                                <?php 	while ($row_ingProfiles = mysqli_fetch_array($res_ingProfiles)){ ?>
								<option data-content="<img class='img_ing_sel' src='<?=profileImg($row_ingProfiles['name'])?>'> <?php echo $row_ingProfiles['name'];?>" value="<?php echo $row_ingProfiles['name'];?>" <?php echo ($ing['profile']==$row_ingProfiles['name'])?"selected=\"selected\"":""; ?>></option>
								<?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="29">Type:</td>
                                <td colspan="5">
                                <select name="type" id="type" class="form-control selectpicker" data-live-search="true">
                                <option value="" selected></option>
                                <?php 	while ($row_ingTypes = mysqli_fetch_array($res_ingTypes)){ ?>
								<option value="<?php echo $row_ingTypes['name'];?>" <?php echo ($ing['type']==$row_ingTypes['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingTypes['name'];?></option>
								<?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="28">Strength:</td>
                                <td colspan="5">
                                <select name="strength" id="strength" class="form-control selectpicker" data-live-search="true">
                                <option value="" selected></option>
                                <?php while ($row_ingStrength = mysqli_fetch_array($res_ingStrength)){ ?>
								<option value="<?php echo $row_ingStrength['name'];?>" <?php echo ($ing['strength']==$row_ingStrength['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingStrength['name'];?></option>
								<?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="31">Olfactive family:</td>
                                <td colspan="5">
                                <select name="category" id="category" class="form-control selectpicker" data-live-search="true">
                                <option value="" selected></option>
                                <?php while ($row_ingCategory = mysqli_fetch_array($res_ingCategory)){ ?>
								<option data-content="<img class='img_ing_sel' src='<?php if($row_ingCategory['image']){ echo $row_ingCategory['image']; }else{ echo '/img/molecule.png';}?>'><?php echo $row_ingCategory['name'];?>" value="<?php echo $row_ingCategory['id'];?>" <?php echo ($ing['category']==$row_ingCategory['id'])?"selected=\"selected\"":""; ?>></option>
								<?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Physical State:</td>
                                <td colspan="5"><select name="physical_state" id="physical_state" class="form-control selectpicker">
                                  <option data-content="<img class='img_ing_sel' src='/img/liquid.png'> Liquid" value="1" <?php if($ing['physical_state']=="1") echo 'selected="selected"'; ?> ></option>
                                  <option data-content="<img class='img_ing_sel' src='/img/solid.png'> Solid" value="2" <?php if($ing['physical_state']=="2") echo 'selected="selected"'; ?> ></option>
                                </select></td>
                              </tr>                              <tr>
                                <td height="31" valign="top">Odor:</td>
                                <td colspan="3"><div id='TGSC'><input name="odor" id="odor" type="text" class="form-control" value="<?php echo $ing['odor']; ?>"/></div>
                                </td>
                                <?php if(file_exists('searchTGSC.php')){?>
                                <td width="2%">&nbsp;</td>
                                <td width="12%"><a href="javascript:search()" id="search">Search TGSC</a></td>
                                <?php } ?>
                              </tr>
                              <tr>
                                <td valign="top">Notes:</td>
                                <td colspan="5"><textarea name="notes" id="notes" cols="45" rows="5" class="form-control"><?php echo $ing['notes']; ?></textarea></td>
                              </tr>
                            </table>
              				</div>
             				<!--general tab-->
                            <?php if($ingID){?>
                            <div class="tab-pane fade" id="usage_limits">
       						   <h3>Usage &amp; Limits</h3>
                                 <hr>
                               <table width="100%" border="0">
                                <tr>
                                  <td width="15%" height="32">No usage limit:</td>
                                  <?php if($usageLimit = searchIFRA($ing['cas'],$ing['name'],null,$conn, $defCatClass)){ $chk = 'disabled'; }?>
                                  <td><input name="noUsageLimit" type="checkbox" <?php echo $chk; ?> id="noUsageLimit" value="1" <?php if($ing['noUsageLimit'] == '1'){; ?> checked="checked"  <?php } ?>/></td>
                                </tr>
                                <tr>
                                  <td height="32">Flavor use:</td>
                                  <td><input name="flavor_use" type="checkbox" id="flavor_use" value="1" <?php if($ing['flavor_use'] == '1'){; ?> checked="checked"  <?php } ?>/></td>
                                </tr>
                                <tr>
                                  <td height="32">Usage classification:</td>
                                  <td><?php if($rType = searchIFRA($ing['cas'],$ing['name'],'type',$conn, $defCatClass)){
			  								 	if($reason = searchIFRA($ing['cas'],$ing['name'],null,$conn, $defCatClass)){
													$reason = explode(' - ',$reason);
												}
										  echo $rType.' - '.$reason['1'];
									  }else{
								?>
                                <select name="usage_type" id="usage_type" class="form-control">
                                  <option value="1" <?php if($ing['usage_type']=="1") echo 'selected="selected"'; ?> >Recommendation</option>
                                  <option value="2" <?php if($ing['usage_type']=="2") echo 'selected="selected"'; ?> >Restriction</option>
                                  <option value="2" <?php if($ing['usage_type']=="3") echo 'selected="selected"'; ?> >Specification</option>
                                  <option value="2" <?php if($ing['usage_type']=="4") echo 'selected="selected"'; ?> >Prohibition</option>
                                </select>
                    			<?php } ?>
                    			</td>
                                 </tr>
                                </table>
                                <hr />
                              <table width="100%" border="0">
                               <?php for($i = 0; $i < $rows/$cols; $i++) { ?>
                                <tr <?php if($rType){ ?>class="<?php echo $usageStyle[$i % 2]; ?>" <?php }?>>
								<?php for($j=0; $j < $cols && $counter <= $rows; $j++, $counter++) {?>
                                  <td align="center"><a href="#" rel="tipsy" title="<?php echo $cats[$counter]['description'];?>">Cat<?php echo $cats[$counter]['name'];?> %:</a></td>
                                  <td><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat'.$cats[$counter]['name'])){
										$limit = explode(' - ',$limit);
										echo $limit['0'];
									}else{
									?>
                                    <input name="cat<?php echo $cats[$counter]['name'];?>" type="text" class="form-control" id="cat<?php echo $cats[$counter]['name'];?>" value="<?php echo $ing['cat'.$cats[$counter]['name']]; ?>" />
                                </td>
                                   <?php 
								   } 
								 } 
								?>
								</tr>
								<?php } ?>
						      </table>
   						  </div>
                          
                          <div class="tab-pane fade" id="supply">
                          	   <div id="msg_sup"></div>
                               <div id="fetch_suppliers"><div class="loader"></div></div>
                           </div>
                    	  
                           <div class="tab-pane fade" id="documents">
                          	   <div id="msg_docs"></div>
                               <div id="fetch_documents"><div class="loader"></div></div>
                           </div>
                           
                            <div class="tab-pane fade" id="tech_data">
          						 <h3>Techical Data</h3>
                                 <hr>
                             <table width="100%" border="0">
                              <tr>
                                <td width="20%">Tenacity:</td>
                                <td width="80%" colspan="3"><input name="tenacity" type="text" class="form-control" id="tenacity" value="<?php echo $ing['tenacity']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Flash Point:</td>
                                <td colspan="3"><input name="flash_point" type="text" class="form-control" id="flash_point" value="<?php echo $ing['flash_point']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Chemical Name:</td>
                                <td colspan="3"><input name="chemical_name" type="text" class="form-control" id="chemical_name" value="<?php echo $ing['chemical_name']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Molecular Formula:</td>
                                <td colspan="3">
								<?php
								 	if($chFormula = searchIFRA($ing['cas'],$ing['name'],'formula',$conn,$defCatClass)){
										echo $chFormula;
									}else{
								?>
                                <input name="formula" type="text" class="form-control" id="molecularFormula" value="<?php echo $ing['formula']; ?>">
                                <?php } ?>
                                </td>
                              </tr>
                              <tr>
                                <td>Log/P:</td>
                                <td colspan="3"><input name="logp" type="text" class="form-control" id="logp" value="<?php echo $ing['logp']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Soluble in:</td>
                                <td colspan="3"><input name="soluble" type="text" class="form-control" id="soluble" value="<?php echo $ing['soluble']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Molecular Weight:</td>
                                <td colspan="3"><input name="molecularWeight" type="text" class="form-control" id="molecularWeight" value="<?php echo $ing['molecularWeight']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Appearance:</td>
                                <td colspan="3"><input name="appearance" type="text" class="form-control" id="appearance" value="<?php echo $ing['appearance']; ?>"/></td>
                              </tr>
                            </table>
      						</div>
                            
              <div class="tab-pane fade" id="safety_info">
              <h3>Safety Information</h3>
              <hr />
               	<table width="100%" border="0">
                	<tr>
                  	<td width="20%">Pictograms:</td>
                    <td width="80%" colspan="3">
                    <select name="pictogram" id="pictogram" class="form-control selectpicker" data-live-search="true">
                    <option value="" disabled selected="selected">Choose Pictogram</option>
                    <?php foreach($pictograms as $pictogram){?>
                    <option data-content="<img class='img_ing_sel' src='/img/Pictograms/GHS0<?=$pictogram['code'];?>.png'><?=$pictogram['name'];?>" value="<?=$pictogram['code'];?>" <?php if($safety[0]['GHS']==$pictogram['code']) echo 'selected="selected"'; ?>></option>
                    <?php } ?>
                    </select>
                    </td>
                  </tr>
                </table>
              </div>
              
              <div class="tab-pane fade" id="note_impact">
              <h3>Note Impact</h3>
              <hr>
              <table width="100%" border="0">
                  <tr>
                    <td width="9%" height="40">Top:</td>
                    <td width="19%"><select name="impact_top" id="impact_top" class="form-control">
                      <option value="none" selected="selected">None</option>
					  <option value="100" <?php if($ing['impact_top']=="100") echo 'selected="selected"'; ?> >High</option>
					  <option value="50" <?php if($ing['impact_top']=="50") echo 'selected="selected"'; ?> >Medium</option>						
					  <option value="10" <?php if($ing['impact_top']=="10") echo 'selected="selected"'; ?> >Low</option>						
                    </select></td>
                    <td width="72%">&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="40">Heart:</td>
                    <td><select name="impact_heart" id="impact_heart" class="form-control">
                      <option value="none" selected="selected">None</option>
                      <option value="100" <?php if($ing['impact_heart']=="100") echo 'selected="selected"'; ?> >High</option>
                      <option value="50" <?php if($ing['impact_heart']=="50") echo 'selected="selected"'; ?> >Medium</option>
                      <option value="10" <?php if($ing['impact_heart']=="10") echo 'selected="selected"'; ?> >Low</option>
                    </select></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="40">Base:</td>
                    <td><select name="impact_base" id="impact_base" class="form-control">
                      <option value="none" selected="selected">None</option>
                      <option value="100" <?php if($ing['impact_base']=="100") echo 'selected="selected"'; ?> >High</option>
                      <option value="50" <?php if($ing['impact_base']=="50") echo 'selected="selected"'; ?> >Medium</option>
                      <option value="10" <?php if($ing['impact_base']=="10") echo 'selected="selected"'; ?> >Low</option>
                    </select></td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
			  </div>
              
              <div class="tab-pane fade" id="whereUsed">
                   <div id="fetch_whereUsed"><div class="loader"></div></div>
			  </div>
              
              <div class="tab-pane fade" id="tech_composition">
                   <div id="fetch_composition"><div class="loader"></div></div>
              </div>
             
              <?php if($settings['pubChem'] == '1' && $ing['cas']){?>
              <div class="tab-pane fade" id="pubChem">
				   <h3>Pub Chem Data</h3>
                   <hr>
                   <div id="pubChemData"> <div class="loader"></div> </div>
              </div>
              <?php } ?>
              <div class="tab-pane fade" id="privacy">
       			  <h3>Privacy</h3>
                  <hr>
                  <table width="100%" border="0">
                     <tr>
                       <td width="9%" height="31"><a href="#" rel="tipsy" title="If enabled, ingredient will automatically excluded if you choose to upload your ingredients to PV Online.">Private:</a></td>
                       <td width="91%" colspan="5"><input name="isPrivate" type="checkbox" id="isPrivate" value="1" <?php if($ing['isPrivate'] == '1'){; ?> checked="checked"  <?php } ?>/></td>
                     </tr>
                  </table>
              </div>
              <?php } ?>
               <!--tabs-->
                
                    <hr>
                    <p><input type="submit" name="save" id="save" class="btn btn-info" value="Save" /></p>

<!-- Modal Print-->
<div class="modal fade" id="printLabel" tabindex="-1" role="dialog" aria-labelledby="printLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="printLabel">Print Label for <?php echo $ing['name']; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="msg"></div>
          <form action="javascript:printLabel()" method="get" name="form1" target="_self" >
          	CAS#:
            <input class="form-control" name="cas" type="text" value="<?php echo $ing['cas']; ?>" />
            <p>
            Dilution %: 
            <input class="form-control" name="dilution" type="text" id="dilution" value="<?php echo $ing['purity']; ?>" />
            <p>
            Dilutant:
            <select class="form-control" name="dilutant" id="dilutant">
            <option selected="selected" value="">None</option>
            <?php
				$res_ing = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
				while ($r_ing = mysqli_fetch_array($res_ing)){
				echo '<option value="'.$r_ing['name'].'">'.$r_ing['name'].'</option>';
			}
			?>
            </select>
            </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary"  value="Print">
      </div>
     </form>
    </div>
  </div>
</div>
</div>

<!-- ADD COMPOSITION-->
<div class="modal fade" id="addComposition" tabindex="-1" role="dialog" aria-labelledby="addComposition" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addComposition">Add composition for <?php echo $ing['name']; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="inf"></div>
          <form action="javascript:addComposition()" method="get" name="form1" target="_self" >
            Name: 
            <input class="form-control" name="allgName" type="text" id="allgName" />
            <p>
            CAS: 
            <input class="form-control" name="allgCAS" type="text" id="allgCAS" />
            <p>
            EINECS: 
            <input class="form-control" name="allgEC" type="text" id="allgEC" />
            <p>            
            Percentage %:
            <input class="form-control" name="allgPerc" type="text" id="allgPerc" />
            </p>
            <div class="dropdown-divider"></div>
      <label>
         <input name="addToIng" type="checkbox" id="addToIng" value="1" />
        Add to ingredients
      </label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary"  value="Add">
      </div>
     </form>
    </div>
  </div>
</div>
</div>

<!-- ADD SUPPLIER-->
<div class="modal fade" id="addSupplier" tabindex="-1" role="dialog" aria-labelledby="addSupplier" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSupplier">Add supplier for <?php echo $ing['name']; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="supplier_inf"></div>
          <form action="javascript:addSupplier()" method="get" name="form1" target="_self" >
          <p>
            Name: 
            <select name="supplier_name" id="supplier_name" class="form-control selectpicker" data-live-search="true">
            <?php while ($row_ingSupplier = mysqli_fetch_array($res_ingSupplier)){ ?>
				<option value="<?=$row_ingSupplier['id']?>" data-vol="<?php if($ing['physical_state'] == '1'){ echo $row_ingSupplier['min_ml']; }elseif($ing['physical_state'] == '2'){ echo $row_ingSupplier['min_gr'];} ?>" ><?=$row_ingSupplier['name'];?></option>
			<?php	}	?>
            </select>
            </p>
            <p>
            URL: 
            <input class="form-control" name="supplier_link" type="text" id="supplier_link" />
            </p>
            <p>            
            Price (<?php echo $settings['currency']; ?>):
            <input class="form-control" name="supplier_price" type="text" id="supplier_price" />
            </p>
            <p>
            Size (<?php if($ing['physical_state'] == '1'){ echo 'ml'; }elseif($ing['physical_state'] == '2'){ echo 'grams'; }else{ echo $settings['mUnit']; }?>):
            <input class="form-control" name="supplier_size" type="text" id="supplier_size" value="10" />
            </p>
            <p>
            Manufacturer:
            <input class="form-control" name="supplier_manufacturer" type="text" id="supplier_manufacturer" />
            </p>
            <p>
            Batch:
            <input class="form-control" name="supplier_batch" type="text" id="supplier_batch" />
            </p>
			<p>
            Manufactured:
            <input class="form-control" name="manufactured" type="date" id="manufactured" />
            </p>
			<p>
            In stock:
            <input class="form-control" name="stock" type="text" id="stock" />
            </p>
            
            <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary"  value="Add">
      </div>
     </form>
    </div>
  </div>
</div>
</div>

<!-- ADD DOCUMENT-->
<div class="modal fade" id="addDoc" tabindex="-1" role="dialog" aria-labelledby="addDoc" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSupplier">Add document for <?php echo $ing['name']; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="doc_inf"></div>
          <form action="javascript:addDoc()" name="docform" id="docform">
            <p>
            Document name: 
            <input class="form-control" name="doc_name" type="text" id="doc_name" />
            </p>
            <p>            
            Notes:
            <input class="form-control" name="doc_notes" type="textarea" id="doc_notes" />
            </p>
            <p>
            File:
            <input type="file" name="doc_file" id="doc_file" class="form-control" />
            </p>            
            <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="doc_upload" value="Upload">
      </div>
     </form>
    </div>
  </div>
</div>
</div>

<!-- Modal Clone-->
<div class="modal fade" id="cloneIng" tabindex="-1" role="dialog" aria-labelledby="cloneIng" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cloneIng">Clone ingredient <?php echo $ing['name']; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="clone_msg"></div>
       <form action="javascript:cloneIng()" method="get" name="form1" target="_self" >
          	Name
            <input class="form-control" name="cloneIngName" id="cloneIngName" type="text" value="" />            
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary"  value="Clone">
      </div>
      </form>
    </div>
  </div>
</div>

</div>
<script type="text/javascript" language="javascript">

$("#supplier_name").change(function () {
    vol = $(this).children(':selected').data('vol');
    $("#supplier_size").focus().val(vol);    
});
//Clone
function cloneIng() {	  
	$.ajax({ 
		url: 'update_data.php', 
		type: 'GET',
		data: {
			action: 'clone',
			new_ing_name: $("#cloneIngName").val(),
			old_ing_name: '<?=$ing['name'];?>',
			ing_id: '<?=$ing['id'];?>'
			},
		dataType: 'html',
		success: function (data) {
			$('#clone_msg').html(data);
		}
	  });
};

function getPrice(supplier, size, ingSupplierID) {
	$('#ingMsg').html('<div class="alert alert-info alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Please wait...</strong></div>');
	$('#' + ingSupplierID).html('<img src="/img/loading.gif"/>');
	$.ajax({ 
		url: 'update_data.php', 
		type: 'POST',
		data: {
			ingSupplier: 'getPrice',
			sLink: supplier,
			size: size,
			ingSupplierID: ingSupplierID,
			ingID: '<?=$ing['id'];?>'
			},
		dataType: 'html',
		success: function (data) {
			$('#ingMsg').html(data);
			reload_data();
		}
	  });
};

function deleteComposition(allgID) {	  
	$.ajax({ 
		url: 'update_data.php', 
		type: 'GET',
		data: {
			composition: 'delete',
			allgID: allgID,
			ing: '<?=$ing['name'];?>'
			},
		dataType: 'html',
		success: function (data) {
			//$('#msg').html(data);
			reload_data();
		}
	  });
};

function addComposition() {	  
	$.ajax({ 
		url: 'update_data.php', 
		type: 'GET',
		data: {
			composition: 'add',
			allgName: $("#allgName").val(),
			allgPerc: $("#allgPerc").val(),
			allgCAS: $("#allgCAS").val(),
			allgEC: $("#allgEC").val(),	
			addToIng: $("#addToIng").is(':checked'),				
			ing: '<?=$ing['name'];?>'
			},
		dataType: 'html',
		success: function (data) {
			$('#inf').html(data);
			$("#allgName").val('');
			$("#allgCAS").val('');
			$("#allgEC").val('');
			$("#allgPerc").val('');
			reload_data();
		}
	  });
};

function addSupplier() {	  
	$.ajax({ 
		url: 'update_data.php', 
		type: 'POST',
		data: {
			ingSupplier: 'add',
			supplier_id: $("#supplier_name").val(),
			supplier_link: $("#supplier_link").val(),
			supplier_size: $("#supplier_size").val(),	
			supplier_price: $("#supplier_price").val(),				
			supplier_manufacturer: $("#supplier_manufacturer").val(),
			supplier_batch: $("#supplier_batch").val(),
			manufactured: $("#manufactured").val(),
			stock: $("#stock").val(),

			ingID: '<?=$ing['id'];?>'
			},
		dataType: 'html',
		success: function (data) {
			$('#supplier_inf').html(data);
			$("#supplier_batch").val('');
			$("#supplier_link").val('');
			$("#supplier_size").val('');
			$("#supplier_price").val('');
			$("#supplier_manufacturer").val('');
			reload_data();
		}
	  });
};

function deleteSupplier(sID) {	  
	$.ajax({ 
		url: 'update_data.php', 
		type: 'GET',
		data: {
			ingSupplier: 'delete',
			sID: sID,
			ingID: '<?=$ing['id'];?>'
			},
		dataType: 'html',
		success: function (data) {
			$('#msg_sup').html(data);
			reload_data();
		}
	  });
};

function deleteDoc(id) {	  
	$.ajax({ 
		url: 'update_data.php', 
		type: 'GET',
		data: {
			doc: 'delete',
			id: id,
			ingID: '<?=$ing['id'];?>'
			},
		dataType: 'html',
		success: function (data) {
			$('#msg_doc').html(data);
			reload_data();
		}
	  });
};

function prefSID(sID, status) {	  
	$.ajax({ 
		url: 'update_data.php', 
		type: 'GET',
		data: {
			ingSupplier: 'preferred',
			sID: sID,
			status: status,
			ingID: '<?=$ing['id'];?>'
			},
		dataType: 'html',
		success: function (data) {
			//$('#msg').html(data);
			reload_data();
		}
	  });
};
$(document).ready(function() {
	$('#save').click(function() {
								  
			$.ajax({ 
				url: 'update_data.php', 
				type: 'POST',
				data: {
					manage: 'ingredient',
					ingID: '<?=$ing['id'];?>',
					name: $("#name").val(),
					INCI: $("#INCI").val(),
					cas: $("#cas").val(),
					reach: $("#reach").val(),
					fema: $("#fema").val(),
					type: $("#type").val(),
					strength: $("#strength").val(),
					category: $("#category").val(),
					profile: $("#profile").val(),
					tenacity: $("#tenacity").val(),
					formula: $("#formula").val(),
					chemical_name: $("#chemical_name").val(),
					flash_point: $("#flash_point").val(),
					appearance: $("#appearance").val(),
					solvent: $("#solvent").val(),
					notes: $("#notes").val(),
					odor: $("#odor").val(),
					purity: $("#purity").val(),
					soluble: $("#soluble").val(),
					logp: $("#logp").val(),
					type: $("#type").val(),
					molecularWeight: $("#molecularWeight").val(),
					physical_state: $("#physical_state").val(),
	
					<?php foreach ($cats as $cat) {?>
					cat<?php echo $cat['name'];?>: $("#cat<?php echo $cat['name'];?>").val(),
					<?php } ?>
					
					manufacturer: $("#manufacturer").val(),
					impact_top: $("#impact_top").val(),
					impact_base: $("#impact_base").val(),
					impact_heart: $("#impact_heart").val(),
					usage_type: $("#usage_type").val(),
					
					isAllergen: $("#isAllergen").is(':checked'),
					flavor_use: $("#flavor_use").is(':checked'),
					noUsageLimit: $("#noUsageLimit").is(':checked'),
					isPrivate: $("#isPrivate").is(':checked'),
	
					pictogram: $("#pictogram").val(),

					<?php if($ing['name']){?>
					ing: '<?=$ing['name'];?>'
					<?php } ?>
					},
				dataType: 'html',
				success: function (data) {
					$('#ingMsg').html(data);
					reload_overview();
					if ($('#name').val()) {
						window.location = 'mgmIngredient.php?id=' + btoa($('#name').val());
					}
				}
	});
})
	
$("#doc_upload").click(function(){
	$("#doc_inf").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	$("#doc_upload").prop("disabled", true);
    $("#doc_upload").prop('value', 'Please wait...');
		
	var fd = new FormData();
    var files = $('#doc_file')[0].files;
    var doc_name = $('#doc_name').val();
    var doc_notes = $('#doc_notes').val();

    if(files.length > 0 ){
		fd.append('doc_file',files[0]);

			$.ajax({
              url: 'upload.php?type=1&doc_name=' + btoa(doc_name) + '&doc_notes=' + btoa(doc_notes) + '&id=<?=$ing['id'];?>',
              type: 'POST',
              data: fd,
              contentType: false,
              processData: false,
			  		cache: false,
              success: function(response){
                 if(response != 0){
                    $("#doc_inf").html(response);
					$("#doc_upload").prop("disabled", false);
        			$("#doc_upload").prop('value', 'Upload');
					reload_data();
                 }else{
                    $("#doc_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
					$("#doc_upload").prop("disabled", false);
        			$("#doc_upload").prop('value', 'Upload');
                 }
              },
           });
        }else{
			$("#doc_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
			$("#doc_upload").prop("disabled", false);
   			$("#doc_upload").prop('value', 'Upload');
        }
    });	
});


</script>
</div>
</div>
</body>
</html>
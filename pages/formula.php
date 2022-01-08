<?php 
if (!defined('pvault_panel')){ die('Not Found');}
require_once(__ROOT__.'/func/arrFilter.php');
require(__ROOT__.'/func/get_formula_notes.php');
$id = mysqli_real_escape_string($conn, $_GET['id']);

$mid = mysqli_fetch_array(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE id = '$id'"));
if(($fid = $mid['fid']) == FALSE){
	echo 'Formula doesn\'t exist';
	exit;
}
$f_name =  base64_decode($fid);

if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulas WHERE fid = '$fid'"))){
	$legend = 1;
}
$cat_details = mysqli_fetch_array(mysqli_query($conn, "SELECT description FROM IFRACategories WHERE name = '4'"));

$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected,catClass FROM formulasMetaData WHERE fid = '$fid'"));
$img = mysqli_fetch_array(mysqli_query($conn, "SELECT docData FROM documents WHERE ownerID = '$id' AND type = '2'"));

$formula_q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid'");
	while ($formula = mysqli_fetch_array($formula_q)){
		$form[] = $formula;
	}
	
						
	foreach ($form as $formula){
		$top_ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name AS ing,category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Top' AND category IS NOT NULL"));
		$heart_ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name AS ing,category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Heart' AND category IS NOT NULL"));
		$base_ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name AS ing,category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Base' AND category IS NOT NULL"));
	
		$top_cat = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingCategory WHERE id = '".$top_ing['category']."' AND image IS NOT NULL"));
		$heart_cat = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingCategory WHERE id = '".$heart_ing['category']."' AND image IS NOT NULL"));
		$base_cat = mysqli_fetch_array(mysqli_query($conn, "SELECT  name FROM ingCategory WHERE id = '".$base_ing['category']."' AND image IS NOT NULL"));
	
		$top[] = array_merge($top_cat,$top_ing);
		$heart[] = array_merge($heart_cat,$heart_ing);
		$base[] = array_merge($base_cat,$base_ing);

	}
$top_cat = array_filter($top);
$heart_cat = array_filter($heart);
$base_cat = array_filter($base);


$top_ex = get_formula_excludes($conn, $fid, 'top');
$heart_ex = get_formula_excludes($conn, $fid, 'heart');
$base_ex = get_formula_excludes($conn, $fid, 'base');
?>
<style>
.mfp-iframe-holder .mfp-content {
    line-height: 0;
    width: 1450px;
    max-width: 1450px; 
	height: 1300px;
}
</style>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
			  <?php if($img['docData']){?><div class="img-formula"><img class="img-perfume" src="<?=$img['docData']?>"/></div><?php } ?>
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=Formula&id=<?=$id?>"><?=$f_name?></a><span class="m-1"><?php if($meta['isProtected']){?><a class="fas fa-lock" href="javascript:isProtected('false')"><?php }else{ ?><a class="fas fa-unlock" href="javascript:isProtected('true')"> <?php } ?></a></span></h2>
              <h5 class="m-1 text-primary"><span><a href="#" rel="tipsy" data-placement="right" title="<?=$cat_details['description']?>"><?=ucfirst($meta['catClass'])?></a></span></h5>
              <h5 class="m-1 text-primary"><a href="pages/getFormMeta.php?id=<?php echo $meta['id'];?>" class="popup-link">Details</a></h5>
            </div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
          <li class="active"><a href="#main_formula" role="tab" data-toggle="tab"><icon class="fa fa-bong"></icon> Formula</a></li>
    	  <li><a href="#impact" role="tab" data-toggle="tab"><i class="fa fa-magic"></i> Notes Impact</a></li>
          <li><a href="#pyramid" role="tab" data-toggle="tab"><i class="fa fa-table"></i> Olfactory Pyramid</a></li>
          <li><a href="#summary" role="tab" data-toggle="tab"><i class="fa fa-cubes"></i> Notes Summary</a></li>
        </ul>
                     
        <div class="tab-content">
          <div class="tab-pane fade active in tab-content" id="main_formula">

            <div class="card-body">
           <div id="msgInfo"></div>
              <div>
                  <tr>
                    <th colspan="6">
                    <?php if($meta['isProtected'] == FALSE){?>
                      <form action="javascript:addING()" enctype="multipart/form-data" name="form1" id="form1">
                         <table width="100%" border="0" class="table">
                                    <tr>  
                                         <td>
                                         <select name="ingredient" id="ingredient" class="form-control selectpicker" data-live-search="true">
                                         <option value="" selected disabled>Ingredient</option>
                                         <?php
										 	$res_ing = mysqli_query($conn, "SELECT id, name, profile, chemical_name, INCI, CAS FROM ingredients ORDER BY name ASC");
										 	while ($r_ing = mysqli_fetch_array($res_ing)){
												if($r_ing['INCI']){
										?>
											<option data-subtext="<?=$r_ing['INCI']?>" value="<?=$r_ing['name']?>"><?=$r_ing['name']?> (<?=$r_ing['CAS']?>)</option>
										<?php 	}else{ ?>
											<option value="<?=$r_ing['name']?>"><?=$r_ing['name']?> (<?=$r_ing['CAS']?>)</option>
										<?php
												} 
											}
										?>
                                         </select>                                         
                                         </td>
                                         <td><input type="text" name="concentration" id="concentration" placeholder="Purity %" class="form-control" /></td>
                                      <td>
                                         <select name="dilutant" id="dilutant" class="form-control">
                                         <option value="" selected disabled>Dilutant</option>
                                         <option value="none">None</option>
                                         <?php
										 	$res_dil = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
										 	while ($r_dil = mysqli_fetch_array($res_dil)){
												echo '<option value="'.$r_dil['name'].'">'.$r_dil['name'].'</option>';
											}
										 ?>
                                         </select>
                                      </td>
                                         <td><input type="text" name="quantity" id="quantity" placeholder="Quantity" class="form-control" /></td>  
                                         <td><input type="submit" name="add" id="add" class="btn btn-info" value="Add" /> </td>  
                                    </tr>  
                        </table>  
                      </form>
                      <?php } ?>
                    </th>
                    </tr>
                <div id="fetch_formula">
                	<div class="loader-center">
                		<div class="loader"></div>
                    	<div class="loader-text"></div>
                	</div>
                </div>
                <?php if($legend){ ?>
                <div id="legend">
                <p></p>
                <p>*Values in: <strong class="alert alert-danger">red</strong> exceeds usage level,   <strong class="alert alert-warning">yellow</strong> have no usage level set,   <strong class="alert alert-success">green</strong> are within usage level, <strong class="alert alert-info">blue</strong> are exceeding recommended usage level</p>
                </div>
                <?php } ?>
            </div>
          </div>
        </div>
      <!--Formula-->
      
          <div class="tab-pane fade" id="impact">
            <div class="card-body">
		        <div id="fetch_impact"><div class="loader"></div></div>
			</div>            
          </div>
      
          <div class="tab-pane fade" id="pyramid">
            <div class="card-body">
		        <div id="fetch_pyramid"><div class="loader"></div></div>
			</div>            
          </div>
      
          <div class="tab-pane fade" id="summary">
            <div class="card-body">
		        <div id="fetch_summary"><div class="loader"></div></div>
                <?php if($legend){ ?>
                <div id="share">
               	  <p><a href="#" data-toggle="modal" data-target="#conf_view">Configure view</a></p>
               	  <p>To include this page in your web site, copy this line and paste it into your html code:</p>
           	    <p><pre>&lt;iframe src=&quot;<?=$_SERVER['REQUEST_SCHEME']?>://<?=$_SERVER['SERVER_NAME']?>/pages/viewSummary.php?id=<?=$fid?>&quot; title=&quot;<?=$f_name?>&quot;&gt;&lt;/iframe&gt;</pre></p>
                	<p>For documentation and parameterisation please refer to: <a href="https://www.jbparfum.com/knowledge-base/share-formula-notes/" target="_blank">https://www.jbparfum.com/knowledge-base/share-formula-notes/</a></p>
                </div>
                <?php } ?>
			</div>            
          </div>
                    
        </div>
       </div>         
     </div><!--tabs-->
   </div>
  </div>
  
<!--Configure View-->

<div class="modal fade" id="conf_view" tabindex="-1" role="dialog" aria-labelledby="conf_view" aria-hidden="true">
  <div class="modal-dialog modal-conf-view" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="conf_view">Choose which notes will be displayed</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
   	    <div id="confViewMsg"></div>
          <form action="javascript:update_view()" id="form1">   
           <div class="conf_tbl">
            <table width="100%" border="0">
              <tr>
                <td colspan="2"><strong>Top notes</strong><hr /></td>
              </tr>
              <?php foreach ($top_cat as $x){
						if (!is_numeric(array_search($x['name'],$top_ex ))){
				?>
              <tr>
				<td width="54%" height="29" ex_top_ing_name="<?=$x['name']?>"><?=$x['name']?></td>
                <td width="46%"><input name="ex_top_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" checked="checked" /></td>
              </tr>
              <?php }else{ ?>
			  <tr>
				<td width="54%" ex_top_ing_name="<?=$x['name']?>"><?=$x['name']?></td>
                <td width="46%"><input name="ex_top_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" /></td>
              </tr>
			 <?php 
			 	}
			  }
			  ?>
             </table>
            </div>
             
           <div class="conf_tbl">
            <table width="100%" border="0">
              <tr>
                <td colspan="2"><p><strong>Heart notes</strong></p><hr /></td>
              </tr>
              <?php foreach ($heart_cat as $x){
						if (!is_numeric(array_search($x['name'],$heart_ex ))){
			   ?>
              <tr>
				<td width="40%" height="29"><?=$x['name']?></td>
                <td width="51%"><input name="ex_heart_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" checked="checked" /></td>
              </tr>
              <?php }else{ ?>
              <tr>
				<td><?=$x['name']?></td>
                <td width="51%"><input name="ex_heart_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" /></td>
              </tr>
              <?php 
			 	}
			  }
			  ?>
             </table> 
            </div>

            <div class="conf_tbl">
             <table width="100%" border="0">
              <tr>
                <td colspan="2"><p><strong>Base notes</strong></p><hr /></td>
              </tr>
              <?php foreach ($base_cat as $x){
						if (!is_numeric(array_search($x['name'],$base_ex ))){
			  ?>
              <tr>
				<td width="40%" height="29"><?=$x['name']?></td>
                <td width="60%"><input name="ex_base_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" checked="checked" /></td>
              </tr>
             <?php }else{ ?>
              <tr>
				<td><?=$x['name']?></td>
                <td width="60%"><input name="ex_base_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" /></td>
              </tr>
              <?php 
			 	}
			  }
			  ?>
            </table>
            </div>
            <table width="100%" border="0">
              <tr>
                <td>            
  					<div class="modal-footer">
     	  				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
	 	  				<input type="submit" name="button" class="btn btn-primary" id="btnUpdate" value="Save">
   		  			</div>
           			</td>
    			</tr>
			</table>
          </form>
    </div>
  </div>
  </div>
</div>

<script type="text/javascript" language="javascript" >
//$(document).ready(function(){
 //UPDATE PURITY
$('#ingredient').on('change', function(){
$.ajax({ 
    url: 'pages/getIngInfo.php', 
	type: 'get',
    data: {
		filter: "purity",
		name: $(this).val()
		},
	dataType: 'html',
    success: function (data) {
	  $('#concentration').val(data);
    }
  });

$.ajax({ 
    url: 'pages/getIngInfo.php', 
	type: 'get',
    data: {
		filter: "solvent",
		name: $(this).val()
		},
	dataType: 'html',
    success: function (data) {
	  $('#dilutant').val(data);
    }
  });

});

//DILUTION
$('#formula_data').editable({
	container: 'body',
	selector: 'td.dilutant',
	type: 'POST',
	emptytext: "",
	emptyclass: "",
  	url: "pages/update_data.php?formula=<?php echo $f_name; ?>",
    source: [
			 <?php
				$res_ing = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
				while ($r_ing = mysqli_fetch_array($res_ing)){
				echo '{value: "'.$r_ing['name'].'", text: "'.$r_ing['name'].'"},';
			}
			?>
          ],
	dataType: 'json',
    
});

//});
//Add ingredient
function addING(ingName,ingID) {	  
$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "addIng",
		fname: "<?php echo $f_name; ?>",
		quantity: $("#quantity").val(),
		concentration: $("#concentration").val(),
		ingredient: $("#ingredient").val(),
		dilutant: $("#dilutant").val()
		},
	dataType: 'html',
    success: function (data) {
        if ( data.indexOf("Error") > -1 ) {
			$('#msgInfo').html(data); 
		}else{
			$('#msgInfo').html(data);
			fetch_formula();
			fetch_impact();
			fetch_pyramid();
		}
    }
  });
};

function isProtected(status) {
  $.ajax({ 
		url: 'pages/update_data.php', 
		type: 'GET',
		data: {
			protect: '<?=$fid?>',
			isProtected: status,
			},
		dataType: 'html',
		success: function (data) {
			$('#msgInfo').html(data);
	        location.reload();
		}
	  });
};
  
$('#csv').on('click',function(){
  $("#formula").tableHTMLExport({
		type:'csv',
		filename:'<?php echo $f_name; ?>.csv',
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

function fetch_formula(){
$.ajax({ 
    url: 'pages/b.php', 
	type: 'GET',
    data: {
		id: "<?=$id?>"
		},
	dataType: 'html',
		success: function (data) {
			$('#fetch_formula').html(data);
		}
	});
}

fetch_formula();

function fetch_pyramid(){
	$.ajax({ 
		url: 'pages/viewPyramid.php', 
		type: 'get',
		data: {
			formula: "<?=$id?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_pyramid').html(data);
		}
	});
}

fetch_pyramid();

function fetch_impact(){
	$.ajax({ 
		url: 'pages/impact.php', 
		type: 'get',
		data: {
			id: "<?php echo $fid; ?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_impact').html(data);
		}
	});
}

fetch_impact();

function fetch_summary(){
$.ajax({ 
    url: 'pages/viewSummary.php', 
	type: 'get',
    data: {
		id: "<?=$fid?>"
		},
	dataType: 'html',
		success: function (data) {
			$('#fetch_summary').html(data);
		}
	});
}

fetch_summary();

function update_view(){
	
	$('.ex_ing').each(function(){
		$.ajax({ 
			url: 'pages/manageFormula.php', 
			type: 'get',
			data: {
				fid: "<?=urlencode($fid)?>",
				manage_view: '1',
				ex_status: $("#" + $(this).val()).is(':checked'),
				ex_ing: $(this).val()
				},
			dataType: 'html',
				success: function (data) {
					$('#confViewMsg').html(data);
					fetch_summary();

				}
		});
	});

}
</script>

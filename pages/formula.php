<?php 
if (!defined('pvault_panel')){ die('Not Found');}
require_once(__ROOT__.'/func/arrFilter.php');
require(__ROOT__.'/func/get_formula_notes.php');
$id = mysqli_real_escape_string($conn, $_GET['id']);

$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT fid,name FROM formulasMetaData WHERE id = '$id'"));
if($meta['fid'] == FALSE){
	echo 'Formula doesn\'t exist';
	exit;
}

$f_name = $meta['name'];
$fid = $meta['fid'];

$cat_details = mysqli_fetch_array(mysqli_query($conn, "SELECT description FROM IFRACategories WHERE name = '4'"));

$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,name,isProtected,catClass FROM formulasMetaData WHERE fid = '$fid'"));
$img = mysqli_fetch_array(mysqli_query($conn, "SELECT docData FROM documents WHERE ownerID = '$id' AND type = '2'"));

$formula_q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid'");
while ($formula = mysqli_fetch_array($formula_q)){
	$form[] = $formula;
}

$ingredients = mysqli_query($conn, "SELECT id, name, chemical_name, INCI, CAS FROM ingredients ORDER BY name ASC");
while ($ingredient = mysqli_fetch_array($ingredients)){
	$ing[] = $ingredient;
}

if($form[0]['ingredient']){
	$legend = 1;
}

?>

<link href="/css/select2.css" rel="stylesheet">
<script src="/js/select2.js"></script> 
<script src="/js/dataTables.rowsGroup.js"></script>

<style>
.mfp-iframe-holder .mfp-content {
    line-height: 0;
    width: 1500px;
    max-width: 1500px; 
	height: 1300px;
}
</style>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
	<div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
            
			  <table width="100%" border="0">
			    <tr>
			      <th width="75%" class="left" scope="col"><h2 class="m-0 font-weight-bold text-primary"><a href="javascript:reload_formula_data()"><div id="formula_name"><?=$f_name?></div></a><span class="m-1"><?php if($meta['isProtected']){?><a class="fas fa-lock" href="javascript:setProtected('false')"><?php }else{ ?><a class="fas fa-unlock" href="javascript:setProtected('true')"> <?php } ?></a></span></h2>
              <h5 class="m-1 text-primary"><span><a href="#" rel="tip" data-placement="right" title="<?=$cat_details['description']?>"><?=ucfirst($meta['catClass'])?></a></span></h5>&nbsp;</th>
			      <th width="21%" scope="col"><div id="formula_desc"><img src="/img/loading.gif"/></div></th>
			      <th width="4%" scope="col"><div class="img-formula"><img class="img-perfume" src="<?=$img['docData']?:'/img/ICO_TR.png';?>"/></div></th>
		        </tr>
		      </table>
          
            </div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
          <li class="active"><a href="#main_formula" id="formula_tab" role="tab" data-toggle="tab"><icon class="fa fa-bong"></icon> Formula</a></li>
    	  <li><a href="#impact" id="impact_tab" role="tab" data-toggle="tab"><i class="fa fa-magic"></i> Notes Impact</a></li>
          <li><a href="#pyramid" id="pyramid_tab" role="tab" data-toggle="tab"><i class="fa fa-table"></i> Olfactory Pyramid</a></li>
          <li><a href="#summary" id="summary_tab" role="tab" data-toggle="tab"><i class="fa fa-cubes"></i> Notes Summary</a></li>
          <li><a href="#ingRep" id="reps_tab" role="tab" data-toggle="tab"><i class="fa fa-exchange-alt"></i> Replacements</a></li>
          <li><a href="#attachments" id="attachments_tab" role="tab" data-toggle="tab"><i class="fa fa-paperclip"></i> Attachments</a></li>
          
        </ul>
                     
        <div class="tab-content">
          <div class="tab-pane fade active in tab-content" id="main_formula">

          <div class="card-body">
          <div id="msgInfo"></div>
          <?php if($meta['isProtected'] == FALSE){?>
	      <div id="add_ing">
           	<div class="form-group">
          	 	<div class="col-md-4 buffer">
				   <input name="ingredient" id="ingredient" class="pv-form-control main-ingredient"></input>
                </div>
                <div class="col-md-2 buffer">
					<input type="text" name="concentration" id="concentration" placeholder="Purity %" class="form-control" />
                </div>
                <div class="col-md-2 buffer">
                	<select name="dilutant" id="dilutant" class="form-control selectpicker" data-live-search="true">
                    	<option value="" selected disabled>Dilutant</option>
                        <option value="none">None</option>
                        <?php
                        $res_dil = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
                        while ($r_dil = mysqli_fetch_array($res_dil)){
                        	echo '<option value="'.$r_dil['name'].'">'.$r_dil['name'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 buffer">
                	<input type="text" name="quantity" id="quantity" placeholder="Quantity" class="form-control" />				
                </div>
                <div class="col-md-2 buffer">
                	<input type="submit" name="add" id="add-btn" class="btn btn-info" value="Add" /> </td>  
                </div>  
            </div>
          </div>

          <?php } ?>

          <div id="fetch_formula">
          	<div class="loader-center">
            	<div class="loader"></div>
               	<div class="loader-text"></div>
            </div>
          </div>
          <?php if($legend){ ?>
          <div id="legend">
          	<p></p>
            <p>*Values in: <strong class="alert alert-danger">red</strong> exceeds usage level,   <strong class="alert alert-warning">yellow</strong> Specification,   <strong class="alert alert-success">green</strong> are within usage level, <strong class="alert alert-info">blue</strong> are exceeding recommended usage level</p>
            </div>
          <?php } ?>
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
    
        <div class="tab-pane fade" id="ingRep">
            <div class="card-body">
                <div id="fetch_replacements"><div class="loader"></div></div>
            </div>            
        </div>
        
        <div class="tab-pane fade" id="attachments">
            <div class="card-body">
                <div id="fetch_attachments"><div class="loader"></div></div>
            </div>            
        </div>
                        
      </div>
     </div>         
   </div><!--tabs-->
 </div>
</div>
  


<script src="/js/select2-v3-ingredient.js"></script>
<script>
document.title = "<?=$meta['name'].' - '.$product?>";
var myFID = "<?=$fid?>";

$("#concentration").prop("disabled", true); 
$("#dilutant").prop("disabled", true);
$('#quantity').prop("disabled", true);


//Add ingredient
$('#add_ing').on('click', '[id*=add-btn]', function () {
	
	$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		data: {
			action: "addIng",
			fid: "<?=$fid?>",
			quantity: $("#quantity").val(),
			concentration: $("#concentration").val(),
			ingredient: $("#ingredient").val(),
			dilutant: $("#dilutant").val()
			},
		dataType: 'json',
		success: function (data) {
			if ( data.success ) {
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				reload_formula_data();
			} else {
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>' + data.error + '</strong></div>';
			}
			$('#msgInfo').html(msg);
		}
		
	  });
				
});

function setProtected(status) {
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

function fetch_formula(){
$.ajax({ 
    url: 'pages/viewFormula.php', 
	type: 'GET',
    data: {
		id: "<?=$id?>",
		"search": "<?=$_GET['search']?>"
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
		type: 'GET',
		data: {
			formula: "<?=$id?>",
			fid: "<?=$fid?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_pyramid').html(data);
		}
	});
}


function fetch_impact(){
	$.ajax({ 
		url: 'pages/impact.php', 
		type: 'GET',
		data: {
			id: "<?php echo $fid; ?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_impact').html(data);
		}
	});
}


function fetch_summary(){
$.ajax({ 
    url: 'pages/viewSummary.php', 
	type: 'GET',
    data: {
		id: "<?=$fid?>"
		},
	dataType: 'html',
		success: function (data) {
			$('#fetch_summary').html(data);
		}
	});
}


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

				}
		});
	});

}


function fetch_replacements(){
	$.ajax({ 
		url: '/pages/views/formula/replacements.php', 
		type: 'POST',
		data: {
			fid: "<?=$fid?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_replacements').html(data);
		}
	});
}

function fetch_attachments(){
	$.ajax({ 
		url: '/pages/views/formula/attachments.php', 
		type: 'POST',
		data: {
			id: "<?=$meta['id']?>"
			},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_attachments').html(data);
		}
	});
}

</script>
<script src="/js/formula.tabs.js"></script>

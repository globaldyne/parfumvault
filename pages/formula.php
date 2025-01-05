<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$id = (int)$_GET['id'] ?? null;

if (!$id) {
    $error_msg = "Invalid formula ID provided.";
    require_once(__ROOT__ . '/pages/error.php');
    return;
}

if($meta = mysqli_fetch_array(mysqli_query($conn, "SELECT fid,name FROM formulasMetaData WHERE id = '$id'"))){

	$f_name = $meta['name'];
	$fid = $meta['fid'];
	$cat_details = mysqli_fetch_array(mysqli_query($conn, "SELECT description FROM IFRACategories WHERE name = '4'"));
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,name,isProtected,catClass FROM formulasMetaData WHERE fid = '$fid'"));
	$img = mysqli_fetch_array(mysqli_query($conn, "SELECT docData FROM documents WHERE ownerID = '$id' AND type = '2'"));
	
	$formula_q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid'");
	while ($formula = mysqli_fetch_array($formula_q)){
		$form[] = $formula;
	}
	
	if($form[0]['ingredient']){
		$legend = 1;
	}
}
?>

<link href="/css/select2.css" rel="stylesheet">
<script src="/js/select2.js"></script> 
<script src="/js/dataTables.rowsGroup.js"></script>

<style>
.mfp-iframe-holder .mfp-content {
    line-height: 0;
    width: 80%;
    max-width: 100%; 
	height: 1300px;
}
</style>
<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php');
if(!$fid){
	$error_msg = "The requested formula id: ".$id.", cannot be found";
	require_once(__ROOT__.'/pages/error.php');
	return;
}
?>
	<div class="container-fluid">    
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
            
                <div class="d-flex w-100">
                  <div class="flex-grow-1 text-start" style="width: 75%;">
                    <h2 class="m-0 fw-bold text-body">
                      <a href="#">
                        <div id="formula_name" class="text-body-emphasis"><?=$f_name ?: 'Unnamed' ?></div>
                      </a>
                      <span class="m-1">
                        <div id="lock_status">
                          <?php if ($meta['isProtected']) { ?>
                            <a class="fas fa-lock text-body-emphasis" href="javascript:setProtected('false')"></a>
                          <?php } else { ?>
                            <a class="fas fa-unlock text-body-emphasis" href="javascript:setProtected('true')"></a>
                          <?php } ?>
                        </div>
                      </span>
                    </h2>
                    <h5 class="m-1 text-primary">
                      <span>
                        <a href="#" rel="tip" class="text-secondary-emphasis" data-bs-placement="right" title="<?= $cat_details['description'] ?>">
                          <?= ucfirst($meta['catClass']) ?>
                        </a>
                        <div id="max_usage" class="text-secondary-emphasis"></div>
                      </span>
                    </h5>
                  </div>
                  <div class="flex-shrink-0" style="width: 21%;">
                    <div id="formula_desc">
                      <img src="/img/loading.gif" alt="Loading"/>
                    </div>
                  </div>
                  <div class="flex-shrink-0">
                    <div id="img-formula"><img src="/img/loading.gif" alt="Loading"/></div>
                  </div>
                </div>

          		<div id="compliance"></div>
            </div>
        <!-- Nav tabs -->
		<ul class="nav nav-tabs mb-3" role="tablist">
          <li class="nav-item active" role="presentation">
          	<a href="#main_formula" id="formula_tab" class="nav-link active" aria-selected="true" role="tab" data-bs-toggle="tab"><i class="fa fa-bong mx-2"></i>Formula</a>
          </li>
    	  <li class="nav-item" role="presentation">
          	<a href="#analysis" id="analysis_tab"class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-magnifying-glass-chart mx-2"></i>Analysis</a>
          </li>
    	  <li class="nav-item" role="presentation">
          	<a href="#usage" id="usage_tab"class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-bong mx-2"></i>Usage Data</a>
          </li>          
    	  <li class="nav-item" role="presentation">
          	<a href="#impact" id="impact_tab"class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-magic mx-2"></i>Notes Impact</a>
          </li>
          <li class="nav-item" role="presentation">
          	<a href="#pyramid" id="pyramid_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-table mx-2"></i>Olfactory Pyramid</a>
          </li>
          <li class="nav-item" role="presentation"><a href="#summary" id="summary_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-cubes mx-2"></i>Notes Summary</a></li>
          <li class="nav-item" role="presentation"><a href="#ingRep" id="reps_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-exchange-alt mx-2"></i>Replacements</a></li>
          <li class="nav-item" role="presentation"><a href="#attachments" id="attachments_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-paperclip mx-2"></i>Attachments</a></li>
          <li class="nav-item" role="presentation"><a href="#revisions" id="revisions_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-clock-rotate-left mx-2"></i>Revisions</a></li>
          <li class="nav-item" role="presentation"><a href="#timeline" id="timeline_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-timeline mx-2"></i>History</a></li>

          <li class="nav-item" role="presentation"><a href="#formula_settings" id="formula_settings_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-cogs mx-2"></i>Settings</a></li>
        </ul>
                     
        <div class="tab-content table-responsive">
        	<div class="tab-pane active" id="main_formula">

          	<div class="card-body">
         		<div id="msgInfo"></div>
	      		<div id="add_ing">
                <div class="row">
                    <!-- Ingredient Selection -->
                    <div class="col-md-4">
                        <select name="ingredient" id="ingredient" class="form-select mb-3 main-ingredient"></select>
                    </div>
                    
                    <!-- Concentration Input -->
                    <div class="col-md-2">
                        <div class="input-group">
                            <input type="text" name="concentration" id="concentration" placeholder="Purity" class="form-control" aria-label="concentration" aria-describedby="conc-addon">
                            <span class="input-group-text" id="conc-addon">%</span>
                        </div>
                    </div>
                    
                    <!-- Dilutant Selection -->
                    <div class="col-md-2">
                        <select name="dilutant" id="dilutant" class="form-control selectpicker" data-live-search="true">
                            <option value="" disabled selected>Select Dilutant</option>
                            <option value="none">None</option>
                            <?php
                            $stmt = $conn->prepare("SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
                            $stmt->execute();
                            $res_dil = $stmt->get_result();
                            while ($r_dil = $res_dil->fetch_assoc()) {
                                $selected = ($r_dil['name'] == $selected_dilutant) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($r_dil['name']) . '" ' . $selected . '>' . htmlspecialchars($r_dil['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <!-- Quantity Input -->
                    <div class="col-md-2">
                        <div class="input-group">
                            <input type="text" name="quantity" id="quantity" placeholder="Quantity" class="form-control" aria-label="quantity" aria-describedby="quantity-addon">
                            <span class="input-group-text" id="quantity-addon"></span>
                        </div>
                    </div>
                    
                    <!-- Add Button -->
                    <div class="col-md-2">
                        <input type="submit" name="add" id="add-btn" class="btn btn-primary" value="Add">
                    </div>
                </div>
                
                <!-- Adjust Solvent Checkbox -->
                <div class="col-sm-6 ml-3 mt-2 mb-2">
                    <input type="checkbox" name="reCalcAdd" id="reCalcAdd" value="1" data-val="1">
                    <label for="reCalcAdd" class="form-label">Adjust solvent</label>
                    <i class="fa-solid fa-circle-info ml-1 pv_point_gen" rel="tip" data-bs-placement="right" data-bs-title="The added ingredient's quantity will be deducted from the selected solvent."></i>
                </div>
            
                <!-- Solvent Meta Section -->
                <div id="slvMetaAdd">
                    <div class="col-sm-6 ml-3 mr-2 mb-1">
                        <label for="formulaSolventsAdd" class="form-label">Formula Solvents</label>
               			<select name="formulaSolventsAdd" id="formulaSolventsAdd" class="form-control formulaSolventsAdd"></select>
                    </div>
                </div>
                
                <!-- Divider -->
                <div class="col-sm dropdown-divider"></div>
            </div>

          
              <div id="fetch_formula">
                <div class="loader-center">
                    <div class="loader"></div>
                    <div class="loader-text"></div>
                </div>
              </div>
          <?php if($legend){ ?>
          <div id="legend">
          	<div class="mt-4 dropdown-divider"></div>
            <p>*Values in: <strong class="alert alert-danger mx-2">red</strong> exceeds usage level, <strong class="alert bg-banned mx-2">dark red</strong> banned/prohibited, <strong class="alert alert-warning mx-2">yellow</strong> Specification,<strong class="alert alert-success mx-2">green</strong> are within usage level,<strong class="alert alert-info mx-2">blue</strong> are exceeding recommended usage level</p>
            </div>
          <?php } ?>
  </div>
</div>
<!--Formula-->

        <div class="tab-pane fade" id="analysis">
            <div class="card-body">
                <div id="fetch_analysis"><div class="loader"></div></div>
            </div>            
        </div>
        <div class="tab-pane fade" id="usage">
            <div class="card-body">
                <div id="fetch_usage"><div class="loader"></div></div>
            </div>            
        </div>        
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
                  <p><a href="#" data-bs-toggle="modal" data-bs-target="#conf_view">Configure view</a></p>
                  <p>To include this page in your web site, copy this line and paste it into your html code:</p>
                <p>
                <pre>&lt;iframe src=&quot;<?=$settings['pv_host']?>/pages/viewSummary.php?id=<?=$fid?>&amp;embed=1&quot; title=&quot;<?=$f_name?>&quot;&gt;&lt;/iframe&gt;</pre></p>
                    <p>For documentation and parameterisation please refer to: <a href="https://www.perfumersvault.com/knowledge-base/share-formula-notes/" target="_blank">https://www.perfumersvault.com/knowledge-base/share-formula-notes/</a></p>
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
        
        <div class="tab-pane fade" id="revisions">
            <div class="card-body">
                <div id="fetch_revisions"><div class="loader"></div></div>
            </div>            
        </div>
        
        <div class="tab-pane fade" id="formula_settings">
            <div class="card-body">
                <div id="fetch_formula_settings"><div class="loader"></div></div>
            </div>            
        </div>
        
        <div class="tab-pane fade" id="timeline">
            <div class="card-body">
                <div id="fetch_timeline"><div class="loader"></div></div>
            </div>            
        </div>
                
      </div>
     </div>         
   </div><!--tabs-->
 </div>
</div>
  


<script src="/js/select2-v3-ingredient.js"></script>
<script>
$(document).ready(function() {
	$("#img-formula").html('<img class="img-perfume" src="<?= $img['docData'] ?: '/img/ICO_TR.png' ?>" alt="Formula Image"/>');
	function convertToDecimalPoint(qStep, hasDot) {
		let zeros = '0'.repeat(qStep);
		return hasDot ? zeros : '.' + zeros;
	}
	
	const settings = {
		qStep: <?=$settings['qStep']?>,
		mUnit: '<?=$settings['mUnit']?>'
	};
	
	function updateSpan() {
		let quantityInput = document.getElementById('quantity').value;
		let hasDot = quantityInput.includes('.');
		let decimalPart = convertToDecimalPoint(settings.qStep, hasDot);
		document.getElementById('quantity-addon').textContent = decimalPart + settings.mUnit;
	}
	
	document.getElementById('quantity').addEventListener('input', updateSpan);
	document.getElementById('quantity-addon').textContent = convertToDecimalPoint(settings.qStep, false) + settings.mUnit;
	
	document.title = "<?=$meta['name'].' - '.$product?>";
	var myFID = "<?=$fid?>";
	var isProtected = "<?=$meta['isProtected']?>";
	
	$('#formula_name').click(function() {
		reload_formula_data();
	});
		
	$('#add_ing').hide();
	
	if(isProtected == '0'){
		$('#add_ing').show();	
	}
});//END DOC - TODO

$("#concentration").prop("disabled", true); 
$("#dilutant").prop("disabled", true);
$('#quantity').prop("disabled", true);


//Add ingredient
$('#add_ing').on('click', '[id*=add-btn]', function () {
	
	$.ajax({ 
		url: '/core/core.php', 
		type: 'POST',
		data: {
			action: "addIngToFormula",
			fid: myFID,
			id: <?php echo $id; ?>,
			quantity: $("#quantity").val(),
			concentration: $("#concentration").val(),
			ingredient: $("#ingredient").val(),
			dilutant: $("#dilutant").val(),			
			reCalc: $("#reCalcAdd").prop('checked'),
			formulaSolventID: $("#formulaSolventsAdd").val()
		},
		dataType: 'json',
		success: function (data) {
			if ( data.success ) {
            	$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
				reload_formula_data();
			} else {
            	$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
			}
			$('.toast').toast('show');
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		}
		
	  });
				
});

function setProtected(status) {
  $.ajax({ 
    url: '/core/core.php', 
    type: 'GET',
    data: {
      protect: myFID,
      isProtected: status,
    },
    dataType: 'json',
    success: function (data) {
      if (data.success) {
        fetch_formula();
        if (data.success === 'Formula locked') {
          $('#lock_status').html('<a class="fas fa-lock text-body-emphasis" href="javascript:setProtected(\'false\')"></a>');
          $('#add_ing').hide();
        } else {
          $('#lock_status').html('<a class="fas fa-unlock text-body-emphasis" href="javascript:setProtected(\'true\')"></a>');
          $('#add_ing').show();
        }
      } else {
        $('#msgInfo').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>' + data.error + '</strong></div>');
      }
    },
    error: function (xhr, status, error) {
      $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. ' + error);
      $('.toast-header').removeClass().addClass('toast-header alert-danger');
      $('.toast').toast('show');
    }
  });
};


function fetch_formula(){
$.ajax({ 
    url: '/pages/views/formula/viewFormula.php', 
	type: 'GET',
    data: {
		id: "<?=$id?>",
		"search": "<?=$_GET['search']?>"
	},
	dataType: 'html',
		success: function (data) {
			$('#fetch_formula').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_formula').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};

fetch_formula();

function fetch_pyramid(){
	$.ajax({ 
		url: '/pages/views/formula/viewPyramid.php', 
		type: 'GET',
		data: {
			formula: "<?=$id?>",
			fid: myFID
		},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_pyramid').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_pyramid').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};


function fetch_impact(){
	$.ajax({ 
		url: '/pages/views/formula/impact.php', 
		type: 'GET',
		data: {
			id: myFID
		},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_impact').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_impact').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};


function fetch_analysis(){
	$.ajax({ 
		url: '/pages/views/formula/analysis.php', 
		type: 'GET',
		data: {
			fid: myFID
		},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_analysis').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_analysis').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};

function fetch_usage(){
	$.ajax({ 
		url: '/pages/views/formula/usage.php', 
		type: 'GET',
		data: {
			fid: myFID
		},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_usage').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_usage').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};

function fetch_summary(){
$.ajax({ 
    url: '/pages/views/formula/viewSummary.php', 
	type: 'GET',
    data: {
		id: myFID
	},
	dataType: 'html',
		success: function (data) {
			$('#fetch_summary').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_summary').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};

function fetch_replacements(){
	$.ajax({ 
		url: '/pages/views/formula/replacements.php', 
		type: 'POST',
		data: {
			fid: myFID
		},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_replacements').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_replacements').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};

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
		},
		error: function (xhr, status, error) {
			$('#fetch_attachments').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};

function fetch_revisions(){
	$.ajax({ 
		url: '/pages/views/formula/revisions.php', 
		type: 'GET',
		data: {
			fid: myFID,
			id: "<?=$meta['id']?>"
		},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_revisions').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_revisions').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};

function fetch_formula_settings(){
	$.ajax({ 
		url: '/pages/views/formula/getFormMeta.php',
		type: 'GET',
		data: {
			id: "<?=$meta['id']?>",
			//embed: true
		},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_formula_settings').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_formula_settings').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};

function fetch_timeline(){
	$.ajax({ 
		url: '/pages/views/formula/timeline.php',
		type: 'GET',
		data: {
			id: "<?=$meta['id']?>",
		},
		dataType: 'html',
		success: function (data) {
		  $('#fetch_timeline').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_timeline').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};

</script>
<script src="/js/formula.tabs.js"></script>

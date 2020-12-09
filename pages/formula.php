<?php 
if (!defined('pvault_panel')){ die('Not Found');}  
$f_name =  mysqli_real_escape_string($conn, $_GET['name']);
$fid = base64_encode($f_name);

if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid'")) == FALSE){
	echo 'Formula doesn\'t exist';
	exit;
}
$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE name = '$f_name' ORDER BY ingredient ASC");
                    

$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE name = '$f_name'"));
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE name = '$f_name'"));

$top_calc = calcPerc($f_name, 'Top', $settings['top_n'], $conn);
$heart_calc = calcPerc($f_name, 'Heart', $settings['heart_n'], $conn);
$base_calc = calcPerc($f_name, 'Base', $settings['base_n'], $conn);

?>

<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
			  <?php if($meta['image']){?><div class="img-formula"><img class="img-perfume" src="<?php echo $meta['image']; ?>"/></div><?php } ?>
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=Formula&name=<?php echo $f_name; ?>"><?php echo $f_name; ?></a></h2>
              <h5 class="m-1 text-primary"><a href="pages/getFormMeta.php?id=<?php echo $meta['id'];?>" class="popup-link">Details</a></h5>
            </div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
          <li class="active"><a href="#main_formula" role="tab" data-toggle="tab"><icon class="fa fa-bong"></icon> Formula</a></li>
    	  <li><a href="#impact" role="tab" data-toggle="tab"><i class="fa fa-magic"></i> Notes Impact</a></li>
          <li><a href="#pyramid" role="tab" data-toggle="tab"><i class="fa fa-table"></i> Olfactory Pyramid</a></li>
        </ul>
                     
        <div class="tab-content">
          <div class="tab-pane fade active in tab-content" id="main_formula">

            <div class="card-body">
           <div id="msgInfo"></div>
              <div>
                  <tr>
                    <th colspan="6">
                      <form action="javascript:addING()" enctype="multipart/form-data" name="form1" id="form1">
                         <table width="100%" border="0" class="table">
                                    <tr>  
                                         <td>
                                         <select name="ingredient" id="ingredient" class="form-control selectpicker" data-live-search="true">
                                         <option value="" selected disabled>Ingredient</option>
                                         <?php
										 	$res_ing = mysqli_query($conn, "SELECT id, name, profile, chemical_name FROM ingredients ORDER BY name ASC");
										 	while ($r_ing = mysqli_fetch_array($res_ing)){
												echo '<option value="'.$r_ing['name'].'">'.$r_ing['name'].' ('.$r_ing['profile'].')</option>';
											}
										 ?>
                                         </select>                                         
                                         </td>
                                         <td><input type="text" name="concentration" id="concentration" placeholder="Purity %" class="form-control" /></td>
                                      <td>
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
                                      </td>
                                         <td><input type="text" name="quantity" id="quantity" placeholder="Quantity" class="form-control" /></td>  
                                         <td><input type="submit" name="add" id="add" class="btn btn-info" value="Add" /> </td>  
                                    </tr>  
                        </table>  
                      </form>
                    </th>
                    </tr>
               
                <div id="fetch_formula"><div class="loader"></div></div>
                
                <div>
                <p></p>
                <p>*Values in: <strong class="alert alert-danger">red</strong> exceeds usage level,   <strong class="alert alert-warning">yellow</strong> have no usage level set,   <strong class="alert alert-success">green</strong> are within usage level</p>
                </div>
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
          
        </div>
       </div>         
     </div><!--tabs-->
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
    url: 'pages/viewFormula.php', 
	type: 'get',
    data: {
		id: "<?php echo $fid; ?>"
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
			formula: "<?php echo $f_name; ?>"
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
</script>

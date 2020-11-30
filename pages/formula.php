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
<script>

//MULTIPLY - DIVIDE
function manageQuantity(quantity) {
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		do: quantity,
		formula: "<?php echo $f_name; ?>",
		},
	dataType: 'html',
    success: function (data) {
		location.reload();
	  	//$('#msgInfo').html(data);
    }
  });

};

//Delete ingredient
function deleteING(ingName,ingID) {	  
$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "deleteIng",
		fname: "<?php echo $f_name; ?>",
		ingID: ingID,
		ing: ingName
		},
	dataType: 'html',
    success: function (data) {
		location.reload();
	  	$('#msgInfo').html(data);
    }
  });

};
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
			location.reload();
		}
    }
  });

};
//Clone
function cloneMe() {	  
$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "clone",
		formula: "<?php echo $f_name; ?>",
		},
	dataType: 'html',
    success: function (data) {
        if ( data.indexOf("Error") > -1 ) {
			$('#msgInfo').html(data); 
		}else{
			$('#msgInfo').html(data);
			//location.reload();
		}
    }
  });
};

//Add in TODO
function addTODO() {
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: 'todo',
		fid: "<?php echo base64_encode($f_name); ?>",
		add: true,
		},
	dataType: 'html',
    success: function (data) {
	  	$('#msgInfo').html(data);
    }
  });
};

//Change ingredient
$(document).ready(function(){
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
})

$('.replaceIngredient').editable({
	//value: "",
	type: 'get',
	emptytext: "",
	emptyclass: "",
  	url: "pages/manageFormula.php?action=repIng&fname=<?php echo $f_name; ?>",
    source: [
			 <?php
				$res_ing = mysqli_query($conn, "SELECT id, name, chemical_name FROM ingredients ORDER BY name ASC");
				while ($r_ing = mysqli_fetch_array($res_ing)){
				echo '{value: "'.$r_ing['name'].'", text: "'.$r_ing['name'].'"},';
			}
			?>
          ],
	dataType: 'html',
    success: function (data) {
        if ( data.indexOf("Error") > -1 ) {
			$('#msgInfo').html(data); 
		}else{
			$('#msgInfo').html(data);
			location.reload();
		}
	}
    });
});

 
</script>
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
                 <?php if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas WHERE fid = '$fid'"))){?>
                
                <div id="fetch_formula"><div class="loader"></div></div>
                
                <div>
                <p></p>
                <p>*Values in: <strong class="alert alert-danger">red</strong> exceeds usage level,   <strong class="alert alert-warning">yellow</strong> have no usage level set,   <strong class="alert alert-success">green</strong> are within usage level</p>
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
          
        </div>
       </div>         
     </div><!--tabs-->
   </div>
  </div>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 
  $('#formula_data').editable({
  container: 'body',
  selector: 'td.quantity',
  url: "pages/update_data.php?formula=<?php echo $f_name; ?>",
  title: 'ml',
  type: "POST",
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; else location.reload();
    },
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
   if($.isNumeric(value) == '' ){
    return 'Numbers only!';
   }
  }
 });
 
  $('#formula_data').editable({
  container: 'body',
  selector: 'td.concentration',
  url: "pages/update_data.php?formula=<?php echo $f_name; ?>",
  title: 'Purity %',
  type: "POST",
  dataType: 'json',
        success: function(response, newValue) {
        if(response.status == 'error') return response.msg; else location.reload();
    },
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
   if($.isNumeric(value) == '' ){
    return 'Numbers only!';
   }
  }
 });
 //
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

});

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
</script>

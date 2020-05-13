<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
<div class="container-fluid">
<?php 
$number = count($_POST['ingredient']); 
$fname = mysqli_real_escape_string($conn, $_POST['fname']);


if($number > 0){
	for($i=0; $i<$number; $i++){
		$qin = mysqli_real_escape_string($conn,$_POST["ingredient"][$i]);
		$ingIDq = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$qin'"));
		$sql = "INSERT INTO formulas(fid,name,ingredient,ingredient_id,concentration,quantity) VALUES('".base64_encode($_POST["fname"])."','".$fname."','".mysqli_real_escape_string($conn, $_POST["ingredient"][$i])."','$ingIDq[0]','".mysqli_real_escape_string($conn, $_POST["concentration"][$i])."','".mysqli_real_escape_string($conn, $_POST["quantity"][$i])."')";
         $fq = mysqli_query($conn, $sql);
	}
	if($fq){
		echo '<div class="alert alert-success alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  			<strong>'.$fname.'</strong> Added!
			</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  			<strong>Error:</strong> missing fields!
			</div>';
	}  
} 

$res_ing = mysqli_query($conn, "SELECT id,name FROM ingredients ORDER BY name ASC");
?>
<script>
$(document).ready(function(){  
      var i=1;  
      $('#add').click(function(){  
           i++;  
           $('#dynamic_field').append('<tr id="row'+i+'"><td>Ingredient '+i+'</td><td><select name="ingredient[]" id="ingredient[]" class="form-control ing_list"><?php
										 	while ($r_ing = mysqli_fetch_array($res_ing)){
												echo '<option value="'.$r_ing['name'].'">'.$r_ing['name'].'</option>';
											}
										 ?></select></td><td><input type="text" name="concentration[]" placeholder="Concentration %" class="form-control ing_list" /></td><td><input type="text" name="quantity[]" placeholder="Quantity" class="form-control ing_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">-</button></td></tr>');  
      });  
      $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();  
      });  

})
</script>
          <h2 class="m-0 mb-4 text-primary">New Formula</h2>
          <p>*All fields required</p>

        </div>
<table width="94%" border="0" align="center">
        <tr>
          <td>
          <div class="form-group">
            <form action="?do=addFormula" method="post" enctype="multipart/form-data" name="add_formula" target="_self" id="add_formula">  
				<div class="table-responsive">  
                               <table width="764" class="table table-bordered" id="dynamic_field">  
                                    <tr>
                                      <td>Formula name</td>
                                      <td colspan="4"><input name="fname" type="text" class="form-control" /></td>
                                    </tr>
                                    <tr>  
                                         <td>Ingredient</td>
                                         <td>
                                         <select name="ingredient[]" id="ingredient[]" class="form-control ing_list">
                                         <?php
										 	$res_ing = mysqli_query($conn, "SELECT id,name FROM ingredients ORDER BY name ASC");
										 	while ($r_ing = mysqli_fetch_array($res_ing)){
												echo '<option value="'.$r_ing['name'].'">'.$r_ing['name'].'</option>';
											}
										 ?>
                                         </select>
                                         </td>
                                         <td><input type="text" name="concentration[]" placeholder="Concentration %" class="form-control ing_list" /></td>
                                         <td><input type="text" name="quantity[]" placeholder="Quantity" class="form-control ing_list" /></td>  
                                         <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>  
                                    </tr>  
                               </table>  
                               <input type="submit" name="submit" id="submit" class="btn btn-info" value="Submit" />  
                       </div>  
     </form>  
                </div></td>
        </tr>
</table>
      </div>
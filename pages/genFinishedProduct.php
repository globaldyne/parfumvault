<?php if (!defined('pvault_panel')){ die('Not Found');} ?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
<div class="container-fluid">
    <div>
      <div class="card shadow mb-4">
        <div class="card-header py-3"> 
           <h2 class="m-0 font-weight-bold text-primary">Sell Formula</h2>
    	</div>
	<div class="card-body">
    
<?php
				if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData"))== 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="/?do=listFormulas">create</a> at least one formula first.</div>';
					return;
				}
				if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bottles"))== 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="/?do=bottles">add</a> at least one bottle in your inventory first.</div>';
					return;
				}
				if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE type = 'Carrier' OR type = 'Solvent'"))== 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="/?do=ingredients">add</a> at least one solvent or carrier first.</div>';
					return;
				}
				
				$cats_q = mysqli_query($conn, "SELECT name,description FROM IFRACategories ORDER BY id ASC");
				while($cats_res = mysqli_fetch_array($cats_q)){
					$cats[] = $cats_res;
				}
				$sup_q = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY id ASC");
				while($r = mysqli_fetch_array($sup_q)){
					$suppliers[] = $r;
				}
				$fTypes_q = mysqli_query($conn, "SELECT id,name,description,concentration FROM perfumeTypes ORDER BY id ASC");
					while($fTypes_res = mysqli_fetch_array($fTypes_q)){
   					$fTypes[] = $fTypes_res;
				}
			?>
           <form action="/?do=genFinishedProduct&generate=1" method="post" enctype="multipart/form-data" target="_blank">
           
           <table width="100%" border="0">
  <tr>
    <td width="9%">Formula:</td>
    <td width="24%">
    <select name="formula" id="formula" class="form-control selectpicker" data-live-search="true">
     <?php
		$sql = mysqli_query($conn, "SELECT fid,name,product_name FROM formulasMetaData WHERE product_name IS NOT NULL ORDER BY name ASC");
		while ($formula = mysqli_fetch_array($sql)){
			echo '<option value="'.$formula['fid'].'">'.$formula['name'].' ('.$formula['product_name'].')</option>';
}
	  ?>
     </select>
   </td>
    <td width="67%">&nbsp;</td>
  </tr>
  <tr>
    <td>Concentration:</td>
    <td>
        <select name="type" id="type" class="form-control selectpicker" data-live-search="true">
     		<option value="100">Concentrated (100%)</option>
			<?php foreach ($fTypes as $fType) {?>
              <option value="<?php echo $fType['concentration'];?>" <?php echo ($info['finalType']==$fType['concentration'])?"selected=\"selected\"":""; ?>><?php echo $fType['name'].' ('.$fType['concentration'];?>%)</option>
            <?php }	?>	
     	</select>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Ingredients Supplier:</td>
    <td><select name="ingSup" id="ingSup" class="form-control selectpicker" data-live-search="true">
       <option value="0" selected="selected">Formula Defaults</option>
      <?php foreach ($suppliers as $supplier) {?>
      <option value="<?=$supplier['id'];?>"><?=$supplier['name'];?></option>
      <?php	}	?>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Category Class:</td>
    <td><select name="defCatClass" id="defCatClass" class="form-control selectpicker" data-live-search="true">
		<?php foreach ($cats as $IFRACategories) {?>
				<option value="cat<?php echo $IFRACategories['name'];?>" <?php echo ($settings['defCatClass']=='cat'.$IFRACategories['name'])?"selected=\"selected\"":""; ?>><?php echo 'Cat '.$IFRACategories['name'].' - '.$IFRACategories['description'];?></option>
		  <?php	}	?>
            </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Batch ID:</td>
    <td><select name="batchID" id="batchID" class="form-control selectpicker" data-live-search="false">
      <option value="0">Do Not Generate</option>
      <option value="1">Generate</option>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Bottle:</td>
    <td>    
    <select name="bottle" id="bottle" class="form-control selectpicker" data-live-search="true">
     <?php
		$sql = mysqli_query($conn, "SELECT id,name,ml FROM bottles WHERE ml != 0 ORDER BY ml DESC");
		while ($bottle = mysqli_fetch_array($sql)){
			echo '<option value="'.$bottle['id'].'">'.$bottle['name'].' ('.$bottle['ml'].'ml)</option>';
		}
	  ?>
     </select>
     </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Carrier:</td>
    <td>
    <select name="carrier" id="carrier" class="form-control selectpicker" data-live-search="true">
      <?php
		$sql = mysqli_query($conn, "SELECT name,id FROM ingredients WHERE type = 'Carrier' OR type = 'Solvent' ORDER BY name ASC");
		while ($carrier = mysqli_fetch_array($sql)){
			echo '<option value="'.$carrier['id'].'">'.$carrier['name'].'</option>';
		}
	  ?>
    </select>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Bottle Lid:</td>
    <td><select name="lid" id="lid" class="form-control selectpicker" data-live-search="true">
      <option value="0" selected="selected">None</option>
      <?php
		$sql = mysqli_query($conn, "SELECT style,id FROM lids ORDER BY style ASC");
		while ($lid = mysqli_fetch_array($sql)){
			echo '<option value="'.$lid['id'].'">'.$lid['style'].'</option>';
		}
	  ?>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><input type="submit" name="button" class="btn btn-info" id="button" value="Generate"></td>
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
           </form>          
           </div>
        </div>
      </div>
   </div>
  </div>
<script type="text/javascript" language="javascript" >
$("#ViewBoxLabel").on("show.bs.modal", function(e) {
	
  const action = "viewBoxLabel"; 
  const batchID = "<?php echo $batchID; ?>";
  const formula = "<?php echo $fid; ?>";
  const carrier = "<?php echo $carrier*100/$bottle;?>";

  const url = "/pages/manageFormula.php?action="+ action + "&batchID=" + batchID +"&fid=" + formula + "&carrier=" + carrier +"&download=text";

  $.get(url)
    .then(data => {
      $("#headerLabel", this).html("<?php echo $f_name; ?>");
      $(".modal-body", this).html(data);
    });
	
});

$('#pdf').on('click',function(){
	$("#formula").tableHTMLExport({
		type:'pdf',
		filename:'<?=$f_name?>.pdf',
		orientation: 'p',
		trimContent: true,
    	quoteFields: true,
		ignoreColumns: '.noexport',
  		ignoreRows: '.noexport',
		htmlContent: true,
		maintitle: '<?=$f_name?>',
		product: '<?php echo trim($product).' '.trim($ver);?>',
	});
});

</script>

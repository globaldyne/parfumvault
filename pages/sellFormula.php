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
        <table width="100%" border="0">
          <tr>
            <td width="10%">Formula</td>
            <td width="90%">
            <select id="formulaID" class="form-control selectpicker" data-live-search="true">
             <?php
             $sql = mysqli_query($conn, "SELECT id,name,product_name FROM formulasMetaData ORDER BY name ASC");
             while ($formula = mysqli_fetch_array($sql)){
                echo '<option value="'.$formula['id'].'">'.$formula['name'].' ('.$formula['product_name'].')</option>';
             }
             ?>
             </select>
           </td>
          </tr>
          <tr>
            <td>Customer</td>
            <td>
              <select id="customerID" class="form-control selectpicker" data-live-search="true">
               <?php
               $res = mysqli_query($conn, "SELECT id, name FROM customers ORDER BY name ASC");
               while ($q = mysqli_fetch_array($res)){
					echo '<option value="'.$q['name'].'">'.$q['name'].'</option>';
                }
                ?>
                </select>
              </td>
          </tr>
          <tr>
            <td>Orientation</td>
            <td colspan="2"><select name="orientation" class="form-control" id="orientation">
              <option value="p">Portrait</option>
              <option value="l">Landscaspe</option>
            </select></td>
          </tr>
          <tr>
            <td>Watermark</td>
            <td colspan="2"><input class="mb-2 form-control" name="watermarkText" type="text" id="watermarkText" value="CONFIDENTIAL"></td>
          </tr>
          <tr>
            <td>Watermark size</td>
            <td colspan="2"><select name="watermarkTextSize" class="form-control" id="watermarkTextSize">
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="200">200</option>
            </select></td>
          </tr>
          <tr>
            <td><input type="submit" name="button" class="btn btn-info" id="btnGEN" value="Generate"></td>
          </tr>
        </table> 
      </div>
     </div>
    </div>
   <div id="results"></div>
   </div>
  </div>
<script>
$('#btnGEN').click(function() {
	$.ajax({ 
		url: '/pages/views/formula/sell_formula.php', 
		type: 'POST',
		data: {
			id: $("#formulaID").val(),
			watermarkText: $("#watermarkText").val(),
			watermarkTextSize: $("#watermarkTextSize").val(),
			orientation: $("#orientation").val()
			},
		dataType: 'html',
		success: function (data) {
			$('#results').html(data);
		}
	  });
});

</script>

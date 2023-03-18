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
            <td width="9%">Formula:</td>
            <td width="24%">
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
            <td>Customer:</td>
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
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td><input type="submit" name="button" class="btn btn-info"id="btnGEN" value="Generate"></td>
            <td colspan="2">&nbsp;</td>
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
		type: 'GET',
		data: {
			id: $("#formulaID").val(),			
			},
		dataType: 'html',
		success: function (data) {
			$('#results').html(data);
		}
	  });
});

</script>

<?php if (!defined('pvault_panel')){ die('Not Found');} ?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
<div class="container-fluid">
    <div>
      <div class="card shadow mb-4">
        <div class="card-header py-3"> 
           <h2 class="m-0 font-weight-bold text-primary-emphasis">Sell Formula</h2>
    	</div>
	<div class="card-body">
 
        <div class="row mb-3">
          <!-- Column 1 -->
          <div class="col-md-6">
            <div class="row mb-3">
              <label for="formulaID" class="col-sm-3 col-form-label">Formula</label>
              <div class="col-sm-9">
                <select id="formulaID" class="form-control selectpicker" data-live-search="true">
                  <?php
                  $sql = mysqli_query($conn, "SELECT id, name, product_name FROM formulasMetaData ORDER BY name ASC");
                  while ($formula = mysqli_fetch_array($sql)){
                    echo '<option value="'.$formula['id'].'">'.$formula['name'].' ('.$formula['product_name'].')</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
        
            <div class="row mb-3">
              <label for="customerID" class="col-sm-3 col-form-label">Customer</label>
              <div class="col-sm-9">
                <select id="customerID" class="form-control selectpicker" data-live-search="true">
                  <?php
                  $res = mysqli_query($conn, "SELECT id, name FROM customers ORDER BY name ASC");
                  while ($q = mysqli_fetch_array($res)){
                    echo '<option value="'.$q['name'].'">'.$q['name'].'</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
        
            <div class="row mb-3">
              <label for="orientation" class="col-sm-3 col-form-label">PDF Orientation</label>
              <div class="col-sm-9">
                <select name="orientation" class="form-control" id="orientation">
                  <option value="portrait">Portrait</option>
                  <option value="landscape">Landscape</option>
                </select>
              </div>
            </div>
        
            <div class="row mb-3">
              <label for="watermarkText" class="col-sm-3 col-form-label">Watermark</label>
              <div class="col-sm-9">
                <input class="form-control" name="watermarkText" type="text" id="watermarkText" value="CONFIDENTIAL">
              </div>
            </div>
          </div>
        
          <!-- Column 2 -->
          <div class="col-md-6">
            <div class="row mb-3">
              <label for="watermarkTextOp" class="col-sm-3 col-form-label">Watermark opacity</label>
              <div class="col-sm-9">
                <select name="watermarkTextOp" class="form-control" id="watermarkTextOp">
                  <option value="0.1">0.1</option>
                  <option value="0.2" selected="selected">0.2</option>
                  <option value="0.3">0.3</option>
                </select>
              </div>
            </div>
        
            <div class="row mb-3">
              <label for="fontSize" class="col-sm-3 col-form-label">PDF font size</label>
              <div class="col-sm-9">
                <input class="form-control" name="fontSize" type="text" id="fontSize" value="8">
              </div>
            </div>
        
            <div class="row mb-3">
              <label for="logoSizeW" class="col-sm-3 col-form-label">Logo width (px)</label>
              <div class="col-sm-9">
                <input class="form-control" name="logoSizeW" type="text" id="logoSizeW" value="200">
              </div>
            </div>
        
            <div class="row mb-3">
              <label for="logoSizeH" class="col-sm-3 col-form-label">Logo height (px)</label>
              <div class="col-sm-9">
                <input class="form-control" name="logoSizeH" type="text" id="logoSizeH" value="200">
              </div>
            </div>
        
            <div class="row mb-3">
              <label for="qStep" class="col-sm-3 col-form-label">Quantity Decimal</label>
              <div class="col-sm-9">
                <select name="qStep" id="qStep" class="form-control">
                  <option value="1" <?php if($settings['qStep']=="1") echo 'selected="selected"'; ?>>0.0</option>
                  <option value="2" <?php if($settings['qStep']=="2") echo 'selected="selected"'; ?>>0.00</option>
                  <option value="3" <?php if($settings['qStep']=="3") echo 'selected="selected"'; ?>>0.000</option>
                  <option value="4" <?php if($settings['qStep']=="4") echo 'selected="selected"'; ?>>0.0000</option>
                </select>
              </div>
            </div>
          </div>
        </div>
          <div class="row mb-3">
            <div class="col-sm-1">
              <input type="submit" name="button" class="btn btn-primary" id="btnGEN" value="Generate">
            </div>
          </div>

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
			watermarkTextOp: $("#watermarkTextOp").val(),
			orientation: $("#orientation").val(),
			qStep: $("#qStep").val(),
			fontSize: $("#fontSize").val(),
			logoSizeW: $("#logoSizeW").val(),
			logoSizeH: $("#logoSizeH").val(),
		},
		dataType: 'html',
		success: function (data) {
			$('#results').html(data);
		}
	  });
});

</script>

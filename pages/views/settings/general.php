<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");

while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}
?>
     <table width="100%" border="0">
        <tr>
          <td colspan="4"><div id="inMsg"></div></td>
          </tr>
        <tr>
          <td width="10%" height="29">Currency:</td>
          <td colspan="2"><input name="currency" type="text" class="form-control" id="currency" value="<?php echo utf8_encode($settings['currency']);?>"/></td>
          <td width="70%">&nbsp;</td>
          </tr>
        <tr>
          <td height="28"><a href="#" rel="tip" title="If enabled, ingredients in formula view will be grouped by type. eg: Top,Heart,Base notes">Group Formula:</a></td>
          <td colspan="2"><select name="grp_formula" id="grp_formula" class="form-control">
			  <option value="0" <?php if($settings['grp_formula']=="0") echo 'selected="selected"'; ?> >Plain</option>
			  <option value="1" <?php if($settings['grp_formula']=="1") echo 'selected="selected"'; ?> >By notes</option>
			  <option value="2" <?php if($settings['grp_formula']=="2") echo 'selected="selected"'; ?> >By category</option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tip" title="If enabled, PV will query PubChem to fetch ingredient data. Please note, the CAS number of the ingredient will be send to the PubChem servers.">Enable PubChem:</a></td>
          <td colspan="2"><input name="pubChem" type="checkbox" id="pubChem" value="1" <?php if($settings['pubChem'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tip" title="Select the image type for PubChem, 2D or 3D. Default: 2D">PubChem view:</a></td>
          <td colspan="2"><select name="pubchem_view" id="pubchem_view" class="form-control">
			  <option value="2d" <?php if($settings['pubchem_view']=="2d") echo 'selected="selected"'; ?> >2D</option>
			  <option value="3d" <?php if($settings['pubchem_view']=="3d") echo 'selected="selected"'; ?> >3D</option>
          </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tip" title="Auto check for new PV version. If enabled, your ip, current PV version and browser info will be send to our servers and or GitHub servers. Please make sure you have read our and GitHub's T&C and Privacy Policy before enable this.">Check for updates:</a></td>
          <td colspan="3"><input name="chkVersion" type="checkbox" id="chkVersion" value="1" <?php if($settings['chkVersion'] == '1'){ ?> checked="checked" <?php } ?>/>
           </td>
          </tr>
        <tr>
          <td height="32"><a href="#" rel="tip" title="Defines the decimal in formula quantity.">Quantity Decimal:</a></td>
          <td colspan="2"><select name="qStep" id="qStep" class="form-control">
			  <option value="1" <?php if($settings['qStep']=="1") echo 'selected="selected"'; ?> >0.0</option>
			  <option value="2" <?php if($settings['qStep']=="2") echo 'selected="selected"'; ?> >0.00</option>
			  <option value="3" <?php if($settings['qStep']=="3") echo 'selected="selected"'; ?> >0.000</option>
			  <option value="4" <?php if($settings['qStep']=="4") echo 'selected="selected"'; ?> >0.0000</option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tip" title="Select the default category class. This will be used to calculate limits in formulas">Default Category:</a></td>
          <td colspan="2"><select name="defCatClass" id="defCatClass" class="form-control">
		<?php foreach ($cats as $IFRACategories) {?>
				<option value="cat<?php echo $IFRACategories['name'];?>" <?php echo ($settings['defCatClass']=='cat'.$IFRACategories['name'])?"selected=\"selected\"":""; ?>><?php echo 'Cat '.$IFRACategories['name'].' - '.$IFRACategories['description'];?></option>
		  <?php	}	?>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#">Measurement Unit:</a></td>
          <td colspan="2"><select name="mUnit" id="mUnit" class="form-control">
			  <option value="ml" <?php if($settings['mUnit']=="ml") echo 'selected="selected"'; ?> >Milliliter</option>
			  <option value="g" <?php if($settings['mUnit']=="g") echo 'selected="selected"'; ?> >Grams</option>
			  <option value="L" <?php if($settings['mUnit']=="L") echo 'selected="selected"'; ?> >Liter</option>
			  <option value="fl. oz." <?php if($settings['mUnit']=="fl. oz.") echo 'selected="selected"'; ?> >Fluid ounce (fl. oz.)</option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tip" title="If enabled, formula will display the chemical names of ingredients, where available, instead of the commercial name">Chemical names</a>:</td>
          <td colspan="2"><input name="chem_vs_brand" type="checkbox" id="chem_vs_brand" value="1" <?php if($settings['chem_vs_brand'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tip" title="If enabled, ingredient usage percentage will be calculated against ingredients compositions as well.">Multi-dimensional lookup:</a></td>
          <td colspan="2"><input name="multi_dim_perc" type="checkbox" id="multi_dim_perc" value="1" <?php if($settings['multi_dim_perc'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tip" title="If enabled, formula quantity will be displayed in advanced mode when editing a formula">Formula editor</a></td>
          <td colspan="2"><select name="editor" id="editor" class="form-control">
			  <option value="1" <?php if($settings['editor']=="1") echo 'selected="selected"'; ?> >Inline</option>
			  <option value="2" <?php if($settings['editor']=="2") echo 'selected="selected"'; ?> >Advanced</option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4">&nbsp;</td>
          </tr>
        <tr>
          <td colspan="3"><h4 class="m-0 mb-4 text-primary">Pyramid View</h4>
            <hr></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Top notes:</td>
          <td width="12%"><input name="top_n" type="text" class="form-control" id="top_n" value="<?php echo $settings['top_n'];?>"/></td>
          <td width="8%">%</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Heart notes:</td>
          <td><input name="heart_n" type="text" class="form-control" id="heart_n" value="<?php echo $settings['heart_n'];?>"/></td>
          <td>%</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Base notes:</td>
          <td><input name="base_n" type="text" class="form-control" id="base_n" value="<?php echo $settings['base_n'];?>"/></td>
          <td>%</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
          <td>&nbsp;</td>
          </tr>
        <tr>
          <td><input type="submit" name="save-general" id="save-general" value="Save" class="btn btn-info"/></td>
          <td colspan="3">&nbsp;</td>
          </tr>
      </table>
      
<script>
$('#save-general').click(function() {
	$.ajax({ 
		url: '/pages/update_settings.php', 
		type: 'POST',
		data: {
			manage: 'general',
			currency: $("#currency").val(),
			top_n: $("#top_n").val(),
			heart_n: $("#heart_n").val(),
			base_n: $("#base_n").val(),
			qStep: $("#qStep").val(),
			defCatClass: $("#defCatClass").val(),
			pubchem_view: $("#pubchem_view").val(),
			grp_formula: $("#grp_formula").val(),
			chem_vs_brand: $("#chem_vs_brand").is(':checked'),
			pubChem: $("#pubChem").is(':checked'),
			chkVersion: $("#chkVersion").is(':checked'),
			multi_dim_perc: $("#multi_dim_perc").is(':checked'),
			mUnit: $("#mUnit").val(),
			editor: $("#editor").val(),
			api: $("#pv_api").val(),
			api_key: $("#pv_api_key").val(),
	},
	dataType: 'json',
	success: function (data) {
		if(data.success){
			msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';	
		}else{
			msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
		}
		$('#inMsg').html(msg);
	}
  });
});

</script>
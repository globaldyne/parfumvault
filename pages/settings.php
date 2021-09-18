<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php'); 
						   
 
$pv_online = mysqli_fetch_array(mysqli_query($conn, "SELECT email FROM pv_online"));
$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");

while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}
require(__ROOT__.'/inc/settings.php');

?>
<script>
$(function() {
  $("#settings").tabs();
  $("#username").val('');
  $("#password").val('');
  $("#fname").val('');
  $("#email").val('');
});
list_users();
list_cat();

</script>
<div class="container-fluid">

<h2 class="m-0 mb-4 text-primary"><a href="?do=settings">Settings</a></h2>
<div id="settings">
     <ul>
         <li><a href="#general"><span>General</span></a></li>
         <li><a href="#categories"><span>Ingredient Categories</span></a></li>
         <li><a href="#types">Perfume Types</a></li>
         <li><a href="#print"><span>Printing</span></a></li>
         <li><a href="#users"><span>Users</span></a></li>
         <li><a href="#brand"><span>My Brand</span></a></li>
         <li><a href="#maintenance"><span>Maintenance</span></a></li>
         <li><a href="#pvonline"><span>PV Online</span></a></li>
         <li><a href="#api">API</a></li>
        <li><a href="pages/about.php"><span>About</span></a></li>
     </ul>
     <div id="general">
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
          <td height="28"><a href="#" rel="tipsy" title="If enabled, ingredients in formula view will be grouped by type. eg: Top,Heart,Base notes">Group Formula:</a></td>
          <td colspan="2"><input name="grp_formula" type="checkbox" id="grp_formula" value="1" <?php if($settings['grp_formula'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="If enabled, PV will query PubChem to fetch ingredient data. Please note, the CAS number of the ingredient will be send to the PubChem servers.">Enable PubChem:</a></td>
          <td colspan="2"><input name="pubChem" type="checkbox" id="pubChem" value="1" <?php if($settings['pubChem'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="Select the image type for PubChem, 2D or 3D. Default: 2D">PubChem view:</a></td>
          <td colspan="2"><select name="pubchem_view" id="pubchem_view" class="form-control">
			  <option value="2d" <?php if($settings['pubchem_view']=="2d") echo 'selected="selected"'; ?> >2D</option>
			  <option value="3d" <?php if($settings['pubchem_view']=="3d") echo 'selected="selected"'; ?> >3D</option>
          </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="Auto check for new PV version. If enabled, your ip, current PV version and browser info will be send to our servers and or GitHub servers. Please make sure you have read our and GitHub's T&C and Privacy Policy before enable this.">Check for updates:</a></td>
          <td colspan="3"><input name="chkVersion" type="checkbox" id="chkVersion" value="1" <?php if($settings['chkVersion'] == '1'){ ?> checked="checked" <?php } ?>/>
            <?php require('privacy_note.php');?></td>
          </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="Defines the decimal in formula quantity.">Quantity Decimal:</a></td>
          <td colspan="2"><select name="qStep" id="qStep" class="form-control">
			  <option value="1" <?php if($settings['qStep']=="1") echo 'selected="selected"'; ?> >0.0</option>
			  <option value="2" <?php if($settings['qStep']=="2") echo 'selected="selected"'; ?> >0.00</option>
			  <option value="3" <?php if($settings['qStep']=="3") echo 'selected="selected"'; ?> >0.000</option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="Select the default category class. This will be used to calculate limits in formulas">Default Category:</a></td>
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
			  <option value="L" <?php if($settings['mUnit']=="L") echo 'selected="selected"'; ?> >Liter</option>
			  <option value="fl. oz." <?php if($settings['mUnit']=="fl. oz.") echo 'selected="selected"'; ?> >Fluid ounce (fl. oz.)</option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="If enabled, formula will display the chemical names of ingredients, where available, instead of the commercial name">Chemical names</a>:</td>
          <td colspan="2"><input name="chem_vs_brand" type="checkbox" id="chem_vs_brand" value="1" <?php if($settings['chem_vs_brand'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="If enabled, ingredient usage percentage will be calculated against ingredients allergens as well.">Multi-dimensional lookup:</a></td>
          <td colspan="2"><input name="multi_dim_perc" type="checkbox" id="multi_dim_perc" value="1" <?php if($settings['multi_dim_perc'] == '1'){ ?> checked="checked" <?php } ?>/></td>
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
          <td><input type="submit" name="save-general" id="save-general" value="Submit" class="btn btn-info"/></td>
          <td colspan="3">&nbsp;</td>
          </tr>
      </table>
	 </div>
     
     <div id="categories">
    	<div id="catMsg"></div>
        <div id="list_cat">
            <div class="loader-center">
                <div class="loader"></div>
                <div class="loader-text"></div>
            </div>
        </div>
     </div> 
     
    <div id="types">
     <table width="100%" border="0">
        <tr>
          <td colspan="4"><div id="ptypes"></div></td>
          </tr>
        <tr>
          <td colspan="3"><h4 class="m-0 mb-4 text-primary">&nbsp;</h4></td>
          <td width="77%" rowspan="9"><div class="bg-ptypes-image"></div></td>
        </tr>
        <tr>
          <td width="6%">EDP:</td>
          <td width="10%"><input name="edp" type="text" class="form-control" id="edp" value="<?php echo $settings['EDP'];?>"/></td>
          <td width="7%">%</td>
          </tr>
        <tr>
          <td>EDT:</td>
          <td><input name="edt" type="text" class="form-control" id="edt" value="<?php echo $settings['EDT'];?>"/></td>
          <td>%</td>
          </tr>
        <tr>
          <td>EDC:</td>
          <td><input name="edc" type="text" class="form-control" id="edc" value="<?php echo $settings['EDC'];?>"/></td>
          <td>%</td>
          </tr>
        <tr>
          <td>Parfum:</td>
          <td><input name="parfum" type="text" class="form-control" id="parfum" value="<?php echo $settings['Parfum'];?>"/></td>
          <td>%</td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td><input type="submit" name="save-perf-types" id="save-perf-types" value="Submit" class="btn btn-info"/></td>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="3">&nbsp;</td>
          </tr>
      </table>
	 </div>
      
    <div id="print">
        <table width="100%" border="0">
          <tr>
            <td colspan="4"><div id="printMsg"></div></td>
          </tr>
          <tr>
            <td width="6%">Printer:</td>
            <td width="15%"><input name="label_printer_addr" type="text" class="form-control" id="label_printer_addr" value="<?php echo $settings['label_printer_addr']; ?>" /></td>
            <td width="1%"></td>
            <td width="78%"><a href="#" class="fas fa-question-circle" rel="tipsy" title="Your printer IP/Hostname. eg: 192.168.1.1"></a></td>
          </tr>
          <tr>
            <td>Model:</td>
            <td>
            <select name="label_printer_model" id="label_printer_model" class="form-control">
			  <option value="QL-500" <?php if($settings['label_printer_model']=="QL-500") echo 'selected="selected"'; ?> >QL-500</option>
			  <option value="QL-550" <?php if($settings['label_printer_model']=="QL-550") echo 'selected="selected"'; ?> >QL-5500</option>
			  <option value="QL-560" <?php if($settings['label_printer_model']=="QL-560") echo 'selected="selected"'; ?> >QL-560</option>
			  <option value="QL-570" <?php if($settings['label_printer_model']=="QL-570") echo 'selected="selected"'; ?> >QL-570</option>
			  <option value="QL-850" <?php if($settings['label_printer_model']=="QL-850") echo 'selected="selected"'; ?> >QL-850</option>
			  <option value="QL-650TD" <?php if($settings['label_printer_model']=="QL-650TD") echo 'selected="selected"'; ?> >QL-650TD</option>
			  <option value="QL-700" <?php if($settings['label_printer_model']=="QL-700") echo 'selected="selected"'; ?> >QL-700</option>
			  <option value="QL-710W" <?php if($settings['label_printer_model']=="QL-710W") echo 'selected="selected"'; ?> >QL-710W</option>
			  <option value="QL-720NW" <?php if($settings['label_printer_model']=="QL-720NW") echo 'selected="selected"'; ?> >QL-720NW</option>
			  <option value="QL-800" <?php if($settings['label_printer_model']=="QL-800") echo 'selected="selected"'; ?> >QL-800</option>
			  <option value="QL-810W" <?php if($settings['label_printer_model']=="QL-810W") echo 'selected="selected"'; ?> >QL-810W</option>
			  <option value="QL-820NB" <?php if($settings['label_printer_model']=="QL-820NB") echo 'selected="selected"'; ?> >QL-820NB</option>
			  <option value="QL-1050" <?php if($settings['label_printer_model']=="QL-1050") echo 'selected="selected"'; ?> >QL-1050</option>
			  <option value="QL-1060N" <?php if($settings['label_printer_model']=="QL-1060N") echo 'selected="selected"'; ?> >QL-1060N</option>
            </select>
            </td>
            <td></td>
            <td><a href="#" class="fas fa-question-circle" rel="tipsy" title="Your Brother printer model"></a></td>
          </tr>
          <tr>
            <td>Label Size:</td>
            <td>
            <select name="label_printer_size" id="label_printer_size" class="form-control">   
			  <option value="12" <?php if($settings['label_printer_size']=="12") echo 'selected="selected"'; ?> >12 mm</option>
              <option value="29" <?php if($settings['label_printer_size']=="29") echo 'selected="selected"'; ?> >29 mm</option>
			  <option value="38" <?php if($settings['label_printer_size']=="38") echo 'selected="selected"'; ?> >38 mm</option>
			  <option value="50" <?php if($settings['label_printer_size']=="50") echo 'selected="selected"'; ?> >50 mm</option>
			  <option value="62" <?php if($settings['label_printer_size']=="62") echo 'selected="selected"'; ?> >62 mm</option>
			  <option value="62 --red" <?php if($settings['label_printer_size']=="62 --red") echo 'selected="selected"'; ?> >62 mm (RED)</option>
			  <option value="102" <?php if($settings['label_printer_size']=="102") echo 'selected="selected"'; ?> >102 mm</option>
			  <option value="17x54" <?php if($settings['label_printer_size']=="17x54") echo 'selected="selected"'; ?> >17x54 mm</option>
			  <option value="17x87" <?php if($settings['label_printer_size']=="17x87") echo 'selected="selected"'; ?> >17x87 mm</option>
			  <option value="23x23" <?php if($settings['label_printer_size']=="23x23") echo 'selected="selected"'; ?> >23x23 mm</option>
			  <option value="29x42" <?php if($settings['label_printer_size']=="29x42") echo 'selected="selected"'; ?> >29x42 mm</option>
			  <option value="29x90" <?php if($settings['label_printer_size']=="29x90") echo 'selected="selected"'; ?> >29x90 mm</option>
			  <option value="39x90" <?php if($settings['label_printer_size']=="39x90") echo 'selected="selected"'; ?> >39x90 mm</option>
			  <option value="39x48" <?php if($settings['label_printer_size']=="39x48") echo 'selected="selected"'; ?> >39x48 mm</option>
			  <option value="52x29" <?php if($settings['label_printer_size']=="52x29") echo 'selected="selected"'; ?> >52x29 mm</option>
			  <option value="62x29" <?php if($settings['label_printer_size']=="62x29") echo 'selected="selected"'; ?> >62x29 mm</option>
			  <option value="62x100" <?php if($settings['label_printer_size']=="62x100") echo 'selected="selected"'; ?> >62x100 mm</option>
			  <option value="102x51" <?php if($settings['label_printer_size']=="102x51") echo 'selected="selected"'; ?> >102x51 mm</option>
			  <option value="d12" <?php if($settings['label_printer_size']=="d12") echo 'selected="selected"'; ?> >D12</option>
			  <option value="d24" <?php if($settings['label_printer_size']=="d24") echo 'selected="selected"'; ?> >D24</option>
			  <option value="d58" <?php if($settings['label_printer_size']=="d58") echo 'selected="selected"'; ?> >D58</option>
            </select>
            </td>
            <td></td>
            <td><a href="#" class="fas fa-question-circle" rel="tipsy" title="Choose your tape size"></a>&nbsp;</td>
          </tr>
          <tr>
            <td>Font Size:</td>
            <td><input name="label_printer_font_size" type="text" id="label_printer_font_size" value="<?php echo $settings['label_printer_font_size']; ?>" class="form-control"/></td>
            <td>&nbsp;</td>
            <td><a href="#" class="fas fa-question-circle" rel="tipsy" title="Label font size"></a></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td><input type="submit" name="save-print" id="save-print" value="Submit" class="btn btn-info"/></td>
            <td colspan="3">&nbsp;</td>
          </tr>
        </table>
</div>

    <div id="users">
    	<div id="usrMsg"></div>
        <div id="list_users">
            <div class="loader-center">
                <div class="loader"></div>
                <div class="loader-text"></div>
            </div>
        </div>
    </div>
     <div id="brand">
         <table width="100%" border="0">
           <tr>
             <td colspan="2"><div id="brandMsg"></div></td>
            </tr>
           <tr>
             <td width="7%">Brand Name:</td>
             <td width="93%"><input name="brandName" type="text" class="form-control" id="brandName" value="<?php echo $settings['brandName'];?>" /></td>
           </tr>
           <tr>
             <td>Address:</td>
             <td><input name="brandAddress" type="text" class="form-control" id="brandAddress" value="<?php echo $settings['brandAddress'];?>"/></td>
           </tr>
           <tr>
             <td>Email:</td>
             <td><input name="brandEmail" type="text" class="form-control" id="brandEmail" value="<?php echo $settings['brandEmail'];?>" /></td>
           </tr>
           <tr>
             <td>Contact No:</td>
             <td><input name="brandPhone" type="text" class="form-control" id="brandPhone" value="<?php echo $settings['brandPhone'];?>" /></td>
           </tr>
            <tr>
               <td>Logo:</td>
				<td colspan="3">
                    <form method="post" action="" enctype="multipart/form-data" id="myform">
                    	<input type="file" id="brandLogo" name="brandLogo" />
                    	<input type="button" class="btn btn-info" value="Upload" id="brandLogo_upload">
                    </form>
                </td>
		   </tr>
           <tr>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
           </tr>
           <tr>
            <td><input type="submit" name="save-brand" id="save-brand" value="Submit" class="btn btn-info"/></td>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
           </tr>
           <tr>
            </tr>
         </table>
     </div>
     
     <div id="api">
	API can be used to access PV Pro from other apps like PV Light APP
	   <table width="100%" border="0">
		<tr>
	      <td colspan="3"><div id="pvAPIMsg"></div></td>
	      </tr>
	    <tr>
	      <td width="9%" height="28">Enable API</td>
	      <td width="9%" valign="middle"><input name="pv_api" type="checkbox" id="pv_api" value="1" <?php if($settings['api'] == '1'){ ?> checked="checked" <?php } ?>/></td>
	      <td width="82%">&nbsp;</td>
	      </tr>
	    <tr>
	      <td>API Key</td>
	      <td valign="middle"><input name="pv_api_key" type="text" class="form-control" id="pv_api_key" value="<?=$settings['api_key']?>" /></td>
	      <td>&nbsp;</td>
	      </tr>
	    <tr>
	      <td>&nbsp;</td>
	      <td valign="middle">&nbsp;</td>
	      <td>&nbsp;</td>
	      </tr>
	    <tr>
	      <td><input type="submit" name="save-api" id="save-api" value="Submit" class="btn btn-info"/></td>
	      <td valign="middle">&nbsp;</td>
	      <td>&nbsp;</td>
	      </tr>
	    </table> 
     </div>
    
     <div id="pvonline">
        <table width="100%" border="0">
          <tr>
            <td colspan="3"><div id="pvOnMsg"></div></td>
          </tr>
          <tr>
            <td width="9%" height="30"><a href="#" rel="tipsy" title="Please enter your PV Online email">Email:</a></td>
            <td width="9%"><input name="pv_online_email" type="text" class="form-control" id="pv_online_email" value="<?php echo $pv_online['email'];?>" /></td>
            <td width="82%">&nbsp;</td>
          </tr>
          <tr>
            <td height="24"><a href="#" rel="tipsy" title="Your PV Online password.">Password:</a></td>
            <td><input name="pv_online_pass" type="password" class="form-control" id="pv_online_pass" /></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="29"><a href="https://online.jbparfum.com/register.php" target="_blank">Create an account</a></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="31"><a href="https://online.jbparfum.com/forgotpass.php" target="_blank">Forgot Password?</a></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><?php require(__ROOT__.'/pages/privacy_note.php');?></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><input type="submit" name="save-pv-on" id="save-pv-on" value="Submit" class="btn btn-info"/></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
		</table>
     </div>
<div id="maintenance">
  <table width="100%" border="0">
    <tr>
      <td width="13%">&nbsp;</td>
      <td width="87%">&nbsp;</td>
    </tr>
    <tr>
      <td><ul>
        <li><a href="pages/operations.php?do=backupDB">Backup DB</a></li>
        </ul></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><ul>
        <li><a href="pages/operations.php?do=backupFILES">Backup Files</a></li>
      </ul></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><ul>
        <li><a href="pages/maintenance.php?do=restoreDB" class="popup-link">Restore DB</a></li>
      </ul></td>
      <td>&nbsp;</td>
    </tr>
  </table>
  </div>
  </div>
 </div>
</div>
<script type="text/javascript" language="javascript" >
$(document).ready(function() {
	$('#save-general').click(function() {
							  
		$.ajax({ 
			url: 'pages/update_settings.php', 
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
				grp_formula: $("#grp_formula").is(':checked'),
				chem_vs_brand: $("#chem_vs_brand").is(':checked'),
				pubChem: $("#pubChem").is(':checked'),
				chkVersion: $("#chkVersion").is(':checked'),
				multi_dim_perc: $("#multi_dim_perc").is(':checked'),
				mUnit: $("#mUnit").val(),
				api: $("#pv_api").val(),
				api_key: $("#pv_api_key").val(),

			},
			dataType: 'html',
			success: function (data) {
				$('#inMsg').html(data);
			}
		  });
 	});
	
$('#save-api').click(function() {

	$.ajax({ 
		url: 'pages/update_settings.php', 
		type: 'POST',
		data: {
			manage: 'api',
				
			api: $("#pv_api").is(':checked'),
			api_key: $("#pv_api_key").val(),
			},
			dataType: 'html',
			success: function (data) {
				$('#pvAPIMsg').html(data);
			}
		 });
});

$('#save-perf-types').click(function() {
							  
		$.ajax({ 
			url: 'pages/update_settings.php', 
			type: 'POST',
			data: {
				manage: 'perfume_types',
				
				edp: $("#edp").val(),
				edc: $("#edc").val(),
				edt: $("#edt").val(),
				parfum: $("#parfum").val()
				},
			dataType: 'html',
			success: function (data) {
				$('#ptypes').html(data);
			}
		  });
  });
	
	$('#save-print').click(function() {
							  
		$.ajax({ 
			url: 'pages/update_settings.php', 
			type: 'POST',
			data: {
				manage: 'print',
				
				label_printer_addr: $("#label_printer_addr").val(),
				label_printer_model: $("#label_printer_model").val(),
				label_printer_size: $("#label_printer_size").val(),
				label_printer_font_size: $("#label_printer_font_size").val()
				},
			dataType: 'html',
			success: function (data) {
				$('#printMsg').html(data);
			}
		  });
  });
	
	$('#save-pv-on').click(function() {
							  
		$.ajax({ 
			url: 'pages/update_settings.php', 
			type: 'POST',
			data: {
				manage: 'pvonline',
				
				pv_online_email: $("#pv_online_email").val(),
				pv_online_pass: $("#pv_online_pass").val(),
				
				},
			dataType: 'html',
			success: function (data) {
				$('#pvOnMsg').html(data);
			}
		  });
  });
	
	$('#save-brand').click(function() {
							  
		$.ajax({ 
			url: 'pages/update_settings.php', 
			type: 'POST',
			data: {
				manage: 'brand',
				
				brandName: $("#brandName").val(),
				brandAddress: $("#brandAddress").val(),
				brandEmail: $("#brandEmail").val(),
				brandPhone: $("#brandPhone").val(),
				},
			dataType: 'html',
			success: function (data) {
				$('#brandMsg').html(data);
			}
		  });
  });
	
	

	$("#brandLogo_upload").click(function(){
        $("#brandMsg").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
		$("#brandLogo_upload").prop("disabled", true);
        $("#brandLogo_upload").prop('value', 'Please wait...');
		
		var fd = new FormData();
        var files = $('#brandLogo')[0].files;
        
        if(files.length > 0 ){
           fd.append('brandLogo',files[0]);

           $.ajax({
              url: 'pages/upload.php?type=brand',
              type: 'post',
              data: fd,
              contentType: false,
              processData: false,
              success: function(response){
                 if(response != 0){
                    $("#brandMsg").html(response);
					$("#brandLogo_upload").prop("disabled", false);
        			$("#brandLogo_upload").prop('value', 'Upload');
                 }else{
                    $("#brandMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
					$("#brandLogo_upload").prop("disabled", false);
        			$("#brandLogo_upload").prop('value', 'Upload');
                 }
              },
           });
        }else{
			$("#brandMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
			$("#brandLogo_upload").prop("disabled", false);
   			$("#brandLogo_upload").prop('value', 'Upload');
        }
    });	
})

function catDel(catId){
	$.ajax({ 
		url: 'pages/update_settings.php', 
		type: 'POST',
		data: {
			action: 'delete',
			catId: catId,
		},
		dataType: 'html',
		success: function (data) {
			$('#catMsg').html(data);
			list_cat();
		}
	});
}

function usrDel(userId){
	$.ajax({ 
		url: 'pages/update_settings.php', 
		type: 'POST',
		data: {
			action: 'delete',
			userId: userId,
		},
		dataType: 'html',
		success: function (data) {
			$('#usrMsg').html(data);
			list_users();
		}
	});
}

</script>

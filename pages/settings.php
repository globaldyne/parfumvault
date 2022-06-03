<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php'); 
						   
 
$pv_online = mysqli_fetch_array(mysqli_query($conn, "SELECT email,enabled FROM pv_online"));
$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");

while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}
require(__ROOT__.'/inc/settings.php');

?>
<script>
$(function() {
  $("#settings").tabs();
});


</script>
<div class="container-fluid">

<h2 class="m-0 mb-4 text-primary"><a href="?do=settings">Settings</a></h2>
<div id="settings">
     <ul>
         <li><a href="#general"><span>General</span></a></li>
         <li><a href="#categories"><span>Ingredient Categories</span></a></li>
         <li><a href="#frmCat">Formula Categories</a></li>
         <li><a href="#types">Perfume Types</a></li>
         <li><a href="#print"><span>Printing</span></a></li>
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
            <?php require('privacy_note.php');?></td>
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
     <div id="frmCat">
    	<div id="fcatMsg"></div>
        <div id="list_fcat">
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
            <td width="78%"><a href="#" class="fas fa-question-circle" rel="tip" title="Your printer IP/Hostname. eg: 192.168.1.1"></a></td>
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
            <td><a href="#" class="fas fa-question-circle" rel="tip" title="Your Brother printer model"></a></td>
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
            <td><a href="#" class="fas fa-question-circle" rel="tip" title="Choose your tape size"></a>&nbsp;</td>
          </tr>
          <tr>
            <td>Font Size:</td>
            <td><input name="label_printer_font_size" type="text" id="label_printer_font_size" value="<?php echo $settings['label_printer_font_size']; ?>" class="form-control"/></td>
            <td>&nbsp;</td>
            <td><a href="#" class="fas fa-question-circle" rel="tip" title="Label font size"></a></td>
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
            <td height="29">&nbsp;</td>
            <td>&nbsp;</td>
            <td width="82%">&nbsp;</td>
          </tr>
          
          <?php
		  $auth = pvOnlineValAcc($pvOnlineAPI, $user['email'], $user['password'], $ver);
		  if($auth['code'] == '001'){?>
          <tr>
            <td height="29"><a href="#" rel="tip" data-placement="right" title="Enable or disable PV Online access.">Enable Service:</a></td>
            <td><input name="pv_online_state" type="checkbox" id="pv_online_state" value="1" <?php if($pv_online['enabled'] == '1'){ ?> checked <?php } ?>/></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="29"><a href="#" rel="tip" data-placement="bottom" title="To enable or disable formula sharing service, please login to PVOnline and navigate to the profile section.">Enable Formula sharing:</a></td>
            <td><div id="sharing_status_state"><input name="sharing_status" type="checkbox" id="sharing_status" value="1"/></div></td>
            <td>&nbsp;</td>
          </tr>
          <?php }elseif($auth['code'] == '002'){?>
          <tr>
            <td colspan="3">
                <div class="alert alert-danger">
    				<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Oops! <?=$auth['msg']?>.</h4>
    				<p>Please make sure your local installation password and PV Online account password match.</p>
    				<hr>
    				<p class="mb-0">You can <a href="https://online.jbparfum.com/forgotpass.php" target="_blank">reset</a> your PV Online password</p>
				</div>
            </td>
          </tr>
          <?php }elseif($auth['code'] == '003'){?>
          <tr>
            <td colspan="3">
            <div id="pv_account_error">
            	<div class="alert alert-warning">
    				<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Oops!  <?=$auth['msg']?>.</h4>
    				<p>Looks like you haven't created a PV Online account yet.</p>
                    <p>To be able to use PV Online services, you need to register an account.</p>
    				<hr>
    				<p class="mb-0 pv_point_gen" id="autoCreateAcc"><strong>Click here to create an account and configure you local installation</strong></p>
				</div>
            </div>
			</td>
          </tr>
          <?php }else{ ?>
           <tr>
            <td colspan="3">
            <div id="pv_account_error">
            	<div class="alert alert-danger">
    				<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Oops! Server Error.</h4>
    				<p>We are unable to connect to PV Online upstream</p>
                    <p>Server may experiencing high load or connectivity issues.</p>
                    <p>While the server is down any PV Online intergration will not work or return errors.</p>
    				<hr>
				</div>
            </div>
			</td>
          </tr>
          <?php } ?>
          <tr>
            <td height="29">&nbsp;</td>
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
	list_cat();
	list_fcat();
	<?php if($pv_online['email'] && $pv_online['password'] && $pv_online['enabled'] == '1'){?>
		getPVProfile();
	<?php } ?>
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
				grp_formula: $("#grp_formula").val(),
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
			dataType: 'json',
			success: function (data) {
				if(data.error){
					var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
				}else if (data.success){
					var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
					$("#pv_online_state").prop('checked', true);
				}
				$('#pvOnMsg').html(rmsg);
			}
		  });
  	});

	//ENABLE OR DISABLE PV ONLINE
	$('#pv_online_state').on('change', function() {
		if($("#pv_online_state").is(':checked')){
			var val = 1;
		}else{
			var val = 0;
			$("#sharing_status").prop('disabled', true);
		}
		$.ajax({ 
			url: 'pages/update_settings.php', 
			type: 'POST',
			data: {
				manage: 'pvonline',
				state_update: '1',
				pv_online_state: val,
				},
			dataType: 'json',
			success: function (data) {
				if(data.error){
					var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
				}else if (data.success){
					var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>PV Online service is now <strong>'+data.success+'</strong></div>';
					if (data.success == 'active'){
						$("#sharing_status").prop('disabled', false);
						getPVProfile();
					}else if (data.success == 'in-active'){
						$("#sharing_status").prop('disabled', true);
					}
				}
				$('#pvOnMsg').html(rmsg);
			}
		  });
	});
	
	//ENABLE OR DISABLE FORMULA SHARING
	$('#sharing_status').on('change', function() {
		if($("#sharing_status").is(':checked')){
			var val = 1;
		}else{
			var val = 0;
		}
		$.ajax({ 
			url: 'pages/update_settings.php', 
			type: 'POST',
			data: {
				manage: 'pvonline',
				share_update: '1',
				pv_online_share: val,
				},
			dataType: 'json',
			success: function (data) {
				if(data.error){
					var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
				}else if (data.success){
					var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>PV Online sharing service is now <strong>'+data.success+'</strong></div>';
				}
				$('#pvOnMsg').html(rmsg);
			}
		  });
	});
	
	$('#pv_account_error').on('click', '[id*=autoCreateAcc]', function () {
		$('#pv_account_error').html('<div class="alert alert-info"><img src="/img/loading.gif"/> Please wait, configuring the system...<p><strong>Please do not close, refresh or navigate away from this page. You will be automatically redirected upon a succesfull installation.</strong></p></div>');															
		
		$.ajax({ 
			url: '/core/configureSystem.php', 
			type: 'POST',
			data: {
				action: 'create_pv_account',
				fullName: "<?=$user['fullName']?>",
				email: "<?=$user['email']?>",
				password: "<?=$user['password']?>",
			},
			dataType: 'json',
			success: function (data) {
				if (data.success){
					$('#pv_account_error').html('<div class="alert alert-success">'+data.success+'</div>');
				    //getPVProfile();
				}else{
					$('#pv_account_error').html('<div class="alert alert-danger">'+data.error+'</div>');
				}
			},
			error: function () {
				$('#pv_account_error').html('<div class="alert alert-danger">Unable to connect, please try again later</div>');
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



function list_cat(){
$.ajax({ 
	url: 'pages/listCat.php', 
	dataType: 'html',
		success: function (data) {
			$('#list_cat').html(data);
		}
	});
};

function list_fcat(){
$.ajax({ 
	url: 'pages/listFrmCat.php', 
	dataType: 'html',
		success: function (data) {
			$('#list_fcat').html(data);
		}
	});
};

function getPVProfile(){
	$.ajax({ 
		url: '<?=$pvOnlineAPI?>',
		dataType: 'json',
		data: {
			username: "<?=$pv_online['email']?>",
			password: "<?=$pv_online['password']?>",
			do: 'getProfile'
		},
		type: 'POST',
		success: function (data) {
			if(data.error){
				$('#msg').html('<div class="alert alert-danger">PV Online '+data.error+' You can <a href="javascript:disablePV()">disable</a> PV integration or <a href="https://online.jbparfum.com/forgotpass.php" target="_blank">reset</a> your PV Online password</p>');
			}else if(data.userProfile.formulaSharing == 0){
				$("#sharing_status").prop('checked', false);
			}else if (data.userProfile.formulaSharing == 1){
				$("#sharing_status").prop('checked', true);
			}
		},
		error: function () {
				$('#sharing_status_state').html('<span class="label label-danger">Unable to fecth data</span>');
			}
			
		});
};

function disablePV(){
	$.ajax({ 
		url: 'pages/update_settings.php',
		dataType: 'json',
		data: {
			pv_online_state: '0',
			state_update: '1',
			manage: 'pvonline'
		},
		type: 'POST',
		success: function (data) {
			if(data.error){
				$('#msg').html('<div class="alert alert-danger">PV Online state update '+data.error+'</div>');	
			}else if(data.success){
				$('#msg').html('<div class="alert alert-success">PV Online state update '+data.success+'</div>');	
			}
		},
		error: function () {
				$('#msg').html('<span class="label label-danger">Unable to update settings</span>');
			}
			
		});
};

</script>

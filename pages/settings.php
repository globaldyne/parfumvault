<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php');
require_once(__ROOT__.'/inc/settings.php');

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
         <li class="active"><a href="#general" id="general_tab" role="tab" data-toggle="tab">General</a></li>
         <li><a href="#categories" id="cat_tab" role="tab" data-toggle="tab">Ingredient Categories</a></li>
         <li><a href="#frmCat" id="frmCat_tab" role="tab" data-toggle="tab">Formula Categories</a></li>
         <li><a href="#perfumeTypes" id="perfume_types_tab" role="tab" data-toggle="tab">Perfume Types</a></li>
         <li><a href="#templates" id="templates_tab" role="tab" data-toggle="tab">HTML Templates</a></li>
         <li><a href="#print" id="print_tab" role="tab" data-toggle="tab">Printing</span></a></li>
         <li><a href="#brand" id="brand_tab" role="tab" data-toggle="tab">My Brand</span></a></li>
         <li><a href="#maintenance" id="maintenance_tab">Maintenance</a></li>
         <li><a href="#api" id="api_tab" role="tab" data-toggle="tab">API</a></li>
         <li><a href="#about" id="about_tab" role="tab" data-toggle="tab">About</a></li>
     </ul>
     
     <div class="tab-content">
     
     <div class="tab-pane fade active in tab-content" id="general">
        <div id="get_general">
            <div class="loader-center">
                <div class="loader"></div>
                <div class="loader-text"></div>
            </div>
        </div> 
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
     
    <div id="perfumeTypes">
   		<div id="ptMsg"></div>
        <div id="list_ptypes">
            <div class="loader-center">
                <div class="loader"></div>
                <div class="loader-text"></div>
            </div>
        </div>
	</div>
      
    <div id="templates">
   		<div id="tmplMsg"></div>
        <div id="list_templates">
            <div class="loader-center">
                <div class="loader"></div>
                <div class="loader-text"></div>
            </div>
        </div>
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
	   <div class="loader-center">
       		<div class="loader"></div>
            <div class="loader-text"></div>
        </div>
     </div>
     
     <div id="api">
	   <div class="loader-center">
       		<div class="loader"></div>
            <div class="loader-text"></div>
        </div>
     </div>
   
     
	<div id="maintenance">
        <div class="loader-center">
            <div class="loader"></div>
            <div class="loader-text"></div>
        </div>
  	</div>
    
    <div id="about">
        <div class="loader-center">
            <div class="loader"></div>
            <div class="loader-text"></div>
        </div>
  	</div>
    
  </div>
 </div>
</div>
</div>


<script type="text/javascript" language="javascript" >
$(document).ready(function() {

	$('#save-perf-types').click(function() {
		$.ajax({ 
			url: '/pages/update_settings.php', 
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
			url: '/pages/update_settings.php', 
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

	
});//END DOC

function list_cat(){
	$.ajax({ 
		url: '/pages/views/settings/listCat.php', 
		dataType: 'html',
		success: function (data) {
			$('#list_cat').html(data);
		}
	});
};


function list_fcat(){
	$.ajax({ 
		url: '/pages/views/settings/listFrmCat.php', 
		dataType: 'html',
		success: function (data) {
			$('#list_fcat').html(data);
		}
	});
};

function list_ptypes(){
	$.ajax({ 
		url: '/pages/views/settings/perfume_types.php', 
		dataType: 'html',
		success: function (data) {
			$('#list_ptypes').html(data);
		}
	});
};

function list_templates(){
	$.ajax({ 
		url: '/pages/views/settings/templates.php', 
		dataType: 'html',
		success: function (data) {
			$('#list_templates').html(data);
		}
	});
};

function get_maintenance(){
	$.ajax({ 
		url: '/pages/views/settings/maintenance.php', 
		dataType: 'html',
		success: function (data) {
			$('#maintenance').html(data);
		}
	});
};

function get_about(){
	$.ajax({ 
		url: '/pages/views/settings/about.php', 
		dataType: 'html',
		success: function (data) {
			$('#about').html(data);
		}
	});
};

function get_api(){
	$.ajax({ 
		url: '/pages/views/settings/api.php', 
		dataType: 'html',
		success: function (data) {
			$('#api').html(data);
		}
	});
};

function get_general(){
	$.ajax({ 
		url: '/pages/views/settings/general.php', 
		dataType: 'html',
		success: function (data) {
			$('#general').html(data);
		}
	});
};

function get_brand(){
	$.ajax({ 
		url: '/pages/views/settings/branding.php', 
		dataType: 'html',
		success: function (data) {
			$('#brand').html(data);
		}
	});
};
</script>
<script src="/js/settings.tabs.js"></script>

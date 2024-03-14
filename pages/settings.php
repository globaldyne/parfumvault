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

<h2 class="m-0 mb-4 text-primary"><a href="/?do=settings">Settings</a></h2>
<div id="settings">
     <ul>
         <li class="active"><a href="#general" id="general_tab" role="tab" data-bs-toggle="tab">General</a></li>
         <li><a href="#categories" id="cat_tab" role="tab" data-bs-toggle="tab">Ingredient Categories</a></li>
         <li><a href="#frmCat" id="frmCat_tab" role="tab" data-bs-toggle="tab">Formula Categories</a></li>
         <li><a href="#perfumeTypes" id="perfume_types_tab" role="tab" data-bs-toggle="tab">Perfume Types</a></li>
         <li><a href="#templates" id="templates_tab" role="tab" data-bs-toggle="tab">HTML Templates</a></li>
         <li><a href="#brand" id="brand_tab" role="tab" data-bs-toggle="tab">My Brand</span></a></li>
         <li><a href="#maintenance" id="maintenance_tab">Maintenance</a></li>
         <li><a href="#bkProviders" id="bkProviders_tab">Backup Providers</a></li>
         <li><a href="#api" id="api_tab" role="tab" data-bs-toggle="tab">API</a></li>
         <li><a href="#about" id="about_tab" role="tab" data-bs-toggle="tab">About</a></li>
     </ul>
     
     <div class="tab-content">
     
     <div class="tab-pane active" id="general">
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
    
    <div id="bkProviders">
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

function get_bkProviders(){
	$.ajax({ 
		url: '/pages/views/settings/remote_backup.php', 
		dataType: 'html',
		success: function (data) {
			$('#bkProviders').html(data);
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

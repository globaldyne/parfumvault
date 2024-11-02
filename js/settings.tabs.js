/*
Settings tabs
*/

$(document).ready(function() {
	
	
	$('#general_tab').on( 'click', function () {
		get_general();
	});
	
	$('#cat_tab').on( 'click', function () {
		list_cat();
	});
		
	$('#frmCat_tab').on( 'click', function () {
		list_fcat();
	});
	
	$('#perfume_types_tab').on( 'click', function () {
		list_ptypes();
	});
		
	$('#templates_tab').on( 'click', function () {
		list_templates();
	});
		
	$('#sds_tab').on( 'click', function () {
		list_sds_settings();
	});
		
	$('#brand_tab').on( 'click', function () {
		
	});
		
	$('#maintenance_tab').on( 'click', function () {
		get_maintenance();
	});
		
	$('#pvOnline_tab').on( 'click', function () {
		get_pvonline();
	});
	
	$('#api_tab').on( 'click', function () {
		
	});
	
	$('#about_tab').on( 'click', function () {
		get_about();
	});
	
	$('#api_tab').on( 'click', function () {
		get_api();
	});
	
	$('#brand_tab').on( 'click', function () {
		get_brand();
	});
	
	$('#integrations_tab').on( 'click', function () {
		get_integrations();
	});
	
	$('#logs_tab').on( 'click', function () {
		get_syslogs();
	});
	
	function get_syslogs(){
		$.ajax({ 
			url: '/pages/views/settings/sysLogs.php', 
			dataType: 'html',
			success: function (data) {
				$('#syslogs').html(data);
			},
			error: function (xhr, status, error) {
				$('#syslogs').html('<div class="mt-4 alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check configuration as this page may have been disabled / restricted. '+ error +'</div>');
			}
		});
	}
	
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
	
	function list_sds_settings(){
		$.ajax({ 
			url: '/pages/views/settings/sds.php', 
			dataType: 'html',
			success: function (data) {
				$('#list_sds_settings').html(data);
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
	
	function get_integrations(){
		$.ajax({ 
			url: '/pages/views/settings/integrations.php', 
			dataType: 'html',
			success: function (data) {
				$('#integrations').html(data);
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
	
	get_general();
});
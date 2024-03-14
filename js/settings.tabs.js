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
		
	$('#print_tab').on( 'click', function () {
		
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
	
	$('#bkProviders_tab').on( 'click', function () {
		get_bkProviders();
	});
	
	get_general();
});
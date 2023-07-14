/*
Main formula tabs
*/

$(document).ready(function() {
						   
	$('#formula_tab').on( 'click', function () {
		fetch_formula();
		reset_solv();
	});
	
	$('#impact_tab').on( 'click', function () {
		fetch_impact();
	});
	
	$('#pyramid_tab').on( 'click', function () {
		fetch_pyramid();
	});
	
	$('#summary_tab').on( 'click', function () {
		fetch_summary();
	});
	
	$('#reps_tab').on( 'click', function () {
		fetch_replacements();
	});
	
	$('#attachments_tab').on( 'click', function () {
		fetch_attachments();
	});
	
	$('#revisions_tab').on( 'click', function () {
		fetch_revisions();
	});
	
	$('#formula_settings_tab').on( 'click', function () {
		fetch_formula_settings();
	});
	
	$('#timeline_tab').on( 'click', function () {
		fetch_timeline();
	});

});
/*
Main formula tabs
*/

$(document).ready(function() {
	
	
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
	
})
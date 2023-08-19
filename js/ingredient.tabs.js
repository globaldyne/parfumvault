/*
Ingredient management tabs
*/

$(document).ready(function() {
	$('#general_tab').on( 'click', function () {
		$('#msg_general').html('');
		fetch_generalData();
	});
	
	$('#usage_tab').on( 'click', function () {
		fetch_usageData();
	});
	
	$('#sups_tab').on( 'click', function () {
		fetch_sups();
	});
	
	$('#techs_tab').on( 'click', function () {
		fetch_techs();
	});
		
	$('#docs_tab').on( 'click', function () {
		fetch_docs();
	});
		
	$('#synonyms_tab').on( 'click', function () {
		fetch_syn();
	});
	
	$('#impact_tab').on( 'click', function () {
		fetch_impact();
	});
		
	$('#cmps_tab').on( 'click', function () {
		fetch_cmps();
	});
	
	$('#safety_tab').on( 'click', function () {
		fetch_safety();
	});
	
	$('#pubChem_tab').on( 'click', function () {
		fetch_pubChem();
	});
		
	$('#privacy_tab').on( 'click', function () {
		fetch_privacy();
	});
		
	$('#whereUsed_tab').on( 'click', function () {
		fetch_whereUsed();
	});
	
	$('#reps_tab').on( 'click', function () {
		fetch_reps();
	});
	
	fetch_generalData();
});
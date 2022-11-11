/*
Ingredient management tabs
*/

$(document).ready(function() {
	
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
	
	$('#cmps_tab').on( 'click', function () {
		fetch_cmps();
	});
		
	$('#pubChem_tab').on( 'click', function () {
		fetch_pubChem();
	});
		
	$('#whereUsed_tab').on( 'click', function () {
		fetch_whereUsed();
	});
	
	$('#reps_tab').on( 'click', function () {
		fetch_reps();
	})
	
})
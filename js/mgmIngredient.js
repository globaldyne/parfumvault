/*
Ingredient management
*/

function search() { 
	$("#odor").val('Loading...');
	
	if ($('#cas').val()) {
		var	ingName = $('#cas').val();
	}else if($('#name').val()) {
		var	ingName = $('#name').val();
	}else{
		var	ingName = myIngName
	}
	
	$.ajax({ 
		url: 'searchTGSC.php', 
		type: 'POST',
		data: {
			name: ingName
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				tgsc = '<input name="odor" id="odor" type="text" class="form-control" value="' + data.success + '"/>';
			}else if(data.error){
				tgsc = '<input name="odor" id="odor" type="text" class="form-control" placeholder="' + data.error + '"/>';
			}
			$('#TGSC').html(tgsc);
		}
	});
};



function fetch_whereUsed(){
	$.ajax({ 
		url: 'whereUsed.php', 
		type: 'POST',
		data: {
			id: btoa(myIngName)
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_whereUsed').html(data);
		},
	});
}

function fetch_usageData(){
	$.ajax({ 
		url: 'views/ingredients/usageData.php', 
		type: 'POST',
		data: {
			ingID: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_usageData').html(data);
		},
	});
}


function fetch_sups(){
	$.ajax({ 
		url: 'ingSuppliers.php', 
		type: 'GET',
		data: {
			id: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_suppliers').html(data);
		},
	});
}
	
function fetch_techs(){
	$.ajax({ 
		url: 'views/ingredients/techData.php', 
		type: 'POST',
		data: {
			ingID: myIngID,
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_tech_data').html(data);
		},
	});
}

function fetch_docs(){
	$.ajax({ 
		url: 'ingDocuments.php', 
		type: 'GET',
		data: {
			id: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_documents').html(data);
		},
	});
}


function fetch_impact(){
	$.ajax({ 
		url: 'views/ingredients/impactData.php', 
		type: 'POST',
		data: {
			ingID: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_impact').html(data);
		},
	});
}

function fetch_cmps(){
	$.ajax({ 
		url: 'compos.php', 
		type: 'GET',
		data: {
			name:  btoa(myIngName),
			id: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_composition').html(data);
		},
	});
}

function fetch_safety(){
	$.ajax({ 
		url: 'views/ingredients/safetyData.php', 
		type: 'POST',
		data: {
			ingID: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_safety').html(data);
		},
	});
}

function fetch_privacy(){
	$.ajax({ 
		url: 'views/ingredients/privacyData.php', 
		type: 'POST',
		data: {
			ingID: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_privacy').html(data);
		},
	});
}

function fetch_syn(){
	$.ajax({ 
		url: 'synonyms.php', 
		type: 'POST',
		data: {
			name:  btoa(myIngName),
			cas: myCAS || btoa(myIngID)
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_synonyms').html(data);
		},
	});
}

function fetch_reps(){
	$.ajax({ 
		url: 'views/ingredients/repData.php', 
		type: 'POST',
		data: {
			id: btoa(myIngName),
			cas: btoa(myCAS)
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_replacements').html(data);
		},
	});
}

//Clone
$('#cloneIng').on('click', '[id*=cloneME]', function () {
	$.ajax({ 
		url: 'update_data.php', 
		type: 'POST',
		data: {
			action: 'clone',
			new_ing_name: $("#cloneIngName").val(),
			old_ing_name: myIngName,
			ing_id: myIngID
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
			}else if(data.error){
				msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#clone_msg').html(msg);
		}
	});
});

if (typeof myCAS !== 'undefined' && myPCH == '1') {
	function fetch_pubChem(){
		$.ajax({ 
			url: 'pubChem.php', 
			type: 'GET',
			data: {
				cas: myCAS
			},
			dataType: 'html',
			success: function (data) {
				$('#pubChemData').html(data);
			}
		});
	}
}

if (typeof myIngID !== 'undefined') {
	function reload_overview() {
		$('#ingOverview').html('<img src="/img/loading.gif"/>');
	
		$.ajax({ 
			url: 'ingOverview.php', 
			type: 'GET',
			data: {
				id: myIngID
			},
			dataType: 'html',
			success: function (data) {
				$('#ingOverview').html(data);
			}
		});
	};
	reload_overview();
}
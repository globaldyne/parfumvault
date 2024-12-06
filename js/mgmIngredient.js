/*
Ingredient management
*/

function fetch_generalData(){
	$.ajax({ 
		url: '/pages/views/ingredients/mgmGeneral.php', 
		type: 'GET',
		data: {
			id: myIngID,
			newIngName: newIngName,
			newIngCAS: newIngCAS
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_generalData').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_generalData').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};

function fetch_whereUsed(){
	$.ajax({ 
		url: '/pages/views/ingredients/whereUsed.php', 
		type: 'GET',
		data: {
			ingName: btoa(myIngName),
			ingID: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_whereUsed').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_whereUsed').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
}

function fetch_usageData(){
	$.ajax({ 
		url: '/pages/views/ingredients/usageData.php', 
		type: 'GET',
		data: {
			ingID: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_usageData').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_usageData').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
}


function fetch_sups(){
	$.ajax({ 
		url: '/pages/views/ingredients/ingSuppliers.php', 
		type: 'GET',
		data: {
			id: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_suppliers').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_suppliers').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
}
	
function fetch_techs(){
	$.ajax({ 
		url: '/pages/views/ingredients/techData.php', 
		type: 'GET',
		data: {
			ingID: myIngID,
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_tech_data').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_tech_data').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
}

function fetch_docs(){
	$.ajax({ 
		url: '/pages/views/ingredients/ingDocuments.php', 
		type: 'GET',
		data: {
			id: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_documents').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_documents').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
}


function fetch_impact(){
	$.ajax({ 
		url: '/pages/views/ingredients/impactData.php', 
		type: 'GET',
		data: {
			ingID: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_impact').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_impact').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
}

function fetch_cmps(){
	$.ajax({ 
		url: '/pages/views/ingredients/compos.php', 
		type: 'GET',
		data: {
			name:  btoa(myIngName),
			id: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_composition').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_composition').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
}

function fetch_safety(){
	$.ajax({ 
		url: '/pages/views/ingredients/safetyData.php', 
		type: 'GET',
		data: {
			ingID: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_safety').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_safety').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
}

function fetch_privacy(){
	$.ajax({ 
		url: '/pages/views/ingredients/privacyData.php', 
		type: 'GET',
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
		url: '/pages/views/ingredients/synonyms.php', 
		type: 'GET',
		data: {
			name:  btoa(myIngName),
			cas: myCAS || btoa(myIngID)
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_synonyms').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_synonyms').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
}

function fetch_reps(){
	$.ajax({ 
		url: '/pages/views/ingredients/repData.php', 
		type: 'GET',
		data: {
			id: btoa(myIngName),
			cas: myCAS,
			ingID: myIngID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_replacements').html(data);
		},
		error: function (xhr, status, error) {
			$('#fetch_replacements').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
}

//Clone
$('#duplicateIng').on('click', '[id*=duplicateME]', function () {
	$.ajax({ 
		url: '/core/core.php', 
		type: 'POST',
		data: {
			action: 'duplicate_ingredient',
			new_ing_name: $("#duplicateIngName").val(),
			old_ing_name: myIngName,
			ing_id: myIngID
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
			}else if(data.error){
				msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
			}
			$('#duplicate_msg').html(msg);
		},
		error: function (xhr, status, error) {
			$('#duplicate_msg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
});


$('#genDOC').on('click', '[id*=dis-genDOC]', function () {
	$("#doc_res").html('');
});
	
//Generate ing sds
$('#genDOC').on('click', '[id*=generateDOC]', function () {
	$("#doc_res").html('<div class="alert alert-info"><img src="/img/loading.gif"/> Please wait, we generating your document</div>');

	$.ajax({ 
		url: '/core/genDoc.php', 
		type: 'POST',
		data: {
			action: 'generateDOC',
			kind: 'ingredient',
			name: myIngName,
			id: myIngID
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				msg = '<div class="alert alert-success">' + data.success + '</div>';
			}else if(data.error){
				msg = '<div class="alert alert-danger">' + data.error + '</div>';
			}
			$('#doc_res').html(msg);
		},
		error: function (xhr, status, error) {
			$('#doc_res').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mr-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
});

//Rename
$('#renameIng').on('click', '[id*=renameME]', function () {
	$.ajax({ 
		url: '/core/core.php', 
		type: 'POST',
		data: {
			action: 'rename',
			new_ing_name: $("#renameIngName").val(),
			old_ing_name: myIngName,
			ing_id: myIngID
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				window.location.href = "/pages/mgmIngredient.php?id=" + data.success.id;
				msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success.msg + '</div>';
			}else if(data.error){
				msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
			}
			$('#rename_msg').html(msg);
		},
		error: function (xhr, status, error) {
			$('#rename_msg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
});


if (typeof myCAS !== 'undefined' && myPCH == '1') {
	function fetch_pubChem(){
		$.ajax({ 
			url: '/pages/views/ingredients/pubChem.php', 
			type: 'GET',
			data: {
				cas: myCAS
			},
			dataType: 'html',
			success: function (data) {
				$('#pubChemData').html(data);
			},
			error: function (xhr, status, error) {
				$('#pubChemData').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
			}
		});
	}
};

if (typeof myIngID !== 'undefined') {
	function reload_overview() {
		$('#ingOverview').html('<img src="/img/loading.gif"/>');
	
		$.ajax({ 
			url: '/pages/views/ingredients/ingOverview.php', 
			type: 'GET',
			data: {
				id: myIngID
			},
			dataType: 'html',
			success: function (data) {
				$('#ingOverview').html(data);
			},
			error: function (xhr, status, error) {
				$('#ingOverview').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
			}
		});
	};
	reload_overview();
};

function fetch_qrc(){
	$.ajax({ 
		url: '/pages/views/generic/qrcode.php', 
		type: 'GET',
		data: {
			id: myIngID,
			type: "ingredient"
		},
		dataType: 'html',
		success: function (data) {
			$('#QRC').html(data);
		},
		error: function (xhr, status, error) {
			$('#QRC').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	});
};
fetch_qrc();

/*
Ingredient management
*/

function fetch_generalData(){
	$.ajax({ 
		url: '/pages/views/ingredients/mgmGeneral.php', 
		type: 'GET',
		data: {
			id: btoa(myIngName),
			newIngName: newIngName,
			newIngCAS: newIngCAS
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_generalData').html(data);
		},
	});
};

function fetch_whereUsed(){
	$.ajax({ 
		url: '/pages/views/ingredients/whereUsed.php', 
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
		url: '/pages/views/ingredients/usageData.php', 
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
		url: '/pages/views/ingredients/ingSuppliers.php', 
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
		url: '/pages/views/ingredients/techData.php', 
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
		url: '/pages/views/ingredients/ingDocuments.php', 
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
		url: '/pages/views/ingredients/impactData.php', 
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
	});
}

function fetch_safety(){
	$.ajax({ 
		url: '/pages/views/ingredients/safetyData.php', 
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
		url: '/pages/views/ingredients/privacyData.php', 
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
		url: '/pages/views/ingredients/synonyms.php', 
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
		url: '/pages/views/ingredients/repData.php', 
		type: 'POST',
		data: {
			id: btoa(myIngName),
			cas: myCAS,
			ingID: myIngID
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
		url: '/pages/update_data.php', 
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
				msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
			}else if(data.error){
				msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
			}
			$('#clone_msg').html(msg);
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
			id: myIngID,
			tmpl: $("#template").val(),
			ingCustomer: $("#ingCustomer").val()
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
		url: '/pages/update_data.php', 
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
				reload_ingredients_data();
				msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success.msg + '</div>';
			}else if(data.error){
				msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#rename_msg').html(msg);
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
	});
};
fetch_qrc();
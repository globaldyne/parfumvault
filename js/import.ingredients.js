/*
IMPORT INGREDIENTS JSON
*/

function uploadProgressHandler(event) {
	$("#loaded_n_total").html("Uploaded " + event.loaded + " bytes of " + event.total);
	var percent = (event.loaded / event.total) * 100;
	var progress = Math.round(percent);
	$("#uploadProgressBar").html(progress + " %");
	$("#uploadProgressBar").css("width", progress + "%");	
}

function loadHandler(event) {
	$("#status").html(event.target.responseText);
	$(".progress").hide();
	//$("#btnRestoreIngredients").hide();
	$("#backupFile").val('');
	$('#btnCloseBK').prop('value', 'Close');
	$("#uploadProgressBar").css("width", "0%");
}

function errorHandler(event) {
	$("#JSRestMsg").html('<div class="alert alert-danger">Upload failed</div>');
}

function abortHandler(event) {
	$("#JSRestMsg").html('<div class="alert alert-info">Upload Aborted</div>');
}
	
$(".progress").hide();
$("#btnRestoreIngredients").prop("disabled", true);
$("#backupFile").change(function(){
	var allowedTypes = ['application/json'];
	var file = this.files[0];
	var fileType = file.type;
	var fileSize = file.size;
	$("#JSRestMsg").html('');
	var fileSizePHP = $("#raw").data("size");
	if(!allowedTypes.includes(fileType)){
		$("#JSRestMsg").html('<div class="alert alert-info">Invalid file selected. Please select a JSON file exported from PV.</div>');
		$("#backupFile").val('');
		$("#btnRestoreIngredients").prop("disabled", true);
		return false;
	}
	
	if (fileSize > fileSizePHP){
		$("#JSRestMsg").html('<div class="alert alert-info">File size <strong>('+formatBytes(fileSize)+')</strong> is exceeding your server file upload limit '+ formatBytes(fileSizePHP)+'</div>');
		$("#backupFile").val('');
		$("#btnRestoreIngredients").prop("disabled", true);
		return false;
	}
	
	$("#btnRestoreIngredients").prop("disabled", false);
	$('#btnRestoreIngredients').prop('value', 'Import');
});

//RESTORE Ingredients
$('#btnRestoreIngredients').click(function() {
	
	event.preventDefault();
	var fd = new FormData();
    var files = $('#backupFile')[0].files;

    if(files.length > 0 ){
		fd.append('backupFile',files[0]);
	}
	
	$.ajax({ 
		url: '/pages/operations.php?action=restoreIngredients', 
		type: 'POST',
		data: fd,
		contentType: false,
      	processData: false,
		cache: false,
		dataType: 'json',
		xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress",
                    uploadProgressHandler,
                    false
                );
                xhr.addEventListener("load", loadHandler, false);
                xhr.addEventListener("error", errorHandler, false);
                xhr.addEventListener("abort", abortHandler, false);
				$(".progress").show();
				$("#btnRestoreIngredients").prop("disabled", true);
				$('#btnRestoreIngredients').prop('value', 'Please wait...');
                return xhr;
            },
			
			success: function (data) {
				let msg = '';
			
				if (data.success) {
					msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
					$("#btnRestoreIngredients").hide();
					$("#backupArea").css('display', 'none');
				}
				
				if (data.warning) {
					msg = '<div class="alert alert-warning"><i class="fa-solid fa-exclamation-circle mx-2"></i><strong>Import complete with warnings</strong> <br/>' + data.warning + '</div>';
				}
				
				if (data.error) {
					msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
					$("#btnRestoreIngredients").show();
					$("#btnRestoreIngredients").prop("disabled", false);
					$('#btnRestoreIngredients').prop('value', 'Import');
				}
			
				$('#btnRestoreIngredients').prop('value', 'Import');
				$("#btnRestoreIngredients").prop("disabled", false);
				$('#JSRestMsg').html(msg);
			},
			error: function (xhr, status, error) {
				msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>';
				$('#JSRestMsg').html(msg);
			}

		
	  });
});

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

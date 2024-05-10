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
	//$("#btnImportCompounds").hide();
	$("#jsonFile").val('');
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
$("#btnImportCompounds").prop("disabled", true);
$("#jsonFile").change(function(){
	var allowedTypes = ['application/json'];
	var file = this.files[0];
	var fileType = file.type;
	var fileSize = file.size;
	$("#JSRestMsg").html('');
	var fileSizePHP = $("#raw").data("size");
	if(!allowedTypes.includes(fileType)){
		$("#JSRestMsg").html('<div class="alert alert-info">Invalid file selected. Please select a JSON file exported from PV.</div>');
		$("#jsonFile").val('');
		$("#btnImportCompounds").prop("disabled", true);
		return false;
	}
	
	if (fileSize > fileSizePHP){
		$("#JSRestMsg").html('<div class="alert alert-info">File size <strong>('+formatBytes(fileSize)+')</strong> is exceeding your server file upload limit '+ formatBytes(fileSizePHP)+'</div>');
		$("#jsonFile").val('');
		$("#btnImportCompounds").prop("disabled", true);
		return false;
	}
	
	$("#btnImportCompounds").prop("disabled", false);
	$('#btnImportCompounds').prop('value', 'Import');
});

//RESTORE Ingredients
$('#btnImportCompounds').click(function() {
	
	event.preventDefault();
	var fd = new FormData();
    var files = $('#jsonFile')[0].files;

    if(files.length > 0 ){
		fd.append('jsonFile',files[0]);
	}
	
	$.ajax({ 
		url: '/pages/operations.php?action=importCompounds', 
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
				$("#btnImportCompounds").prop("disabled", true);
				$('#btnImportCompounds').prop('value', 'Please wait...');
                return xhr;
            },
			
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>'+data.success+'</div>';
				$("#btnImportCompounds").hide();
				$("#backupArea").css('display', 'none');

			}else if(data.error){
				var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2">'+data.error+'</div>';
				$("#btnImportCompounds").show();
				$("#btnImportCompounds").prop("disabled", false);
				$('#btnImportCompounds').prop('value', 'Import');
			}
			$('#btnImportCompounds').prop('value', 'Import');
			$("#btnImportCompounds").prop("disabled", false);
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

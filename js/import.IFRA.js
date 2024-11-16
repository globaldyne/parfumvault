/*
IMPORT IFRA JSON
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
	//$("#btnRestoreIFRA").hide();
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
$("#btnRestoreIFRA").prop("disabled", true);
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
		$("#btnRestoreIFRA").prop("disabled", true);
		return false;
	}
	
	if (fileSize > fileSizePHP){
		$("#JSRestMsg").html('<div class="alert alert-info">File size <strong>('+formatBytes(fileSize)+')</strong> is exceeding your server file upload limit '+ formatBytes(fileSizePHP)+'</div>');
		$("#backupFile").val('');
		$("#btnRestoreIFRA").prop("disabled", true);
		return false;
	}
	
	$("#btnRestoreIFRA").prop("disabled", false);
	$('#btnRestoreIFRA').prop('value', 'Import');
});

//RESTORE
$('#btnRestoreIFRA').click(function() {
	
	event.preventDefault();
	var fd = new FormData();
    var files = $('#backupFile')[0].files;

    if(files.length > 0 ){
		fd.append('backupFile',files[0]);
	}
	
	$.ajax({ 
		url: '/core/core.php?action=restoreIFRA', 
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
				$("#btnRestoreIFRA").prop("disabled", true);
				$('#btnRestoreIFRA').prop('value', 'Please wait...');
                return xhr;
            },
			
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success">'+data.success+'</div>';
				$("#btnRestoreIFRA").hide();
				$("#backupArea").css('display', 'none');
				$('#tdDataIFRA').DataTable().ajax.reload(null, true);
			}else if(data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
				$("#btnRestoreIFRA").show();
				$("#btnRestoreIFRA").prop("disabled", false);
				$('#btnRestoreIFRA').prop('value', 'Import');
			}
			$('#btnRestoreIFRA').prop('value', 'Import');
			$("#btnRestoreIFRA").prop("disabled", false);
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

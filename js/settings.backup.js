/*
Settings backup restore js
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
	//$("#btnRestore").hide();
	$("#backupFile").val('');
	$('#btnCloseBK').prop('value', 'Close');
	$("#uploadProgressBar").css("width", "0%");
}

function errorHandler(event) {
	$("#DBRestMsg").html('<div class="alert alert-danger">Upload failed</div>');
}

function abortHandler(event) {
	$("#DBRestMsg").html('<div class="alert alert-info">Upload Aborted</div>');
}
	
$(".progress").hide();
$("#btnRestore").prop("disabled", true);
$("#backupFile").change(function(){
	var allowedTypes = ['application/x-gzip'];
	var file = this.files[0];
	var fileType = file.type;
	var fileSize = file.size;
	$("#DBRestMsg").html('');
	var fileSizePHP = $("#raw").data("size");
	if(!allowedTypes.includes(fileType)){
		$("#DBRestMsg").html('<div class="alert alert-info">Invalid backup file selected. Please select a file exported from PV backup.</div>');
		$("#backupFile").val('');
		$("#btnRestore").prop("disabled", true);
		return false;
	}
	
	if (fileSize > fileSizePHP){
		$("#DBRestMsg").html('<div class="alert alert-info">File size <strong>('+formatBytes(fileSize)+')</strong> is exceeding your server file upload limit '+ formatBytes(fileSizePHP)+'</div>');
		$("#backupFile").val('');
		$("#btnRestore").prop("disabled", true);
		return false;
	}
	
	$("#btnRestore").prop("disabled", false);
	$('#btnRestore').prop('value', 'Restore');
});

//RESTORE DB
$('#btnRestore').click(function() {
	
	event.preventDefault();
	var fd = new FormData();
    var files = $('#backupFile')[0].files;

    if(files.length > 0 ){
		fd.append('backupFile',files[0]);
	}
	
	$.ajax({ 
		url: '/core/core.php?restore=db_bk', 
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
				$("#btnRestore").prop("disabled", true);
				$('#btnRestore').prop('value', 'Please wait...');
				$("#bk_res_info").prop('disabled',true);
                return xhr;
            },
			
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success">'+data.success+'</div>';
				$("#btnRestore").hide();
				$("#backupArea").css('display', 'none');

			}else if(data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
				$("#btnRestore").show();
				$("#btnRestore").prop("disabled", false);
				$('#btnRestore').prop('value', 'Restore');
			}
			$('#btnRestore').prop('value', 'Restore');
			$("#btnRestore").prop("disabled", false);
			$("#bk_res_info").prop('disabled',false);
			$('#DBRestMsg').html(msg);
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
};

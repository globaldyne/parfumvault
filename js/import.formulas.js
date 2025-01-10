/*
IMPORT FORMULAS JSON
*/

//RESTORE
$('#addtxtFormula').click(function(event) {
	event.preventDefault(); // Prevent default form submission

	// Disable form elements
	$('#txtImpName, #txtImpFormula, #addtxtFormula').prop('disabled', true);
	$('#close_imp_txt').prop('disabled', true);

	$.ajax({
		url: '/core/core.php',
		type: 'POST',
		data: {
			action: 'importTXTFormula',
			formulaName: $('#txtImpName').val(),
			formulaData: $('#txtImpFormula').val(),
		},
		dataType: 'json',
		success: function(data) {
			if(data.success){
				$('#txtImpMsg').html('<div class="alert alert-success"><i class="fa-regular fa-circle-check mx-2"></i>' + data.success + '</div>');
				$('#all-table').DataTable().ajax.reload(null, true);
			} else {
				$('#txtImpMsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error + '</div>');
			}
		},
		error: function(xhr, status, error) {
			// Handle error
			$('#txtImpMsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Error: ' + error + '</div>');
		},
		complete: function() {
			// Re-enable form elements
			$('#txtImpName, #txtImpFormula, #addtxtFormula, #close_imp_txt').prop('disabled', false);
		}
	});
});


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
	//$("#btnRestoreFormulas").hide();
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
$("#btnRestoreFormulas").prop("disabled", true);
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
		$("#btnRestoreFormulas").prop("disabled", true);
		return false;
	}
	
	if (fileSize > fileSizePHP){
		$("#JSRestMsg").html('<div class="alert alert-info">File size <strong>('+formatBytes(fileSize)+')</strong> is exceeding your server file upload limit '+ formatBytes(fileSizePHP)+'</div>');
		$("#backupFile").val('');
		$("#btnRestoreFormulas").prop("disabled", true);
		return false;
	}
	
	$("#btnRestoreFormulas").prop("disabled", false);
	$('#btnRestoreFormulas').prop('value', 'Import');
});

//RESTORE
$('#btnRestoreFormulas').click(function() {
	
	event.preventDefault();
	var fd = new FormData();
    var files = $('#backupFile')[0].files;

    if(files.length > 0 ){
		fd.append('backupFile',files[0]);
	}
	
	$.ajax({ 
		url: '/core/core.php?action=restoreFormulas', 
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
				$("#btnRestoreFormulas").prop("disabled", true);
				$('#btnRestoreFormulas').prop('value', 'Please wait...');
                return xhr;
            },
			
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success">'+data.success+'</div>';
				$("#btnRestoreFormulas").hide();
				$("#backupArea").css('display', 'none');

			}else if(data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
				$("#btnRestoreFormulas").show();
				$("#btnRestoreFormulas").prop("disabled", false);
				$('#btnRestoreFormulas').prop('value', 'Import');
			}
			$('#btnRestoreFormulas').prop('value', 'Import');
			$("#btnRestoreFormulas").prop("disabled", false);
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
};
$('.table').on('show.bs.dropdown', function () {
	 $('.table-responsive').css( "overflow", "inherit" );
});

$('.table').on('hide.bs.dropdown', function () {
	 $('.table-responsive').css( "overflow", "auto" );
});

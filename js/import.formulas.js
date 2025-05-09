/*
IMPORT FORMULAS JSON
*/

// RESTORE TXT FORMULA
$('#addtxtFormula').click(function(event) {
    event.preventDefault(); // Prevent default form submission

    // Disable form elements
    $('#txtImpName, #txtImpFormula, #addtxtFormula, #close_imp_txt').prop('disabled', true);

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
            if (data.success) {
                $('#txtImpMsg').html('<div class="alert alert-success"><i class="fa-regular fa-circle-check mx-2"></i>' + data.success + '</div>');
                $('#all-table').DataTable().ajax.reload(null, true);
            } else {
                $('#txtImpMsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error + '</div>');
            }
        },
        error: function(xhr, status, error) {
            $('#txtImpMsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Error: ' + error + '</div>');
        },
        complete: function() {
            // Re-enable form elements
            $('#txtImpName, #txtImpFormula, #addtxtFormula, #close_imp_txt').prop('disabled', false);
        }
    });
});

// Upload progress handler
function uploadProgressHandler(event) {
    const percent = (event.loaded / event.total) * 100;
    const progress = Math.round(percent);
    $("#uploadProgressBar").html(progress + " %").css("width", progress + "%");
}

// Load handler
function loadHandler(event) {
    $("#status").html(event.target.responseText);
    $(".progress").hide();
    $("#backupFile").val('');
    $('#btnCloseBK').prop('value', 'Close');
    $("#uploadProgressBar").css("width", "0%");
}

// Error handler
function errorHandler(event) {
    $("#JSRestMsg").html('<div class="alert alert-danger">Upload failed</div>');
    $(".progress").hide(); // Hide progress bar on error
}

// Abort handler
function abortHandler(event) {
    $("#JSRestMsg").html('<div class="alert alert-info">Upload Aborted</div>');
    $(".progress").hide(); // Hide progress bar on abort
}

// File input change handler
$("#backupFile").change(function() {
    const allowedTypes = ['application/json'];
    const file = this.files[0];
    const fileType = file.type;
    const fileSize = file.size;
    const fileSizePHP = $("#raw").data("size");

    $("#JSRestMsg").html('');

    if (!allowedTypes.includes(fileType)) {
        $("#JSRestMsg").html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Invalid file selected. Please select a JSON file exported from Perfumers Vault</div>');
        $("#backupFile").val('');
        $("#btnRestoreFormulas").prop("disabled", true);
        return false;
    }

    if (fileSize > fileSizePHP) {
        $("#JSRestMsg").html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>File size <strong>(' + formatBytes(fileSize) + ')</strong> is exceeding your server file upload limit ' + formatBytes(fileSizePHP) + '</div>');
        $("#backupFile").val('');
        $("#btnRestoreFormulas").prop("disabled", true);
        return false;
    }

    $("#btnRestoreFormulas").prop("disabled", false);
    $('#btnRestoreFormulas').prop('value', 'Import');
});

// RESTORE FORMULAS
$('#btnRestoreFormulas').click(function(event) {
    event.preventDefault();

    const fd = new FormData();
    const files = $('#backupFile')[0].files;

    if (files.length > 0) {
        fd.append('backupFile', files[0]);
    }

    $.ajax({
        url: '/core/core.php?action=restoreFormulas',
        type: 'POST',
        data: fd,
        contentType: false,
        processData: false,
        cache: false,
        dataType: 'json',
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", uploadProgressHandler, false);
            xhr.addEventListener("load", loadHandler, false);
            xhr.addEventListener("error", errorHandler, false);
            xhr.addEventListener("abort", abortHandler, false);
            $(".progress").show();
            $("#btnRestoreFormulas").prop("disabled", true).prop('value', 'Please wait...');
            return xhr;
        },
        success: function(data) {
            let msg;
            if (data.success) {
                msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
                $("#btnRestoreFormulas").hide();
                $("#backupArea").css('display', 'none');
                $('#all-table').DataTable().ajax.reload(null, true);
            } else if (data.error) {
                msg = '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error + '</div>';
                $("#btnRestoreFormulas").show().prop("disabled", false).prop('value', 'Import');
            }
            $('#JSRestMsg').html(msg);
            $(".progress").hide(); // Hide progress bar after completion
        }
    });
});

// Format bytes to human-readable format
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

// Handle table dropdown overflow
$('.table').on('show.bs.dropdown', function() {
    $('.table-responsive').css("overflow", "inherit");
});

$('.table').on('hide.bs.dropdown', function() {
    $('.table-responsive').css("overflow", "auto");
});


//Import CSV
$("#btnImportCSV").prop("disabled", true).hide();

$("input[type=file]").on('change', function() {
    const fd = new FormData();
    const files = $('#CSVFile')[0].files;
    const formulaNameInput = $('#CSVname');
    let formulaName = formulaNameInput.val();

    if (!formulaName) {
        const fileInput = $('input[type=file]').val();
        if (fileInput) {
            const filename = fileInput.split(/(\\|\/)/g).pop().replace(/.csv$/i, '');
            formulaNameInput.val(filename);
        } else {
            $('#CSVImportMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill mx-2"></i>No file selected. Please select a file.</div>');
            return;
        }
    }

    if (files.length > 0) {
        fd.append('CSVFile', files[0]);
        $.ajax({
            url: '/pages/upload.php?type=frmCSVImport&step=upload',
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            cache: false,
            success: function(response) {
                if (response !== 'error') {
                    $("#CSVImportMsg").html('');
                    $("#step_upload").html(response);
                    $("#btnImportCSV").show();
                } else {
                    $("#CSVImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="bi bi-exclamation-circle-fill mx-2"></i>Error uploading file</div>');
                }
            },
            error: function() {
                $("#CSVImportMsg").html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill mx-2"></i>An error occurred while uploading the file.</div>');
            }
        });
    } else {
        $("#CSVImportMsg").html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill mx-2"></i>Please select a file to upload</div>');
    }
});

let columnData = {};
let totalSelection = 0;

$(document).on('change', '.set_column_data', function() {
    const columnName = $(this).val();
    const columnNumber = $(this).data('column_number');

    if (columnData[columnName]) {
        $('#CSVImportMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill mx-2"></i><strong>' + columnName + '</strong> is already assigned.</div>');
        $(this).val('');
        return;
    }

    $('#CSVImportMsg').html('');

    if (columnName) {
        columnData[columnName] = columnNumber;
    } else {
        for (const [key, value] of Object.entries(columnData)) {
            if (value === columnNumber) {
                delete columnData[key];
            }
        }
    }

    totalSelection = Object.keys(columnData).length;

    if (totalSelection === 4) {
        $('#btnImportCSV').prop("disabled", false);
    } else {
        $('#btnImportCSV').prop("disabled", true);
    }
});

$(document).on('click', '#btnImportCSV', function(event) {
    event.preventDefault();

    const formulaName = $('#CSVname').val();
    const formulaProfile = $('#CSVProfile').val();

    $.ajax({
        url: "/pages/upload.php?type=frmCSVImport&step=import",
        method: "POST",
        data: {
            formula_name: formulaName,
            formula_profile: formulaProfile,
            ...columnData
        },
        dataType: "json",
        beforeSend: function() {
            $('#btnImportCSV').prop("disabled", true);
        },
        success: function(data) {
            if (data.error) {
                $('#CSVImportMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill mx-2"></i><strong>' + data.error + '</strong></div>');
                $('#btnImportCSV').prop("disabled", false);
            } else {
                $('#CSVImportMsg').html('<div class="alert alert-success"><i class="bi bi-check-circle-fill mx-2"></i><strong>' + data.success + '</strong></div>');
                $('#btnImportCSV').hide();
                $('#btnCloseCsv').prop('value', 'Close');
                $('#process_area').hide();
                $('#all-table').DataTable().ajax.reload(null, true);
            }
        },
        error: function() {
            $('#CSVImportMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill mx-2"></i>An error occurred during the import process.</div>');
            $('#btnImportCSV').prop("disabled", false);
        }
    });
});

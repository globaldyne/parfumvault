/*
IMPORT INGREDIENTS JSON
*/

function resetModalElements() {
    $("#loaded_n_total, #status, #JSRestMsg").html("");
    $("#uploadProgressBar").html("0 %").css("width", "0%");
    $("#backupFile").val('');
    $("#btnRestoreIngredients, #btnRestoreIngredientsCloseBK").prop("disabled", true);
    $(".progress").hide();
    $("#btnRestoreIngredients").show();
    $("#backupArea").show();
}

$('#import_ingredients_json').on('show.bs.modal', resetModalElements);

function updateProgress(event) {
    const percent = Math.round((event.loaded / event.total) * 100);
    $("#loaded_n_total").html(`Uploaded ${event.loaded} bytes of ${event.total}`);
    $("#uploadProgressBar").html(`${percent} %`).css("width", `${percent}%`);
}

function uploadComplete(event) {
    $("#status").html(event.target.responseText);
    $(".progress").hide();
    $("#backupFile").val('');
    $('#btnCloseBK').prop('value', 'Close');
    $("#uploadProgressBar").css("width", "0%");
}

function showError(message) {
    $("#JSRestMsg").html(`<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>${message}</div>`);
}

function showInfo(message) {
    $("#JSRestMsg").html(`<div class="alert alert-info"><i class="fa-solid fa-circle-exclamation mx-2"></i>${message}</div>`);
}

function handleFileValidation(file) {
    const allowedTypes = ['application/json'];
    const fileSizePHP = $("#raw").data("size");
    $("#JSRestMsg").html('');
    
    if (!allowedTypes.includes(file.type)) {
        showError("Invalid file selected. Please select a JSON file exported from PV.");
        return false;
    }
    
    if (file.size > fileSizePHP) {
        showError(`File size <strong>(${formatBytes(file.size)})</strong> exceeds the server limit ${formatBytes(fileSizePHP)}`);
        return false;
    }
    
    return true;
}

$("#backupFile").change(function () {
    const file = this.files[0];
    if (file && handleFileValidation(file)) {
        $("#btnRestoreIngredients").prop("disabled", false).prop('value', 'Import');
    } else {
        $("#backupFile").val('');
        $("#btnRestoreIngredients").prop("disabled", true);
    }
});

$('#btnRestoreIngredients').click(function (event) {
    event.preventDefault();
    const files = $('#backupFile')[0].files;
    if (files.length === 0) return;
    
    const fd = new FormData();
    fd.append('backupFile', files[0]);
    
    $.ajax({
        url: '/core/core.php?action=restoreIngredients',
        type: 'POST',
        data: fd,
        contentType: false,
        processData: false,
        cache: false,
        dataType: 'json',
        xhr: function () {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", updateProgress, false);
            xhr.addEventListener("load", uploadComplete, false);
            xhr.addEventListener("error", () => showError("Upload failed"), false);
            xhr.addEventListener("abort", () => showInfo("Upload Aborted"), false);
            $(".progress").show();
            $("#btnRestoreIngredients, #btnRestoreIngredientsCloseBK").prop("disabled", true).prop('value', 'Please wait...');
            return xhr;
        },
        success: function (data) {
            let msg = '';
            if (data.success) {
                msg = `<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>${data.success}</div>`;
                $("#btnRestoreIngredients").hide();
                $("#backupArea").hide();
                $('#tdDataIng').DataTable().ajax.reload(null, false);
                $("#btnRestoreIngredientsCloseBK").prop("disabled", false);
            } else if (data.warning) {
                msg = `<div class="alert alert-warning"><i class="fa-solid fa-exclamation-circle mx-2"></i><strong>Import complete with warnings</strong> <br/>${data.warning}</div>`;
            } else if (data.error) {
                msg = `<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>${data.error}</div>`;
                $("#btnRestoreIngredients").show().prop("disabled", false).prop('value', 'Import');
                $("#btnRestoreIngredientsCloseBK").prop("disabled", false);
            }
            $('#JSRestMsg').html(msg);
        },
        error: function (xhr, status, error) {
            showError(`An ${status} occurred, check server logs for more info. ${error}`);
            $("#btnRestoreIngredients, #btnRestoreIngredientsCloseBK").prop("disabled", false).prop('value', 'Import');
        }
    });
});

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024, dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
}

//INGREDIENTS CSV IMPORT
$("#btnImportCSV").prop("disabled", true);
$("input[type=file]").on('change', function() {	
    $("#CSVImportMsg").html('<div class="alert alert-info">Please wait, file upload in progress....</div>');
    const fd = new FormData();
    const files = $('#CSVFile')[0].files;
    
    if (files.length > 0) {
        fd.append('CSVFile', files[0]);

        $.ajax({
            url: '/pages/upload.php?type=ingCSVImport&step=upload',
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response != 0) {
                    $("#CSVImportMsg").html('');
                    $("#step_upload").html(response);

                    // Auto-assign columns based on server-provided defaults
                    $('.set_column_data').each(function() {
                        const autoAssignValue = $(this).find('option[selected]').val();
                        if (autoAssignValue) {
                            const column_number = $(this).data('column_number');
                            column_data[autoAssignValue] = column_number;
                        }
                    });

                    total_selection = Object.keys(column_data).length;

                    // Enable import button if all columns are assigned
                    if (total_selection === 13) {
                        $('#btnImportCSV').prop("disabled", false).show();
                    } else {
                        $('#btnImportCSV').prop("disabled", true);
                    }
                } else {
                    $("#CSVImportMsg").html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>File upload failed</div>');
                    $("#btnImportCSV").prop("disabled", false);
                }
            },
            error: function() {
                $("#CSVImportMsg").html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred during file upload</div>');
                $("#btnImportCSV").prop("disabled", false);
            }
        });
    } else {
        $("#CSVImportMsg").html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Please select a file to upload</div>');
        $("#btnImportCSV").prop("disabled", false);
    }
});

let total_selection = 0;
let ingredient_name = '';
let iupac = '';
let cas = '';
let fema = '';
let type = '';
let strength = '';
let profile = '';
let physical_state = '';
let allergen = 0;
let odor = '';
let impact_top = 0;
let impact_heart = 0;
let impact_base = 0;

let column_data = {};

$(document).on('change', '.set_column_data', function() {
    const column_name = $(this).val();
    const column_number = $(this).data('column_number');

    if (column_name in column_data) {
        $('#CSVImportMsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + column_name + '</strong> is already assigned.</div>');
        $(this).val('');
        return false;
    } else {
        $('#CSVImportMsg').html('');
    }

    if (column_name !== '') {
        column_data[column_name] = column_number;
    } else {
        for (const [key, value] of Object.entries(column_data)) {
            if (value === column_number) {
                delete column_data[key];
            }
        }
    }

    total_selection = Object.keys(column_data).length;

    if (total_selection === 13) {
        $('#btnImportCSV').prop("disabled", false).show();

        ingredient_name = column_data.ingredient_name;
        iupac = column_data.iupac;
        cas = column_data.cas;
        fema = column_data.fema;
        type = column_data.type;
        strength = column_data.strength;
        profile = column_data.profile;
        physical_state = column_data.physical_state;
        allergen = column_data.allergen;
        odor = column_data.odor;
        impact_top = column_data.impact_top;
        impact_heart = column_data.impact_heart;
        impact_base = column_data.impact_base;
    } else {
        $('#btnImportCSV').prop("disabled", true);
    }
});

$(document).on('click', '#btnImportCSV', function(event) {
    event.preventDefault();

    $.ajax({
        url: "/pages/upload.php?type=ingCSVImport&step=import",
        method: "POST",
        data: {		  
            ingredient_name,
            iupac,  
            cas, 
            fema,
            type,
            strength,  
            profile,  
            physical_state,
            allergen,
            odor,  
            impact_top, 
            impact_heart, 
            impact_base
        },
        beforeSend: function() {
            $('#btnImportCSV').prop("disabled", true);
        },
        success: function(data) {
            if (data.error) {
                $('#btnImportCSV').prop("disabled", false);
                $('#CSVImportMsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error + ' </div>');
            } else {
                $('#btnImportCSV').prop("disabled", false).hide();
                $('#btnCloseCsv').prop('value', 'Close');
                $('#process_area').css('display', 'none');
                $('#tdDataIng').DataTable().ajax.reload(null, false);
                resetCSVImportUI();
                $('#CSVImportMsg').html('<div class="alert alert-success"><i class="bi bi-check-circle mx-2"></i>Import complete</div>');
            }
        },
        error: function() {
            $('#CSVImportMsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred during the import process</div>');
            $('#btnImportCSV').prop("disabled", false);
        }
    });
});


function resetCSVImportUI() {
  // Clear file input
  $("#CSVFile").val('');
  // Clear messages
  $("#CSVImportMsg").html('');
  // Clear table
  $("#step_upload").html('');
  // Reset column tracking
  column_data = {};
  // Enable import button
  $('#btnImportCSV').prop("disabled", true).show();
  // Reset close button if needed
  $('#btnCloseCsv').val('Close');
  // Re-show import processing area
  $('#process_area').show();
}

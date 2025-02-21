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

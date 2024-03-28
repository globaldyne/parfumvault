<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
require_once(__ROOT__.'/func/php-settings.php');

$ver = trim(file_get_contents(__ROOT__.'/VERSION.md'));

?>
<div class="col mt-2">
    <div class="row mb-2">
      <div class="col">
        <li><a href="#" data-bs-toggle="modal" data-bs-target="#backup_db">Backup DB</a></li>
      </div>
    </div>
    
    <div class="row mb-2">
        <div class="col">
            <li><a href="#" data-bs-toggle="modal" data-bs-target="#restore_db">Restore DB</a></li>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col">
            <li><a href="#" data-bs-toggle="modal" data-bs-target="#clear_user_pref">Clear user preferences</a></li>
        </div>
    </div>
</div>

<div class="modal fade" id="clear_user_pref" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="clear_user_pref" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Clear user preferences</h5>
      </div>
      <div class="modal-body">
        <div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>
        	You can reset any user modifications like table sorting.
            This will bring Perfumers Vault instalation to its defaults.
            Your data will not be affected.
        </div>
      </div>
	  <div class="modal-footer">
        <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK" value="Cancel">
        <button name="btnClear" class="btn btn-warning" id="btnClear">Clear data</button>
      </div>
  </div>
 </div>
</div>

<div class="modal fade" id="backup_db" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="backup_db" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Backup database</h5>
      </div>
      <div class="modal-body">
          <div id="DBMsg"></div>
          <div id="backupArea">
              <div class="form-group">
                <div class="mx-4">
    				<input type="checkbox" class="form-check-input" id="column-statistics" checked>
   					<label class="form-check-label" for="column-statistics">Column Statistics<i class="fa-solid fa-circle-info mx-2" data-bs-toggle="tooltip" data-bs-placement="right" title="Add ANALYZE TABLE statements to the output to generate histogram statistics for dumped tables when the dump file is reloaded. Disable this if your back-up fails or takes too long."></i></label>
  				</div>
              </div>
              <div class="col-md-12">
                 <div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>You can pass more parameteres to the mysqldump by adding them to the DB_BACKUP_PARAMETERS environment variable.</div>
              </div>
          </div>
      </div>
	  <div class="modal-footer">
        <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK" value="Cancel">
        <button name="btnBackup" class="btn btn-primary" id="btnBackup">Backup</button>
      </div>
  </div>
 </div>
</div>


<div class="modal fade" id="restore_db" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="restore_db" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Restore database from a backup file</h5>
      </div>
      <div class="modal-body">
      <div id="DBRestMsg"></div>
      <div class="progress">  
         <div id="uploadProgressBar" class="progress-bar" role="progressbar" aria-valuemin="0"></div>
      </div>
      <div id="backupArea">
          <div class="form-group">
              <label class="col-md-3 control-label">Backup file:</label>
              <div class="col-md-8">
                 <input type="file" name="backupFile" id="backupFile" class="form-control" />
              </div>
          </div>
          <div class="col-md-12" id="bk_res_info">
             <hr />
             <p><strong>IMPORTANT:</strong></p>
              <ul>
                <li><div id="raw" data-size="<?=getMaximumFileUploadSizeRaw()?>">Maximum file size: <strong><?=getMaximumFileUploadSize()?></strong></div></li>
                <li>Backup file must match your current PV version<strong> (<?=$ver?>)</strong>, if not downgrade or upgrade accordingly before restoring a backup</li>
                <li>You current database will be wiped-out so if it contains any data you wanna keep, please take a <a href="#" data-bs-toggle="modal" data-bs-target="#backup_db" id="bk_modal_open" class="text-primary">backup</a> first</li>
              </ul>
    <p>&nbsp;</p>
            </div>
          </div>
      </div>
	  <div class="modal-footer">
        <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK" value="Cancel">
        <input type="submit" name="btnRestore" class="btn btn-primary" id="btnRestore" value="Restore">
      </div>
  </div>
 </div>
</div>
<script>
$('#btnClear').click(function() {
	$("#btnClear").prop("disabled", true);			

	$.ajax({
		url: '/pages/operations.php',
		data: {
			do: 'userPerfClear',
		},
		cache: false,
		
		success: function (data) {
			$("#btnClear").prop("disabled", false);	
			$('#clear_user_pref').modal('hide');
		},
		error: function (request, status, error) {
			$("#btnClear").prop("disabled", false);
		},
			
	});

});

$(function () {
  $('[data-bs-toggle="tooltip"]').tooltip();
});

$('#bk_modal_open').click(function() {
	$('#restore_db').modal('hide');
});

$('#btnBackup').click(function() {
	$('#DBMsg').html('');
	$("#btnBackup").prop("disabled", true);
	$("#btnCloseBK").prop("disabled", true);
 	$('#btnBackup').prepend('<span class="spinner-border spinner-border-sm mx-2" id="bk_span" aria-hidden="true"></span>');
	$.ajax({
		url: '/pages/operations.php',
		data: {
			do: 'backupDB',
			column_statistics: $('#column-statistics').prop("checked")
		},
		cache: false,
		xhr: function () {
			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function () {
				if (xhr.readyState == 2) {
					if (xhr.status == 200) {
						xhr.responseType = "blob";
					} else {
						xhr.responseType = "text";
					}
				}
			};
			return xhr;
		},
		success: function (data) {
			$('span[id^="bk_span"]').remove();
			$("#btnBackup").prop("disabled", false);
			$("#btnCloseBK").prop("disabled", false);
			var blob = new Blob([data], { type: "application/octetstream" });
			
			var url = window.URL || window.webkitURL;
			link = url.createObjectURL(blob);
			var a = $("<a />");
			a.attr("download", 'backup_<?=$ver?>_<?=date("d-m-Y")?>.sql.gz');
			a.attr("href", link);
			$("body").append(a);
			a[0].click();
			$("body").remove(a);
		},
		error: function (request, status, error) {
			$('#DBMsg').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mr2"></i>Unable to handle request, server returned an error: '+request.status+'</div>');
			$('span[id^="bk_span"]').remove();
			$("#btnBackup").prop("disabled", false);
			$("#btnCloseBK").prop("disabled", false);
		},
			
	});
});
</script>
<script src="/js/settings.backup.js"></script>

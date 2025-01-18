<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/php-settings.php');
?>
<div class="container-fluid">

  <h2 class="m-0 mb-4">Settings</h2>
  <div id="settings">
    <ul>
        <li class="active"><a href="#general" id="general_tab" role="tab" data-bs-toggle="tab">My preferences</a></li>
        <?php if($role === 1){?>
          <li class="active"><a href="#systemSettings" id="systemSettings_tab" role="tab" data-bs-toggle="tab">System settings</a></li>
          <li class="active"><a href="#users" id="users_tab" role="tab" data-bs-toggle="tab">Users</a></li>
        <?php }?>
        <li><a href="#categories" id="cat_tab" role="tab" data-bs-toggle="tab">Ingredient Categories</a></li>
        <li><a href="#frmCat" id="frmCat_tab" role="tab" data-bs-toggle="tab">Formula Categories</a></li>
        <li><a href="#perfumeTypes" id="perfume_types_tab" role="tab" data-bs-toggle="tab">Perfume Types</a></li>
        <li><a href="#templates" id="templates_tab" role="tab" data-bs-toggle="tab">HTML Templates</a></li>
        <li><a href="#sds" id="sds_tab" role="tab" data-bs-toggle="tab">SDS Settings</a></li>
        <li><a href="#brand" id="brand_tab" role="tab" data-bs-toggle="tab">My Brand</span></a></li>
        <li><a href="#maintenance" id="maintenance_tab">Maintenance</a></li>
        <li><a href="#integrations" id="integrations_tab">Integrations</a></li>
        <li><a href="#api" id="api_tab" role="tab" data-bs-toggle="tab">API</a></li>
        <li><a href="#syslogs" id="logs_tab" role="tab" data-bs-toggle="tab">System logs</a></li>
        <li><a href="#about" id="about_tab" role="tab" data-bs-toggle="tab">About</a></li>
    </ul>
    
    <div class="tab-content">
      
      <div class="tab-pane active" id="general">
        <div id="get_general">
          <div class="loader-center">
            <div class="loader"></div>
            <div class="loader-text"></div>
          </div>
        </div> 
      </div>

    <?php if($role === 1){?>
      <div class="tab-pane" id="systemSettings">
        <div id="get_systemSettings">
          <div class="loader-center">
            <div class="loader"></div>
            <div class="loader-text"></div>
          </div>
        </div> 
      </div>
      <div class="tab-pane" id="users">
        <div id="get_users">
          <div class="loader-center">
            <div class="loader"></div>
            <div class="loader-text"></div>
          </div>
        </div> 
      </div>
    <?php } ?>
      <div id="categories">
        <div id="catMsg"></div>
          <div id="list_cat">
              <div class="loader-center">
                  <div class="loader"></div>
                  <div class="loader-text"></div>
              </div>
          </div>
      </div> 
          
      <div id="frmCat">
        <div id="fcatMsg"></div>
          <div id="list_fcat">
              <div class="loader-center">
                  <div class="loader"></div>
                  <div class="loader-text"></div>
              </div>
          </div>
      </div> 
        
      <div id="perfumeTypes">
        <div id="ptMsg"></div>
          <div id="list_ptypes">
              <div class="loader-center">
                  <div class="loader"></div>
                  <div class="loader-text"></div>
              </div>
          </div>
      </div>
          
      <div id="templates">
        <div id="tmplMsg"></div>
          <div id="list_templates">
              <div class="loader-center">
                  <div class="loader"></div>
                  <div class="loader-text"></div>
              </div>
          </div>
      </div>
      
      <div id="sds">
          <div id="list_sds_settings">
              <div class="loader-center">
                  <div class="loader"></div>
                  <div class="loader-text"></div>
              </div>
          </div>
      </div>
        
      <div id="brand">
        <div class="loader-center">
            <div class="loader"></div>
            <div class="loader-text"></div>
          </div>
      </div>
        
      <div id="api">
        <div class="loader-center">
          <div class="loader"></div>
          <div class="loader-text"></div>
        </div>
      </div>
        
      <div id="maintenance">
        <div class="loader-center">
          <div class="loader"></div>
          <div class="loader-text"></div>
        </div>
      </div>
        
      <div id="integrations">
        <div class="loader-center">
          <div class="loader"></div>
          <div class="loader-text"></div>
        </div>
      </div>
        
      <div id="syslogs">
        <div class="loader-center">
          <div class="loader"></div>
          <div class="loader-text"></div>
        </div>
      </div>
          
      <div id="about">
        <div class="loader-center">
          <div class="loader"></div>
          <div class="loader-text"></div>
        </div>
      </div>
        
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function() {
	$("#settings").tabs();
});

</script>
<script src="/js/settings.tabs.js"></script>
<!-- IMPORT JSON MODAL -->
<div class="modal fade" id="import_categories_json" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="import_categories_json" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import categories from a JSON file</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="JSRestMsg"></div>
        <div class="progress">  
          <div id="uploadProgressBar" class="progress-bar" role="progressbar" aria-valuemin="0"></div>
        </div>
        <div id="backupArea" class="mt-4">
          <div class="form-group row">
            <label for="jsonFile" class="col-auto col-form-label">JSON file</label>
            <div class="col-md">
              <input type="file" name="jsonFile" id="jsonFile" class="form-control" />
            </div>
          </div>
          <div class="col-md-12 mt-3">
            <hr />
            <p><strong>IMPORTANT</strong></p>
            <ul>
              <li>
                <div id="raw" data-size="<?=getMaximumFileUploadSizeRaw()?>">Maximum file size: <strong><?=getMaximumFileUploadSize()?></strong></div>
              </li>
              <li>Please make sure you have taken a backup before importing a JSON file</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK">Close</button>
        <button type="submit" name="btnRestore" class="btn btn-primary" id="btnRestoreCategories">Import</button>
      </div>
    </div>
  </div>
</div>
<script src="/js/import.categories.js"></script>

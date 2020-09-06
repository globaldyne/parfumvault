<?php 
if (!defined('pvault_panel')){ die('Not Found');}


?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=pvmaker">PV Maker</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
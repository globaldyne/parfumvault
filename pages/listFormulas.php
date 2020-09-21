<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
		<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=listFormulas">Formulas</a></h2>
              <div id="msg2"></div>
            </div>

            <table width="100%" border="0">
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td width="98%"><div class="text-right">
                  <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                    <div class="dropdown-menu dropdown-menu-right">
                      <a class="dropdown-item" href="?do=addFormula">Add new formula</a>
                      <a class="dropdown-item popup-link" id="csv" href="pages/csvImport.php">Import from a CSV</a>
                      <?php if($pv_online['email'] && $pv_online['password']){?>
                       <div class="dropdown-divider"></div>
	                   <a class="dropdown-item" href="#" data-toggle="modal" data-target="#pv_online_import">Import from PV Online</a>
                       <?php } ?>
                    </div>
                    </div>
                </div></td>
                <td width="2%">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2">
                                  
            <div class="card-body">
              <div class="table-responsive">
<?php
if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients"))== 0){
	echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no ingredients yet, click <a href="?do=ingredients">here</a> to add.</div>';
}elseif(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData"))== 0){
	echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no formulas yet, click <a href="?do=addFormula">here</a> to add.</div>';

}else{
?>
     <div id="listFormulas">
     <ul>
         <li><a href="#all"><span>All</span></a></li>
         <li><a href="#oriental"><span>Oriental</span></a></li>
         <li><a href="#woody"><span>Woody</span></a></li>
         <li><a href="#floral"><span>Floral</span></a></li>
         <li><a href="#fresh"><span>Fresh</span></a></li>
         <li><a href="#unisex"><span>Unisex</span></a></li>
         <li><a href="#men"><span>Men</span></a></li>
         <li><a href="#women"><span>Women</span></a></li>
     </ul>
     <div id="all"><?php formulaProfile($conn,null,null); ?></div>
     <div id="oriental"><?php formulaProfile($conn,'oriental',null); ?></div>
     <div id="woody"><?php formulaProfile($conn,'woody',null); ?></div>
     <div id="floral"><?php formulaProfile($conn,'floral',null); ?></div>
     <div id="fresh"><?php formulaProfile($conn,'fresh',null); ?></div>
     <div id="unisex"><?php formulaProfile($conn,null,'unisex'); ?></div>
     <div id="men"><?php formulaProfile($conn,null,'men'); ?></div>
     <div id="women"><?php formulaProfile($conn,null,'women'); ?></div>
<?php } ?>
                
              </div>
            </div>
                </td>
              </tr>
            </table>
            <p>&nbsp;</p>
          </div>
        </div>
      </div>
    </div>
    
<?php if($pv_online['email'] && $pv_online['password']){?>
<!-- Modal PV ONLINE-->
<div class="modal fade" id="pv_online_import" tabindex="-1" role="dialog" aria-labelledby="pv_online_import" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pv_online_import">Import formulas from PV Online</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div id="pvImportMsg"></div>
  <form action="javascript:pv_online_import('ingredients')" method="get" name="form1" target="_self" id="form1">
      <strong>WARNING:</strong><br />
      you are about to import data from PV Online, please bear in mind, PV Online is a community driven database therefore may contain unvalidated or incorrect data. <br />
      If your local database contains already a formula with the same name, the formula data will not be imported. <p></p>
      Formulas online: <strong><?php echo pvOnlineStats($pvOnlineAPI, $pv_online['email'], $pv_online['password'], 'formulas');?></strong>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="button" value="Import">
      </div>
     </form>
    </div>
  </div>
</div>
<?php } ?>
<script type="text/javascript" language="javascript" >
$(function() {
  $("#listFormulas").tabs();
});

//Clone
function cloneMe(cloneFormulaName) {	  
$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "clone",
		formula: cloneFormulaName,
		},
	dataType: 'html',
    success: function (data) {
        if ( data.indexOf("Error") > -1 ) {
			$('#msg').html(data); 
		}else{
			$('#msg').html(data);
			location.reload();
		}
    }
  });
};

function deleteMe(deleteFormulaID) {	  
$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "delete",
		fid: deleteFormulaID,
		},
	dataType: 'html',
    success: function (data) {
        if ( data.indexOf("Error") > -1 ) {
			$('#msg').html(data); 
		}else{
			$('#msg').html(data);
			location.reload();
		}
    }
  });
};

function addTODO(fid) {
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: 'todo',
		fid: fid,
		add: true,
		},
	dataType: 'html',
    success: function (data) {
		//location.reload();
	  	$('#msg2').html(data);
    }
  });
};

<?php if($pv_online['email'] && $pv_online['password']){?>

function pv_online_import(items) {
$.ajax({ 
    url: 'pages/pvonline.php', 
	type: 'get',
    data: {
		action: "import",
		items: items
		},
	dataType: 'html',
    success: function (data) {
	 // $('#pv_online_import').modal('toggle');
	  $('#pvImportMsg').html(data);
    }
  });
};

<?php } ?>
</script>

<?php 

require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');

require_once('../func/formulaProfile.php');

?>


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

<script type="text/javascript" language="javascript" >

$('a[rel=tipsy]').tipsy();
	
$('.popup-link').magnificPopup({
	type: 'iframe',
	closeOnContentClick: false,
	closeOnBgClick: false,
	showCloseBtn: true,
});
	
$('#tdData,#tdDataSup,#tdDataCat,#tdDataUsers,#tdDataCustomers').DataTable({
    "paging":   true,
	"info":   true,
	"lengthMenu": [[20, 35, 60, -1], [20, 35, 60, "All"]]
});

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
			$('#inMsg').html(data); 
		}else{
			$('#inMsg').html(data);
			list_formulas();
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
			$('#inMsg').html(data); 
		}else{
			$('#inMsg').html(data);
			list_formulas();
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
	  	$('#inMsg').html(data);
    }
  });
};
</script>

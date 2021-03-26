<?php 

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

require_once(__ROOT__.'/func/formulaProfile.php');

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
	                  <a class="dropdown-item" href="#" data-toggle="modal" data-target="#add_formula">Add new formula</a>
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
            
<!--ADD FORMULA MODAL-->
<div class="modal fade" id="add_formula" tabindex="-1" role="dialog" aria-labelledby="add_formula" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_formula">Add a new formula</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="addFormulaMsg"></div>
  	  <form action="javascript:add_formula()" id="form1">
      <table width="100%" border="0">  
          <tr>
            <td>Name:</td>
            <td><input name="name" id="name" type="text" class="form-control" /></td>
          </tr>
          <tr>
            <td>Profile:</td>
            <td>
            <select name="profile" id="profile" class="form-control">
                <option value="oriental">Oriental</option>
                <option value="woody">Woody</option>
                <option value="floral">Floral</option>
                <option value="fresh">Fresh</option>
                <option value="other">Other</option>
            </select>
            </td>
          </tr>
           <tr>
           	<td valign="top">Notes:</td>
            <td><textarea name="notes" id="notes" cols="45" rows="5" class="form-control"></textarea></td>
           </tr>  
      </table>  
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="btnAdd" value="Add">
      </div>
     </form>
    </div>
  </div>
</div>

<script type="text/javascript" language="javascript" >

$('a[rel=tipsy]').tipsy();
	
$('.popup-link').magnificPopup({
	type: 'iframe',
	closeOnContentClick: false,
	closeOnBgClick: false,
	showCloseBtn: true,
});
	
$('#tdData').DataTable({
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

function add_formula() {
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'POST',
    data: {
		action: 'addFormula',
		name: $("#name").val(),
		profile: $("#profile").val(),
		notes: $("#notes").val(),
		},
	dataType: 'html',
    success: function (data) {
	  	$('#addFormulaMsg').html(data);
		//list_formulas();

    }
  });
};

</script>

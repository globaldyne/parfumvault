<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="/?do=listFormulas">Formulas</a></h2>
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
                      <a class="dropdown-item" href="/?do=addFormula">Add new formula</a>
                      <a class="dropdown-item popup-link" id="csv" href="/pages/csvImport.php">Import from a CSV</a>
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
}elseif(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas"))== 0){
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
     <div id="all"><?php formulaProfile($dbhost,$dbuser,$dbpass,$dbname,null,null); ?></div>
     <div id="oriental"><?php formulaProfile($dbhost,$dbuser,$dbpass,$dbname,'oriental',null); ?></div>
     <div id="woody"><?php formulaProfile($dbhost,$dbuser,$dbpass,$dbname,'woody',null); ?></div>
     <div id="floral"><?php formulaProfile($dbhost,$dbuser,$dbpass,$dbname,'floral',null); ?></div>
     <div id="fresh"><?php formulaProfile($dbhost,$dbuser,$dbpass,$dbname,'fresh',null); ?></div>
     <div id="unisex"><?php formulaProfile($dbhost,$dbuser,$dbpass,$dbname,null,'unisex'); ?></div>
     <div id="men"><?php formulaProfile($dbhost,$dbuser,$dbpass,$dbname,null,'men'); ?></div>
     <div id="women"><?php formulaProfile($dbhost,$dbuser,$dbpass,$dbname,null,'women'); ?></div>
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
    
<script type="text/javascript" language="javascript" >
$(function() {
  $("#listFormulas").tabs();
});

//Clone
function cloneMe(cloneFormulaName) {	  
$.ajax({ 
    url: '/pages/manageFormula.php', 
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
</script>
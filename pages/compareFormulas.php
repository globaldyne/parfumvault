<?php 
if (!defined('pvault_panel')){ die('Not Found');}

if($_REQUEST['formula_a'] && $_REQUEST['formula_b']){
	require(__ROOT__.'/func/compareFormulas.php');
	$id_a = $_REQUEST['formula_a'];
	$id_b = $_REQUEST['formula_b'];

	$meta_a = mysqli_fetch_array(mysqli_query($conn, "SELECT name,fid FROM formulasMetaData WHERE id = '$id_a'"));
	if($_REQUEST['compare'] == '1'){
		$meta_b = mysqli_fetch_array(mysqli_query($conn, "SELECT name,fid FROM formulasMetaData WHERE id = '$id_b'"));
	}
	if($_REQUEST['compare'] == '2'){		
		$revision = $_REQUEST['revision'];
		$meta_b['name'] = base64_decode($id_b).' - Revision: '.$_REQUEST['revision'];
	}
	
	$q_a = mysqli_query($conn, "SELECT ingredient,concentration,quantity FROM formulas WHERE fid = '".$meta_a['fid']."' ORDER BY ingredient ASC");
	if($_REQUEST['compare'] == '1'){
		$q_b = mysqli_query($conn, "SELECT ingredient,concentration,quantity FROM formulas WHERE fid = '".$meta_b['fid']."' ORDER BY ingredient ASC");
	}
	if($_REQUEST['compare'] == '2'){
		$q_b = mysqli_query($conn, "SELECT ingredient,concentration,quantity FROM formulasRevisions WHERE revision = '$revision' AND fid = '$id_b' ORDER BY ingredient ASC");
	}

	while ($formula = mysqli_fetch_array($q_a)){
	    $formula_a[] = $formula;
	}
	while ($formula = mysqli_fetch_array($q_b)){
	    $formula_b[] = $formula;
	}
	$r = compareFormula($formula_a, $formula_b, array('ingredient','concentration','quantity'),$meta_a['name'], $meta_b['name']);
}
?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
            <?php if($_GET['compare'] && $_REQUEST['formula_a'] && $_REQUEST['formula_b']){?>
             <h5 class="m-1 text-primary">Formula A: <strong><?=$meta_a['name']?></strong></h5>
             <h5 class="m-1 text-primary">Formula B: <strong><?php echo $meta_b['name'];?></strong></h5>
        	<?php }else{ ?>
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=compareFormulas">Compare formulas</a></h2>
            <?php } ?>
            </div>
            <div class="card-body">
            <?php if($_GET['compare'] && $_REQUEST['formula_a'] && $_REQUEST['formula_b']){?>
              <div>
              <?php if(empty($r[$meta_a['name']])){ ?>
              <div class="alert alert-info alert-dismissible">No differences between formulas found</div>
              <?php }else{ ?>
                <table class="table table-bordered compare" id="formula_a" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th colspan="3" class="compare_formula_name"><?=$meta_a['name']?></th>
                    </tr>
                    <tr>
                      <th width="22%">Ingredient</th>
                      <th width="10%">Purity %</th>
                      <th width="10%">Quantity</th>
                    </tr>
                  </thead>
                  <?php foreach($r[$meta_a['name']] as $formula){ ?>
					  <tr>
                      <td align="center"><?=$formula['ingredient']?></td>
					  <td align="center"><?=$formula['concentration']?:'100'?></td>
                      <td align="center"><?=number_format($formula['quantity'],$settings['qStep'])?></td>                    </tr>
                    <?php }?>                   
                </table> 
                  <table class="table table-bordered compare" id="formula_b" width="100%" cellspacing="0">
                  <thead>
                 	<tr>
                      <th colspan="3" class="compare_formula_name"><?=$meta_b['name']?></th>
                    </tr>
                    <tr>
                      <th width="22%">Ingredient</th>
                      <th width="10%">Purity %</th>
                      <th width="10%">Quantity</th>
                    </tr>
                  </thead>
                  <?php foreach($r[$meta_b['name']] as $formula){ ?>
					  <tr>
                      <td align="center"><?=$formula['ingredient']?></td>
					  <td align="center"><?=$formula['concentration']?:'100'?></td>
                      <td align="center"><?=number_format($formula['quantity'],$settings['qStep'])?></td>                     
                    </tr>
                    <?php }?>                   
                </table> 
                <?php } ?>
                <div>
                </div>
            </div>
            

            <?php 
			}else{ 
				if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData"))== 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="?do=addFormula">create</a> at least one formula first.</div>';
					return;
				}
				
				if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE type = 'Carrier' OR type = 'Solvent'"))== 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="?do=ingredients">add</a> at least one solvent or carrier first.</div>';
					return;
				}
			?>
           <form action="?do=compareFormulas&compare=1" method="post" enctype="multipart/form-data" target="_blank">
           
           <table width="100%" border="0">
  <tr>
    <td width="9%">Formula A:</td>
    <td width="24%">
    <select name="formula_a" id="formula_a" class="form-control selectpicker" data-live-search="true">
     <?php
		$a = mysqli_query($conn, "SELECT id,name FROM formulasMetaData ORDER BY name ASC");
		while ($formula_a = mysqli_fetch_array($a)){
			echo '<option value="'.$formula_a['id'].'">'.$formula_a['name'].'</option>';
		}
	  ?>
     </select>
   </td>
    <td width="67%">&nbsp;</td>
  </tr>
  <tr>
    <td>Formula B:</td>
    <td>
    <select name="formula_b" id="formula_b" class="form-control selectpicker" data-live-search="true">
    <?php
		$b = mysqli_query($conn, "SELECT id,name FROM formulasMetaData ORDER BY name ASC");
		while ($formula_b = mysqli_fetch_array($b)){
			echo '<option value="'.$formula_b['id'].'">'.$formula_b['name'].'</option>';
		}
	?>
    </select>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><input type="submit" name="button" class="btn btn-info" id="button" value="Compare"></td>
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
           </form>          
            <?php } ?>
           </div>
        </div>
      </div>
   </div>
  </div>

<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$todo = mysqli_query($conn, "SELECT fid, name, SUM(toadd) AS toAdd FROM makeFormula GROUP BY name ORDER BY name ASC");

?>

<script>
function removeTODO(fid) {
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: 'todo',
		fid: fid,
		remove: true,
		},
	dataType: 'html',
    success: function (data) {
		location.reload();
	  	$('#msg').html(data);
    }
  });
};
</script>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
		<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=todo">Formulas to make</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                      <th colspan="3">
                      </th>
                    </tr>
                    <tr>
                      <th>Formula Name</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="todo_data">
                    <?php while ($r = mysqli_fetch_array($todo)) { ?>
                    <tr>
                      <td align="center"><a href="pages/makeFormula.php?fid=<?php echo $r['fid']; ?>" target="_blank" class="<?php if($r['toAdd'] == '0'){ echo $class = 'fas fa-check'; } ?>"><?php echo ' '.$r['name']; ?></a></td>
					  <td align="center"><a href="javascript:removeTODO('<?php echo $r['fid']; ?>')" onclick="return confirm('Delete <?php echo $r['name']; ?>?');" class="fas fa-trash"></a></td>
					  </tr>
				  <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
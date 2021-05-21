<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$cart = mysqli_query($conn, "SELECT * FROM cart ORDER BY name ASC");


?>

<script>

function removeFromCart(materialId) {
$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "removeFromCart",
		materialId: materialId
		},
	dataType: 'text',
    success: function (data) {
		location.reload();
	  $('#msg').html(data);
    }
  });
};

</script>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
		<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=cart">Cart</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                      <th colspan="5">
                      </th>
                    </tr>
                    <tr>
                      <th>Material</th>
                      <th>Purity (%)</th>
                      <th>Quantity (ml)</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="todo_data">
                    <?php while ($r = mysqli_fetch_array($cart)) { ?>
                    <tr>
                      <td align="center">
                      	<div class="btn-group">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $r['name']; ?></a>
                             <div class="dropdown-menu dropdown-menu-right">
                         <?php 
						 $a = getIngSupplier($r['ingID'],$conn); 
						 foreach ($a as $b){ ?>
                             <a class="dropdown-item" target="_blank" href="<?=$b['supplierLink']?>"><?=$b['name']?></a> 
                         <?php } ?>
                             </div>
                         </div>
                      </td>
                      <td align="center"><?=$r['purity'] ?? '100'?></td>
                      <td align="center"><a href="#"><?php echo $r['quantity']; ?></a></td>
					  <td align="center"><a href="javascript:removeFromCart('<?php echo $r['id'] ?>')" onclick="return confirm('Remove <?php echo $r['name']; ?> from cart?');" class="fas fa-trash"></a></td>
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
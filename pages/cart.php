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
<?php require_once('pages/top.php'); ?>
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
                      <th colspan="3">
                      </th>
                    </tr>
                    <tr>
                      <th>Material</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="todo_data">
                    <?php  
					while ($r = mysqli_fetch_array($cart)) {
						
					?>
                    <tr>
                      <td align="center"><a href="<?php echo $r['supplier_link']; ?>" target="_blank"><?php echo $r['name']; ?></a></td>
					  <td align="center"><a href="javascript:removeFromCart('<?php echo $r['id']; ?>')" onclick="return confirm('Remove <?php echo $r['name']; ?> from cart?');" class="fas fa-trash"></a></td>
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
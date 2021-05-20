<?php

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$ingID = mysqli_real_escape_string($conn, $_GET["id"]);

$q = mysqli_query($conn, "SELECT * FROM suppliers WHERE ingID = '$ingID'");

?>
<script type='text/javascript'>

</script>
			<h3>Suppliers</h3>
            <hr>
             <div class="card-body">
              <div>
                <table class="table table-bordered" id="tdIngSup" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                      <th colspan="6">
                  <div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addSupplier">Add new</a>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>eShop</th>
                      <th>Price (<?=$settings['currency']?>)</th>
                      <th>Size (<?=$settings['mUnit']?>)</th>
                      <th>Manufacturer</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="ing_supplier">
                    <?php 
						while ($supplier = mysqli_fetch_array($q)) { 
						$sup = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '".$supplier['id']."'"));
					?>
                    <tr>
                      <td data-name="supplier_name" class="supplier_name" data-type="text" align="center" data-pk="<?=$supplier['ingSupplierID']?>"><?=$sup['name']?></td>
                      <td data-name="supplierLink" class="supplierLink" data-type="text" align="center" data-pk="<?=$supplier['ingSupplierID']?>"><a href="<?=$supplier['supplierLink']?>" target="_blank" class="fas fa-store"></a></td>
					  <td data-name="price" class="price" data-type="text" align="center" data-pk="<?=$supplier['ingSupplierID']?>"><?=$supplier['price']?></td>
					  <td data-name="size" class="size" data-type="text" align="center" data-pk="<?=$supplier['ingSupplierID']?>"><?=$supplier['size']?></td>
					  <td data-name="manufacturer" class="manufacturer" data-type="text" align="center" data-pk="<?=$supplier['ingSupplierID']?>"><?=$supplier['manufacturer']?></td>
                      <td align="center"><a href="javascript:deleteSupplier('<?=$supplier['id']?>')" onclick="return confirm('Remove <?=$sup['name']?>?');" class="fas fa-trash"></a></td>
					</tr>
				  	<?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            
            
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 $('#tdIngSup').DataTable({
    "paging":   true,
	"info":   true,
	"lengthMenu": [[15, 35, 60, -1], [15, 35, 60, "All"]]
 });

 $('#ing_supplier').editable({
	  container: 'body',
	  selector: 'td.supplier_name',
	  type: 'POST',
	  url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	  title: 'Name',
 });
  
 $('#ing_supplier').editable({
	  container: 'body',
	  selector: 'td.supplierLink',
	  type: 'POST',
	  url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	  title: 'Store link',
 });
  
 $('#ing_supplier').editable({
	  container: 'body',
	  selector: 'td.price',
	  type: 'POST',
	  url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	  title: 'Price',
 });
	
 $('#ing_supplier').editable({
  	container: 'body',
  	selector: 'td.size',
  	type: 'POST',
	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Size',
 });
 
 $('#ing_supplier').editable({
	container: 'body',
	selector: 'td.manufacturer',
	type: 'POST',
	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID;?>",
	title: 'Manufacturer',
 });
	  
});
</script>

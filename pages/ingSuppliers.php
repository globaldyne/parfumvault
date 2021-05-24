<?php

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$ingID = mysqli_real_escape_string($conn, $_GET["id"]);

$q = mysqli_query($conn, "SELECT * FROM suppliers WHERE ingID = '$ingID' ORDER BY preferred");

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
						$sup = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '".$supplier['ingSupplierID']."'"));
					?>
                    <tr>
                      <td data-name="ingSupplierID" class="ingSupplierID" data-type="select" align="center" data-pk="<?=$supplier['ingSupplierID']?>"><?=$sup['name']?></td>
                      <td data-name="supplierLink" class="supplierLink" data-type="textarea" align="center" data-pk="<?=$supplier['ingSupplierID']?>"><a href="#"><?=$supplier['supplierLink']?></a></td>
					  <td data-name="price" class="price" data-type="text" align="center" data-pk="<?=$supplier['ingSupplierID']?>" id="<?=$supplier['ingSupplierID']?>"><?=$supplier['price']?></td>
					  <td data-name="size" class="size" data-type="text" align="center" data-pk="<?=$supplier['ingSupplierID']?>"><?=$supplier['size']?></td>
					  <td data-name="manufacturer" class="manufacturer" data-type="text" align="center" data-pk="<?=$supplier['ingSupplierID']?>"><?=$supplier['manufacturer']?></td>
                      <td align="center"><a <?php if($supplier['preferred']){ ?>href="#" class="fas fa-star" <?php }else{ ?>href="javascript:prefSID('<?=$supplier['ingSupplierID']?>','1')" class="far fa-star" data-toggle="tooltip" data-placement="top" title="Set as preferred supplier."<?php } ?> ></a>&nbsp;<a href="javascript:getPrice('<?=urlencode($supplier['supplierLink'])?>','<?=$supplier['size']?>','<?=$supplier['ingSupplierID']?>')" data-toggle="tooltip" data-placement="top" title="Get the latest price from the supplier." class="fas fa-sync"></a>&nbsp;<a href="<?=$supplier['supplierLink']?>" target="_blank" class="fas fa-store" data-toggle="tooltip" data-placement="top" title="Open supplier's web page."></a>&nbsp;<a href="javascript:deleteSupplier('<?=$supplier['id']?>')" onclick="return confirm('Remove <?=$sup['name']?>?');" class="fas fa-trash" data-toggle="tooltip" data-placement="top" title="Remove supplier from the list."></a></td>
					</tr>
				  	<?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 $('[data-toggle="tooltip"]').tooltip();
 $('#tdIngSup').DataTable({
    "paging":   true,
	"info":   true,
	"lengthMenu": [[15, 35, 60, -1], [15, 35, 60, "All"]]
 });
 


$('#ing_supplier').editable({
	pvnoresp: false,
	highlight: false,
	container: 'body',
	selector: 'td.ingSupplierID',
	type: 'POST',
	emptytext: "",
	emptyclass: "",
  	url: "update_data.php?ingSupplier=update&ingID=<?=$ingID?>",
    source: [
			 <?php
				$res_ing = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY name ASC");
				while ($r_ing = mysqli_fetch_array($res_ing)){
					echo '{value: "'.htmlspecialchars($r_ing['id']).'", text: "'.htmlspecialchars($r_ing['name']).'"},';
			}
			?>
          ],
    success: function (data) {
			reload_data();
	}
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

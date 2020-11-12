<?php

require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');

$ingID = mysqli_real_escape_string($conn, $_GET["id"]);

$qAlg = mysqli_query($conn, "SELECT * FROM allergens WHERE ing = '$ingID'");

?>
<script type='text/javascript'>
$(document).ready(function() {
	
    $('#tdData').DataTable({
	    "paging":   true,
		"info":   true,
		"lengthMenu": [[5, 35, 60, -1], [20, 35, 60, "All"]]
	});
}); 
</script>
				   <h3>Allergens</h3>
                                 <hr>
                    <div class="card-body">
              <div>
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                      <th colspan="4">
                  <div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addAllergen">Add new</a>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>CAS</th>
                      <th>Percentage %</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="ing_allergen">
                    <?php while ($allergen = mysqli_fetch_array($qAlg)) { ?>
                    <tr>
                      <td data-name="name" class="name" data-type="text" align="center" data-pk="<?=$allergen['id']?>"><?=$allergen['name']?></td>
                      <td data-name="cas" class="cas" data-type="text" align="center" data-pk="<?=$allergen['id']?>"><?=$allergen['cas']?></td>
					  <td data-name="percentage" class="percentage" data-type="text" align="center" data-pk="<?=$allergen['id']?>"><?=$allergen['percentage']?></td>
                      <td align="center"><a href="javascript:deleteAllergen('<?=$allergen['id']?>')" onclick="return confirm('Remove <?=$allergen['name']?>?');" class="fas fa-trash"></a></td>
					</tr>
				  	<?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            
            
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 
  $('#ing_allergen').editable({
  container: 'body',
  selector: 'td.name',
  type: 'POST',
  url: "update_data.php?allergen=update&ing=<?=$ingID;?>",
  title: 'Name',
 });
  
  $('#ing_allergen').editable({
  container: 'body',
  selector: 'td.percentage',
  type: 'POST',
  url: "update_data.php?allergen=update&ing=<?=$ingID;?>",
  title: 'Percentage',
 });
  
  $('#ing_allergen').editable({
  container: 'body',
  selector: 'td.cas',
  type: 'POST',
  url: "update_data.php?allergen=update&ing=<?=$ingID;?>",
  title: 'CAS',
 });
	
});
</script>

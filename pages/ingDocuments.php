<?php

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/formatBytes.php');

$ingID = mysqli_real_escape_string($conn, $_GET["id"]);

$q = mysqli_query($conn, "SELECT * FROM documents WHERE ownerID = '$ingID' AND type = '1'");

?>
<script type='text/javascript'>

</script>
			<h3>Documents</h3>
            <hr>
             <div class="card-body">
              <div>
                <table class="table table-bordered" id="tdIngDocs" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                      <th colspan="5">
                  <div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addDoc">Add new</a>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Document</th>
                      <th>File</th>
                      <th>Notes</th>
                      <th>Size</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="ing_doc">
                    <?php while ($doc = mysqli_fetch_array($q)) { ?>
                    <tr>
                      <td data-name="name" class="name" data-type="text" align="center" data-pk="<?=$doc['id']?>"><?=$doc['name']?></td>
                      <td align="center"><a href="viewDoc.php?id=<?=$doc['id']?>" target="_blank" class="fa fa-file-alt"></a></td>
					  <td data-name="notes" class="notes" data-type="text" align="center" data-pk="<?=$doc['id']?>"><?=$doc['notes']?></td>
					  <td align="center"><?=formatBytes(strlen($doc['docData']))?></td>
                      <td align="center"><a href="javascript:deleteDoc('<?=$doc['id']?>')" onclick="return confirm('Remove <?=$doc['name']?>?');" class="fas fa-trash" data-toggle="tooltip" data-placement="top" title="Remove <?=$doc['name']?> document"></a></td>
					</tr>
				  	<?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 $('[data-toggle="tooltip"]').tooltip();
 $('#tdIngDocs').DataTable({
    "paging":   true,
	"info":   true,
	"lengthMenu": [[15, 35, 60, -1], [15, 35, 60, "All"]]
 });
 


 $('#ing_doc').editable({
	  container: 'body',
	  selector: 'td.name',
	  type: 'POST',
	  url: "update_data.php?ingDoc=update&ingID=<?=$ingID;?>",
	  title: 'Document name',
 });
  
 $('#ing_doc').editable({
	  container: 'body',
	  selector: 'td.notes',
	  type: 'POST',
	  url: "update_data.php?ingDoc=update&ingID=<?=$ingID;?>",
	  title: 'Notes',
 });
	


});
</script>

<?php 

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$cat_q = mysqli_query($conn, "SELECT * FROM ingCategory ORDER BY name ASC");

?>
<table width="100%" border="0" class="table table-striped table-sm">
              <tr>
                <td colspan="8"><div id="catMsg"></div></td>
              </tr>
              <tr>
                <td width="4%"><p>Category:</p></td>
                <td width="12%"><input type="text" name="category" id="category" class="form-control"/></td>
                <td width="1%">&nbsp;</td>
                <td width="6%">Description:</td>
                <td width="13%"><input type="text" name="cat_notes" id="cat_notes" class="form-control"/></td>
                <td width="2%">&nbsp;</td>
                <td width="22%"><input type="submit" name="add-category" id="add-category" value="Add" class="btn btn-info" /></td>
                <td width="40%">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="8">
                <div class="card-body">
              <div>
                <table class="table table-bordered" id="tdDataCat" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="cat_data">
                  <?php while ($cat = mysqli_fetch_array($cat_q)) { ?>
                    <tr>
                      <td data-name="name" class="name" data-type="text" align="center" data-pk="<?php echo $cat['id'];?>"><?php echo $cat['name'];?></td>
					  <td width="60%" data-name="notes" class="notes" data-type="text" align="center" data-pk="<?php echo $cat['id']; ?>"><?php echo wordwrap($cat['notes'], 150, "<br />\n");?></td>
                      <td align="center"><a href="javascript:catDel('<?php echo $cat['id']; ?>')" onclick="return confirm('Delete category <?php echo $cat['name'];?>?')" class="fas fa-trash"></a></td>
					</tr>
				  	<?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
                </td>
              </tr>
            </table>
<script type="text/javascript" language="javascript" >
$('#add-category').click(function() {
	$.ajax({ 
		url: 'pages/update_settings.php', 
			type: 'POST',
			data: {
				manage: 'category',
				
				category: $("#category").val(),
				cat_notes: $("#cat_notes").val(),
				
				},
			dataType: 'html',
			success: function (data) {
				$('#catMsg').html(data);
				list_cat();
			}
		  });
});

$('#tdDataCat').DataTable({
    "paging":   true,
	"info":   true,
	"lengthMenu": [[20, 35, 60, -1], [20, 35, 60, "All"]]
});

$('#cat_data').editable({
  container: 'body',
  selector: 'td.name',
  url: "pages/update_data.php?settings=cat",
  title: 'Category',
  type: "POST",
  dataType: 'json',
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
});
 
$('#cat_data').editable({
  container: 'body',
  selector: 'td.notes',
  url: "pages/update_data.php?settings=cat",
  title: 'Description',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
});
</script>
            
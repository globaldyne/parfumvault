<?php 

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$cat_q = mysqli_query($conn, "SELECT * FROM ingCategory ORDER BY name ASC");

?>
<script>
$('.popup-link').magnificPopup({
	type: 'iframe',
	closeOnContentClick: false,
	closeOnBgClick: false,
  	showCloseBtn: true,
});
</script>
<table width="100%" border="0" class="table table-striped table-sm">
              <div id="catMsg"></div>
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
                    <tr>
                      <th>Image</th>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="cat_data">
                  <?php while ($cat = mysqli_fetch_array($cat_q)) { ?>
                    <tr>
                      <td align="center" valign="middle"><img class="img_ing" src="<?php if($cat['image']){ echo 'uploads/categories/'.$cat['image']; }else{ echo 'img/molecule.png'; }?>" /></td>
                      <td align="center" valign="middle" class="name" data-name="name" data-type="text" data-pk="<?php echo $cat['id'];?>"><?php echo $cat['name'];?></td>
					  <td width="60%" align="center" valign="middle" class="notes" data-name="notes" data-type="text" data-pk="<?php echo $cat['id']; ?>"><?php echo wordwrap($cat['notes'], 150, "<br />\n");?></td>
                      <td align="center" valign="middle"><a href="pages/editCat.php?id=<?=$cat['id']?>" class="fas fa-edit popup-link"></a> &nbsp; <a href="javascript:catDel('<?php echo $cat['id']; ?>')" onclick="return confirm('Delete category <?php echo $cat['name'];?>?')" class="fas fa-trash"></a></td>
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
            
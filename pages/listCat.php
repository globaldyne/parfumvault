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
                      <th>Colour Key</th>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="cat_data">
                  <?php while ($cat = mysqli_fetch_array($cat_q)) { ?>
                    <tr>
                      <td align="center" valign="middle"><a href="pages/editCat.php?id=<?=$cat['id']?>" class="popup-link"><?php if($cat['image']){ echo '<img class="img_ing" src="'.$cat['image'].'"/>'; }else{ echo '<img class="img_ing" src="img/molecule.png"/>'; }?> </a></td>
                      
                      <td align="center" valign="middle"><a href="#" class="colorKey" style="background-color: rgb(<?=$cat['colorKey']?>)" id="colorKey" data-name="colorKey" data-type="select" data-pk="<?=$cat['id']?>" data-title="Choose Colour Key for <?=$cat['name']?>"></a></td>
                      
                      <td align="center" valign="middle" class="name" data-name="name" data-type="text" data-pk="<?=$cat['id']?>"><?=$cat['name']?></td>
					  <td width="60%" align="center" valign="middle" class="notes" data-name="notes" data-type="text" data-pk="<?=$cat['id']?>"><?php echo wordwrap($cat['notes'], 150, "<br />\n");?></td>
                      <td align="center" valign="middle"><a href="javascript:catDel('<?=$cat['id']?>')" onclick="return confirm('Delete category <?=$cat['name']?>?')" class="fas fa-trash"></a></td>
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
});

//Change colorKey
$('.colorKey').editable({
	pvnoresp: false,
	highlight: false,
	type: "POST",
	emptytext: "",
	emptyclass: "",
  	url: "pages/update_data.php?settings=cat",
    source: [
			 {value: '0, 255, 255', text: 'Aqua'},
             {value: '240, 255, 255', text: 'Azure'},
             {value: '245, 245, 220', text: 'Beige'},
             {value: '165, 42, 42', text: 'Brown'},
			 {value: '0, 0, 0', text: 'Black'},
			 {value: '0, 0, 255', text: 'Blue'},
             {value: '0, 255, 255', text: 'Cyan'},
			 {value: '0, 0, 139', text: 'Dark Blue'},
             {value: '0, 139, 139', text: 'Dark Cyan'},
			 {value: '0, 100, 0', text: 'Dark Green'},
             {value: '169, 169, 169', text: 'Dark Grey'},
             {value: '189, 183, 107', text: 'Dark Khaki'},
             {value: '255, 140, 0', text: 'Dark Orange'},
             {value: '153, 50, 204', text: 'Dark Orchid'},
             {value: '233, 150, 122', text: 'Dark Salmon'},
             {value: '255, 0, 255', text: 'Fuchsia'},
             {value: '255, 215, 0', text: 'Gold'},
             {value: '0, 128, 0', text: 'Green'},
             {value: '240, 230, 140', text: 'Khaki'},
             {value: '173, 216, 230', text: 'Light Blue'},
             {value: '224, 255, 255', text: 'Light Cyan'},
             {value: '211, 211, 211', text: 'Light Grey'},
             {value: '144, 238, 144', text: 'Light Green'},
             {value: '255, 182, 193', text: 'Light Pink'},
             {value: '255, 255, 224', text: 'Light Yellow'},
             {value: '0, 255, 0', text: 'Lime'},
             {value: '255, 0, 255', text: 'Magenta'},
             {value: '0, 0, 128', text: 'Navy'},
             {value: '128, 0, 128', text: 'Purple'},
             {value: '128, 128, 0', text: 'Olive'},
             {value: '255, 165, 0', text: 'Orange'},
			 {value: '255, 0, 0', text: 'Red'},
             {value: '255, 192, 203', text: 'Pink'},
             {value: '192, 192, 192', text: 'Silver'},
			 {value: '255, 255, 0', text: 'Yellow'},
			 {value: '255, 255, 255', text: 'White'},
          ],
	dataType: 'html',
	success: function () {
		list_cat();
	}
});

</script>
            
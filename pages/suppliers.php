<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$q = mysqli_query($conn, "SELECT * FROM ingSuppliers ORDER BY name ASC");

?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=suppliers">Suppliers</a></h2>
            </div>
            <div id="errMsg"></div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder noexport">
                      <th colspan="10">
                  <div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addSupplier">Add new</a>
                            <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>Platform</th>
                      <th>Price start tag</th>
                      <th>Price end tag</th>
                      <th>Additional costs</th>
                      <th>Price per</th>
                      <th>Min ml</th>
                      <th>Min grams</th>
                      <th>Description</th>
                      <th class="noexport">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="supplier_data">
                    <?php while ($supplier = mysqli_fetch_array($q)) {?>
                    <tr>
                      <td data-name="name" class="name" data-type="text" align="center" data-pk="<?=$supplier['id']?>"><?=$supplier['name']?></td>
                      <td data-name="platform" class="platform" data-type="select" align="center" data-pk="<?php echo $supplier['id']; ?>"><?=$supplier['platform']?></td>
                      <td data-name="price_tag_start" class="price_tag_start" data-type="textarea" align="center" data-pk="<?=$supplier['id']?>"><?=$supplier['price_tag_start']?></td>
                      <td data-name="price_tag_end" class="price_tag_end" data-type="textarea" align="center" data-pk="<?=$supplier['id']?>"><?=$supplier['price_tag_end']?></td>
                      <td data-name="add_costs" class="add_costs" data-type="text" align="center" data-pk="<?=$supplier['id']?>"><?=$supplier['add_costs']?></td>
                      <td data-name="price_per_size" class="price_per_size" data-type="select" align="center" data-pk="<?=$supplier['id']?>"><?php if($supplier['price_per_size'] == '0'){ echo 'Product'; }else{ echo 'Volume'; }?></td>
                      <td data-name="min_ml" class="min_ml" data-type="text" align="center" data-pk="<?=$supplier['id']?>"><?=$supplier['min_ml']?></td>
					  <td data-name="min_gr" class="min_gr" data-type="text" align="center" data-pk="<?=$supplier['id']?>"><?=$supplier['min_gr']?></td>
					  <td data-name="notes" class="notes" data-type="text" align="center" data-pk="<?=$supplier['id']?>"><?=$supplier['notes']?></td>
					  <td class="noexport" align="center"><a href="javascript:deleteSupplier('<?=$supplier['id']?>')" onclick="return confirm('Delete <?=$supplier['name']?>?')" class="fas fa-trash"></a></td>
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
 
<!-- ADD NEW-->
<div class="modal fade" id="addSupplier" tabindex="-1" role="dialog" aria-labelledby="addSupplier" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSupplier">Add supplier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="inf"></div>
          <form action="javascript:addSupplier()" method="get" name="form1" target="_self" id="form1">
            <p>Name:
  <input class="form-control" name="name" type="text" id="name" />
            </p>
            Platform:
            <p>
              <select class="form-control" name="select" id="platform">
                <option value="woocomerce">Woocomerce</option>
                <option value="shopify">Shopify</option>
                <option value="Other">Other/Custom</option>
              </select>
            </p>
            <p>Price start tag:</p>
            <p>
              <input class="form-control" type="text" name="price_tag_start" id="price_tag_start" />
            </p>
            <p>Price end tag:</p>
            <p>
              <input class="form-control" type="text" name="price_tag_end" id="price_tag_end" />
            </p>
            <p>Additional costs:</p>
            <p>
              <input class="form-control" type="text" name="add_costs" id="add_costs" />
            </p>
            <p>Minimum ml quantiy:</p>
            <p>
              <input class="form-control" type="text" name="min_ml" id="min_ml" />
            </p>
            <p>Minimum grams quantiy:</p>
            <p>
              <input class="form-control" type="text" name="min_gr" id="min_gr" />
            </p>
            <p>Price to be calucalted per:</p>
            <p>
              <select class="form-control" name="select" id="price_per_size">
                <option value="0">Product</option>
                <option value="1">Volume</option>
              </select>
              </p>
            <p>
            Description: 
            <input class="form-control" name="description" type="text" id="description" />            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="button" value="Add">
      </div>
     </form>
    </div>
  </div>
</div>
</div>
<script type="text/javascript" language="javascript" >

$('#supplier_data').editable({
  container: 'body',
  selector: 'td.name',
  url: "pages/update_data.php?settings=sup",
  title: 'Supplier',
  type: "POST",
  dataType: 'html',
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
});

$('#supplier_data').editable({
	container: 'body',
	selector: 'td.platform',
	type: 'POST',
  	url: "pages/update_data.php?settings=sup",
    source: [
			 {value: "woocomerce", text: "Woocomerce"},
			 {value: "shopify", text: "Shopify"},
			 {value: "other", text: "Custom/Other"},
          ],
});

$('#supplier_data').editable({
	container: 'body',
	selector: 'td.price_per_size',
	type: 'POST',
  	url: "pages/update_data.php?settings=sup",
    source: [
			 {value: "0", text: "Product"},
			 {value: "1", text: "Volume"},
          ],
});

$('#supplier_data').editable({
  container: 'body',
  selector: 'td.min_ml',
  url: "pages/update_data.php?settings=sup",
  title: 'Minimum ml',
  type: "POST",
  dataType: 'html',
    validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
});

$('#supplier_data').editable({
  container: 'body',
  selector: 'td.min_gr',
  url: "pages/update_data.php?settings=sup",
  title: 'Minimum grams',
  type: "POST",
  dataType: 'html',
    validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
});

$('#supplier_data').editable({
  container: 'body',
  selector: 'td.price_tag_start',
  url: "pages/update_data.php?settings=sup",
  title: 'Price tag start',
  type: "POST",
  dataType: 'html'
});

$('#supplier_data').editable({
  container: 'body',
  selector: 'td.price_tag_end',
  url: "pages/update_data.php?settings=sup",
  title: 'Price tag end',
  type: "POST",
  dataType: 'html'
});

$('#supplier_data').editable({
  container: 'body',
  selector: 'td.add_costs',
  url: "pages/update_data.php?settings=sup",
  title: 'Additional Costs',
  type: "POST",
  dataType: 'html'
});

$('#supplier_data').editable({
  container: 'body',
  selector: 'td.notes',
  url: "pages/update_data.php?settings=sup",
  title: 'Description',
  type: "POST",
  dataType: 'html',
  validate: function(value){
  }
});

function deleteSupplier(ID) {	  
$.ajax({ 
    url: 'pages/update_data.php', 
	type: 'GET',
    data: {
		supp: 'delete',
		ID: ID,
		},
	dataType: 'html',
    success: function (data) {
	  	$('#errMsg').html(data);
		location.reload();
    }
  });
};

function addSupplier() {	  
$.ajax({ 
    url: 'pages/update_data.php', 
	type: 'POST',
    data: {
		supp: 'add',
		name: $("#name").val(),
		platform: $("#platform").val(),
		price_tag_start: $("#price_tag_start").val(),
		price_tag_end: $("#price_tag_end").val(),
		add_costs: $("#add_costs").val(),
		description: $("#description").val(),
		min_ml: $("#min_ml").val(),
		min_gr: $("#min_gr").val()
		},
	dataType: 'html',
    success: function (data) {
	  	$('#inf').html(data);
     	$("#name").val('');
     	$("#description").val('');
     	$("#platform").val('');
     	$("#price_tag_start").val('');
     	$("#price_tag_end").val('');
     	$("#add_costs").val('');
     	$("#min_ml").val('');
     	$("#min_gr").val('');

    }
  });
};
//Export
$('#csv').on('click',function(){
  $("#tdData").tableHTMLExport({
	type:'csv',
	filename:'suppliers.csv',
	separator: ',',
  	newline: '\r\n',
  	trimContent: true,
  	quoteFields: true,
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',	
	htmlContent: false,
  	// debug
  	consoleLog: false   
	});
})

</script>
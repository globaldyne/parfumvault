<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$bottleId = $_GET['id'];

if ($bottleId && is_numeric($bottleId)) {
    $stmtBottle = $conn->prepare("SELECT * FROM bottles WHERE id = ? AND owner_id = ?");
    $stmtBottle->bind_param("ii", $bottleId, $userID);
    $stmtBottle->execute();
    $resultBottle = $stmtBottle->get_result();
    $bottle = $resultBottle->fetch_array(MYSQLI_ASSOC);
    $stmtBottle->close();
} else {
    $response["error"] = "Invalid or missing bottle ID.";
    echo json_encode($response);
    return;
}

$stmtDoc = $conn->prepare("SELECT docData AS photo FROM documents WHERE ownerID = ? AND type = '4' AND owner_id = ?");
$stmtDoc->bind_param("ii", $bottle['id'], $userID);
$stmtDoc->execute();
$resultDoc = $stmtDoc->get_result();
$doc = $resultDoc->fetch_array(MYSQLI_ASSOC);
$stmtDoc->close();

$supplier = [];
$stmtSup = $conn->prepare("SELECT id, name FROM ingSuppliers WHERE owner_id = ? ORDER BY name ASC");
$stmtSup->bind_param("i", $userID);
$stmtSup->execute();
$resultSup = $stmtSup->get_result();

while ($suppliers = $resultSup->fetch_array(MYSQLI_ASSOC)) {
    $supplier[] = $suppliers;
}
$stmtSup->close();

?>


<div class="container">
    <div class="text-center">
        <div id="bottle_pic" class="mb-3">
            <div class="loader"></div>
        </div>
    </div>
    <div id="bottle-inf"></div>
      <div class="row g-3">
        <div class="col-md-4">
            <label for="bottle-name" class="form-label">Name</label>
            <input class="form-control" name="bottle-name" type="text" id="bottle-name" value="<?=$bottle['name']?>">
        </div>
        <div class="col-md-4">
            <label for="bottle-size" class="form-label">Size (ml)</label>
            <input class="form-control" name="bottle-size" type="text" id="bottle-size" value="<?=$bottle['ml']?>">
        </div>
        <div class="col-md-4">
            <label for="bottle-price" class="form-label">Price:</label>
            <input class="form-control" name="bottle-price" type="text" id="bottle-price" value="<?=$bottle['price']?>">
        </div>
        <div class="col-md-4">
            <label for="bottle-height" class="form-label">Height</label>
            <input class="form-control" name="bottle-height" type="text" id="bottle-height" value="<?=$bottle['height']?>">
        </div>
        <div class="col-md-4">
            <label for="bottle-width" class="form-label">Width</label>
            <input class="form-control" name="bottle-width" type="text" id="bottle-width" value="<?=$bottle['width']?>">
        </div>
        <div class="col-md-4">
            <label for="bottle-weight" class="form-label">Weight (grams)</label>
            <input class="form-control" name="bottle-weight" type="text" id="bottle-weight" value="<?=$bottle['weight']?>">
        </div>
        <div class="col-md-4">
            <label for="bottle-diameter" class="form-label">Diameter</label>
            <input class="form-control" name="bottle-diameter" type="text" id="bottle-diameter" value="<?=$bottle['diameter']?>">
        </div>
        <div class="col-md-4">
            <label for="bottle-pieces" class="form-label">Stock (pieces):</label>
            <input class="form-control" name="bottle-pieces" type="text" id="bottle-pieces" value="<?=$bottle['pieces']?>">
        </div>
        <div class="col-md-4">
            <label for="bottle-notes" class="form-label">Notes</label>
            <input class="form-control" name="bottle-notes" type="text" id="bottle-notes" value="<?=$bottle['notes']?>">
        </div>
        <div class="col-md-4">
            <label for="bottle-supplier" class="form-label">Supplier</label>
            <select name="bottle-supplier" id="bottle-supplier" class="form-control">
                <option value="" selected></option>
                <?php foreach($supplier as $sup) { ?>
                    <option value="<?php echo $sup['name'];?>" <?php echo ($bottle['supplier']==$sup['name'])?"selected=\"selected\"":""; ?>><?php echo $sup['name'];?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="bottle-supplier_link" class="form-label">Supplier URL</label>
            <input class="form-control" name="bottle-supplier_link" type="text" id="bottle-supplier_link" value="<?=$bottle['supplier_link']?>">
        </div>
        <div class="col-md-4">
            <label for="bottle_pic_file" class="form-label">Image</label>
            <input type="file" name="bottle_pic_file" id="bottle_pic_file" class="form-control">
        </div>
        <div class="dropdown-divider"></div>
        <div class="modal-footer">
            <input type="submit" name="button" class="btn btn-primary" id="bottle-save" value="Save">
        </div>
    </div>
</div>
 
<hr>
<script>
$(document).ready(function() {

    $('#bottle_pic').html('<img class="img-profile-avatar" src="<?=$doc['photo']?: '/img/logo_def.png'; ?>">');
    $('#bottle-save').click(function() {
        $.ajax({ 
            url: '/core/core.php', 
            type: 'POST',
            data: {
                action: "update_bottle_data",
                bottle_id:  "<?=$bottle['id']?>",
                name: $("#bottle-name").val(),			
                size: $("#bottle-size").val(),
                price: $("#bottle-price").val(),
                height: $("#bottle-height").val(),
                width: $("#bottle-width").val(),
                diameter: $("#bottle-diameter").val(),
                pieces: $("#bottle-pieces").val(),
                weight: $("#bottle-weight").val(),
                notes: $("#bottle-notes").val(),
                supplier: $("#bottle-supplier").val(),
                supplier_link: $("#bottle-supplier_link").val(),
            },
            dataType: 'json',
            success: function (data) {
                if(data.success){
                    var msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>'+data.success+'</div>';
                    $('#tdDataBottles').DataTable().ajax.reload(null, true);
                }else if( data.error){
                    var msg = '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>'+data.error+'</div>';
                }
                $('#bottle-inf').html(msg);
            },error: function (xhr, status, error) {
                $('#bottle-inf').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error +'</div>');
            }
        });

        var fd = new FormData();
        var files = $('#bottle_pic_file')[0].files;

        if(files.length > 0 ){
            fd.append('bottle_pic',files[0]);
            $.ajax({ 
                url: '/core/core.php?update_bottle_pic=1&bottle_id=<?=$bottle['id']?>', 
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                cache: false,
                dataType: 'json',
                success: function (data) {
                    if(data.success){
                        $('#bottle_pic').html('<img class="img-profile-avatar" src="'+data.success.file+'">');
                        $('#tdDataBottles').DataTable().ajax.reload(null, true);
                    }else if( data.error){
                        $('#bottle-inf').html('<div class="alert alert-danger">'+data.error+'</div>');
                    }
                },error: function (xhr, status, error) {
                    $('#bottle-inf').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error +'</div>');
                }
            });
        }
    });
});   
</script>

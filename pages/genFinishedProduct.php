<div id="content-wrapper" class="d-flex flex-column">
    <?php require_once(__ROOT__.'/pages/top.php'); ?>
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h2 class="m-0 font-weight-bold text-primary-emphasis">Finished Product</h2>
            </div>
            <div class="card-body">
                <?php 
                if (!defined('pvault_panel')){ die('Not Found');}

                if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE owner_id = '$userID'"))){
                    echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>You need to <a href="/?do=listFormulas">create</a> at least one formula before you be able to generate a finished product</div>';
                    return;
                }
                if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bottles WHERE owner_id = '$userID'"))){
                    echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>You need to <a href="/?do=bottles">add</a> at least one bottle in your inventory first</div>';
                    return;
                }
                
                if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bottles WHERE price <= '0' AND owner_id = '$userID'"))){
                    echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Please make sure all your bottles suppliers contains valid prices</div>';
                    return;
                }
                
                if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE (type = 'Carrier' OR type = 'Solvent')  AND owner_id = '$userID'"))){
                    echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>You need to <a href="/?do=ingredients">add</a> at least one solvent or carrier first</div>';
                    return;
                }
                
                if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM suppliers WHERE price <= '0' AND owner_id = '$userID'"))){
                    echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Please make sure all your ingredients suppliers contains valid prices</div>';
                    return;
                }
                
                $cats_q = mysqli_query($conn, "SELECT name,description FROM IFRACategories ORDER BY id ASC");//PUBLIC
                while($cats_res = mysqli_fetch_array($cats_q)){
                    $cats[] = $cats_res;
                }
                $sup_q = mysqli_query($conn, "SELECT id,name FROM ingSuppliers WHERE owner_id = '$userID' ORDER BY id ASC");
                while($r = mysqli_fetch_array($sup_q)){
                    $suppliers[] = $r;
                }
                $fTypes_q = mysqli_query($conn, "SELECT id,name,description,concentration FROM perfumeTypes WHERE owner_id = '$userID' ORDER BY id ASC");
                while($fTypes_res = mysqli_fetch_array($fTypes_q)){
                    $fTypes[] = $fTypes_res;
                }
                ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="formulaID" class="col-form-label">Formula</label>
                        <div>
                            <select name="formulaID" id="formulaID" class="form-control selectpicker" data-live-search="true">
                                <?php
                                $sql = mysqli_query($conn, "SELECT id,fid,name,product_name FROM formulasMetaData WHERE owner_id = '$userID' AND product_name IS NOT NULL ORDER BY name ASC");
                                while ($formula = mysqli_fetch_array($sql)){
                                    echo '<option value="'.$formula['id'].'">'.$formula['name'].' ('.$formula['product_name'].')</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="concentration" class="col-form-label">Concentration</label>
                        <div>
                            <select name="concentration" id="concentration" class="form-control selectpicker" data-live-search="true">
                                <option value="100">Concentrated (100%)</option>
                                <?php foreach ($fTypes as $fType) {?>
                                <option value="<?php echo $fType['concentration'];?>"
                                    <?php echo ($info['finalType']==$fType['concentration'])?"selected=\"selected\"":""; ?>>
                                    <?php echo $fType['name'].' ('.$fType['concentration'];?>%)</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="supplier_id" class="col-form-label">Ingredients Supplier</label>
                        <div>
                            <select name="supplier_id" id="supplier_id" class="form-control selectpicker" data-live-search="true">
                                <option value="0" selected="selected">Formula Defaults</option>
                                <?php foreach ($suppliers as $supplier) {?>
                                <option value="<?=$supplier['id'];?>"><?=$supplier['name'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="defCatClass" class="col-form-label">Category Class</label>
                        <div>
                            <select name="defCatClass" id="defCatClass" class="form-control selectpicker" data-live-search="true">
                                <?php foreach ($cats as $IFRACategories) {?>
                                <option value="cat<?php echo $IFRACategories['name'];?>"
                                    <?php echo ($settings['defCatClass']=='cat'.$IFRACategories['name'])?"selected=\"selected\"":""; ?>>
                                    <?php echo 'Cat '.$IFRACategories['name'].' - '.$IFRACategories['description'];?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="batch_id" class="col-form-label">Batch ID</label>
                        <div>
                            <select name="batch_id" id="batch_id" class="form-control selectpicker" data-live-search="false">
                                <option value="0">Do Not Generate</option>
                                <option value="1">Generate</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="bottle_id" class="col-form-label">Bottle</label>
                        <div>
                            <select name="bottle_id" id="bottle_id" class="form-control selectpicker" data-live-search="true">
                                <?php
                                $sql = mysqli_query($conn, "SELECT id,name,ml FROM bottles WHERE ml != 0  AND owner_id = '$userID' ORDER BY ml DESC");
                                while ($bottle = mysqli_fetch_array($sql)){
                                    echo '<option value="'.$bottle['id'].'">'.$bottle['name'].' ('.$bottle['ml'].'ml)</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="carrier_id" class="col-form-label">Carrier</label>
                        <div>
                            <select name="carrier_id" id="carrier_id" class="form-control selectpicker" data-live-search="true">
                                <?php
                                $sql = mysqli_query($conn, "SELECT name,id FROM ingredients WHERE (type = 'Carrier' OR type = 'Solvent') AND owner_id = '$userID' ORDER BY name ASC");
                                while ($carrier = mysqli_fetch_array($sql)){
                                    echo '<option value="'.$carrier['id'].'">'.$carrier['name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="accessory_id" class="col-form-label">Accessory</label>
                        <div>
                            <select name="accessory_id" id="accessory_id" class="form-control selectpicker" data-live-search="true">
                                <option selected="selected">None</option>
                                <?php
                                $sql = mysqli_query($conn, "SELECT id, name, accessory FROM inventory_accessories WHERE owner_id = '$userID' ORDER BY name ASC");
                                while ($accessory = mysqli_fetch_array($sql)){
                                    echo '<option value="'.$accessory['id'].'">'.$accessory['name'].' ('.$accessory['accessory'].')</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5">
                        <input type="submit" name="button" class="btn btn-primary" id="btnGEN" value="Generate">
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div id="results"></div>
</div>
<script>
$(document).ready(function () {
    $('#btnGEN').click(function() {
        var formulaID = $("#formulaID").val();
        var bottleID = $("#bottle_id").val();
        var carrierID = $("#carrier_id").val();
        var accessoryID = $("#accessory_id").val();
        var concentration = $("#concentration").val();
        var defCatClass = $("#defCatClass").val();
        var supplierID = $("#supplier_id").val();
        var batchID = $("#batch_id").val();

        if (!formulaID || !bottleID || !carrierID || !concentration || !defCatClass) {
            alert("Please fill in all required fields.");
            return;
        }

        $('#results').html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin mx-2"></i>Generating finished product, please wait...</div>');

        $.ajax({
            url: '/pages/views/formula/finishedProduct.php',
            type: 'POST',
            data: {
                fid: formulaID,
                bottle_id: bottleID,
                carrier_id: carrierID,
                accessory_id: accessoryID,
                concentration: concentration,
                defCatClass: defCatClass,
                supplier_id: supplierID,
                batch_id: batchID
            },
            dataType: 'html',
            success: function(data) {
                $('#results').html(data);
            },
            error: function(xhr, status, error) {
                $('#results').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle mx-2"></i>Error generating finished product: ' + error + '</div>');
            }
        });
    });
});
</script>
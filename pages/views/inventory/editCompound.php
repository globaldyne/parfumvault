<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM inventory_compounds WHERE id = ? AND owner_id = ?");
$stmt->bind_param("si", $_GET['id'], $userID);
$stmt->execute();
$result = $stmt->get_result();
$rs = $result->fetch_assoc();

// Check if $rs is valid
if (!$rs) {
    die("No record found or insufficient permissions.");
}

// Query for documents
$data = [];
$docStmt = $conn->prepare("SELECT id, name FROM documents WHERE type = ? AND isBatch = ? AND owner_id = ?");
$type = 5;
$isBatch = 1;
$docStmt->bind_param("iii", $type, $isBatch, $userID);
$docStmt->execute();
$docResult = $docStmt->get_result();

// Fetch data
while ($res = $docResult->fetch_assoc()) {
    $data[] = $res;
}

$stmt->close();
$docStmt->close();

?>

<div class="modal-body" id="edit_compound">
    <div id="cmp_edit_inf"></div>
    <div class="row">
        <!-- Compound Name -->
        <div class="mb-3">
            <label for="cmp_edit_name" class="form-label">Compound name</label>
            <input name="cmp_edit_name" type="text" class="form-control" id="cmp_edit_name" 
                   value="<?= htmlspecialchars($rs['name']) ?>">
        </div>

        <!-- Batch -->
        <div class="mb-3">
            <label for="cmp_edit_batch" class="form-label">Batch</label>
            <select name="cmp_edit_batch" id="cmp_edit_batch" class="form-control" data-live-search="true">
                <?php foreach ($data as $b): ?>
                    <option value="<?= htmlspecialchars($b['id']) ?>" 
                            <?= $rs['batch_id'] == $b['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Bottle Size -->
        <div class="mb-3">
            <label for="cmp_edit_size" class="form-label">Bottle size (<?= htmlspecialchars($settings['mUnit']) ?>)</label>
            <input name="cmp_edit_size" type="text" class="form-control" id="cmp_edit_size" 
                   value="<?= htmlspecialchars($rs['size']) ?>">
        </div>

        <!-- Location -->
        <div class="mb-3">
            <label for="cmp_edit_location" class="form-label">Location</label>
            <input name="cmp_edit_location" type="text" class="form-control" id="cmp_edit_location" 
                   value="<?= htmlspecialchars($rs['location']) ?>">
        </div>

        <!-- Short Description -->
        <div class="mb-3">
            <label for="cmp_edit_desc" class="form-label">Short Description</label>
            <input name="cmp_edit_desc" type="text" class="form-control" id="cmp_edit_desc" 
                   value="<?= htmlspecialchars($rs['description']) ?>">
        </div>

        <!-- Label Info -->
        <div class="col-sm">
            <label for="cmp_edit_label_info" class="form-label">Label info</label>
            <textarea class="form-control" name="cmp_edit_label_info" id="cmp_edit_label_info" rows="5"><?= htmlspecialchars($rs['label_info']) ?></textarea>
        </div>
    </div>

    <div class="dropdown-divider mb-3"></div>

    <!-- Modal Footer -->
    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="cmp_edit_save">Save</button>
    </div>
</div>

<script>
$(document).ready(function() {
    const cmpId = <?= json_encode($rs['id']) ?>;

    $('#edit_compound #cmp_edit_save').click(function() {
        const payload = {
            action: 'update_inv_compound_data',
            cmp_id: cmpId,
            name: $('#cmp_edit_name').val(),
            batch_id: $('#cmp_edit_batch').val(),
            description: $('#cmp_edit_desc').val(),
            size: $('#cmp_edit_size').val(),
            location: $('#cmp_edit_location').val(),
            label_info: $('#cmp_edit_label_info').val()
        };

        $.ajax({
            url: '/core/core.php',
            type: 'POST',
            data: payload,
            dataType: 'json',
            success: function(response) {
                let msg;
                if (response.success) {
                    msg = `<div class="alert alert-success">
                                <i class="fa-solid fa-circle-check mx-2"></i>${response.success}
                           </div>`;
                    $('#tdDataCompounds').DataTable().ajax.reload(null, true);
                } else if (response.error) {
                    msg = `<div class="alert alert-danger">
                                <i class="fa-solid fa-circle-exclamation mx-2"></i>${response.error}
                           </div>`;
                }
                $('#cmp_edit_inf').html(msg);
            },
            error: function() {
                $('#cmp_edit_inf').html(
                    '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred. Please try again.</div>'
                );
            }
        });
    });
});
</script>


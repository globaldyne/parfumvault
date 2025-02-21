<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))));

require_once(__ROOT__ . '/inc/sec.php');
require_once(__ROOT__ . '/inc/opendb.php');
require_once(__ROOT__ . '/inc/settings.php');

$order_id = $_GET['order_id'];

// Fetch order details
$query = $conn->prepare("SELECT * FROM orders WHERE id = ? AND owner_id = ?");
$query->bind_param("is", $order_id, $userID);
$query->execute();
$result = $query->get_result();
$order = $result->fetch_assoc();
$query->close();

if (!$order) {
    echo '<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>Order not found.</div>';
    exit;
}

$orderDate = !empty($order['placed']) ? date('Y-m-d', strtotime($order['placed'])) : date('Y-m-d');
$receivedDate = !empty($order['received']) ? date('Y-m-d', strtotime($order['received'])) : date('Y-m-d');
$receivedChecked = !empty($order['received']) ? 'checked' : '';
?>

<form id="updateOrderForm">
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="form-floating">
                <select class="form-select" id="orderStatus" name="orderStatus">
                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <label for="orderStatus">Order Status</label>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="form-floating">
                <input type="date" class="form-control" id="orderDate" name="orderDate" value="<?php echo $orderDate; ?>">
                <label for="orderDate">Order Date</label>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="form-floating">
                <input type="date" class="form-control" id="receivedDate" name="receivedDate" value="<?php echo $receivedDate; ?>" <?php echo !$receivedChecked ? 'disabled' : ''; ?>>
                <label for="receivedDate">Received Date</label>
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="receivedCheckbox" <?php echo $receivedChecked; ?>>
                <label class="form-check-label" for="receivedCheckbox">
                    Order received
                </label>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="reference_number" name="reference_number" value="<?php echo htmlspecialchars($order['reference_number'], ENT_QUOTES, 'UTF-8'); ?>">
                <label for="reference_number">Tracking Number</label>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <div class="form-floating">
                <textarea class="form-control" id="orderNotes" name="orderNotes" style="height: 150px;"><?php echo htmlspecialchars($order['notes'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <label for="orderNotes">Order Notes</label>
            </div>
        </div>
    </div>
    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveOrderChanges">Save changes</button>
    </div>
</form>
            
<script>
$(document).ready(function() {
    function toggleReceivedCheckbox() {
        if ($('#orderStatus').val() === 'completed') {
            $('#receivedCheckbox').prop('disabled', false);
        } else {
            $('#receivedCheckbox').prop('disabled', true).prop('checked', false);
            $('#receivedDate').prop('disabled', true);
        }
    }

    $('#orderStatus').change(function() {
        toggleReceivedCheckbox();
    });

    $('#receivedCheckbox').change(function() {
        if ($(this).is(':checked')) {
            $('#receivedDate').prop('disabled', false);
        } else {
            $('#receivedDate').prop('disabled', true);
        }
    });

    $('#saveOrderChanges').click(function() {
        var formData = $('#updateOrderForm').serialize();
        $.ajax({
            url: '/core/core.php',
            type: 'POST',
            data: formData + '&action=updateorder',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#updateOrderModalMsg').html('<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle mx-2"></i>' + data.success + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $('#tdOrdersData').DataTable().ajax.reload(null, true);
                } else {
                    $('#updateOrderModalMsg').html('<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle mx-2"></i>' + data.error + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            },
            error: function(xhr, status, error) {
                $('#updateOrderModalMsg').html('<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred while updating the order. ' + error + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            }
        });
    });

    // Initial call to set the correct state of the received checkbox
    toggleReceivedCheckbox();
});
</script>


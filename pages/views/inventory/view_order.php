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

// Fetch order items
$items_query = $conn->prepare("SELECT * FROM order_items WHERE order_id = ? AND owner_id = ?");
$items_query->bind_param("is", $order_id, $userID);
$items_query->execute();
$items_result = $items_query->get_result();

$subtotal = 0;
while ($item = $items_result->fetch_assoc()) {
    $subtotal += $item['unit_price'];
}
$items_query->close();

// Calculate total
$discount = floatval($order['discount']);
$tax = floatval($order['tax']);
$shipping = floatval($order['shipping']);
$tax_amount = ($tax / 100) * $subtotal;
$total = $subtotal - $discount + $tax_amount + $shipping;

$orderDate = !empty($order['placed']) ? date('Y-m-d', strtotime($order['placed'])) : date('Y-m-d');
$receivedDate = !empty($order['received']) ? date('Y-m-d', strtotime($order['received'])) : date('Y-m-d');
$receivedChecked = !empty($order['received']) ? 'checked' : '';
?>

<div id="viewOrderForm">
    <div class="row mx-0 mb-3">
        <div class="col-md-6">
            <label class="form-label">Supplier</label>
            <div class="form-control-plaintext fw-bold"><?php echo htmlspecialchars($order['supplier'], ENT_QUOTES, 'UTF-8'); ?></div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Currency</label>
            <div class="form-control-plaintext fw-bold"><?php echo htmlspecialchars($order['currency'], ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
    </div>

    <hr class="my-4">
    <!-- Order Items -->
    <div class="table-responsive">
        <table id="orderViewTable" class="table table-hover">
            <thead>
                <tr>
                    <th>Ingredient</th>
                    <th>Size</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>SKU</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $items_query = $conn->prepare("SELECT * FROM order_items WHERE order_id = ? AND owner_id = ?");
                $items_query->bind_param("is", $order_id, $userID);
                $items_query->execute();
                $items_result = $items_query->get_result();
                while ($item = $items_result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($item['material'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($item['size'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($item['unit_price'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($item['unit_price'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($item['lot'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '</tr>';
                }
                $items_query->close();
                ?>
            </tbody>
        </table>
    </div>

    <hr class="my-4">

    <div class="row">
        <!-- Left Column: Order Details -->
        <div class="col-md-8">
            <div class="mb-3">
                <label class="form-label">Order Number</label>
                <div class="form-control-plaintext"><?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Tracking Number</label>
                <div class="form-control-plaintext"><?php echo htmlspecialchars($order['reference_number'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <div class="form-control-plaintext"><?php echo nl2br(htmlspecialchars($order['notes'], ENT_QUOTES, 'UTF-8')); ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Attached Order File</label>
                <?php if (!empty($order['attachments'])): 
                    $file_info = new finfo(FILEINFO_MIME_TYPE);
                    $mime_type = $file_info->buffer($order['attachments']);
                    $extension = '';
                    switch ($mime_type) {
                        case 'application/pdf':
                            $extension = 'pdf';
                            break;
                        case 'image/jpeg':
                            $extension = 'jpg';
                            break;
                        case 'image/png':
                            $extension = 'png';
                            break;
                        default:
                            $extension = 'bin';
                    }
                ?>
                    <a href="data:application/octet-stream;base64,<?php echo base64_encode($order['attachments']); ?>" download="<?php echo htmlspecialchars($order['reference_number'], ENT_QUOTES, 'UTF-8') . '.' . $extension; ?>">Download</a>
                <?php else: ?>
                    <div class="form-control-plaintext">No file attached</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Financial Details -->
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Discount</label>
                <div class="form-control-plaintext"><?php echo htmlspecialchars($order['discount'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Tax</label>
                <div class="form-control-plaintext"><?php echo htmlspecialchars($order['tax'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Shipping</label>
                <div class="form-control-plaintext"><?php echo htmlspecialchars($order['shipping'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Total</label>
                <div class="form-control-plaintext"><?php echo htmlspecialchars($total, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#orderViewTable').DataTable({
        paging: false,
        searching: true,
        info: false,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'csv',
                text: 'Export to CSV',
                className: 'btn btn-primary',
                title: 'supply_order_<?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?>'
            }
        ],
        language: {
            emptyTable: '<div class="alert alert-info mt-3"><i class="fa fa-info-circle mx-2"></i>No items found for this order.</div>',
            zeroRecords: '<div class="alert alert-info mt-3"><i class="fa fa-info-circle mx-2"></i>No matching items found.</div>'
        }
    });
});
</script>
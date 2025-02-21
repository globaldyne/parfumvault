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

$supplier_query = $conn->prepare("SELECT * FROM ingSuppliers WHERE name = ? AND owner_id = ?");
$supplier_query->bind_param("si", $order['supplier'], $userID);
$supplier_query->execute();
$supplier_result = $supplier_query->get_result();
$supplier = $supplier_result->fetch_assoc();
$supplier_query->close();

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

<div id="reOrderForm">
    <form id="reOrderForm">
        <div id="reOrderModalMsg" class="mx-2"></div>
        <div class="row mx-0 mb-3">
            <div class="col-md-6">
                <label class="form-label">Supplier</label>
                <div class="form-control-plaintext fw-bold"><?php echo htmlspecialchars($order['supplier'], ENT_QUOTES, 'UTF-8'); ?></div>
                <input type="hidden" name="supplier" value="<?php echo htmlspecialchars($order['supplier'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-6">
                <label for="supplierEmail" class="form-label">Supplier Email</label>
                <input type="email" class="form-control" id="supplierEmail" name="supplierEmail" value="<?php echo htmlspecialchars($supplier['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any notes to supplier here..."><?php echo htmlspecialchars($order['notes'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <hr class="my-4">
        <!-- Order Items -->
        <div class="table-responsive">
            <table id="orderReTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Ingredient</th>
                        <th>Size</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>SKU</th>
                        <th></th>
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
                        echo '<td><input type="text" class="form-control size-input" name="size[]" value="' . htmlspecialchars($item['size'], ENT_QUOTES, 'UTF-8') . '"></td>';
                        echo '<td><input type="text" class="form-control unit-price-input" name="unit_price[]" value="' . htmlspecialchars($item['unit_price'], ENT_QUOTES, 'UTF-8') . '"></td>';
                        echo '<td><input type="text" class="form-control quantity-input" name="quantity[]" value="' . htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8') . '"></td>';
                        echo '<td>' . htmlspecialchars($item['lot'], ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '<td><button type="button" class="btn btn-danger btn-sm removeItem">Remove</button></td>';
                        echo '</tr>';
                    }
                    $items_query->close();
                    ?>
                </tbody>
            </table>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="reOrderButton">Re-order</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    var table = $('#orderReTable').DataTable({
        paging: false,
        searching: false,
        info: false,
        dom: 'frtip',
        language: {
            emptyTable: '<div class="alert alert-info"><i class="bi bi-exclamation-triangle mx-2"></i>No items to reorder.</div>',
            zeroRecords: '<div class="alert alert-info"><i class="bi bi-exclamation-triangle mx-2"></i>No matching items found.</div>'
        },
        columnDefs: [
            { className: 'text-center', targets: '_all' },
            { orderable: false, targets: -1 }
        ]
    });

    function toggleReOrderButton() {
        if (table.rows().count() === 0) {
            $('#reOrderButton').prop('disabled', true);
        } else {
            $('#reOrderButton').prop('disabled', false);
        }
    }

    $('#orderReTable tbody').on('click', '.removeItem', function() {
        table.row($(this).parents('tr')).remove().draw();
        toggleReOrderButton();
    });

    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function calculateUnitPrice(row) {
        var size = parseFloat(row.find('input.size-input').val()) || 0;
        var quantity = parseFloat(row.find('input.quantity-input').val()) || 0;
        var unitPrice = size * quantity; // Adjust this formula as needed
        row.find('input.unit-price-input').val(unitPrice.toFixed(2));
    }

    $('#orderReTable').on('input', 'input.size-input, input.quantity-input', function() {
        var row = $(this).closest('tr');
        calculateUnitPrice(row);
    });

    $('#reOrderForm').submit(function(e) {
        e.preventDefault();
        var supplierEmail = $('#supplierEmail').val();

        if (!supplierEmail || !validateEmail(supplierEmail)) {
            $('#reOrderModalMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>Please enter a valid email address.</div>');
            return;
        }

        if (table.rows().count() === 0) {
            $('#reOrderModalMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>Please add at least one item to reorder.</div>');
            return;
        }

        var formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'reorder' });
        formData.push({ name: 'supplier', value: '<?php echo $order['supplier']; ?>' });
        formData.push({ name: 'currency', value: '<?php echo $order['currency']; ?>' });
        formData.push({ name: 'supplierEmail', value: supplierEmail });
        formData.push({ name: 'notes', value: $('#notes').val() });

        $('#orderReTable tbody tr').each(function(index, tr) {
            var item = $(tr).find('td').eq(0).text();
            var size = $(tr).find('input[name="size[]"]').val();
            var unit_price = $(tr).find('input[name="unit_price[]"]').val();
            var quantity = $(tr).find('input[name="quantity[]"]').val();
            var sku = $(tr).find('td').eq(4).text();

            formData.push({ name: 'items[' + index + '][item]', value: item });
            formData.push({ name: 'items[' + index + '][size]', value: size });
            formData.push({ name: 'items[' + index + '][unit_price]', value: unit_price });
            formData.push({ name: 'items[' + index + '][quantity]', value: quantity });
            formData.push({ name: 'items[' + index + '][sku]', value: sku });
        });

        $.ajax({
            url: '/core/core.php',
            type: 'POST',
            data: $.param(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    //$('#reOrderModalMsg').html('<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle mx-2"></i>Order successfully placed.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $('#tdOrdersData').DataTable().ajax.reload(null, true);

                    $('#reOrderModal').modal('hide');
                } else {
                    $('#reOrderModalMsg').html('<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle mx-2"></i>' + response.error + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            },
            error: function(xhr, status, error) {
                $('#reOrderModalMsg').html('<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred while placing the order. ' + error + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            }
        });
    });

    // Initial check to disable the button if no items
    toggleReOrderButton();
});
</script>
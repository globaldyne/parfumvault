<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))));

require_once(__ROOT__ . '/inc/sec.php');
require_once(__ROOT__ . '/inc/opendb.php');
require_once(__ROOT__ . '/inc/settings.php');
$query = "SELECT id, name, min_ml, min_gr FROM ingSuppliers WHERE owner_id = ? ORDER BY name ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $userID);
$stmt->execute();
$res_ingSupplier = $stmt->get_result();

?>

<div id="addOrderForm">
    <div class="row mx-0 mb-3">
        <div class="col-md-6 form-floating">
            <select class="form-select" name="supplier_name" id="supplier_name" required>
                <option value="">Choose supplier</option>
                <?php while ($row_ingSupplier = mysqli_fetch_assoc($res_ingSupplier)) { ?>
                    <option value="<?= htmlspecialchars($row_ingSupplier['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($row_ingSupplier['name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php } ?>
            </select>
            <label class="mx-2" for="supplier_name">Supplier Name</label>
        </div>

        <div class="col-md-6 form-floating">
            <select class="form-select" name="currency" id="currency">
                <option value="">Choose currency</option>
                <?php
                $json = file_get_contents(__ROOT__ . '/inc/currencies.json');
                $currencies = json_decode($json, true);
                foreach ($currencies as $code => $details) {
                    $symbol = $details['symbol'];
                    $selected = ($user_settings['currency_code'] == $code) ? 'selected' : '';
                    $name = $details['name'];
                    echo "<option value=\"$symbol\" $selected>$name ($symbol) [$code]</option>";
                }
                ?>
            </select>
            <label class="mx-2" for="currency">Currency</label>
        </div>
    </div>

    <hr class="my-4">
    <!-- Order Items -->
    <div class="table-responsive">
        <table id="orderItemsTable" class="table table-hover">
            <thead>
                <tr>
                    <th class="order-items">Ingredient <i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Select the ingredient from the supplier's list."></i></th>
                    <th>Size <i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Specify the size or quantity of the item."></i></th>
                    <th>Unit Price <i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Enter the price per unit of the item."></i></th>
                    <th>Quantity</th>
                    <th>Subtotal <i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Subtotal is the total cost for each item (Unit Price x Quantity)."></i></th>
                    <th>SKU <i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional. A lot number is a unique identifier assigned to a batch of products."></i></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <!-- Dynamic rows will be added here -->
            </tbody>
        </table>
        <button type="button" class="btn btn-primary" id="addItem" disabled>Add ingredient</button> - or -
        <button type="button" class="btn btn-secondary" id="importItems" disabled>Import</button>

        <!-- Import Modal -->
        <div class="modal fade" id="importModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Items</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" id="importTextarea" rows="10" placeholder="Enter items in CSV format (ingredient,size,price,quantity,SKU)"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="importSubmit">Import</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <hr class="my-4">

    <div class="row">
        <!-- Left Column: Order Details -->
        <div class="col-md-8">
            <div class="mb-3">
                <label for="order_number" class="form-label">Order Number</label>
                <div class="input-group">
                    <span class="input-group-text">#</span>
                    <input type="text" class="form-control" id="order_number" placeholder="AAAA-12345" autocomplete="off" value="">
                </div>
            </div>
            <div class="mb-3">
                <label for="referenceNumber" class="form-label">Tracking Number</label>
                <div class="input-group">
                    <span class="input-group-text">#</span>
                    <input type="text" class="form-control" id="referenceNumber" placeholder="XXXX-12345" autocomplete="off" value="">
                </div>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" placeholder="Enter notes" autocomplete="off" style="height: 68px !important;"></textarea>
            </div>
            <div class="mb-3">
                <label for="orderFile" class="form-label">Attach order file
                    <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Accepted formats: .pdf, .doc, .docx, .xls, .xlsx, .csv"></i>
                </label>
                <input type="file" class="form-control" id="orderFile" name="orderFile" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv">
            </div>
        </div>

        <!-- Right Column: Financial Details -->
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Discount</label>
                <div class="input-group">
                    <span id="discounts_symbol" class="input-group-text">$</span>
                    <input type="text" class="form-control" placeholder="0" autocomplete="off" inputmode="decimal" id="discounts">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Tax</label>
                <div class="input-group">
                    <span id="tax_symbol" class="input-group-text">%</span>
                    <input type="text" class="form-control" placeholder="0" autocomplete="off" inputmode="decimal" id="tax">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Shipping</label>
                <div class="input-group">
                    <span id="shipping_symbol" class="input-group-text">$</span>
                    <input type="text" class="form-control" placeholder="0" autocomplete="off" inputmode="decimal" id="shipping">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Total</label>
                <div id="totals" class="fw-bold">0.00</div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveNewOrder">Save order</button>
    </div>
</div>


<script>
$(document).ready(function() {
    var availableItems = [];

    $('[data-bs-toggle="tooltip"]').tooltip();

    var orderItemsTable = $('#orderItemsTable').DataTable({
        paging: false,
        searching: false,
        info: false,
        columnDefs: [
            { targets: -1, orderable: false } // Disable sorting for the last column (actions)
        ],
        language: {
            emptyTable: '<div class="alert alert-info mt-3"><i class="fa fa-info-circle mx-2"></i>No items added yet. Select a supplier and click "Add Item" to get started.</div>'
        }
    });

    function populateItemSelect() {
        var supplierId = $('#supplier_name').val();
        var deferred = $.Deferred();
        $.getJSON('/core/list_suppliers_materials_data.php?supplier_id=' + supplierId, function(response) {
            let items = response.data || []; // Ensure we access the correct array

            // Get already selected items
            var selectedItems = [];
            $('#orderItemsTable tbody tr').each(function() {
                var item = $(this).find('input[name="item[]"]').val();
                if (item) {
                    selectedItems.push(item);
                }
            });

            if (!Array.isArray(items)) {
                console.error('Invalid data format:', response);
                deferred.resolve('<input type="text" class="form-control item-autocomplete wide-input" name="item[]" placeholder="Error loading items">');
            } else {
                // Sort items alphabetically by material name
                items.sort((a, b) => a.material.localeCompare(b.material));

                availableItems = items
                    .filter(item => !selectedItems.includes(item.material)) // Filter out already selected items
                    .map(item => ({
                        label: `${item.material} (CAS: ${item.cas})`,
                        value: item.material,
                        price: item.price,
                        size: item.size,
                        sku: item.supplier_sku || ''
                    }));

                if (availableItems.length === 0) {
                    $('#addItem').prop('disabled', true); // Disable Add Item button if no items left
                } else {
                    $('#addItem').prop('disabled', false); // Enable Add Item button if items are available
                }

                deferred.resolve(`<input type="text" class="form-control item-autocomplete wide-input" name="item[]" placeholder="Search for ingredient">`);

                // Initialize autocomplete
                $('.item-autocomplete').autocomplete({
                    source: availableItems,
                    appendTo: '#addOrderForm', // Ensure the autocomplete results appear inside the form
                    select: function(event, ui) {
                        var row = $(this).closest('tr');
                        row.find('input[name="unit_price[]"]').val(ui.item.price);
                        row.find('input[name="size[]"]').val(ui.item.size);
                        row.find('input[name="lot_number[]"]').val(ui.item.sku);
                        var quantity = parseFloat(row.find('input[name="quantity[]"]').val()) || 1;
                        var subtotal = ui.item.price * quantity;
                        row.find('input[name="subtotal[]"]').val(subtotal.toFixed(2));
                        updateTotal();
                    }
                });
            }
        });
        return deferred.promise();
    }

    // Disable addItem button if no supplier is selected and clear DataTable items
    $('#supplier_name').change(function() {
        if ($(this).val() === "") {
            $('#addItem').prop('disabled', true);
            $('#importItems').prop('disabled', true);
            orderItemsTable.clear().draw(); // Clear DataTable items
        } else {
            if (orderItemsTable.rows().count() > 0) {
                bootbox.confirm({
                    title: "Change Supplier",
                    message: "Changing the supplier will clear all current order items. Do you want to proceed?",
                    buttons: {
                        confirm: {
                            label: 'Yes',
                            className: 'btn-success'
                        },
                        cancel: {
                            label: 'No',
                            className: 'btn-danger'
                        }
                    },
                    callback: function(result) {
                        if (result) {
                            $('#addItem').prop('disabled', false);
                            $('#importItems').prop('disabled', false);
                            orderItemsTable.clear().draw(); // Clear DataTable items
                        } else {
                            $('#supplier_name').val($('#supplier_name').data('previous'));
                        }
                    }
                });
            } else {
                $('#addItem').prop('disabled', false);
                $('#importItems').prop('disabled', false);
            }
        }
        $('#supplier_name').data('previous', $(this).val());
    }).trigger('change'); // Trigger change event on page load to set initial state

    function updateTotal() {
        var discounts = parseFloat($('#discounts').val()) || 0;
        var taxPercentage = parseFloat($('#tax').val()) || 0;
        var shipping = parseFloat($('#shipping').val()) || 0;
        var subtotal = 0;

        $('#orderItemsTable tbody tr').each(function() {
            var row = $(this);
            var rowSubtotal = parseFloat(row.find('input[name="subtotal[]"]').val()) || 0;
            subtotal += rowSubtotal;
        });

        var tax = (taxPercentage / 100) * subtotal;
        var total = subtotal - discounts + tax + shipping;
        var selectedCurrency = $('#currency').val().split('|')[0];
        $('#totals').text(selectedCurrency + ' ' + (total ? total.toFixed(2) : '0.00'));
    }

    $('#discounts, #tax, #shipping').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, ''); // Allow only numeric values
        updateTotal();
    });

    $('#orderItemsTable tbody').on('input', 'input[name="unit_price[]"], input[name="quantity[]"], input[name="size[]"]', function() {
        var row = $(this).closest('tr');
        var unitPrice = parseFloat(row.find('input[name="unit_price[]"]').val()) || 0;
        var quantity = parseFloat(row.find('input[name="quantity[]"]').val()) || 0;
        var subtotal = unitPrice * quantity;
        row.find('input[name="subtotal[]"]').val(subtotal.toFixed(2));
        updateTotal(); // Update total after changing unit price, quantity, or size
    });

    $('#addItem').click(function() {
        populateItemSelect().done(function(itemInput) {
            var newRow = orderItemsTable.row.add([
                itemInput,
                '<input type="text" class="form-control" name="size[]" placeholder="Size">',
                '<input type="text" class="form-control" name="unit_price[]" placeholder="Unit Price" inputmode="decimal">',
                '<input type="text" class="form-control" name="quantity[]" placeholder="Quantity" inputmode="decimal" value="1">',
                '<input type="text" class="form-control bg-dark" name="subtotal[]" placeholder="Subtotal" readonly>',
                '<input type="text" class="form-control" name="lot_number[]" placeholder="Lot Number">',
                '<button type="button" class="btn btn-danger btn-sm removeItem">Remove</button>'
            ]).draw(false).node();

            // Initialize autocomplete for the new row
            $(newRow).find('.item-autocomplete').autocomplete({
                source: availableItems,
                appendTo: '#addOrderForm', // Ensure the autocomplete results appear inside the form
                select: function(event, ui) {
                    var row = $(this).closest('tr');
                    row.find('input[name="unit_price[]"]').val(ui.item.price);
                    row.find('input[name="size[]"]').val(ui.item.size);
                    row.find('input[name="lot_number[]"]').val(ui.item.sku);
                    var quantity = parseFloat(row.find('input[name="quantity[]"]').val()) || 1;
                    var subtotal = ui.item.price * quantity;
                    row.find('input[name="subtotal[]"]').val(subtotal.toFixed(2));
                    updateTotal();
                }
            });

            populateItemSelect(); // Re-populate item select to update available items
        });
    });

    $('#importItems').click(function() {
        $('#importModal').modal('show');
    });

    $('#importSubmit').click(function() {
        var importData = $('#importTextarea').val();
        try {
            var rows = importData.split('\n');
            rows.forEach(function(row) {
                var columns = row.split(',');
                if (columns.length === 5) {
                    var item = {
                        item: columns[0].trim(),
                        size: columns[1].trim(),
                        unit_price: columns[2].trim(),
                        quantity: columns[3].trim(),
                        lot_number: columns[4].trim()
                    };
                    var subtotal = parseFloat(item.unit_price) * parseFloat(item.quantity);
                    var newRow = orderItemsTable.row.add([
                        `<input type="text" class="form-control item-autocomplete wide-input" name="item[]" value="${item.item}" placeholder="Search for ingredient">`,
                        `<input type="text" class="form-control" name="size[]" value="${item.size}" placeholder="Size">`,
                        `<input type="text" class="form-control" name="unit_price[]" value="${item.unit_price}" placeholder="Unit Price" inputmode="decimal">`,
                        `<input type="text" class="form-control" name="quantity[]" value="${item.quantity}" placeholder="Quantity" inputmode="decimal">`,
                        `<input type="text" class="form-control bg-dark" name="subtotal[]" value="${subtotal.toFixed(2)}" placeholder="Subtotal" readonly>`,
                        `<input type="text" class="form-control" name="lot_number[]" value="${item.lot_number}" placeholder="Lot Number">`,
                        '<button type="button" class="btn btn-danger btn-sm removeItem">Remove</button>'
                    ]).draw(false).node();

                    // Initialize autocomplete for the new row
                    $(newRow).find('.item-autocomplete').autocomplete({
                        source: availableItems,
                        appendTo: '#addOrderForm',
                        select: function(event, ui) {
                            var row = $(this).closest('tr');
                            row.find('input[name="unit_price[]"]').val(ui.item.price);
                            row.find('input[name="size[]"]').val(ui.item.size);
                            row.find('input[name="lot_number[]"]').val(ui.item.sku);
                            var quantity = parseFloat(row.find('input[name="quantity[]"]').val()) || 1;
                            var subtotal = ui.item.price * quantity;
                            row.find('input[name="subtotal[]"]').val(subtotal.toFixed(2));
                            updateTotal();
                        }
                    });
                }
            });
            $('#importModal').modal('hide');
            updateTotal();
        } catch (e) {
            alert('Invalid CSV format');
        }
    });

    $('#orderItemsTable tbody').on('click', '.removeItem', function() {
        orderItemsTable.row($(this).closest('tr')).remove().draw();
        updateTotal(); // Update total after removing an item
        populateItemSelect(); // Re-populate item select to update available items
    });

    var currencySelect = document.getElementById('currency');
    var totalsSymbol = document.getElementById('totals');
    var shippingSymbol = document.getElementById('shipping_symbol');
    var discountsSymbol = document.getElementById('discounts_symbol');

    function updateSymbols() {
        var selectedCurrency = currencySelect.value.split('|')[0];
        totalsSymbol.innerText = selectedCurrency;
        shippingSymbol.innerText = selectedCurrency;
        discountsSymbol.innerText = selectedCurrency;
    }

    // Update the symbols on page load
    updateSymbols();

    // Update the symbols when the currency changes
    currencySelect.addEventListener('change', updateSymbols);

    $('.selectpicker').selectpicker();

    $('#saveNewOrder').click(function() {
        // Validate required fields
        var supplierId = $('#supplier_name').val();
        var currency = $('#currency').val();
        var orderItems = [];
        var hasEmptyFields = false;

        $('#orderItemsTable tbody tr').each(function() {
            var row = $(this);
            var item = {
                item: row.find('input[name="item[]"]').val(),
                size: row.find('input[name="size[]"]').val(),
                unit_price: row.find('input[name="unit_price[]"]').val(),
                quantity: row.find('input[name="quantity[]"]').val(),
                subtotal: row.find('input[name="subtotal[]"]').val(),
                lot_number: row.find('input[name="lot_number[]"]').val()
            };

            // Check for empty fields
            if (!item.item || !item.size || !item.unit_price || !item.quantity) {
                hasEmptyFields = true;
                row.find('input[name="item[]"], input[name="size[]"], input[name="unit_price[]"], input[name="quantity[]"]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                return false; // Break the loop
            }

            orderItems.push(item);
        });

        $('#addOrderModalMsg').html('<div class="alert alert-info"><i class="bi bi-info-circle-fill mx-2"></i>Saving order...</div>');

        var formData = new FormData();
        formData.append('action', 'addorder');
        formData.append('supplier_id', supplierId);
        formData.append('currency', currency);
        formData.append('reference_number', $('#referenceNumber').val());
        formData.append('notes', $('#notes').val());
        formData.append('discounts', $('#discounts').val());
        formData.append('tax', $('#tax').val());
        formData.append('shipping', $('#shipping').val());
        formData.append('order_number', $('#order_number').val());
        formData.append('orderFile', $('#orderFile')[0].files[0]);

        orderItems.forEach(function(item, index) {
            formData.append('order_items[' + index + '][item]', item.item);
            formData.append('order_items[' + index + '][size]', item.size);
            formData.append('order_items[' + index + '][unit_price]', item.unit_price);
            formData.append('order_items[' + index + '][quantity]', item.quantity);
            formData.append('order_items[' + index + '][subtotal]', item.subtotal);
            formData.append('order_items[' + index + '][lot_number]', item.lot_number);
        });

        $.ajax({
            url: '/core/core.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                   // $('#addOrderModalMsg').html('<div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert"><i class="bi bi-check-circle mx-2"></i>' + data.success + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $('#tdOrdersData').DataTable().ajax.reload(null, true);
                    $('#addOrderModal').modal('hide');
                } else {
                    $('#addOrderModalMsg').html('<div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert"><i class="bi bi-exclamation-circle mx-2"></i>' + data.error + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            },
            error: function(xhr, status, error) {
                $('#addOrderModalMsg').html('<div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred while saving order data. ' + error + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            }
        });
    });

    // Remove is-invalid class when input is filled
    $('#orderItemsTable tbody').on('input', 'input[name="item[]"], input[name="size[]"], input[name="unit_price[]"], input[name="quantity[]"]', function() {
        if ($(this).val()) {
            $(this).removeClass('is-invalid');
        }
    });

    $('#supplier_name, #currency').on('change', function() {
        if ($(this).val()) {
            $(this).removeClass('is-invalid');
        }
    });

});
</script>
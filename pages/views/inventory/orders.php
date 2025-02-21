<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php'); 
//require_once(__ROOT__.'/func/php-settings.php');
?>
 <div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle">Supply Orders</a></h2>
        </div>
        <div class="card-body">
            <div class="text-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                    <div class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" id="addOrder" data-bs-toggle="modal" data-bs-target="#addOrderModal" aria-controls="addOrder"><i class="bi bi-plus mx-2"></i>Create new order</a></li>
                    </div>
                </div>        
            </div>
            <table class="table table-striped" id="tdOrdersData" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Order #</th>
                        <th>Tracking #</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Items</th>
                        <th>Order placed</th>
                        <th>Order received</th>
                        <th>Notes</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
            </div>
        </div>
    </div>
</div>

<!-- add order modal -->
<div class="modal fade" id="addOrderModal" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-add-order">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrderModalLabel">Add new order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="addOrderModalMsg"></div>
            <div class="modal-body" id="addOrderModalBody">
                <!-- Content will be loaded here from the AJAX call -->
            </div>
        </div>
    </div>
</div>

<!-- update order modal -->
<div class="modal fade" id="updateOrderModal" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="updateOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <input type="hidden" name="id" id="id" />
            <div class="modal-header">
                <h5 class="modal-title" id="updateOrderModalLabel">Update Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="updateOrderModalMsg" class="mx-2"></div>
            <div class="modal-body" id="updateOrderModalBody">
                <!-- Content will be loaded here from the AJAX call -->
            </div>
        </div>
    </div>
</div>

<!-- view order modal -->
<div class="modal fade" id="viewOrderModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewOrderModalLabel">View Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="viewOrderModalMsg" class="mx-2"></div>
            <div class="modal-body" id="viewOrderModalBody">
                <!-- Content will be loaded here from the AJAX call -->
            </div>
        </div>
    </div>
</div>

<!-- re-order modal -->
<div class="modal fade" id="reOrderModal" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="reOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reOrderModalLabel">Re-order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="reOrderModalMsg" class="mx-2"></div>
            <div class="modal-body" id="reOrderModalBody">
                <!-- Content will be loaded here from the AJAX call -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';

    $('#mainTitle').click(function() {
        reload_data();
    });

    $('[data-toggle="tooltip"]').tooltip();
    var tdOrdersData = $('#tdOrdersData').DataTable({
        columnDefs: [
            { className: 'text-center', targets: '_all' },
            { orderable: false, targets: [9] }
        ],
        dom: 'lfrtip',
        processing: true,
        serverSide: false,
        searching: true,
        language: {
            loadingRecords: '&nbsp;',
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
            zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
            emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No orders yet</strong></div></div>',
            searchPlaceholder: 'Search by name...',
            search: ''
        },
        ajax: {
            url: '/core/list_orders_data.php',
            type: 'POST',
            dataType: 'json',
        },
        columns: [
            { data: 'supplier', title: 'Supplier', render: supplier },
            { data: 'order_id', title: 'Order #', render: order_id },
            { data: 'reference_number', title: 'Tracking #', render: reference_number },
            { data: 'status', title: 'Status', render: status },
            { data: 'total', title: 'Total', render: total },
            { data: 'items', title: 'Items', render: items },
            { data: 'placed', title: 'Order placed', render: placed },
            { data: 'received', title: 'Order received', render: received },
            { data: 'notes', title: 'Notes', render: notes },
            { data: null, title: '', render: actions },
        ],
        order: [[1, 'asc']],
        lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
        pageLength: 20,
        displayLength: 20,
        stateSave: true,
        stateDuration: -1,
        stateLoadCallback: function(settings, callback) {
            $.ajax({
                url: '/core/update_user_settings.php?set=listOrders&action=load',
                dataType: 'json',
                success: function(json) {
                    callback(json);
                }
            });
        },
        stateSaveCallback: function(settings, data) {
            $.ajax({
                url: "/core/update_user_settings.php?set=listOrders&action=save",
                data: data,
                dataType: "json",
                type: "POST"
            });
        },
        drawCallback: function( settings ) {
			extrasShow();
		},
        }).on('error.dt', function(e, settings, techNote, message) {
            var m = message.split(' - ');
            $('#tdOrdersData').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
    });

    function order_id(data, type, row) {
        return '<a href="#" data-bs-toggle="modal" data-bs-target="#viewOrderModal" id="viewOrder" data-id="' + row.id + '"><strong class="fw-bold">' + row.order_id + '</strong></a>';
    };

    function reference_number(data, type, row) {
        return '<a href="#" class="copy-to-clipboard" data-clipboard-text="' + row.reference_number + '">' + row.reference_number + '</a>';
    };

    function supplier(data, type, row) {
        return row.supplier;
    };

    function status(data, type, row) {
        if (row.status == 'pending') {
            return '<span class="badge bg-secondary">Pending</span>';
        } else if (row.status == 'processing') {
            return '<span class="badge bg-warning text-dark">Processing</span>';
        } else if (row.status == 'completed') {
            return '<span class="badge bg-success">Completed</span>';
        } else if (row.status == 'cancelled') {
            return '<span class="badge bg-danger">Cancelled</span>';
        } else {
            return '<span class="badge bg-secondary">Unknown</span>';
        }
    };

    function total(data, type, row) {
        return row.currency + row.total;
    };

    function items(data, type, row) {
        return row.items + ' items';
    };

    function placed(data, type, row) {
        const date = new Date(data);
        if (isNaN(date.getTime())) {
            return '-';
        }
        return date.toLocaleDateString(navigator.language || 'en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    };

    function received(data, type, row) {
        const date = new Date(data);
        if (isNaN(date.getTime())) {
            return '-';
        }
        return date.toLocaleDateString(navigator.language || 'en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    };

    function notes(data, type, row) {
        if (row.notes.length > 50) {
            return row.notes.substring(0, 50) + '...';
        } else {
            return row.notes;
        }
    };

    function actions(data, type, row) {
        data = '<div class="dropdown">' +
            '<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
            '<ul class="dropdown-menu dropdown-menu-right">';
        data += '<li><a class="dropdown-item pv_point_gen" data-bs-toggle="modal" data-bs-target="#viewOrderModal" id="viewOrder" data-id="' + row.id + '"><i class="fas fa-eye mx-2"></i>View</a></li>';
        data += '<li><a class="dropdown-item pv_point_gen" data-bs-toggle="modal" data-bs-target="#updateOrderModal" id="updateOrder" data-id="' + row.id + '"><i class="fas fa-sync-alt mx-2"></i>Update</a></li>';
        data += '<li><a class="dropdown-item pv_point_gen" data-bs-toggle="modal" data-bs-target="#reOrderModal" id="reOrder" data-id="' + row.id + '"><i class="fas fa-redo mx-2"></i>Re-order</a></li>';
        data += '<div class="dropdown-divider"></div>';
        data += '<li><a class="dropdown-item pv_point_gen text-danger" id="delete" data-order=' + row.order_id + ' data-id=' + row.id + '><i class="fas fa-trash mx-2"></i>Delete</a></li>';
        data += '</ul></div>';

        return data;
    };

    $('#tdOrdersData').on('click', '[id*=delete]', function() {
        var d = {};
        d.order_id = $(this).attr('data-id');
        d.order = $(this).attr('data-order');

        bootbox.dialog({
            title: "Confirm deletion",
            message: 'Delete order <strong>' + d.order + '</strong> ?',
            buttons: {
                main: {
                    label: "Delete",
                    className: "btn-danger",
                    callback: function() {

                        $.ajax({
                            url: '/core/core.php',
                            type: 'GET',
                            data: {
                                action: 'deleteorder',
                                order_id: d.order_id,
                            },
                            dataType: 'json',
                            success: function(data) {
                                if (data.success) {
                                    $('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
                                    $('.toast-header').removeClass().addClass('toast-header alert-success');
                                    reload_data();
                                } else if (data.error) {
                                    $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error);
                                    $('.toast-header').removeClass().addClass('toast-header alert-danger');
                                }
                                $('.toast').toast('show');
                            },
                            error: function(xhr, status, error) {
                                $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. ' + error);
                                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                                $('.toast').toast('show');
                            }
                        });

                        return true;
                    }
                },
                cancel: {
                    label: "Cancel",
                    className: "btn-secondary",
                    callback: function() {
                        return true;
                    }
                }
            },
            onEscape: function () {
                return true;
            }
        });
    });

    function reload_data() {
        $('#tdOrdersData').DataTable().ajax.reload(null, true);
    };

    $('#addOrder').click(function() {
        $('#addOrderModal').modal('show');
        $('#addOrderModalBody').html('Loading...');
        $('#addOrderModalMsg').html('');
        $.ajax({
            url: '/pages/views/inventory/add_order.php',
            type: 'GET',
            success: function(data) {
                $('#addOrderModalBody').html(data);
            },
            error: function(xhr, status, error) {
                $('#addOrderModalBody').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred while loading the form. ' + error + '</div>');
            }
        });
    });


    function extrasShow(){
        $('#tdOrdersData').on('click', '#updateOrder', function() {
            var order_id = $(this).data('id');
            $('#updateOrderModalMsg').html('');
            $('#updateOrderModal #order_id').val(order_id);
            $('#updateOrderModal').modal('show');
            $('#updateOrderModalLabel').html('Update Order');
            $('#updateOrderModalBody').html('Loading...');
            $.ajax({
                url: '/pages/views/inventory/update_order.php',
                type: 'GET',
                data: {
                    order_id: order_id
                },
                success: function(data) {
                    $('#updateOrderModalBody').html(data);
                },
                error: function(xhr, status, error) {
                    $('#updateOrderModalBody').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred while loading order data. ' + error + '</div>');
                }
            });
        });

        $('#tdOrdersData').on('click', '#viewOrder', function() {
            var order_id = $(this).data('id');
            $('#viewOrderModalMsg').html('');
            $('#viewOrderModal').modal('show');
            $('#viewOrderModalLabel').html('View Order');
            $('#viewOrderModalBody').html('Loading...');
            $.ajax({
                url: '/pages/views/inventory/view_order.php',
                type: 'GET',
                data: {
                    order_id: order_id
                },
                success: function(data) {
                    $('#viewOrderModalBody').html(data);
                },
                error: function(xhr, status, error) {
                    $('#viewOrderModalBody').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred while loading order data. ' + error + '</div>');
                }
            });
        });

        $('#tdOrdersData').on('click', '#reOrder', function() {
            var order_id = $(this).data('id');
            $('#reOrderModalMsg').html('');
            $('#reOrderModal').modal('show');
            $('#reOrderModalLabel').html('Re-order');
            $('#reOrderModalBody').html('Loading...');
            $.ajax({
                url: '/pages/views/inventory/re_order.php',
                type: 'GET',
                data: {
                    order_id: order_id
                },
                success: function(data) {
                    $('#reOrderModalBody').html(data);
                },
                error: function(xhr, status, error) {
                    $('#reOrderModalBody').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred while loading order data. ' + error + '</div>');
                }
            });
        });

        $('#tdOrdersData').on('click', '.copy-to-clipboard', function() {
            var clipboardText = $(this).data('clipboard-text');
            navigator.clipboard.writeText(clipboardText).then(function() {
                $('.toast').toast('show');
                $('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>Copied to clipboard');
                $('.toast-header').removeClass().addClass('toast-header alert-success');
            }, function(err) {
                $('.toast').toast('show');
                $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>Failed to copy');
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
            });
        });
    }

});
</script>
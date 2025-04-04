<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php'); 
require_once(__ROOT__.'/func/php-settings.php');
?>
     <div class="container-fluid">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle">Suppliers</a></h2>
            </div>
             <div class="card-body">
                <div class="text-right">
                  <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                      <div class="dropdown-menu">
                          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addSupplier"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
                          <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
                        <li><a class="dropdown-item" id="json_export" href="/pages/export.php?format=json&kind=suppliers"><i class="fa-solid fa-file-code mx-2"></i>Export to JSON</a></li>
                       <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importJSON"><i class="fa-solid fa-file-import mx-2"></i>Import from JSON</a></li>
                      </div>
                    </div>        
                 </div>
                <table class="table table-striped" id="tdIngSupData" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Materials</th>
                      <th>Platform</th>
                      <th>Price start tag</th>
                      <th>Price end tag</th>
                      <th>Additional costs</th>
                      <th>Price per</th>
                      <th>Min ml</th>
                      <th>Min grams</th>
                      <th>Description</th>
                      <th></th>
                    </tr>
                  </thead>
                </table>
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
    var tdIngSupData = $('#tdIngSupData').DataTable( {
        columnDefs: [
            { className: 'text-center', targets: '_all' },
            { orderable: false, targets: [10]}
        ],
        dom: 'lfrtip',
        buttons: [{
            extend: 'csvHtml5',
            title: "Suppliers",
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
            },
        }],
        processing: true,
        serverSide: false,
        searching: true,
        language: {
            loadingRecords: '&nbsp;',
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
            zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
            emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No suppliers added yet</strong></div></div>',
            searchPlaceholder: 'Search by name...',
            search: ''
        },
        ajax: {	
            url: '/core/list_suppliers_data.php',
            type: 'POST',
            dataType: 'json',
        },
        columns: [
          { data : 'name', title: 'Name', render: name },
          { data : 'materials', title: 'Materials', render: materials },
          { data : 'platform', title: 'Platform', render: platform},
          { data : 'price_tag_start', title: 'Price start tag', render: price_tag_start},
          { data : 'price_tag_end', title: 'Price end tag', render: price_tag_end},
          { data : 'add_costs', title: 'Additional costs', render: add_costs},
          { data : 'price_per_size', title: 'Price per size', render: price_per_size},
          { data : 'min_ml', title: 'Min ml', render: min_ml},
          { data : 'min_gr', title: 'Min grams', render: min_gr},
          { data : 'description', title: 'Description', render: description},
    
          { data : null, title: '', render: actions},		   
        ],
        order: [[ 1, 'asc' ]],
        lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
        pageLength: 20,
        displayLength: 20,
        stateSave: true,
        stateDuration: -1,
        stateLoadCallback: function (settings, callback) {
            $.ajax( {
                url: '/core/update_user_settings.php?set=listSuppliers&action=load',
                dataType: 'json',
                success: function (json) {
                    callback( json );
                }
            });
        },
        stateSaveCallback: function (settings, data) {
           $.ajax({
             url: "/core/update_user_settings.php?set=listSuppliers&action=save",
             data: data,
             dataType: "json",
             type: "POST"
          });
        },
    }).on('error.dt', function(e, settings, techNote, message) {
        var m = message.split(' - ');
        $('#tdIngSupData').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
    });


    function name(data, type, row){
        return '<i class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</i>';    
    };
    
    function materials(data, type, row){
        
        var data = '<a class="pv_point_gen" href="#" data-bs-toggle="modal" data-bs-target="#supplier_materials" data-id="' + row.id + '" data-name="' + row.name + '">' + row.materials + '</a></li>';
        return data;    
    };
    
    function platform(data, type, row){
        return '<i class="platform pv_point_gen" data-name="platform" data-type="select" data-pk="'+row.id+'">'+row.platform+'</i>';
    };
    
    function price_tag_start(data, type, row){
        return '<i class="price_tag_start pv_point_gen" data-name="price_tag_start" data-type="textarea" data-pk="'+row.id+'">'+atob(row.price_tag_start)+'</i>';    
    };
    
    function price_tag_end(data, type, row){
        return '<i class="price_tag_end pv_point_gen" data-name="price_tag_end" data-type="textarea" data-pk="'+row.id+'">'+atob(row.price_tag_end)+'</i>';    
    };
    
    function add_costs(data, type, row){
        return '<i class="add_costs pv_point_gen" data-name="add_costs" data-type="text" data-pk="'+row.id+'">'+row.add_costs+'</i>';    
    };
    
    function price_per_size(data, type, row){
        return '<i class="price_per_size pv_point_gen" data-name="price_per_size" data-type="select" data-pk="'+row.id+'">'+row.price_per_size+'</i>';    
    };
    
    function min_ml(data, type, row){
        return '<i class="min_ml pv_point_gen" data-name="min_ml" data-type="text" data-pk="'+row.id+'">'+row.min_ml+'</i>';
    };
    
    function min_gr(data, type, row){
        return '<i class="min_gr pv_point_gen" data-name="min_gr" data-type="text" data-pk="'+row.id+'">'+row.min_gr+'</i>';
    };

    function description(data, type, row){
        return '<i class="notes pv_point_gen" data-name="notes" data-type="textarea" data-pk="'+row.id+'">'+row.notes+'</i>';    
    };
    
    function actions(data, type, row){
        data = '<div class="dropdown">' +
                '<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
                    '<ul class="dropdown-menu dropdown-menu-right">';
        data += '<li><i class="pv_point_gen dropdown-item" data-bs-toggle="modal" data-bs-target="#details" id="edit_supplier" data-id="' + row.id + '" data-name="' + row.name + '" data-address="'+row.address+'" data-po="'+row.po+'" data-country="'+row.country+'" data-telephone="'+row.telephone+'" data-url="'+row.url+'" data-email="'+row.email+'" data-currency="'+row.currency+'"><i class="fas fa-edit mx-2"></i>Edit</i></li>';
        data += '<li><a class="dropdown-item" id="json_export" href="/pages/export.php?format=json&kind=supplier-materials&supplier-name='+row.name+'&id='+row.id+'"><i class="fa-solid fa-file-code mx-2"></i>Export materials to JSON</a></li>';
        data += '<div class="dropdown-divider"></div>';
        data += '<li><a class="dropdown-item pv_point_gen text-danger" id="dDel" data-name="'+ row.name +'" data-id='+ row.id +'><i class="fas fa-trash mx-2"></i>Delete</a></li>';
        data += '</ul></div>';
        
        return data;
    };
    
    $('#tdIngSupData').editable({
        container: 'body',
      	selector: 'i.name',
      	url: "/core/core.php?action=supplier_update&kind=suppliers",
     	title: 'Supplier',
        ajaxOptions: {
            type: "POST",
            dataType: 'json'
        },
        success: function (data) {
            if ( data.success ) {
                reload_data();
            } else if ( data.error ) {
                $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
        },
        error: function (xhr, status, error) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
        },
      	validate: function(value){
       		if($.trim(value) == ''){
                return 'This field is required';
       		}
      	}
    });
    
    $('#tdIngSupData').editable({
        container: 'body',
        selector: 'i.platform',
        url: "/core/core.php?action=supplier_update&kind=suppliers",
        source: [
            {value: "woocomerce", text: "Woocomerce"},
            {value: "shopify", text: "Shopify"},
            {value: "other", text: "Custom/Other"},
        ],
        title: 'Supplier platform',
        ajaxOptions: {
            type: "POST",
            dataType: 'json'
        },
        success: function (data) {
            if ( data.success ) {
                reload_data();
            } else if ( data.error ) {
                $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
        },
        error: function (xhr, status, error) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
        }
    });
    
    $('#tdIngSupData').editable({
        container: 'body',
        selector: 'i.price_per_size',
        url: "/core/core.php?action=supplier_update&kind=suppliers",
        source: [
             {value: "0", text: "Product size"},
             {value: "1", text: "Volume size"},
        ],
        title: 'Price per',
        ajaxOptions: {
            type: "POST",
            dataType: 'json'
        },
        success: function (data) {
            if ( data.success ) {
                reload_data();
            } else if ( data.error ) {
                $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
        },
        error: function (xhr, status, error) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
        }
    });
    
    $('#tdIngSupData').editable({
      	container: 'body',
      	selector: 'i.min_ml',
      	url: "/core/core.php?action=supplier_update&kind=suppliers",
      	title: 'Minimum ml',
      	ajaxOptions: {
            type: "POST",
            dataType: 'json'
        },
        success: function (data) {
            if ( data.success ) {
                reload_data();
            } else if ( data.error ) {
                $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
        },
        error: function (xhr, status, error) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
        },
        validate: function(value){
            if($.trim(value) == ''){
                return 'This field cannot be empty, set to 0 for none';
            }
            if($.isNumeric(value) == '' ){
                return 'Numbers only';
            }
        }
    });
    
    $('#tdIngSupData').editable({
      	container: 'body',
      	selector: 'i.min_gr',
      	url: "/core/core.php?action=supplier_update&kind=suppliers",
      	title: 'Minimum grams',
      	  	ajaxOptions: {
            type: "POST",
            dataType: 'json'
        },
        success: function (data) {
            if ( data.success ) {
                reload_data();
            } else if ( data.error ) {
                $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
        },
        error: function (xhr, status, error) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
        },
      	validate: function(value){
            if($.trim(value) == ''){
                return 'This field cannot be empty, set 0 for none';
            }
            if($.isNumeric(value) == '' ){
                return 'Numbers only!';
            }
      	}
    });
    
    $('#tdIngSupData').editable({
        container: 'body',
      	selector: 'i.price_tag_start',
      	url: "/core/core.php?action=supplier_update&kind=suppliers",
      	title: 'Price tag start',
        	ajaxOptions: {
            type: "POST",
            dataType: 'json'
        },
        success: function (data) {
            if ( data.success ) {
                reload_data();
            } else if ( data.error ) {
                $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
        },
        error: function (xhr, status, error) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
        }
    });
    
    $('#tdIngSupData').editable({
        container: 'body',
      	selector: 'i.price_tag_end',
      	url: "/core/core.php?action=supplier_update&kind=suppliers",
      	title: 'Price tag end',
      	  	ajaxOptions: {
            type: "POST",
            dataType: 'json'
        },
        success: function (data) {
            if ( data.success ) {
                reload_data();
            } else if ( data.error ) {
                $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
        },
        error: function (xhr, status, error) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
        }
    });
    
    $('#tdIngSupData').editable({
        container: 'body',
      	selector: 'i.add_costs',
      	url: "/core/core.php?action=supplier_update&kind=suppliers",
      	title: 'Additional Costs',
      	  	ajaxOptions: {
            type: "POST",
            dataType: 'json'
        },
        success: function (data) {
            if ( data.success ) {
                reload_data();
            } else if ( data.error ) {
                $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
        },
        error: function (xhr, status, error) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
        },
      	validate: function(value){
            if($.trim(value) == ''){
                return 'This field cannot be empty, set to 0 for none';
            }
            if($.isNumeric(value) == '' ){
                return 'Numbers only';
            }
      	}
    });
    
    $('#tdIngSupData').editable({
        container: 'body',
      	selector: 'i.notes',
      	url: "/core/core.php?action=supplier_update&kind=suppliers",
      	title: 'Description',
        	ajaxOptions: {
            type: "POST",
            dataType: 'json'
        },
        success: function (data) {
            if ( data.success ) {
                reload_data();
            } else if ( data.error ) {
                $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
        },
        error: function (xhr, status, error) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
        }
    });
    
        
    $('#tdIngSupData').on('click', '[id*=dDel]', function () {
        var d = {};
        d.ID = $(this).attr('data-id');
        d.Name = $(this).attr('data-name');
    
        bootbox.dialog({
           title: "Confirm deletion",
           message : 'Delete supplier <strong>'+ d.Name +'</strong> ?',
           buttons :{
               main: {
                   label : "Delete",
                   className : "btn-danger",
                   callback: function (){
                        
                    $.ajax({ 
                        url: '/core/core.php', 
                        type: 'GET',
                        data: {
                            supp: 'delete',
                            ID: d.ID,
                        },
                        dataType: 'json',
                        success: function (data) {
                            if(data.success){
                                $('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
                                $('.toast-header').removeClass().addClass('toast-header alert-success');
                                reload_data();
                            }else if(data.error){
                                $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error);
                                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                            }
                            $('.toast').toast('show');
                        },
                        error: function (xhr, status, error) {
                            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error);
                            $('.toast-header').removeClass().addClass('toast-header alert-danger');
                            $('.toast').toast('show');
                        }
                      });
                    
                     return true;
                   }
               },
               cancel: {
                   label : "Cancel",
                   className : "btn-secondary",
                   callback : function() {
                       return true;
                   }
               }   
           },onEscape: function () {return true;}
       });
    });
    
    
    $('#btnAddSupplier').on('click', function () {
        // Validate form fields
        var isValid = true;
		var currency = $("#add_supplier #currency").val();
		var currencyData = currency.split('|');

        $('#add_supplier input[required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            $('#inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Please fill in all required fields.</div>');
            return;
        }

        $.ajax({ 
            url: '/core/core.php', 
            type: 'POST',
            data: {
                action: 'addsupplier',
                name: $("#add_supplier #name").val(),
                address: $("#add_supplier #address").val(),
                po: $("#add_supplier #po").val(),
                country: $("#add_supplier #country").val(),
            	currency: currencyData[1], // Code
                telephone: $("#add_supplier #telephone").val(),
                website: $("#add_supplier #website").val(),
                email: $("#add_supplier #email").val(),
                platform: $("#add_supplier #platform").val(),
                price_tag_start: $("#add_supplier #price_tag_start").val(),
                price_tag_end: $("#add_supplier #price_tag_end").val(),
                add_costs: $("#add_supplier #add_costs").val(),
                description: $("#add_supplier #description").val(),
                min_ml: $("#add_supplier #min_ml").val(),
                min_gr: $("#add_supplier #min_gr").val()
            },
            dataType: 'json',
            success: function (data) {
                if(data.success){
					$('#inf').html('<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check mx-2"></i><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>');
                    // Reset form fields
                    $('#add_supplier input').val('');
                    $('#add_supplier select').val('');
                    reload_data();
                } else if(data.error){
                    $('#inf').html('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation mx-2"></i><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>');
                }
            },
            error: function (xhr, status, error) {
                $('#inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
            }
        });
    });

    function reload_data() {
        $('#tdIngSupData').DataTable().ajax.reload(null, true);
    }
	
	$('#btnEditSupplier').on('click', function () {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				action: 'editsupplier',
				id: $("#id").val(),
				name: $("#name").val(),
				address: $("#address").val(),
				po: $("#po").val(),
				country: $("#country").val(),
            	currency: $("#currency").val(),
				telephone: $("#telephone").val(),
				url: $("#url").val(),
				email: $("#email").val(),
			},
			dataType: 'json',
			success: function (data) {
				if(data.success){			
					msg = '<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check mx-2"></i><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
					reload_data();
				}else if(data.error){
					msg = '<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation mx-2"></i><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				}
				$('#editSup').html(msg);
			},
			error: function (xhr, status, error) {
				$('#editSup').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
			}
	  });
	});
	
	
	function reload_data() {
		$('#tdIngSupData').DataTable().ajax.reload(null, true);
	};

	$('#tdIngSupData').on('click', '[id*=edit_supplier]', function () {
        
        $(".modal-body div span").text("");
        $(".modal-title").text($(this).data().name);
        $("#address").val($(this).data().address);
		$("#po").val($(this).data().po);
		$("#country").val($(this).data().country);
		$("#currency").val($(this).data().currency);
        $("#telephone").val($(this).data().telephone);
        $("#url").val($(this).data().url);
        $("#email").val($(this).data().email);
        $("#id").val($(this).data().id);
        $("#name").val($(this).data().name);

        $("#details").modal("show");
	});
	
	$('#exportCSV').click(() => {
		$('#tdIngSupData').DataTable().button(0).trigger();
	});
	
	$("#supplier_materials").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get('/pages/views/inventory/supplierMaterials.php?id=' + id +' &name=' + name)
			.then(data => {
				$(".modal-body", this).html(data);
		});
	});
});
</script>

<!-- REQUIRED MATERIALS MODAL -->
<div class="modal fade" id="supplier_materials" data-bs-backdrop="static" tabindex="-1" aria-labelledby="supplier_materials" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">All supplier materials</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">Please wait...</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- Edit additional info -->
<div class="modal fade" id="details" data-bs-backdrop="static" tabindex="-1" aria-labelledby="detailsLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="detailsLabel">Supplier Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="edit_supplier">
				<div id="editSup"></div>
				<div class="container-fluid">
					<div class="col-sm-12">
						<input type="hidden" name="id" id="id" />
						<input type="hidden" name="name" id="name" />
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<input class="form-control" name="address" type="text" id="address" placeholder="Address" />
								<label for="address" class="form-label mx-2">Address</label>
							</div>
							<div class="form-floating col-md-6">
								<input class="form-control" name="po" type="text" id="po" placeholder="Postal Code" />
								<label for="po" class="form-label mx-2">Postal Code</label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<select class="form-select" name="country" id="country">
									<option value="">Choose your country</option>
									<?php foreach ($countries as $country): ?>
										<option value="<?php echo htmlspecialchars($country['name']); ?>">
											<?php echo htmlspecialchars($country['name']); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<label for="country" class="form-label mx-2">Country</label>
							</div>
							<div class="form-floating col-md-6">
								<select name="currency" id="currency" class="form-select">
									<option value="">Choose currency</option>
									<?php
									$json = file_get_contents(__ROOT__.'/db/currencies.json');
									$currencies = json_decode($json, true);
									foreach ($currencies as $code => $details) {
										$symbol = $details['symbol'];
										$name = $details['name'];
										echo "<option value=\"$code\">$name ($symbol) [$code]</option>";
									}
									?>
								</select>
								<label for="currency" class="form-label mx-2"><strong>Currency</strong></label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<input class="form-control" name="telephone" type="text" id="telephone" placeholder="Telephone" />
								<label for="telephone" class="form-label mx-2">Telephone</label>
							</div>
							<div class="form-floating col-md-6">
								<input class="form-control" name="url" type="text" id="url" placeholder="Website" />
								<label for="url" class="form-label mx-2">Website</label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<input class="form-control" name="email" type="text" id="email" placeholder="Email" />
								<label for="email" class="form-label mx-2">Email</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<input type="submit" name="button" class="btn btn-primary" id="btnEditSupplier" value="Update">
			</div>
		</div>
	</div>
</div>

<!--IMPORT JSON MODAL-->
<div class="modal fade" id="importJSON" data-bs-backdrop="static" tabindex="-1" aria-labelledby="importJSONLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importJSONLabel">Import suppliers from a JSON file</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="JSRestMsg"></div>
        <div class="progress mb-3">  
          <div id="uploadProgressBar" class="progress-bar" role="progressbar" aria-valuemin="0"></div>
        </div>
        <div id="backupArea">
          <div class="form-group row mb-3">
            <label for="jsonFile" class="col-md-3 col-form-label">JSON file</label>
            <div class="col-md-8">
              <input type="file" name="jsonFile" id="jsonFile" class="form-control" />
            </div>
          </div>
          <div class="col-md-12">
            <hr />
            <div class="alert alert-info">
                <p><strong>IMPORTANT:</strong></p>
                <ul>
                  <li>
                    <div id="raw" data-size="<?=getMaximumFileUploadSizeRaw()?>">Maximum file size: <strong><?=getMaximumFileUploadSize()?></strong></div>
                  </li>
                  <li>Any supplier with a name that already exists, will be updated.</li>
                </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK">Close</button>
        <input type="submit" name="btnRestore" class="btn btn-primary" id="btnImportSuppliers" value="Import">
      </div>
    </div>  
  </div>
</div>

<!-- ADD NEW-->
<div class="modal fade" id="addSupplier" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addSupplierLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addSupplierLabel">Add Supplier</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="add_supplier">
				<div id="inf"></div>
				<div class="container-fluid">
					<div class="col-sm-12">
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<input class="form-control" name="name" type="text" id="name" placeholder="Name" required />
								<label for="name" class="form-label mx-2">Name</label>
							</div>
							<div class="form-floating col-md-6">
								<input class="form-control" name="address" type="text" id="address" placeholder="Address" required />
								<label for="address" class="form-label mx-2">Address</label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<input class="form-control" name="po" type="text" id="po" placeholder="Postal Code" required />
								<label for="po" class="form-label mx-2">Postal Code</label>
							</div>
							<div class="form-floating col-md-6">
								<select class="form-select" name="country" id="country">
									<option value="">Choose your country</option>
									<?php foreach ($countries as $country): ?>
										<option value="<?php echo htmlspecialchars($country['name']); ?>" <?php echo $user['country'] == $country['isoAlpha2'] ? 'selected' : ''; ?>>
											<?php echo htmlspecialchars($country['name']); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<label for="country" class="form-label mx-2">Country</label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<select name="currency" id="currency" class="form-select">
									<?php
									$json = file_get_contents(__ROOT__.'/db/currencies.json');
									$currencies = json_decode($json, true);
									foreach ($currencies as $code => $details) {
										$symbol = $details['symbol'];
										$selected = ($user_settings['currency_code'] == $code) ? 'selected' : '';
										$name = $details['name'];
										echo "<option value=\"$symbol|$code\" $selected>$name ($symbol) [$code]</option>";
									}
									?>
								</select>
								<label for="currency" class="form-label mx-2"><strong>Currency</strong></label>
							</div>
							<div class="form-floating col-md-6">
								<input class="form-control" name="telephone" type="text" id="telephone" placeholder="Telephone" required />
								<label for="telephone" class="form-label mx-2">Telephone</label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<input class="form-control" name="website" type="text" id="website" placeholder="Website" required />
								<label for="website" class="form-label mx-2">Website</label>
							</div>
							<div class="form-floating col-md-6">
								<input class="form-control" name="email" type="email" id="email" placeholder="Email" required />
								<label for="email" class="form-label mx-2">Email</label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<select class="form-control" name="platform" id="platform" aria-label="Platform" required>
									<option value="woocommerce">Woocommerce</option>
									<option value="shopify">Shopify</option>
									<option value="other">Other/Custom</option>
								</select>
								<label for="platform" class="form-label mx-2">Platform</label>
							</div>
							<div class="form-floating col-md-6">
								<input class="form-control" name="price_tag_start" type="text" id="price_tag_start" placeholder="Price Start Tag" />
								<label for="price_tag_start" class="form-label mx-2">Price Start Tag</label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<input class="form-control" name="price_tag_end" type="text" id="price_tag_end" placeholder="Price End Tag" />
								<label for="price_tag_end" class="form-label mx-2">Price End Tag</label>
							</div>
							<div class="form-floating col-md-6">
								<input class="form-control" name="add_costs" type="text" id="add_costs" placeholder="Additional Costs" />
								<label for="add_costs" class="form-label mx-2">Additional Costs</label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<input class="form-control" name="min_ml" type="text" id="min_ml" placeholder="Minimum Quantity (ml)" required />
								<label for="min_ml" class="form-label mx-2">Minimum Quantity (ml)</label>
							</div>
							<div class="form-floating col-md-6">
								<input class="form-control" name="min_gr" type="text" id="min_gr" placeholder="Minimum Quantity (grams)" required />
								<label for="min_gr" class="form-label mx-2">Minimum Quantity (grams)</label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="form-floating col-md-6">
								<select class="form-control" name="price_per_size" id="price_per_size" aria-label="Price to be Calculated Per">
									<option value="0">Product</option>
									<option value="1">Volume</option>
								</select>
								<label for="price_per_size" class="form-label mx-2">Price to be Calculated Per</label>
							</div>
							<div class="form-floating col-md-6">
								<input class="form-control" name="description" type="text" id="description" placeholder="Description" />
								<label for="description" class="form-label mx-2">Description</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<input type="submit" name="button" class="btn btn-primary" id="btnAddSupplier" value="Add">
			</div>
		</div>
	</div>
</div>
<script src="/js/import.suppliers.js"></script>


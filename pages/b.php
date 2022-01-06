<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('..//inc/opendb.php');
require_once('../inc/settings.php');
//require_once('../func/pvFileGet.php');

if(!$_GET['id']){
	echo 'Formula id is missing.';
	return;
}
	
$id = mysqli_real_escape_string($conn, $_GET['id']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE id = '$id'")) == 0){
	echo '<div class="alert alert-info alert-dismissible">Incomplete formula. Please add ingredients.</div>';
	return;
}
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,fid,name,isProtected FROM formulasMetaData WHERE id = '$id'"));
$f_name = base64_decode($meta['fid']);


?>
<link href="../css/fontawesome-free/css/all.min.css" rel="stylesheet">


<script src="../js/jquery/jquery.min.js"></script>

<link rel="stylesheet" href="../css/bootstrap.min.css">
<script src="../js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/r/dt/dt-1.10.22/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/r/dt/dt-1.10.22/datatables.min.js"></script>
<link rel="stylesheet" href="../css/vault.css">
<script src="../js/magnific-popup.js"></script>
<link href="../css/magnific-popup.css" rel="stylesheet" />
<link href="../css/bootstrap-editable.css" rel="stylesheet">
<script type="text/javascript" src="../js/bootbox.min.js"></script>
<script src="../js/bootstrap-editable.js"></script>

<script>

$(document).ready(function() {
  $('#formula').editable({
	  container: 'body',
	  selector: 'a.concentration',
	  url: "/pages/update_data.php?formula=<?=$meta['fid']?>",
	  title: 'Purity %',
	  type: "POST",
	  dataType: 'json',
			success: function(response, newValue) {
			if(response.status == 'error'){
				return response.msg; 
			}else{
				re();
			}
		},
	  validate: function(value){
	   if($.trim(value) == ''){
		return 'This field is required';
	   }
	   if($.isNumeric(value) == '' ){
		return 'Numbers only!';
	   }
	  }
	});
	
	$('#formula').editable({
		container: 'body',
		selector: 'a.solvent',
		type: 'POST',
		emptytext: "",
		emptyclass: "",
		url: "/pages/update_data.php?formula=<?=$meta['fid']?>",
		source: [
				 <?php
					$res_ing = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
					while ($r_ing = mysqli_fetch_array($res_ing)){
					echo '{value: "'.$r_ing['name'].'", text: "'.$r_ing['name'].'"},';
				}
				?>
			  ],
		dataType: 'json',
		success: function(response, newValue) {
			if(response.status == 'error'){
				return response.msg; 
			}else{
				re();
			}
		}
    
	});
	
	$('#formula').editable({
	  container: 'body',
	  selector: 'a.quantity',
	  url: "/pages/update_data.php?formula=<?=$meta['fid']?>",
	  title: 'ml',
	  type: "POST",
	  dataType: 'json',
		  success: function(response, newValue) {
			if(response.status == 'error'){
				return response.msg; 
			}else{ 
				re();
			}
		},
	  validate: function(value){
	   if($.trim(value) == ''){
		return 'This field is required';
	   }
	   if($.isNumeric(value) == '' ){
		return 'Numbers only!';
	   }
	  }
    });
	
	var groupColumn = 0;
	function ingName(data, type, row, meta){
		$('.popup-link').magnificPopup({
			type: 'iframe',
			closeOnContentClick: false,
			closeOnBgClick: false,
			showCloseBtn: true,
		});
		if(row.chk_ingredient){
			var chkIng = row.chk_ingredient;
		}else{
			var chkIng = '';
		}
		if(type === 'display'){
			data = '<a class="popup-link" href="/pages/mgmIngredient.php?id=' + row.enc_id + '">' + data + '</a> '+ chkIng;
		}

  	  return data;
  }

	function ingConc(data, type, row, meta){
	  if(type === 'display'){
		  data = '<a href="#" data-name="concentration" class="concentration" data-type="text" data-pk="' + row.ingredient + '">' + data + '</a>';
	  }

  	  return data;
  	}
  
  function ingSolvent(data, type, row, meta){
	  if(type === 'display'){
		  if(row.purity !== 100){
		  	data = '<a href="#" data-name="dilutant" class="solvent" data-type="select" data-pk="' + row.ingredient + '">' + data + '</a>';
	  	}else{
			data = 'None';
		}
	  }
  	  return data;
  }
  
  function ingQuantity(data, type, row, meta){
	if(type === 'display'){
		data = '<a href="#" data-name="quantity" class="quantity" data-type="text" data-pk="' + row.ingredient + '">' + data + '</a>';
	}
    return data;
  }

  function ingActions(data, type, row, meta){
	//Change ingredient
	$('#formula').editable({
		selector: 'a.replaceIngredient',
		pvnoresp: false,
		highlight: false,
		type: 'get',
		emptytext: "",
		emptyclass: "",
		url: "/pages/manageFormula.php?action=repIng&fname=<?=$meta['name']?>",
		source: [
				 <?php
					$res_ing = mysqli_query($conn, "SELECT name FROM ingredients ORDER BY name ASC");
					while ($r_ing = mysqli_fetch_array($res_ing)){
						echo '{value: "'.htmlspecialchars($r_ing['name']).'", text: "'.htmlspecialchars($r_ing['name']).'"},';
				}
				?>
			  ],
		dataType: 'html',
		success: function (data) {
			if ( data.indexOf("Error") > -1 ) {
				$('#msgInfo').html(data); 
			}else{
				$('#msgInfo').html(data);
				re();
			}
		}
	});

	if(type === 'display'){
		data = '<a href="'+ row.pref_supplier_link +'" target="_blank" class="fas fa-shopping-cart"></a>';
		<?php if($meta['isProtected'] == FALSE){?>
		data += '&nbsp; <a href="#" class="fas fa-exchange-alt replaceIngredient" rel="tipsy" title="Replace '+ row.ingredient +'"  data-name="'+ row.ingredient +'" data-type="select" data-pk="'+ row.ingredient +'" data-title="Choose Ingredient to replace '+ row.ingredient +'"></a> &nbsp; <a href="#" class="fas fa-trash" id="rmIng" data-name="'+ row.ingredient +'" data-id='+ row.formula_ingredient_id +'></a>';
		<?php } ?>
	}
    return data;
  }
  var table = $('#formula').DataTable( {
		"columnDefs": [
            { visible: false, targets: groupColumn },
			{ className: 'text-center', targets: '_all' },
        ],
		"processing": true,
        "language": {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
			},
    	ajax: {
    		url: '/core/data.php?id=<?=$id?>'
 		 },
		 columns: [
				   { data : 'profile', title: 'Profile' },
				   { data : 'ingredient', title: 'Ingredient', render: ingName},
    			   { data : 'cas', title: 'CAS'},
				   { data : 'purity', title: 'Purity', render: ingConc},
				   { data : 'dilutant', title: 'Dilutant', render: ingSolvent},
				   { data : 'quantity', title: 'Quantity', render: ingQuantity},
				   { data : 'concentration', title: 'Concentration %'},
				   { data : 'final_concentration', title: 'Final Concentration %'},
				   { data : 'cost', title: 'Cost'},
				   { data : 'desc', title: 'Properties'},
   				   { data : null, title: 'Actions', render: ingActions},		   
				   
				  ],

  		footerCallback : function( tfoot, data, start, end, display ) {    
      
		  var response = this.api().ajax.json();
		  if(response){
			 var $td = $(tfoot).find('th');
			 $td.eq(0).html("Total Ingredients: " + response.meta['total_ingredients'] );
			 $td.eq(4).html("Total: " + response.meta['total_quantity'] + response.meta['quantity_unit'] );
			 $td.eq(5).html("Total: " + response.meta['concentration'] + "%" );
			 $td.eq(7).html("Total: " + response.meta['currency'] + response.meta['total_cost'] );
		 }
      },
	  
        "order": [[ groupColumn, 'desc' ]],
        "pageLength": 100,
		"displayLength": 100,
		"createdRow": function( row, data, dataIndex){
			if( data['usage_regulator'] == "IFRA" && parseFloat(data['usage_limit']) < parseFloat(data['concentration'])){
				$(row).find('td:eq(5)').addClass('alert-danger').append(' <a href="#" rel="tipsy" title="Max usage: ' + data['usage_limit'] +'%" class="fas fa-info-circle"></a></div>');
			}else if( data['usage_regulator'] == "PV" && parseFloat(data['usage_limit']) < parseFloat(data['concentration'])){
				$(row).find('td:eq(5)').addClass('alert-info');
            }else{
				$(row).find('td:eq(5)').addClass('alert-success');
			}
			
			if( data['usage_regulator'] == "IFRA" && parseFloat(data['usage_limit']) < parseFloat(data['final_concentration'])){
				$(row).find('td:eq(6)').addClass('alert-danger');
			}else if( data['usage_regulator'] == "PV" && parseFloat(data['usage_limit']) < parseFloat(data['final_concentration'])){
				$(row).find('td:eq(6)').addClass('alert-info');
			}else{
				$(row).find('td:eq(6)').addClass('alert-success');
			}
       },
	   "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last = null;
 
            api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group noexport"><td colspan="' + rows.columns()[0].length +'">' + group + ' Notes</td></tr>'
                    );
                    last = group;
                }
            } );
	   }
});

$('#formula').on('click', '[id*=rmIng]', function () {
	var ing = {};
	ing.ID = $(this).attr('data-id');
	ing.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm ingredient removal",
       message : 'Remove <strong>'+ $(this).attr('data-name') +'</strong> from formula?',
       buttons :{
           main: {
               label : "Remove",
               className : "btn-primary",
               callback: function (){
	    			
				$.ajax({ 
					url: '/pages/manageFormula.php', 
					type: 'GET',
					data: {
						action: "deleteIng",
						fname: "<?=$meta['name']?>",
						ingID: ing.ID,
						ing: ing.Name
						},
					dataType: 'html',
					success: function (data) {
						$('#msgInfo').html(data);
						re();
					}
				  });
                 return true;
               }
           },
           cancel: {
               label : "Cancel",
               className : "btn-default",
               callback : function() {
                   return true;
               }
           }   
       },onEscape: function () {return true;}
   });
});
	
	update_bar();
	
});//doc ready

function update_bar(){
     $.getJSON("/core/data.php?id=<?=$id?>&stats_only=1", function (json) {
		
        var top = Math.round(json.stats.top);
		var top_max = Math.round(json.stats.top_max);
		
		var heart = Math.round(json.stats.heart);
		var heart_max = Math.round(json.stats.heart_max);
		
        var base = Math.round(json.stats.base);
        var base_max = Math.round(json.stats.base_max);

		//console.log(top_max);

		$('#top_bar').attr('aria-valuenow', top).css('width', top+'%').attr('aria-valuemax', top_max);
		$('#heart_bar').attr('aria-valuenow', heart).css('width', heart+'%').attr('aria-valuemax', heart_max);
		$('#base_bar').attr('aria-valuenow', base).css('width', base+'%').attr('aria-valuemax', base_max);;
        
		$('.top-label').html(top + "% Top Notes");
        $('.heart-label').html(heart + "% Heart Notes");
        $('.base-label').html(base + "% Base Notes");
	}); 
};

function re() {
    $('#formula').DataTable().ajax.reload();
	update_bar();
};


</script>

<table class="table table-striped table-bordered nowrap" width="100%" cellspacing="0">
	<thead>
    	 <tr class="noexport">
            <th colspan="10">
<div class="progress">  
      <div id="base_bar" class="progress-bar pv_bar_base_notes" role="progressbar" aria-valuemin="0"><span><div class="base-label"></div></span></div>
      <div id="heart_bar" class="progress-bar pv_bar_heart_notes" role="progressbar" aria-valuemin="0"><span><div class="heart-label"></div></span></div>
      <div id="top_bar" class="progress-bar pv_bar_top_notes" role="progressbar" aria-valuemin="0"><span><div class="top-label"></div></span></div>
</div>
</th>
<th>
                      <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                      <div class="dropdown-menu dropdown-menu-left">
                        <a class="dropdown-item" href="javascript:export_as('csv')">Export to CSV</a>
                        <a class="dropdown-item" href="javascript:export_as('pdf')">Export to PDF</a>
             			<div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:manageQuantity('multiply')">Multiply x2</a>
                        <a class="dropdown-item" href="javascript:manageQuantity('divide')">Divide x2</a>
                        <a class="dropdown-item" href="javascript:cloneMe()">Clone Formula</a>
	                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#amount_to_make">Amount to make</a>
               			<div class="dropdown-divider"></div>
	                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#create_accord">Create Accord</a>
	                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#conv_ingredient">Convert to ingredient</a>

             			<div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:addTODO()">Add to the make list</a>
                        <!-- <a class="dropdown-item" href="javascript:addAllToCart()">Add all to cart</a> -->
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="pages/viewHistory.php?id=<?=$meta['id']?>" target="_blank">View history</a>
                      </div>
                    </div>
                    </th>
</tr>
</table>
<div id="msgInfo"></div>
<a href="javascript:re()">RELOAD</a>
<table id="formula" class="table table-striped table-bordered nowrap viewFormula" style="width:100%">
        <thead>
            <tr>
                <th>Profile</th>
                <th>Ingredient</th>
                <th>CAS</th>
                <th>Purity</th>
                <th>Dilutant</th>
                <th>Quantity</th>
                <th>Concentration %*</th>
                <th>Final Concentration %</th>
                <th>Cost</th>
                <th>Properties</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tfoot>
        	<tr>
            <th>Total ingredients:</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total ml:</th>
            <th>Total conc %</th>
            <th></th>
            <th>Cost: </th>
            <th></th>
            <th></th>
            </tr>
        </tfoot>

</table>

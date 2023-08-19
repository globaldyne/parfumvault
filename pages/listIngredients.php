<?php 

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/loadModules.php');

$defCatClass = $settings['defCatClass'];

?>
<div class="col mb-4">
	<div class="text-right">
     <div class="btn-group">
     	<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
      	<div class="dropdown-menu dropdown-menu-right">
       	<li><a class="dropdown-item popup-link" href="/pages/mgmIngredient.php"><i class="fa-solid fa-plus mx-2"></i>Create new ingredient</a></li>
        <div class="dropdown-divider"></div>
        <li><a class="dropdown-item" id="csv_export" href="/pages/export.php?format=csv&kind=ingredients"><i class="fa-solid fa-file-csv mx-2"></i>Export to CSV</a></li>
        <li><a class="dropdown-item" id="json_export" href="/pages/export.php?format=json&kind=ingredients"><i class="fa-solid fa-file-code mx-2"></i>Export to JSON</a></li>
        <div class="dropdown-divider"></div>
        <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#csv_import"><i class="fa-solid fa-file-import mx-2"></i>Import from CSV</a></li>
        <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#import_ingredients_json"><i class="fa-solid fa-file-import mx-2"></i>Import from JSON</a></li>
        <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#pv_online_import"><i class="fa-solid fa-cloud-arrow-down mx-2"></i>Import from PV Online</a></li>
      </div>
     </div>                    
    </div>
</div>

<div class="dropdown-divider"></div>

<div id="row pv_search">
	<div class="text-right">
        <div class="pv_input_grp">   
          <input name="ing_search" type="text" class="form-control input-sm pv_input_sm" id="ing_search" value="<?=$_GET['search']?>" placeholder="Ingredient name, CAS, odor..">
            <div class="input-group-btn">
                <button class="btn btn-search btn-primary" id="pv_search_btn" data-provider="local">
                    <span class="fas fa-database mx-2"></span>
                    <span class="label-icon"><a href="#" class="btn-search">Local DB</a></span>
                </button>
                <label class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></label>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <?php foreach (loadModules('suppliers') as $search){ ?>
                    <li>
                        <a href="#" class="supplier dropdown-item" data-provider="<?=$search['fileName']?>">
                            <span class="<?=$search['icon']?>"></span>
                            <span class="label-icon"><?=$search['name']?></span>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
          </div>
   	  </div>
	</div>
</div>

<table id="tdDataIng" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>IUPAC</th>
          <th>Description</th>
          <th>Profile</th>
          <th>Category</th>
          <th>Stock</th>
          <th><?=ucfirst($defCatClass)?></th>
          <th>Supplier(s)</th>
          <th>Document(s)</th>
          <th></th>
      </tr>
   </thead>
</table>

<script type="text/javascript">

$(document).ready(function() {
	
	var tdDataIng = $('#tdDataIng').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [1,5,6,7,8,9]}
	],
	search: {
    	search: "<?=$_GET['search']?>"
  	},
	dom: 'lr<"#advanced_search">tip',
	initComplete: function(settings, json) {
        $("#advanced_search").html('<span><hr /><a href="#" class="advanced_search_box mb-2" data-toggle="modal" data-target="#adv_search">Advanced Search</a></span>');
		$("#tdDataIng_filter").detach().appendTo('#pv_search');
    },
	processing: true,
	serverSide: true,
	searching: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: 'Blending...',
		zeroRecords: '<div class="alert alert-warning"><strong>Nothing found, try <a href="#" data-toggle="modal" data-target="#adv_search">advanced</a> search instead?</strong></div>',
		search: 'Quick Search:',
		searchPlaceholder: 'Name, CAS, EINECS, IUPAC, odor..',
		},
	ajax: {	
		url: '/core/list_ingredients_data.php',
		type: 'POST',
		data: function(d) {
            d.provider = $('#pv_search_btn').attr('data-provider')
			d.adv = '<?=$_GET['adv']?:0?>'
			d.profile = '<?=$_GET['profile']?:null?>'
			d.name = '<?=$_GET['name']?:null?>'
			d.cas = '<?=$_GET['cas']?:null?>'
			d.einecs = '<?=$_GET['einecs']?:null?>'
			d.odor = '<?=$_GET['odor']?:null?>'
			d.cat = '<?=$_GET['cat']?:null?>'
			d.synonym = '<?=$_GET['synonym']?:null?>'
			if (d.order.length>0){
                d.order_by = d.columns[d.order[0].column].data
                d.order_as = d.order[0].dir
            }
        },
		dataType: 'json',
		},
	columns: [
			  { data : 'name', title: 'Name', render: iName },
			  { data : 'IUPAC', title: 'IUPAC' },
			  { data : 'odor', title: 'Description'},
			  { data : 'profile', title: 'Profile', render: iProfile },
			  { data : 'category', title: 'Category', render: iCategory },
			  { data : 'usage.limit', title: '<?=ucfirst($defCatClass)?>(%)', render: iLimit},
			  { data : 'stock', title: 'In Stock <i rel="tip" title="The total amount available in stock from all suppliers." class="fas fa-info-circle"></i></span>', render: iStock},
			  { data : null, title: 'Supplier(s)', render: iSuppliers},
			  { data : null, title: 'Document(s)', render: iDocs},

			  { data : null, title: '', render: actions},		   
			 ],
	order: [[ 0, 'asc' ]],
	lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
	pageLength: 20,
	displayLength: 20,
	drawCallback: function( settings ) {
		extrasShow();
    },
	stateSave: true,
	stateDuration : -1,
	stateLoadCallback: function (settings, callback) {
       	$.ajax( {
           	url: '/core/update_user_settings.php?set=listIngredients&action=load',
           	dataType: 'json',
           	success: function (json) {
               	callback( json );
				if(json.search.search){
					$('#ing_search').val(json.search.search);
				}
           	}
       	});
    },
    stateSaveCallback: function (settings, data) {
	   $.ajax({
		 url: "/core/update_user_settings.php?set=listIngredients&action=save",
		 data: data,
		 dataType: "json",
		 type: "POST"
	  });
	},

});
	    
	$('#ing_search').keyup(function() {
        tdDataIng.search($(this).val()).draw();
    });
	
	$('#pv_search').on('click', '[id*=pv_search_btn]', function () {
		var ingSearch = {};
		ingSearch.txt = $('#ing_search').val();
		tdDataIng.search(ingSearch.txt).draw();
	});
	
 
});
					   
function iName(data, type, row, meta){
	var alg = '';
	if(row.allergen == 1){
		var alg = '<span class="ing_alg"><i rel="tip" title="Allergen" class="fas fa-exclamation-triangle"></i></span>';
	}
	if(meta.settings.json.source == 'local'){
		
		return '<a class="popup-link listIngName listIngName-with-separator" href="/pages/mgmIngredient.php?id=' + btoa(row.name) + '">' + data + '</a>'+alg+'<span class="listIngHeaderSub">CAS: <i class="pv_point_gen subHeaderCAS" rel="tip" title="Click to copy cas" id="cCAS" data-name="'+row.cas+'">'+row.cas+'</i> | EINECS: <i class="pv_point_gen subHeaderCAS">'+row.einecs+'</i></span>';
	}else{
		return '<a class="listIngName listIngName-with-separator" href="#">' + data + '</a>'+alg+'<span class="listIngHeaderSub">CAS: <i class="pv_point_gen subHeaderCAS" rel="tip" title="Click to copy cas" id="cCAS" data-name="'+row.cas+'">'+row.cas+'</i> | EINECS: <i class="pv_point_gen subHeaderCAS">'+row.einecs+'</i></span>';
	}
}


function iProfile(data, type, row){
	if(row.profile){
		return '<img src="/img/Pyramid/Pyramid_Slice_'+row.profile+'.png" class="img_ing_prof"/>';    
	}else{
		return '<img src="/img/pv_molecule.png" class="img_ing_prof"/>';
	}
}

function iStock(data, type, row, meta){
	if (row.physical_state == 1) {
		var ingUnit = "ml";
	}else if (row.physical_state == 2) {
		var ingUnit = "gr";
	}
	if(meta.settings.json.source == 'local'){
		return '<a class="popup-link" rel="tip" title="'+ingUnit+'" href="/pages/views/ingredients/ingSuppliers.php?id=' + row.id + '&standAlone=1">' + data + '</a>';
	}else{
		return 'N/A';
	}
}

function iCategory(data, type, row){
	if(row.category.image){
		return '<i rel="tip" title="'+row.category.name+'"><img class="img_ing ing_ico_list" src="'+row.category.image+'" /></i>';    
	}else{
		return '<img src="/img/pv_molecule.png" class="img_ing_prof"/>';
	}
}

function iLimit(data, type, row){
	var byPassIFRA = '';
	if(row.info.byPassIFRA == 1){
		var byPassIFRA = '<span class="ing_alg"> <i rel="tip" title="IFRA record is bypassed" class="fas fa-exclamation-triangle"></i></span>';	
	}
	
	if(row.usage.reason == 1){
		var reason = 'Recommendation';
	}else if(row.usage.reason == 2){
		var reason = 'Restriction';
	}else if(row.usage.reason == 3){
		var reason = 'Specification';
	}else if(row.usage.reason == 4){
		var reason = 'Prohibition';
	}else{
		var reason = row.usage.reason;
	}
	
	return '<i class="pv_point_gen pv_gen_li" rel="tip" title="'+reason+'">'+row.usage.limit+'</i>'+byPassIFRA;
}

function iSuppliers(data, type, row){
	if(row.supplier){
	data ='<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-store mx-2"></i><span class="badge badge-light">'+row.supplier.length+'</span></button><div class="dropdown-menu dropdown-menu-right">';
	for (var key in row.supplier) {
		if (row.supplier.hasOwnProperty(key)) {
			data+='<li><a class="dropdown-item popup-link" href="'+row.supplier[key].link+'"><i class="fa fa-store mx-2"></i>'+row.supplier[key].name+'</a></li>';
		}
	}                
	data+='</div></div></td>';
}else{
		data = 'N/A';
	}
	return data;
}

function iDocs(data, type, row){
	if(row.document){	
		data ='<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-alt mx-2"></i><span class="badge badge-light">'+row.document.length+'</span></button><div class="dropdown-menu dropdown-menu-right">';
		for (var key in row.document) {
			if (row.document.hasOwnProperty(key)) {
				data+='<a class="dropdown-item popup-link" href="/pages/viewDoc.php?id='+row.document[key].id+'"><i class="fa fa-file-alt mx-2"></i>'+row.document[key].name+'</a>';
			}
		}                
		data+='</div></div></td>';
	
		}else{
			data = 'N/A';
		}
	
	return data;
}


function actions(data, type, row, meta){
	if(meta.settings.json.source == 'PVOnline'){
		data = '<div class="dropdown">' +
			'<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu dropdown-menu-right">' + 
				'<li><a rel="tip" title="Import '+ row.name +'" class="pv_point_gen text-dark" id="impIng" data-name="'+ row.name +'" data-id='+ row.id +'><i class="fas fa-download mx-2"></i>Import to local DB</a></li>'; 
		data += '</ul></div>';		
	}else{//Treat the rest as local
		data = '<div class="dropdown">' +
			'<button type="button" class="btn btn-primary btn-floating dropdown-toggle hidden-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu dropdown-menu-right">';
		data += '<li><a href="/pages/mgmIngredient.php?id='+btoa(row.name)+'" class="dropdown-item popup-link"><i class="fas fa-edit mx-2"></i>Manage</a></li>';
		
		data += '<li><a class="dropdown-item" href="/pages/export.php?format=json&kind=single-ingredient&id=' + row.id + '" rel="tip" title="Export '+ row.name +' as JSON" ><i class="fas fa-download mx-2"></i>Export as JSON</a></li>';

		data += '<div class="dropdown-divider"></div>';

		data += '<li><a rel="tip" title="Remove '+ row.name +'" class="dropdown-item pv_point_gen text-danger" id="rmIng" data-name="'+ row.name +'" data-id='+ row.id +'><i class="fas fa-trash mx-2"></i>Delete</a></li>'; 
		data += '</ul></div>';		
	}
	
	return data;
}


$('#tdDataIng').on('click', '[id*=cCAS]', function () {
	var copy = {};
	copy.Name = $(this).attr('data-name');
	const el = document.createElement('textarea');
    el.value = copy.Name;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
});



$('#tdDataIng').on('click', '[id*=impIng]', function () {
	var ing = {};
	ing.ID = $(this).attr('data-id');
	ing.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm ingredient import",
       message : 'Import <strong>'+ ing.Name +'</strong>\'s data from PVOnline? <hr/><div class="alert alert-warning"><strong>Please note: data maybe incorrect and/or incomplete, you should validate them after import.</strong></div>',
       buttons :{
           main: {
               label : "Import",
               className : "btn-warning",
               callback: function (){
	    			
				$.ajax({
					url: '/pages/update_data.php', 
					type: 'POST',
					data: {
						action: "import",
						source: "PVOnline",
						kind: "ingredient",
						ing_id: ing.ID,
						},
					dataType: 'json',
					success: function (data) {
						if(data.success) {
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
						} else {
							var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				
						}
						$('#innermsg').html(msg);
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


$('#tdDataIng').on('click', '[id*=rmIng]', function () {
	var ing = {};
	ing.ID = $(this).attr('data-id');
	ing.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm ingredient deletion",
       message : 'Permanently delete <strong>'+ ing.Name +'</strong> and its data?',
       buttons :{
           main: {
               label : "Delete",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({
					url: '/pages/update_data.php', 
					type: 'POST',
					data: {
						ingredient: "delete",
						ing_id: ing.ID,
						},
					dataType: 'json',
					success: function (data) {
						if(data.success) {
								var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
								reload_ingredients_data();
							} else {
								var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				
							}
							$('#innermsg').html(msg);
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

function reload_ingredients_data() {
    $('#tdDataIng').DataTable().ajax.reload(null, true);
}

$(".input-group-btn .dropdown-menu li a").click(function () {
	var selText = $(this).html();
	var provider = $(this).attr('data-provider');
	  
	$(this).parents(".input-group-btn").find(".btn-search").html(selText);
	$(this).parents(".input-group-btn").find(".btn-search").attr('data-provider',provider);
	
	$('#pv_search_btn').click();
	if($('#pv_search_btn').data().provider == 'local'){
		$("#advanced_search").html('<span><hr /><a href="#" class="advanced_search_box" data-toggle="modal" data-target="#adv_search">Advanced Search</a></span>');
	}else{
		$("#advanced_search").html('');
	}
	
});

function extrasShow() {
	$('[rel=tip]').tooltip({
        "html": true,
        "delay": {"show": 100, "hide": 0},
     });
	$('.popup-link').magnificPopup({
		type: 'iframe',
		closeOnContentClick: false,
		closeOnBgClick: false,
		showCloseBtn: true,
	});
};

</script>
<script src="/js/import.ingredients.js"></script>

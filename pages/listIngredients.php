<?php 

define('__ROOT__', dirname(dirname(__FILE__))); 

require(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/loadModules.php');

$defCatClass = $settings['defCatClass'];

?>
<table class="table table-striped table-bordered" style="width:100%">
    <tr class="noBorder noexport">
    <th colspan="9">
     <div class="col-sm-6 text-left">
              
     </div>
     <div class="col-sm-6 text-right">
      <div class="btn-group">
       <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item popup-link" href="pages/mgmIngredient.php">Add new ingredient</a>
        <a class="dropdown-item" id="csv_export" href="/pages/export.php?format=csv&kind=ingredients">Export to CSV</a>
        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#csv_import">Import from CSV</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#pv_online_import">Import from PV Online</a>
        <?php if($pv_online['email'] && $pv_online['password'] && $pv_online['enabled'] == '1'){?>
        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#pv_online_upload">Upload to PV Online</a>
        <?php } ?>
      </div>
     </div>                    
    </div>
  </th>
</tr>
</table>

<div id="pv_search">
	<div class="text-right">
        <div class="pv_input_grp">   
          <input name="ing_search" type="text" class="form-control input-sm pv_input_sm" id="ing_search" value="<?=$_GET['search']?>" placeholder="Name, CAS, odor..">
            <div class="input-group-btn">
                <button class="btn btn-search btn-primary" id="pv_search_btn" data-provider="local">
                    <span class="fas fa-database"></span>
                    <span class="label-icon"><a href="#" class="btn-search">Local DB</a></span>
                </button>
                <label class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
              </label>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <?php foreach (loadModules('suppliers') as $search){ ?>
                    <li>
                        <a href="#" class="supplier" id="provider" data-provider="<?=$search['fileName']?>">
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
          <th><?=ucfirst($defCatClass)?></th>
          <th>Supplier(s)</th>
          <th>Document(s)</th>
          <th>Actions</th>
      </tr>
   </thead>
</table>

<script type="text/javascript">

$(document).ready(function() {
	
	var tdDataIng = $('#tdDataIng').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [1,3,5,8]}
	],
	search: {
    	search: "<?=$_GET['search']?>"
  	},
	dom: 'lr<"#advanced_search">tip',
	initComplete: function(settings, json) {
        $("#advanced_search").html('<span><hr /><a href="#" class="advanced_search_box" data-toggle="modal" data-target="#adv_search">Advanced Search</a></span>');
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
			  { data : null, title: 'Supplier(s)', render: iSuppliers},
			  { data : null, title: 'Document(s)', render: iDocs},

			  { data : null, title: 'Actions', render: actions},		   
			 ],
	order: [[ 0, 'asc' ]],
	lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
	pageLength: 20,
	displayLength: 20,
	drawCallback: function( settings ) {
			extrasShow();
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
					   
function iName(data, type, row){
	var alg = '';
	if(row.allergen == 1){
		var alg = '<span class="ing_alg"> <i rel="tip" title="Allergen" class="fas fa-exclamation-triangle"></i></span>';
	}
	return '<a class="popup-link listIngName listIngName-with-separator" href="pages/mgmIngredient.php?id=' + btoa(row.name) + '">' + data + '</a>'+alg+'<span class="listIngHeaderSub">CAS: <i class="pv_point_gen subHeaderCAS" rel="tip" title="Click to copy cas" id="cCAS" data-name="'+row.cas+'">'+row.cas+'</i> | EINECS: <i class="pv_point_gen subHeaderCAS">'+row.einecs+'</i></span>';

}


function iProfile(data, type, row){
	if(row.profile){
		return '<img src="/img/Pyramid/Pyramid_Slice_'+row.profile+'.png" class="img_ing_prof"/>';    
	}else{
		return '<img src="/img/pv_molecule.png" class="img_ing_prof"/>';
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
	
	return '<i class="pv_point_gen pv_gen_li" rel="tip" title="'+reason+'">'+row.usage.limit+'</i>';
}

function iSuppliers(data, type, row){
	if(row.supplier){
	data ='<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-store"></i> <span class="badge badge-light">'+row.supplier.length+'</span></button><div class="dropdown-menu dropdown-menu-right">';
	for (var key in row.supplier) {
		if (row.supplier.hasOwnProperty(key)) {
			data+='<a class="dropdown-item popup-link" href="'+row.supplier[key].link+'">'+row.supplier[key].name+'</a>';
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
		data ='<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-alt"></i> <span class="badge badge-light">'+row.document.length+'</span></button><div class="dropdown-menu dropdown-menu-right">';
		for (var key in row.document) {
			if (row.document.hasOwnProperty(key)) {
				data+='<a class="dropdown-item popup-link" href="pages/viewDoc.php?id='+row.document[key].id+'">'+row.document[key].name+'</a>';
			}
		}                
		data+='</div></div></td>';
	
		}else{
			data = 'N/A';
		}
	
	return data;
}


function actions(data, type, row){
	return '<a href="pages/mgmIngredient.php?id='+btoa(row.name)+'" class="fas fa-edit popup-link"><a> <i rel="tip" title="Remove '+ row.name +'" class="pv_point_gen fas fa-trash" style="color: #c9302c;" id="rmIng" data-name="'+ row.name +'" data-id='+ row.id +'></i>';    
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
					url: 'pages/update_data.php', 
					type: 'GET',
					data: {
						ingredient: "delete",
						ing_id: ing.ID,
						},
					dataType: 'html',
					success: function (data) {
						$('#innermsg').html(data);
						reload_ingredients_data();
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

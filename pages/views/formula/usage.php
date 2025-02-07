<div class="text-right">
	<div class="btn-group">
      <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
      <div class="dropdown-menu">                                	  
        <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export as CSV</a></li>
      </div>
  </div>
</div>
<h3>Usage data</h3>
<hr>
<table id="tdDataUsage" class="table table-striped" style="width:100%">
  <thead>
      <tr>
        <th>Cat1%</th>
        <th>Cat2%</th>
        <th>Cat3%</th>
        <th>Cat4%</th>
        <th>Cat5A%</th>
        <th>Cat5B%</th>
        <th>Cat5C%</th>
        <th>Cat5D%</th>
        <th>Cat6%</th>
        <th>Cat7A%</th>
        <th>Cat7B%</th>
        <th>Cat8%</th>
        <th>Cat9%</th>
        <th>Cat10A%</th>
        <th>Cat11A%</th>
        <th>Cat11B%</th>
        <th>Cat12%</th>
      </tr>
   </thead>
</table>
<div class="mt-4">
    <div class="alert alert-info">
    	<i class="fa-solid fa-circle-info mx-2"></i>
    	<strong>IFRA Categories explanation</strong>
        <table id="tdDataIFRACat" class="table table-striped" style="width:100%">
          <thead>
              <tr>
                <th>Category name</th>
                <th>Purpose</th>
              </tr>
           </thead>
        </table>
    </div>
</div>
<script>
$(document).ready(function() {
	$.fn.dataTable.ext.errMode = 'none';

	var tdDataUsage = $('#tdDataUsage').DataTable( {
		columnDefs: [
			{ className: 'pv_vertical_middle text-center', targets: '_all' },
			{ orderable: false, targets: ['_all']},
			{ responsivePriority: 1, targets: 0 }
		],
		dom: 'rt',
		buttons: [{
			extend: 'csvHtml5',
			title: "Formula Usage"
		}],
		processing: true,
		serverSide: true,
		searching: false,
		responsive: true,
		
		ajax: {	
			url: '/core/list_formula_usage_data.php',
			type: 'POST',
			dataType: 'json',
			 data: function(d) {
                d.fid = '<?= $_GET['fid'] ?>';
            }
		 },
		 columns: [
			{ data: 'cat1', title: 'Cat1%'},
			{ data: 'cat2', title: 'Cat2%'},
			{ data: 'cat3', title: 'Cat3%'},
			{ data: 'cat4', title: 'Cat4%'},
			{ data: 'cat5A', title: 'Cat5A%'},
			{ data: 'cat5B', title: 'Cat5B%'},
			{ data: 'cat5C', title: 'Cat5C%'},
			{ data: 'cat5D', title: 'Cat5D%'},
			{ data: 'cat6', title: 'Cat6%'},
			{ data: 'cat7A', title: 'Cat7A%'},
			{ data: 'cat7B', title: 'Cat7B%'},
			{ data: 'cat8', title: 'Cat8%'},
			{ data: 'cat9', title: 'Cat9%'},
			{ data: 'cat10A', title: 'Cat10A%'},
			{ data: 'cat11A', title: 'Cat11A%'},
			{ data: 'cat11B', title: 'Cat11B%'},
			{ data: 'cat12', title: 'Cat12%'}
		]
	}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
		$('#tdDataUsage').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
	});
	
	var tdDataIFRACat = $('#tdDataIFRACat').DataTable( {
		columnDefs: [
			{ className: 'pv_vertical_middle text-center', targets: '_all' },
			{ orderable: false, targets: ['_all']},
		],
		dom: 'rt',
		processing: true,
		serverSide: true,
		searching: false,
		responsive: true,
		ajax: {	
			url: '/core/list_ifra_cat_data.php',
			type: 'POST',
			dataType: 'json'
		 },
		 columns: [
			{ data: 'name', title: 'Category name', render: catFullName},
			{ data: 'description', title: 'Purpose'}
		]
	}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
		$('#tdDataIFRACat').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
	});
	
	
	function catFullName(data, type, row){
		return 'Cat' + row.name; 
	};

	$("#exportCSV").click(() => {
		$("#tdDataUsage").DataTable().button(0).trigger();
	});

});
	

</script>

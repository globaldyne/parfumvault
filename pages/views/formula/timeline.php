<h3>Historical changes</h3>
<hr>
<div class="card-body">
  <div>
    <table class="table table-bordered" id="tdHistory" width="100%" cellspacing="0">
      <thead>
          <tr>
              <th>Changes</th>
              <th>Date</th>
              <th>User</th>
          </tr>
       </thead>
    </table>
  </div>
</div>

<script type="text/javascript" language="javascript" >
$(document).ready(function(){

	var tdHistory = $('#tdHistory').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
	],
	dom: 'lfrtip',
	processing: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
		emptyTable: 'No data found.',
		search: 'Search:'
		},
	ajax: {	
		url: '/core/formula_timeline_data.php',
		type: 'GET',
		data: {
				id: '<?=$_GET['id']?>',
			},
		},
	columns: [
			  { data : 'change_made', title: 'Changes' },
			  { data : 'date_time', title: 'Date' },
			  { data : 'user', title: 'User' },
			],
	
	order: [[ 1, 'desc' ]],
	lengthMenu: [[15, 50, 100, -1], [15, 50, 100, "All"]],
	pageLength: 15,
	displayLength: 15,
	stateSave: true,
	stateLoadCallback: function (settings, callback) {
		$.ajax( {
			url: '/core/update_user_settings.php?set=listTimeline&action=load',
			dataType: 'json',
			success: function (json) {
				callback( json );
			}
		});
	},
	stateSaveCallback: function (settings, data) {
	   $.ajax({
		 url: "/core/update_user_settings.php?set=listTimeline&action=save",
		 data: data,
		 dataType: "json",
		 type: "POST"
	  });
	},
	
  });

});
</script>
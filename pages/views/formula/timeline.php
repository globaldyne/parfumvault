<h3>Historical changes</h3>
<hr>
<div class="card-body">
  <div>
    <table class="table table-striped" id="tdHistory" width="100%" cellspacing="0">
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

<script>
$(document).ready(function(){
	$.fn.dataTable.ext.errMode = 'none';

	var tdHistory = $('#tdHistory').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No historical data found</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by name...',
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
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
		pageLength: 20,
		displayLength: 20,
		stateSave: true,
		stateDuration: -1,
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
		}
	  }).on('error.dt', function(e, settings, techNote, message) {
            var m = message.split(' - ');
            $('#fetch_timeline').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
      });

});
</script>
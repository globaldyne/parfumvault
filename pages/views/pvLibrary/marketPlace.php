<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
<div class="container-fluid">
	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h2 class="m-0 font-weight-bold text-primary-emphasis"><i id="reload_data" class="pv_point_gen">Marketplace</i></h2>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<div id="data_area">
					<table id="all-table-market" class="table table-striped" width="100%" cellspacing="0">
						<thead>
							<tr>
								<th>Formula Name</th>
								<th>Status</th>
								<th>Author</th>
								<th>License</th>
								<th>Published</th>
								<th></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<!--Contact author modal-->
<div class="modal fade" id="contact-formula-author" data-bs-backdrop="static" tabindex="-1" aria-labelledby="contact-formula-author" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Contact <span id="fname-display" class="d-inline"></span> formula's author</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div id="cntMsg"></div>
				<div class="modal-body-main">
					<input type="hidden" name="fid" id="fid" />
					<input type="hidden" name="fname" id="fname" />
					<div class="alert alert-warning">
						You can contact the author of the formula using the form below if you have queries or recommendations regarding this formula.
						<p><strong>Please note, your full name and your email address will be shared with the author(s) of the formula so they can get in touch to discuss further. Also, a copy of your message will be shared with the admins to ensure there is no service abuse.</strong></p>
					</div>
					<div class="mb-3">
						<label for="contactName" class="form-label">Full name</label>
						<input name="contactName" id="contactName" type="text" class="form-control" required>
					</div>
					<div class="mb-3">
						<label for="contactEmail" class="form-label">Email</label>
						<input name="contactEmail" id="contactEmail" type="email" class="form-control" required>
					</div>
					<div class="mb-3">
						<label for="contactReason" class="form-label">Comments</label>
						<textarea name="contactReason" id="contactReason" rows="3" class="form-control" required></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary" id="confirm-contact-author">Send message</button>
			</div>
		</div>
	</div>
</div>


<!--Report formula modal-->
<div class="modal fade" id="report-market-formula" data-bs-backdrop="static" tabindex="-1" aria-labelledby="report-market-formula" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Report <span id="fname-display" class="d-inline"></span> formula</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="reportMsg"></div>
        <div class="modal-body-main">
          <input type="hidden" name="fid" id="fid" />
          <input type="hidden" name="fname" id="fname" />
          <div class="alert alert-warning">
            If you believe that <strong><span id="fname-display" class="d-inline"></span></strong> formula violates our 
            <a href="https://www.perfumersvault.com/community_rules" target="_blank">community rules</a>, please use this form to report it explaining in detail what's wrong.
            <p>Once we receive the report and review it, we will take actions, if any required, and let the author know.</p>
            <p><strong>Please don't use this form if you have queries or suggestions regarding the formula, use the <i>Contact Author</i> option instead.</strong></p>
          </div>
          <div class="mb-3">
            <label for="reporterName" class="form-label">Full name</label>
            <input name="reporterName" id="reporterName" type="text" class="form-control">
          </div>
          <div class="mb-3">
            <label for="reporterEmail" class="form-label">Email</label>
            <input name="reporterEmail" id="reporterEmail" type="email" class="form-control">
          </div>
          <div class="mb-3">
            <label for="reportReason" class="form-label">Please provide the reason you're reporting <span id="fname-display" class="d-inline"></span> formula in detail</label>
            <textarea name="reportReason" id="reportReason" rows="3" class="form-control"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="confirm-formula-report">Report formula</button>
      </div>
    </div>
  </div>
</div>

<?php

if ($system_settings['LIBRARY_enable'] == '0') {
	echo '<script>document.getElementById("all-table-market").innerHTML = \'<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>Marketplace is a part of the PV Library service which is currently disabled by your administrator.</strong></div>\';</script>';
} else {
	$qPVfids = mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE src = '1' AND owner_id = '$userID'");
	$result_fids = [];
	while ($rPVFIDS = mysqli_fetch_array($qPVfids)) {
		$result_fids[] = $rPVFIDS['name'];
	}
	$json_fids = json_encode($result_fids);
?>
<script>
$(document).ready(function() {
	const arrayFIDS = <?php echo $json_fids; ?> || '0';

	$.fn.dataTable.ext.errMode = 'none';

	function extrasShow() {
		$('[rel=tip]').tooltip({
			"html": true,
			"delay": {"show": 100, "hide": 0},
		});
	};

	var tableMarket = $("#all-table-market").DataTable({
		ajax: {
			url: '<?=$pvLibraryAPI?>',
			type: 'POST',
			dataType: 'json',
			timeout: 5000,
			data: function(d) {
				d.request = 'MarketPlace';
				d.action = 'list_all';
				if (d.order.length > 0) {
					d.order_by = d.columns[d.order[0].column].data;
					d.order_as = d.order[0].dir;
				}
			},
		},
		columns: [
			{ data: 'name', title: 'Formula Name', render: name },
			{ data: null, title: 'Status', render: status },
			{ data: 'author', title: 'Author', render: author },
			{ data: 'cost', title: 'License', render: cost },
			{ data: 'created_at', title: 'Published', render: formatDate },
			{ data: null, title: '', render: actions },
		],
		processing: true,
		serverSide: true,
		searching: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Blending...</span>',
			emptyTable: '<div class="alert alert-warning"><strong>No formulas found in Marketplace, please come back later.</strong></div>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			searchPlaceholder: 'Search by formula name..',
			search: ''
		},
		order: [0, 'asc'],
		columnDefs: [
			{ orderable: false, targets: [3, 5] },
			{ className: 'text-center', targets: '_all' },
		],
		destroy: true,
		paging: true,
		info: true,
		lengthMenu: [[20, 40, 60, 100], [20, 40, 60, 100]],
		drawCallback: function(settings) {
			extrasShow();
		},
	}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
		$('#data_area').html('<div class="alert alert-danger"><strong>' + m[1] + '</strong></div>');
	});

	var detailRows = [];

	$('#all-table-market tbody').on('click', '[id*=open-details]', function() {
		var tr = $(this).parents('tr');
		var row = tableMarket.row(tr);
		var idx = $.inArray(tr.attr('id'), detailRows);

		if (row.child.isShown()) {
			tr.removeClass('details');
			row.child.hide();
			detailRows.splice(idx, 1);
		} else {
			tr.addClass('details');
			row.child(format(row.data())).show();
			if (idx === -1) {
				detailRows.push(tr.attr('id'));
			}
		}
	});

	tableMarket.on('draw', function() {
		$.each(detailRows, function(i, id) {
			$('#' + id + ' td:first-child + td').trigger('click');
		});
	});

	function formatDate(data, type) {
	  if (type === 'display') {
		if (data === '0000-00-00 00:00:00') {
		  return '-';
		}
		
		try {
		  const [year, month, day] = data.split(/[- :]/).map(Number);
		  const dateObject = new Date(year, month - 1, day);
	
		  return dateObject.toLocaleDateString();
		} catch (error) {
		  console.error("Date parsing error:", error);
		  return data; // Return original data if parsing fails
		}
	  }
	
	  return data;
	}
	
	function format(d) {
		var details = '<strong>Description:</strong><br><span class="formula_details">' + d.notes +
			'</span><br><strong>Published:</strong><br><span class="formula_details">' + d.created_at +
			'</span><br><strong>Updated:</strong><br><span class="formula_details">' + d.updated_at + '</span>' +
			'</span><br><strong>Downloads:</strong><br><span class="formula_details">' + d.downloads + '</span>' +
			'<br><strong>Labels:</strong><br>';

		for (var key in d.labels) {
			if (d.labels.hasOwnProperty(key)) {
				details += '<span class="formula_details mx-2 badge pv-label label-md bg-primary">' + d.labels[key].name + '</span>';
			}
		}

		return details;
	}

	function cost(data, type, row) {
		if (row.cost == 0) {
			data = '<span class="badge pv-label label-md bg-success">FREE!</span>';
		} else {
			data = '<span class="badge pv-label label-md bg-info">' + row.currency + row.cost + '</span>';
		}
		return data;
	}

	function name(data, type, row) {
		return '<i class="pv_point_gen pv_gen_li" id="open-details"> ' + data + '</i>';
	}

	function author(data, type, row) {
		return '<i class="pv_point_gen pv_gen_li" id="open-details"> <a href="' + row.author.url + '" target="_blank">' + row.author.name + '</a></i>';
	}

	function status(data, type, row, meta) {
		if (arrayFIDS.includes(row.name)) {
			data = '<span class="badge pv-label label-md bg-success"><strong>Downloaded</strong></span>';
		} else {
			data = '<span class="badge pv-label label-md bg-warning"><strong>NEW!!!</strong></span>';
		}
		return data;
	}

	function actions(data, type, row, meta) {
		return '<div class="dropdown">' +
			'<button type="button" class="btn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
			'<ul class="dropdown-menu dropdown-menu-right">' +
			'<li><i class="pv_point_gen pv_gen_li dropdown-item" id="import-market-formula" data-id="' + row.id + '" data-name="' + row.name + '" rel="tip" title="Import ' + row.name + ' to my database"><i class="fas fa-download mx-2"></i>Import Formula</i></li>' +
			'<li><i class="pv_point_gen pv_gen_li dropdown-item open-contact-dialog" data-bs-toggle="modal" data-bs-target="#contact-formula-author" data-id="' + row.id + '" data-name="' + row.name + '" rel="tip" title="Contact ' + row.author.name + ' regarding the formula"><i class="fas fa-id-card mx-2"></i>Contact the author</i></li>' +
			'<div class="dropdown-divider"></div>' +
			'<li><i class="pv_point_gen pv_gen_li dropdown-item open-report-dialog link-danger" data-bs-toggle="modal" data-bs-target="#report-market-formula" rel="tip" title="Report ' + row.name + ' to admins" data-id=' + row.id + ' data-name="' + row.name + '"><i class="fas fa-bug mx-2"></i>Report formula</i></li>' +
			'</ul></div>';
	}

	$('#reload_data').click(function reload_data() {
		$('#all-table-market').DataTable().ajax.reload(null, true);
	});

	// Import Formula
	$('#all-table-market').on('click', '[id*=import-market-formula]', function() {
		$("#impMsg").html('<div class="alert alert-info"><img src="/img/loading.gif" class="mx-2"/>Please wait...</div>');

		var frm = {
			ID: $(this).attr('data-id'),
			Name: $(this).attr('data-name')
		};

		bootbox.dialog({
			title: "Confirm import",
			message: '<div id="impMsg">Import <strong>' + frm.Name + '</strong>\'s data from Marketplace? <hr/><div class="alert alert-warning"><strong>Please note: data maybe incorrect and/or incomplete, you should validate them after import.</strong></div></div>',
			buttons: {
				main: {
					label: "Import formula",
					className: "btn-warning",
					callback: function() {
						$.ajax({
							url: '/core/core.php',
							type: 'POST',
							data: {
								action: "import",
								source: "pvMarket",
								kind: "formula",
								fid: frm.ID,
							},
							dataType: 'json',
							success: function(data) {
								if (data.success) {
									location.reload(true);
									bootbox.hideAll();
									return true;
								} else {
									$('#impMsg').html('<div class="alert alert-danger">' + data.error + '</div>');
									return false;
								}
							},
							error: function(request, status, error) {
								$('#impMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>Unable to handle request, server returned an error: ' + request.status + '</div>');
							},
						});

						return false;
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
			onEscape: function() {
				return true;
			}
		});
	});

	// Contact author
	$("#all-table-market").on("click", ".open-contact-dialog", function() {
		$('#cntMsg').html('');
		$('#contact-formula-author .modal-body-main').show();
		$("#contactName, #contactEmail, #contactReason").val('');

		var fname = $(this).data('name');
		var fid = $(this).data('id');

		$("#contact-formula-author #fname").val(fname);
		$("#contact-formula-author #fid").val(fid);
		$("#contact-formula-author #fname").html(fname);
	});

	$('#contact-formula-author').on('click', '[id*=confirm-contact-author]', function() {
		$("#cntMsg").html('<div class="alert alert-info"><img src="/img/loading.gif" class="mx-2"/>Please wait...</div>');
		$('#contact-formula-author, #confirm-contact-author').prop('disabled', true);
		$.ajax({
			url: '/core/core.php',
			type: 'POST',
			data: {
				action: "contactAuthor",
				src: 'pvMarket',
				contactName: $('#contactName').val(),
				contactEmail: $('#contactEmail').val(),
				contactReason: $('#contactReason').val(),
				fname: $('#fname').val(),
				fid: $('#fid').val()
			},
			dataType: 'json',
			success: function(data) {
				var msg;
				if (data.success) {
					msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
					$('#contact-formula-author .modal-body-main, #contact-formula-author #confirm-contact-author').hide();
					$('#contact-formula-author, #confirm-contact-author').prop('disabled', true);
				} else {
					msg = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>' + data.error + '</div>';
					$('#contact-formula-author, #confirm-contact-author').prop('disabled', false);
				}
				$('#cntMsg').html(msg);
			},
			error: function(request, status, error) {
				$('#cntMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>Unable to handle request, server returned an error: ' + request.status + '</div>');
				$('#contact-formula-author, #confirm-contact-author').prop('disabled', false);
			},
		});
	});

	// Report formula
	$("#all-table-market").on("click", ".open-report-dialog", function() {
		$('#reportMsg').html('');
		$('#report-market-formula .modal-body-main').show();
		$("#reporterName, #reporterEmail, #reportReason").val('');

		var fname = $(this).data('name');
		var fid = $(this).data('id');

		$("#report-market-formula #fname").val(fname);
		$("#report-market-formula #fid").val(fid);
		$("#report-market-formula #fname").html(fname);
	});

	$('#report-market-formula').on('click', '[id*=confirm-formula-report]', function() {
		$("#reportMsg").html('<div class="alert alert-info"><img src="/img/loading.gif" class="mx-2"/>Please wait...</div>');
		$('#report-market-formula, #confirm-formula-report').prop('disabled', true);

		$.ajax({
			url: '/core/core.php',
			type: 'POST',
			data: {
				action: 'report',
				src: 'pvMarket',
				reporterName: $('#reporterName').val(),
				reporterEmail: $('#reporterEmail').val(),
				reportReason: $('#reportReason').val(),
				fname: $('#report-market-formula #fname').val(),
				fid: $('#report-market-formula #fid').val()
			},
			dataType: 'json',
			success: function(data) {
				var msg;
				if (data.success) {
					msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
					$('#report-market-formula .modal-body-main, #report-market-formula #confirm-formula-report').hide();
				} else {
					msg = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>' + data.error + '</div>';
					$('#report-market-formula, #confirm-formula-report').prop('disabled', false);
				}
				$('#reportMsg').html(msg);
			},
			error: function(request, status, error) {
				$('#reportMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>Unable to handle request, server returned an error: ' + request.status + '</div>');
				$('#report-market-formula, #confirm-formula-report').prop('disabled', false);
			},
		});
	});

	$('.table').on('show.bs.dropdown', function() {
		$('.table-responsive').css("overflow", "inherit");
	});

	$('.table').on('hide.bs.dropdown', function() {
		$('.table-responsive').css("overflow", "auto");
	});
});
<?php } ?>
</script>

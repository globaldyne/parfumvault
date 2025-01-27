
<script>
$(document).ready(function() {
	
	
	$("#configureScale").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/pvscale/configure.php")
			.then(data => {
			$(".modal-body", this).html(data);
		});
	});

	$("#buyScale").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/pvscale/buy.php")
			.then(data => {
			$(".modal-body", this).html(data);
		});
	});
	
});

</script>

<!-- SCALE BUY MODAL -->            
<div class="modal fade" id="buyScale" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="buyScale" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="buyScaleLabel">Buy PV Scale</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
    </div>
  </div>
</div>

<!-- SCALE CONFIGURE MODAL -->            
<div class="modal fade" id="configureScale" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="configureScaleLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="configureScaleLabel">Configure Scale</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
    </div>
  </div>
</div>
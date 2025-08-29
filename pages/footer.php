<?php 
if (!defined('pvault_panel')){ die('Not Found');}
$commitFile = __ROOT__ . '/COMMIT';
$gitEditMsgFile = __ROOT__ . '/.git/COMMIT_EDITMSG';

$commit = @file_get_contents($commitFile);

if ($commit === false || empty(trim($commit))) {
    $gitEditMsg = @file_get_contents($gitEditMsgFile);

    if ($gitEditMsg !== false && !empty(trim($gitEditMsg))) {
        $commitParts = explode('-', $gitEditMsg);
        $commit = trim($commitParts[0]);
    } else {
        $commit = getenv('OPENSHIFT_BUILD_COMMIT');
    }
}

if (empty($commit)) {
    $commit = ''; // fallback if commit is not found in any source
}

	
?>

  <footer class="sticky-footer">
    <div class="container my-auto">
      <div class="copyright text-center my-auto">
        <hr/>
        <strong><a href="#" id="pv_link" data-bs-toggle="modal" data-bs-target="#pv_app_modal"><?php echo $product; ?></a></strong>
        <div id="footer_release" class="pv_point_gen"> Version: <strong><?php echo $ver ."  ". $commit; ?></strong></div>
        <div><a href="https://ko-fi.com/perfumersvault" target="_blank"><strong>Sponsor the project on Ko-fi</strong></a></div>
        <div class="mt-2" style="display: flex; justify-content: center; align-items: center; gap: 20px;">
          <div style="text-align: center;">
            <a href="https://apps.apple.com/us/app/perfumers-vault-2/id6748814424" target="_blank">
              <img src="/img/appstore/get_pv.png" alt="App Store" style="width: 150px;">
            </a>
            <div class="mt-1">Get the Perfumers Vault 2 app</div>
          </div>
        </div>
        <div class="mt-2">Copyright &copy; 2017-<?php echo date('Y'); ?></div>
      </div>
    </div>
  </footer>

<!-- Perfumers Vault App Modal -->
<div class="modal fade" id="pv_app_modal" tabindex="-1" aria-labelledby="pv_app_modal_label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pv_app_modal_label">Get Perfumers Vault</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p class="mb-4">Choose your platform to download the Perfumers Vault app:</p>
        <div class="row justify-content-center">
          <div class="col-md-6 mb-3">
            <a href="https://apps.apple.com/us/app/perfumers-vault-2/id6748814424" target="_blank" class="btn btn-outline-primary w-100 p-3">
              <div class="d-flex align-items-center justify-content-center">
                <i class="bi bi-apple me-2" style="font-size: 1.5rem;"></i>
                <div>
                  <div class="fw-bold">App Store</div>
                  <small class="text-muted">for iOS devices</small>
                </div>
              </div>
            </a>
          </div>
          <div class="col-md-6 mb-3">
            <a href="#" onclick="alert('Play Store version coming soon!')" class="btn btn-outline-success w-100 p-3">
              <div class="d-flex align-items-center justify-content-center">
                <i class="bi bi-google-play me-2" style="font-size: 1.5rem;"></i>
                <div>
                  <div class="fw-bold">Play Store</div>
                  <small class="text-muted">for Android devices</small>
                </div>
              </div>
            </a>
          </div>
        </div>
        <hr class="my-4">
        <p class="mb-3">Or visit our website:</p>
        <a href="https://www.perfumersvault.com" target="_blank" class="btn btn-outline-secondary">
          <i class="bi bi-globe me-2"></i>
          perfumersvault.com
        </a>
      </div>
    </div>
  </div>
</div>


<script>
$('#footer_release').click(function() {
  $('#release_notes').modal('show');
});
</script>

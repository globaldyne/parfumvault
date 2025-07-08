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
        <strong><a href="https://www.perfumersvault.com" target="_blank"><?php echo $product; ?></a></strong>
        <div id="footer_release" class="pv_point_gen"> Version: <strong><?php echo $ver ."  ". $commit; ?></strong></div>
        <div><a href="https://discord.gg/WxNE8kR8ug" target="_blank"><strong>Discord Server</strong></a></div>
        <div><a href="https://ko-fi.com/perfumersvault" target="_blank"><strong>Sponsor the project on Ko-fi</strong></a></div>
        <div class="mt-2" style="display: flex; justify-content: center; align-items: center; gap: 20px;">
          <div style="text-align: center;">
            <a href="https://apps.apple.com/us/app/perfumers-vault/id1525381567" target="_blank">
              <img src="/img/appstore/get_pv.png" alt="App Store" style="width: 150px;">
            </a>
            <div class="mt-1">Get the Perfumers Vault app</div>
          </div>
          <div style="text-align: center;">
            <a href="https://apps.apple.com/us/app/aromatrack/id6742348411" target="_blank">
              <img src="/img/appstore/get_aroma_track.png" alt="App Store" style="width: 150px;">
            </a>
            <div class="mt-1">Get the AromaTrack app</div>
          </div>
        </div>
        <div class="mt-2">Copyright &copy; 2017-<?php echo date('Y'); ?></div>
      </div>
    </div>
  </footer>

<script>
$('#footer_release').click(function() {
  $('#release_notes').modal('show');
});
</script>

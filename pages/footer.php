<?php if (!defined('pvault_panel')){ die('Not Found');}?>
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span><strong><a href="https://www.jbparfum.com" target="_blank"><?php echo $product; ?></a></strong><div id="footer_release" class="pv_point_gen"> Version: <strong><?php echo $ver; ?></div></strong><br />Copyright &copy; 2017-<?php echo date('Y'); ?> </span>
          </div>
        </div>
      </footer>

<script>
$('#footer_release').click(function() {
	$('#release_notes').modal('show');
});
</script>

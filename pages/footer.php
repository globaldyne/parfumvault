<?php if (!defined('pvault_panel')){ die('Not Found');}?>

  <footer class="sticky-footer bg-white">
    <div class="container my-auto">
      <div class="copyright text-center my-auto">
      
        <span><strong><i class="fa-solid fa-heart mx-2" style="color: #ff0000;"></i><a href="https://www.paypal.com/paypalme/jbparfum" target="_blank" class="pv_point_gen">Donate if you found this useful.
</a></strong></span>
	<hr>
        <span><strong><a href="https://www.perfumersvault.com" target="_blank"><?php echo $product; ?></a></strong>
        <div id="footer_release" class="pv_point_gen"> Version: <strong><?php echo $ver; ?> | <a href="https://discord.gg/WxNE8kR8ug" target="_blank">Discord Server</a></strong></div>
        <br />Copyright &copy; 2017-<?php echo date('Y'); ?> </span>
      </div>
    </div>
  </footer>

<script>
$('#footer_release').click(function() {
	$('#release_notes').modal('show');
});
</script>

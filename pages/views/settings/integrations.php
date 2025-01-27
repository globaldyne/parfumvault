<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if($role !== 1){
    die('You do not have permission to access this page');
}

$bkData = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM backup_provider WHERE id = '1' AND owner_id = '$userID'"));

if($bkData['enabled']){
	$state = '<span class="card-subtitle badge badge-success ml-2">Enabled</span>';
}else{
	$state = '<span class="card-subtitle badge badge-danger ml-2">Disabled</span>';
}

if($settings['pv_scale_enabled']){
	$scaleState = '<span class="card-subtitle badge badge-success ml-2">Enabled</span>';
}else{
	$scaleState = '<span class="card-subtitle badge badge-danger ml-2">Disabled</span>';
}
?>

<h3>Integrations</h3>
<hr>
<div class="card-body" id="main_area">
	<div class="row">

		<?php
		$dir = __ROOT__ . '/integrations/';
		$integrations = array_diff(scandir($dir), array('..', '.'));

		foreach ($integrations as $integration) {
			if (is_dir($dir . $integration)) {
				$metaFile = $dir . $integration . '/meta.json';
				if (file_exists($metaFile)) {
					$metaData = json_decode(file_get_contents($metaFile), true);
					if ($metaData) {
						echo '<div class="col-sm-3">';
						echo '<div id="' . htmlspecialchars($integration) . '">';
						echo '<div class="card w-60">';
						echo '<div class="text-center mt-4">';
						echo '<i class="' . htmlspecialchars($metaData['icon']) . ' pv-fa-2xl" style="color: ' . htmlspecialchars($metaData['color']) . ';"></i>';
						echo '</div>';
						echo '<div class="card-body">';
						echo '<h5 class="card-title">' . htmlspecialchars($metaData['title']) . '<span class="badge badge-info float-right">' . htmlspecialchars($metaData['build']) . '</span></h5>';
						echo '<h6 class="card-subtitle mb-2 text-muted">version ' . htmlspecialchars($metaData['version']) . '</h6>';
						echo '<p class="card-text">' . htmlspecialchars($metaData['description']) . '</p>';
						foreach ($metaData['actions'] as $action) {
							echo '<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#' . htmlspecialchars($action['target']) . '"><i class="' . htmlspecialchars($action['icon']) . ' mx-2"></i>' . htmlspecialchars($action['label']) . '</a>';
						}
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
					}
				}
			}
		}
		?>
			
	</div>

	<?php
	foreach ($integrations as $integration) {
		if (is_dir($dir . $integration)) {
			$metaFile = $dir . $integration . '/meta.json';
			if (file_exists($metaFile)) {
				$metaData = json_decode(file_get_contents($metaFile), true);
				if ($metaData && isset($metaData['fileName'])) {
					echo file_get_contents($dir . $integration . '/' . $metaData['fileName'].'.php');
				}
			}
		}
	}
	?>
	

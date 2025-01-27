<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if($role !== 1){
    die('You do not have permission to access this page');
}


?>

<h3>Integrations</h3>
<hr>
<div class="card-body" id="main_area">
	<div class="row">

		<?php
		
		$dir = __ROOT__ . '/integrations/';
		$integrations = array_diff(scandir($dir), array('..', '.'));

		$integrationData = [];
		foreach ($integrations as $integration) {
			if (is_dir($dir . $integration)) {
				$metaFile = $dir . $integration . '/meta.json';
				if (file_exists($metaFile)) {
					$metaData = json_decode(file_get_contents($metaFile), true);
					if ($metaData) {
						$metaData['integration'] = $integration;
						$integrationData[] = $metaData;
					}
				}
			}
		}

		foreach ($integrations as $integration) {
			if (is_dir($dir . $integration)) {
				$metaFile = $dir . $integration . '/meta.json';
				if (file_exists($metaFile)) {
					$metaData = json_decode(file_get_contents($metaFile), true);
					if ($metaData && isset($metaData['fileName'])) {
						require_once($dir . $integration . '/' . $metaData['fileName'] . '.php');
					}
				}
			}
		}

		usort($integrationData, function($a, $b) {
			return $a['order_id'] <=> $b['order_id'];
		});

		foreach ($integrationData as $metaData) {
			$integration = $metaData['integration'];
			$stateVar = $metaData['slug'] . '_state';
			$state = isset($$stateVar) ? $$stateVar : '';

			echo '<div class="col-sm-3">';
			echo '<div id="' . htmlspecialchars($integration) . '">';
			echo '<div class="card w-60">';
			echo '<div class="text-center mt-4">';
			echo '<i class="' . htmlspecialchars($metaData['icon']) . ' pv-fa-2xl" style="color: ' . htmlspecialchars($metaData['color']) . ';"></i>';
			echo '</div>';
			echo '<div class="card-body">';
			echo '<h5 class="card-title">' . htmlspecialchars($metaData['title']) . (isset($state) ? $state : '') . '</h5>';
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
		?>
			
	</div>

	

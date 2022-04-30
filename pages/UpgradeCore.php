<?php 
if (!defined('pvault_panel')){ die('Not Found');}  
require_once(__ROOT__.'/func/pvCopy.php');		

$githubVer = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/VERSION.md';
$data = trim(pv_file_get_contents($githubVer));
if(trim(file_get_contents(__ROOT__.'/VERSION.md')) > $data){

	$r = '<div class="alert alert-info">PV is already in its latest version.</div>';

}else{
	if (file_exists(__ROOT__.'/tmp/') === FALSE) {
    	mkdir(__ROOT__.'/tmp/', 0740, true);
	}
	$gitHubRep = 'https://github.com/globaldyne/parfumvault/archive/refs/tags/v'.$data.'.zip';
	$tmpData = __ROOT__.'/tmp/'.$data.'.zip';
	
	if (is_writable(__ROOT__.'/VERSION.md')) {
		file_put_contents($tmpData, fopen($gitHubRep, 'r'));
		$zip = new ZipArchive;
		$res = $zip->open($tmpData);
		
		if ($res === TRUE) {
			$zip->extractTo(__ROOT__.'/tmp/CoreUpgrade/');
			$zip->close();
			pvCopy(__ROOT__.'/tmp/CoreUpgrade/parfumvault-'.$data, __ROOT__.'/');
			
			$r = '<div class="alert alert-success">Update complete</div>';
			
		}else {
			$r = '<div class="alert alert-danger">Something went wrong...</div>';
		}
	}else{
			$r = '<div class="alert alert-danger">Unable to update as home directory its not writable. Please make sure you have write permissions on your server.</div>';
	}
}
?>

<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
              <h2 class="m-0 font-weight-bold text-primary">Version Upgrade</h2>
              <p>&nbsp;</p>
            </div>
       </div> 
	   	<?=$r?>
     </div>
   </div>
  </div>

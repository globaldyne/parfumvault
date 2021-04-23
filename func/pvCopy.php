<?php
if (!defined('pvault_panel')){ die('Not Found');}

function pvCopy($src,$dst, $childFolder='') { 
	$dir = opendir($src); 
    mkdir($dst);
    if ($childFolder!='') {
        mkdir($dst.'/'.$childFolder);

        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    pvCopy($src . '/' . $file,$dst.'/'.$childFolder . '/' . $file); 
                }else{ 
                    copy($src . '/' . $file, $dst.'/'.$childFolder . '/' . $file); 
                }  
            } 
        }
    }else{
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    pvCopy($src . '/' . $file,$dst . '/' . $file); 
                }else{ 
                    copy($src . '/' . $file, $dst . '/' . $file); 
                }  
            } 
        } 
    }
    
    closedir($dir); 
}
?>

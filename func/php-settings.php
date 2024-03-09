<?php


function getMaximumFileUploadSize() {  
    return ini_get('upload_max_filesize');  
}  

function getMaximumFilePostSize() {  

	return ini_get('post_max_size');
	
}

function getMaximumFileUploadSizeRaw() {  
    return convertPHPSizeToBytes(ini_get('upload_max_filesize'));  
} 

function convertPHPSizeToBytes($sSize){
    $sSuffix = strtoupper(substr($sSize, -1));
    if (!in_array($sSuffix,array('P','T','G','M','K'))){
        return (int)$sSize;  
    } 
    $iValue = substr($sSize, 0, -1);
    switch ($sSuffix) {
        case 'P':
            $iValue *= 1024;
        case 'T':
            $iValue *= 1024;
        case 'G':
            $iValue *= 1024;
        case 'M':
            $iValue *= 1024;
        case 'K':
            $iValue *= 1024;
            break;
    }
    return (int)$iValue;
}   


function checkFunctionIsAvailable($func) {
    if (ini_get('safe_mode')) return false;
    $disabled = ini_get('disable_functions');
    if ($disabled) {
        $disabled = explode(',', $disabled);
        $disabled = array_map('trim', $disabled);
        return !in_array($func, $disabled);
    }
    return true;
}


?>
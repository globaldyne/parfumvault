<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function genBatchID($length=16) {
    $batchID = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet);

    for ($i=0; $i < $length; $i++) {
        $batchID .= $codeAlphabet[random_int(0, $max-1)];
    }

    return $batchID;
}

?>

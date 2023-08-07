<?php 

if (!defined('pvault_panel')){ die('Not Found');}


function pvPost($url, $data){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return $response = curl_exec($curl);
}
?>

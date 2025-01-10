<?php

if (!defined('pvault_panel')){ die('Not Found');}

header('Content-Type: application/json');
global $conn;

$sql = mysqli_query($conn, "SELECT * FROM IFRALibrary");

if (!$sql) {
	error_log(mysqli_error($conn));
    die(json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]));
}

$rows = array();

// Default image and color key values
$defaultImagePath = __ROOT__."/img/molecule.png";

// Loop through the result set
while ($r = mysqli_fetch_assoc($sql)) {

    // Handle the image field
    if (empty($r['image'])) {
        // Image is empty, use default image
        $r['image'] = base64_encode(file_get_contents($defaultImagePath));
    } else {
        // Split the base64 string and use it
        $img = explode('data:image/png;base64,', $r['image']);
        $r['image'] = isset($img[1]) ? $img[1] : base64_encode(file_get_contents($defaultImagePath));
    }

    // Cast id to integer
    $r['id'] = (int)$r['id'];
    $r['amendment'] = (int)$r['amendment'];
    $r['cas'] = (string)$r['cas'] ?: '-';
    $r['flavor_use'] = (string)$r['flavor_use'] ?: '-';
    
    $r['synonyms'] =  (string)$r['synonyms'] ?: '-';

    
    $r['flavor_use'] = (string)$r['flavor_use'] ?: '-';
    $r['prohibited_notes'] = (string)$r['prohibited_notes'] ?: '-';
    $r['restricted_notes'] = (string)$r['restricted_notes'] ?: '-';
    $r['specified_notes'] = (string)$r['specified_notes'] ?: '-';
    $r['type'] = (string)$r['type'] ?: '-';
    $r['contrib_others'] = (string)$r['contrib_others'] ?: '-';
    $r['contrib_others_notes'] = (string)$r['contrib_others_notes'] ?: '-';
    $r['formula'] = (string)$r['formula'] ?: '-';
    $r['restricted_photo_notes'] = (string)$r['restricted_photo_notes'] ?: '-';
    
    $r['cat1'] = (double)$r['cat1'];
    $r['cat2'] = (double)$r['cat2'];
    $r['cat3'] = (double)$r['cat3'];
    $r['cat4'] = (double)$r['cat4'];
    $r['cat5A'] = (double)$r['cat5A'];
    $r['cat5B'] = (double)$r['cat5B'];
    $r['cat5C'] = (double)$r['cat5C'];
    $r['cat5D'] = (double)$r['cat5D'];
    $r['cat6'] = (double)$r['cat6'];
    $r['cat7A'] = (double)$r['cat7A'];
    $r['cat7B'] = (double)$r['cat7B'];
    $r['cat8'] = (double)$r['cat8'];
    $r['cat9'] = (double)$r['cat9'];
    $r['cat10A'] = (double)$r['cat10A'];
    $r['cat10B'] = (double)$r['cat10B'];
    $r['cat11A'] = (double)$r['cat11A'];
    $r['cat11B'] = (double)$r['cat11B'];
    $r['cat12'] = (double)$r['cat12'];
    // Filter out empty values (if needed)
    $rows['IFRALibrary'][] = array_filter($r);
}

// Close the database connection
mysqli_close($conn);

// Set headers and output the JSON response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows, JSON_PRETTY_PRINT);
return;
?>
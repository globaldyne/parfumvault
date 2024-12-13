<?php

if (!defined('pvault_panel')){ die('Not Found');}

header('Content-Type: application/json');
global $conn;

$sql = mysqli_query($conn, "SELECT id, name, notes, image, colorKey FROM ingCategory");

if (!$sql) {
	error_log(mysqli_error($conn));
    die(json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]));
}

$rows = array();

// Default image and color key values
$defaultImagePath = __ROOT__."/img/molecule.png";
$defaultColorKey = "255, 255, 255";

// Loop through the result set
while ($r = mysqli_fetch_assoc($sql)) {
    // Ensure notes are set to "N/A" if empty
    $r['notes'] = empty($r['notes']) ? "N/A" : $r['notes'];

    // Handle the image field
    if (empty($r['image'])) {
        // Image is empty, use default image
        $r['image'] = base64_encode(file_get_contents($defaultImagePath));
    } else {
        // Split the base64 string and use it
        $img = explode('data:image/png;base64,', $r['image']);
        $r['image'] = isset($img[1]) ? $img[1] : base64_encode(file_get_contents($defaultImagePath));
    }

    // Ensure colorKey is set to default value if empty
    $r['colorKey'] = empty($r['colorKey']) ? $defaultColorKey : $r['colorKey'];

    // Cast id to integer
    $r['id'] = (int)$r['id'];

    // Filter out empty values (if needed)
    $rows['categories'][] = array_filter($r);
}

// Close the database connection
mysqli_close($conn);

// Set headers and output the JSON response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows, JSON_PRETTY_PRINT);
return;
?>
<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$id = $_GET['id'];
$type = $_GET['type'];

if ($role == 1) {
    // Admin or elevated role: no filtering applied
    $filter = "";
} else {
    // Regular user: apply filtering based on owner_id
    $filter = " AND owner_id = '$userID'";
}

// Validate input and ensure `$id` and `$type` are sanitized
$id = mysqli_real_escape_string($conn, $id);
$type = mysqli_real_escape_string($conn, $type);

switch ($type) {
    case 'internal':
        // Fetch an internal document
        $query = "SELECT name, docData FROM documents WHERE id = '$id' $filter";
        $result = mysqli_query($conn, $query);
        $q = mysqli_fetch_array($result);

        if ($q) {
            header('Content-Type: application/pdf');
            echo $q['docData'];
        } else {
            http_response_code(404);
            echo "Document not found.";
        }
        break;

    case 'sds':
        // Fetch an SDS document
        $query = "SELECT name, docData FROM documents WHERE ownerID = '$id' AND isSDS = '1' $filter";
        $result = mysqli_query($conn, $query);
        $q = mysqli_fetch_array($result);

        if ($q) {
            header('Content-Type: text/html');
            echo $q['docData'];
        } else {
            http_response_code(404);
            echo "SDS document not found.";
        }
        break;

    case 'batch':
        // Fetch a batch PDF document
        $query = "SELECT product_name, pdf FROM batchIDHistory WHERE id = '$id' $filter";
        $result = mysqli_query($conn, $query);
        $q = mysqli_fetch_array($result);

        if ($q) {
            header('Content-Type: application/pdf');
            echo base64_decode($q['pdf']);
        } else {
            http_response_code(404);
            echo "Batch document not found.";
        }
        break;

    default:
        // Default case: fetch a generic document
        $query = "SELECT name, docData FROM documents WHERE id = '$id' $filter";
        $result = mysqli_query($conn, $query);
        $q = mysqli_fetch_array($result);

        if ($q) {
            $d = explode('base64,', $q['docData']);
            $c = explode('data:', $d[0]);

            // Ensure valid content type
            $contentType = isset($c[1]) ? $c[1] : 'application/octet-stream';
            header("Content-Type: $contentType");

            // Output the decoded content
            echo base64_decode($d[1]);
        } else {
            http_response_code(404);
            echo "Document not found.";
        }
}

return;
?>

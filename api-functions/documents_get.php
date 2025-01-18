<?php

if (!defined('pvault_panel')){ die('Not Found');}

header('Content-Type: application/json');
global $conn, $userID;
// Pagination setup
$itemsPerPage = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default to 10 items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default to page 1
$page = $page > 0 ? $page : 1; // Ensure page number is positive
$offset = ($page - 1) * $itemsPerPage;

// Query to fetch paginated data
$sql = mysqli_query($conn, "SELECT id, ownerID, type, name, notes, docData, created_at, updated_at 
                            FROM documents
                            WHERE owner_id = '$userID'
                            LIMIT $itemsPerPage OFFSET $offset");

// Check if the query was successful
if (!$sql) {
    die(json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]));
}

$rows = array();

// Process the results
while ($rx = mysqli_fetch_assoc($sql)) {
    $r['id'] = (int)$rx['id'];
    $r['ownerID'] = (int)$rx['ownerID'] ?: 0;
    $r['type'] = (int)$rx['type'] ?: 0;
    $r['name'] = (string)$rx['name'] ?: "-";
    $r['notes'] = (string)$rx['notes'] ?: "-";
    $r['docData'] = (string)$rx['docData'];
    $r['created_at'] = (string)$rx['created_at'] ?: "-";
    $r['updated_at'] = (string)$rx['updated_at'] ?: "-";

    $rows['documents'][] = $r;
}

// Total records count for pagination metadata
$totalCountQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM documents");
$totalCount = mysqli_fetch_assoc($totalCountQuery)['total'];

// Pagination metadata
$rows['pagination'] = [
    'currentPage' => $page,
    'itemsPerPage' => $itemsPerPage,
    'totalItems' => (int)$totalCount,
    'totalPages' => ceil($totalCount / $itemsPerPage)
];

mysqli_close($conn);

// Output the JSON response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
return;

?>
<?php
//define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/pvPost.php');


$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$order_by = isset($_POST['order_by']) ? $_POST['order_by'] : 'name';
$order = isset($_POST['order_as']) ? $_POST['order_as'] : 'ASC';

// Trim and sanitize search input
$s = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

// Prepare data array for API request
$data = [
    'request' => 'ingredients',
    'start' => $row,
    'length' => $limit,
    'order_by' => $order_by,
    'order_as' => $order,
    'src' => 'PV_PRO',
    'search[value]' => $s
];

// Make API request and decode the JSON response
$output = json_decode(pvPost($pvLibraryAPI, $data));

// Initialize response array
$rx = [];
if (isset($output->ingredients) && is_array($output->ingredients)) {
    	foreach ($output->ingredients as $ingredient) {
			if (empty($ingredient->name)) {
			continue;
		}

        $r = [
            'id' => (int) $ingredient->id,
            'name' => (string) filter_var($ingredient->name, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'cas' => (string) ($ingredient->cas ?: 'N/A'),
            'odor' => (string) ($ingredient->odor ?: 'N/A'),
            'profile' => (string) ($ingredient->profile ?: 'Unknown'),
            'physical_state' => (int) ($ingredient->physical_state ?: 1),
            'category' => (int) ($ingredient->category ?: 0),
            'type' => (string) ($ingredient->type ?: 'N/A'),
            'IUPAC' => (string) filter_var($ingredient->INCI, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'strength' => (string) ($ingredient->strength ?: 'N/A'),
            'purity' => (float) ($ingredient->purity ?: 100),
            'FEMA' => (string) ($ingredient->FEMA ?: 'N/A'),
            'tenacity' => (string) ($ingredient->tenacity ?: 'N/A'),
            'chemical_name' => (string) ($ingredient->chemical_name ?: 'N/A'),
            'formula' => (string) ($ingredient->formula ?: 'N/A'),
            'flash_point' => (string) ($ingredient->flash_point ?: 'N/A'),
            'appearance' => (string) ($ingredient->appearance ?: 'N/A'),
            'notes' => (string) ($ingredient->notes ?: 'N/A'),
            'allergen' => (int) ($ingredient->allergen ?: 0),
            'flavor_use' => (int) ($ingredient->flavor_use ?: 0),
            'einecs' => (string) ($ingredient->einecs ?: 'N/A'),
            'usage' => [
                'limit' => (float) ($ingredient->cat4 ?: 100),
                'reason' => (string) ($ingredient->risk ?: 'N/A')
            ],
            'impact_top' => (int) ($ingredient->impact_top ?: 0),
            'impact_heart' => (int) ($ingredient->impact_heart ?: 0),
            'impact_base' => (int) ($ingredient->impact_base ?: 0),
            'stock' => 0.0, // Stock not available from online source
            'info' => [
                'byPassIFRA' => 0 // Not available from online source
            ]
        ];

        $rx[] = $r;
    }
}

// Prepare final response
$response = [
    'source' => 'PVLibrary',
    'draw' => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
    'recordsTotal' => (int) ($output->ingredientsTotal ?? 0),
    'recordsFiltered' => (int) ($output->ingredientsFiltered ?? 0),
    'data' => $rx
];

// If no data found, ensure an empty array is returned
if (empty($rx)) {
    $response['data'] = [];
}

// Send the response as JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
return;

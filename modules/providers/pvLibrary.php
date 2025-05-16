<?php
$starttime = microtime(true);

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/pvPost.php');

if ($system_settings['LIBRARY_enable'] == '0') {
    $response = [
        'draw' => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'PV Library service is currently disabled by your administrator.'
    ];
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    return;
}
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
            'cas' => (string) ($ingredient->cas ?: '-'),
            'profile' => (string) ($ingredient->profile ?: 'Unknown'),
            'physical_state' => (int) ($ingredient->physical_state ?: 1),
            'category' => (int) ($ingredient->category ?: 0),
            'type' => (string) ($ingredient->type ?: '-'),
            'IUPAC' => (string) filter_var($ingredient->INCI, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'strength' => (string) ($ingredient->strength ?: '-'),
            'purity' => (float) ($ingredient->purity ?: 100),
            'FEMA' => (string) ($ingredient->FEMA ?: '-'),
            'tenacity' => (string) ($ingredient->tenacity ?: '-'),
            'chemical_name' => (string) ($ingredient->chemical_name ?: '-'),
            'formula' => (string) ($ingredient->formula ?: '-'),
            'flash_point' => (string) ($ingredient->flash_point ?: '-'),
            'appearance' => (string) ($ingredient->appearance ?: '-'),
            'notes' => (string) ($ingredient->notes ?: '-'),
            'allergen' => (int) ($ingredient->allergen ?: 0),
            'flavor_use' => (int) ($ingredient->flavor_use ?: 0),
            'einecs' => (string) ($ingredient->einecs ?: '-'),
            'usage' => [
                'limit' => (float) ($ingredient->cat4 ?: 100),
                'reason' => (string) ($ingredient->risk ?: '-')
            ],
            'impact_top' => (int) ($ingredient->impact_top ?: 0),
            'impact_heart' => (int) ($ingredient->impact_heart ?: 0),
            'impact_base' => (int) ($ingredient->impact_base ?: 0),
            'stock' => 0.0, // Stock not available from the PVLibrary source
            'info' => [
                'byPassIFRA' => 0 // Not available from the PVLibrary source
            ],
            'labels' => $ingredient->labels ? explode(', ', $ingredient->labels) : null
        ];

        $rx[] = $r;
    }
}

// Prepare final response
$response = [
    'source' => 'PVLibrary',
    'draw' => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
    'recordsTotal' => isset($output->ingredientsTotal) ? (int) $output->ingredientsTotal : 0,
    'recordsFiltered' => (int) count(($rx)),
    'data' => $rx
];

// If no data found, ensure an empty array is returned
if (empty($rx)) {
    $response['data'] = [];
}

// Log the load time
$load_time = microtime(true) - $starttime;
$logMessage = sprintf(
    "PV Info: Ingredients PVLibrary API load time: %.4f seconds",
    $load_time
);
error_log($logMessage);

// Send the response as JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
return;

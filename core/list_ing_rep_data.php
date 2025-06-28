<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$response = ['data' => []];

if ($_POST['view'] == 'ingredients') {
    $ing_name = base64_decode($_POST["ing_name"]);
    $ing_cas = base64_decode($_POST["ing_cas"]);

    // Use prepared statement with OR for both name and rep_name
    $stmt = $conn->prepare(
        "SELECT id, 
                COALESCE(ing_rep_name, ing_name) AS ing_rep_name, 
                COALESCE(ing_rep_cas, ing_cas) AS ing_rep_cas, 
                COALESCE(notes, '-') AS notes 
         FROM ingReplacements 
         WHERE (ing_name = ? OR ing_rep_name = ?) AND owner_id = ?"
    );
    $stmt->bind_param('sss', $ing_name, $ing_name, $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $r = [
            'id' => (int)$row['id'],
            'ing_rep_name' => (string)$row['ing_rep_name'],
            'ing_rep_cas' => (string)$row['ing_rep_cas'],
            'notes' => (string)$row['notes'],
        ];
        $response['data'][] = $r;
    }
    $stmt->close();
}

if ($_POST['view'] == 'formula') {
    $fid = $_POST["fid"];

    // Get all ingredients and their IDs for the formula in one query
    $stmt = $conn->prepare(
        "SELECT ingredient, ingredient_id FROM formulas WHERE fid = ? AND owner_id = ?"
    );
    $stmt->bind_param('ss', $fid, $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    $ingredients = [];
    $ingredient_ids = [];
    while ($row = $result->fetch_assoc()) {
        $ingredients[] = $row['ingredient'];
        $ingredient_ids[$row['ingredient']] = (int)$row['ingredient_id'];
    }
    $stmt->close();

    if ($ingredients) {
        // Prepare placeholders for IN clause
        $in = str_repeat('?,', count($ingredients) - 1) . '?';
        $types = str_repeat('s', count($ingredients));
        $params = $ingredients;

        // Fetch all replacements for these ingredients in one query
        $sql = "SELECT id, ing_name, ing_rep_name, ing_rep_id, ing_rep_cas, notes FROM ingReplacements WHERE ing_name IN ($in) AND owner_id = ?";
        $types .= 's';
        $params[] = $userID;

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $replacements = [];
        while ($row = $result->fetch_assoc()) {
            $row['ingredient_id'] = $ingredient_ids[$row['ing_name']];
            $replacements[] = $row;
        }
        $stmt->close();

        // Get notes for all ingredient_ids in one query
        $id_in = str_repeat('?,', count($ingredient_ids) - 1) . '?';
        $id_types = str_repeat('i', count($ingredient_ids));
        $id_params = array_values($ingredient_ids);

        $notes_map = [];
        $sql = "SELECT id, notes FROM ingredients WHERE id IN ($id_in)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($id_types, ...$id_params);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $notes_map[$row['id']] = $row['notes'];
        }
        $stmt->close();

        foreach ($replacements as $get_rep_ing) {
            $ingredient_id = (int)$get_rep_ing['ingredient_id'];
            $r = [
                'original_id' => $ingredient_id,
                'replacement_id' => (int)$get_rep_ing['ing_rep_id'],
                'ing_name' => (string)$get_rep_ing['ing_name'],
                'ing_rep_name' => (string)$get_rep_ing['ing_rep_name'],
                'notes' => (string)$get_rep_ing['notes'] ?: ($notes_map[$ingredient_id] ?? 'No information available'),
            ];
            $response['data'][] = $r;
        }
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

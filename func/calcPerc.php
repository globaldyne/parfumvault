<?php
if (!defined('pvault_panel')){ die('Not Found');}

function calcPerc($id, $profile, $percent, $conn){
    global $userID;

    $stmt = $conn->prepare("SELECT fid FROM formulasMetaData WHERE id = ? AND owner_id = ?");
    $stmt->bind_param("is", $id, $userID);
    $stmt->execute();
    $stmt->bind_result($fid);
    $stmt->fetch();
    $stmt->close();

    if ($fid) {
        $stmt = $conn->prepare("
            SELECT i.profile 
            FROM formulas f 
            JOIN ingredients i ON f.ingredient = i.name 
            WHERE f.fid = ? AND f.owner_id = ? AND i.owner_id = ?
        ");
        $stmt->bind_param("sss", $fid, $userID, $userID);
        $stmt->execute();
        $formula_q = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if ($formula_q) {
            $prf = array_column($formula_q, 'profile'); // Extract profile values
            $countValues = array_count_values($prf);
            return isset($countValues[$profile]) ? ($countValues[$profile] / $percent) * 100 : 0;
        }
    }
    return 0;
}

function multi_dim_search($array, $key, $value) {
    $results = [];

    foreach ($array as $subarray) {
        if (is_array($subarray) && isset($subarray[$key]) && $subarray[$key] == $value) {
            $results[] = $subarray;
        }
    }
    return $results;
}

function multi_dim_perc($conn, $form, $ingCas, $qStep, $defPercentage) {
    global $userID;
    $conc = [];

    $stmt = $conn->prepare("SELECT cas, $defPercentage FROM ingredient_compounds WHERE ing = ? AND owner_id = ?");
    $stmt->bind_param("ss", $ingCas, $userID);
    $stmt->execute();
    $compos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($compos as $a) {
        $cas = $a['cas'];
        $conc[$cas] = isset($conc[$cas]) ? $conc[$cas] : 0;
        $conc[$cas] += number_format(($a[$defPercentage] / 100) * ($form['quantity'] * $form['concentration'] / 100), $qStep);
    }

    return $conc;
}

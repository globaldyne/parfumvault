<?php
if (!defined('pvault_panel')){ die('Not Found');}
global $conn, $userID;

function jsonResponse($success, $message) {
	header('Content-Type: application/json;');
	echo json_encode(['success' => $success, 'message' => $message]);
	exit;
}

function validateRequestParams($params) {
	foreach ($params as $param) {
		if (empty($_REQUEST[$param])) {
			jsonResponse(false, "Missing required param: $param");
		}
	}
}

if ($_REQUEST['action'] == 'skipMaterial') {
	validateRequestParams(['fid', 'id', 'ingId', 'notes']);

	$fid = mysqli_real_escape_string($conn, $_REQUEST['fid']);
	$id = mysqli_real_escape_string($conn, $_REQUEST['id']);
	$ingID = mysqli_real_escape_string($conn, $_REQUEST['ingId']);
	$notes = mysqli_real_escape_string($conn, $_REQUEST['notes']) ?: "-";

	if (mysqli_query($conn, "UPDATE makeFormula SET skip = '1', notes = '$notes' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")) {
		file_put_contents($tmp_path.'reload_signal.txt', 'reload');
		jsonResponse(true, $_REQUEST['ing'].' skipped from the formulation');
	} else {
		jsonResponse(false, 'Error skipping the ingredient');
	}
}

if ($_REQUEST['do'] == 'callback' && $_REQUEST['action'] == 'makeFormula') {
	validateRequestParams(['fid', 'id', 'ingId', 'qr', 'q']);

	$fid = mysqli_real_escape_string($conn, $_REQUEST['fid']);
	$id = mysqli_real_escape_string($conn, $_REQUEST['id']);
	$ingID = mysqli_real_escape_string($conn, $_REQUEST['ingId']);
	$qr = trim($_REQUEST['qr']);
	$q = trim($_REQUEST['q']);
	$notes = mysqli_real_escape_string($conn, $_REQUEST['notes']);

	if (!is_numeric($q)) {
		jsonResponse(false, 'Invalid quantity value');
	}

	if ((double)$q == 0.00) {
		jsonResponse(false, 'Please add quantity');
	}

	if ($_REQUEST['updateStock'] == "true") {
		$getStock = mysqli_fetch_array(mysqli_query($conn, "SELECT stock, mUnit FROM suppliers WHERE ingID = '$ingID' AND preferred = '1' AND owner_id = '$userID'"));
		if ($getStock['stock'] < $q) {
			$q = $getStock['stock'];
		}
		mysqli_query($conn, "UPDATE suppliers SET stock = stock - $q WHERE ingID = '$ingID' AND preferred = '1' AND owner_id = '$userID'");
		jsonResponse(true, "Stock deducted by ".$q.$settings['mUnit']);
	}

	if ($qr == $q) {
		if (mysqli_query($conn, "UPDATE makeFormula SET toAdd = '0' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")) {
			jsonResponse(true, "Ingredient added");
		}
	} else {
		$sub_tot = $qr - $q;
		if (mysqli_query($conn, "UPDATE makeFormula SET quantity='$sub_tot' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")) {
			jsonResponse(true, "Quantity updated ($q)");
		}
	}

	if ($notes) {
		$notes = "Formula make, ingredient: ".$_REQUEST['ing']."\\n";
		mysqli_query($conn, "UPDATE formulasMetaData SET notes = CONCAT(notes, '".$notes."') WHERE fid = '$fid' AND owner_id = '$userID'");
	}

	if ($qr < $q) {
		if (mysqli_query($conn, "UPDATE makeFormula SET overdose = '$q' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")) {
			jsonResponse(true, $_REQUEST['ing'].' is overdosed, '.$q.' added');
		}
	}

	if (!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1' AND owner_id = '$userID'"))) {
		jsonResponse(true, 'All materials added. You should mark formula as complete now');
	}

	file_put_contents($tmp_path.'reload_signal.txt', 'reload');
	jsonResponse(true, 'Operation completed successfully');
}
?>

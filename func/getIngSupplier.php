<?php 
if (!defined('pvault_panel')){ die('Not Found');}

// Function to get suppliers or stock for an ingredient
function getIngSupplier($ingID, $getStock, $conn) {
    $ingID = (int)$ingID;  // Ensure $ingID is an integer for security

    if ($getStock == 1) {
        $stmt = $conn->prepare("SELECT mUnit, SUM(stock) AS stock FROM suppliers WHERE ingID = ?");
        $stmt->bind_param('i', $ingID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
    } else {
        $stmt = $conn->prepare("SELECT ingSupplierID, supplierLink, status FROM suppliers WHERE ingID = ?");
        $stmt->bind_param('i', $ingID);
        $stmt->execute();
        $result = [];

        $queryResult = $stmt->get_result();
        while ($r = $queryResult->fetch_assoc()) {
            $supplierInfo = getSupplierByID($r['ingSupplierID'], $conn);
            if ($supplierInfo) {
                $result[] = array_merge($r, $supplierInfo);
            }
        }
    }

    return $result ?: null;
}

// Function to get preferred supplier for an ingredient
function getPrefSupplier($ingID, $conn) {
    $ingID = (int)$ingID;  // Ensure $ingID is an integer for security

    $stmt = $conn->prepare("SELECT price, ingSupplierID, size, supplierLink FROM suppliers WHERE ingID = ? AND preferred = '1'");
    $stmt->bind_param('i', $ingID);
    $stmt->execute();
    $ing = $stmt->get_result()->fetch_assoc();

    if ($ing) {
        $supplierInfo = getSupplierByID($ing['ingSupplierID'], $conn);
        return array_merge($ing, $supplierInfo);
    }

    return null;
}

// Function to get single supplier's price for an ingredient
function getSingleSupplier($sID, $ingID, $conn) {
    $sID = (int)$sID;
    $ingID = (int)$ingID;

    $stmt = $conn->prepare("SELECT price FROM suppliers WHERE ingSupplierID = ? AND ingID = ?");
    $stmt->bind_param('ii', $sID, $ingID);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Helper function to get supplier name by supplier ID
function getSupplierByID($sID, $conn) {
    $sID = (int)$sID;

    $stmt = $conn->prepare("SELECT name FROM ingSuppliers WHERE id = ?");
    $stmt->bind_param('i', $sID);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


?>

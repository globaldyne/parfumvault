<?php 
if (!defined('pvault_panel')){ die('Not Found');}


function compareFormula(array $formula_a, array $formula_b, array $keysToCompare = null, $formula_a_name, $formula_b_name) {
    $serialize = function (&$item) use ($keysToCompare) {
        if (is_array($item) && $keysToCompare) {
            $item = array_intersect_key($item, array_flip($keysToCompare));
        }
        $item = serialize($item);
    };

    $deserialize = function (&$item) {
        $item = unserialize($item);
    };

    array_walk($formula_a, $serialize);
    array_walk($formula_b, $serialize);

    $diff_a = array_diff($formula_a, $formula_b);
    $diff_b = array_diff($formula_b, $formula_a);

    array_walk($diff_a, $deserialize);
    array_walk($diff_b, $deserialize);

    if (!empty($diff_a) || !empty($diff_b)) {
        return array($formula_a_name => $diff_a, $formula_b_name => $diff_b);
    }

    return null;
}
?>

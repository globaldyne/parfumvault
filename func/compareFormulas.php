<?php 
if (!defined('pvault_panel')){ die('Not Found');}


function compareFormula(array $formula_a, array $formula_b, array $keysToCompare = null, $formula_a_name, $formula_b_name) {
   $serialize = function (&$item, $idx, $keysToCompare) {
       if (is_array($item) && $keysToCompare) {
           $a = array();
           foreach ($keysToCompare as $k) {
               if (array_key_exists($k, $item)) {
                   $a[$k] = $item[$k];
               }
           }
           $item = $a;
       }
       $item = serialize($item);
   };

   $deserialize = function (&$item) {
       $item = unserialize($item);
   };

   array_walk($formula_a, $serialize, $keysToCompare);
   array_walk($formula_b, $serialize, $keysToCompare);

   $diff_b = array_diff($formula_a, $formula_b);
   $diff_a = array_diff($formula_b, $formula_a);

   array_walk($diff_a, $deserialize);
   array_walk($diff_b, $deserialize);

   return array($formula_b_name => $diff_a, $formula_a_name => $diff_b);
}
?>
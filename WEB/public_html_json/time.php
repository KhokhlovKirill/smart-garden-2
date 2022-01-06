<?php
function shift_in_left (&$arr) {
 $item = array_shift($arr);
 array_push ($arr,$item);
}

function shift_in_right (&$arr) {
 $item = array_pop($arr);
 array_unshift ($arr,$item);
}

$arr = array(1, 2, 3, 4);
shift_in_left ($arr);
print_r($arr); //сдвинуть влево и показать
?>
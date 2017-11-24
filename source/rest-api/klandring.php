<?php
// switch ($_SERVER['REQUEST_METHOD']) {
//     case 'GET':
//     case 'PUT':
//     case 'POST':
//     case 'DELETE':
//         break;
// }
header("status: 200");
header("Content-Type: text/json; charset=UTF-8");
echo $_SERVER["HTTP_AUTHORIZATION"];
$input = file_get_contents('php://input');
echo "$input<br>";
$row = get_klandring_from_id($resources["kid"]);
echo json_encode($row);
?>
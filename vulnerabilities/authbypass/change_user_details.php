<?php
define( 'DVWA_WEB_PAGE_TO_ROOT', '../../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaDatabaseConnect();

/*
Only the admin is allowed to change user details — enforced on EVERY security
level, so this endpoint can't be called directly to bypass the UI's auth.
*/

if (dvwaCurrentUser() != "admin") {
	print json_encode (array ("result" => "fail", "error" => "Access denied"));
	http_response_code(403);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] != "POST") {
	$result = array (
						"result" => "fail",
						"error" => "Only POST requests are accepted"
					);
	echo json_encode($result);
	exit;
}

try {
	$json = file_get_contents('php://input');
	$data = json_decode($json);
	if (is_null ($data)) {
		$result = array (
							"result" => "fail",
							"error" => 'Invalid format, expecting "{id: {user ID}, first_name: "{first name}", surname: "{surname}"}'

						);
		echo json_encode($result);
		exit;
	}
} catch (Exception $e) {
	$result = array (
						"result" => "fail",
						"error" => 'Invalid format, expecting \"{id: {user ID}, first_name: "{first name}", surname: "{surname}\"}'

					);
	echo json_encode($result);
	exit;
}

// Validate the id and bind every value as a parameter (no SQL injection).
if (!isset($data->id) || !is_numeric($data->id) || !isset($data->first_name) || !isset($data->surname)) {
	print json_encode (array ("result" => "fail", "error" => "Invalid input"));
	exit;
}
$user_id = (int)$data->id;
$first_name = (string)$data->first_name;
$surname = (string)$data->surname;

$query = "UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ?";
$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], $query);
mysqli_stmt_bind_param($stmt, "ssi", $first_name, $surname, $user_id);
mysqli_stmt_execute($stmt) or die( '<pre>' . mysqli_stmt_error($stmt) . '</pre>' );
mysqli_stmt_close($stmt);

print json_encode (array ("result" => "ok"));
exit;
?>

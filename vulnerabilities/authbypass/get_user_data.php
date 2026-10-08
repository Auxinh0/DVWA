<?php
define( 'DVWA_WEB_PAGE_TO_ROOT', '../../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaDatabaseConnect();

/*
Only the admin is allowed to retrieve user data — enforced on EVERY security
level, so a non-admin can't call this endpoint directly to bypass auth.
*/
if (dvwaCurrentUser() != "admin") {
	print json_encode (array ("result" => "fail", "error" => "Access denied"));
	http_response_code(403);
	exit;
}

$query  = "SELECT user_id, first_name, last_name FROM users";
$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query );

$guestbook = ''; 
$users = array();

while ($row = mysqli_fetch_row($result) ) {
	// Always output-encode the values, regardless of security level.
	$user_id = $row[0];
	$first_name = htmlspecialchars( $row[1] );
	$surname = htmlspecialchars( $row[2] );

	$user = array (
					"user_id" => $user_id,
					"first_name" => $first_name,
					"surname" => $surname
				);
	$users[] = $user;
}

print json_encode ($users);
exit;
?>

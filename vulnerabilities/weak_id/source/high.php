<?php

$html = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	// Generate an unpredictable, cryptographically strong session id instead
	// of md5() over a guessable incrementing counter.
	$cookie_value = bin2hex( random_bytes( 20 ) );
	setcookie("dvwaSession", $cookie_value, time()+3600, "/vulnerabilities/weak_id/", $_SERVER['HTTP_HOST'], false, true);
}

?>

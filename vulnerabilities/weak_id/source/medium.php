<?php

$html = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	// Generate an unpredictable, cryptographically strong session id instead
	// of a predictable time()-based value.
	$cookie_value = bin2hex( random_bytes( 20 ) );
	setcookie("dvwaSession", $cookie_value);
}
?>

<?php
header("Content-Type: application/json; charset=UTF-8");

if (array_key_exists ("callback", $_GET)) {
	$callback = $_GET['callback'];
} else {
	return "";
}

// The callback is reflected into a script response, so it must be a bare
// identifier only. Anything else (parentheses, operators, markup) would let
// an attacker turn this same-origin endpoint into arbitrary executable JS.
if (!preg_match('/^[A-Za-z0-9_]+$/', $callback)) {
	header("HTTP/1.1 400 Bad Request");
	echo json_encode(array("error" => "Invalid callback"));
	return;
}

$outp = array ("answer" => "15");

echo $callback . "(".json_encode($outp).")";
?>

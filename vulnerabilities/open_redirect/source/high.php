<?php

// The old check only required the string "info.php" to appear somewhere, which
// can be bypassed with "https://evil.com/info.php" or "https://evil.com?info.php".
// Use an explicit allowlist of known-safe, relative targets instead.
$allowed_targets = array( 'info.php?id=1', 'info.php?id=2' );

if (array_key_exists ("redirect", $_GET) && $_GET['redirect'] != "") {
	if (in_array ($_GET['redirect'], $allowed_targets, true)) {
		header ("location: " . $_GET['redirect']);
		exit;
	}

	http_response_code (500);
	?>
	<p>You can only redirect to the info page.</p>
	<?php
	exit;
}

http_response_code (500);
?>
<p>Missing redirect target.</p>
<?php
exit;
?>

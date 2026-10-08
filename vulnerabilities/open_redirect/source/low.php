<?php

// Only redirect to an explicit allowlist of known-safe, relative targets. Any
// absolute or external URL (or anything not on the list) is rejected, which
// closes the open redirect.
$allowed_targets = array( 'info.php?id=1', 'info.php?id=2' );

if (array_key_exists ("redirect", $_GET) && $_GET['redirect'] != "") {
	if (in_array ($_GET['redirect'], $allowed_targets, true)) {
		header ("location: " . $_GET['redirect']);
		exit;
	}

	http_response_code (500);
	?>
	<p>Invalid redirect target.</p>
	<?php
	exit;
}

http_response_code (500);
?>
<p>Missing redirect target.</p>
<?php
exit;
?>

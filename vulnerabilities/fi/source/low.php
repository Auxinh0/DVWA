<?php

// The page we wish to display
$file = $_GET[ 'page' ];

// Only allow an explicit allowlist of known pages. A blacklist of "../" or
// "http://" can always be bypassed, so instead reject anything that is not an
// exact match for one of the intended pages (no path traversal, no remote
// includes, no absolute paths).
$allowed_pages = array( 'include.php', 'file1.php', 'file2.php', 'file3.php' );

if( !in_array( $file, $allowed_pages, true ) ) {
	// This isn't the page we want!
	echo "ERROR: File not found!";
	exit;
}

?>

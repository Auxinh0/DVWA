<?php

// The page we wish to display
$file = $_GET[ 'page' ];

// Only allow an explicit allowlist of known pages. The old str_replace
// blacklist could be defeated (e.g. "....//" or absolute paths), so reject
// anything that is not an exact match for one of the intended pages.
$allowed_pages = array( 'include.php', 'file1.php', 'file2.php', 'file3.php' );

if( !in_array( $file, $allowed_pages, true ) ) {
	// This isn't the page we want!
	echo "ERROR: File not found!";
	exit;
}

?>

<?php

// The page we wish to display
$file = $_GET[ 'page' ];

// Only allow an explicit allowlist of known pages. The old fnmatch( "file*" )
// check still allowed things like "file:///etc/passwd" or "fileXYZ"; require an
// exact match against the intended pages instead.
$allowed_pages = array( 'include.php', 'file1.php', 'file2.php', 'file3.php' );

if( !in_array( $file, $allowed_pages, true ) ) {
	// This isn't the page we want!
	echo "ERROR: File not found!";
	exit;
}

?>

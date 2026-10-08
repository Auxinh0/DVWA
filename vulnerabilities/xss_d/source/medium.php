<?php

// Is there any input?
if ( array_key_exists( "default", $_GET ) && !is_null ($_GET[ 'default' ]) ) {

	# White list the allowable languages -- a "<script" blacklist is trivially
	# bypassed (other tags/event handlers), so only known values are accepted.
	switch ($_GET['default']) {
		case "French":
		case "English":
		case "German":
		case "Spanish":
			# ok
			break;
		default:
			header ("location: ?default=English");
			exit;
	}
}

?>

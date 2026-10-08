<?php

if( isset( $_POST[ 'Submit'  ] ) ) {
	// Get input
	$target = $_REQUEST[ 'ip' ];
	$target = stripslashes( $target );

	// Split the IP into 4 octets
	$octet = explode( ".", $target );

	// A shell-metacharacter blacklist can always be bypassed, so validate the
	// input against what a real IPv4 address looks like (four integer octets)
	// and only then hand it to the shell as a single, quoted argument.
	if( ( sizeof( $octet ) == 4 ) && is_numeric( $octet[0] ) && is_numeric( $octet[1] ) && is_numeric( $octet[2] ) && is_numeric( $octet[3] ) ) {
		// Put the validated IP back together.
		$target = $octet[0] . '.' . $octet[1] . '.' . $octet[2] . '.' . $octet[3];

		// Determine OS and execute the ping command.
		if( stristr( php_uname( 's' ), 'Windows NT' ) ) {
			// Windows
			$cmd = shell_exec( 'ping  ' . escapeshellarg( $target ) );
		}
		else {
			// *nix
			$cmd = shell_exec( 'ping  -c 4 ' . escapeshellarg( $target ) );
		}

		// Feedback for the end user
		$html .= "<pre>{$cmd}</pre>";
	}
	else {
		// Ops. Let the user know there is a mistake
		$html .= '<pre>ERROR: You have entered an invalid IP.</pre>';
	}
}

?>

<?php

if( isset( $_GET[ 'Login' ] ) && isset( $_GET[ 'username' ] ) && isset( $_GET[ 'password' ] ) ) {
	// Check Anti-CSRF token
	checkToken( $_REQUEST[ 'user_token' ], $_SESSION[ 'session_token' ], 'index.php' );

	// Get username
	$user = $_GET[ 'username' ];
	$user = stripslashes( $user );

	// Get password
	$pass = $_GET[ 'password' ];
	$pass = stripslashes( $pass );
	$pass = md5( $pass );

	// Brute-force protection settings
	$total_failed_login = 3;
	$lockout_time       = 15;
	$account_locked     = false;

	// Check to see if the user has been locked out (parameterised query).
	$data = $db->prepare( 'SELECT failed_login, last_login FROM users WHERE user = (:user) LIMIT 1;' );
	$data->bindParam( ':user', $user, PDO::PARAM_STR );
	$data->execute();
	$row = $data->fetch();

	if( ( $data->rowCount() == 1 ) && ( $row[ 'failed_login' ] >= $total_failed_login ) ) {
		$last_login = strtotime( $row[ 'last_login' ] );
		$timeout    = $last_login + ( $lockout_time * 60 );
		if( time() < $timeout ) {
			$account_locked = true;
		}
	}

	// Check the credentials (parameterised query - no SQL injection).
	$data = $db->prepare( 'SELECT * FROM users WHERE user = (:user) AND password = (:password) LIMIT 1;' );
	$data->bindParam( ':user', $user, PDO::PARAM_STR );
	$data->bindParam( ':password', $pass, PDO::PARAM_STR );
	$data->execute();
	$row = $data->fetch();

	if( ( $data->rowCount() == 1 ) && ( $account_locked == false ) ) {
		// Get users details
		$avatar = $row[ 'avatar' ];

		// Login successful
		$html .= "<p>Welcome to the password protected area {$user}</p>";
		$html .= "<img src=\"{$avatar}\" />";

		// Reset bad login count
		$data = $db->prepare( 'UPDATE users SET failed_login = "0" WHERE user = (:user) LIMIT 1;' );
		$data->bindParam( ':user', $user, PDO::PARAM_STR );
		$data->execute();
	}
	else {
		// Login failed - slow the attacker down and count the failure
		sleep( rand( 2, 4 ) );
		$html .= "<pre><br />Username and/or password incorrect.</pre>";

		$data = $db->prepare( 'UPDATE users SET failed_login = (failed_login + 1) WHERE user = (:user) LIMIT 1;' );
		$data->bindParam( ':user', $user, PDO::PARAM_STR );
		$data->execute();
	}

	// Record the time of this login attempt
	$data = $db->prepare( 'UPDATE users SET last_login = now() WHERE user = (:user) LIMIT 1;' );
	$data->bindParam( ':user', $user, PDO::PARAM_STR );
	$data->execute();
}

// Generate Anti-CSRF token
generateSessionToken();

?>

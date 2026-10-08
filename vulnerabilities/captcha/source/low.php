<?php

if( isset( $_POST[ 'Change' ] ) ) {
	// Hide the CAPTCHA form
	$hide_form = true;

	// Get input
	$pass_new  = $_POST[ 'password_new' ];
	$pass_conf = $_POST[ 'password_conf' ];

	// Check CAPTCHA from 3rd party. The CAPTCHA must genuinely pass on every
	// password change - there is no multi-step flow to skip and no client
	// supplied "already passed" flag to trust.
	$resp = recaptcha_check_answer(
		$_DVWA[ 'recaptcha_private_key'],
		$_POST['g-recaptcha-response']
	);

	// Did the CAPTCHA fail?
	if( !$resp ) {
		// What happens when the CAPTCHA was entered incorrectly
		$html     .= "<pre><br />The CAPTCHA was incorrect. Please try again.</pre>";
		$hide_form = false;
	}
	else {
		// CAPTCHA was correct. Do both new passwords match?
		if( $pass_new == $pass_conf ) {
			$pass_new = md5( $pass_new );

			// Update the database with a parameterised query
			$data = $db->prepare( 'UPDATE users SET password = (:password) WHERE user = (:user);' );
			$data->bindParam( ':password', $pass_new, PDO::PARAM_STR );
			$user = dvwaCurrentUser();
			$data->bindParam( ':user', $user, PDO::PARAM_STR );
			$data->execute();

			// Feedback for the end user
			$html .= "<pre>Password Changed.</pre>";
		}
		else {
			// Both new passwords do not match.
			$html     .= "<pre>Both passwords must match.</pre>";
			$hide_form = false;
		}
	}
}

?>

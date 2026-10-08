<?php

// Strong, authenticated encryption replaces the trivially-reversible XOR
// cipher. A short repeating-key XOR leaks the plaintext to known-plaintext /
// frequency analysis; AES-256-GCM does not.
function crypto_key( $key ) {
	return hash( 'sha256', $key, true );
}

function encode_message( $cleartext, $key ) {
	$iv  = random_bytes( 12 );
	$tag = '';
	$ct  = openssl_encrypt( $cleartext, 'aes-256-gcm', crypto_key( $key ), OPENSSL_RAW_DATA, $iv, $tag );
	if ( $ct === false ) {
		return '';
	}
	return $iv . $tag . $ct;
}

function decode_message( $blob, $key ) {
	$iv  = substr( $blob, 0, 12 );
	$tag = substr( $blob, 12, 16 );
	$ct  = substr( $blob, 28 );
	if ( strlen( $iv ) != 12 || strlen( $tag ) != 16 ) {
		return false;
	}
	return openssl_decrypt( $ct, 'aes-256-gcm', crypto_key( $key ), OPENSSL_RAW_DATA, $iv, $tag );
}

$key = "wachtwoord";

$errors = "";
$success = "";
$messages = "";
$encoded = null;
$encode_radio_selected = " checked='checked' ";
$decode_radio_selected = " ";
$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	try {
		if (array_key_exists ('message', $_POST)) {
			$message = $_POST['message'];
			if (array_key_exists ('direction', $_POST) && $_POST['direction'] == "decode") {
				$encoded = decode_message (base64_decode ($message), $key);
				if ($encoded === false) {
					$encoded = "";
					$errors = "Could not decode message.";
				}
				$encode_radio_selected = " ";
				$decode_radio_selected = " checked='checked' ";
			} else {
				$encoded = base64_encode(encode_message ($message, $key));
			}
		}
		if (array_key_exists ('password', $_POST)) {
			$password = $_POST['password'];
			if ($password == "Olifant") {
				$success = "Welcome back user";
			} else {
				$errors = "Login Failed";
			}
		}
	} catch(Exception $e) {
		$errors = $e->getMessage();
	}
}

$html = "
		<p>
		This super secure system will allow you to exchange messages with your friends without anyone else being able to read them. Use the box below to encode and decode messages.
		</p>
		<form name=\"xor\" method='post' action=\"" . $_SERVER['PHP_SELF'] . "\">
			<p>
				<label for='message'>Message:</lable><br />
				<textarea style='width: 600px; height: 56px' id='message' name='message'>" . htmlentities ($message) . "</textarea>
			</p>
			<p>
				<input type='radio' value='encode' name='direction' id='direction_encode' " . $encode_radio_selected . "><label for='direction_encode'>Encode</label> or
				<input type='radio' value='decode' name='direction' id='direction_decode' " . $decode_radio_selected . "><label for='direction_decode'>Decode</label>
			</p>
			<p>
				<input type=\"submit\" value=\"Submit\">
			</p>
		</form>
";

if (!is_null ($encoded)) {
	$html .= "
			<p>
				<label for='encoded'>Message:</lable><br />
				<textarea readonly='readonly' style='width: 600px; height: 56px' id='encoded' name='encoded'>" . htmlentities ($encoded) . "</textarea>
			</p>";
}

$html .= "
		<hr>
		<p>
		You have intercepted the following message, decode it and log in below.
		</p>
		<p>
		<textarea readonly='readonly' style='width: 600px; height: 28px' id='encoded' name='encoded'>Lg4WGlQZChhSFBYSEB8bBQtPGxdNQSwEHREOAQY=</textarea>
		</p>
";

if ($errors != "") {
	$html .= '<div class="warning">' . $errors . '</div>';
}

if ($messages != "") {
	$html .= '<div class="nearly">' . $messages . '</div>';
}

if ($success != "") {
	$html .= '<div class="success">' . $success . '</div>';
}

$html .= "
		<form name=\"ecb\" method='post' action=\"" . $_SERVER['PHP_SELF'] . "\">
			<p>
				<label for='password'>Password:</lable><br />
<input type='password' id='password' name='password'>
			</p>
			<p>
				<input type=\"submit\" value=\"Login\">
			</p>
		</form>
";
?>

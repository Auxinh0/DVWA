<?php

if( isset( $_GET[ 'Submit' ] ) ) {
	// Get input
	$id = $_GET[ 'id' ];
	$exists = false;

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			// Parameterized query: the id is bound as data, never concatenated
			// into the SQL string, so it can't alter the query (no blind SQLi).
			$exists = false;
			try {
				$stmt = mysqli_prepare( $GLOBALS["___mysqli_ston"], "SELECT first_name, last_name FROM users WHERE user_id = ?" );
				mysqli_stmt_bind_param( $stmt, "s", $id );
				mysqli_stmt_execute( $stmt );
				$result = mysqli_stmt_get_result( $stmt );
				$exists = ($result !== false && mysqli_num_rows( $result ) > 0);
				mysqli_stmt_close( $stmt );
			} catch (Exception $e) {
				$exists = false;
			}
			((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
			break;
		case SQLITE:
			global $sqlite_db_connection;

			try {
				$stmt = $sqlite_db_connection->prepare( "SELECT first_name, last_name FROM users WHERE user_id = :id" );
				$stmt->bindValue( ':id', $id, SQLITE3_TEXT );
				$results = $stmt->execute();
				$row = $results->fetchArray();
				$exists = $row !== false;
			} catch(Exception $e) {
				$exists = false;
			}
			break;
	}

	if ($exists) {
		// Feedback for end user
		$html .= '<pre>User ID exists in the database.</pre>';
	} else {
		// User wasn't found, so the page wasn't!
		header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 404 Not Found' );

		// Feedback for end user
		$html .= '<pre>User ID is MISSING from the database.</pre>';
	}

}

?>

<?php

if( isset( $_COOKIE[ 'id' ] ) ) {
	// Get input
	$id = $_COOKIE[ 'id' ];
	$exists = false;

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			// Parameterized query: the id is bound as data, never concatenated
			// into the SQL string (no blind SQL injection).
			$exists = false;
			try {
				$stmt = mysqli_prepare( $GLOBALS["___mysqli_ston"], "SELECT first_name, last_name FROM users WHERE user_id = ? LIMIT 1" );
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
				$stmt = $sqlite_db_connection->prepare( "SELECT first_name, last_name FROM users WHERE user_id = :id LIMIT 1" );
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
		$html .= '<pre>User ID exists in the database.</pre>';
	} else {
		header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 404 Not Found' );
		$html .= '<pre>User ID is MISSING from the database.</pre>';
	}
}

?>

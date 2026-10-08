<?php

if( isset( $_SESSION [ 'id' ] ) ) {
	// Get input
	$id = $_SESSION[ 'id' ];

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			// Parameterized query: the id is bound as data, never concatenated
			// into the SQL string, so it can't alter the query (no SQL injection).
			$stmt = mysqli_prepare( $GLOBALS["___mysqli_ston"], "SELECT first_name, last_name FROM users WHERE user_id = ? LIMIT 1" );
			mysqli_stmt_bind_param( $stmt, "s", $id );
			mysqli_stmt_execute( $stmt );
			$result = mysqli_stmt_get_result( $stmt );

			// Get results
			while( $row = mysqli_fetch_assoc( $result ) ) {
				// Get values
				$first = $row["first_name"];
				$last  = $row["last_name"];

				// Feedback for end user
				$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
			}

			mysqli_stmt_close( $stmt );
			((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
			break;
		case SQLITE:
			global $sqlite_db_connection;

			$stmt = $sqlite_db_connection->prepare( "SELECT first_name, last_name FROM users WHERE user_id = :id LIMIT 1" );
			$stmt->bindValue( ':id', $id, SQLITE3_TEXT );
			$results = $stmt->execute();
			if ($results) {
				while ($row = $results->fetchArray()) {
					$first = $row["first_name"];
					$last  = $row["last_name"];
					$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
				}
			}
			break;
	}
}

?>

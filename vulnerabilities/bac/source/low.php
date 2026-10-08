<?php
if (!defined('DVWA_WEB_PAGE_TO_ROOT')) {
    define('DVWA_WEB_PAGE_TO_ROOT', '../../../');
}

// Get the currently authenticated user's ID from the server side (session),
// never from a client-supplied cookie.
$query = "SELECT user_id, role FROM users WHERE user = '" . dvwaCurrentUser() . "';";
$result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
$row = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result) : array('user_id' => 0, 'role' => '');
$current_user_id = intval($row['user_id']);
$role = $row['role'];

$html = "";
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    if (!preg_match('/^\d+$/', $_GET['user_id'])) {
        $html .= "<p>Invalid user ID format. Please enter a number.</p>";
    } else {
        $id = intval($_GET['user_id']);

        // Check if user exists first
        $check_query = "SELECT user_id FROM users WHERE user_id = $id";
        $check_result = mysqli_query($GLOBALS["___mysqli_ston"], $check_query);
        $user_exists = ($check_result && mysqli_num_rows($check_result) > 0);

        if (!$user_exists) {
            $html .= "<p>No user found with ID: {$id}</p>";
        } else {
            // Object-level authorization: a user may only view THEIR OWN
            // profile, determined by the server-side authenticated user id.
            // Admins may view any profile.
            if ($id === $current_user_id || $role === 'admin') {
                $query = "SELECT first_name, last_name, user_id, avatar FROM users WHERE user_id = $id;";
                $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $html .= "
                        <div class=\"profile-info\">
                            <h3>User Profile</h3>
                            <p>User ID: " . htmlspecialchars($row['user_id'], ENT_QUOTES, 'UTF-8') . "</p>
                            <p>Name: " . htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($row['last_name'], ENT_QUOTES, 'UTF-8') . "</p>
                            <p>Avatar: " . htmlspecialchars($row['avatar'], ENT_QUOTES, 'UTF-8') . "</p>
                        </div>";
                }
            } else {
                $html .= "<p>Access denied. You can only view your own profile.</p>";
            }
        }

        // Log access attempts
        try {
            $check_table = "SHOW TABLES LIKE 'bac_log'";
            $table_exists = mysqli_query($GLOBALS["___mysqli_ston"], $check_table);

            if ($table_exists && mysqli_num_rows($table_exists) == 0) {
                $create_table = "CREATE TABLE IF NOT EXISTS bac_log (
                   id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(6) NULL,
                    target_id INT(6) NULL,
                    ip_address VARCHAR(50) NULL,
                    action VARCHAR(50) NULL,
                    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                mysqli_query($GLOBALS["___mysqli_ston"], $create_table);
            }

            $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
            $target_id = $user_exists ? $id : 0;
            $log_query = "INSERT INTO bac_log (user_id, target_id, ip_address) VALUES (?, ?, ?)";
            $log_stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], $log_query);
            if ($log_stmt) {
                mysqli_stmt_bind_param($log_stmt, "iis", $current_user_id, $target_id, $ip);
                mysqli_stmt_execute($log_stmt);
                mysqli_stmt_close($log_stmt);
            }
        } catch (Exception $e) {
            // Silently fail if logging doesn't work
        }
    }
}

// Show current user's role for context (server-side value, not a cookie)
$html .= "<div class='info-banner'>Current Role: " . htmlspecialchars($role, ENT_QUOTES, 'UTF-8') . "</div>";
?>

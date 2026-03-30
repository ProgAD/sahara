<?php
// 1. Start the session to access it
session_start();

// 2. Unset all session variables
$_SESSION = array();

// 3. Destroy the session cookie in the browser
// This is important to ensure the session cannot be hijacked
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destroy the session on the server
session_destroy();

// 5. Redirect to the login page
// You can also add a query parameter to show a 'logged out' message
header("Location: ../../login.html?status=logged_out");
exit;
?>
<?php
require_once('includes/functions.php');

// Clear all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Finally, destroy the session.
session_destroy();

// Redirect to home page
setFlashMessage('You have been logged out successfully', 'success');
redirect('index.php');
?> 
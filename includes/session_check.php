<?php
// Session validation helper
// Usage: include 'includes/session_check.php';
// Or with role check: checkSession('admin');

function checkSession($requiredRole = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }
    
    if ($requiredRole !== null && $_SESSION['role'] !== $requiredRole) {
        header("Location: index.php");
        exit();
    }
}

// Auto-check session if this file is included
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

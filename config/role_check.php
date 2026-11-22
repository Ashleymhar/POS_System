<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function requireRole($allowedRoles) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        die("ACCESS DENIED: Please log in to access this page.");
    }

    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles, true)) {
        http_response_code(403);
        die("ACCESS DENIED: You do not have permission to access this page.");
    }
}

function hasRole($allowedRoles) {
    return isset($_SESSION['role'])
        && is_array($allowedRoles)
        && in_array($_SESSION['role'], $allowedRoles, true);
}
<?php
/**
 * db_connect.php
 * ─────────────────────────────────────────────────────────────
 * Establishes a MySQLi connection to my_database and returns
 * the connection object.  Include this file in every script
 * that needs database access:
 *
 *   require_once __DIR__ . '/db_connect.php';
 *   // $conn is now available
 * ─────────────────────────────────────────────────────────────
 */

define('DB_HOST',    'localhost');
define('DB_USER',    'my_user');
define('DB_PASS',    'my_password');
define('DB_NAME',    'my_database');
define('DB_CHARSET', 'utf8mb4');

// ── Suppress the default MySQLi connection error so we can
//    handle it ourselves cleanly.
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ── Connection error handling ────────────────────────────────
if ($conn->connect_error) {
    // Log the real error server-side (never expose to end-user)
    error_log(
        '[DB] Connection failed: ' .
        $conn->connect_errno . ' – ' .
        $conn->connect_error
    );

    // Return a safe, generic message to the browser
    http_response_code(503);
    die(json_encode([
        'success' => false,
        'message' => 'Service temporarily unavailable. Please try again later.',
    ]));
}

// ── Enforce charset for the session ─────────────────────────
if (!$conn->set_charset(DB_CHARSET)) {
    error_log('[DB] Could not set charset: ' . $conn->error);
}

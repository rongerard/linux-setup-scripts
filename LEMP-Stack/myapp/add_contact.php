<?php
/**
 * add_contact.php
 * ─────────────────────────────────────────────────────────────
 * Receives POST data from the contact form on index.php,
 * validates the input, and inserts a row into `contacts`.
 * Always responds with JSON so the frontend can react without
 * a full page reload.
 * ─────────────────────────────────────────────────────────────
 */

header('Content-Type: application/json');

// ── Only allow POST ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Pull & sanitise inputs ───────────────────────────────────
$name  = trim($_POST['name']  ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// ── Basic validation ─────────────────────────────────────────
$errors = [];

if ($name === '') {
    $errors[] = 'Name is required.';
} elseif (mb_strlen($name) > 100) {
    $errors[] = 'Name must be 100 characters or fewer.';
}

if ($email === '') {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please provide a valid email address.';
} elseif (mb_strlen($email) > 150) {
    $errors[] = 'Email must be 150 characters or fewer.';
}

if ($phone === '') {
    $errors[] = 'Phone is required.';
} elseif (!preg_match('/^[0-9+\-\s().]{7,30}$/', $phone)) {
    $errors[] = 'Phone number is invalid (7–30 digits / symbols allowed).';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ── Database connection ──────────────────────────────────────
require_once __DIR__ . '/db_connect.php';  // provides $conn

// ── Prepared statement – prevents SQL injection ──────────────
$stmt = $conn->prepare(
    'INSERT INTO contacts (name, email, phone) VALUES (?, ?, ?)'
);

if (!$stmt) {
    error_log('[DB] Prepare failed: ' . $conn->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not save contact. Please try again.']);
    exit;
}

$stmt->bind_param('sss', $name, $email, $phone);

if ($stmt->execute()) {
    $newId = $stmt->insert_id;
    $stmt->close();
    $conn->close();

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Contact saved successfully.',
        'id'      => $newId,
    ]);
} else {
    error_log('[DB] Execute failed: ' . $stmt->error);
    $stmt->close();
    $conn->close();

    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not save contact. Please try again.']);
}

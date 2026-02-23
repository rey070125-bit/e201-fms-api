<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["ok" => false, "error" => "Use POST"]);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(["ok" => false, "error" => "Missing username/password"]);
    exit;
}

/**
 * ✅ ADMIN LOGIN (admins table)
 * Adjust column names below ONLY if your admins table uses different ones.
 *
 * Expected columns (common):
 * - id
 * - username
 * - password_hash   (hashed password)
 * - role            (optional)
 */

$stmt = $conn->prepare("SELECT id, username, password, role FROM admins WHERE username=? LIMIT 1");
if (!$stmt) {
    echo json_encode(["ok" => false, "error" => "SQL prepare failed"]);
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$admin) {
    echo json_encode(["ok" => false, "error" => "Invalid credentials"]);
    exit;
}

// If your admins table stores hashed passwords:
$hash = $admin['password_hash'] ?? '';
if ($hash === '' || !password_verify($password, $hash)) {
    echo json_encode(["ok" => false, "error" => "Invalid credentials"]);
    exit;
}

echo json_encode([
    "ok" => true,
    "token" => "test_token_123", // temporary
    "user" => [
        "id" => (int)$admin['id'],
        "username" => $admin['username'],
        "role" => $admin['role'] ?? 'admin'
    ]
]);
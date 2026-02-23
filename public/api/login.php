<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["ok"=>false,"error"=>"Use POST"]);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(["ok"=>false,"error"=>"Missing username/password"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, username, password_hash, role FROM admins WHERE username=? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user['password_hash'])) {
    echo json_encode(["ok"=>false,"error"=>"Invalid credentials"]);
    exit;
}

echo json_encode([
    "ok"=>true,
    "token"=>"test_token_123",   // temporary
    "user"=>[
        "id"=>$user['id'],
        "username"=>$user['username'],
        "role"=>$user['role']
    ]
]);
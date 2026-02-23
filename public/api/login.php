<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once __DIR__ . '/../../includes/config.php';

function out($arr, $code = 200){
  http_response_code($code);
  echo json_encode($arr);
  exit;
}

try {

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    out(["ok"=>false,"error"=>"Use POST"], 405);
  }

  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($username === '' || $password === '') {
    out(["ok"=>false,"error"=>"Missing username/password"], 400);
  }

  $stmt = $conn->prepare("
      SELECT id, username, password, role
      FROM admins
      WHERE username = ?
      LIMIT 1
  ");

  $stmt->bind_param("s", $username);
  $stmt->execute();
  $admin = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if (!$admin) {
    out(["ok"=>false,"error"=>"Invalid credentials"], 401);
  }

  // Since your DB stores plain password
  if ($admin['password'] !== $password) {
    out(["ok"=>false,"error"=>"Invalid credentials"], 401);
  }

  out([
    "ok"=>true,
    "token"=>"test_token_123",
    "user"=>[
      "id"=>(int)$admin['id'],
      "username"=>$admin['username'],
      "role"=>$admin['role']
    ]
  ]);

} catch (Throwable $e) {
  out(["ok"=>false,"error"=>"Server error","message"=>$e->getMessage()], 500);
}
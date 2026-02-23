<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
ini_set('display_errors', '0');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once __DIR__ . '/../../includes/config.php';

function out($arr, $code=200){
  http_response_code($code);
  echo json_encode($arr);
  exit;
}

try {
  $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
  if (!preg_match('/Bearer\s+(.+)/', $auth, $m)) {
    out(["ok"=>false,"error"=>"Missing token"], 401);
  }

  // TEMP: If you want to enforce the temp token, uncomment:
  // $token = trim($m[1]);
  // if ($token !== "test_token_123") out(["ok"=>false,"error"=>"Invalid token"], 401);

  // ✅ FIX: removed `category` (your table doesn't have it)
  $res = $conn->query("SELECT id, filename, uploaded_at FROM documents ORDER BY id DESC");

  $docs = [];
  while($row = $res->fetch_assoc()){
    $docs[] = [
      "id" => (int)$row["id"],
      "filename" => $row["filename"],
      "uploaded_at" => $row["uploaded_at"] ?? null
    ];
  }

  out(["ok"=>true, "documents"=>$docs]);

} catch (Throwable $e) {
  out(["ok"=>false,"error"=>"Server error","message"=>$e->getMessage()], 500);
}
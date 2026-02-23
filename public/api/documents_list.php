<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/../../includes/config.php';

function out($arr, $code=200){
  http_response_code($code);
  echo json_encode($arr);
  exit;
}

// TODO: validate Bearer token properly (for now accept any non-empty)
$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s+(.+)/', $auth, $m)) out(["ok"=>false,"error"=>"Missing token"], 401);

$res = $conn->query("SELECT id, filename, category, uploaded_at FROM documents ORDER BY id DESC");
$docs = [];
while($row = $res->fetch_assoc()){
  $docs[] = [
    "id" => (int)$row["id"],
    "filename" => $row["filename"],
    "category" => $row["category"],
    "uploaded_at" => $row["uploaded_at"],
  ];
}

out(["ok"=>true, "documents"=>$docs]);
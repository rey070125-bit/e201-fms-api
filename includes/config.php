<?php
// Railway MySQL env vars
$host = getenv('MYSQLHOST') ?: '127.0.0.1';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$port = (int)(getenv('MYSQLPORT') ?: 3306);

// ✅ Your imported schema name
$db = 'e201';

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(["ok"=>false,"error"=>"DB connection failed: ".$conn->connect_error]);
  exit;
}
$conn->set_charset("utf8mb4");
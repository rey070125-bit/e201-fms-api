<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Use Railway-provided MySQL vars (these should exist in the API service)
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$port = getenv('MYSQLPORT');
$dbFromEnv = getenv('MYSQLDATABASE');

// Your schema name where tables exist
$db = getenv('MYSQLDATABASE');

// If env vars are missing, return JSON error (instead of crashing / HTML fatal)
if (!$host || !$user || !$pass || !$port) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    "ok" => false,
    "error" => "Missing Railway MySQL env vars in API service",
    "debug" => [
      "MYSQLHOST" => $host ? "set" : "missing",
      "MYSQLUSER" => $user ? "set" : "missing",
      "MYSQLPASSWORD" => $pass ? "set" : "missing",
      "MYSQLPORT" => $port ? "set" : "missing",
      "MYSQLDATABASE" => $dbFromEnv ? $dbFromEnv : "missing",
      "using_schema" => $db
    ]
  ]);
  exit;
}

try {
  $conn = new mysqli($host, $user, $pass, $db, (int)$port);
  $conn->set_charset("utf8mb4");
} catch (Throwable $e) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    "ok" => false,
    "error" => "DB connection failed",
    "message" => $e->getMessage(),
    "debug" => [
      "host" => $host,
      "port" => (int)$port,
      "db" => $db,
      "user" => $user
    ]
  ]);
  exit;
}
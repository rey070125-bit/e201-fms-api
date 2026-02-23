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

$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s+(.+)/', $auth, $m)) out(["ok"=>false,"error"=>"Missing token"], 401);

// TEMP: because you use "test_token_123" in login.php
$token = trim($m[1]);
if ($token !== "test_token_123") out(["ok"=>false,"error"=>"Invalid token"], 401);

// TEMP: return fixed user (replace with real token->user lookup later)
out(["ok"=>true, "profile"=>["id"=>39, "username"=>"JOHN", "role"=>"superadmin"]]);
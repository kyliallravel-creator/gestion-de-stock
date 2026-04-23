<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__.'/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD']==='GET') {
  if (!empty($_SESSION['user'])) { $u = $_SESSION['user']; unset($u['mot_de_passe']); echo json_encode(['ok'=>true,'user'=>$u]); }
  else echo json_encode(['ok'=>false]);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$ident = $data['identifiant'] ?? '';
$pass  = $data['mot_de_passe'] ?? '';

if (!$ident || !$pass) { echo json_encode(['ok'=>false,'error'=>'identifiants manquants']); exit; }

$st = $pdo->prepare('SELECT * FROM utilisateur WHERE identifiant = ?');
$st->execute([$ident]);
$user = $st->fetch();
if ($user && password_verify($pass, $user['mot_de_passe'])) {
  $_SESSION['user'] = $user;
  unset($_SESSION['user']['mot_de_passe']);
  echo json_encode(['ok'=>true,'user'=>$_SESSION['user']]);
} else {
  echo json_encode(['ok'=>false,'error'=>'Identifiant ou mot de passe invalide']);
}

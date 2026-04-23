<?php
require_once __DIR__.'/auth.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$act = $input['action'] ?? '';

if ($act==='list') {
  $st=$pdo->query('SELECT j.*, u.nom AS nom_user FROM journal j LEFT JOIN utilisateur u ON j.id_utilisateur=u.id_utilisateur ORDER BY date_action DESC LIMIT 500');
  echo json_encode(['ok'=>true,'rows'=>$st->fetchAll()]); exit;
}

echo json_encode(['ok'=>false,'error'=>'action inconnue']);

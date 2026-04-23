<?php
require_once __DIR__.'/auth.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$act = $input['action'] ?? '';

if ($act==='list') {
  $rows = $pdo->query('SELECT * FROM categorie ORDER BY nom')->fetchAll();
  echo json_encode(['ok'=>true,'rows'=>$rows]); exit;
}
if ($act==='get') {
  $id = (int)($input['id'] ?? 0);
  $st = $pdo->prepare('SELECT * FROM categorie WHERE id_categorie=?'); $st->execute([$id]);
  echo json_encode(['ok'=>true,'row'=>$st->fetch()]); exit;
}
if ($act==='create') {
  require_login_json();
  $nom = trim($input['nom'] ?? '');
  if (!$nom) { echo json_encode(['ok'=>false,'error'=>'nom requis']); exit; }
  try {
    $st = $pdo->prepare('INSERT INTO categorie (nom) VALUES (?)'); $st->execute([$nom]);
    $catId = $pdo->lastInsertId();
    // journal
    $j = $pdo->prepare('INSERT INTO journal (action, table_concernee, id_enregistrement, id_utilisateur) VALUES (?,?,?,?)');
    $j->execute(['Création catégorie: '.$nom, 'categorie', $catId, current_user_id()]);
    echo json_encode(['ok'=>true]);
  } catch (Exception $e) { echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); }
  exit;
}
if ($act==='update') {
  require_login_json();
  $id = (int)($input['id_categorie'] ?? 0);
  $nom = trim($input['nom'] ?? '');
  if (!$id || !$nom) { echo json_encode(['ok'=>false,'error'=>'données manquantes']); exit; }
  $st = $pdo->prepare('UPDATE categorie SET nom=? WHERE id_categorie=?');
  $st->execute([$nom,$id]);
  $j = $pdo->prepare('INSERT INTO journal (action, table_concernee, id_enregistrement, id_utilisateur) VALUES (?,?,?,?)');
  $j->execute(['Modification catégorie: '.$nom,'categorie',$id,current_user_id()]);
  echo json_encode(['ok'=>true]); exit;
}
if ($act==='delete') {
  require_login_json();
  $id = (int)($input['id'] ?? 0);
  $st = $pdo->prepare('DELETE FROM categorie WHERE id_categorie=?'); $st->execute([$id]);
  $j = $pdo->prepare('INSERT INTO journal (action, table_concernee, id_enregistrement, id_utilisateur) VALUES (?,?,?,?)');
  $j->execute(['Suppression catégorie','categorie',$id,current_user_id()]);
  echo json_encode(['ok'=>true]); exit;
}

echo json_encode(['ok'=>false,'error'=>'action inconnue']);

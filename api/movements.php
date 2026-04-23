<?php
require_once __DIR__.'/auth.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$act = $input['action'] ?? '';

if ($act==='create') {
  require_login_json();
  $idp = (int)($input['id_produit'] ?? 0);
  $type = $input['type_mouvement'] ?? '';
  $qty  = (int)($input['quantite'] ?? 0);
  $desc = $input['description'] ?: null;
  $uid  = current_user_id();

  if ($qty<=0) { echo json_encode(['ok'=>false,'error'=>'quantité invalide']); exit; }

  try{
    $pdo->beginTransaction();
    $st=$pdo->prepare('SELECT quantite_stock, nom FROM produit WHERE id_produit=? FOR UPDATE'); $st->execute([$idp]);
    $p=$st->fetch(); if(!$p) throw new Exception('Produit introuvable');
    if ($type==='exit' && $qty > (int)$p['quantite_stock']) throw new Exception('Stock insuffisant');

    $newQty = $type==='entrée' ? ((int)$p['quantite_stock'] + $qty) : ((int)$p['quantite_stock'] - $qty);
    $up=$pdo->prepare('UPDATE produit SET quantite_stock=? WHERE id_produit=?'); $up->execute([$newQty,$idp]);

    $ins=$pdo->prepare('INSERT INTO mouvement_stock (type_mouvement, id_produit, quantite, id_utilisateur, description) VALUES (?,?,?,?,?)');
    $ins->execute([$type,$idp,$qty,$uid,$desc]);
    $mid=$pdo->lastInsertId();

    $j=$pdo->prepare('INSERT INTO journal (action, table_concernee, id_enregistrement, id_utilisateur) VALUES (?,?,?,?)');
    $j->execute([( $type==='entrée'?'entrée':'sortie')." qte:$qty produit:".$p['nom'], 'mouvement_stock', $mid, $uid]);

    $pdo->commit(); echo json_encode(['ok'=>true]);
  } catch(Exception $e){ $pdo->rollBack(); echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); }
  exit;
}

if ($act==='list') {
  $st=$pdo->query('SELECT m.*, p.nom, u.nom AS nom_user FROM mouvement_stock m JOIN produit p ON m.id_produit=p.id_produit LEFT JOIN utilisateur u ON m.id_utilisateur=u.id_utilisateur ORDER BY date_mouvement DESC LIMIT 200');
  echo json_encode(['ok'=>true,'rows'=>$st->fetchAll()]); exit;
}

if ($act==='recent') {
  $st=$pdo->query('SELECT m.id_mouvement, p.nom, m.type_mouvement, m.quantite, u.nom AS nom_user, m.date_mouvement FROM mouvement_stock m JOIN produit p ON m.id_produit=p.id_produit LEFT JOIN utilisateur u ON m.id_utilisateur=u.id_utilisateur ORDER BY date_mouvement DESC LIMIT 8');
  echo json_encode($st->fetchAll()); exit;
}

echo json_encode(['ok'=>false,'error'=>'action inconnue']);

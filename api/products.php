<?php
require_once __DIR__.'/auth.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$act = $input['action'] ?? '';

if ($act==='list') {
  $st = $pdo->query('SELECT p.*, c.nom AS nom_categorie FROM produit p LEFT JOIN categorie c ON p.id_categorie=c.id_categorie ORDER BY p.nom');
  echo json_encode(['ok'=>true,'rows'=>$st->fetchAll()]); exit;
}
if ($act==='get') {
  $id=(int)($input['id'] ?? 0);
  $st=$pdo->prepare('SELECT * FROM produit WHERE id_produit=?'); $st->execute([$id]);
  echo json_encode(['ok'=>true,'row'=>$st->fetch()]); exit;
}
if ($act==='create') {
  require_login_json();
  $nom=$input['nom']??''; $id_cat=$input['id_categorie']?:null; $qty=(int)($input['quantite_stock']??0);
  $exp=$input['date_expiration']?:null; $desc=$input['description']?:null;
  $st=$pdo->prepare('INSERT INTO produit (nom, description, date_expiration, quantite_stock, id_categorie) VALUES (?,?,?,?,?)');
  $st->execute([$nom,$desc,$exp,$qty,$id_cat]);
  $idp = $pdo->lastInsertId();
  $j=$pdo->prepare('INSERT INTO journal (action, table_concernee, id_enregistrement, id_utilisateur) VALUES (?,?,?,?)');
  $j->execute(['Création produit: '.$nom,'produit',$idp,current_user_id()]);
  echo json_encode(['ok'=>true]); exit;
}
if ($act==='update') {
  require_login_json();
  $id=(int)($input['id_produit']??0);
  $nom=$input['nom']??''; $id_cat=$input['id_categorie']?:null; $qty=(int)($input['quantite_stock']??0);
  $exp=$input['date_expiration']?:null; $desc=$input['description']?:null;
  $st=$pdo->prepare('UPDATE produit SET nom=?, description=?, date_expiration=?, quantite_stock=?, id_categorie=? WHERE id_produit=?');
  $st->execute([$nom,$desc,$exp,$qty,$id_cat,$id]);
  $j=$pdo->prepare('INSERT INTO journal (action, table_concernee, id_enregistrement, id_utilisateur) VALUES (?,?,?,?)');
  $j->execute(['Modification produit: '.$nom,'produit',$id,current_user_id()]);
  echo json_encode(['ok'=>true]); exit;
}
if ($act==='search') {
  $q='%'.($input['q']??'').'%';
  $st=$pdo->prepare('SELECT p.*, c.nom AS nom_categorie FROM produit p LEFT JOIN categorie c ON p.id_categorie=c.id_categorie WHERE p.nom LIKE ? OR c.nom LIKE ? ORDER BY p.nom');
  $st->execute([$q,$q]);
  echo json_encode(['ok'=>true,'rows'=>$st->fetchAll()]); exit;
}
if ($act==='types') {
  $st=$pdo->query('SELECT * FROM type_mouvement_stock ORDER BY type_mouvement');
  echo json_encode(['ok'=>true,'rows'=>$st->fetchAll()]); exit;
}
if ($act==='stats') {
  $total_products = (int)$pdo->query('SELECT COUNT(*) FROM produit')->fetchColumn();
  $total_quantity = (int)$pdo->query('SELECT COALESCE(SUM(quantite_stock),0) FROM produit')->fetchColumn();
  $expired_count = (int)$pdo->query("SELECT COUNT(*) FROM produit WHERE date_expiration IS NOT NULL AND date_expiration < CURDATE()")->fetchColumn();
  echo json_encode(['ok'=>true,'total_products'=>$total_products,'total_quantity'=>$total_quantity,'expired_count'=>$expired_count]); exit;
}

echo json_encode(['ok'=>false,'error'=>'action inconnue']);

<?php
// Script facultatif pour insérer les rôles et types manquants
require_once __DIR__.'/db.php';
header('Content-Type: text/plain');

$pdo->exec("INSERT IGNORE INTO role_utilisateur (role) VALUES ('utilisateur'), ('admin')");
$pdo->exec("INSERT IGNORE INTO type_mouvement_stock (type_mouvement) VALUES ('entry'), ('exit')");

echo "OK: rôles et types de mouvement insérés si manquants.\n";

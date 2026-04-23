<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

if (!empty($_SESSION['utilisateur'])) {
    $u = $_SESSION['utilisateur'];
    unset($u['mot_de_passe']); // ne pas exposer le mot de passe
    echo json_encode(['ok'=>true,'user'=>$u]);
} else {
    echo json_encode(['ok'=>false]);
}

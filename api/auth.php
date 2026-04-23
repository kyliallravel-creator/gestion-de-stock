<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__.'/db.php';

// Vérifie si l'utilisateur est connecté, sinon renvoie JSON
function require_login_json() {
    if (empty($_SESSION['utilisateur'])) { // <- modifié ici
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>false,'error'=>'login required']);
        exit;
    }
}

// Récupère l'ID de l'utilisateur connecté
function current_user_id() {
    return $_SESSION['utilisateur']['id_utilisateur'] ?? null; // <- modifié ici
}

<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$db   = 'gestionstock';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur connexion BDD : ' . $e->getMessage()]);
    exit();
}

$type = $_POST['type_formulaire'] ?? '';

if ($type === 'inscription') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $identifiant = trim($_POST['Identifiant'] ?? '');

    if (!$username || !$password || !$identifiant) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE identifiant = ?");
    $stmt->execute([$identifiant]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Cet identifiant est déjà utilisé.']);
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, identifiant, mot_de_passe) VALUES (?, ?, ?)");
    $stmt->execute([$username, $identifiant, $hash]);

    // Redirection vers la page de login
    echo json_encode([
        'success' => true,
        'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.',
        'redirect' => 'index.html' // ← nom exact de ton fichier de connexion
    ]);
    exit();
}

elseif ($type === 'connexion') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$username || !$password) {
        echo json_encode(['success'=>false,'message'=>'Tous les champs sont obligatoires.']);
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE nom = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['utilisateur'] = $user;
        echo json_encode([
            'success'=>true,
            'message'=>'Connexion réussie !',
            'redirect'=>'index.html' // ← redirection vers dashboard
        ]);
        exit();
    } else {
        echo json_encode(['success'=>false,'message'=>'Nom ou mot de passe incorrect.']);
        exit();
    }
}

elseif ($type === 'oubli') {
    $username = trim($_POST['username'] ?? '');
    $identifiant = trim($_POST['Identifiant'] ?? '');

    if (!$username || !$identifiant) {
        echo json_encode(['success'=>false,'message'=>'Tous les champs sont obligatoires.']);
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE nom = ? AND identifiant = ?");
    $stmt->execute([$username, $identifiant]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success'=>false,'message'=>'Utilisateur non trouvé avec ces informations.']);
        exit();
    }

    echo json_encode(['success'=>true,'message'=>"Demande reçue. Contactez l'administrateur pour récupérer votre mot de passe."]);
    exit();
}

else {
    echo json_encode(['success'=>false,'message'=>'Type de formulaire non reconnu.']);
    exit();
}
?>
<?php
require_once 'auth.php';
require_login(); // Vérifie que l'utilisateur est connecté
require_once 'config.php'; // Connexion PDO ($pdo)

$term = trim($_GET['q'] ?? '');
$termLike = "%$term%";

$results = [
    'produits' => [],
    'categories' => [],
    'mouvements' => [],
    'utilisateurs' => [],
    'journal' => []
];

if ($term !== '') {

    // Recherche produits
    $stmt = $pdo->prepare("
        SELECT p.id_produit, p.nom, p.quantite_stock, c.nom AS categorie
        FROM produit p
        LEFT JOIN categorie c ON p.id_categorie = c.id_categorie
        WHERE p.nom LIKE :term OR c.nom LIKE :term
        LIMIT 50
    ");
    $stmt->execute(['term' => $termLike]);
    $results['produits'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recherche catégories
    $stmt = $pdo->prepare("SELECT id_categorie, nom FROM categorie WHERE nom LIKE :term LIMIT 50");
    $stmt->execute(['term' => $termLike]);
    $results['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recherche mouvements
    $stmt = $pdo->prepare("
        SELECT m.id_mouvement, p.nom AS produit, m.type_mouvement, m.quantite, u.nom AS employe
        FROM mouvement_stock m
        LEFT JOIN produit p ON m.id_produit = p.id_produit
        LEFT JOIN utilisateur u ON m.id_utilisateur = u.id_utilisateur
        WHERE p.nom LIKE :term OR m.type_mouvement LIKE :term OR u.nom LIKE :term
        LIMIT 50
    ");
    $stmt->execute(['term' => $termLike]);
    $results['mouvements'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recherche utilisateurs
    $stmt = $pdo->prepare("SELECT id_utilisateur, nom, identifiant, role FROM utilisateur WHERE nom LIKE :term OR identifiant LIKE :term LIMIT 50");
    $stmt->execute(['term' => $termLike]);
    $results['utilisateurs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recherche journal
    $stmt = $pdo->prepare("
        SELECT j.id_journal, j.action, j.table_concernee, j.id_enregistrement, u.nom AS employe
        FROM journal j
        LEFT JOIN utilisateur u ON j.id_utilisateur = u.id_utilisateur
        WHERE j.action LIKE :term OR j.table_concernee LIKE :term OR u.nom LIKE :term
        LIMIT 50
    ");
    $stmt->execute(['term' => $termLike]);
    $results['journal'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Recherche globale</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<h1>Résultats pour "<?= htmlspecialchars($term) ?>"</h1>

<form method="get" action="search.php">
  <input type="text" name="q" value="<?= htmlspecialchars($term) ?>" placeholder="Recherche...">
  <button type="submit">Rechercher</button>
</form>

<?php foreach ($results as $table => $rows): ?>
  <h2><?= ucfirst($table) ?> (<?= count($rows) ?>)</h2>
  <?php if ($rows): ?>
    <table border="1" cellpadding="5" cellspacing="0">
      <thead>
        <tr>
          <?php foreach (array_keys($rows[0]) as $col): ?>
            <th><?= htmlspecialchars($col) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <?php foreach ($row as $val): ?>
              <td><?= htmlspecialchars($val) ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>Aucun résultat.</p>
  <?php endif; ?>
<?php endforeach; ?>

<p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>

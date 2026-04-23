<?php
// Page principale (SPA) - nécessite login via api/login.php (POST)
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion de Stock - Entreprise</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dark">
  <aside class="sidebar">
    <div class="brand">Stock<span>Pro</span></div>
    <nav>
      <button data-view="dashboard" class="active">Tableau de bord</button>
      <button data-view="products">Produits</button>
      <button data-view="categories">Catégories</button>
      <button data-view="movements">Mouvements</button>
      <button data-view="journal">Journal</button>
      <button id="logoutBtn">Déconnexion</button>

    </nav>
  </aside>

  <main class="main">
    <header class="topbar">
      <div class="search">
        <input id="globalSearch" placeholder="Recherche produit ou catégorie...">
        <button id="searchBtn">Rechercher</button>
      </div>
      <div class="userinfo">Utilisateur: <span id="username">non connecté</span></div>
    </header>

    <section id="dashboard" class="view active">
      <h1>Tableau de bord</h1>
      <div class="cards">
        <div class="card">
          <h3>Total produits</h3><div class="big" id="totalProducts">0</div>
        </div>
        <div class="card">
          <h3>Quantité totale</h3><div class="big" id="totalStock">0</div>
        </div>
        <div class="card">
          <h3>Produits périmés</h3><div class="big" id="expiredCount">0</div>
        </div>
      </div>

      <div class="recent">
        <h2>Mouvements récents</h2>
        <table id="recentMovementsTable">
          <thead><tr><th>#</th><th>Produit</th><th>Type</th><th>Qté</th><th>Employé</th><th>Date</th></tr></thead>
          <tbody></tbody>
        </table>
      </div>
    </section>

    <section id="products" class="view">
      <div class="toolbar">
        <h1>Produits</h1>
        <div class="controls">
          <button id="btnNewProduct">Nouveau produit</button>
        </div>
      </div>
      <table id="productsTable">
        <thead><tr><th>ID</th><th>Nom</th><th>Catégorie</th><th>Qté</th><th>Expiration</th><th>Actions</th></tr></thead>
        <tbody></tbody>
      </table>

      <div id="productFormModal" class="modal">
        <form id="productForm">
          <h3 id="productFormTitle">Ajouter produit</h3>
          <label>Nom<input name="nom" required></label>
          <label>Catégorie<select name="id_categorie" id="selCategory"></select></label>
          <label>Quantité<input type="number" name="quantite_stock" min="0" value="0" required></label>
          <label>Date d'expiration<input type="date" name="date_expiration"></label>
          <label>Description<textarea name="description"></textarea></label>
          <input type="hidden" name="id_produit" value="">
          <div class="form-actions"><button type="submit">Enregistrer</button><button type="button" id="cancelProductForm">Annuler</button></div>
        </form>
      </div>
    </section>

    <section id="categories" class="view">
      <div class="toolbar">
        <h1>Catégories</h1>
        <div class="controls"><button id="btnNewCategory">Nouvelle catégorie</button></div>
      </div>
      <table id="catsTable"><thead><tr><th>ID</th><th>Nom</th><th>Actions</th></tr></thead><tbody></tbody></table>

      <div id="catFormModal" class="modal">
        <form id="catForm">
          <h3>Ajouter / Modifier catégorie</h3>
          <label>Nom<input name="nom" required></label>
          <input type="hidden" name="id_categorie" value="">
          <div class="form-actions"><button type="submit">Enregistrer</button><button type="button" id="cancelCatForm">Annuler</button></div>
        </form>
      </div>
    </section>

    <section id="movements" class="view">
      <div class="toolbar">
        <h1>Mouvements de stock</h1>
        <div class="controls"><button id="btnNewMovement">Nouveau mouvement</button></div>
      </div>
      <table id="movementsTable"><thead><tr><th>#</th><th>Produit</th><th>Type</th><th>Qté</th><th>Employé</th><th>Date</th></tr></thead><tbody></tbody></table>

      <div id="movementFormModal" class="modal">
        <form id="movementForm">
          <h3>Ajouter mouvement</h3>
          <label>Produit<select name="id_produit" id="selProductForMove" required></select></label>
          <label>Type<select name="type_mouvement" id="selTypeMove" required></select></label>
          <label>Quantité<input type="number" name="quantite" min="1" value="1" required></label>
          <label>Motif / Description<textarea name="description"></textarea></label>
          <div class="form-actions"><button type="submit">Valider</button><button type="button" id="cancelMovementForm">Annuler</button></div>
        </form>
      </div>
    </section>

    <section id="journal" class="view">
      <h1>Journal des actions</h1>
      <table id="journalTable"><thead><tr><th>#</th><th>Action</th><th>Table</th><th>ID</th><th>Employé</th><th>Date</th></tr></thead><tbody></tbody></table>
    </section>
  </main>
 


  <script src="assets/js/app.js"></script>
</body>
</html>

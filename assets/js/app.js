// Frontend JS - fetch API vers /api/* .php
const api = (path, opts={}) => fetch('api/'+path, {credentials:'same-origin', headers:{'Accept':'application/json'}, ...opts}).then(r=>r.json());

// Switch vues
const views = document.querySelectorAll('.view');
document.querySelectorAll('.sidebar nav button').forEach(btn=>btn.addEventListener('click',()=>{
  document.querySelectorAll('.sidebar nav button').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active'); const v=btn.dataset.view;
  views.forEach(x=>x.classList.remove('active'));
  document.getElementById(v).classList.add('active');
  if(v==='dashboard') loadDashboard();
  if(v==='products') loadProducts();
  if(v==='categories') loadCategories();
  if(v==='movements') loadMovements();
  if(v==='journal') loadJournal();
}));

// Afficher user connecté
api('whoami.php').then(d=>{ if(d.ok){ document.getElementById('username').textContent = d.user.nom; } });

// Recherche globale
document.getElementById('searchBtn').addEventListener('click',()=>{
  const q = document.getElementById('globalSearch').value.trim();
  api('products.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'search',q})}).then(renderProducts);
});

// Dashboard
function loadDashboard(){
  Promise.all([
    api('products.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'stats'})}),
    api('movements.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'recent'})})
  ]).then(([s, rec])=>{
    document.getElementById('totalProducts').textContent = s.total_products || 0;
    document.getElementById('totalStock').textContent = s.total_quantity || 0;
    document.getElementById('expiredCount').textContent = s.expired_count || 0;
    const tb = document.querySelector('#recentMovementsTable tbody'); tb.innerHTML='';
    rec.forEach(m=>{ const tr=document.createElement('tr'); tr.innerHTML = `<td>${m.id_mouvement}</td><td>${m.nom}</td><td>${m.type_mouvement}</td><td>${m.quantite}</td><td>${m.nom_user||''}</td><td>${m.date_mouvement}</td>`; tb.appendChild(tr); });
  });
}

// Produits
function loadProducts(){
  api('products.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'list'})}).then(renderProducts);
}
function renderProducts(data){
  const tb = document.querySelector('#productsTable tbody'); tb.innerHTML='';
  (data.rows||[]).forEach(p=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${p.id_produit}</td><td>${p.nom}</td><td>${p.nom_categorie||''}</td><td>${p.quantite_stock}</td><td>${p.date_expiration||''}</td>
      <td><button class="edit" data-id="${p.id_produit}">Edit</button> <button class="move" data-id="${p.id_produit}">Mvt</button></td>`;
    tb.appendChild(tr);
  });
  document.querySelectorAll('#productsTable .edit').forEach(b=>b.addEventListener('click',e=>openProductForm(e.target.dataset.id)));
  document.querySelectorAll('#productsTable .move').forEach(b=>b.addEventListener('click',e=>openMovementForm(e.target.dataset.id)));
}

// Formulaire produit
const productFormModal = document.getElementById('productFormModal');
const productForm = document.getElementById('productForm');
document.getElementById('btnNewProduct').addEventListener('click',()=>openProductForm());
document.getElementById('cancelProductForm').addEventListener('click',()=>productFormModal.style.display='none');
function openProductForm(id){
  productForm.reset();
  document.getElementById('productFormTitle').textContent = id ? 'Modifier produit' : 'Ajouter produit';
  productForm.id_produit.value='';
  api('categories.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'list'})}).then(d=>{
    const sel = document.getElementById('selCategory'); sel.innerHTML='';
    (d.rows||[]).forEach(c=>{ const opt=document.createElement('option'); opt.value=c.id_categorie; opt.textContent=c.nom; sel.appendChild(opt); });
    if(id){
      api('products.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'get',id})}).then(r=>{
        const p=r.row; if(!p) return; productForm.id_produit.value=p.id_produit; productForm.nom.value=p.nom;
        productForm.id_categorie.value=p.id_categorie||''; productForm.quantite_stock.value=p.quantite_stock;
        productForm.date_expiration.value=p.date_expiration||''; productForm.description.value=p.description||'';
      });
    }
    productFormModal.style.display='block';
  });
}
productForm.addEventListener('submit',e=>{
  e.preventDefault(); const fd=new FormData(productForm); const obj={}; fd.forEach((v,k)=>obj[k]=v);
  const action = obj.id_produit ? 'update':'create';
  api('products.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.assign({action},obj))}).then(res=>{
    if(res.ok){ productFormModal.style.display='none'; loadProducts(); loadDashboard(); } else alert(res.error||'Erreur');
  });
});

// Catégories
function loadCategories(){
  api('categories.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'list'})}).then(d=>{
    const tb = document.querySelector('#catsTable tbody'); tb.innerHTML='';
    (d.rows||[]).forEach(c=>{
      const tr=document.createElement('tr'); tr.innerHTML=`<td>${c.id_categorie}</td><td>${c.nom}</td>
        <td><button class="editCat" data-id="${c.id_categorie}">Edit</button> <button class="delCat" data-id="${c.id_categorie}">Suppr</button></td>`;
      tb.appendChild(tr);
    });
    document.querySelectorAll('.editCat').forEach(b=>b.addEventListener('click',e=>{
      const id=e.target.dataset.id; const modal=document.getElementById('catFormModal'); const form=document.getElementById('catForm');
      form.reset(); form.id_categorie.value=id;
      api('categories.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'get',id})}).then(r=>{ form.nom.value=r.row.nom; modal.style.display='block'; });
    }));
    document.querySelectorAll('.delCat').forEach(b=>b.addEventListener('click',e=>{
      if(confirm('Supprimer cette catégorie ?')){
        api('categories.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'delete',id:e.target.dataset.id})}).then(()=>loadCategories());
      }
    }));
  });
}
const catFormModal=document.getElementById('catFormModal');
const catForm=document.getElementById('catForm');
document.getElementById('btnNewCategory').addEventListener('click',()=>{catForm.reset();catForm.id_categorie.value='';catFormModal.style.display='block';});
document.getElementById('cancelCatForm').addEventListener('click',()=>catFormModal.style.display='none');
catForm.addEventListener('submit',e=>{
  e.preventDefault(); const fd=new FormData(catForm); const obj={}; fd.forEach((v,k)=>obj[k]=v);
  const action = obj.id_categorie ? 'update' : 'create';
  api('categories.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.assign({action},obj))}).then(res=>{
    if(res.ok){ catFormModal.style.display='none'; loadCategories(); } else alert(res.error||'Erreur');
  });
});

// Mouvements
const movementFormModal=document.getElementById('movementFormModal');
const movementForm=document.getElementById('movementForm');
document.getElementById('btnNewMovement').addEventListener('click',()=>openMovementForm());
document.getElementById('cancelMovementForm').addEventListener('click',()=>movementFormModal.style.display='none');
function openMovementForm(prodId){
  Promise.all([
    api('products.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'list'})}),
    api('products.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'types'})})
  ]).then(([plist, types])=>{
    const ps = document.getElementById('selProductForMove'); ps.innerHTML='';
    (plist.rows||[]).forEach(p=>{ const opt=document.createElement('option'); opt.value=p.id_produit; opt.textContent=`${p.nom} (${p.quantite_stock})`; ps.appendChild(opt); });
    if(prodId) ps.value = prodId;
    const ts = document.getElementById('selTypeMove'); ts.innerHTML='';
    (types.rows||[]).forEach(t=>{ const opt=document.createElement('option'); opt.value=t.type_mouvement; opt.textContent=t.type_mouvement; ts.appendChild(opt); });
    movementFormModal.style.display='block';
  });
}
movementForm.addEventListener('submit',e=>{
  e.preventDefault(); const fd=new FormData(movementForm); const obj={}; fd.forEach((v,k)=>obj[k]=v);
  api('movements.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.assign({action:'create'},obj))}).then(res=>{
    if(res.ok){ movementFormModal.style.display='none'; loadMovements(); loadProducts(); loadDashboard(); } else alert(res.error||'Erreur');
  });
});
function loadMovements(){
  api('movements.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'list'})}).then(d=>{
    const tb=document.querySelector('#movementsTable tbody'); tb.innerHTML='';
    (d.rows||[]).forEach(m=>{
      const tr=document.createElement('tr');
      tr.innerHTML=`<td>${m.id_mouvement}</td><td>${m.nom}</td><td>${m.type_mouvement}</td><td>${m.quantite}</td><td>${m.nom_user||''}</td><td>${m.date_mouvement}</td>`;
      tb.appendChild(tr);
    });
  });
}

// Journal
function loadJournal(){
  api('journal.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'list'})}).then(d=>{
    const tb=document.querySelector('#journalTable tbody'); tb.innerHTML='';
    (d.rows||[]).forEach(j=>{
      const tr=document.createElement('tr');
      tr.innerHTML=`<td>${j.id_journal}</td><td>${j.action}</td><td>${j.table_concernee||''}</td><td>${j.id_enregistrement||''}</td><td>${j.nom_user||''}</td><td>${j.date_action}</td>`;
      tb.appendChild(tr);
    });
  });
}

loadDashboard();

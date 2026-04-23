document.addEventListener("DOMContentLoaded", () => {
  const sections = {
    connexion: document.getElementById("connexion"),
    inscription: document.getElementById("inscription"),
    oubli: document.getElementById("oubli"),
  };

  function afficherSection(id) {
    for (let key in sections) {
      sections[key].style.display = (key === id) ? "block" : "none";
    }
  }

  document.querySelectorAll("a[data-target]").forEach(link => {
    link.addEventListener("click", e => {
      e.preventDefault();
      const target = link.getAttribute("data-target");
      afficherSection(target);
    });
  });

  document.querySelectorAll("form").forEach(form => {
    form.addEventListener("submit", e => {
      e.preventDefault();

      const formData = new FormData(form);

      // Vérification des champs requis
      let champsValides = true;
      form.querySelectorAll("input[required]").forEach(input => {
        if (!input.value.trim()) champsValides = false;
      });

      if (!champsValides) {
        alert("Tous les champs sont obligatoires !");
        return;
      }

      fetch("traitemen.php", {
        method: "POST",
        body: formData,
      })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        if(data.success && data.redirect){
          window.location.href = data.redirect; // redirection
        }
      })
      .catch(err => {
        console.error("Erreur :", err);
        alert("Erreur réseau ou serveur.");
      });
    });
  });

  afficherSection("connexion");

  // Bouton 👁 pour les passwords
  document.querySelectorAll("input[type='password']").forEach(input => {
    const container = document.createElement("div");
    container.style.position = "relative";
    container.style.display = "flex";
    container.style.alignItems = "center";

    const clone = input.cloneNode(true);
    input.parentNode.replaceChild(container, input);
    container.appendChild(clone);

    const toggle = document.createElement("span");
    toggle.textContent = "👁";
    toggle.style.cursor = "pointer";
    toggle.style.position = "absolute";
    toggle.style.right = "10px";
    toggle.style.color = "#fff";

    toggle.addEventListener("click", () => {
      clone.type = (clone.type === "password") ? "text" : "password";
    });

    container.appendChild(toggle);
  });
});
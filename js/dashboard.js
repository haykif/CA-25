document.addEventListener("DOMContentLoaded", function () {
    const doorStatus = document.getElementById("door-status");
    const presenceStatus = document.getElementById("presence-status");

    function majEtatPorte() {
        console.log("Tentative de récupération de l'état de la porte...");
        fetch("http://173.21.1.14:5000/etat_porte", {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            mode: 'cors'
        })
        .then(response => {
            console.log("Réponse reçue:", response);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Données reçues:", data);
            if (data && data.etat) {
                doorStatus.textContent = data.etat === "ouverte" ? "Ouverte" : "Fermée";
                doorStatus.className = data.etat === "ouverte" ? "status-open" : "status-closed";
            } else {
                throw new Error("Format de données invalide");
            }
        })
        .catch(error => {
            console.error("Erreur détaillée:", error);
            doorStatus.textContent = "Erreur de connexion";
            doorStatus.className = "status-error";
        });
    }

    console.log("Initialisation de la mise à jour de l'état de la porte...");
    majEtatPorte();             // Première mise à jour immédiate
    setInterval(majEtatPorte, 3000);  // Puis mise à jour toutes les 3 secondes
});

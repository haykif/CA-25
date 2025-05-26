document.addEventListener("DOMContentLoaded", function () {
    const doorStatus = document.getElementById("door-status");
    const presenceStatus = document.getElementById("presence-status");
    const lastUpdate = document.getElementById("last-update");

    function majEtatPorte() {
        console.log("Tentative de récupération de l'état de la porte...");
        fetch("http://173.21.1.14:5000/etat_porte", {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            mode: 'cors',
            credentials: 'omit'
        })
        .then(response => {
            console.log("Réponse reçue:", response);
            console.log("Status:", response.status);
            console.log("Headers:", response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Données reçues:", data);
            if (data && data.etat) {
                const etat = data.etat === "ouverte" ? "Ouverte" : "Fermée";
                doorStatus.textContent = etat;
                doorStatus.className = data.etat === "ouverte" ? "status-open" : "status-closed";
                
                // Mise à jour du timestamp
                const now = new Date();
                lastUpdate.textContent = `Dernière mise à jour : ${now.toLocaleTimeString()}`;
            } else {
                throw new Error("Format de données invalide");
            }
        })
        .catch(error => {
            console.error("Erreur détaillée:", error);
            console.error("Stack trace:", error.stack);
            doorStatus.textContent = "Erreur de connexion";
            doorStatus.className = "status-error";
            lastUpdate.textContent = "Dernière mise à jour : Erreur";
        });
    }

    // Vérification initiale de la connexion
    console.log("Vérification de la connexion au serveur...");
    fetch("http://173.21.1.14:5000/etat_porte", { method: 'HEAD' })
        .then(response => {
            console.log("Serveur accessible, initialisation de la mise à jour...");
            majEtatPorte();             // Première mise à jour immédiate
            setInterval(majEtatPorte, 3000);  // Puis mise à jour toutes les 3 secondes
        })
        .catch(error => {
            console.error("Impossible de se connecter au serveur:", error);
            doorStatus.textContent = "Serveur inaccessible";
            doorStatus.className = "status-error";
            lastUpdate.textContent = "Dernière mise à jour : Serveur inaccessible";
        });
});

document.addEventListener("DOMContentLoaded", function () {
    const doorStatus = document.getElementById("door-status");
    const presenceStatus = document.getElementById("presence-status");
    const lastUpdate = document.getElementById("last-update");

    async function majEtatPorte() {
        try {
            console.log("Tentative de connexion au serveur...");
            const response = await fetch("http://173.21.1.14:5000/etat_porte", {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                mode: 'cors'
            });

            console.log("Réponse reçue:", response);
            console.log("Status:", response.status);
            console.log("Headers:", response.headers);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log("Données reçues:", data);

            if (data && data.etat) {
                doorStatus.textContent = data.etat === "ouverte" ? "Ouverte" : "Fermée";
                doorStatus.className = data.etat === "ouverte" ? "status-open" : "status-closed";
                
                // Mise à jour du timestamp
                const now = new Date();
                lastUpdate.textContent = `Dernière mise à jour : ${now.toLocaleTimeString()}`;
            } else {
                throw new Error("Format de données invalide");
            }
        } catch (error) {
            console.error("Erreur détaillée:", error);
            console.error("Stack trace:", error.stack);
            doorStatus.textContent = "Erreur de connexion";
            doorStatus.className = "status-error";
            lastUpdate.textContent = "Dernière mise à jour : Erreur";
        }
    }

    // Première mise à jour immédiate
    majEtatPorte();
    
    // Mise à jour toutes les 3 secondes
    setInterval(majEtatPorte, 3000);
});

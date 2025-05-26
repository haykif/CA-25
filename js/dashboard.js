document.addEventListener("DOMContentLoaded", function () {
    const doorStatus = document.getElementById("door-status");
    const presenceStatus = document.getElementById("presence-status");
    console.log("Élément door-status trouvé:", doorStatus);

    async function majEtatPorte() {
        console.log("Tentative de récupération de l'état de la porte...");
        
        try {
            const response = await fetch("http://173.21.1.14:5000/etat_porte", {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                mode: 'cors',
                credentials: 'include'
            });
            
            console.log("Réponse reçue:", response.status, response.statusText);
            console.log("Headers:", response.headers);
            
            if (response.ok) {
                const data = await response.json();
                console.log("Données reçues:", data);
                
                if (data && data.etat) {
                    doorStatus.textContent = data.etat === "ouverte" ? "Ouverte" : "Fermée";
                    doorStatus.className = data.etat === "ouverte" ? "status-open" : "status-closed";
                } else {
                    throw new Error("Format de données invalide");
                }
            } else {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
        } catch (error) {
            console.error("Erreur détaillée:", error);
            doorStatus.textContent = "Erreur de connexion";
            doorStatus.className = "status-error";
        }
    }

    // Première mise à jour immédiate
    majEtatPorte();
    
    // Mise à jour toutes les 3 secondes
    setInterval(majEtatPorte, 3000);
});

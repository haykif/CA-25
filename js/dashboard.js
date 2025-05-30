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
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                mode: 'cors',
                credentials: 'include'
            });
            
            console.log("Réponse reçue:", response.status, response.statusText);
            
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

    async function majEtatPIR() {
        try {
            const response = await fetch("http://173.21.1.14:5000/etat_pir", {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                mode: 'cors',
                credentials: 'include'
            });

            if (response.ok) {
                const data = await response.json();
                if (data && data.pir) {
                    presenceStatus.textContent = data.pir === "mouvement détecté" ? "Présence détectée" : "Aucun Mouvement";
                    presenceStatus.className = data.pir === "mouvement détecté" ? "status-open" : "status-closed";
                } else {
                    throw new Error("Format de données PIR invalide");
                }
            } else {
                throw new Error(`HTTP error PIR! status: ${response.status}`);
            }
        } catch (error) {
            console.error("Erreur PIR:", error);
            presenceStatus.textContent = "Erreur capteur";
            presenceStatus.className = "status-error";
        }
    }

    // Mise à jour immédiate
    majEtatPorte();
    majEtatPIR();

    // Mise à jour toutes les 3 secondes
    setInterval(majEtatPorte, 3000);
    setInterval(majEtatPIR, 3000);
});
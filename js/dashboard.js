document.addEventListener("DOMContentLoaded", function () {
    const doorStatus = document.getElementById("door-status");
    const presenceStatus = document.getElementById("presence-status");

    function majEtatPorte() {
        fetch("http://173.21.1.14:5000/etat_porte")  // Remplace par l’IP de ton RPi si nécessaire
            .then(response => response.json())
            .then(data => {
                doorStatus.textContent = data.etat === "ouverte" ? "Ouverte" : "Fermée";
            })
            .catch(error => {
                console.error("Erreur récupération état porte:", error);
                doorStatus.textContent = "Erreur";
            });
    }

    majEtatPorte();             // Première mise à jour immédiate
    setInterval(majEtatPorte, 3000);  // Puis mise à jour toutes les 3 secondes
});

function fetchDoorStatus() {
    fetch('../php/etat_Porte.php')
        .then(response => response.json())
        .then(data => {
            const doorStatusElement = document.getElementById('door-status');
            if (data.status) {
                doorStatusElement.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            } else {
                doorStatusElement.textContent = 'Inconnu';
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération de l\'état de la porte:', error);
        });
}

// Mettre à jour l'état toutes les 5 secondes
setInterval(fetchDoorStatus, 5000);

// Charger l'état initial
fetchDoorStatus();
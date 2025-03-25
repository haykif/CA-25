document.addEventListener("DOMContentLoaded", function() {
    let doorStatus = document.getElementById("door-status");
    let presenceStatus = document.getElementById("presence-status");
    // let authorizedCount = document.getElementById("authorized-count");
    
    setTimeout(() => {
        doorStatus.textContent = "Ouverte";
        presenceStatus.textContent = "Oui";
        // authorizedCount.textContent = "5";
    }, 2000);
});
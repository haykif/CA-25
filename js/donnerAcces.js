function donnerAcces(email) {
    if (!confirm("Scanner une carte NFC maintenant ?")) return;

    fetch("http://localhost:5050/scan_nfc", {
        method: "POST"
    })
    .then(res => res.json())
    .then(data => {
        if (data.uid) {
            fetch("insert_carte.php", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `uid=${data.uid}&email=${encodeURIComponent(email)}`
            })
            .then(r => r.json())
            .then(result => {
                if (result.status === "success") {
                    alert("✅ Carte ajoutée avec succès !");
                    location.reload();
                } else {
                    alert("❌ Erreur PHP : " + result.message);
                }
            });
        } else {
            alert("❌ Erreur NFC : " + (data.error || "Carte non détectée"));
        }
    })
    .catch(err => {
        alert("❌ Impossible de contacter le lecteur NFC (Flask doit être lancé)");
        console.error(err);
    });
}

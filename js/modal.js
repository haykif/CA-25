function openModal(formElement) {
    window.currentForm = formElement;
    document.getElementById('modalPython').style.display = 'block';
}

function closeModal() {
    document.getElementById('modalPython').style.display = 'none';
}

function submitJson() {
    const fileInput = document.getElementById('jsonFileInput');
    if (fileInput.files.length === 0) {
        alert('Veuillez charger un fichier JSON.');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(event) {
        const jsonContent = JSON.parse(event.target.result);
        if (jsonContent.UID) {
            const formData = new FormData(window.currentForm);
            formData.append('uid', jsonContent.UID);

            fetch('bouton.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                closeModal();
                window.location.reload();
            })
            .catch(error => {
                alert('Erreur : ' + error);
            });
        } else {
            alert('Le fichier JSON ne contient pas de champ "uid".');
        }
    };
    reader.readAsText(fileInput.files[0]);
}
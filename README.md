# 🚀 Projet Contrôle d'Accès 2025 (CA25)

<div style="display: flex; justify-content: center; gap: 20px;">
   <img src="https://github.com/haykif/CA-25/blob/main/assets/favicon.png" alt="Logo CA25" width="150" title="Logo CA25" target="_blank">
   <img src="https://github.com/haykif/CA-25/blob/main/assets/Apple/applestyle.png" alt="Logo CA25 Apple style" width="150" title="Logo CA25 Apple style" target="_blank">
   <img src="https://github.com/haykif/CA-25/blob/main/assets/light-4k.webp" alt="Website Background light" width="250" title="Website Background light" target="_blank">
   <img src="https://github.com/haykif/CA-25/blob/main/assets/dark-4k.webp" alt="Website Background dark" width="250" title="Website Background dark" target="_blank">
</div>

---

## 📌 Description
Ce projet vise à mettre en place un **système de contrôle d'accès** basé sur des **cartes NFC** pour le local technique informatique du Lycée Charles Poncet.  
Le système permettra :
- D'autoriser l'accès via une carte NFC unique pour chaque utilisateur.
- De gérer les droits d'accès via une interface administrateur.
- D'assurer une surveillance du local en temps réel.
- De stocker un historique des accès et des tentatives non autorisées.

## 🛠️ Technologies utilisées
- **Raspberry Pi** pour le contrôle des accès.
- **Lecteur NFC** pour identifier les cartes.
- **Base de données MySQL** pour stocker les utilisateurs et les accès.
- **Serveur Web (Nginx)** pour l'administration des accès.
- **Interface Web (CSS)** pour l’interface administrateur.
- **Python** pour le développement embarqué sur le RPi et l'application native.

## 📂 Structure du projet
- `assets/` → Fond d'écran adaptative dans le GUI au thème clair ou sombre du system local
- `css/` → Interface web administrateur et formulaire de demande d'accès
- `database/` → Fichiers constituant la base de donnée
- `downloader/` → Executables
- `html/` → Formulaire de demande d'accès
- `js/` → Emulation de l'état ouvert/fermée de la porte
- `php/` → Code pour communiquer avec la Base de données
- `python/` → Code pour le Raspberry Pi
---
## 📝 Installation
1. **Cloner le projet: en ligne de commande**
   ```bash
   git clone https://github.com/haykif/CA-25.git
   cd CA-25```
   
2. **Cloner le projet: avec Github Desktop**
   - Installer sur ce lien: [**Github Desktop**](https://desktop.github.com/download/)
---
## 🔐 Serveur Login / Mot de Passe 
- username: `ca25`
- password: `ca25`

## 💚 aaPanel Login / Mot de Passe
- aaPanel Internet Address: https://80.245.21.40:41022/c712c546
- aaPanel Internal Address: https://173.21.1.164:41022/c712c546
- username: `n2wdd8ov`
- password: `121bb80e`

## ⚙️ Base de Donnée
- username: `dbca25`
- password: `admin`

---

[Dashboard](http://ca25.charles-poncet.net:8083): `http://ca25.charles-poncet.net:8083`

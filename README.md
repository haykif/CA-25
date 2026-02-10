# Projet Contrôle d'Accès 2025 / 2025 Access Control project (CA25)

<div style="display: flex; justify-content: center; gap: 20px;">
   <img src="https://github.com/haykif/CA-25/blob/main/assets/favicon.png" alt="Logo CA25" width="150" title="Logo CA25" target="_blank">
   <img src="https://github.com/haykif/CA-25/blob/main/assets/Apple/applestyle.png" alt="Logo CA25 Apple style" width="150" title="Logo CA25 Apple style" target="_blank">
   <img src="https://github.com/haykif/CA-25/blob/main/assets/light-4k.webp" alt="Website Background light" width="250" title="Website Background light" target="_blank">
   <img src="https://github.com/haykif/CA-25/blob/main/assets/dark-4k.webp" alt="Website Background dark" width="250" title="Website Background dark" target="_blank">
</div>

---

## 📌 Description
This project aims to implement an **access control system** based on **NFC cards** for a server room.

The system will enable:
- Granting access via a unique NFC card for each user.
- Managing access rights via an administrator interface.
- Ensuring real-time monitoring of the room.
- Storing a history of access logs and unauthorized attempts.

## 🛠️ Technologies Used
- **Raspberry Pi** for access control.
- **NFC Reader** to identify cards.
- **MySQL Database** to store users and access logs.
- **Web Server (Nginx)** for access administration.
- **Web Interface (CSS)** for the admin interface.
- **Python** for embedded development on the RPi and the native application.

## 📂 Project Structure
The project is organized as follows:

- `assets/` → GUI background wallpaper adaptive to the local system's light or dark theme
- `css/` → Admin web interface and access request form
- `database/` → Files constituting the database
- `downloader/` → Executables
- `html/` → Access request form
- `js/` → Emulation of the door's open/closed state
- `php/` → Code to communicate with the Database
- `python/` → Code for the Raspberry Pi

---

## 📌 Description
Ce projet vise à mettre en place un **système de contrôle d'accès** basé sur des **cartes NFC** pour un local de serveurs.

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
Le projet est organisé comme suit :

- `assets/` → Fond d'écran adaptative dans le GUI au thème clair ou sombre du system local
- `css/` → Interface web administrateur et formulaire de demande d'accès
- `database/` → Fichiers constituant la base de donnée
- `downloader/` → Executables
- `html/` → Formulaire de demande d'accès
- `js/` → Emulation de l'état ouvert/fermée de la porte
- `php/` → Code pour communiquer avec la Base de données
- `python/` → Code pour le Raspberry Pi

---

## 🔐 Server Login / Password 
- username: `ca25`
- password: `ca25`

## aaPanel Login / Password
- aaPanel Internet Address: https://80.245.21.40:41022/c712c546
- aaPanel Internal Address: https://173.21.1.164:41022/c712c546
- username: `n2wdd8ov`
- password: `121bb80e`

## ⚙️ Datas Base
- username: `dbca25`
- password: `admin`

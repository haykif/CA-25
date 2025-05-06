#!/bin/bash

# === CONFIGURATION ===
INSTALL_DIR="/opt/github-sync-ca25"
SERVICE_NAME="github-sync-ca25"
PYTHON_SCRIPT="$INSTALL_DIR/github_sync_daemon.py"
USER_NAME=$(whoami)

# === CRÉATION DU DOSSIER D’INSTALLATION ===
echo "[INFO] Création du dossier d'installation..."
sudo mkdir -p "$INSTALL_DIR"
sudo chown "$USER_NAME":"$USER_NAME" "$INSTALL_DIR"

# === INSTALLATION DU SCRIPT PYTHON ===
echo "[INFO] Installation du script Python..."
cat > "$PYTHON_SCRIPT" <<EOF
import os
import time
import requests
import hashlib
import shutil

REPO_OWNER = "haykif"
REPO_NAME = "CA-25"
BRANCH = "main"
TARGET_FOLDER = "/opt/github-sync-ca25/CA-25"
SLEEP_INTERVAL = 300  # 5 minutes

def get_repo_tree_sha():
    url = f"https://api.github.com/repos/{REPO_OWNER}/{REPO_NAME}/git/trees/{BRANCH}?recursive=1"
    r = requests.get(url)
    if r.status_code != 200:
        print(f"[ERREUR] Impossible d'accéder à l'API GitHub: {r.status_code}")
        return None
    return hashlib.sha1(r.content).hexdigest()

def clean_folder(path):
    if os.path.exists(path):
        shutil.rmtree(path)
    os.makedirs(path)

def download_repo():
    print("[INFO] Téléchargement du dépôt en cours...")
    url = f"https://api.github.com/repos/{REPO_OWNER}/{REPO_NAME}/git/trees/{BRANCH}?recursive=1"
    r = requests.get(url)
    if r.status_code != 200:
        print(f"[ERREUR] {r.status_code} - {r.text}")
        return

    tree = r.json().get("tree", [])
    for item in tree:
        if item["type"] == "blob":
            file_url = f"https://raw.githubusercontent.com/{REPO_OWNER}/{REPO_NAME}/{BRANCH}/{item['path']}"
            file_path = os.path.join(TARGET_FOLDER, item["path"])
            os.makedirs(os.path.dirname(file_path), exist_ok=True)
            file_data = requests.get(file_url)
            with open(file_path, "wb") as f:
                f.write(file_data.content)
    print("[OK] Dépôt synchronisé.")

def main():
    print("[LANCEMENT] Synchronisation continue du repo GitHub...")
    last_sha = None

    while True:
        try:
            current_sha = get_repo_tree_sha()
            if current_sha and current_sha != last_sha:
                print("[CHANGEMENT] Nouvelle mise à jour détectée.")
                clean_folder(TARGET_FOLDER)
                download_repo()
                last_sha = current_sha
            else:
                print("[R.A.S] Pas de mise à jour.")
        except Exception as e:
            print(f"[EXCEPTION] {e}")

        time.sleep(SLEEP_INTERVAL)

if __name__ == "__main__":
    main()
EOF

chmod +x "$PYTHON_SCRIPT"

# === CRÉATION DU SERVICE SYSTEMD ===
echo "[INFO] Création du service systemd..."
SERVICE_PATH="/etc/systemd/system/${SERVICE_NAME}.service"

sudo bash -c "cat > $SERVICE_PATH" <<EOF
[Unit]
Description=GitHub Sync CA-25 Service
After=network.target

[Service]
ExecStart=/usr/bin/python3 $PYTHON_SCRIPT
Restart=always
User=$USER_NAME

[Install]
WantedBy=multi-user.target
EOF

# === ACTIVATION DU SERVICE ===
echo "[INFO] Activation du service..."
sudo systemctl daemon-reload
sudo systemctl enable $SERVICE_NAME
sudo systemctl start $SERVICE_NAME

echo "[✔️ Done] Le service tourne. Pour voir les logs:  | sudo journalctl -u $SERVICE_NAME -f"
echo "[✔️ Done] Pour arrêter le service:                | sudo systemctl stop $SERVICE_NAME"
echo "[✔️ Done] Pour redémarrer le service:             | sudo systemctl restart $SERVICE_NAME"
echo "[✔️ Done] Pour désactiver le service:             | sudo systemctl disable $SERVICE_NAME"
echo "[✔️ Done] Pour voir l'état du service:            | sudo systemctl status $SERVICE_NAME"
# -*- coding: utf-8 -*-
# -----------------------------------------------------------------------------
# Nom du fichier : AdminSaveRfid.py
# Description   : Application PyQt5 pour la gestion de cartes RFID (ACR122U)
# Projet        : CA-25
# Auteur        : Hamza Aydogdu
# Date de création : 2025-04-15
# Licence       : MIT License
# -----------------------------------------------------------------------------
# © 2025 Hamza Aydogdu. Tous droits réservés.
#
# Ce logiciel est fourni "tel quel", sans garantie d'aucune sorte.
# Vous pouvez l'utiliser, le modifier et le redistribuer selon les termes
# de la licence MIT. Respectez l'auteur et contribuez à l'amélioration
# en conservant cette notice.
# -----------------------------------------------------------------------------
import sys
import json
import datetime
import os
from PyQt5 import QtWidgets, QtGui, QtCore
from smartcard.System import readers
from smartcard.util import toHexString

# Définir un chemin sûr pour stocker le fichier JSON
APP_DIR_NAME = "UIDScanner"
if sys.platform.startswith('win'):
    base_dir = os.path.join(os.environ['USERPROFILE'], "Documents", APP_DIR_NAME)
else:
    base_dir = os.path.join(os.path.expanduser("~"), "Documents", APP_DIR_NAME)
os.makedirs(base_dir, exist_ok=True)
JSON_FILE = os.path.join(base_dir, "rfid_data.json")

class Sidebar(QtWidgets.QFrame):
    def __init__(self, parent=None):
        super().__init__(parent)
        self.setFixedWidth(250)
        self.setStyleSheet("""
            QFrame {
                background-color: qlineargradient(
                    spread:pad, x1:0, y1:0, x2:1, y2:1,
                    stop:0 rgba(244, 167, 87, 230),
                    stop:1 rgba(235, 97, 24, 230)
                );
                padding: 30px 15px;
            }
            QPushButton {
                background-color: #FF914D;
                border: none;
                border-radius: 8px;
                padding: 12px;
                margin-bottom: 15px;
                font-size: 14px;
                font-weight: bold;
                color: black;
                text-align: left;
                cursor: pointer;
            }
            QPushButton:hover {
                background-color: #ffad72;
            }
        """)
        self.layout = QtWidgets.QVBoxLayout(self)
        self.layout.setAlignment(QtCore.Qt.AlignTop)
        self.layout.setSpacing(10)
        self.buttons = []

    def add_button(self, text, callback):
        btn = QtWidgets.QPushButton(text)
        btn.setCursor(QtGui.QCursor(QtCore.Qt.PointingHandCursor))
        btn.clicked.connect(callback)
        self.layout.addWidget(btn)
        self.buttons.append(btn)

class UIDCard(QtWidgets.QFrame):
    def __init__(self, parent=None):
        super().__init__(parent)
        self.setStyleSheet("""
            QFrame {
                background-color: #141f23;
                border-radius: 12px;
                padding: 30px;
            }
            QLabel#title {
                color: white;
                font-size: 22px;
                font-weight: bold;
            }
            QLabel#uid {
                color: #00e5ff;
                font-family: Courier New;
                font-size: 24px;
                margin-top: 20px;
            }
        """)
        layout = QtWidgets.QVBoxLayout(self)
        layout.setAlignment(QtCore.Qt.AlignCenter)

        self.title = QtWidgets.QLabel("Dernier UID scanné")
        self.title.setObjectName("title")
        self.uid_label = QtWidgets.QLabel("Aucun UID")
        self.uid_label.setObjectName("uid")
        self.uid_label.setCursor(QtGui.QCursor(QtCore.Qt.PointingHandCursor))
        self.uid_label.mousePressEvent = self.copy_uid_to_clipboard

        layout.addWidget(self.title)
        layout.addWidget(self.uid_label)

    def update_uid(self, uid):
        self.uid_label.setText(uid)

    def copy_uid_to_clipboard(self, event=None):
        clipboard = QtWidgets.QApplication.clipboard()
        text = self.uid_label.text().strip().split("\n")[0]
        if text.startswith("UID : "):
            text = text.split("UID : ")[-1].strip()
        clipboard.setText(text)
        QtWidgets.QToolTip.showText(QtGui.QCursor.pos(), "UID copié !", self.uid_label)

class RFIDDashboard(QtWidgets.QMainWindow):
    def __init__(self):
        super().__init__()
        self.setWindowTitle("UIDScanner")
        self.resize(900, 500)
        self.setStyleSheet("QMainWindow { background-color: #1f232a; }")

        main_widget = QtWidgets.QWidget()
        self.setCentralWidget(main_widget)
        layout = QtWidgets.QHBoxLayout(main_widget)
        layout.setContentsMargins(0, 0, 0, 0)

        self.sidebar = Sidebar()
        layout.addWidget(self.sidebar)

        self.sidebar.add_button("📡 Lire carte RFID", self.read_card)
        self.sidebar.add_button("📂 Ouvrir le JSON", self.open_json)
        self.sidebar.add_button("📁 Emplacement JSON", self.show_json_path)
        self.sidebar.add_button("🗑️ Vider le JSON", self.clear_json)
        self.sidebar.add_button("🔎 Vérifier JSON", self.check_json_exists)
        self.sidebar.add_button("❓ Aide / Documentation", self.show_help)
        self.sidebar.add_button("🛑 À propos", self.show_about)

        self.uid_card = UIDCard()
        layout.addWidget(self.uid_card, 1)

    def show_help(self):
        doc_text = (
            "1. Posez la carte sur le lecteur RFID.\n\n"
            "2. Cliquez sur 'Lire carte RFID' pour scanner un badge.\n\n"
            "3. Le badge est enregistré dans un fichier JSON local.\n\n"
            "4. L'UID s'affiche à droite en hexadécimal.\n\n"
            "5. Vous pouvez gérer ce fichier JSON avec les boutons à gauche :\n"
            "   - Ouvrir le fichier\n"
            "   - Voir son emplacement\n"
            "   - Le vider\n"
            "   - Vérifier s'il existe\n\n\n"
            "L'UID est également vérifié dans la base de données."
        )
        QtWidgets.QMessageBox.warning(self, "Aide / Documentation", doc_text)

    def show_about(self):
        QtWidgets.QMessageBox.information(self, "À propos", (
            "Badging RFID UI\n"
            "Version : 1.0\n"
            "Projet : CA-25\n"
            "Auteur : Hamza Aydogdu\n"
            "Date : 2025-04-15\n\n"
            "© 2025 Hamza Aydogdu. Tous droits réservés.\n"
            "Licence : MIT\n\n"
            "Ce logiciel est fourni \"tel quel\".\n"
            "Contribuez librement, et gardez cette trace ✊"
        ))

    def save_uid(self, uid_dec):
        record = {
            "UID": uid_dec,
            "timestamp": datetime.datetime.now().isoformat()
        }
        try:
            with open(JSON_FILE, "r", encoding="utf-8") as f:
                data = json.load(f)
        except:
            data = []
        data = [entry for entry in data if entry.get("UID") != uid_dec]
        data.append(record)
        with open(JSON_FILE, "w", encoding="utf-8") as f:
            json.dump(data, f, indent=4)

    def read_card(self):
        try:
            lecteurs = readers()
            if not lecteurs:
                QtWidgets.QMessageBox.warning(self, "Attention", "Aucun lecteur RFID détecté.")
                return

            lecteur = lecteurs[0]
            connection = lecteur.createConnection()

            try:
                connection.connect()
            except:
                QtWidgets.QMessageBox.warning(self, "Attention", "Veuillez poser une carte RFID sur le lecteur.")
                return

            SELECT_UID = [0xFF, 0xCA, 0x00, 0x00, 0x00]
            data, sw1, sw2 = connection.transmit(SELECT_UID)
            if sw1 == 0x90 and sw2 == 0x00:
                uid_hex = toHexString(data).replace(" ", "")
                if not uid_hex or uid_hex == "00":
                    connection.disconnect()
                    QtWidgets.QMessageBox.warning(self, "Erreur", "Aucune carte valide détectée.")
                    return

                uid_dec = int(uid_hex, 16)

                already_scanned = False
                try:
                    with open(JSON_FILE, "r", encoding="utf-8") as f:
                        data = json.load(f)
                        if any(entry["UID"] == uid_dec for entry in data):
                            already_scanned = True
                except:
                    pass
                if not already_scanned:
                    QtWidgets.QMessageBox.information(self, "Nouvelle carte", "✅ Carte enregistrée dans le JSON.")

                status_msg = f"UID : {uid_hex}\nDéjà scannée : {'✅' if already_scanned else '❌'}"
                self.uid_card.update_uid(status_msg)
                QtCore.QTimer.singleShot(4000, lambda: self.uid_card.update_uid(f"UID : {uid_hex}"))
                self.save_uid(uid_dec)
            else:
                raise Exception(f"Erreur : {sw1:02X} {sw2:02X}")
        except Exception as e:
            QtWidgets.QMessageBox.critical(self, "Erreur", str(e))

    def open_json(self):
        path = os.path.abspath(JSON_FILE)
        if not os.path.exists(path):
            QtWidgets.QMessageBox.critical(self, "Erreur", "Le fichier JSON n'existe pas.")
            return
        try:
            if sys.platform.startswith('win'):
                os.startfile(path)
            elif sys.platform == 'darwin':
                os.system(f'open "{path}"')
            else:
                os.system(f'xdg-open "{path}"')
        except Exception as e:
            QtWidgets.QMessageBox.critical(self, "Erreur", f"Impossible d’ouvrir le fichier : {e}")

    def show_json_path(self):
        abs_path = os.path.abspath(JSON_FILE)
        QtWidgets.QMessageBox.information(self, "Emplacement", "Chemin vers le fichier .json:\n\n" + abs_path)

    def clear_json(self):
        try:
            with open(JSON_FILE, "w", encoding="utf-8") as f:
                json.dump([], f, indent=4)
            QtWidgets.QMessageBox.information(self, "Succès", "Fichier vidé.")
        except Exception as e:
            QtWidgets.QMessageBox.critical(self, "Erreur", str(e))

    def check_json_exists(self):
        if os.path.exists(JSON_FILE):
            QtWidgets.QMessageBox.information(self, "Existence", "✅ Le fichier existe.")
        else:
            QtWidgets.QMessageBox.warning(self, "Existence", "❌ Le fichier n'existe pas.")

def main():
    app = QtWidgets.QApplication(sys.argv)
    app.setStyle("Fusion")
    window = RFIDDashboard()
    window.show()
    sys.exit(app.exec_())

if __name__ == "__main__":
    main()
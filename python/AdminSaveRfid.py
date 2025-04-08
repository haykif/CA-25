import sys
import json
import datetime
from PyQt5 import QtWidgets, QtCore, QtGui
from smartcard.System import readers
from smartcard.util import toHexString

JSON_FILE = "rfid_data.json"

class MainWindow(QtWidgets.QMainWindow):
    def __init__(self):
        super().__init__()
        self.setWindowTitle("Lecteur RFID ACR122U")
        self.resize(900, 600)
        
        # Widget central et layout principal
        central_widget = QtWidgets.QWidget()
        self.setCentralWidget(central_widget)
        main_layout = QtWidgets.QVBoxLayout(central_widget)
        main_layout.setContentsMargins(20, 20, 20, 20)
        main_layout.setSpacing(15)
        
        # Zone d'instructions
        instructions = (
            "<b>Instructions :</b><br>"
            "1. Place ta carte RFID sur le lecteur ACR122U.<br>"
            "2. Clique sur <i>Lire la carte RFID</i> pour lancer la lecture.<br>"
            "3. Si la lecture aboutit, l'UID s'affiche et l'enregistrement est ajouté."
        )
        self.instr_label = QtWidgets.QLabel(instructions)
        self.instr_label.setWordWrap(True)
        self.instr_label.setStyleSheet("font-size: 14px;")
        main_layout.addWidget(self.instr_label)
        
        # Bouton pour lire la carte
        self.read_button = QtWidgets.QPushButton("Lire la carte RFID")
        self.read_button.setFixedHeight(40)
        self.read_button.setStyleSheet("""
            QPushButton {
                font-size: 16px;
                background-color: #007ACC;
                color: white;
                border-radius: 5px;
            }
            QPushButton:hover {
                background-color: #005F9E;
            }
        """)
        self.read_button.clicked.connect(self.read_card)
        main_layout.addWidget(self.read_button)
        
        # Label d'information / statut
        self.status_label = QtWidgets.QLabel("")
        self.status_label.setStyleSheet("font-size: 14px; color: #00e676;")
        main_layout.addWidget(self.status_label)
        
        # Tableau d'affichage des enregistrements
        self.table = QtWidgets.QTableWidget()
        self.table.setColumnCount(8)
        self.table.setHorizontalHeaderLabels(["Nom", "Prénom", "Email", "Téléphone", "Motif", "Date", "ID", "UID"])
        self.table.horizontalHeader().setStretchLastSection(True)
        self.table.horizontalHeader().setSectionResizeMode(QtWidgets.QHeaderView.Stretch)
        self.table.setStyleSheet("""
            QTableWidget { 
                background-color: #3e3e3e; 
                color: #f0f0f0; 
                gridline-color: #555; 
                font-size: 13px;
            }
            QHeaderView::section { 
                background-color: #444; 
                color: #f0f0f0; 
                padding: 4px; 
                border: 1px solid #555; 
            }
        """)
        main_layout.addWidget(self.table)
        
        # Charger les données existantes dans la table
        self.load_data_to_table()
        
        # Style global de la fenêtre pour un look moderne/dark
        self.setStyleSheet("""
            QMainWindow { background-color: #2e2e2e; }
            QLabel { color: #f0f0f0; }
        """)
        
    def read_rfid(self):
        """
        Lit la carte via le lecteur ACR122U en envoyant l'APDU pour récupérer l'UID.
        """
        available_readers = readers()
        if not available_readers:
            raise Exception("Aucun lecteur RFID détecté. Branche ton ACR122U!")
        lecteur = available_readers[0]
        connection = lecteur.createConnection()
        connection.connect()
        SELECT_UID = [0xFF, 0xCA, 0x00, 0x00, 0x00]
        data, sw1, sw2 = connection.transmit(SELECT_UID)
        if sw1 == 0x90 and sw2 == 0x00:
            uid = toHexString(data).replace(" ", "")
            return uid
        else:
            raise Exception("Erreur de lecture, status: {:02X} {:02X}".format(sw1, sw2))
    
    def load_data(self):
        """
        Charge les enregistrements du fichier JSON.  
        """
        try:
            with open(JSON_FILE, "r", encoding="utf-8") as f:
                data = json.load(f)
        except (FileNotFoundError, json.JSONDecodeError):
            data = []
        return data
    
    def save_data(self, data_list):
        """
        Sauvegarde les enregistrements dans le fichier JSON.
        """
        with open(JSON_FILE, "w", encoding="utf-8") as f:
            json.dump(data_list, f, indent=4, ensure_ascii=False)
    
    def load_data_to_table(self):
        """
        Charge les données du JSON et les insère dans le tableau.
        """
        data = self.load_data()
        self.table.setRowCount(0)
        for entry in data:
            row_position = self.table.rowCount()
            self.table.insertRow(row_position)
            self.table.setItem(row_position, 0, QtWidgets.QTableWidgetItem(entry.get("Nom", "")))
            self.table.setItem(row_position, 1, QtWidgets.QTableWidgetItem(entry.get("Prenom", "")))
            self.table.setItem(row_position, 2, QtWidgets.QTableWidgetItem(entry.get("Email", "")))
            self.table.setItem(row_position, 3, QtWidgets.QTableWidgetItem(entry.get("Telephone", "")))
            self.table.setItem(row_position, 4, QtWidgets.QTableWidgetItem(entry.get("Motif", "")))
            self.table.setItem(row_position, 5, QtWidgets.QTableWidgetItem(entry.get("Date", "")))
            self.table.setItem(row_position, 6, QtWidgets.QTableWidgetItem(str(entry.get("ID", ""))))
            self.table.setItem(row_position, 7, QtWidgets.QTableWidgetItem(entry.get("UID", "")))
    
    def read_card(self):
        """
        Lance la lecture RFID, enregistre le résultat, met à jour la table,
        et affiche un message d'information ou d'erreur.
        """
        try:
            uid = self.read_rfid()
            # Pour cet exemple, les autres informations sont définies par défaut.
            new_entry = {
                "Nom": "Inconnu",
                "Prenom": "Inconnu",
                "Email": "inconnu@exemple.com",
                "Telephone": "0000000000",
                "Motif": "Test RFID",
                "Date": datetime.datetime.now().strftime("%d-%m-%Y %H:%M:%S"),
                "ID": len(self.load_data()) + 1,
                "UID": uid
            }
            data_list = self.load_data()
            data_list.append(new_entry)
            self.save_data(data_list)
            self.load_data_to_table()
            self.status_label.setText(f"Carte {uid} lue et enregistrée avec succès!")
        except Exception as e:
            QtWidgets.QMessageBox.critical(self, "Erreur", str(e))
            self.status_label.setText("")

def main():
    app = QtWidgets.QApplication(sys.argv)
    window = MainWindow()
    window.show()
    sys.exit(app.exec_())

if __name__ == "__main__":
    main()

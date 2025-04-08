import tkinter as tk
from tkinter import messagebox
import json
import datetime

# Bibliothèque pour interagir avec le lecteur ACR122U
from smartcard.System import readers
from smartcard.util import toHexString

def read_rfid():
    """
    Lit la carte via le lecteur ACR122U.
    Cette fonction vérifie d'abord si un lecteur est disponible, puis envoie la commande APDU
    [0xFF, 0xCA, 0x00, 0x00, 0x00] pour récupérer l'UID de la carte.
    """
    # Récupère les lecteurs disponibles
    available_readers = readers()
    if not available_readers:
        raise Exception("Aucun lecteur RFID détecté. Branche ton ACR122U!")
    
    lecteur = available_readers[0]  # On prend le premier lecteur disponible
    connection = lecteur.createConnection()
    connection.connect()
    
    # Commande APDU pour obtenir l'UID de la carte
    SELECT_UID = [0xFF, 0xCA, 0x00, 0x00, 0x00]
    data, sw1, sw2 = connection.transmit(SELECT_UID)
    
    # Vérification du status de la réponse (0x90 0x00 indique le succès)
    if sw1 == 0x90 and sw2 == 0x00:
        uid = toHexString(data).replace(" ", "")
        return uid
    else:
        raise Exception("Erreur de lecture, status: {:02X} {:02X}".format(sw1, sw2))

def save_card(card):
    """
    Enregistre la lecture RFID dans un fichier JSON.
    Ajoute chaque lecture (avec timestamp) à une liste dans 'rfid_data.json'.
    """
    data_entry = {
        "card": card,
        "timestamp": datetime.datetime.now().isoformat()
    }
    try:
        # Essaye de lire les données existantes, sinon initialise la liste
        try:
            with open("rfid_data.json", "r") as file:
                rfid_data = json.load(file)
        except FileNotFoundError:
            rfid_data = []
        
        # Ajoute la nouvelle lecture
        rfid_data.append(data_entry)
        with open("rfid_data.json", "w") as file:
            json.dump(rfid_data, file, indent=4)
    except Exception as e:
        messagebox.showerror("Erreur", f"Erreur en enregistrant la carte: {e}")

def read_card():
    """
    Fonction appelée lors du clic sur le bouton.
    Lance la lecture de la carte et affiche le résultat dans l'IHM,
    puis sauvegarde les informations dans le fichier JSON.
    """
    try:
        card = read_rfid()
        label_card.config(text="Carte RFID lue: " + card)
        save_card(card)
        messagebox.showinfo("Succès", "Carte enregistrée!")
    except Exception as e:
        messagebox.showerror("Erreur", str(e))

# Création de l'interface graphique avec Tkinter
root = tk.Tk()
root.title("Lecteur RFID ACR122U")
root.geometry("400x200")

label_card = tk.Label(root, text="Pas de carte lue", font=("Helvetica", 14))
label_card.pack(pady=20)

btn_read = tk.Button(root, text="Lire la carte RFID", font=("Helvetica", 12), command=read_card)
btn_read.pack(pady=10)

# Lancement de la boucle principale de l'IHM
root.mainloop()

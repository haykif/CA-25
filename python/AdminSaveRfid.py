import tkinter as tk
from tkinter import messagebox
import json
import random
import datetime
# Si t'utilises un vrai lecteur RFID, dé-commente la ligne suivante et adapte en conséquence.
# import serial  

def read_rfid():
    """
    Fonction pour lire la carte RFID.
    Ici c'est simulé avec un identifiant aléatoire.
    Pour un vrai lecteur, tu pourrais utiliser pyserial comme :
    
        with serial.Serial('/dev/ttyUSB0', 9600, timeout=1) as ser:
            card = ser.readline().decode('utf-8').strip()
            return card
    """
    # Simulation d'une lecture RFID: génère un ID hexadécimal aléatoire
    card = hex(random.getrandbits(64))[2:].upper()  
    return card

def save_card(card):
    """
    Fonction pour enregistrer la lecture RFID dans un fichier JSON.
    Chaque lecture est ajoutée à une liste sous forme d'un dictionnaire avec l'ID et un timestamp.
    """
    data = {
        "card": card,
        "timestamp": datetime.datetime.now().isoformat()
    }
    try:
        # Tente de lire les données existantes
        try:
            with open("rfid_data.json", "r") as file:
                rfid_data = json.load(file)
        except FileNotFoundError:
            rfid_data = []  # Fichier inexistant, on part d'une liste vide

        rfid_data.append(data)
        with open("rfid_data.json", "w") as file:
            json.dump(rfid_data, file, indent=4)
    except Exception as e:
        messagebox.showerror("Erreur", f"Erreur en enregistrant la carte: {e}")

def read_card():
    """
    Fonction appelée lors du clic sur le bouton.
    Lit la carte, met à jour l'IHM, sauvegarde les données et affiche une notification.
    """
    card = read_rfid()
    label_card.config(text="Carte RFID lue: " + card)
    save_card(card)
    messagebox.showinfo("Succès", "Carte enregistrée !")

# Création de la fenêtre principale
root = tk.Tk()
root.title("RFID Reader")
root.geometry("400x200")

# Label pour afficher la lecture de la carte
label_card = tk.Label(root, text="Pas de carte lue", font=("Helvetica", 14))
label_card.pack(pady=20)

# Bouton pour lancer la lecture RFID
btn_read = tk.Button(root, text="Lire la carte RFID", font=("Helvetica", 12), command=read_card)
btn_read.pack(pady=10)

# Lancement de la boucle principale de l'IHM
root.mainloop()

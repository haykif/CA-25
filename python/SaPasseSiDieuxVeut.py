import RPi.GPIO as GPIO
import time
from mfrc522 import SimpleMFRC522

# 🎯 Configuration du relais et du lecteur RFID
RELAY_PIN = 18  # Modifier selon ton branchement
GPIO.setmode(GPIO.BCM)
GPIO.setup(RELAY_PIN, GPIO.OUT)

# 🛠️ Assurer que la gâche est fermée au démarrage
GPIO.output(RELAY_PIN, GPIO.HIGH)  # La gâche reste fermée par défaut

# Initialisation du lecteur RFID
reader = SimpleMFRC522()

def activer_gache():
    """ Ouvre la gâche pendant 3 secondes puis la referme """
    print("✅ Accès accordé ! Ouverture de la porte...")
    GPIO.output(RELAY_PIN, GPIO.LOW)  # Active le relais (ouvre la gâche)
    time.sleep(3)  # La gâche reste ouverte pendant 3 sec
    GPIO.output(RELAY_PIN, GPIO.HIGH)  # Désactive le relais (ferme la gâche)
    print("🔒 Porte refermée.")

def read_card():
    """ Lecture de la carte RFID """
    print("📡 En attente d'une carte RFID...")
    try:
        card_id, text = reader.read()  # Lire l'ID de la carte
        print(f"📡 Carte détectée : {card_id}")
        activer_gache()  # Toute carte active la gâche

    except Exception as e:
        print(f"⚠️ Erreur de lecture RFID : {e}")

try:
    while True:
        read_card()  # Attente de badgeage

except KeyboardInterrupt:
    print("🛑 Arrêt du programme.")
finally:
    GPIO.cleanup()

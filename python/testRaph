import nfc
import RPi.GPIO as GPIO
import time

# 🎯 CONFIGURATION DE LA GÂCHE ÉLECTRIQUE
RELAIS_PIN = 17  # GPIO 17 (adapter selon ton branchement)
GPIO.setmode(GPIO.BCM)
GPIO.setup(RELAIS_PIN, GPIO.OUT)
GPIO.output(RELAIS_PIN, GPIO.HIGH)  # Par défaut, la gâche est verrouillée

def activer_gache():
    """ Active la gâche électrique pendant 3 secondes """
    print("✅ Accès autorisé ! Ouverture de la porte...")
    GPIO.output(RELAIS_PIN, GPIO.LOW)  # Déverrouille la gâche
    time.sleep(3)
    GPIO.output(RELAIS_PIN, GPIO.HIGH)  # Verrouille la gâche après 3 sec
    print("🔒 Porte refermée.")

def on_connect(tag):
    """ Fonction appelée lorsqu'une carte NFC est détectée """
    card_id = tag.identifier.hex()  # Convertir l'ID en hexadécimal
    print(f"📡 Carte détectée : {card_id}")
    
    # Toute carte active la gâche
    activer_gache()

    return False  # Ne garde pas la carte connectée

def read_card():
    """ Lecture continue des cartes NFC """
    with nfc.ContactlessFrontend('usb') as clf:
        print("📡 En attente d'une carte NFC...")
        while True:
            try:
                clf.connect(rdwr={'on-connect': on_connect})
            except Exception as e:
                print(f"⚠️ Erreur NFC : {e}")

try:
    read_card()
except KeyboardInterrupt:
    print("⏹️ Arrêt du programme.")
    GPIO.cleanup()  # Libérer les GPIO avant de quitter

import nfc
import RPi.GPIO as GPIO
import time
from datetime import datetime



# CONFIGURATION DE LA GÂCHE ÉLECTRIQUE
RELAIS_PIN = 11  # GPIO 17 (adapter selon ton branchement)
GPIO.setmode(GPIO.BCM)
GPIO.setup(RELAIS_PIN, GPIO.OUT)
GPIO.output(RELAIS_PIN, GPIO.HIGH)  # Par défaut, la gâche est verrouillée


def activer_gache():
    """ Active la gâche électrique pendant 3 secondes """
    print(" ✅ Accès autorisé ! Ouverture de la porte...")
    GPIO.output(RELAIS_PIN, GPIO.LOW)  # Déverrouille la gâche
    time.sleep(3)
    GPIO.output(RELAIS_PIN, GPIO.HIGH)  # Verrouille la gâche après 3 sec
    print("🔒 Porte refermée.")


def on_connect(tag):
    """ Fonction appelée lorsqu'une carte NFC est détectée """
    card_id = tag.identifier.hex()  # Convertir l'ID en hexadécimal
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S") #format date et heure


    print(f" 📡 Carte détectée : {card_id}")
    print(f"Badgeage effectué a : {current_time}") #ajout de la date et de l'heure
    
    # Toute carte active la gâche
    activer_gache()

    return False  # Ne garde pas la carte connectée

# Lecture de la carte
def read_card():
    """ Lecture continue des cartes NFC """
    with nfc.ContactlessFrontend('usb') as clf:
        print(" 📡 En attente d'une carte NFC...")
        try:
            clf.connect(rdwr={'on-connect': on_connect})
        except Exception as e:
            print(f" ⚠️  Erreur NFC : {e}")

read_card()

# Faire un choix.
def choisirUnChoix():
    decision = """
        Choisissez:
	'1' si vous voulez continuer le scan.
 	'2' si vous voulez abandonner le scan.
    """
    print(decision + "\n")
    choix = int(input("Faites votre choix: "))

    if choix == 1:
        print(" 🔁 Execution du programme...")
        read_card()
        choisirUnChoix()

    elif choix == 2:
        print(" 🛑 Arrêt du programme...")
        GPIO.cleanup()
        return None

    else:
        try:
            print(" ❌", choix, "n'est pas une réponse valable. Veuillez réessayer.")

        except ValueError:
            print("noone")

        choisirUnChoix()

choisirUnChoix()

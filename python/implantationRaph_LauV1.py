import mysql.connector
import time
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
from gpiozero import MotionSensor
from gpiozero.pins.rpigpio import RPiGPIOFactory
from gpiozero import Device

# 🔧 Forcer gpiozero à utiliser RPi.GPIO et éviter l'importation de lgpio
Device.pin_factory = RPiGPIOFactory()


# === CONFIGURATION DES PINS ===
GPIO.setmode(GPIO.BCM)



RELAY_PIN = 18
LED_VERTE = 20
LED_ROUGE = 21
CAPTEUR_PORTE = 17  # capteur magnétique
pir = MotionSensor(26)

GPIO.setup(RELAY_PIN, GPIO.OUT)
GPIO.setup(LED_VERTE, GPIO.OUT)
GPIO.setup(LED_ROUGE, GPIO.OUT)
GPIO.setup(CAPTEUR_PORTE, GPIO.IN, pull_up_down=GPIO.PUD_UP)

# Gâche fermée par défaut
GPIO.output(RELAY_PIN, GPIO.HIGH)

# === RFID ===
reader = SimpleMFRC522()

# === Base de données ===
DB_CONFIG = {
    'user': 'dbca25',
    'password': 'admin',
    'host': '173.21.1.162',
    'port': 3306,
    'database': 'dbca25'
}

# === GESTION PORTE MAGNÉTIQUE ===
def etat_filtre():
    """Filtrage anti-rebond pour le capteur magnétique"""
    etat1 = GPIO.input(CAPTEUR_PORTE)
    time.sleep(0.1)
    etat2 = GPIO.input(CAPTEUR_PORTE)
    return etat1 if etat1 == etat2 else None

def afficher_etat_porte():
    etat = etat_filtre()
    if etat is not None:
        if etat == GPIO.LOW:
            print("🚪 La porte est FERMÉE")
        else:
            print("🚪 La porte est OUVERTE !")


# === GESTION DE LA GÂCHE ===
def activer_gache():
    #print("✅ Ouverture de la porte...")
    GPIO.output(RELAY_PIN, GPIO.LOW)
    time.sleep(3)
    GPIO.output(RELAY_PIN, GPIO.HIGH)
    #print("🔒 Porte refermée.")

# === GESTION BASE DE DONNÉES ===
def enregistrer_acces(uid, autorise):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()

        date_entree = time.strftime('%Y-%m-%d %H:%M:%S')
        resultat = "Accès autorisé" if autorise else "Accès refusé"
        etat_porte = "1"
        IdUser = "1"

        sql = """
        INSERT INTO Acces_log (Date_heure_entree, Resultat_tentative, Presence, Etat_porte, RFID_utilise, IdUser)
        VALUES (%s, %s, %s, %s, %s, %s)
        """
        valeurs = (date_entree, resultat, True, etat_porte, uid, IdUser)
        cursor.execute(sql, valeurs)
        conn.commit()

        print(f"📌 {resultat} | UID : {uid} enregistré.")

    except mysql.connector.Error as err:
        print(f"⚠️ Erreur MySQL : {err}")
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

# === LECTURE ET TRAITEMENT RFID ===
def verifier_et_traiter(uid):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Carte WHERE RFID = %s", (uid,))
        carte = cursor.fetchone()

        if carte:
            print("✅ Carte autorisée")
            GPIO.output(LED_VERTE, GPIO.LOW)
            activer_gache()
            time.sleep(1)
            GPIO.output(LED_VERTE, GPIO.HIGH)
            enregistrer_acces(uid, True)
        else:
            print("❌ Carte non autorisée")
            GPIO.output(LED_ROUGE, GPIO.LOW)
            time.sleep(2)
            GPIO.output(LED_ROUGE, GPIO.HIGH)
            enregistrer_acces(uid, False)

    except mysql.connector.Error as err:
        print(f"⚠️ Erreur MySQL : {err}")
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

# === BOUCLE PRINCIPALE ===
def boucle_principale():
    try:
        while True:
            afficher_etat_porte()

            print("📡 En attente d'une carte RFID...")
            card_id = reader.read_id()
            print(f"📡 Carte détectée : {card_id}")
            verifier_et_traiter(card_id)

            time.sleep(1)

    except KeyboardInterrupt:
        print("\n🛑 Programme interrompu.")
    finally:
        GPIO.cleanup()
        print("🔧 GPIO nettoyés.")

# === LANCEMENT ===
if __name__ == "__main__":
    boucle_principale()

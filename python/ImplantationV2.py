import mysql.connector
import time
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
from gpiozero.pins.rpigpio import RPiGPIOFactory
from gpiozero import Device

# 🔧 Forcer gpiozero à utiliser RPi.GPIO
Device.pin_factory = RPiGPIOFactory()

# === CONFIGURATION DES PINS ===
GPIO.setmode(GPIO.BCM)

RELAY_PIN = 18
LED_VERTE = 20
LED_ROUGE = 21
CAPTEUR_PORTE = 17  # capteur magnétique
PIR_PIN = 4         # Détecteur de mouvement

GPIO.setup(RELAY_PIN, GPIO.OUT)
GPIO.setup(LED_VERTE, GPIO.OUT)
GPIO.setup(LED_ROUGE, GPIO.OUT)
GPIO.setup(CAPTEUR_PORTE, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(PIR_PIN, GPIO.IN)  # 🔧 Ajout nécessaire !

# Gâche fermée par défaut
GPIO.output(RELAY_PIN, GPIO.HIGH)

# === CONFIGURATION BDD ===
DB_CONFIG = {
    'user': 'dbca25',
    'password': 'admin',
    'host': '173.21.1.164',
    'port': 3306,
    'database': 'dbca25'
}

# === PORTE ===
def etat_filtre():
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

# === GÂCHE ÉLECTRIQUE ===
def activer_gache():
    print("✅ Ouverture de la porte...")
    GPIO.output(RELAY_PIN, GPIO.LOW)
    time.sleep(3)
    GPIO.output(RELAY_PIN, GPIO.HIGH)
    print("🔒 Porte refermée.")

# === LOG BDD ===
def enregistrer_acces(uid, autorise):
    conn = None
    cursor = None
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()

        date_entree = time.strftime('%Y-%m-%d %H:%M:%S')
        resultat = "Accès autorisé" if autorise else "Accès refusé"
        etat_porte = "1"
        IdUser = "1"

        sql = """
        INSERT INTO Acces_log (Date_heure_entree, Resultat_tentative, Presence, Etat_porte, UID, IdUser)
        VALUES (%s, %s, %s, %s, %s, %s)
        """
        valeurs = (date_entree, resultat, True, etat_porte, uid, IdUser)
        cursor.execute(sql, valeurs)
        conn.commit()

        print(f"📌 {resultat} | UID : {uid} enregistré.")

    except mysql.connector.Error as err:
        print(f"⚠️ Erreur MySQL : {err}")
    finally:
        try:
            if cursor:
                cursor.close()
            if conn and conn.is_connected():
                conn.close()
        except:
            pass

# === VÉRIF RFID ===
def verifier_et_traiter(uid):
    conn = None
    cursor = None
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Carte WHERE RFID = %s", (int(uid),))
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
        try:
            if cursor:
                cursor.close()
            if conn and conn.is_connected():
                conn.close()
        except:
            pass

# === BOUCLE PRINCIPALE ===
def boucle_principale():
    reader = SimpleMFRC522()
    try:
        while True:
            afficher_etat_porte()

            ##### DETECTEUR DE MOUVEMENT #####
            if GPIO.input(PIR_PIN):
                print("⚠️ Mouvement détecté sur GPIO4 (broche 7) !")
            else:
                print("Aucun mouvement détecté")
            ##################################

            print("📡 En attente d'une carte RFID...")
            try:
                uid, _ = reader.read()
                print(f"📡 Carte détectée : {uid}")
                verifier_et_traiter(uid)
            except Exception as e:
                print(f"⚠️ Erreur RFID : {e}")
            time.sleep(1)
    except KeyboardInterrupt:
        print("\n🛑 Programme interrompu.")
    finally:
        GPIO.cleanup()
        print("🔧 GPIO nettoyés.")

# === LANCEMENT ===
if __name__ == "__main__":
    boucle_principale()

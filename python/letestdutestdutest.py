import mysql.connector
import time
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
from gpiozero.pins.rpigpio import RPiGPIOFactory
from gpiozero import Device
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# Pour gpiozero
Device.pin_factory = RPiGPIOFactory()

# === CONFIGURATION DES PINS ===
GPIO.setmode(GPIO.BCM)
RELAY_PIN = 18
LED_VERTE = 20
LED_ROUGE = 21
CAPTEUR_PORTE = 17

GPIO.setup(RELAY_PIN, GPIO.OUT)
GPIO.setup(LED_VERTE, GPIO.OUT)
GPIO.setup(LED_ROUGE, GPIO.OUT)
GPIO.setup(CAPTEUR_PORTE, GPIO.IN, pull_up_down=GPIO.PUD_UP)

GPIO.output(RELAY_PIN, GPIO.HIGH)

# === CONFIGURATION MAIL
expediteur = "carteacces99@gmail.com"
mot_de_passe = "llvz ctlm vjas xyfq"
destinataire = "laurent14123@gmail.com"

def envoyer_mail(uid):
    heure = time.strftime('%d-%m-%Y à %H:%M:%S')
    message = MIMEMultipart()
    message["From"] = expediteur
    message["To"] = destinataire
    message["Subject"] = "ENTREE NON AUTORISEE"
    corps = f"Entrée interdite détectée le {heure}.\nUID : {uid}"
    message.attach(MIMEText(corps, "plain"))

    try:
        with smtplib.SMTP_SSL("smtp.gmail.com", 465) as serveur:
            serveur.login(expediteur, mot_de_passe)
            serveur.sendmail(expediteur, destinataire, message.as_string())
            print("📧 Mail envoyé")
    except Exception as e:
        print(f"❌ Erreur mail : {e}")

# === CONFIGURATION BDD
DB_CONFIG = {
    'user': 'dbca25',
    'password': 'admin',
    'host': '173.21.1.164',
    'port': 3306,
    'database': 'dbca25'
}

# === ENREGISTREMENTS ===

def enregistrer_entree(uid):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        date_entree = time.strftime('%Y-%m-%d %H:%M:%S')
        sql = """
        INSERT INTO Acces_log (Date_heure_entree, Resultat_tentative, Presence, Etat_porte, UID, IdUser)
        VALUES (%s, %s, %s, %s, %s, %s)
        """
        cursor.execute(sql, (date_entree, "Accès autorisé", True, "1", uid, "1"))
        conn.commit()
        print("✅ Entrée enregistrée.")
    except Exception as e:
        print(f"❌ Erreur MySQL (entrée) : {e}")
    finally:
        cursor.close()
        conn.close()

def enregistrer_sortie(uid):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute("""
            SELECT idAcces FROM Acces_log WHERE UID = %s ORDER BY idAcces DESC LIMIT 1
        """, (uid,))
        last = cursor.fetchone()
        if last:
            log_id = last[0]
            cursor.execute("""
                UPDATE Acces_log SET Date_heure_sortie = %s WHERE idAcces = %s
            """, (time.strftime('%Y-%m-%d %H:%M:%S'), log_id))
            conn.commit()
            print(f"🕒 Sortie enregistrée pour ID {log_id}")
    except Exception as e:
        print(f"❌ Erreur MySQL (sortie) : {e}")
    finally:
        cursor.close()
        conn.close()

# === PORTE
def attendre_ouverture(message="Attente ouverture porte..."):
    print(f"👁️ {message}")
    etat_precedent = GPIO.input(CAPTEUR_PORTE)
    while True:
        etat_actuel = GPIO.input(CAPTEUR_PORTE)
        if etat_actuel == GPIO.HIGH and etat_precedent == GPIO.LOW:
            print("🚪 Porte ouverte")
            break
        etat_precedent = etat_actuel
        time.sleep(0.2)

# === GÂCHE
def activer_gache():
    print("🔓 Gâche activée (10s)")
    GPIO.output(RELAY_PIN, GPIO.LOW)
    time.sleep(10)
    GPIO.output(RELAY_PIN, GPIO.HIGH)
    print("🔒 Porte refermée")

# === RFID
def verifier_carte(uid):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Carte WHERE RFID = %s", (int(uid),))
        carte = cursor.fetchone()
        return carte is not None
    except Exception as e:
        print(f"❌ Erreur DB RFID : {e}")
        return False
    finally:
        cursor.close()
        conn.close()

# === BOUCLE PRINCIPALE
def boucle_principale():
    reader = SimpleMFRC522()
    try:
        while True:
            print("\n📡 En attente d'une carte RFID...")
            uid, _ = reader.read()
            print(f"📡 Carte détectée : {uid}")

            if verifier_carte(uid):
                GPIO.output(LED_VERTE, GPIO.LOW)
                activer_gache()
                GPIO.output(LED_VERTE, GPIO.HIGH)

                # Attente de la première ouverture (entrée)
                attendre_ouverture("🕒 En attente de l’ouverture de la porte (entrée)...")
                enregistrer_entree(uid)

                # Attente de la deuxième ouverture (sortie)
                attendre_ouverture("🕒 En attente de l’ouverture de la porte (sortie)...")
                enregistrer_sortie(uid)

            else:
                GPIO.output(LED_ROUGE, GPIO.LOW)
                time.sleep(2)
                GPIO.output(LED_ROUGE, GPIO.HIGH)
                enregistrer_entree(uid)  # Enregistre tentative refusée
                envoyer_mail(uid)

    except KeyboardInterrupt:
        print("🛑 Arrêt manuel.")
    finally:
        GPIO.cleanup()

# === LANCEMENT
if __name__ == "__main__":
    boucle_principale()

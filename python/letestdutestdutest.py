import mysql.connector
import time
import threading
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
from gpiozero.pins.rpigpio import RPiGPIOFactory
from gpiozero import Device
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# 🔧 Forcer gpiozero à utiliser RPi.GPIO
Device.pin_factory = RPiGPIOFactory()

# === CONFIGURATION DES PINS ===
GPIO.setmode(GPIO.BCM)
RELAY_PIN = 18
LED_VERTE = 20
LED_ROUGE = 21
LED_JAUNE = 16
CAPTEUR_PORTE = 17
PIR_PIN = 4

GPIO.setup(RELAY_PIN, GPIO.OUT)
GPIO.setup(LED_VERTE, GPIO.OUT)
GPIO.setup(LED_ROUGE, GPIO.OUT)
GPIO.setup(LED_JAUNE, GPIO.OUT)
GPIO.setup(CAPTEUR_PORTE, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(PIR_PIN, GPIO.IN)
GPIO.output(RELAY_PIN, GPIO.HIGH)

# === CONFIGURATION MAIL ===
expediteur = "carteacces99@gmail.com"
mot_de_passe = "llvz ctlm vjas xyfq"
destinataire = "laurent14123@gmail.com"

def envoyer_mail(uid):
    heure = time.strftime('%d-%m-%Y à %H:%M:%S')
    sujet = "ENTREE NON AUTORISEE"
    corps = f"Entrée interdite détectée le {heure}.\nUID : {uid}"
    message = MIMEMultipart()
    message["From"] = expediteur
    message["To"] = destinataire
    message["Subject"] = sujet
    message.attach(MIMEText(corps, "plain"))

    try:
        with smtplib.SMTP_SSL("smtp.gmail.com", 465) as serveur:
            serveur.login(expediteur, mot_de_passe)
            serveur.sendmail(expediteur, destinataire, message.as_string())
            print("📧 Mail envoyé")
    except Exception as e:
        print(f"⚠️ Erreur envoi mail : {e}")

# === CONFIGURATION BDD ===
DB_CONFIG = {
    'user': 'dbca25',
    'password': 'admin',
    'host': '173.21.1.164',
    'port': 3306,
    'database': 'dbca25'
}

# === GESTION PORTE ===
def etat_filtre():
    etat1 = GPIO.input(CAPTEUR_PORTE)
    time.sleep(0.1)
    etat2 = GPIO.input(CAPTEUR_PORTE)
    return etat1 if etat1 == etat2 else None

def afficher_etat_porte():
    etat = etat_filtre()
    if etat is not None:
        statut = "fermée" if etat == GPIO.LOW else "ouverte"
        print(f"🚪 La porte est {statut.upper()}")
        with open('../data/door_status.txt', 'w') as f:
            f.write(statut)

# === GÂCHE ===
def activer_gache():
    print("✅ Ouverture de la porte...")
    GPIO.output(RELAY_PIN, GPIO.LOW)
    time.sleep(10)
    GPIO.output(RELAY_PIN, GPIO.HIGH)
    print("🔒 Porte refermée.")

# === BDD : ENREGISTREMENT
def enregistrer_acces(uid, autorise):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        date_entree = time.strftime('%Y-%m-%d %H:%M:%S')
        resultat = "Accès autorisé" if autorise else "Accès refusé"
        sql = """
            INSERT INTO Acces_log 
            (Date_heure_entree, Resultat_tentative, Presence, Etat_porte, UID, IdUser)
            VALUES (%s, %s, %s, %s, %s, %s)
        """
        valeurs = (date_entree, resultat, True, "1", uid, "1")
        cursor.execute(sql, valeurs)
        conn.commit()
        print(f"📌 {resultat} | UID : {uid} enregistré.")
    except mysql.connector.Error as err:
        print(f"⚠️ Erreur MySQL : {err}")
    finally:
        if cursor: cursor.close()
        if conn and conn.is_connected(): conn.close()

def enregistrer_heure_sortie(uid):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        heure_sortie = time.strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute("""
            SELECT idAcces FROM Acces_log
            WHERE UID = %s
            ORDER BY idAcces DESC
            LIMIT 1
        """, (uid,))
        last = cursor.fetchone()
        if last:
            cursor.execute("""
                UPDATE Acces_log SET Date_heure_sortie = %s
                WHERE idAcces = %s
            """, (heure_sortie, last[0]))
            conn.commit()
            print(f"🕒 Sortie enregistrée pour ID {last[0]}")
        else:
            print("⚠️ Aucun log trouvé")
    except mysql.connector.Error as err:
        print(f"⚠️ Erreur MySQL sortie : {err}")
    finally:
        if cursor: cursor.close()
        if conn and conn.is_connected(): conn.close()

# === RFID ===
def verifier_et_traiter(uid):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Carte WHERE RFID = %s", (int(uid),))
        carte = cursor.fetchone()
        if carte:
            print("✅ Carte autorisée")
            GPIO.output(LED_VERTE, GPIO.LOW)
            activer_gache()
            GPIO.output(LED_VERTE, GPIO.HIGH)
            enregistrer_acces(uid, True)
        else:
            print("❌ Carte non autorisée")
            GPIO.output(LED_ROUGE, GPIO.LOW)
            time.sleep(2)
            GPIO.output(LED_ROUGE, GPIO.HIGH)
            enregistrer_acces(uid, False)
            envoyer_mail(uid)
    except mysql.connector.Error as err:
        print(f"⚠️ Erreur MySQL : {err}")
    finally:
        if cursor: cursor.close()
        if conn and conn.is_connected(): conn.close()

# === SORTIE ===
def detecter_sortie(uid):
    def surveillance():
        print("👁️ Surveillance ouverture porte...")
        prev = GPIO.input(CAPTEUR_PORTE)
        while True:
            actuel = GPIO.input(CAPTEUR_PORTE)
            if actuel == GPIO.HIGH and prev == GPIO.LOW:
                print("🚪 Porte réouverte → enregistrement sortie")
                enregistrer_heure_sortie(uid)
                break
            prev = actuel
            time.sleep(0.2)
    t = threading.Thread(target=surveillance)
    t.start()

# === BOUCLE PRINCIPALE ===
def boucle_principale():
    reader = SimpleMFRC522()
    try:
        while True:
            afficher_etat_porte()
            mouvement = GPIO.input(PIR_PIN)
            GPIO.output(LED_JAUNE, GPIO.HIGH if mouvement else GPIO.LOW)
            print("📡 En attente carte...")
            try:
                uid, _ = reader.read()
                print(f"📡 UID détecté : {uid}")
                verifier_et_traiter(uid)
                detecter_sortie(uid)
            except Exception as e:
                print(f"⚠️ Erreur RFID : {e}")
            time.sleep(1)
    except KeyboardInterrupt:
        print("\n🛑 Interruption")
    finally:
        GPIO.cleanup()
        print("🔧 Nettoyage GPIO terminé.")

# === LANCEMENT ===
if __name__ == "__main__":
    boucle_principale()

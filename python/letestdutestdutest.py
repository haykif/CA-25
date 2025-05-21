import mysql.connector
import time
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
from gpiozero.pins.rpigpio import RPiGPIOFactory
from gpiozero import Device
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# 🔧 Forcer gpiozero à utiliser RPi.GPIO
Device.pin_factory = RPiGPIOFactory()

# === CONFIGURATION ADRESSE MAIL === 
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
            print("📧 E-mail envoyé avec succès !")
    except Exception as e:
        print(f"⚠️ Erreur lors de l'envoi de l'e-mail : {e}")

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

GPIO.output(RELAY_PIN, GPIO.HIGH)  # Gâche fermée par défaut

# === CONFIGURATION BDD ===
DB_CONFIG = {
    'user': 'dbca25',
    'password': 'admin',
    'host': '173.21.1.164',
    'port': 3306,
    'database': 'dbca25'
}

def etat_filtre():
    etat1 = GPIO.input(CAPTEUR_PORTE)
    time.sleep(0.1)
    etat2 = GPIO.input(CAPTEUR_PORTE)
    return etat1 if etat1 == etat2 else None

def afficher_etat_porte():
    etat = etat_filtre()
    if etat is not None:
        with open('../data/door_status.txt', 'w') as file:
            if etat == GPIO.LOW:
                print("🚪 La porte est FERMÉE")
                file.write("fermée")
            else:
                print("🚪 La porte est OUVERTE !")
                file.write("ouverte")

def activer_gache():
    print("✅ Ouverture de la porte...")
    GPIO.output(RELAY_PIN, GPIO.LOW)
    time.sleep(10)
    GPIO.output(RELAY_PIN, GPIO.HIGH)
    print("🔒 Porte refermée.")

def enregistrer_acces(uid, autorise):
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
            cursor.close()
            conn.close()
        except:
            pass

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
        last_entry = cursor.fetchone()

        if last_entry:
            log_id = last_entry[0]
            cursor.execute("""
                UPDATE Acces_log
                SET Date_heure_sortie = %s
                WHERE idAcces = %s
            """, (heure_sortie, log_id))
            conn.commit()
            print(f"🕒 Heure de sortie enregistrée pour ID {log_id} : {heure_sortie}")
        else:
            print("⚠️ Aucun accès trouvé pour ce badge.")
    except mysql.connector.Error as e:
        print(f"⚠️ Erreur MySQL sortie : {e}")
    finally:
        try:
            cursor.close()
            conn.close()
        except:
            pass

def detecter_sortie(uid):
    print("👁️ Détection de sortie : attente d'une réouverture de la porte...")
    porte_precedente = GPIO.input(CAPTEUR_PORTE)
    while True:
        etat_porte = GPIO.input(CAPTEUR_PORTE)
        if etat_porte == GPIO.HIGH and porte_precedente == GPIO.LOW:
            print("🚪 Porte réouverte → surveillance pendant 10 secondes")
            enregistrer_heure_sortie(uid)
            break
        porte_precedente = etat_porte
        time.sleep(0.2)

def boucle_principale():
    reader = SimpleMFRC522()
    try:
        while True:
            try:
                uid, _ = reader.read()
                print(f"📡 Carte détectée : {uid}")

                conn = mysql.connector.connect(**DB_CONFIG)
                cursor = conn.cursor()

                cursor.execute("SELECT * FROM Carte WHERE RFID = %s", (uid,))
                carte = cursor.fetchone()

                if carte is not None:
                    print("✅ Carte autorisée")
                    GPIO.output(LED_VERTE, GPIO.LOW)
                    activer_gache()
                    GPIO.output(LED_VERTE, GPIO.HIGH)
                    enregistrer_acces(uid, True)
                    detecter_sortie(uid)
                else:
                    print("❌ Carte non autorisée, veuillez re-badger...")
                    GPIO.output(LED_ROUGE, GPIO.LOW)
                    time.sleep(2)
                    GPIO.output(LED_ROUGE, GPIO.HIGH)
                    enregistrer_acces(uid, False)
                    envoyer_mail(uid)

            except mysql.connector.Error as err:
                print(f"⚠️ Erreur MySQL : {err}")
            except Exception as e:
                print(f"⚠️ Erreur de lecture RFID : {e}")
            finally:
                try:
                    cursor.close()
                    conn.close()
                except:
                    pass

            time.sleep(1)

    except KeyboardInterrupt:
        print("\n🛑 Programme interrompu par l'utilisateur.")
    finally:
        GPIO.cleanup()
        print("🔧 GPIO nettoyés.")

# === LANCEMENT ===
if __name__ == "__main__":
    boucle_principale()

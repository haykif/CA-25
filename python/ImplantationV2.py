import mysql.connector
import time
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
from gpiozero.pins.rpigpio import RPiGPIOFactory
from gpiozero import Device
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart


# === Forcer gpiozero à utiliser RPi.GPIO ===
Device.pin_factory = RPiGPIOFactory()

# === CONFIGURATION ADRESSE MAIL === 
expediteur = "carteacces99@gmail.com"
mot_de_passe = "llvz ctlm vjas xyfq" 
destinataire = "laurent14123@gmail.com"


# === CONFIGURATION DU MAIL ENVOYE === 

def envoyer_mail(uid):
    # Sujet et corps de l'e-mail
    heure = time.strftime('%d-%m-%Y à %H:%M:%S')
    sujet = "ENTREE NON AUTORISEE"
    corps = f"Entrée interdite détectée lee {heure}.\nUID : {uid}"

    # Création du message
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
CAPTEUR_PORTE = 17
RELAY_PIN = 18
LED_JAUNE = 16
LED_VERTE = 20
LED_ROUGE = 21
PIR_PIN = 4


GPIO.setup(RELAY_PIN, GPIO.OUT)
GPIO.setup(LED_VERTE, GPIO.OUT)
GPIO.setup(LED_JAUNE, GPIO.OUT)
GPIO.setup(LED_ROUGE, GPIO.OUT)
GPIO.setup(CAPTEUR_PORTE, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(PIR_PIN, GPIO.IN)

# Gâche fermée par défaut
GPIO.output(RELAY_PIN, GPIO.HIGH)
#LED par défaut
GPIO.output(LED_JAUNE, GPIO.HIGH)
GPIO.output(LED_VERTE, GPIO.HIGH)
GPIO.output(LED_ROUGE, GPIO.LOW)
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
        if etat == GPIO.LOW:
            print("🚪 La porte est FERMÉE")
        else:
            print("🚪 La porte est OUVERTE !")

# === GÂCHE ===
def activer_gache():
    print("✅ Ouverture de la porte...")
    GPIO.output(RELAY_PIN, GPIO.LOW)
    time.sleep(3)  # <- Ouverture pendant 3 secondes
    GPIO.output(RELAY_PIN, GPIO.HIGH)

    porte_ouverte = False
    start_time = time.time()

    while time.time() - start_time < 5:
        if GPIO.input(CAPTEUR_PORTE) == GPIO.HIGH:  # Porte ouverte
            porte_ouverte = True
            break
        time.sleep(0.1)  # Attente pour ne pas surcharger le CPU

    GPIO.output(RELAY_PIN, GPIO.HIGH)
    print("🔒 Porte refermée.")

    return porte_ouverte


# === BASE DE DONNÉES ===
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


# === DETECTEUR DE PRESENCE ===
def presence_detecter():
    while GPIO.input(PIR_PIN):
                print("⚠️ Mouvement détecté")
                GPIO.output(LED_JAUNE, GPIO.HIGH)
    else:
                print("Aucun mouvement détecté")
                GPIO.output(LED_JAUNE, GPIO.LOW)

# === LOGIQUE RFID ===
def verifier_et_traiter(uid):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()

        heure_actuelle = time.strftime('%H:%M:%S %d-%m-%Y ')
        print(f"🕓 Carte détectée le {heure_actuelle}")

        cursor.execute("SELECT * FROM Carte WHERE RFID = %s", (int(uid),))
        carte = cursor.fetchone()

        if carte:
            print("✅ Carte autorisée")
            GPIO.output(LED_VERTE, GPIO.LOW)
            porte_a_ete_ouverte = activer_gache()
            GPIO.output(LED_VERTE, GPIO.HIGH)
            enregistrer_acces(uid, True)
            if porte_a_ete_ouverte:
                GPIO.output(LED_JAUNE, GPIO.LOW)
                detecter_sortie(uid)
            else:
                print("⚠️ La porte n’a pas été ouverte après l’activation.")

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
        try:
            cursor.close()
            conn.close()

        except:
            pass

# === SURVEILLANCE SORTIE ===
def detecter_sortie(uid):
    print("👁️ Détection de sortie : attente d'une réouverture de la porte...")

    porte_precedente = GPIO.input(CAPTEUR_PORTE)

    while True:
        etat_porte = GPIO.input(CAPTEUR_PORTE)

        # Attendre une réouverture
        if etat_porte == GPIO.HIGH and porte_precedente == GPIO.LOW:
            GPIO.output(LED_JAUNE, GPIO.HIGH)
            GPIO.output(LED_ROUGE, GPIO.LOW)
            enregistrer_heure_sortie(uid)
            break  # passer à l'analyse


        porte_precedente = etat_porte
        time.sleep(0.2)


# === BOUCLE PRINCIPALE ===
def boucle_principale():
    reader = SimpleMFRC522()  # Une seule instance au début


    try:
        while True:
            print("📡 En attente d'une carte RFID...")
            try:
                uid, _ = reader.read()
                print(f"📡 Carte détectée : {uid}")
                verifier_et_traiter(uid)

                # 🛠️ Forcer le reset du lecteur
                time.sleep(0.5)
                reader = SimpleMFRC522()  # Réinitialiser le lecteur
                time.sleep(0.5)

            except Exception as e:
                print(f"⚠️ Erreur RFID : {e}")
                time.sleep(1)

    except KeyboardInterrupt:
        print("\n🛑 Programme interrompu.")
    finally:
        try:
            reader.close()
        except:
            pass
        GPIO.cleanup()
        print("🔧 GPIO nettoyés.")


# === LANCEMENT ===
if __name__ == "__main__":
    boucle_principale()
    

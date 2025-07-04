from flask import Flask, jsonify, render_template_string
import threading
import mysql.connector
import time
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
from gpiozero.pins.rpigpio import RPiGPIOFactory
from gpiozero import Device
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from flask_cors import CORS

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}}, supports_credentials=True)

@app.route("/etat_porte")
def etat_porte():
    return jsonify({"etat": etat_porte_actuel})

@app.route("/etat_pir")
def etat_pir():
    return jsonify({"pir": etat_pir_actuel})

# === Variables globales ===
etat_porte_actuel = "inconnu"

# === Forcer gpiozero à utiliser RPi.GPIO ===
Device.pin_factory = RPiGPIOFactory()

# === CONFIGURATION MAIL ===
expediteur = "carteacces99@gmail.com"
mot_de_passe = "llvz ctlm vjas xyfq"
destinataire = "laurent14123@gmail.com"

# === PINS GPIO ===
GPIO.setmode(GPIO.BCM)
CAPTEUR_PORTE = 17
RELAY_PIN = 18
LED_JAUNE = 16
LED_VERTE = 20
LED_ROUGE = 21
PIR_PIN = 26  # Le pin OUT du capteur est branché ici
etat_pir_actuel = "aucun mouvement"

GPIO.setmode(GPIO.BCM)
GPIO.setup(PIR_PIN, GPIO.IN)
GPIO.setup(RELAY_PIN, GPIO.OUT)
GPIO.setup(LED_VERTE, GPIO.OUT)
GPIO.setup(LED_JAUNE, GPIO.OUT)
GPIO.setup(LED_ROUGE, GPIO.OUT)
GPIO.setup(CAPTEUR_PORTE, GPIO.IN, pull_up_down=GPIO.PUD_UP)

GPIO.output(RELAY_PIN, GPIO.HIGH)
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

# === Fonctions ===
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
            print("Mail envoyé !")
    except Exception as e:
        print(f"Erreur mail : {e}")
        
def clignote_led_rouge():
    for i in range(5):
        GPIO.output(LED_ROUGE, GPIO.LOW)
        time.sleep(0.5)
        GPIO.output(LED_ROUGE, GPIO.HIGH)
        time.sleep(0.5)
        i=+1
    

def initialiser_capteur_pir():
    print("> Initialisation du capteur PIR...")
    time.sleep(15)  # Temps pour que le capteur se stabilise
    print("> Prêt ! Surveillance du mouvement...")

def activer_gache():
    print("> Gâche activée")
    GPIO.output(RELAY_PIN, GPIO.LOW)
    time.sleep(10)
    GPIO.output(RELAY_PIN, GPIO.HIGH)
    return GPIO.input(CAPTEUR_PORTE) == GPIO.HIGH

def enregistrer_acces(uid, autorise, id_user):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        date_entree = time.strftime('%Y-%m-%d %H:%M:%S')
        resultat = "Accès autorisé" if autorise else "Accès refusé"

        sql = """
        INSERT INTO Acces_log (Date_heure_entree, Resultat_tentative, UID, IdUser)
        VALUES (%s, %s, %s, %s)
        """
        valeurs = (date_entree, resultat, uid, id_user if id_user is not None else 0)
        cursor.execute(sql, valeurs)
        conn.commit()
        print(f"{resultat} | UID : {uid} logué avec ID utilisateur : {id_user}")
    except Exception as e:
        print(f"MySQL (entrée) : {e}")
    finally:
        cursor.close()
        conn.close()


def enregistrer_heure_sortie(uid):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        heure_sortie = time.strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute("SELECT idAcces FROM Acces_log WHERE UID = %s ORDER BY idAcces DESC LIMIT 1", (uid,))
        last_entry = cursor.fetchone()
        if last_entry:
            cursor.execute("UPDATE Acces_log SET Date_heure_sortie = %s WHERE idAcces = %s", (heure_sortie, last_entry[0]))
            conn.commit()
            print(f"🕒 Sortie enregistrée pour ID {last_entry[0]}")
        else:
            print("> Aucun log trouvé")
    except Exception as e:
        print(f"MySQL (sortie) : {e}")
    finally:
        cursor.close()
        conn.close()

def verifier_et_traiter(uid):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(buffered=True)

        cursor.execute("SELECT idCarte FROM Carte WHERE RFID = %s", (uid,))
        carte = cursor.fetchone()

        if carte:
            idCarte = carte[0]

            cursor.execute("SELECT idUser FROM User WHERE idCarte = %s", (idCarte,))
            utilisateur = cursor.fetchone()

            if utilisateur:
                idUser = utilisateur[0]
            else:
                print("> Carte reconnue mais aucun utilisateur lié.")
                idUser = 0

            GPIO.output(LED_VERTE, GPIO.LOW)
            GPIO.output(LED_ROUGE, GPIO.HIGH)
            porte_ouverte = activer_gache()
            GPIO.output(LED_VERTE, GPIO.HIGH)

            enregistrer_acces(uid, True, idUser)
            if porte_ouverte and idUser:
                detecter_sortie(uid)
                GPIO.output(LED_ROUGE, GPIO.LOW)
        else:
            clignote_led_rouge()
            GPIO.output(LED_ROUGE, GPIO.LOW)
            enregistrer_acces(uid, False, 0)
            envoyer_mail(uid)

    except Exception as e:
        print(f"Erreur RFID : {e}")
    finally:
        cursor.close()
        conn.close()



def detecter_sortie(uid):
    print("> Surveillance ouverture porte...")
    precedent = GPIO.input(CAPTEUR_PORTE)
    while True:
        etat = GPIO.input(CAPTEUR_PORTE)
        if etat == GPIO.HIGH and precedent == GPIO.LOW:
            enregistrer_heure_sortie(uid)
            break
        precedent = etat
        time.sleep(0.2)

# === Thread dynamique pour l'état de la porte ===
def surveiller_etat_porte():
    global etat_porte_actuel
    try:
        while True:
            etat = GPIO.input(CAPTEUR_PORTE)
            if etat == GPIO.LOW:
                etat_porte_actuel = "fermée"
            else:
                etat_porte_actuel = "ouverte"
            time.sleep(0.5)
    except Exception as e:
        print(f"Thread porte : {e}")

# === Thread capteur
def surveiller_pir():
    global etat_pir_actuel
    etat_precedent = 0
    try:
        while True:
            etat = GPIO.input(PIR_PIN)
            if etat == 1 and etat_precedent == 0:
                etat_pir_actuel = "mouvement détecté"
                GPIO.output(LED_JAUNE, GPIO.LOW)  # Allume LED jaune
                print("> Mouvement détecté par le PIR !")
            elif etat == 0 and etat_precedent == 1:
                etat_pir_actuel = "aucun mouvement"
                GPIO.output(LED_JAUNE, GPIO.HIGH)  # Éteint LED jaune
            etat_precedent = etat
            time.sleep(0.2)
    except Exception as e:
        print(f"Thread PIR : {e}")

# === BOUCLE PRINCIPALE ===
def boucle_principale():
    reader = SimpleMFRC522()
    try:
        while True:
            time.sleep(1)
            print("> En attente d'un badge...")
            uid_int, _ = reader.read()

            # Convertir en bytes (important: little vs big endian)
            uid_bytes = uid_int.to_bytes((uid_int.bit_length() + 7) // 8, byteorder='big')
            uid_bytes = uid_bytes[:4]
            uid_hex = ''.join(f"{b:02X}" for b in uid_bytes)

            print(f"> UID brut (int) : {uid_int}")
            print(f"> UID formaté    : {uid_hex}")

            verifier_et_traiter(uid_hex)  # Envoie le bon format hexa dans la BDD
            time.sleep(0.5)
            reader = SimpleMFRC522()
    except KeyboardInterrupt:
        GPIO.cleanup()
        print("> Arrêt programme.")


def lancer_serveur():
    app.run(host="0.0.0.0", port=5000)

# === MAIN ===
if __name__ == "__main__":
    initialiser_capteur_pir()
    threading.Thread(target=lancer_serveur, daemon=True).start()
    threading.Thread(target=surveiller_etat_porte, daemon=True).start()
    threading.Thread(target=surveiller_pir, daemon=True).start()
    boucle_principale()

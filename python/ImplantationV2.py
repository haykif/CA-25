import mysql.connector
import time
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
from gpiozero import MotionSensor

# 🎯 Configuration du relais et du lecteur RFID
GPIO.setmode(GPIO.BCM)
RELAY_PIN = 18  # Modifier selon ton branchement
LED1 = 20
LED2 = 21
pir = MotionSensor(26)



GPIO.setup(RELAY_PIN, GPIO.OUT)
GPIO.setup(LED1, GPIO.OUT)
GPIO.setup(LED2, GPIO.OUT)

# 🛠️ Assurer que la gâche est fermée au démarrage
GPIO.output(RELAY_PIN, GPIO.HIGH)  # La gâche reste fermée par défaut

# Initialisation du lecteur RFID
reader = SimpleMFRC522()

# 📌 Paramètres de connexion à la base de données
DB_CONFIG = {
    'user': 'dbca25',
    'password': 'admin25',
    'host': '173.21.1.164',
    'port': 887,
    'database': 'dbca25'
}

def activer_gache():
    """ Ouvre la gâche pendant 3 secondes puis la referme """
    print("✅ Accès accordé ! Ouverture de la porte...")
    GPIO.output(LED1, GPIO.LOW)
    GPIO.output(RELAY_PIN, GPIO.LOW)  # Active le relais (ouvre la gâche)
    time.sleep(5)  # La gâche reste ouverte pendant 3 sec
    GPIO.output(RELAY_PIN, GPIO.HIGH)  # Désactive le relais (ferme la gâche)
    print("🔒 Porte refermée.")

def verifier_acces(uid):
    """ Vérifie si l'UID de la carte est autorisé dans la base de données """
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Carte WHERE RFID = %s", (uid,))
        carte = cursor.fetchone()

        if carte:
            print("✅ Carte autorisée !")
            activer_gache()
            enregistrer_acces_autorisee(uid)  # Enregistre l'accès en base
        else:
            print("❌ Accès refusé ! Carte inconnue.")
            enregistrer_acces_refusee(uid)

    except mysql.connector.Error as err:
        print(f"⚠️ Erreur MySQL : {err}")

    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()
    return

def enregistrer_acces_autorisee(uid):
    """ Enregistre l'accès réussi dans la table Acces_log """
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()

        date_entree = time.strftime('%Y-%m-%d %H:%M:%S')
        resultat = "Accès autorisé"
        etat_porte = "1"
        IdUser="1"

        sql = """
        INSERT INTO Acces_log (Date_heure_entree, Resultat_tentative, Presence, Etat_porte, RFID_utilise, UID)
        VALUES (%s, %s, %s, %s, %s, %s)
        """
        valeurs = (date_entree, resultat, True, etat_porte, uid, IdUser)


        GPIO.output(RELAY_PIN, GPIO.HIGH)
        GPIO.output(LED1, GPIO.LOW) #allumage de la led VERTE
        time.sleep(2)
        GPIO.output(LED1, GPIO.HIGH) #allumage de la led VERTE

        cursor.execute(sql, valeurs)
        conn.commit()
        print(f"📌 UID {uid} enregistré avec succès dans Acces_log.")

    except mysql.connector.Error as err:
        print(f"⚠️ Erreur MySQL : {err}")

    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

def enregistrer_acces_refusee(uid):
    """ Enregistre l'accès réussi dans la table Acces_log """
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()

        date_entree = time.strftime('%Y-%m-%d %H:%M:%S')
        resultat = "Accès refusé"
        etat_porte = "1"
        IdUser="1"

        sql = """
        INSERT INTO Acces_log (Date_heure_entree, Resultat_tentative, Presence, Etat_porte, RFID_utilise, IdUser)
        VALUES (%s, %s, %s, %s, %s, %s)
        """
        valeurs = (date_entree, resultat, True, etat_porte, uid, IdUser)
        GPIO.output(RELAY_PIN, GPIO.HIGH)
        GPIO.output(LED2, GPIO.LOW) #allumage de la led ROUGE
        time.sleep(2)
        GPIO.output(LED2, GPIO.HIGH) #allumage de la led ROUGE


        cursor.execute(sql, valeurs)
        conn.commit()
        print(f"📌 UID {uid} enregistré avec succès dans Acces_log.")

    except mysql.connector.Error as err:
        print(f"⚠️ Erreur MySQL : {err}")

    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

def lire_carte():
    """ Lecture de la carte et vérification de l'accès """
    global reader

    try:
        while True:
            print("📡 En attente d'une carte RFID...")
            card_id = reader.read_id()
            print(f"📡 Carte détectée : {card_id}")
            verifier_acces(card_id)  # Vérifier l'accès dans la BDD
            time.sleep(2)# Pause avant la prochaine lecture
            reader = SimpleMFRC522()
            continue

    except KeyboardInterrupt:
        print("\n🛑 Arrêt du programme.")

    finally:
        GPIO.cleanup()
        print("🔧 GPIO nettoyés.")

# Lancer la lecture
lire_carte()

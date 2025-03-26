import mysql.connector
import time
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522

# 🎯 Configuration du relais et du lecteur RFID
RELAY_PIN = 18  # Modifier selon ton branchement
GPIO.setmode(GPIO.BCM)
GPIO.setup(RELAY_PIN, GPIO.OUT)

# 🛠️ Assurer que la gâche est fermée au démarrage
GPIO.output(RELAY_PIN, GPIO.HIGH)  # La gâche reste fermée par défaut

# Initialisation du lecteur RFID
reader = SimpleMFRC522()

# 📌 Paramètres de connexion à la base de données
DB_CONFIG = {
    'user': 'dbca25',
    'password': 'admin',
    'host': '173.21.1.162',
    'port': 3306,
    'database': 'dbca25'
}

def activer_gache():
    """ Ouvre la gâche pendant 3 secondes puis la referme """
    print("✅ Accès accordé ! Ouverture de la porte...")
    GPIO.output(RELAY_PIN, GPIO.LOW)  # Active le relais (ouvre la gâche)
    time.sleep(3)  # La gâche reste ouverte pendant 3 sec
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
            enregistrer_acces(uid)  # Enregistre l'accès en base
        else:
            print("❌ Accès refusé ! Carte inconnue.")
        
    except mysql.connector.Error as err:
        print(f"⚠️ Erreur MySQL : {err}")
    
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

def enregistrer_acces(uid):
    """ Enregistre l'accès réussi dans la table Acces_log """
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()

        date_entree = time.strftime('%Y-%m-%d %H:%M:%S')
        resultat = "Accès autorisé"
        etat_porte = "1"

        sql = """
        INSERT INTO Acces_log (Date_heure_entree, Resultat_tentative, Presence, Etat_porte, RFID_utilise)
        VALUES (%s, %s, %s, %s, %s)
        """
        valeurs = (date_entree, resultat, True, etat_porte, uid)

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
    try:
        while True:
            print("📡 En attente d'une carte RFID...")
            card_id, text = reader.read()
            print(f"📡 Carte détectée : {card_id}")
            verifier_acces(card_id)  # Vérifier l'accès dans la BDD
            time.sleep(2)  # Pause avant la prochaine lecture

    except KeyboardInterrupt:
        print("\n🛑 Arrêt du programme.")
    
    finally:
        GPIO.cleanup()
        print("🔧 GPIO nettoyés.")

# Lancer la lecture
lire_carte()

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
LED_JAUNE = 16
CAPTEUR_PORTE = 17
PIR_PIN = 4

GPIO.setup(RELAY_PIN, GPIO.OUT)
GPIO.setup(LED_VERTE, GPIO.OUT)
GPIO.setup(LED_ROUGE, GPIO.OUT)
GPIO.setup(LED_JAUNE, GPIO.OUT)

GPIO.setup(CAPTEUR_PORTE, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(PIR_PIN, GPIO.IN)

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
            with open('../data/door_status.txt', 'w') as file:
                file.write("fermée")
        else:
            print("🚪 La porte est OUVERTE !")
            with open('../data/door_status.txt', 'w') as file:
                file.write("ouverte")

# === GÂCHE ===
def activer_gache():
    print("✅ Ouverture de la porte...")
    GPIO.output(RELAY_PIN, GPIO.LOW)
    time.sleep(10)
    GPIO.output(RELAY_PIN, GPIO.HIGH)
    print("🔒 Porte refermée.")

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

# === LOGIQUE RFID ===
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
            time.sleep(10)
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
            print("🚪 Porte réouverte → surveillance pendant 10 secondes")
            enregistrer_heure_sortie(uid)
            break  # passer à l'analyse


        porte_precedente = etat_porte
        time.sleep(0.2)

    # ⏳ Surveillance pendant 10 secondes
    duree = 10
    pas_de_mouvement = 0

    for i in range(duree):
        mouvement = GPIO.input(PIR_PIN)
        if not mouvement:
            pas_de_mouvement += 1
            print(f"🔴 {i+1}s : aucun mouvement")
        else:
            print(f"🟢 {i+1}s : mouvement détecté")

        time.sleep(1)

    print(f"⏱️ Temps sans mouvement : {pas_de_mouvement}s / {duree}s")

    if pas_de_mouvement >= 3:
        print("✅ Sortie détectée → log de sortie enregistré")
    else:
        print("📌 Sortie non détectée (mouvement trop fréquent)")


# === BOUCLE PRINCIPALE ===
def boucle_principale():
    reader = SimpleMFRC522()
    try:
        while True:
            afficher_etat_porte()

            if GPIO.input(PIR_PIN):
                print("⚠️ Mouvement détecté")
                GPIO.output(LED_JAUNE, GPIO.HIGH)
            else:
                print("Aucun mouvement détecté")
                GPIO.output(LED_JAUNE, GPIO.LOW) 

            print("📡 En attente d'une carte RFID...")
            try:
                uid, _ = reader.read()
                print(f"📡 Carte détectée : {uid}")
                verifier_et_traiter(uid)

                detecter_sortie(uid)


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

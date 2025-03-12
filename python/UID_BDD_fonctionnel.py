import mysql.connector
from datetime import datetime
import nfc  

# Fonction pour lire une carte NFC et récupérer son UID
def on_connect(tag):
    uid_hex = tag.identifier.hex().upper()  # Convertir en majuscules
    print(f"UID détecté : {uid_hex}")
    return uid_hex  # Retourner l'UID de la carte

# Fonction pour enregistrer l'UID dans MySQL
def enregistrer_acces(uid):
    config = {
        'user': 'dbca25',
        'password': 'admin',
        'host': '173.21.1.162',
        'port': 3306,
        'database': 'dbca25'
    }

    try:
        conn = mysql.connector.connect(**config)
        cursor = conn.cursor()

        date_entree = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        resultat = "Accès autorisé"
        date_sortie = None
        presence = True
        etat_porte = "1"
        id_user="1"

        sql = """
        INSERT INTO Acces_log (Date_heure_entree, Resultat_tentative, Date_heure_sortie, Presence, Etat_porte,RFID_utilise, IdUser)
        VALUES (%s, %s, %s, %s, %s, %s,%s)
        """
        valeurs = (date_entree, resultat, date_sortie, presence, etat_porte, uid, id_user)

        cursor.execute(sql, valeurs)
        conn.commit()

        print(f"UID {uid} enregistré avec succès !")

    except mysql.connector.Error as err:
        print(f"Erreur MySQL : {err}")

    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()
            print("Connexion fermée.")

# Détection et enregistrement automatique de la carte NFC
def lire_carte():
    clf = nfc.ContactlessFrontend('usb')
    if clf:
        print("Lecteur NFC prêt. Approche une carte...")
        clf.connect(rdwr={'on-connect': lambda tag: enregistrer_acces(on_connect(tag))})
        clf.close()
    else:
        print("Lecteur NFC non détecté !")

# Lancer la lecture NFC
lire_carte()


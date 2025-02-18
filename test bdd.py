import time
import mysql.connector

# Fonction pour enregistrer une tentative dans la base de données
def enregistrer_tentative(id_user, resultat):
    connection = mysql.connector.connect(
        host="173.21.1.240:3306",
        user="dbca25",
        password="admin",
        database="dbca25"
    )
    cursor = connection.cursor()
    
    query = """
    INSERT INTO Acess_log (Date_heure_entree, Resultat_tentative, IdUser)
    VALUES (NOW(), %s, %s)
    """
    cursor.execute(query, (resultat, id_user))
    connection.commit()
    cursor.close()
    connection.close()

print("Simulation de scan de carte...")

try:
    while True:
        id_rfid = 12345,
        if id_rfid.lower() == 'exit':
            break
        
        print(f"Carte détectée avec ID : {id_rfid}")
        enregistrer_tentative(id_rfid, "Tentative enregistrement")
        
        time.sleep(2)
except KeyboardInterrupt:
    print("Arrêt du programme.")

print("Fin du test.")

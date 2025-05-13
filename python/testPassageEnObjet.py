# Infrastructure

# projet_rfid/
# │
# ├── config/
# │   ├── __init__.py
# │   └── settings.py        # Configuration des pins et de la BDD
# │
# ├── hardware/
# │   ├── __init__.py
# │   ├── gpio_controller.py # Gestion des GPIO
# │   └── rfid_reader.py     # Lecture RFID
# │
# ├── database/
# │   ├── __init__.py
# │   └── db_handler.py      # Interactions avec la BDD
# │
# ├── logic/
# │   ├── __init__.py
# │   ├── door_manager.py    # Gestion porte/gâche
# │   └── access_logic.py    # Logique d'accès
# │
# └── main.py                # Point d'entrée principal

-------------------------------------------------------------------------------

config/settings.py

class Settings:
    # Configuration GPIO
    RELAY_PIN = 18
    LED_VERTE = 20
    LED_ROUGE = 21
    LED_JAUNE = 16
    CAPTEUR_PORTE = 17
    PIR_PIN = 4

    # Configuration BDD
    DB_CONFIG = {
        'user': 'dbca25',
        'password': 'admin',
        'host': '173.21.1.164',
        'port': 3306,
        'database': 'dbca25'
    }

---------------------------------------------------------------------------------------

hardware/gpio_controller.py

import RPi.GPIO as GPIO
from gpiozero.pins.rpigpio import RPiGPIOFactory
from gpiozero import Device
from config.settings import Settings

class GPIOController:
    def __init__(self):
        Device.pin_factory = RPiGPIOFactory()
        GPIO.setmode(GPIO.BCM)
        self.settings = Settings()
        self._setup_pins()
        
    def _setup_pins(self):
        # Configuration des sorties
        GPIO.setup(self.settings.RELAY_PIN, GPIO.OUT)
        GPIO.setup(self.settings.LED_VERTE, GPIO.OUT)
        GPIO.setup(self.settings.LED_ROUGE, GPIO.OUT)
        GPIO.setup(self.settings.LED_JAUNE, GPIO.OUT)
        
        # Configuration des entrées
        GPIO.setup(self.settings.CAPTEUR_PORTE, GPIO.IN, pull_up_down=GPIO.PUD_UP)
        GPIO.setup(self.settings.PIR_PIN, GPIO.IN)
        
        # État initial
        GPIO.output(self.settings.RELAY_PIN, GPIO.HIGH)  # Gâche fermée
        
    def cleanup(self):
        GPIO.cleanup()
        
    def set_output(self, pin, state):
        GPIO.output(pin, state)
        
    def get_input(self, pin):
        return GPIO.input(pin)

-------------------------------------------------------------------------------------

hardware/rfid_reader.py

from mfrc522 import SimpleMFRC522

class RFIDReader:
    def __init__(self):
        self.reader = SimpleMFRC522()
        
    def read_card(self):
        try:
            uid, text = self.reader.read()
            return uid
        except Exception as e:
            raise Exception(f"Erreur de lecture RFID: {str(e)}")

---------------------------------------------------------------------------------------

database/db_handler.py

import mysql.connector
import time
from config.settings import Settings

class DatabaseHandler:
    def __init__(self):
        self.settings = Settings()
        
    def _get_connection(self):
        return mysql.connector.connect(**self.settings.DB_CONFIG)
        
    def enregistrer_acces(self, uid, autorise):
        conn = None
        try:
            conn = self._get_connection()
            cursor = conn.cursor()

            date_entree = time.strftime('%Y-%m-%d %H:%M:%S')
            resultat = "Accès autorisé" if autorise else "Accès refusé"
            etat_porte = "1"
            IdUser = "1"

            sql = """
            INSERT INTO Acces_log (Date_heure_entree, Resultat_tentative, Presence, Etat_porte, UID, IdUser)
            VALUES (%s, %s, %s, %s, %s, %s)
            """
            cursor.execute(sql, (date_entree, resultat, True, etat_porte, uid, IdUser))
            conn.commit()
            return True
            
        except mysql.connector.Error as err:
            print(f"Erreur MySQL: {err}")
            return False
        finally:
            if conn and conn.is_connected():
                cursor.close()
                conn.close()
                
    def verifier_carte(self, uid):
        conn = None
        try:
            conn = self._get_connection()
            cursor = conn.cursor()
            cursor.execute("SELECT * FROM Carte WHERE RFID = %s", (int(uid),))
            return cursor.fetchone() is not None
        except mysql.connector.Error as err:
            print(f"Erreur MySQL: {err}")
            return False
        finally:
            if conn and conn.is_connected():
                cursor.close()
                conn.close()

----------------------------------------------------------------------------------------------

logic/door_manager.py

import time
from config.settings import Settings

class DoorManager:
    def __init__(self, gpio_controller):
        self.gpio = gpio_controller
        self.settings = Settings()
        
    def etat_filtre(self):
        etat1 = self.gpio.get_input(self.settings.CAPTEUR_PORTE)
        time.sleep(0.1)
        etat2 = self.gpio.get_input(self.settings.CAPTEUR_PORTE)
        return etat1 if etat1 == etat2 else None
        
    def afficher_etat_porte(self):
        etat = self.etat_filtre()
        if etat is not None:
            if etat == GPIO.LOW:
                print("🚪 La porte est FERMÉE")
            else:
                print("🚪 La porte est OUVERTE !")
                
    def activer_gache(self, duree=10):
        print("✅ Ouverture de la porte...")
        self.gpio.set_output(self.settings.RELAY_PIN, GPIO.LOW)
        time.sleep(duree)
        self.gpio.set_output(self.settings.RELAY_PIN, GPIO.HIGH)
        print("🔒 Porte refermée.")

--------------------------------------------------------------------------------------------

logic/access_logic.py

import time
from config.settings import Settings

class AccessLogic:
    def __init__(self, gpio_controller, db_handler):
        self.gpio = gpio_controller
        self.db = db_handler
        self.settings = Settings()
        
    def verifier_et_traiter(self, uid):
        if self.db.verifier_carte(uid):
            print("✅ Carte autorisée")
            self.gpio.set_output(self.settings.LED_VERTE, GPIO.LOW)
            self.gpio.activer_gache()
            time.sleep(10)
            self.gpio.set_output(self.settings.LED_VERTE, GPIO.HIGH)
            self.db.enregistrer_acces(uid, True)
        else:
            print("❌ Carte non autorisée")
            self.gpio.set_output(self.settings.LED_ROUGE, GPIO.LOW)
            time.sleep(2)
            self.gpio.set_output(self.settings.LED_ROUGE, GPIO.HIGH)
            self.db.enregistrer_acces(uid, False)

--------------------------------------------------------------------------------------------------

main.py

import time
from hardware.gpio_controller import GPIOController
from hardware.rfid_reader import RFIDReader
from database.db_handler import DatabaseHandler
from logic.door_manager import DoorManager
from logic.access_logic import AccessLogic

class MainApp:
    def __init__(self):
        self.gpio = GPIOController()
        self.rfid = RFIDReader()
        self.db = DatabaseHandler()
        self.door = DoorManager(self.gpio)
        self.access = AccessLogic(self.gpio, self.db)
        
    def run(self):
        try:
            while True:
                self.door.afficher_etat_porte()
                self._check_movement()
                self._check_rfid()
                time.sleep(1)
                
        except KeyboardInterrupt:
            print("\n🛑 Programme interrompu.")
        finally:
            self.gpio.cleanup()
            print("🔧 GPIO nettoyés.")
            
    def _check_movement(self):
        if self.gpio.get_input(self.settings.PIR_PIN):
            print("⚠️ Mouvement détecté")
            self.gpio.set_output(self.settings.LED_JAUNE, GPIO.HIGH)
        else:
            self.gpio.set_output(self.settings.LED_JAUNE, GPIO.LOW)
            
    def _check_rfid(self):
        print("📡 En attente d'une carte RFID...")
        try:
            uid = self.rfid.read_card()
            print(f"📡 Carte détectée : {uid}")
            self.access.verifier_et_traiter(uid)
        except Exception as e:
            print(f"⚠️ Erreur RFID : {e}")

if __name__ == "__main__":
    app = MainApp()
    app.run()

import RPi.GPIO as GPIO
import time

CAPTEUR_PIN = 17

GPIO.setmode(GPIO.BCM)
GPIO.setup(CAPTEUR_PIN, GPIO.IN, pull_up_down=GPIO.PUD_UP)

def etat_filtre():
    """ Vérifie l'état du capteur avec une petite temporisation pour éviter les faux déclenchements. """
    etat1 = GPIO.input(CAPTEUR_PIN)
    time.sleep(0.1)  # Attente de 100ms
    etat2 = GPIO.input(CAPTEUR_PIN)
    return etat1 if etat1 == etat2 else None  # Retourne l'état stable

print("Surveillance de la porte...")

try:
    while True:
        etat = etat_filtre()
        if etat is not None:  # Ignore les valeurs instables
            if etat == GPIO.LOW:
                print("🚪 La porte est FERMÉE")
            else:
                print("🚪 La porte est OUVERTE !")
        time.sleep(0.5)  # Vérification toutes les 500ms

except KeyboardInterrupt:
    print("Arrêt du programme")
    GPIO.cleanup()

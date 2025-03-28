import RPi.GPIO as GPIO
import time

# Configuration du GPIO
CAPTEUR_PIN = 17  # Utilise le GPIO 17 (adapter si besoin)
GPIO.setmode(GPIO.BCM)
GPIO.setup(CAPTEUR_PIN, GPIO.IN, pull_up_down=GPIO.PUD_UP)  # Active une pull-up

print("Surveillance de la porte...")

try:
    while True:
        if GPIO.input(CAPTEUR_PIN) == GPIO.LOW:  # Si l'interrupteur se ferme
            print("🚪 La porte est FERMÉE")
        else:
            print("🚪 La porte est OUVERTE !")
        time.sleep(1)  # Pause d'une seconde pour éviter le spam

except KeyboardInterrupt:
    print("Arrêt du programme")
    GPIO.cleanup()  # Nettoie les GPIO

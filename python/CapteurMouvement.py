import RPi.GPIO as GPIO
import time

# Configuration des GPIO
PIR_PIN = 26  # GPIO 17 correspond à la Pin 11
GPIO.setmode(GPIO.BCM)
GPIO.setup(PIR_PIN, GPIO.IN)

print("✅ Capteur PIR prêt ! Attente de mouvement...")

try:
    while True:
        if GPIO.input(PIR_PIN):  # Si un mouvement est détecté
            print("⚠️ Mouvement détecté !")
        else:
            print("aucun mouvement detecter")

        time.sleep(1)  # Petite pause pour éviter les détections trop rapides

except KeyboardInterrupt:
    print("❌ Arrêt du programme")
    GPIO.cleanup()  # Nettoyage des GPIO

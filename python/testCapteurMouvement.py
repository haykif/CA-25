import RPi.GPIO as GPIO
import time

# Définition des broches GPIO utilisées
PIR_PIN = 17  # Broche du capteur PIR (OUT)
LED_PIN = 18  # Broche de la LED

# Configuration des GPIO
GPIO.setmode(GPIO.BCM)
GPIO.setup(PIR_PIN, GPIO.IN)
GPIO.setup(LED_PIN, GPIO.OUT)

print("Capteur PIR prêt ! Attente de mouvement...")

try:
    while True:
        if GPIO.input(PIR_PIN):  # Mouvement détecté
            print("⚠️ Mouvement détecté !")
            GPIO.output(LED_PIN, GPIO.HIGH)  # Allumer la LED
        else:
            GPIO.output(LED_PIN, GPIO.LOW)   # Éteindre la LED
        time.sleep(0.5)  # Pause pour éviter les lectures trop rapides

except KeyboardInterrupt:
    print("Arrêt du programme")
    GPIO.cleanup()  # Nettoyage des GPIO avant de quitter

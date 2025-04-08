import RPi.GPIO as GPIO
import time

PIR_PIN = 4  # GPIO4 correspond à la broche physique 7

GPIO.setmode(GPIO.BCM)
GPIO.setup(PIR_PIN, GPIO.IN)

print("✅ Capteur PIR prêt ! Attente de mouvement...")

try:
    while True:
        if GPIO.input(PIR_PIN):
            print("⚠️ Mouvement détecté sur GPIO4 (broche 7) !")
        else:
            print("Aucun mouvement détecté")

        time.sleep(1)

except KeyboardInterrupt:
    print("❌ Arrêt du programme")
    GPIO.cleanup()

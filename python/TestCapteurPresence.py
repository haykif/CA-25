import RPi.GPIO as GPIO
import time

PIR_PIN = 4  # Le pin OUT du capteur est branché ici

GPIO.setmode(GPIO.BCM)
GPIO.setup(PIR_PIN, GPIO.IN)

print("⏳ Initialisation du capteur PIR...")
time.sleep(2)  # Temps pour que le capteur se stabilise
print("✅ Prêt ! Surveillance du mouvement...")

try:
    while True:
        if GPIO.input(PIR_PIN):
            print("⚠️ Mouvement détecté !")
        else:
            print("✅ Aucun mouvement.")
        time.sleep(1)

except KeyboardInterrupt:
    print("\n🛑 Arrêt manuel du programme.")
finally:
    GPIO.cleanup()
    print("🔧 Nettoyage GPIO terminé.")

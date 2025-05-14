import RPi.GPIO as GPIO
import time

# === PINS ===
PIR_PIN = 4
LED_JAUNE = 16

# === SETUP ===
GPIO.setmode(GPIO.BCM)
GPIO.setup(PIR_PIN, GPIO.IN)
GPIO.setup(LED_JAUNE, GPIO.OUT)

print("🕵️ Capteur PIR en attente...")

try:
    while True:
        if GPIO.input(PIR_PIN):
            print("⚠️ Mouvement détecté !")
            GPIO.output(LED_JAUNE, GPIO.HIGH)
            time.sleep(2)  # Laisse la LED allumée pendant 2 sec
        else:
            GPIO.output(LED_JAUNE, GPIO.LOW)

        time.sleep(0.1)  # Anti-rebond et limitation CPU

except KeyboardInterrupt:
    print("🛑 Arrêt du programme.")
finally:
    GPIO.cleanup()

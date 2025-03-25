import RPi.GPIO as GPIO
import time

# Configuration des GPIO
GPIO.setmode(GPIO.BCM)
RELAY_PIN = 18  # Modifier selon ton branchement
LED1 = 23
LED2 = 24

GPIO.setup(RELAY_PIN, GPIO.OUT)
GPIO.setup(LED1, GPIO.OUT)
GPIO.setup(LED2, GPIO.OUT)

try:
    while True:
        print("✅ Allumage relais et LED...")
        GPIO.output(RELAY_PIN, GPIO.HIGH)
        GPIO.output(LED1, GPIO.HIGH)
        GPIO.output(LED2, GPIO.HIGH)
        time.sleep(2)

        print("⏹️ Extinction relais et LED...")
        GPIO.output(RELAY_PIN, GPIO.LOW)
        GPIO.output(LED1, GPIO.LOW)
        GPIO.output(LED2, GPIO.LOW)
        time.sleep(2)

except KeyboardInterrupt:
    print("🛑 Arrêt du programme.")
    GPIO.cleanup()

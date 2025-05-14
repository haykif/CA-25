import RPi.GPIO as GPIO
import time

# === CONFIGURATION ===
PIR_PIN   = 4    # GPIO4 (Pin 7)
LED_JAUNE = 16   # GPIO16 (Pin 36)

GPIO.setmode(GPIO.BCM)
GPIO.setup(PIR_PIN, GPIO.IN)
GPIO.setup(LED_JAUNE, GPIO.OUT)

print("🔋 Stabilisation du capteur PIR (30s)…")
time.sleep(30)  # Phase de stabilisation du PIR
print("✅ Stabilisation terminée. Surveillance active.")

# On mémorise l'état précédent pour n'afficher qu'au changement
etat_prec = GPIO.input(PIR_PIN)
GPIO.output(LED_JAUNE, GPIO.HIGH if etat_prec else GPIO.LOW)

try:
    while True:
        etat = GPIO.input(PIR_PIN)
        if etat != etat_prec:
            if etat:
                print("🟢 Mouvement détecté !")
                GPIO.output(LED_JAUNE, GPIO.HIGH)
            else:
                print("🔴 Plus de mouvement")
                GPIO.output(LED_JAUNE, GPIO.LOW)
            etat_prec = etat

        time.sleep(0.1)  # Boucle légère

except KeyboardInterrupt:
    pass

finally:
    GPIO.cleanup()
    print("🔧 GPIO nettoyés.")

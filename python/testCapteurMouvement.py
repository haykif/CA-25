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


#----------------------------------------------------------

import RPi.GPIO as GPIO
import time

PIR_PIN = 17

GPIO.setmode(GPIO.BCM)
GPIO.setup(PIR_PIN, GPIO.IN)

print("Test PIR :")

while True:
    print(GPIO.input(PIR_PIN))  # Devrait afficher 1 ou 0
    time.sleep(0.5)



#--------------------------------------------------------


import RPi.GPIO as GPIO
import time

# Configuration des GPIO
PIR_PIN = 17  # GPIO 17 correspond à la Pin 11
GPIO.setmode(GPIO.BCM)
GPIO.setup(PIR_PIN, GPIO.IN)

print("✅ Capteur PIR prêt ! Attente de mouvement...")

try:
    while True:
        if GPIO.input(PIR_PIN):  # Si un mouvement est détecté
            print("⚠️ Mouvement détecté !")
        time.sleep(0.5)  # Petite pause pour éviter les détections trop rapides

except KeyboardInterrupt:
    print("❌ Arrêt du programme")
    GPIO.cleanup()  # Nettoyage des GPIO





#-----------------------------------------------------------------



import RPi.GPIO as GPIO
import time

# Configuration du capteur
PIR_PIN = 17  # GPIO 17 correspond à la Pin 11
GPIO.setmode(GPIO.BCM)
GPIO.setup(PIR_PIN, GPIO.IN)

print("✅ Capteur PIR prêt ! Attente de mouvement...")

try:
    mouvement_detecte = False  # Variable pour éviter les répétitions

    while True:
        if GPIO.input(PIR_PIN):  # Si mouvement détecté
            if not mouvement_detecte:  # Vérifie s'il n'a pas déjà détecté avant
                print("⚠️ Mouvement détecté !")
                mouvement_detecte = True  # Marque comme détecté
        else:
            mouvement_detecte = False  # Réinitialise quand il n'y a plus de mouvement

        time.sleep(0.1)  # Pause courte pour éviter les faux positifs

except KeyboardInterrupt:
    print("❌ Arrêt du programme")
    GPIO.cleanup()  # Nettoyage des GPIO




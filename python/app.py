from flask import Flask, jsonify
from flask_cors import CORS
import RPi.GPIO as GPIO
import time

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})

# Configuration GPIO
DOOR_PIN = 17  # Ajustez selon votre configuration
GPIO.setmode(GPIO.BCM)
GPIO.setup(DOOR_PIN, GPIO.IN, pull_up_down=GPIO.PUD_UP)

@app.route('/etat_porte', methods=['GET'])
def get_door_state():
    try:
        # Lecture de l'état de la porte (0 = fermée, 1 = ouverte)
        door_state = GPIO.input(DOOR_PIN)
        return jsonify({"etat": "ouverte" if door_state == 1 else "fermée"})
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    try:
        print("🚪 Démarrage du serveur de surveillance de la porte...")
        app.run(host='0.0.0.0', port=5000, debug=True)
    except KeyboardInterrupt:
        print("\n🛑 Arrêt du serveur...")
    finally:
        GPIO.cleanup()
        print("🔧 Nettoyage GPIO terminé.") 
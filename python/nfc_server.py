from flask import Flask, jsonify, request
import nfc
import threading

app = Flask(__name__)
last_uid = None

def read_nfc():
    global last_uid
    try:
        clf = nfc.ContactlessFrontend('usb')
        tag = clf.connect(rdwr={'on-connect': lambda tag: False})
        last_uid = tag.identifier.hex()
        clf.close()
    except Exception as e:
        print("Erreur NFC :", e)
        last_uid = None

@app.route('/scan_nfc', methods=['POST'])
def scan_nfc():
    global last_uid
    last_uid = None
    thread = threading.Thread(target=read_nfc)
    thread.start()
    thread.join(timeout=10)

    if last_uid:
        return jsonify({"uid": last_uid})
    else:
        return jsonify({"error": "Aucune carte détectée"}), 408

if __name__ == '__main__':
    app.run(port=5050)

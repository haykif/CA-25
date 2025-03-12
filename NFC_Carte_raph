import nfc

def on_connect(tag):
    uid_hex = tag.identifier.hex().upper()  # Convertir en majuscules
    print(f"UID de la carte : {uid_hex}")
    return False  # Déconnecter après lecture

clf = nfc.ContactlessFrontend('usb')
clf.connect(rdwr={'on-connect': on_connect})
clf.close()

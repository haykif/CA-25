import nfc
# DETECTE l'UID de la carte
def on_connect(tag):

    uid_hex = tag.identifier.hex().upper()  # Convertir en majuscules
    print(f"UID de la carte : {uid_hex}")
    return True  # Déconnecter après lecture

#DETECTE LA PRESENCE DE LA CARTE
def read_card():
    with nfc.ContactlessFrontend('usb') as clf:
        # Connect to the card and return tag if successful, otherwise None
        tag = clf.connect(rdwr={'on connect': lambda tag: False})
        print("Carte détecté:", tag)


clf = nfc.ContactlessFrontend('usb')
while(True):
    clf.connect(rdwr={'on-connect': on_connect})
    

#clf.close()
#read_card()

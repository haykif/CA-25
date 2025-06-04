from mfrc522 import SimpleMFRC522

reader = SimpleMFRC522()

try:
    id, _ = reader.read()
    uid_bytes = id.to_bytes((id.bit_length() + 7) // 8, byteorder='big')  # ou 'little' selon le cas
    uid_hex = ''.join(f"{b:02X}" for b in uid_bytes)
    print(uid_hex)
finally:
    GPIO.cleanup()

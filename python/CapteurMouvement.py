from gpiozero import MotionSensor
from time import sleep


pir = MotionSensor(17)
while True:
    pir.wait_for_motion()
    print("bougé!")
    pir.wait_for_no_motion()
    print("pas bougé")


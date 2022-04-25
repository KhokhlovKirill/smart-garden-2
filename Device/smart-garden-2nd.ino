#include <Wire.h>
void setup() {
 Wire.begin(8);
 Wire.onReceive(receiveEvent);
 Wire.onRequest(requestEvent);
 Serial.begin(9600);
}
void loop() {
 delay(100);
}

void receiveEvent(int howMany) {
 while (0 < Wire.available()) {
    byte c = Wire.read();
    Serial.println(c);
    switch (c){
      case 0:
      
      break;
      case 1:
      turnOnRedLED();
      break;
      case 2:
      
      break;  
      }
  }    
}

void turnOnRedLED(){
  
  }

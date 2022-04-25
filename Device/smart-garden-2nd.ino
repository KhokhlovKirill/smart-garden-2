#include <Wire.h>
void setup() {
 Wire.begin(8);                /* join i2c bus with address 8 */
 Wire.onReceive(receiveEvent); /* register receive event */
 Wire.onRequest(requestEvent); /* register request event */
 Serial.begin(9600);           /* start serial comm. */
 Serial.println("I am I2C Slave");
}
void loop() {
 delay(100);
}
// function that executes whenever data is received from master
void receiveEvent(int howMany) {
 while (0 <Wire.available()) {
    byte c = Wire.read();      /* receive byte as a character */
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
// function that executes whenever data is requested from master
void turnOnRedLED(){
  
  }

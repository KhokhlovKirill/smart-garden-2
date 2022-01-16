// отправка данных по однопроводному юарту

// подключаем софт юарт
#include "softUART.h"
// делаем только отправителем (экономит память)
softUART<2, GBUS_FULL> UART(1000); // пин 4, скорость 1000

// подключаем GBUS
#include "GBUS.h"
GBUS bus(&UART, 3, 20); // обработчик UART, адрес 3, буфер 20 байт


void setup() {
  pinMode(A0, INPUT);
  Serial.begin(9600);
}


void loop() {
  // в тике сидит отправка и приём
  bus.tick();

  static uint32_t tmr;
  if (millis() - tmr >= 1000) {
    tmr = millis();
    byte data = getRotate();
    bus.sendData(5, data);  // на адрес 5
  }

  if (bus.gotData()) {
    // выводим данные
    byte datatest;
    bus.readData(datatest);

    Serial.println(datatest);
    Serial.println();
  }
}

byte getRotate(){
  return (analogRead(A0) / 4);
  }

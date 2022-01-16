// приём данных по однопроводному юарту

// подключаем софт юарт
#include "softUART.h"
// делаем только приёмником (экономит память)
softUART<2, GBUS_FULL> UART(1000); // пин 4, скорость 1000

// подключаем GBUS
#include "GBUS.h"
GBUS bus(&UART, 5, 20); // обработчик UART, адрес 5, буфер 20 байт
    byte data;
void setup() {
  Serial.begin(9600); // сериал для отладки (вывод в монитор)
  pinMode(12, OUTPUT);
}

void loop() {
  // в тике сидит отправка и приём
  bus.tick();

    static uint32_t tmr;
  if (millis() - tmr >= 1000) {
    tmr = millis();
    byte data = 111;
    bus.sendData(3, data);  // на адрес 5
  }

  if (bus.gotData()) {
    // выводим данные

    bus.readData(data);

    Serial.println(data);
    Serial.println();
    checkData();
  }
}

void checkData(){
  if (data > 123){
    digitalWrite(12, HIGH);
    } else {
      digitalWrite(12, LOW);
      }
  }

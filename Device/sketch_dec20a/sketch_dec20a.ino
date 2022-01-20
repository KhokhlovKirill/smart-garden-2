// приём данных по однопроводному юарту

// подключаем софт юарт
#include "softUART.h"
// делаем только приёмником (экономит память)
softUART<6, GBUS_RX> UART(1000); // пин 4, скорость 1000

// подключаем GBUS
#include "GBUS.h"
GBUS bus(&UART, 5, 20); // обработчик UART, адрес 5, буфер 20 байт

struct myStruct {
  byte code;
};

void setup() {
  Serial.begin(9600); // сериал для отладки (вывод в монитор)
}

void loop() {
  // в тике сидит отправка и приём
  bus.tick();

  if (bus.gotData()) {
    // выводим данные
    myStruct data;
    bus.readData(data);

    Serial.println(data.code);
    Serial.println();
  }
}

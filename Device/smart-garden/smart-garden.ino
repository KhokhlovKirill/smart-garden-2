/*
* Smart Garden Pro by Khokhlov Kirill

? Техническая информация:

?   EEPROM адреса данных:
?   0 - Регулярность обновления
?   1 - Необходимая влажность почвы
?   2 - Необходимая температура почвы
?   3 - Необходимая влажность воздуха
?   4 - Необходимая температура воздуха
?   5 - Необходимый уровень освещенности
?   6-38 - Wi-Fi SSID
?   39-71 - Wi-Fi пароль
?   72-93 - Название устройства (возможно удаление)
*/

//* Подключение библиотек
#include <ArduinoJson.h>
#include <SoftwareSerial.h>
#include <LiquidCrystal.h>
#include <TroykaDHT.h>
#include <GyverButton.h>
#include <EEPROM.h>

//* Директивы пинов
#define DHT_PIN 7
#define BUT_UP 0
#define BUT_DOWN 0
#define BUT_ENTER 0

//* Настройки
#define RX 8
#define TX 9
String AP = "Poco";
String PASS = "esp82668";

//@ Пресеты
const char *namesPreset[] = {
    "B\273a\264o\273\306\262\270\263\303e", // 0
    "C\263e\277o\273\306\262\270\263\303e", // 1
    "Te\276\273o\273\306\262\270\263\303e", // 2
    "Te\276\273oc\263e\277o\273\306\262\270\263\303e", // 3
};

//@ Значения пресетов
//? Пресет 0
const byte groundHumidityPreset0 = 0;
const byte groundTempPreset0 = 0;
const byte airHumidityPreset0 = 0;
const byte airTempPreset0 = 0;


//? Пресет 1
const byte groundHumidityPreset1 = 0;
const byte groundTempPreset1 = 0;
const byte airHumidityPreset1 = 0;
const byte airTempPreset1 = 0;


//? Пресет 2
const byte groundHumidityPreset2 = 0;
const byte groundTempPreset2 = 0;
const byte airHumidityPreset2 = 0;
const byte airTempPreset2 = 0;


//? Пресет 3
const byte groundHumidityPreset3 = 0;
const byte groundTempPreset3 = 0;
const byte airHumidityPreset3 = 0;
const byte airTempPreset3 = 0;


//@ Общие настройки устройства
const int id = 1; //? ID устройства
String deviceName = "Smart Garden";

byte regularUpdate = 1; //? Регулярность обновления данных

byte groundHumidity = 0; //? Текущая влажность почвы
float groundTemp = 0; //? Текущая температура почвы
byte airHumidity = 0; //? Текущая влажность воздуха
float airTemp = 0; //? Текущая температура воздуха
byte lighting = 0; //? Текущий уровень освещенности

byte groundHumiditySet = 0; //? Необходимая влажность почвы
float groundTempSet = 0; //? Необходимая температура почвы
byte airHumiditySet = 0; //? Необходимая влажность воздуха
float airTempSet = 0; //? Необходимая температура воздуха
byte lightingSet = 0; //? Необходимый уровень освещенности

char notificationCode[4] = {"0000"}; //? Код уведомления

//@ Технические переменные
bool wifiIsNotConnect;
String currentSSID = "";

int countTimeCommand;

unsigned long lastTimeMillis = 0;
unsigned long currentTime[2] = {0, 0};

boolean found = false;


//@ Инициализация классов для библиотек
SoftwareSerial ESP8266(RX, TX); //? Программный последовательный порт
LiquidCrystal lcd(12, 11, 5, 4, 3, 2); //? Экран
DHT dht(DHT_PIN, DHT11); //? Датчик температуры и влажности воздуха
GButton but_up(BUT_UP); //? GyverButton Кнопка вверх
GButton but_down(BUT_DOWN); //? GyverButton Кнопка вниз
GButton but_enter(BUT_ENTER); //? GyverButton Кнопка ввод (Enter)

//@ Символы для LCD-экрана
byte water[8] = { //? Капля
    B00100,
    B00100,
    B01110,
    B01110,
    B11111,
    B11111,
    B01110

};

byte temp[8] = { //? Температура
    B00101,
    B00100,
    B00100,
    B01110,
    B00100,
    B00101,
    B00111

};

byte lamp[8] = { //? Лампочка
    B01110,
    B10001,
    B10101,
    B10101,
    B01110,
    B01110,
    B00100

};

//* Технические функции

void(* resetFunc) (void) = 0;  //@ Функция перезагрузки


//@ Поиск нужных данных среди String
String outputDataFromString(String text, char firstChar, char secondChar, bool json = true)
{
  byte first, second;
  String result = "";

  for (int i = 0; i < text.length(); i++)
  {
    if (text[i] == firstChar)
    {
      first = i;
    }
    else if (text[i] == secondChar)
    {
      second = i;
      break;
    }
  }

  if (json)
  {
    for (int i = first; i <= second; i++)
    {
      result = result + text[i];
    }
  }
  else
  {
    for (int i = first + 1; i <= second - 1; i++)
    {
      result = result + text[i];
    }
  }
  return result;
}

//@ Формирование и отправление GET-запроса на сервер
void makeGetRequest(String host, String url)
{
  ESP8266.println("AT+CIPMUX=1");
  delay(3000);

  ESP8266.println("AT+CIPSTART=4,\"TCP\",\"" + host + "\",80");
  delay(3000);

  String cmd = "GET " + url + " HTTP/1.1\r\nHost:" + host + "\r\nConnection: close";
  ESP8266.println("AT+CIPSEND=4," + String(cmd.length() + 4));
  delay(1000);

  ESP8266.println(cmd);
  delay(1000);
  ESP8266.println("");
}

//@ Отправление AT-команды на ESP8266
void sendCommand(String command, int maxTime, char readReplay[])
{
  Serial.print("at command => ");
  Serial.print(command);
  Serial.print(" ");
  while (countTimeCommand < (maxTime * 1))
  {
    ESP8266.println(command);     //at+cipsend
    if (ESP8266.find(readReplay)) //ok
    {
      found = true;
      break;
    }
  }

  if (found == true)
  {
    Serial.print("Wifi connected!");
    countTimeCommand = 0;
  }

  if (found == false)
  {
    Serial.println("Fail");
    countTimeCommand = 0;
  }

  found = false;
}

//@ Подключение к Wi-Fi
void wifiConnection()
{
  sendCommand("AT+CWMODE=1", 5, "OK");
  sendCommand("AT+CWJAP=\"" + AP + "\",\"" + PASS + "\"", 20, "OK");
}

//@ Проверка подключение к Wi-Fi / Получение SSID
String checkWifiConnection()
{
  ESP8266.println("AT+CWJAP?");
  String ssid = "Fail";
  unsigned long currentTimeLocal = millis();
  while ((ESP8266.available() == 0) || (millis() - currentTimeLocal) <= 5000)
  {
    if (ESP8266.available() > 0)
    {
      String replay = ESP8266.readString();
      if (replay == "No AP")
      {
        return "#no-ap#";
      }
      
      return outputDataFromString(replay, '"', '"', false);
    }
  }
  return "ERROR";
}

//@ Формирование URL для GET-запроса на сервер
void sendRequest()
{
  makeGetRequest("kirill.pw", "/data-send.php?id=" + String(id) + "&notificationCode=" + String(notificationCode[0]) + String(notificationCode[1]) + String(notificationCode[2]) + String(notificationCode[3]) + "&airTemp=" + String(airTemp) + "&airHumidity=" + String(airHumidity) + "&groundTemp=" + String(groundTemp) + "&groundHumidity=" + String(groundHumidity) + "&wifi=" + String(currentSSID));
}

//@ Отрисовка основного экрана на ЖК-дисплее
void lcdDisplay()
{ 
  lcd.setCursor(1, 1);
  lcd.write(byte(0));
  lcd.print("-");
  lcd.print(airHumidity);
  lcd.print("%");

  lcd.setCursor(1, 2);
  lcd.write(byte(1));
  lcd.print("-");
  lcd.print(String(airTemp, 1));
  lcd.print("\x99"
            "C");

  lcd.setCursor(1, 3);
  lcd.write(byte(2));
  lcd.print("-");
  lcd.print(lighting);
  lcd.print("%");

  lcd.setCursor(11, 1);
  lcd.write(byte(0));
  lcd.print("-");
  lcd.print(groundHumidity);
  lcd.print("%");

  lcd.setCursor(11, 2);
  lcd.write(byte(1));
  lcd.print("-");
  lcd.print(String(groundTemp, 1));
  lcd.print("\x99"
            "C");
}

//@ Получение данных с датчиков и формирование кода уведомления
void getValueFromSensors()
{
  dht.read();
  airTemp = dht.getTemperatureC();
  airHumidity = dht.getHumidity();

  if (groundHumidity < groundHumiditySet - 2)
  {
    notificationCode[0] = '1';
  }
  else if (groundHumidity > groundHumiditySet + 2)
  {
    notificationCode[0] = '2';
  }
  else
  {
    notificationCode[0] = '0';
  }

  if (groundTemp < groundTempSet - 2)
  {
    notificationCode[1] = '1';
  }
  else if (groundTemp > groundTempSet + 2)
  {
    notificationCode[1] = '2';
  }
  else
  {
    notificationCode[1] = '0';
  }

  if (airHumidity < airHumiditySet - 2)
  {
    notificationCode[2] = '1';
  }
  else if (airHumidity > airHumiditySet + 2)
  {
    notificationCode[2] = '2';
  }
  else
  {
    notificationCode[2] = '0';
  }

  if (airTemp < airTempSet - 2)
  {
    notificationCode[3] = '1';
  }
  else if (airTemp > airTempSet + 2)
  {
    notificationCode[3] = '2';
  }
  else
  {
    notificationCode[3] = '0';
  }
}

//@ Функция отправки данных на сервер
void sendData(){
  lcd.clear();
  lcd.setCursor(3, 1);
  lcd.print("Updating data...");
  lcd.setCursor(5, 2);
  lcd.print("Wait...");

  sendRequest();

  lcd.clear();
  lcdDisplay();
}


//* Стандартные функции Arduino
void setup()
{
  //@ Инициализация модулей
  lcd.begin(20, 4);
  Serial.begin(9600);
  ESP8266.begin(9600);
  dht.begin();

  //@ Инициализация символов для ЖК-дисплея
  lcd.createChar(0, water);
  lcd.createChar(1, temp);
  lcd.createChar(2, lamp);

  //@ Установка режима опроса кнопки на автоматический
  but_up.setTickMode(AUTO);
  but_down.setTickMode(AUTO);
  but_enter.setTickMode(AUTO);

  //@ Получение настроек с EEPROM
  //? Проверка всех используемых байтов EEPROM на стандартное значение в 255
  for (int i = 0; i < 93; i++){ 
    if (EEPROM.read(i) == 255){
      EEPROM.update(i, 0);
    }
  }

  //? Получение данных с EEPROM
  EEPROM.get(0, regularUpdate);
  EEPROM.get(1, groundHumiditySet);
  EEPROM.get(2, groundTempSet);
  EEPROM.get(3, airHumiditySet);
  EEPROM.get(4, airTempSet);
  // TODO Получение необходимого уровня освещенности с EEPROM
  EEPROM.get(6, AP);
  EEPROM.get(39, PASS);
  EEPROM.get(72, deviceName);

  //@ Получение данных с датчиков и отрисовка основного экрана
  getValueFromSensors();
  lcdDisplay();

  //@ Перезагрузка ESP8266
  ESP8266.println("AT+RST");

  //@ Подключение к Wi-Fi и отправка данных на сервер
  wifiConnection();
  sendData();
}

void loop()
{
  but_enter.tick(); //@ Принудительное считывание значения с кнопки Enter

  //@ Обновление данных и проверка Wi-Fi в устройстве каждые 10 сек
  if (millis() - currentTime[0] > 10000)
  {
    currentTime[0] = millis();
    getValueFromSensors();
    lcdDisplay();

    String checkWifiResult = checkWifiConnection();
    if (checkWifiResult == "#no-ap#"){
      wifiIsNotConnect = true;
    } else {
      wifiIsNotConnect = false;
      currentSSID = checkWifiResult;
    }
  }

  //@ Отправка данных на сервер через заданный промежуток времени
  if (millis() - currentTime[1] > regularUpdate * 60000)
  {
    currentTime[1] = millis();
    sendData();
  }

  //@ Работа с кнопкой (Вход в меню настроек и пресетов)
  if (but_enter.hasClicks())
  {
    switch (but_enter.getClicks())
    {
    case 1:
      menuSettings(false); //? Вход в меню настроек с параметром back = false
      break;

    case 2:
      menuPresets(); //? Вход в меню пресетов
      break;
    }
  }
}


//* Меню настройки устройства
void menuSettings(bool back)
{
  byte posMenu = 1; //? Текущая выбранная позиция в меню

  if (back) posMenu = 4; //? Если пользователь возвращается из нижнего меню в это, то позицию в меню выставить в 3

  //@ Отрисовка меню на ЖК-дисплее
  lcd.clear();
  while (true)
  {
    lcd.setCursor(1, 0);
    lcd.print("Regular update"); // Регулярность обновления

    lcd.setCursor(1, 1);
    lcd.print("Wi-Fi settings"); // Настройка Wi-Fi

    lcd.setCursor(1, 2);
    lcd.print("Forced update"); // Принудительное обновление данных

    lcd.setCursor(1, 3);
    lcd.print("Restart"); // Перезагрузка

    //@ Работа с кнопками (вверх и вниз)
    if (but_down.isClick())
      posMenu = posMenu + 1;
    if (but_up.isClick())
      posMenu = posMenu - 1;

    if (posMenu == 0) posMenu = 1; //? Запрет выхода в более верхнее меню

    if (posMenu == 5) menuSettingsValue(); //? Переход на следующую страницу меню настроек
    

    //@ Отрисовка курсоров выбора пунктов меню
    if (posMenu == 1)
    {
      lcd.setCursor(0, 1);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print(" ");
      lcd.setCursor(0, 0);
      lcd.print("\x13");
    }
    else if (posMenu == 2)
    {
      lcd.setCursor(0, 0);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print(" ");
      lcd.setCursor(0, 1);
      lcd.print("\x13");
    }
    else if (posMenu == 3)
    {
      lcd.setCursor(0, 1);
      lcd.print(" ");
      lcd.setCursor(0, 0);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print("\x13");
    }
    else if (posMenu == 4)
    {
      lcd.setCursor(0, 1);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print(" ");
      lcd.setCursor(0, 0);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print("\x13");
    }

    //@ Выбор пунктов в меню
    if (but_enter.isSingle())
    {
      switch (posMenu)
      {
      case 1: //? Пункт 1
        regularUpdateSettings();
        break;

      case 2: //? Пункт 2
        menuWifiSettings();
        break;
      
      case 3: //? Пункт 3
        sendData();
        break;

      case 4: //? Пункт 4
        resetFunc();
        break;
      }
    }

    //@ Выход на главный экран при двойном нажатии кнопки Enter
    if (but_enter.isDouble())
    {
      lcd.clear();
      lcdDisplay();
      loop();
      break;
    }
  }
}

//* Меню настройки необходимых параметров для растения
void menuSettingsValue()
{
  byte posMenu = 1; //? Текущая выбранная позиция в меню

  //@ Отрисовка меню на ЖК-дисплее
  lcd.clear();
  while (true)
  {
    lcd.setCursor(1, 0);
    lcd.print("Ground Humidity"); // Влажность почвы

    lcd.setCursor(1, 1);
    lcd.print("Ground Temp"); // Температура почвы

    lcd.setCursor(1, 2);
    lcd.print("Air Humidity"); // Влажность воздуха

    lcd.setCursor(1, 3);
    lcd.print("Air Temp"); // Температура воздуха

    //@ Работа с кнопками (вверх и вниз)
    if (but_down.isClick())
      posMenu = posMenu + 1;
    if (but_up.isClick())
      posMenu = posMenu - 1;

    if (posMenu == 0) menuSettings(true); //? Возврат на предыдущую страницу настроек
    
    if (posMenu == 5) posMenu = 4; //? Запрет выхода в более низкое меню
    
    //@ Отрисовка курсоров выбора пунктов меню
    if (posMenu == 1)
    {
      lcd.setCursor(0, 1);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print(" ");
      lcd.setCursor(0, 0);
      lcd.print("\x13");
    }
    else if (posMenu == 2)
    {
      lcd.setCursor(0, 0);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print(" ");
      lcd.setCursor(0, 1);
      lcd.print("\x13");
    }
    else if (posMenu == 3)
    {
      lcd.setCursor(0, 1);
      lcd.print(" ");
      lcd.setCursor(0, 0);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print("\x13");
    }
    else if (posMenu == 4)
    {
      lcd.setCursor(0, 1);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print(" ");
      lcd.setCursor(0, 0);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print("\x13");
    }

    //@ Выбор пунктов в меню
    if (but_enter.isSingle())
    {
      switch (posMenu)
      {
      case 1: //? Пункт 1
        groundHumiditySettings();
        break;

      case 2: //? Пункт 2
        groundTempSettings();
        break;
      
      case 3: //? Пункт 3
        airHumiditySettings();
        break;
      
      case 4: //? Пункт 4
        airTempSettings();
        break;
      }
    }

    //@ Выход на главный экран при двойном нажатии кнопки Enter
    if (but_enter.isDouble())
    {
      lcd.clear();
      lcdDisplay();
      loop();
      break;
    }
  }
}

//* Меню пресетов
void menuPresets()
{
  byte posMenu = 1; //? Текущая выбранная позиция в меню

  //@ Отрисовка меню на ЖК-дисплее
  lcd.clear();
  while (true)
  {
    lcd.setCursor(1, 0);
    lcd.print(namesPreset[0]); //? Пресет 0

    lcd.setCursor(1, 1);
    lcd.print(namesPreset[1]); //? Пресет 1

    lcd.setCursor(1, 2);
    lcd.print(namesPreset[2]); //? Пресет 2

    lcd.setCursor(1, 3);
    lcd.print(namesPreset[3]); //? Пресет 3

    //@ Работа с кнопками (вверх и вниз)
    if (but_down.isClick())
      posMenu = posMenu + 1;
    if (but_up.isClick())
      posMenu = posMenu - 1;

    if (posMenu == 0) posMenu = 1; //? Запрет выхода в более верхнее меню

    if (posMenu == 5) posMenu = 4; //? Запрет выхода в более низкое меню
    
    //@ Отрисовка курсоров выбора пунктов меню
    if (posMenu == 1)
    {
      lcd.setCursor(0, 1);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print(" ");
      lcd.setCursor(0, 0);
      lcd.print("\x13");
    }
    else if (posMenu == 2)
    {
      lcd.setCursor(0, 0);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print(" ");
      lcd.setCursor(0, 1);
      lcd.print("\x13");
    }
    else if (posMenu == 3)
    {
      lcd.setCursor(0, 1);
      lcd.print(" ");
      lcd.setCursor(0, 0);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print("\x13");
    }
    else if (posMenu == 4)
    {
      lcd.setCursor(0, 1);
      lcd.print(" ");
      lcd.setCursor(0, 2);
      lcd.print(" ");
      lcd.setCursor(0, 0);
      lcd.print(" ");
      lcd.setCursor(0, 3);
      lcd.print("\x13");
    }

    //@ Выбор пунктов в меню
    if (but_enter.isSingle())
    {
      switch (posMenu)
      {
      case 1: //? Пункт 1
        preset0();
        lcd.clear();
        lcdDisplay();
        loop();
        break;

      case 2: //? Пункт 2
        preset1();
        lcd.clear();
        lcdDisplay();
        loop();
        break;

      case 3: //? Пункт 3
        preset2();
        lcd.clear();
        lcdDisplay();
        loop();
        break;

      case 4: //? Пункт 4
        preset3();
        lcd.clear();
        lcdDisplay();
        loop();
        break;
      }
    }

    //@ Выход на главный экран при двойном нажатии кнопки Enter
    if (but_enter.isDouble())
    {
      lcd.clear();
      lcdDisplay();
      loop();
      break;
    }
  }
}

//* Функции прменения пресетов
void preset0() {
  EEPROM.put(1, groundHumidityPreset0);
  EEPROM.put(2, groundTempPreset0);
  EEPROM.put(3, airHumidityPreset0);
  EEPROM.put(4, airTempPreset0);
}
void preset1() {
  EEPROM.put(1, groundHumidityPreset1);
  EEPROM.put(2, groundTempPreset1);
  EEPROM.put(3, airHumidityPreset1);
  EEPROM.put(4, airTempPreset1);
}
void preset2() {
  EEPROM.put(1, groundHumidityPreset2);
  EEPROM.put(2, groundTempPreset2);
  EEPROM.put(3, airHumidityPreset2);
  EEPROM.put(4, airTempPreset2);
}
void preset3() {
  EEPROM.put(1, groundHumidityPreset3);
  EEPROM.put(2, groundTempPreset3);
  EEPROM.put(3, airHumidityPreset3);
  EEPROM.put(4, airTempPreset3);
}

//* Меню настроек времени полива
void regularUpdateSettings()
{
  lcd.clear();
  for (;;)
  {
    lcd.setCursor(0, 1);
    lcd.print("Regular Update");
    lcd.setCursor(4, 2);
    lcd.print(regularUpdate);
    lcd.print(" \274\270\275"); // мин

    if (but_up.isClick())
    {
      regularUpdate++;
    }
    if (but_down.isClick())
    {
      regularUpdate--;
    }
    if (but_up.isStep())
    {
      regularUpdate++;
    }
    if (but_down.isStep())
    {
      regularUpdate--;
    }

    if (but_enter.isSingle())
    {
      EEPROM.put(0, regularUpdate); //? Запись данных в EEPROM
      lcd.clear();
      break;
    }
  }
}

//* Меню настроек влажности почвы
void groundHumiditySettings()
{
  lcd.clear();
  for (;;)
  {
    lcd.setCursor(0, 1);
    lcd.print("Ground Humidity");
    lcd.setCursor(4, 2);
    lcd.print(groundHumiditySet);
    lcd.print(" \274\270\275"); // мин

    if (but_up.isClick())
    {
      groundHumiditySet++;
    }
    if (but_down.isClick())
    {
      groundHumiditySet--;
    }
    if (but_up.isStep())
    {
      groundHumiditySet++;
    }
    if (but_down.isStep())
    {
      groundHumiditySet--;
    }

    if (but_enter.isSingle())
    {
      EEPROM.put(1, groundHumiditySet); //? Запись данных в EEPROM
      lcd.clear();
      break;
    }
  }
}

//* Меню настроек температуры почвы
void groundTempSettings()
{
  lcd.clear();
  for (;;)
  {
    lcd.setCursor(0, 1);
    lcd.print("Ground Temperature");
    lcd.setCursor(4, 2);
    lcd.print(groundTempSet);
    lcd.print(" \274\270\275"); // мин

    if (but_up.isClick())
    {
      groundTempSet++;
    }
    if (but_down.isClick())
    {
      groundTempSet--;
    }
    if (but_up.isStep())
    {
      groundTempSet++;
    }
    if (but_down.isStep())
    {
      groundTempSet--;
    }

    if (but_enter.isSingle())
    {
      EEPROM.put(2, groundTempSet); //? Запись данных в EEPROM
      lcd.clear();
      break;
    }
  }
}

//* Меню настроек влажности воздуха
void airHumiditySettings()
{
  lcd.clear();
  for (;;)
  {
    lcd.setCursor(0, 1);
    lcd.print("Air Humidity");
    lcd.setCursor(4, 2);
    lcd.print(airHumiditySet);
    lcd.print(" \274\270\275"); // мин

    if (but_up.isClick())
    {
      airHumiditySet++;
    }
    if (but_down.isClick())
    {
      airHumiditySet--;
    }
    if (but_up.isStep())
    {
      airHumiditySet++;
    }
    if (but_down.isStep())
    {
      airHumiditySet--;
    }

    if (but_enter.isSingle())
    {
      EEPROM.put(3, airHumiditySet); //? Запись данных в EEPROM
      lcd.clear();
      break;
    }
  }
}

//* Меню настроек температуры воздуха
void airTempSettings()
{
  lcd.clear();
  for (;;)
  {
    lcd.setCursor(0, 1);
    lcd.print("Air Temperature");
    lcd.setCursor(4, 2);
    lcd.print(airTempSet);
    lcd.print(" \274\270\275"); // мин

    if (but_up.isClick())
    {
      airTempSet++;
    }
    if (but_down.isClick())
    {
      airTempSet--;
    }
    if (but_up.isStep())
    {
      airTempSet++;
    }
    if (but_down.isStep())
    {
      airTempSet--;
    }

    if (but_enter.isSingle())
    {
      EEPROM.put(4, airTempSet); //? Запись данных в EEPROM
      lcd.clear();
      break;
    }
  }
}

//* Меню настроек Wi-Fi
void menuWifiSettings(){

}
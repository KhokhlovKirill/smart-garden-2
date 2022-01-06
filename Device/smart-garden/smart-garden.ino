#include <ArduinoJson.h>
#include <SoftwareSerial.h>
#include <LiquidCrystal.h>
#include <TroykaDHT.h>
#include <GyverButton.h>
#include <EEPROM.h>

#define DHT_PIN 7

#define BUT_UP 0
#define BUT_DOWN 0
#define BUT_ENTER 0

#define RX 8
#define TX 9
String AP = "Poco";
String PASS = "esp82668";

// Пресеты
const char *namesPreset[] = {
    "B\273a\264o\273\306\262\270\263\303e",               // 0
    "C\263e\277o\273\306\262\270\263\303e",               // 1
    "Te\276\273o\273\306\262\270\263\303e",               // 2
    "Te\276\273oc\263e\277o\273\306\262\270\263\303e",    // 3
    "B\273a\264o\277e\276\273o\273\306\262\270\263\303e", // 4
    "He\276p\270xo\277\273\270\263\303e",                 // 5
};

// Значения пресетов
// Пресет 1
const byte groundHumidityPreset1 = 0;
const byte groundTempPreset1 = 0;
const byte airHumidityPreset1 = 0;
const byte airTempPreset1 = 0;
//_____________

// Пресет 2
const byte groundHumidityPreset2 = 0;
const byte groundTempPreset2 = 0;
const byte airHumidityPreset2 = 0;
const byte airTempPreset2 = 0;
//_____________

// Пресет 3
const byte groundHumidityPreset3 = 0;
const byte groundTempPreset3 = 0;
const byte airHumidityPreset3 = 0;
const byte airTempPreset3 = 0;
//_____________

// Пресет 4
const byte groundHumidityPreset4 = 0;
const byte groundTempPreset4 = 0;
const byte airHumidityPreset4 = 0;
const byte airTempPreset4 = 0;
//_____________

//___________________________
//_________________________________________

const int id = 1;
String deviceName = "Smart Garden";
byte regularUpdate = 1;

byte groundHumidity = 0;
float groundTemp = 0;
byte airHumidity = 0;
float airTemp = 0;
byte lighting = 0;

byte groundHumiditySet = 0;
float groundTempSet = 0;
byte airHumiditySet = 0;
float airTempSet = 0;
char notificationCode[4] = {"0000"};

int countTimeCommand;

unsigned long lastTimeMillis = 0;
unsigned long currentTime[2] = {0, 0};

boolean found = false;

SoftwareSerial ESP8266(RX, TX);
LiquidCrystal lcd(12, 11, 5, 4, 3, 2);
DHT dht(DHT_PIN, DHT11);
GButton but_up(BUT_UP);       // Кнопка вверх
GButton but_down(BUT_DOWN);   // Кнопка вниз
GButton but_enter(BUT_ENTER); // Кнопка ввод

// Символы для LCD-экрана
byte water[8] = { // Капля
    B00100,
    B00100,
    B01110,
    B01110,
    B11111,
    B11111,
    B01110

};

byte temp[8] = { // Температура
    B00101,
    B00100,
    B00100,
    B01110,
    B00100,
    B00101,
    B00111

};

byte lamp[8] = { // Лампочка
    B01110,
    B10001,
    B10101,
    B10101,
    B01110,
    B01110,
    B00100

};
//________________________________________

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

void wifiConnection()
{
  sendCommand("AT+CWMODE=1", 5, "OK");
  sendCommand("AT+CWJAP=\"" + AP + "\",\"" + PASS + "\"", 20, "OK");
}

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

void sendData()
{
  makeGetRequest("kirill.pw", "/data-send.php?id=" + String(id) + "&notificationCode=" + String(notificationCode[0]) + String(notificationCode[1]) + String(notificationCode[2]) + String(notificationCode[3]) + "&airTemp=" + String(airTemp) + "&airHumidity=" + String(airHumidity));
}

void lcdDisplay()
{ // Отрисовка основного экрана
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

void getValueFromSensors()
{ // Получение данных с датчиков
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

void setup()
{
  // Инициализация модулей
  lcd.begin(20, 4);
  Serial.begin(9600);
  ESP8266.begin(9600);
  dht.begin();

  // Инициализация символов
  lcd.createChar(0, water);
  lcd.createChar(1, temp);
  lcd.createChar(2, lamp);
  //____________________________

  // Установка режима опроса кнопки на автоматический
  but_up.setTickMode(AUTO);
  but_down.setTickMode(AUTO);
  but_enter.setTickMode(AUTO);
  //______________________________

  getValueFromSensors();
  lcdDisplay();

  ESP8266.println("AT+RST"); // Перезагрузка esp8266

  wifiConnection();
  sendData();
}

void loop()
{
  but_enter.tick(); // Принудительное считывание значения с кнопки Enter

  if (millis() - currentTime[0] > 10000)
  {
    currentTime[0] = millis();
    getValueFromSensors();
    lcdDisplay();
    checkWifiConnection();
  }

  if (millis() - currentTime[1] > regularUpdate * 60000)
  {
    currentTime[1] = millis();
    sendData();
  }

  if (but_enter.hasClicks())
  {
    switch (but_enter.getClicks())
    {
    case 1:
      menuSettings(false);
      break;

    case 2:
      menuPresets();
      break;
    }
  }
}

void menuSettings(boolean back)
{ // Меню настроек
  byte posMenu = 0;

  if (back)
    posMenu = 3;

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

    if (but_down.isClick())
      posMenu = posMenu + 1;
    if (but_up.isClick())
      posMenu = posMenu - 1;

    if (posMenu == -1)
      posMenu = 0;

    if (posMenu == 0)
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
    else if (posMenu == 1)
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
    else if (posMenu == 2)
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
    else if (posMenu == 3)
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

    if (posMenu == 4)
    {
      // Переход на след стр
      menuSettingsValue();
    }

    if (but_enter.isSingle())
    {
      switch (posMenu)
      {
      case 1:
        // TODO 1 Пункт меню
        break;

      case 2:
        // TODO 2 Пункт меню
        break;
      }
    }

    if (but_enter.isDouble())
    {
      lcd.clear();
      // TODO Выход в главный экран
      break;
    }
  }
}

void menuSettingsValue()
{ // Меню настроек
  byte posMenu = 0;

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

    if (but_down.isClick())
      posMenu = posMenu + 1;
    if (but_up.isClick())
      posMenu = posMenu - 1;

    if (posMenu == 0)
    {
      menuSettings(true);
    }

    if (posMenu == 0)
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
    else if (posMenu == 1)
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
    else if (posMenu == 2)
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
    else if (posMenu == 3)
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

    if (posMenu == 4)
    {
      // Переход на след стр
      posMenu = 3; // Временный запрет перехода на 3 стр
    }

    if (but_enter.isSingle())
    {
      switch (posMenu)
      {
      case 1:
        // TODO 1 Пункт меню
        break;

      case 2:
        // TODO 2 Пункт меню
        break;
      }
    }

    if (but_enter.isDouble())
    {
      lcd.clear();
      // TODO Выход в главный экран
      break;
    }
  }
}

void menuPresets()
{ // Меню настроек
  byte posMenu = 0;

  lcd.clear();
  while (true)
  {
    lcd.setCursor(1, 0);
    lcd.print(namesPreset[0]); // Температура

    lcd.setCursor(1, 1);
    lcd.print(namesPreset[1]); // Освещение

    lcd.setCursor(1, 2);
    lcd.print(namesPreset[2]); // Освещение

    lcd.setCursor(1, 3);
    lcd.print(namesPreset[3]); // Освещение

    if (but_down.isClick())
      posMenu = posMenu + 1;
    if (but_up.isClick())
      posMenu = posMenu - 1;

    if (posMenu == 0)
    {
    }

    if (posMenu == 0)
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
    else if (posMenu == 1)
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
    else if (posMenu == 2)
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
    else if (posMenu == 3)
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

    if (posMenu == 4)
    {
      // Переход на след стр
    }

    if (but_enter.isSingle())
    {
      switch (posMenu)
      {
      case 1:
        preset1();
        break;

      case 2:
        preset2();
        break;

      case 3:
        preset3();
        break;

      case 4:
        preset4();
        break;
      }
    }

    if (but_enter.isDouble())
    {
      lcd.clear();

      break;
    }
  }
}

void preset0() {}
void preset1() {}
void preset2() {}
void preset3() {}
void preset4() {}
void preset5() {}

// Меню настроек времени полива
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
      EEPROM.update(5, regularUpdate);
      lcd.clear();
      break;
    }
  }
}
//________________________________________________

// Меню настроек влажности почвы
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
      EEPROM.update(5, groundHumiditySet);
      lcd.clear();
      break;
    }
  }
}
//________________________________________________

// Меню настроек температуры почвы
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
      EEPROM.update(5, groundTempSet);
      lcd.clear();
      break;
    }
  }
}
//________________________________________________

// Меню настроек влажности воздуха
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
      EEPROM.update(5, airHumiditySet);
      lcd.clear();
      break;
    }
  }
}
//________________________________________________

// Меню настроек температуры воздуха
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
      EEPROM.update(5, airTempSet);
      lcd.clear();
      break;
    }
  }
}
//________________________________________________
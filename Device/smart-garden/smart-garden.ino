#include <SoftwareSerial.h>
#include <LiquidCrystal.h>
#include <TroykaDHT.h>
#include <EncButton.h>

#define DHT_PIN 7

#define BUT_UP 0
#define BUT_DOWN 0
#define BUT_ENTER 0

#define RX 8
#define TX 9
String AP = "Poco";
String PASS = "esp82668";

const char id[] = "1";
String deviceName = "Smart Garden";
byte regularUpdate = 1;

byte groundHumidity = 0;
float groundTemp = 0;
byte airHumidity = 0;
float airTemp = 0;

byte groundHumiditySet = 0;
float groundTempSet = 0;
byte airHumiditySet = 0;
float airTempSet = 0;

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
  unsigned long currentTimeLocal;
  while ((ESP8266.available() == 0) || (millis() - currentTimeLocal) <= 5000)
  {
  }
  if (ESP8266.available() > 0)
  {
    String replay = ESP8266.readString();
    if (replay == "No AP")
    {
      return "#no-ap#";
    }
    //  Извлечь SSID !TODO!

    return ssid;
  }
}

void sendData()
{
  makeGetRequest("kirill.pw", "/data-send.php?id=" + String(id) + "&airTemp=" + String(airTemp) + "&airHumidity=" + String(airHumidity));
}

void lcdDisplay()
{ // Отрисовка основного экрана
  lcd.setCursor(1, 1);
  lcd.write(byte(0));
  lcd.print("-");
  lcd.print(groundHumidity);
  lcd.print("%");

  lcd.setCursor(1, 2);
  lcd.write(byte(1));
  lcd.print("-");
  lcd.print(airTemp);
  lcd.print("\x99"
            "C");

  lcd.setCursor(11, 1);
  lcd.write(byte(2));
  lcd.print("-");
  lcd.print(airHumidity);
  lcd.print("%");

  lcd.setCursor(11, 2);
  lcd.write(byte(1));
  lcd.print("-");
  lcd.print(groundTemp);
  lcd.print("\x99"
            "C");
}

void getValueFromSensors()
{ // Получение данных с датчиков
  dht.read();
  airTemp = dht.getTemperatureC();
  airHumidity = dht.getHumidity();
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
}

void loop()
{
  but_enter.tick();
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

  switch (but_enter.getClicks())
  {
  case 1:
    menuSettings();
    break;

  case 2:
    menuPresets();
    break;
  }
}

void menuSettings()
{ // Меню настроек
  byte posMenu = 0;

  lcd.clear();
  while (true)
  {
    lcd.setCursor(1, 0);
    lcd.print("Te\274\276epa\277ypa"); // Температура

    lcd.setCursor(1, 1);
    lcd.print("Oc\263e\346e\275\270e"); // Освещение

    lcd.setCursor(1, 2);
    lcd.print("Oc\263e\346e\275\270e"); // Освещение

    lcd.setCursor(1, 3);
    lcd.print("Oc\263e\346e\275\270e"); // Освещение

    if (but_down.isClick())
      posMenu = posMenu + 1;
    if (but_up.isClick())
      posMenu = posMenu - 1;

    if (posMenu == 0)
    {
      menuSettings();
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
      // TODO Переход на след стр
    }

    if (but_enter.isSingle())
    {
      switch (posMenu)
      {
      case 1:
        // TODO 1 пункт меню
        break;

      case 2:
        // TODO 2 пункт меню
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
    lcd.print("Te\274\276epa\277ypa"); // Температура

    lcd.setCursor(1, 1);
    lcd.print("Oc\263e\346e\275\270e"); // Освещение

    lcd.setCursor(1, 2);
    lcd.print("Oc\263e\346e\275\270e"); // Освещение

    lcd.setCursor(1, 3);
    lcd.print("Oc\263e\346e\275\270e"); // Освещение

    if (but_down.isClick())
      posMenu = posMenu + 1;
    if (but_up.isClick())
      posMenu = posMenu - 1;

    if (posMenu == 0)
    {
      menuSettings();
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
        // 1 клик
        break;

      case 2:
        // 2 клика
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

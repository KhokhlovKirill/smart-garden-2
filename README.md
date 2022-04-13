# Умная теплица «Smart  Garden 2»
«Smart  Garden 2» позволяет отслеживать ряд важных показателей, таких как влажность, температура воздуха и почвы. Устройство имеет удобное меню и веб-приложение с возможностью удалённого контроля и настройки необходимых параметров.

Умная теплица выглядит как блок управления со всей электроникой, на котором находятся растения.
## Необходимые компоненты
-	Микроконтроллер «Arduino»
-	Wi-Fi модуль на базе ESP8266
-	Датчик температура и влажности воздуха
-	Датчик влажности почвы
-	Датчик температуры почвы
-	Жидкокристаллический экран
## Принцип работы
Устройство контролирует и выводит на экран температуру, влажность воздуха и почвы. В случае отклонения от заданных в настройках устройства необходимых параметров, устройство будет сигнализировать с помощью светодиода, а в случае с показателем влажности почвы будет включать полив растения. Также устройство отправляет данные с датчиков и уведомления на удалённый сервер «Smart  Garden» через Wi-Fi.
## Веб-приложение
Веб-приложение позволяет контролировать удалённо все показатели и уведомления, зная только ID устройства и пароль. Оно доступно по адресу [kirill.pw](http://kirill.pw/). Перейдя на сайт, пользователь попадает на страницу входа, где он должен указать необходимые данные для входа в центр управления. После ввода данных, пользователь оказывается в центре управления, в котором ему доступна вся необходимая информация о климатических условиях в его умной теплице, такая как:
-	Текущая влажность почвы
-	Текущая температура почвы
-	Текущая влажность воздуха
-	Текущая температура воздуха
-	Интерактивные графики изменения показаний за последние 24 часа
-	Уведомления о климатических условиях
-	Wi-Fi сеть к которой подключена «Smart Garden»

Находясь в центре управления можно переключится на другую умную теплицу, для этого нужно ввести её данные, также можно открыть более крупный интерактивный график изменения показаний (рис. 3) или перейти в настройки устройства (рис. 4). В настройках можно настроить такие параметры как:
-	Название устройства
-	Необходимая влажность почвы
-	Необходимая температура почвы
-	Необходимая влажность воздуха
-	Необходимая температура воздуха
-	Регулярность обновления данных на веб-сервере
-	Название Wi-Fi сети (SSID)
-	Пароль Wi-Fi сети
-	Пароль для входа в центр управления «Smart Garden»

Благодаря возможности удалённого управления и контроля людям не нужно постоянно находится возле растения, ведь все необходимые сведения можно получить из любой точки мира, где есть подключение к интернету.
## Библиотеки, используемые в коде устройства

 - [SoftwareSerial](https://www.arduino.cc/en/Reference/softwareSerial)
 - [LiquidCrystal](https://www.arduino.cc/reference/en/libraries/liquidcrystal/)
 - [TroykaDHT](https://www.arduino.cc/reference/en/libraries/troykadht/)
 - [GyverButton](https://www.arduino.cc/reference/en/libraries/gyverbutton/)
 - [EEPROM](https://docs.arduino.cc/learn/built-in-libraries/eeprom)
 - [OneWire](https://www.arduino.cc/reference/en/libraries/onewire/)
 - [DallasTemperature](https://www.arduino.cc/reference/en/libraries/dallastemperature/)


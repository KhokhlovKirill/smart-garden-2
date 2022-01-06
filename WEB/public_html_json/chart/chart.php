<?php
  /* Подключаем классы */
  require_once "../pChart/pChart/pData.class";
  require_once "../pChart/pChart/pChart.class";
  $DataSet = new pData(); // Создаём объект pData
  $DataSet->AddPoint(array(0, 1, 4, 9, 16, 25, 36, 49, 64, 81, 100), "Serie1"); // Загружаем данные графика 1
  $DataSet->AddPoint(array(0, 1, 8, 27, 64, 125, 216, 343, 512, 729, 1000), "Serie2"); // Загружаем данные графика 2
  $DataSet->AddAllSeries(); // Добавить все данные для построения
  $Test = new pChart(700, 230); // Рисуем графическую плоскость
  $Test->setFontProperties("Fonts/tahoma.ttf", 8); // Установка шрифта
  $Test->setGraphArea(50, 30, 585, 200); // Установка области графика
  $Test->drawFilledRoundedRectangle(7, 7, 693, 223, 5, 240, 240, 240); // Выделяем плоскость прямоугольником
  $Test->drawRoundedRectangle(5, 5, 695, 225, 5, 230, 230, 230); // Делаем контур графической плоскости
  $Test->drawGraphArea(255, 255, 255, true); // Рисуем графическую плоскость
  $Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, true, 0, 2); // Рисуем оси и график
  $Test->drawGrid(4, true, 230, 230, 230, 50); // Рисуем сетку
  $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription()); // Соединяем точки графика линиями
  $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(), 3, 2, 255, 255, 255); // Рисуем точки
  $Test->drawTitle(50, 22, "MyRusakov.ru", 50, 50, 50, 585); // Выводим заголовок графика
  $Test->Stroke(); // Выводим график в окно браузера;
?>
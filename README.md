# Морской бой

## History
I wrote this project in 2016 on PHP 5.6

## Описание
Это проект, который я затеял, чтобы получше разобраться в тонкостях ООП, попытавшись построить архитектуру игры полностью в объектно-ориентированном стиле. Я, конечно, понимаю, что подобные игры лучше писать на Javascript'е, но я решил, что в учебных целях данный проект окажется весьма полезным.


## Установка и запуск

```shell
git clone https://github.com/PavelGushchin/seabattle.git
cd seabattle
composer update
cd public
php -S localhost:8080
```
Затем перейти на `http://localhost:8080` в браузере.


## Искусственный интеллект

Самым долгим по времени оказалось написание ИИ для противника. В результате получилось 3 алгоритма:

### 1. Рандомный алгоритм
Самый "тупой" алгоритм. Просто бьёт наугад.

### 2. Умный алгоритм
Моделирует действия человека: сначала стреляет наугад, а потом, если попадает в корабль, начинает его добивать.

### 3. Алгоритм со стратегией
Базируется на идее, что у каждой клетки есть своя ценность. Изначально, когда игра только начинается, все клетки имеют одинаковую ценность, но постепенно какие-то клетки становятся ценнее и данный алгоритм стреляет по таким клеткам в первую очередь.


## Как поменять ИИ

Алгоритм для противника задаётся в методе `startNewGame()` класса `Game`:
```php
// src/SeaBattle/Game/Game.php

public function startNewGame()
{
    ...
    $this->enemyField = new Field(new ShootingWithStrategyAI());
    ...
}
```


## Автобитва

Для тестирования эффективности алгоритмов я ввёл фичу автобитвы, то есть возможности "стравливать" алгоритмы друг с другом. Активировать её можно следующим образом:

    http://localhost:8080/?autobattle=10

где 10 - это количество игр, которое алгоритмы сыграют друг с другом.


## Запуск тестов

Перейти в корень проекта и ввести:
```shell
vendor/bin/phpunit
```
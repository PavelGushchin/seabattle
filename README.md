# Морской бой

Это проект, который я затеял, чтобы получше разобраться в тонкостях ООП, попытавшись построить архитектуру игры полностью в объектно-ориентированном стиле. Я, конечно, понимаю, что подобные игры лучше писать на Javascript'е, но я решил, что в учебных целях данный проект оказажется весьма полезным.


## Установка и запуск

```shell
git clone git@bitbucket.org:pavel-gushchin/seabattle.git
cd seabattle
composer update
cd web
php localhost -S localhost:8080
```
Затем перейти на `http://localhost:8080` в браузере.


## Искусственный интеллект

Самым долгим по времени оказалось написание ИИ для противника. В результате получилось 3 алгоритма:

  #### 1. Рандомный алгоритм
  Самый "тупой" алгоритм. Просто бьёт наугад.

  #### 2. Умный алгоритм
  Моделирует действия человека: сначала стреляет наугад, а потом, если попадает в корабль, то начинает его добивать.

  #### 3. Алгоритм со стратегией
  Базируется на идее, что у каждой клетки есть своя ценность. Изначально, когда игра только начинается, все клетки имеют одинаковую ценность, но постепенно какие-то клетки становятся ценнее и данный алгоритм стреляет по таким клеткам в первую очередь.


## Автобитва

Для тестирования эффективности алгоритмов я ввёл фичу автобитвы, то есть возможности "стравливать" алгоритмы друг с другом. Активировать её можно следующим образом:

    ###### http://localhost:8080/?autobattle=100

где 100 - это количество игр, которое алгоритмы сыграют друг с другом


## Запуск тестов

```shell
cd seabattle
vendor/bin/phpunit
```